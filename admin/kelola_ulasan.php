<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'ulasan';

// Optional: check admin login to match kelola_pesan.php
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['id_admin'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// ==========================================
// ACTION HANDLER (POST & GET ACTIONS)
// ==========================================

// 1. Reply Action (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    $id_review = intval($_POST['id_review'] ?? 0);
    $balasan_admin = mysqli_real_escape_string($conn, trim($_POST['balasan_admin'] ?? ''));

    if ($id_review > 0 && !empty($balasan_admin)) {
        $update_q = "UPDATE review_produk SET balasan_admin = '$balasan_admin' WHERE id_review = $id_review";
        if (mysqli_query($conn, $update_q)) {
            $_SESSION['success_msg'] = "Balasan ulasan berhasil disimpan!";
        } else {
            $_SESSION['error_msg'] = "Gagal menyimpan balasan: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error_msg'] = "ID Ulasan atau Teks Balasan tidak valid.";
    }
    
    // Redirect to preserve search query parameters if any
    $redirect_url = "kelola_ulasan.php";
    $query_params = [];
    if (!empty($_GET['search'])) $query_params['search'] = $_GET['search'];
    if (!empty($_GET['rating'])) $query_params['rating'] = $_GET['rating'];
    if (!empty($_GET['status_balasan'])) $query_params['status_balasan'] = $_GET['status_balasan'];
    if (!empty($query_params)) {
        $redirect_url .= '?' . http_build_query($query_params);
    }
    header("Location: " . $redirect_url);
    exit;
}

// 2. Delete Action (POST via GET parameter confirmation for ease of use)
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id_review = intval($_GET['id'] ?? 0);
    if ($id_review > 0) {
        // Fetch review details first to clean up the uploaded image
        $review_q = mysqli_query($conn, "SELECT foto_review FROM review_produk WHERE id_review = $id_review LIMIT 1");
        if ($review_q && mysqli_num_rows($review_q) > 0) {
            $review = mysqli_fetch_assoc($review_q);
            $foto_path = $review['foto_review'];
            if (!empty($foto_path)) {
                $absolute_foto_path = __DIR__ . '/../' . $foto_path;
                if (file_exists($absolute_foto_path) && is_file($absolute_foto_path)) {
                    @unlink($absolute_foto_path);
                }
            }
        }

        // Delete from database
        $delete_q = "DELETE FROM review_produk WHERE id_review = $id_review LIMIT 1";
        if (mysqli_query($conn, $delete_q)) {
            $_SESSION['success_msg'] = "Ulasan berhasil dihapus secara permanen.";
        } else {
            $_SESSION['error_msg'] = "Gagal menghapus ulasan: " . mysqli_error($conn);
        }
    }
    
    $redirect_url = "kelola_ulasan.php";
    $query_params = [];
    if (!empty($_GET['search'])) $query_params['search'] = $_GET['search'];
    if (!empty($_GET['rating'])) $query_params['rating'] = $_GET['rating'];
    if (!empty($_GET['status_balasan'])) $query_params['status_balasan'] = $_GET['status_balasan'];
    if (!empty($query_params)) {
        $redirect_url .= '?' . http_build_query($query_params);
    }
    header("Location: " . $redirect_url);
    exit;
}


// ==========================================
// FETCHING TOP METRICS DATA
// ==========================================

// Total Ulasan
$r_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM review_produk");
$total_ulasan = mysqli_fetch_assoc($r_total)['total'] ?? 0;

// Rata-rata Rating
$r_avg = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM review_produk");
$avg_rating = round(mysqli_fetch_assoc($r_avg)['avg_rating'] ?? 0, 1);

// Sudah Dibalas
$r_replied = mysqli_query($conn, "SELECT COUNT(*) as total FROM review_produk WHERE balasan_admin IS NOT NULL AND balasan_admin != ''");
$sudah_dibalas = mysqli_fetch_assoc($r_replied)['total'] ?? 0;

// Belum Dibalas
$r_unreplied = mysqli_query($conn, "SELECT COUNT(*) as total FROM review_produk WHERE balasan_admin IS NULL OR balasan_admin = ''");
$belum_dibalas = mysqli_fetch_assoc($r_unreplied)['total'] ?? 0;


// ==========================================
// FILTER & SEARCH QUERY BUILDING
// ==========================================

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : 'Semua';
$status_balasan_filter = isset($_GET['status_balasan']) ? $_GET['status_balasan'] : 'Semua';

$where_clauses = ["1=1"];

