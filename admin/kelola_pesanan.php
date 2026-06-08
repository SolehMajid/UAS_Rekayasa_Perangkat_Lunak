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

// =========================
// FILTER
// =========================

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'Semua Status';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';


// =========================
// METRIC DATA
// =========================

// Total Pesanan Bulan Ini
$q_total = "
SELECT COUNT(*) as total
FROM `order`
WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE())
AND YEAR(tanggal_pesanan) = YEAR(CURDATE())
";

$r_total = mysqli_query($conn, $q_total);
$total_pesanan = mysqli_fetch_assoc($r_total)['total'] ?? 0;


// Pending
$q_pending = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'pending'
";

$r_pending = mysqli_query($conn, $q_pending);
$menunggu_validasi = mysqli_fetch_assoc($r_pending)['total'] ?? 0;


// Dibayar
$q_dibayar = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'dibayar'
";

$r_dibayar = mysqli_query($conn, $q_dibayar);
$dibayar = mysqli_fetch_assoc($r_dibayar)['total'] ?? 0;


// Diproses
$q_proses = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'diproses'
";

$r_proses = mysqli_query($conn, $q_proses);
$diproses = mysqli_fetch_assoc($r_proses)['total'] ?? 0;


// Dikirim
$q_kirim = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'dikirim'
";

$r_kirim = mysqli_query($conn, $q_kirim);
$dikirim = mysqli_fetch_assoc($r_kirim)['total'] ?? 0;


// Selesai
$q_selesai = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'selesai'
";

$r_selesai = mysqli_query($conn, $q_selesai);
$selesai = mysqli_fetch_assoc($r_selesai)['total'] ?? 0;


// Dibatalkan
$q_batal = "
SELECT COUNT(*) as total
FROM payment
WHERE LOWER(status_pembayaran) = 'dibatalkan'
";

$r_batal = mysqli_query($conn, $q_batal);
$dibatalkan = mysqli_fetch_assoc($r_batal)['total'] ?? 0;


// =========================
// FILTER QUERY
// =========================

$where_clauses = ["1=1"];

if (!empty($search)) {
    $where_clauses[] = "(o.id_order LIKE '%$search%' OR o.nama_pembeli LIKE '%$search%' OR o.id_user LIKE '%$search%')";
}

if ($status !== 'Semua Status') {
    $status_lower = strtolower($status);
    $where_clauses[] = "LOWER(p.status_pembayaran) = '$status_lower'";
}

if (!empty($start_date) && !empty($end_date)) {
    $where_clauses[] = "DATE(o.tanggal_pesanan) BETWEEN '$start_date' AND '$end_date'";
}

$where_sql = implode(' AND ', $where_clauses);


// =========================
// QUERY PESANAN
// =========================

$query_orders = "
SELECT 
    o.id_order,
    o.nama_pembeli,
    o.tanggal_pesanan,
    o.total_tagihan,
    p.status_pembayaran
FROM `order` o
LEFT JOIN payment p ON o.id_order = p.id_order
WHERE $where_sql
ORDER BY o.tanggal_pesanan DESC, o.id_order DESC
";

