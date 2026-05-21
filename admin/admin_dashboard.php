<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'dashboard';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// 1. Filter Rentang Waktu
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'bulan';

switch ($filter) {
    case 'minggu':
        $time_condition = "YEARWEEK(tanggal_pesanan, 1) = YEARWEEK(CURDATE(), 1)";
        break;

    case 'tahun':
        $time_condition = "YEAR(tanggal_pesanan) = YEAR(CURDATE())";
        break;

    case 'bulan':
    default:
        $time_condition = "MONTH(tanggal_pesanan) = MONTH(CURDATE()) 
                           AND YEAR(tanggal_pesanan) = YEAR(CURDATE())";
        break;
}

// 2. Total Pendapatan
$query_income = "
SELECT SUM(total_tagihan) AS total_pendapatan 
FROM `order`
WHERE $time_condition
";

$result_income = mysqli_query($conn, $query_income);
$row_income = mysqli_fetch_assoc($result_income);

$total_pendapatan = $row_income['total_pendapatan'] ?? 0;

// 3. Total Produk Terjual
$query_sold = "
SELECT SUM(od.kuantitas) AS total_terjual
FROM order_detail od
JOIN `order` o ON od.id_order = o.id_order
WHERE $time_condition
";

$result_sold = mysqli_query($conn, $query_sold);
$row_sold = mysqli_fetch_assoc($result_sold);

$total_terjual = $row_sold['total_terjual'] ?? 0;

// 4. Tren Penjualan
$query_trend = "
SELECT 
    WEEK(tanggal_pesanan) - WEEK(DATE_SUB(tanggal_pesanan, INTERVAL DAYOFMONTH(tanggal_pesanan)-1 DAY)) + 1 AS minggu_ke,
    SUM(total_tagihan) AS total_mingguan
FROM `order`
WHERE MONTH(tanggal_pesanan) = MONTH(CURDATE()) 
AND YEAR(tanggal_pesanan) = YEAR(CURDATE())
GROUP BY minggu_ke
ORDER BY minggu_ke ASC
LIMIT 4
";

$result_trend = mysqli_query($conn, $query_trend);

$trend_data = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

while ($row = mysqli_fetch_assoc($result_trend)) {
    $m = (int)$row['minggu_ke'];

    if ($m >= 1 && $m <= 4) {
        $trend_data[$m] = $row['total_mingguan'];
    }
}

$max_trend = max($trend_data) > 0 ? max($trend_data) : 1;

// 5. Produk Terlaris
$query_top_products = "
SELECT 
    pr.nama_produk,
    SUM(od.kuantitas) AS total_produk_terjual
FROM order_detail od
JOIN produk pr ON od.id_produk = pr.id_produk
JOIN `order` o ON od.id_order = o.id_order
WHERE $time_condition
GROUP BY od.id_produk
ORDER BY total_produk_terjual DESC
LIMIT 3
";

$result_top_products = mysqli_query($conn, $query_top_products);

// 6. Penjualan Per Kategori
$query_categories = "
SELECT 
    k.nama_kategori,
    SUM(od.kuantitas) AS jumlah_kategori
FROM order_detail od
JOIN produk pr ON od.id_produk = pr.id_produk
JOIN kategori k ON pr.id_kategori = k.id_kategori
JOIN `order` o ON od.id_order = o.id_order
WHERE $time_condition
GROUP BY k.id_kategori
";

$result_categories = mysqli_query($conn, $query_categories);

$categories_data = [];
$total_kategori_all = 0;

