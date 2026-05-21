<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'pesanan';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: kelola_pesanan.php");
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
    header("Location: kelola_pesanan.php");
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

// Format tampilan ID
$display_id = "PLG-" . str_pad($order['id_order'], 5, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Detail Pesanan <?= $display_id ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --mint-bg: #8DE3C7;
            --cream-bg: #FFEAA7;
            --dark-purple: #3A3063;
            --orange-card: #F2994A;
            --green-card: #27AE60;
            --soft-green: #A8E6CF;
            --white: #FFFFFF;
            --pink-accent: #FF6FB7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--cream-bg);
            color: var(--dark-purple);
            min-height: 100vh;
            display: flex;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 260px;
            background-color: var(--mint-bg);
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .sidebar .logo-box {
            width: 160px;
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar .logo-box img {
            width: 100%;
            height: auto;
            display: block;
        }

        .menu-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 15px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            text-decoration: none;
            color: var(--dark-purple);
            font-weight: 700;
            font-size: 15px;
            border-radius: 15px;
            transition: all 0.2s;
        }

        .menu-item.active {
            background-color: transparent;
            box-shadow: none;
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
            position: relative;
            min-height: 100vh;
        }

        .header-dash {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .header-dash h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .btn-back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 999px;
            background: var(--dark-purple);
            color: var(--white);
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-back-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* ================= GRID LAYOUT ================= */
        .detail-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ================= CARDS ================= */
        .detail-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
        }

        .detail-card h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-bottom: 2px dashed #EEEEEE;
            padding-bottom: 12px;
            color: var(--dark-purple);
        }

        /* ================= INFO TABLE ================= */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 10px 0;
            font-size: 14px;
            font-weight: 700;
            vertical-align: top;
        }

        .info-table td.label-td {
            width: 140px;
            color: #888888;
        }

        /* ================= ITEM TABLE ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            background-color: #FFE1D6;
            padding: 14px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: left;
        }

        .items-table th:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .items-table th:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .items-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #EEEEEE;
            font-size: 14px;
            font-weight: 700;
            vertical-align: middle;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .product-thumb {
            width: 60px;
            height: 60px;
            background-color: #F5F5F5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            overflow: hidden;
        }

        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .item-name {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .total-row {
            background-color: #FDF9F3;
            border-radius: 15px;
            margin-top: 20px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px dashed var(--mint-bg);
        }

        .total-row span {
            font-size: 16px;
            font-weight: 900;
        }

        .total-row strong {
            font-family: 'Nunito', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: var(--pink-accent);
        }

        /* ================= STATUS BADGE ================= */
        .badge-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .pending {
            background-color: #FFE082;
            color: #5D4037;
        }

        .diproses {
            background-color: #FFCC80;
            color: #6D4C41;
        }

        .dikirim {
            background-color: #90CAF9;
            color: #0D47A1;
        }

        .selesai {
            background-color: #A5D6A7;
            color: #1B5E20;
        }

        .dibatalkan {
            background-color: #FFCDD2;
            color: #C62828;
        }

        /* ================= ACTION BUTTONS ================= */
        .action-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-action-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 15px;
            font-weight: 900;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            text-transform: uppercase;
        }

        .btn-action-button:hover {
            transform: translateY(-2px);
        }

        .btn-update-status {
            background-color: var(--pink-accent);
            color: white;
            box-shadow: 0 6px 15px rgba(255, 111, 183, 0.3);
        }

        .btn-print-invoice {
            background-color: var(--green-card);
            color: white;
            box-shadow: 0 6px 15px rgba(39, 174, 96, 0.3);
        }

        /* ================= DECOR ================= */
        .decor-flower-bottom {
            position: fixed;
            bottom: 0;
            right: 0;
            height: 90px;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>

<body>

    <?php require_once '../components/layout/header_admin.php'; ?>

    <div class="main-content">

        <div class="header-dash">
            <h1>DETAIL PESANAN: <?= $display_id ?></h1>
            <a href="kelola_pesanan.php" class="btn-back-link">📋 Kembali ke Pesanan</a>
        </div>

        <div class="detail-grid">

            <!-- LEFT COLUMN: ITEMS -->
            <div>
                <div class="detail-card">
                    <h3>Daftar Item Pesanan</h3>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result_detail) > 0) {
                                while ($item = mysqli_fetch_assoc($result_detail)) {
                                    $subtotal = $item['harga_saat_order'] * $item['kuantitas'];
                            ?>
                                    <tr>
                                        <td>
                                            <div class="item-info">
                                                <div class="product-thumb">
                                                    <?php if (!empty($item['foto_produk'])): ?>
                                                        <img src="../<?= htmlspecialchars($item['foto_produk']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                                                    <?php else: ?>
                                                        🧸
                                                    <?php endif; ?>
                                                </div>
                                                <span class="item-name"><?= htmlspecialchars($item['nama_produk']) ?></span>
                                            </div>
                                        </td>
                                        <td>Rp. <?= number_format($item['harga_saat_order'], 0, ',', '.') ?></td>
                                        <td style="text-align: center;"><?= $item['kuantitas'] ?></td>
                                        <td style="text-align: right; color: var(--dark-purple);">Rp. <?= number_format($subtotal, 0, ',', '.') ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px; color: #888;">Tidak ada rincian item untuk pesanan ini.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="total-row">
                        <span>TOTAL HAK PEMBAYARAN:</span>
                        <strong>Rp. <?= number_format($order['total_tagihan'], 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: ORDER & PAYMENT & CUSTOMER INFO -->
            <div>
                <!-- ORDER INFORMATION -->
                <div class="detail-card">
                    <h3>Rincian Transaksi</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label-td">ID Pesanan</td>
                            <td>#<?= $order['id_order'] ?> (<?= $display_id ?>)</td>
                        </tr>
                        <tr>
                            <td class="label-td">Tanggal Transaksi</td>
                            <td><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?></td>
                        </tr>
                        <tr>
                            <td class="label-td">Status Utama</td>
                            <td>
                                <?php $status_class = strtolower($order['status_pesanan'] ?? 'pending'); ?>
                                <span class="badge-status <?= $status_class ?>">
                                    <?= htmlspecialchars(ucfirst($order['status_pesanan'] ?? 'pending')) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- CUSTOMER & SHIPPING INFORMATION -->
                <div class="detail-card">
                    <h3>Informasi Pelanggan</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label-td">Nama Pelanggan</td>
                            <td><?= htmlspecialchars(strtoupper($order['nama_pembeli'])) ?></td>
                        </tr>
                        <tr>
                            <td class="label-td">No. Handphone</td>
                            <td><?= htmlspecialchars($order['nomer_hp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label-td">Email Pembeli</td>
                            <td><?= htmlspecialchars($order['email'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label-td">Alamat Kirim</td>
                            <td>
                                <?php if ($address): ?>
                                    <strong><?= htmlspecialchars($address['label_alamat'] ?? 'Alamat Utama') ?></strong><br>
                                    <?= htmlspecialchars($address['jalan']) ?><br>
                                    Kec. <?= htmlspecialchars($address['kecamatan']) ?>, Kab. <?= htmlspecialchars($address['kabupaten']) ?><br>
                                    Kode Pos: <?= htmlspecialchars($address['kodepos']) ?>
                                <?php else: ?>
                                    <span style="color:#C62828; font-style:italic;">Alamat pengiriman tidak diisi/tidak ditemukan.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- PAYMENT INFORMATION -->
                <div class="detail-card">
                    <h3>Status & Metode Pembayaran</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label-td">Metode</td>
                            <td><?= htmlspecialchars(strtoupper($payment['metode_pembayaran'] ?? 'Belum Ditentukan')) ?></td>
                        </tr>
                        <tr>
                            <td class="label-td">Status Pembayaran</td>
                            <td>
                                <?php $pay_status = strtolower($payment['status_pembayaran'] ?? 'pending'); ?>
                                <span class="badge-status <?= $pay_status ?>">
                                    <?= htmlspecialchars(ucfirst($payment['status_pembayaran'] ?? 'pending')) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">Waktu Bayar</td>
                            <td><?= !empty($payment['waktu_bayar']) ? date('d M Y, H:i', strtotime($payment['waktu_bayar'])) : '-' ?></td>
                        </tr>
                    </table>

                    <div class="action-container">
                        <a href="update_status.php?id=<?= $order['id_order'] ?>" class="btn-action-button btn-update-status">
                            🔄 Perbarui Status Pesanan
                        </a>
                        <a href="cetak_invoice.php?id=<?= $order['id_order'] ?>" target="_blank" class="btn-action-button btn-print-invoice">
                            🖨 Cetak Struk / Invoice
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

</body>

</html>