$result_orders = mysqli_query($conn, $query_orders);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Kelola Pesanan</title>

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

        /* .menu-item:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.4);
        } */

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

        .btn-status-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 999px;
            background: var(--mint-bg);
            color: var(--dark-purple);
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-status-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* ================= TOP METRICS ================= */

        .top-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        .color-block {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .orange {
            background-color: var(--orange-card);
        }

        .green {
            background-color: var(--green-card);
        }

        .blue {
            background-color: #4DA8FF;
        }

        .pink {
            background-color: #FF6FB7;
        }

        .purple {
            background-color: #7d5fff;
        }

        .red {
            background-color: #EB5757;
        }

        .stat-info span {
            font-size: 12px;
            font-weight: 700;
            color: #777;
            text-transform: uppercase;
        }

        .stat-info h2 {
            font-size: 24px;
            font-weight: 900;
            margin-top: 3px;
        }

        /* ================= TABLE BOX ================= */

        .table-box {
            background: white;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        .table-box h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* ================= FILTER ================= */

        .filter-form-bar {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .input-search,
        .select-status,
        .input-date {
            background: white;
            border: 2px solid var(--soft-green);
            padding: 10px 16px;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            outline: none;
        }

        .input-search {
            width: 250px;
        }

        .select-status {
            min-width: 160px;
            cursor: pointer;
        }

        .input-date {
            width: 170px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 700;
        }

        .btn-filter {
            background-color: var(--dark-purple);
            color: white;
            border: none;
            padding: 11px 22px;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-filter:hover {
            opacity: 0.9;
        }

        /* ================= TABLE ================= */

        .squashy-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        .squashy-table thead tr {
            background-color: #FFE1D6;
        }

        .squashy-table th {
            padding: 16px;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 900;
            text-align: left;
        }

        .squashy-table td {
            padding: 18px 16px;
            border-bottom: 1px solid #EEEEEE;
            font-size: 13px;
            font-weight: 700;
        }

        .squashy-table tbody tr:hover {
            background-color: #FFF9EF;
        }

        /* ================= STATUS BADGE ================= */

        .badge-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .pending {
            background-color: #FFE082;
            color: #5D4037;
        }

        .dibayar {
            background-color: #B2DFDB;
            color: #004D40;
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
            color: #B71C1C;
        }

        /* ================= ACTION BUTTONS ================= */

        .actions-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            font-size: 16px;
            transition: 0.2s;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        .bg-detail {
            background-color: #F2994A;
        }

        .bg-update {
            background-color: #FF6FB7;
        }

        .bg-print {
            background-color: #27AE60;
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

    <!-- MAIN CONTENT -->

    <div class="main-content">

        <div class="header-dash">
            <h1>KELOLA PESANAN</h1>
            <a href="kelola_pembayaran.php" class="btn-status-link">💳 Kelola Pembayaran</a>
        </div>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert-success" style="background-color: #D4EDDA; color: #155724; border-left: 5px solid #28A745; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; font-weight: 700; font-size: 14px;">
                🎉 <?= $_SESSION['success_msg'] ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <!-- TOP CARDS -->

        <div class="top-cards-grid">

            <div class="stat-card">
                <div class="color-block purple"></div>
                <div class="stat-info">
                    <span>Total Pesanan</span>
                    <h2><?= number_format($total_pesanan, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block pink"></div>
                <div class="stat-info">
                    <span>Pending</span>
                    <h2><?= number_format($menunggu_validasi, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block blue" style="background-color: #B2DFDB;"></div>
                <div class="stat-info">
                    <span>Dibayar</span>
                    <h2><?= number_format($dibayar, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block orange"></div>
                <div class="stat-info">
                    <span>Diproses</span>
                    <h2><?= number_format($diproses, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block blue"></div>
                <div class="stat-info">
                    <span>Dikirim</span>
                    <h2><?= number_format($dikirim, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block green"></div>
                <div class="stat-info">
                    <span>Selesai</span>
                    <h2><?= number_format($selesai, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block red"></div>
                <div class="stat-info">
                    <span>Dibatalkan</span>
                    <h2><?= number_format($dibatalkan, 0, ',', '.') ?></h2>
                </div>
            </div>

        </div>

        <!-- TABLE -->

        <div class="table-box">

            <h3>DAFTAR PESANAN</h3>

            <form method="GET" class="filter-form-bar">

                <input
                    type="text"
                    name="search"
                    class="input-search"
                    placeholder="Cari ID / Nama Pelanggan..."
                    value="<?= htmlspecialchars($search) ?>">

                <select name="status" class="select-status">
                    <option value="Semua Status" <?= $status == 'Semua Status' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Dibayar" <?= $status == 'Dibayar' ? 'selected' : '' ?>>Dibayar</option>
                    <option value="Diproses" <?= $status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="Dikirim" <?= $status == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <option value="Selesai" <?= $status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="Dibatalkan" <?= $status == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>

                <input
                    type="date"
                    name="start_date"
                    class="input-date"
                    value="<?= htmlspecialchars($start_date) ?>">

                <span class="filter-label">Sampai</span>

                <input
                    type="date"
                    name="end_date"
                    class="input-date"
                    value="<?= htmlspecialchars($end_date) ?>">

                <button type="submit" class="btn-filter">
                    Terapkan Filter
                </button>

            </form>

            <table class="squashy-table">

                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    if (mysqli_num_rows($result_orders) > 0) {

                        while ($row = mysqli_fetch_assoc($result_orders)) {

                            $display_id = "PLG-" . str_pad($row['id_order'], 5, "0", STR_PAD_LEFT);

                            $status_class = strtolower($row['status_pembayaran']);
                    ?>

                            <tr>

                                <td><?= $display_id ?></td>

                                <td>
                                    <?= date('d M Y', strtotime($row['tanggal_pesanan'])) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(strtoupper($row['nama_pembeli'])) ?>
                                </td>

                                <td>
                                    Rp. <?= number_format($row['total_tagihan'], 0, ',', '.') ?>
                                </td>

                                <td>
                                    <span class="badge-status <?= $status_class ?>">
                                        <?= htmlspecialchars($row['status_pembayaran']) ?>
                                    </span>
                                </td>

                                <td>

                                    <div class="actions-cell" style="justify-content:center;">

                                        <a href="detail_pesanan.php?id=<?= $row['id_order'] ?>"
                                            class="btn-action bg-detail"
                                            title="Detail">
                                            👁
                                        </a>

                                        <a href="update_status.php?id=<?= $row['id_order'] ?>"
                                            class="btn-action bg-update"
                                            title="Update">
                                            🔄
                                        </a>

                                        <a href="cetak_invoice.php?id=<?= $row['id_order'] ?>"
                                            class="btn-action bg-print"
                                            title="Print">
                                            🖨
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php }
                    } else { ?>

                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#888;">
                                Tidak ada data pesanan ditemukan.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

</body>

</html>