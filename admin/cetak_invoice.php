<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID Pesanan tidak valid.";
    exit;
}

$id_order = intval($_GET['id']);

// Ambil data order
$query_order = "
SELECT o.*, u.nama_lengkap, u.email 
FROM `order` o
LEFT JOIN `user` u ON o.id_user = u.id_user
WHERE o.id_order = $id_order
";
$result_order = mysqli_query($conn, $query_order);

if (!$result_order || mysqli_num_rows($result_order) === 0) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

$order = mysqli_fetch_assoc($result_order);

// Ambil data payment
$query_payment = "
SELECT * 
FROM payment 
WHERE id_order = $id_order
";
$result_payment = mysqli_query($conn, $query_payment);
$payment = mysqli_fetch_assoc($result_payment);

// Ambil data alamat pengiriman
$query_address = "
SELECT * 
FROM alamat_pengiriman 
WHERE id_user = " . intval($order['id_user']) . "
LIMIT 1
";
$result_address = mysqli_query($conn, $query_address);
$address = mysqli_fetch_assoc($result_address);

// Ambil detail item order
$query_detail = "
SELECT * 
FROM order_detail 
WHERE id_order = $id_order
ORDER BY id_detail ASC
";
$result_detail = mysqli_query($conn, $query_detail);

$display_id = "PLG-" . str_pad($order['id_order'], 5, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= $display_id ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #FFFFFF;
            color: #3A3063;
            padding: 40px;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ================= INVOICE WRAPPER ================= */
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #FFEAA7;
            border-radius: 20px;
            padding: 40px;
            position: relative;
            background-color: #FFFDF8;
        }

        /* ================= HEADER ================= */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px dashed #8DE3C7;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }

        .brand-section h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 32px;
            font-weight: 900;
            color: #3A3063;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .brand-section p {
            color: #888888;
            font-weight: 700;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 900;
            color: #FF6FB7;
            margin-bottom: 10px;
        }

        .invoice-details p {
            font-weight: 700;
            color: #555555;
        }

        /* ================= BILLING INFO ================= */
        .billing-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .bill-card h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            color: #3A3063;
            margin-bottom: 15px;
            border-left: 4px solid #8DE3C7;
            padding-left: 10px;
        }

        .bill-card p {
            font-weight: 700;
            color: #555555;
            margin-bottom: 6px;
        }

        .bill-card strong {
            color: #3A3063;
            font-weight: 900;
        }

        /* ================= ITEMS TABLE ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 45px;
        }

        .items-table th {
            background-color: #FFE1D6;
            color: #3A3063;
            padding: 12px 15px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: left;
            border-bottom: 2px solid #F2994A;
        }

        .items-table td {
            padding: 15px;
            font-size: 13px;
            font-weight: 700;
            color: #3A3063;
            border-bottom: 1px solid #EEEEEE;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* ================= TOTALS ================= */
        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .total-table {
            width: 300px;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 700;
        }

        .total-table td.label-td {
            text-align: right;
            color: #888888;
        }

        .total-table td.val-td {
            text-align: right;
            color: #3A3063;
        }

        .total-table tr.grand-total td {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 900;
            color: #FF6FB7;
            border-top: 2px dashed #8DE3C7;
            padding-top: 15px;
        }

        /* ================= FOOTER ================= */
        .invoice-footer {
            text-align: center;
            border-top: 2px dashed #EEEEEE;
            padding-top: 30px;
            margin-top: 40px;
        }

        .invoice-footer h4 {
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 900;
            color: #3A3063;
            margin-bottom: 8px;
        }

        .invoice-footer p {
            color: #888888;
            font-weight: 700;
            font-size: 12px;
        }

        /* ================= ACTION BUTTONS ================= */
        .action-bar {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: 'Quicksand', sans-serif;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-back {
            background-color: #3A3063;
            color: white;
        }

        .btn-print {
            background-color: #27AE60;
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        /* ================= PRINT MEDIA ================= */
        @media print {
            body {
                padding: 0;
                background-color: #FFFFFF;
            }

            .action-bar {
                display: none;
            }

            .invoice-box {
                border: none;
                padding: 0;
                background-color: #FFFFFF;
            }
        }
    </style>
</head>

<body>

    <div class="action-bar">
        <a href="detail_pesanan.php?id=<?= $id_order ?>" class="btn btn-back">⬅ Kembali ke Detail</a>
        <button onclick="window.print()" class="btn btn-print">🖨 Cetak Struk</button>
    </div>

    <div class="invoice-box">

        <!-- HEADER -->
        <div class="invoice-header">
            <div class="brand-section">
                <h1>SQUASHY STORE</h1>
                <p>Belanja mainan lucu dan gemas!</p>
            </div>
            <div class="invoice-details">
                <h2>FAKTUR PEMBELIAN</h2>
                <p><strong>Nomor:</strong> <?= $display_id ?></p>
                <p><strong>Tanggal:</strong> <?= date('d M Y', strtotime($order['tanggal_pesanan'])) ?></p>
            </div>
        </div>

        <!-- BILLING GRID -->
        <div class="billing-grid">
            <div class="bill-card">
                <h3>Informasi Pelanggan</h3>
                <p><strong>Nama:</strong> <?= htmlspecialchars(strtoupper($order['nama_pembeli'])) ?></p>
                <p><strong>No. HP:</strong> <?= htmlspecialchars($order['nomer_hp'] ?? '-') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? '-') ?></p>
            </div>

            <div class="bill-card">
                <h3>Alamat Pengiriman</h3>
                <?php if ($address): ?>
                    <p><strong><?= htmlspecialchars($address['label_alamat'] ?? 'Alamat Pengiriman') ?></strong></p>
                    <p><?= htmlspecialchars($address['jalan']) ?></p>
                    <p>Kec. <?= htmlspecialchars($address['kecamatan']) ?>, Kab. <?= htmlspecialchars($address['kabupaten']) ?></p>
                    <p>Kode Pos: <?= htmlspecialchars($address['kodepos']) ?></p>
                <?php else: ?>
                    <p style="color: #888; font-style: italic;">Menggunakan alamat default/tidak tercatat.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_produk = 0;
                if (mysqli_num_rows($result_detail) > 0) {
                    mysqli_data_seek($result_detail, 0); // reset pointer ke awal
                    while ($item = mysqli_fetch_assoc($result_detail)) {
                        $subtotal = $item['harga_saat_order'] * $item['kuantitas'];
                        $total_produk += $subtotal;
                ?>
                        <tr>
                            <td><?= htmlspecialchars(strtoupper($item['nama_produk'])) ?></td>
                            <td style="text-align: right;">Rp. <?= number_format($item['harga_saat_order'], 0, ',', '.') ?></td>
                            <td style="text-align: center;"><?= $item['kuantitas'] ?></td>
                            <td style="text-align: right;">Rp. <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <!-- TOTALS & PAYMENT -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 30px;">
            <div class="bill-card" style="flex: 1;">
                <h3>Pembayaran</h3>
                <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars(strtoupper($payment['metode_pembayaran'] ?? 'BELUM DITENTUKAN')) ?></p>
                <p><strong>Status Transaksi:</strong> <?= htmlspecialchars(strtoupper($payment['status_pembayaran'] ?? 'PENDING')) ?></p>
                <?php if (!empty($payment['waktu_bayar'])): ?>
                    <p><strong>Waktu Bayar:</strong> <?= date('d M Y, H:i', strtotime($payment['waktu_bayar'])) ?></p>
                <?php endif; ?>
            </div>

            <div class="total-box">
                <?php $ongkir = intval($order['total_tagihan']) - $total_produk; ?>
                <table class="total-table">
                    <tr>
                        <td class="label-td">Total Barang</td>
                        <td class="val-td">Rp. <?= number_format($total_produk, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="label-td">Biaya Pengiriman</td>
                        <td class="val-td">Rp. <?= number_format(max(0, $ongkir), 0, ',', '.') ?></td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label-td">Total Tagihan</td>
                        <td class="val-td">Rp. <?= number_format($order['total_tagihan'], 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="invoice-footer">
            <h4>Terima Kasih Telah Berbelanja!</h4>
            <p>Jika Anda memiliki pertanyaan tentang invoice ini, silakan hubungi Customer Service Squashy.</p>
        </div>

    </div>

    <script>
        // Otomatis memicu cetak saat halaman dimuat
        window.addEventListener('DOMContentLoaded', () => {
            // Beri jeda sangat singkat agar render layout selesai sempurna
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>

</html>