if (!empty($search)) {
    $where_clauses[] = "(u.nama_lengkap LIKE '%$search%' OR p.nama_produk LIKE '%$search%' OR r.komentar LIKE '%$search%' OR r.id_order LIKE '%$search%')";
}

if ($rating_filter !== 'Semua') {
    $rating_val = intval($rating_filter);
    $where_clauses[] = "r.rating = $rating_val";
}

if ($status_balasan_filter === 'belum') {
    $where_clauses[] = "(r.balasan_admin IS NULL OR r.balasan_admin = '')";
} elseif ($status_balasan_filter === 'sudah') {
    $where_clauses[] = "(r.balasan_admin IS NOT NULL AND r.balasan_admin != '')";
}

$where_sql = implode(' AND ', $where_clauses);


// ==========================================
// PAGINATION LOGIC
// ==========================================

$limit = 6; // item per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

$count_q = "
SELECT COUNT(*) as total 
FROM review_produk r
JOIN user u ON r.id_user = u.id_user
JOIN produk p ON r.id_produk = p.id_produk
WHERE $where_sql
";
$r_count = mysqli_query($conn, $count_q);
$total_records = mysqli_fetch_assoc($r_count)['total'] ?? 0;
$total_pages = ceil($total_records / $limit);
if ($total_pages < 1) $total_pages = 1;
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $limit;


// ==========================================
// RUN MAIN QUERY
// ==========================================

