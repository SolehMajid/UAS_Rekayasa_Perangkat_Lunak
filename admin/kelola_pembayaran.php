<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'status'; // Keep active sidebar tab aligned

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// Proses konfirmasi pembayaran
if (isset($_GET['action'], $_GET['id'])) {
    $order_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($order_id > 0) {
        // Cek status pesanan saat ini
        $check_sql = "SELECT status_pesanan FROM `order` WHERE id_order = $order_id";
        $check_res = mysqli_query($conn, $check_sql);
        $check_row = mysqli_fetch_assoc($check_res);
        $status_current = strtolower($check_row['status_pesanan'] ?? '');

        if ($status_current === 'selesai') {
            $_SESSION['error_msg'] = "Gagal: Pesanan yang telah dikonfirmasi selesai tidak dapat diubah status pembayarannya.";
        } elseif ($action === 'reset' && !in_array($status_current, ['pending', 'dibatalkan'])) {
            $_SESSION['error_msg'] = "Gagal: Pesanan yang telah lunas tidak dapat dikembalikan ke pending.";
        } else {
            if ($action === 'confirm') {
                // Update tabel order dan payment menjadi dibayar
                mysqli_query($conn, "UPDATE `order` SET status_pesanan = 'dibayar' WHERE id_order = $order_id");
                mysqli_query($conn, "UPDATE payment SET status_pembayaran = 'dibayar', waktu_bayar = NOW() WHERE id_order = $order_id");
                $_SESSION['success_msg'] = "Pembayaran untuk pesanan #PLG-" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " berhasil dikonfirmasi!";
            } elseif ($action === 'reset') {
                // Reset tabel order dan payment menjadi pending
                mysqli_query($conn, "UPDATE `order` SET status_pesanan = 'pending' WHERE id_order = $order_id");
                mysqli_query($conn, "UPDATE payment SET status_pembayaran = 'pending', waktu_bayar = NULL WHERE id_order = $order_id");
                $_SESSION['success_msg'] = "Status pembayaran untuk pesanan #PLG-" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " berhasil dikembalikan ke pending.";
            }
        }
    }

    header('Location: kelola_pembayaran.php');
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_pembayaran = isset($_GET['status_pembayaran']) ? mysqli_real_escape_string($conn, $_GET['status_pembayaran']) : 'Semua Status';

$where_clauses = ["1=1"];

if (!empty($search)) {
    $where_clauses[] = "(o.id_order LIKE '%$search%' OR o.nama_pembeli LIKE '%$search%')";
}

if ($status_pembayaran !== 'Semua Status') {
    $status_lower = strtolower($status_pembayaran);
    $where_clauses[] = "LOWER(p.status_pembayaran) = '$status_lower'";
}

$where_sql = implode(' AND ', $where_clauses);

$query_payments = "
SELECT
    o.id_order,
    o.nama_pembeli,
    o.tanggal_pesanan,
    o.total_tagihan,
    o.status_pesanan,
    p.id_payment,
    p.metode_pembayaran,
    p.status_pembayaran,
    p.waktu_bayar
FROM `order` o
LEFT JOIN payment p ON o.id_order = p.id_order
WHERE $where_sql
ORDER BY o.tanggal_pesanan DESC, o.id_order DESC
";

$result_payments = mysqli_query($conn, $query_payments);

// Metric summary data
$summary_sql = "
SELECT 
    COUNT(*) as total_transaksi,
    SUM(CASE WHEN LOWER(p.status_pembayaran) = 'dibayar' OR LOWER(p.status_pembayaran) = 'selesai' THEN 1 ELSE 0 END) as total_lunas,
    SUM(CASE WHEN LOWER(p.status_pembayaran) = 'pending' OR p.status_pembayaran IS NULL THEN 1 ELSE 0 END) as total_pending
FROM `order` o
LEFT JOIN payment p ON o.id_order = p.id_order
";
$summary_res = mysqli_query($conn, $summary_sql);
$summary_data = mysqli_fetch_assoc($summary_res);

$total_transaksi = $summary_data['total_transaksi'] ?? 0;
$total_lunas = $summary_data['total_lunas'] ?? 0;
$total_pending = $summary_data['total_pending'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Kelola Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --mint-bg: #8DE3C7;
            --cream-bg: #FFEAA7;
            --dark-purple: #3A3063;
            --green-card: #27AE60;
            --orange-card: #F2994A;
            --blue-card: #4DA8FF;
            --soft-green: #A8E6CF;
            --white: #FFFFFF;
            --text-muted: #666;
            --pink-card: #FF6FB7;
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

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
            min-height: 100vh;
        }

        .header-dash {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-dash h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
        }

        .btn-status-link,
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 999px;
            background: var(--mint-bg);
            color: var(--dark-purple);
            font-weight: 700;
            text-decoration: none;
            padding: 12px 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-status-link:hover,
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .top-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 22px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .stat-card span {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            color: #777;
        }

        .stat-card h2 {
            font-size: 30px;
            font-weight: 900;
        }

        .status-chip {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .chip-pending {
            background: var(--pink-card);
        }

        .chip-lunas {
            background: var(--green-card);
        }

        .table-box {
            background: white;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .table-box h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 22px;
        }

        .filter-form-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
            align-items: center;
        }

        .filter-form-bar input,
        .filter-form-bar select {
            border: 2px solid rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 14px;
            min-width: 190px;
            outline: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }

        .filter-form-bar button {
            border: none;
            background: var(--dark-purple);
            color: white;
            border-radius: 16px;
            padding: 12px 20px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Quicksand', sans-serif;
            transition: all 0.2s ease;
        }

        .filter-form-bar button:hover {
            opacity: 0.9;
        }

        .squashy-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .squashy-table th,
        .squashy-table td {
            padding: 16px 18px;
            border-bottom: 1px solid #F0F0F0;
            text-align: left;
        }

        .squashy-table th {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.8px;
            color: #555;
            text-transform: uppercase;
        }

        .squashy-table tbody tr:hover {
            background: #FAFAFA;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-action {
            border: none;
            padding: 10px 12px;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
            color: var(--white);
            min-width: 95px;
            text-align: center;
            font-family: 'Quicksand', sans-serif;
        }

        .bg-confirm {
            background: var(--green-card);
        }

        .bg-reset {
            background: var(--orange-card);
        }

        .info-note {
            background: #FFF7E6;
            border-left: 4px solid #FFB847;
            padding: 18px 22px;
            border-radius: 18px;
            margin-bottom: 24px;
            color: #5C4A00;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 1024px) {
            .top-cards-grid {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }

            .main-content {
                padding: 24px;
            }
        }

        @media (max-width: 720px) {
            .top-cards-grid {
                grid-template-columns: 1fr;
            }

            .squashy-table {
                min-width: 100%;
            }

            .header-dash {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-status-link {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <?php require_once '../components/layout/header_admin.php'; ?>

    <div class="main-content">

        <div class="header-dash">
            <div>
                <h1>KELOLA PEMBAYARAN</h1>
                <p style="color: var(--text-muted); margin-top: 8px;">Validasi konfirmasi transaksi pembayaran yang diajukan oleh pelanggan.</p>
            </div>
            <a href="kelola_pesanan.php" class="btn-status-link">📋 Kembali ke Kelola Pesanan</a>
        </div>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert-success" style="background-color: #D4EDDA; color: #155724; border-left: 5px solid #28A745; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; font-weight: 700; font-size: 14px;">
                🎉 <?= $_SESSION['success_msg'] ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert-error" style="background-color: #FFEBEE; color: #C62828; border-left: 5px solid #E53935; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; font-weight: 700; font-size: 14px;">
                ⚠️ <?= $_SESSION['error_msg'] ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <div class="top-cards-grid">
            <div class="stat-card">
                <span>Total Transaksi</span>
                <h2><?= number_format($total_transaksi, 0, ',', '.') ?></h2>
            </div>
            <div class="stat-card">
                <span>Lunas / Dibayar</span>
                <h2><?= number_format($total_lunas, 0, ',', '.') ?></h2>
            </div>
            <div class="stat-card">
                <span>Pending / Menunggu</span>
                <h2><?= number_format($total_pending, 0, ',', '.') ?></h2>
            </div>
        </div>

        <div class="info-note">
            Klik tombol "Konfirmasi Lunas" untuk memverifikasi pembayaran. Gunakan tombol "Batal Konfirmasi" jika terjadi kesalahan validasi transfer/pembayaran.
        </div>

        <div class="table-box">
            <h3>DAFTAR TRANSAKSI PEMBAYARAN</h3>

            <form method="GET" class="filter-form-bar">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari ID / Nama Pelanggan..."
                    value="<?= htmlspecialchars($search) ?>">

                <select name="status_pembayaran">
                    <option value="Semua Status" <?= $status_pembayaran == 'Semua Status' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= $status_pembayaran == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="dibayar" <?= $status_pembayaran == 'dibayar' ? 'selected' : '' ?>>Dibayar</option>
                    <option value="selesai" <?= $status_pembayaran == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>

                <button type="submit">Terapkan Filter</button>
            </form>

            <table class="squashy-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Metode Bayar</th>
                        <th>Total Tagihan</th>
                        <th>Status Pembayaran</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_payments && mysqli_num_rows($result_payments) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_payments)): ?>
                            <?php $display_id = "PLG-" . str_pad($row['id_order'], 5, "0", STR_PAD_LEFT); ?>
                            <tr>
                                <td><?= $display_id ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_pesanan'])) ?></td>
                                <td><?= htmlspecialchars(strtoupper($row['nama_pembeli'])) ?></td>
                                <td><strong><?= htmlspecialchars(strtoupper($row['metode_pembayaran'] ?? '-')) ?></strong></td>
                                <td>Rp. <?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                    $is_lunas = strtolower($row['status_pembayaran']) === 'dibayar' || strtolower($row['status_pembayaran']) === 'selesai';
                                    $status_label = $is_lunas ? 'chip-lunas' : 'chip-pending'; 
                                    $status_text = $is_lunas ? 'Lunas' : 'Pending';
                                    ?>
                                    <span class="status-chip <?= $status_label ?>"><?= $status_text ?></span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="actions-cell">
                                        <?php if (strtolower($row['status_pesanan']) === 'selesai'): ?>
                                            <span style="font-weight: 700; color: var(--green-card); font-size: 0.85rem;">🔒 Selesai (Terkunci)</span>
                                        <?php elseif ($is_lunas): ?>
                                            <span style="font-weight: 700; color: var(--green-card); font-size: 0.85rem;">🔒 Lunas (Terkunci)</span>
                                        <?php else: ?>
                                            <a href="kelola_pembayaran.php?id=<?= $row['id_order'] ?>&action=confirm" class="btn-action bg-confirm">Konfirmasi Lunas</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:32px; color:#888;">Tidak ada data transaksi pembayaran ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