while ($row = mysqli_fetch_assoc($result_categories)) {
    $categories_data[] = $row;
    $total_kategori_all += $row['jumlah_kategori'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Dashboard Penjualan</title>
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

        /* ── SIDEBAR ── */
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

        /* ── MAIN CONTENT AREA ── */
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
        }

        .header-dash h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .filter-time-box {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .filter-time-box label {
            font-size: 12px;
            font-weight: 700;
        }

        .select-custom {
            background: white;
            border: 2px solid var(--soft-green);
            padding: 8px 15px;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            outline: none;
            cursor: pointer;
            min-width: 130px;
        }

        /* ── TOP CARDS ROW ── */
        .top-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        .color-block {
            width: 45px;
            height: 45px;
            border-radius: 12px;
        }

        .color-block.orange {
            background-color: var(--orange-card);
        }

        .color-block.green {
            background-color: var(--soft-green);
        }

        .stat-info span {
            font-size: 13px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
        }

        .stat-info h2 {
            font-size: 24px;
            font-weight: 900;
            margin-top: 2px;
        }

        /* ── CHARTS SECTIONS GRID ── */
        .charts-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 25px;
        }

        .chart-box {
            background: white;
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        .chart-box h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        /* Bar Chart Tren Penjualan */
        .simple-trend-container {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 160px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E0E0E0;
            margin-bottom: 25px;
        }

        .trend-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 15%;
        }

        .trend-bar {
            width: 100%;
            background: linear-gradient(to top, #FF6FB7, #FF852D);
            border-radius: 8px 8px 0 0;
            transition: height 0.5s ease;
            position: relative;
        }

        .trend-bar::after {
            content: attr(data-value);
            position: absolute;
            top: -22px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 900;
            color: var(--dark-purple);
            white-space: nowrap;
        }

        .trend-label {
            font-size: 12px;
            font-weight: 700;
            color: #666;
        }

        /* Kategori List Progress Bar */
        .category-list-wrapper {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .category-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .category-info-text {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
        }

        .progress-bar-bg {
            background-color: #F0F0F0;
            height: 12px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 10px;
        }

        /* ── TOP PRODUCTS LIST ── */
        .top-products-section {
            margin-top: 25px;
            border-top: 2px dashed #E0E0E0;
            padding-top: 20px;
        }

        .top-products-section h4 {
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 15px;
        }

        .product-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #F9F9F9;
            padding: 12px 20px;
            border-radius: 15px;
            margin-bottom: 10px;
        }

        .prod-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .prod-thumb-dummy {
            width: 40px;
            height: 40px;
            background-color: #E0E0E0;
            border-radius: 10px;
        }

        .prod-name {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .prod-count {
            font-size: 13px;
            font-weight: 700;
            text-align: right;
        }

        .prod-count span {
            display: block;
            font-size: 11px;
            color: #888;
        }

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
            <h1>DASHBOARD PENJUALAN</h1>

            <div class="header-right">
                <div class="filter-time-box">
                    <label>Pilih Rentang Waktu</label>

                    <select class="select-custom" onchange="location = this.value;">
                        <option value="?filter=minggu" <?= $filter == 'minggu' ? 'selected' : '' ?>>
                            Minggu Ini
                        </option>

                        <option value="?filter=bulan" <?= $filter == 'bulan' ? 'selected' : '' ?>>
                            Bulan Ini
                        </option>

                        <option value="?filter=tahun" <?= $filter == 'tahun' ? 'selected' : '' ?>>
                            Tahun Ini
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TOP CARDS -->

        <div class="top-cards-grid">

            <div class="stat-card">
                <div class="color-block orange"></div>

                <div class="stat-info">
                    <span>
                        Total Pendapatan <?= ucfirst($filter) ?> Ini
                    </span>

                    <h2>
                        Rp. <?= number_format($total_pendapatan, 0, ',', '.') ?>
                    </h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block green"></div>

                <div class="stat-info">
                    <span>Total Produk Terjual</span>

                    <h2>
                        <?= number_format($total_terjual, 0, ',', '.') ?>
                    </h2>
                </div>
            </div>

        </div>

        <!-- CHARTS -->

        <div class="charts-grid">

            <!-- LEFT -->

            <div class="chart-box">

                <h3>Tren Penjualan Bulanan (Per Minggu)</h3>

                <div class="simple-trend-container">

                    <?php for ($i = 1; $i <= 4; $i++):

                        $height_percentage = ($trend_data[$i] / $max_trend) * 100;

                        $short_value = $trend_data[$i] >= 1000000
                            ? number_format($trend_data[$i] / 1000000, 1) . 'M'
                            : number_format($trend_data[$i] / 1000, 0) . 'K';
                    ?>

                        <div class="trend-bar-wrapper">

                            <div
                                class="trend-bar"
                                style="height: <?= $height_percentage ?>%;"
                                data-value="<?= $trend_data[$i] > 0 ? $short_value : 'Rp0' ?>">
                            </div>

                            <span class="trend-label">
                                Minggu <?= $i ?>
                            </span>

                        </div>

                    <?php endfor; ?>

                </div>

                <!-- TOP PRODUCT -->

                <div class="top-products-section">

                    <h4>PRODUK TERLARIS</h4>

                    <?php
                    if (mysqli_num_rows($result_top_products) > 0) {

                        while ($prod = mysqli_fetch_assoc($result_top_products)) {
                    ?>

                            <div class="product-strip">

                                <div class="prod-left">

                                    <div class="prod-thumb-dummy"></div>

                                    <span class="prod-name">
                                        <?= htmlspecialchars($prod['nama_produk']) ?>
                                    </span>

                                </div>

                                <div class="prod-count">

                                    <?= $prod['total_produk_terjual'] ?>

                                    <span>Terjual</span>

                                </div>

                            </div>

                        <?php }
                    } else { ?>

                        <p style="font-size: 13px; color: #888;">
                            Belum ada data penjualan pada rentang waktu ini.
                        </p>

                    <?php } ?>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="chart-box">

                <h3>Penjualan Per Kategori</h3>

                <div class="category-list-wrapper">

                    <?php

                    $colors = [
                        '#4DC8F0',
                        '#FF6FB7',
                        '#FFD93D',
                        '#27AE60',
                        '#F2994A'
                    ];

                    if ($total_kategori_all > 0) {

                        foreach ($categories_data as $index => $cat) {

                            $percentage = round(
                                ($cat['jumlah_kategori'] / $total_kategori_all) * 100
                            );

                            $color = $colors[$index % count($colors)];
                    ?>

                            <div class="category-row">

                                <div class="category-info-text">

                                    <span>
                                        📦 <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </span>

                                    <span>
                                        <?= $percentage ?>%
                                    </span>

                                </div>

                                <div class="progress-bar-bg">

                                    <div
                                        class="progress-bar-fill"
                                        style="
                                        width: <?= $percentage ?>%;
                                        background-color: <?= $color ?>;
                                    ">
                                    </div>

                                </div>

                            </div>

                        <?php }
                    } else { ?>

                        <p style="font-size: 13px; color: #888;">
                            Belum ada data kategori.
                        </p>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>

    <img
        src="../assets/images/decor-right.png"
        class="decor-flower-bottom">

</body>

</html>