$query_reviews = "
SELECT r.*, u.nama_lengkap, p.nama_produk, p.foto as foto_produk
FROM review_produk r
JOIN user u ON r.id_user = u.id_user
JOIN produk p ON r.id_produk = p.id_produk
WHERE $where_sql
ORDER BY r.created_at DESC
LIMIT $limit OFFSET $offset
";
$result_reviews = mysqli_query($conn, $query_reviews);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Kelola Ulasan</title>

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
            --pink-card: #FF6FB7;
            --blue-card: #4DA8FF;
            --soft-pink: #FFEBF5;
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
            text-transform: uppercase;
        }

        /* ================= TOP METRICS ================= */

        .top-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .color-block {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .orange { background-color: var(--orange-card); }
        .pink { background-color: var(--pink-card); }
        .green { background-color: var(--green-card); }
        .blue { background-color: var(--blue-card); }

        .stat-info span {
            font-size: 12px;
            font-weight: 700;
            color: #777;
            text-transform: uppercase;
            display: block;
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
            margin-bottom: 30px;
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
        .btn-filter {
            background: white;
            border: 2px solid var(--soft-green);
            padding: 10px 16px;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            outline: none;
            font-size: 14px;
        }

        .input-search {
            width: 280px;
        }

        .select-status {
            min-width: 160px;
            cursor: pointer;
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
            cursor: pointer;
            transition: 0.2s;
            text-transform: uppercase;
            font-weight: 900;
        }

        .btn-filter:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        /* ================= TABLE ================= */

        .squashy-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 15px;
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
            vertical-align: top;
        }

        .squashy-table tbody tr:hover {
            background-color: #FFF9EF;
        }

        /* ================= CUSTOM ELEMENTS ================= */

        .stars-display {
            color: #FFD93D;
            font-size: 16px;
            letter-spacing: 2px;
            display: inline-block;
        }

        .reviewer-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .reviewer-meta h4 {
            font-size: 14px;
            font-weight: 900;
            color: var(--dark-purple);
        }

        .reviewer-meta span {
            font-size: 11px;
            color: #888;
        }

        .product-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-meta img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            background: #eee;
            border: 1px solid #ddd;
        }

        .review-text-block {
            font-size: 13px;
            line-height: 1.5;
            color: #444;
            max-width: 320px;
            word-wrap: break-word;
        }

        .review-photo-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 8px;
            cursor: zoom-in;
            border: 2px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
        }

        .review-photo-preview:hover {
            transform: scale(1.05);
        }

        /* Balasan Box */
        .admin-reply-box {
            margin-top: 10px;
            background: #F3FFF9;
            border-left: 4px solid var(--green-card);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: #2E5A44;
            max-width: 320px;
            position: relative;
        }

        .admin-reply-box strong {
            display: block;
            margin-bottom: 3px;
            color: var(--green-card);
            font-size: 10px;
            text-transform: uppercase;
        }

        /* Badge Status */
        .badge-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .replied {
            background-color: #E2F8EE;
            color: #27AE60;
        }

        .unreplied {
            background-color: #FFEBEE;
            color: #EB5757;
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
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .bg-reply {
            background-color: #4DA8FF;
        }

        .bg-delete {
            background-color: #EB5757;
        }

        /* ================= PAGINATION ================= */

        .pagination-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 25px;
        }

        .pagination-bar a,
        .pagination-bar span {
            text-decoration: none;
            color: var(--dark-purple);
            padding: 8px 16px;
            border-radius: 12px;
            background: white;
            border: 2px solid var(--dark-purple);
            font-weight: 700;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .pagination-bar a:hover {
            background-color: var(--cream-bg);
            transform: translateY(-2px);
            box-shadow: 0 4px 0 var(--dark-purple);
        }

        .pagination-bar a:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .pagination-bar a.active {
            background-color: var(--mint-bg);
            pointer-events: none;
        }

        .pagination-bar span.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f2f2f2;
            border-color: #ccc;
            color: #888;
        }

        /* ================= MODAL BACKDROP ================= */

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(58, 48, 99, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .modal {
            width: 520px;
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleUp {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-cancel {
            background: #E0E0E0;
            color: #555;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #D5D5D5;
        }

        .btn-save {
            background: var(--pink-card);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 900;
            font-family: 'Quicksand', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-save:hover {
            opacity: 0.9;
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
            <h1>KELOLA ULASAN PELANGGAN</h1>
        </div>

        <!-- Success or Error Alerts -->
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert-success" style="background-color: #D4EDDA; color: #155724; border-left: 5px solid #28A745; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; font-weight: 700; font-size: 14px;">
                🎉 <?= $_SESSION['success_msg'] ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert-error" style="background-color: #F8D7DA; color: #721C24; border-left: 5px solid #DC3545; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; font-weight: 700; font-size: 14px;">
                ⚠️ <?= $_SESSION['error_msg'] ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <!-- TOP CARDS / METRICS -->

        <div class="top-cards-grid">

            <div class="stat-card">
                <div class="color-block orange"></div>
                <div class="stat-info">
                    <span>Total Ulasan</span>
                    <h2><?= number_format($total_ulasan, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block pink"></div>
                <div class="stat-info">
                    <span>Rerata Rating</span>
                    <h2>⭐ <?= $avg_rating ?> / 5.0</h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block green"></div>
                <div class="stat-info">
                    <span>Sudah Dibalas</span>
                    <h2><?= number_format($sudah_dibalas, 0, ',', '.') ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="color-block blue"></div>
                <div class="stat-info">
                    <span>Belum Dibalas</span>
                    <h2><?= number_format($belum_dibalas, 0, ',', '.') ?></h2>
                </div>
            </div>

        </div>

        <!-- TABLE BOX -->

        <div class="table-box">

            <h3>DAFTAR ULASAN PRODUK</h3>

            <!-- FILTER BAR -->
            <form method="GET" class="filter-form-bar">

                <input
                    type="text"
                    name="search"
                    class="input-search"
                    placeholder="Cari Pelanggan, Produk, Ulasan..."
                    value="<?= htmlspecialchars($search) ?>">

                <select name="rating" class="select-status">
                    <option value="Semua" <?= $rating_filter == 'Semua' ? 'selected' : '' ?>>Semua Rating</option>
                    <option value="5" <?= $rating_filter == '5' ? 'selected' : '' ?>>5 ⭐ (Sangat Baik)</option>
                    <option value="4" <?= $rating_filter == '4' ? 'selected' : '' ?>>4 ⭐ (Baik)</option>
                    <option value="3" <?= $rating_filter == '3' ? 'selected' : '' ?>>3 ⭐ (Cukup)</option>
                    <option value="2" <?= $rating_filter == '2' ? 'selected' : '' ?>>2 ⭐ (Buruk)</option>
                    <option value="1" <?= $rating_filter == '1' ? 'selected' : '' ?>>1 ⭐ (Sangat Buruk)</option>
                </select>

                <select name="status_balasan" class="select-status">
                    <option value="Semua" <?= $status_balasan_filter == 'Semua' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="belum" <?= $status_balasan_filter == 'belum' ? 'selected' : '' ?>>Belum Dibalas</option>
                    <option value="sudah" <?= $status_balasan_filter == 'sudah' ? 'selected' : '' ?>>Sudah Dibalas</option>
                </select>

                <button type="submit" class="btn-filter">
                    Terapkan Filter
                </button>

                <?php if (!empty($search) || $rating_filter !== 'Semua' || $status_balasan_filter !== 'Semua'): ?>
                    <a href="kelola_ulasan.php" style="font-size: 13px; font-weight:700; color:var(--pink-card); text-decoration:none;">Reset Filter</a>
                <?php endif; ?>

            </form>

            <!-- SQUASHY TABLE -->
            <table class="squashy-table">

                <thead>
                    <tr>
                        <th>Ulasan / Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Rating / Bintang</th>
                        <th>Komentar / Ulasan</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    if (mysqli_num_rows($result_reviews) > 0) {

                        while ($row = mysqli_fetch_assoc($result_reviews)) {
                            $display_rev_id = "REV-" . str_pad($row['id_review'], 5, "0", STR_PAD_LEFT);
                            $display_order_id = "PLG-" . str_pad($row['id_order'], 5, "0", STR_PAD_LEFT);
                            
                            $rating_stars = str_repeat('⭐', intval($row['rating']));
                            $has_reply = (!empty($row['balasan_admin']));
                            
                            $photo_src = !empty($row['foto_produk']) ? '../' . htmlspecialchars($row['foto_produk']) : '../assets/images/ada.png';
                    ?>

                            <tr>
                                <td>
                                    <div style="font-weight:900; color:var(--dark-purple);"><?= $display_rev_id ?></div>
                                    <div style="font-size:10px; color:#888; margin-top:2px;">
                                        Order: <?= $display_order_id ?>
                                    </div>
                                    <div style="font-size:10px; color:#888; margin-top:2px;">
                                        <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="reviewer-meta">
                                        <h4><?= htmlspecialchars(strtoupper($row['nama_lengkap'])) ?></h4>
                                        <span>User ID: #<?= $row['id_user'] ?></span>
                                    </div>
                                </td>

                                <td>
                                    <div class="product-meta">
                                        <img src="<?= $photo_src ?>" alt="Foto Produk">
                                        <div style="font-weight:900; font-size:13px; text-transform:uppercase;">
                                            <?= htmlspecialchars($row['nama_produk']) ?>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="stars-display"><?= $rating_stars ?></div>
                                    <div style="font-size:11px; font-weight:700; color:#555; margin-top:4px;">
                                        (<?= number_format($row['rating'], 1) ?> / 5.0)
                                    </div>
                                </td>

                                <td>
                                    <div class="review-text-block">
                                        <?= nl2br(htmlspecialchars($row['komentar'] ?: '-')) ?>
                                        
                                        <?php if (!empty($row['foto_review'])): ?>
                                            <div>
                                                <img 
                                                    src="../<?= htmlspecialchars($row['foto_review']) ?>" 
                                                    class="review-photo-preview" 
                                                    alt="Foto Review" 
                                                    onclick="openImageModal('../<?= htmlspecialchars($row['foto_review']) ?>')"
                                                    title="Klik untuk memperbesar">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($has_reply): ?>
                                        <div class="admin-reply-box" id="reply-container-<?= $row['id_review'] ?>">
                                            <strong>Balasan Admin 🐰:</strong>
                                            <span id="reply-text-<?= $row['id_review'] ?>"><?= nl2br(htmlspecialchars($row['balasan_admin'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($has_reply): ?>
                                        <span class="badge-status replied">Dibalas</span>
                                    <?php else: ?>
                                        <span class="badge-status unreplied">Belum Dibalas</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="actions-cell" style="justify-content:center;">
                                        <!-- Reply/Edit Button -->
                                        <button 
                                            class="btn-action bg-reply" 
                                            title="Balas / Edit Balasan" 
                                            onclick="openReplyModal(<?= $row['id_review'] ?>, '<?= htmlspecialchars(rawurlencode($row['komentar'] ?? '')) ?>', '<?= htmlspecialchars(rawurlencode($row['balasan_admin'] ?? '')) ?>')">
                                            💬
                                        </button>

                                        <!-- Delete Button -->
                                        <a 
                                            href="javascript:void(0)" 
                                            class="btn-action bg-delete" 
                                            title="Hapus Ulasan"
                                            onclick="confirmDelete(<?= $row['id_review'] ?>, '<?= $display_rev_id ?>')">
                                            🗑️
                                        </a>
                                    </div>
                                </td>

                            </tr>

                        <?php }
                    } else { ?>

                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px; color:#888; font-weight:700;">
                                Tidak ada data ulasan pembeli yang ditemukan.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <!-- PAGINATION BAR -->
            <?php if ($total_pages > 1): 
                $search_query_param = ($search !== '') ? '&search=' . urlencode($search) : '';
                $rating_query_param = ($rating_filter !== 'Semua') ? '&rating=' . urlencode($rating_filter) : '';
                $status_query_param = ($status_balasan_filter !== 'Semua') ? '&status_balasan=' . urlencode($status_balasan_filter) : '';
                $all_params = $search_query_param . $rating_query_param . $status_query_param;
            ?>
                <div class="pagination-bar">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $all_params ?>">← Sebelumnya</a>
                    <?php else: ?>
                        <span class="disabled">← Sebelumnya</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <a href="#" class="active"><?= $i ?></a>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= $all_params ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $all_params ?>">Selanjutnya →</a>
                    <?php else: ?>
                        <span class="disabled">Selanjutnya →</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <!-- ================= MODALS ================= -->

    <!-- Reply / Edit Reply Modal -->
    <div class="modal-backdrop" id="replyModalBackdrop">
        <div class="modal" role="dialog" aria-modal="true" style="max-width: 520px;">
            <h3 id="replyModalTitle" style="font-family: 'Nunito', sans-serif; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;">Tanggapi Ulasan</h3>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="id_review" id="replyIdReview" value="">
                
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="display:block; font-weight:900; margin-bottom:6px; font-size:12px; color:#888; text-transform:uppercase;">Ulasan Pembeli:</label>
                    <div id="replyUserReviewText" style="background:#FFF9EF; padding:12px 16px; border-radius:15px; font-size:13px; font-style:italic; border-left:4px solid var(--orange-card); color:#555; line-height:1.4; max-height:100px; overflow-y:auto;">
                        "Komentar ulasan..."
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 22px;">
                    <label for="replyBalasanText" style="display:block; font-weight:900; margin-bottom:8px; font-size:14px; text-transform:uppercase;">Balasan Admin 🐰:</label>
                    <textarea 
                        name="balasan_admin" 
                        id="replyBalasanText" 
                        rows="5" 
                        style="width:100%; border:2px solid var(--soft-green); border-radius:15px; font-family:'Quicksand', sans-serif; font-weight:700; padding:12px 16px; outline:none; resize:none; font-size:14px; color:var(--dark-purple); transition: border-color 0.2s;" 
                        placeholder="Tulis balasan atau pesan terima kasih di sini..." 
                        required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeReplyModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Tanggapan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal-backdrop" id="imageModalBackdrop" onclick="closeImageModal()">
        <div class="modal" role="dialog" aria-modal="true" style="max-width: 600px; background: transparent; box-shadow: none; border-radius: 0; padding: 0; text-align: center;">
            <img id="modalPreviewImg" src="" alt="Pratinjau Foto" style="max-width: 100%; max-height: 75vh; border-radius: 16px; border: 4px solid white; box-shadow: 0 10px 30px rgba(0,0,0,0.3); object-fit: contain;">
            <p style="color: white; font-weight: 700; margin-top: 12px; font-size: 14px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">Klik di mana saja untuk menutup</p>
        </div>
    </div>

    <!-- Decorative flower image -->
    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

    <!-- ================= JAVASCRIPT ================= -->
    <script>
        // Modal Balas Ulasan
        function openReplyModal(id, komentarEscaped, balasanEscaped) {
            const komentar = decodeURIComponent(komentarEscaped);
            const balasan = decodeURIComponent(balasanEscaped);

            document.getElementById('replyIdReview').value = id;
            document.getElementById('replyUserReviewText').textContent = komentar ? `"${komentar}"` : "(Tidak ada komentar teks)";
            document.getElementById('replyBalasanText').value = balasan;

            const modalTitle = document.getElementById('replyModalTitle');
            if (balasan) {
                modalTitle.textContent = "Edit Tanggapan Ulasan";
            } else {
                modalTitle.textContent = "Tanggapi Ulasan";
            }

            document.getElementById('replyModalBackdrop').style.display = 'flex';
        }

        function closeReplyModal() {
            document.getElementById('replyModalBackdrop').style.display = 'none';
        }

        // Modal Pratinjau Foto
        function openImageModal(imgSrc) {
            document.getElementById('modalPreviewImg').src = imgSrc;
            document.getElementById('imageModalBackdrop').style.display = 'flex';
        }

        function closeImageModal() {
            document.getElementById('imageModalBackdrop').style.display = 'none';
        }

        // Konfirmasi Hapus
        function confirmDelete(id, code) {
            if (confirm(`Apakah Anda yakin ingin menghapus ulasan ${code} secara permanen?\nTindakan ini tidak dapat dibatalkan.`)) {
                // Construct redirects preserving filters
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('action', 'delete');
                urlParams.set('id', id);
                window.location.href = 'kelola_ulasan.php?' + urlParams.toString();
            }
        }

        // Close modal when clicking on backdrop (outside modal box)
        window.onclick = function(event) {
            const replyModal = document.getElementById('replyModalBackdrop');
            if (event.target == replyModal) {
                closeReplyModal();
            }
        }
    </script>

</body>

</html>
