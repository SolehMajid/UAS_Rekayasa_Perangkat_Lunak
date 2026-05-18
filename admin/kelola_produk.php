<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// 2. Query untuk menghitung data 3 Kartu Statistik Atas
// Hitung Total Produk
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM `produk`");
$data_total  = mysqli_fetch_assoc($query_total);
$total_produk = $data_total['total'];

// Hitung Produk Tersedia (stok > 0)
$query_ready = mysqli_query($conn, "SELECT COUNT(*) as ready FROM `produk` WHERE `stok` > 0");
$data_ready  = mysqli_fetch_assoc($query_ready);
$produk_tersedia = $data_ready['ready'];

// Hitung Produk Habis (stok = 0)
$query_empty = mysqli_query($conn, "SELECT COUNT(*) AS total_empty FROM `produk` WHERE `stok` = 0");
$data_empty  = mysqli_fetch_assoc($query_empty);
$produk_habis = $data_empty['total_empty'];


// 3. Fitur Pencarian Dinamis
$search = "";
$where_clause = "";
if (isset($_GET['search']) && $_GET['search'] != '') {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause = " WHERE p.nama_produk LIKE '%$search%' OR p.id_produk LIKE '%$search%' ";
}

// 4. Query Mengambil Data Produk & Join dengan Tabel Kategori
$sql_produk = "SELECT p.*, k.nama_kategori 
               FROM `produk` p 
               LEFT JOIN `kategori` k ON p.id_kategori = k.id_kategori" . $where_clause . " 
               ORDER BY p.id_produk DESC";
$result_produk = mysqli_query($conn, $sql_produk);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Kelola Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --mint-bg: #8DE3C7;
            --cream-bg: #FFEAA7;
            --dark-purple: #3A3063;
            --orange-card: #F2994A;
            --green-card: #6EDB8F;
            --pink-card: #EB5757;
            --coral-header: #FFA07A;
            --soft-gray: #F9F9F9;
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

        /* Header */
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

        /* ── STATISTIK KELOLA PRODUK (3 KARTU) ── */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.02);
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
            background-color: var(--green-card);
        }

        .color-block.pink {
            background-color: var(--pink-card);
        }

        .stat-info span {
            font-size: 12px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
        }

        .stat-info h2 {
            font-size: 22px;
            font-weight: 900;
            margin-top: 2px;
        }

        /* ── DATA TABLE WORKSPACE ── */
        .table-container {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        /* Bar Atas Tabel: Pencarian & Tambah */
        .table-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 2px solid var(--dark-purple);
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            outline: none;
            font-size: 14px;
        }

        .search-box button {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-tambah {
            background-color: #C57E7E;
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 900;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 0 #A06262;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.1s;
        }

        .btn-tambah:active {
            transform: translateY(4px);
            box-shadow: none;
        }

        /* Desain Tabel Utama */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-bottom: 25px;
        }

        .main-table th {
            background-color: var(--coral-header);
            color: var(--dark-purple);
            font-weight: 900;
            font-size: 14px;
            padding: 12px 15px;
            text-transform: capitalize;
        }

        .main-table th:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .main-table th:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .main-table td {
            padding: 15px;
            font-size: 14px;
            font-weight: 700;
            border-bottom: 2px solid #EAEAEA;
            vertical-align: middle;
        }

        .img-container-td {
            width: 50px;
            height: 40px;
            background-color: #E0E0E0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-badge.tersedia {
            color: #27AE60;
        }

        .status-badge.habis {
            color: #EB5757;
        }

        /* Tombol Aksi */
        .action-group {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            border: none;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--dark-purple);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            text-decoration: none;
        }

        .btn-edit {
            background-color: #FFE599;
        }

        .btn-hapus {
            background-color: #F8CECC;
        }

        .btn-preview {
            background-color: #B4E5E2;
        }

        .action-btn span {
            font-size: 10px;
            transform: scale(0.85);
            margin-top: -2px;
        }

        .pagination-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            font-size: 13px;
            font-weight: 700;
        }

        .pagination-bar a {
            text-decoration: none;
            color: var(--dark-purple);
        }

        .decor-flower-bottom {
            position: fixed;
            bottom: 0;
            right: 0;
            height: 90px;
            pointer-events: none;
            z-index: 5;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo-box">
            <img src="../assets/images/logo.png" alt="Logo Squashy">
        </div>
        <div class="menu-list">
            <a href="admin_dashboard.php" class="menu-item">📈 Dashboard</a>
            <a href="kelola_produk.php" class="menu-item active">📦 Kelola Produk</a>
            <a href="kelola_pesanan.php" class="menu-item active">📋 Kelola Pesanan</a>
            <a href="#" class="menu-item">👥 Status Pesanan</a>
            <a href="?logout=true" class="menu-item">Logout</a>
        </div>
    </div>

    <div class="main-content">

        <div class="stats-row">
            <div class="stat-card">
                <div class="color-block orange"></div>
                <div class="stat-info">
                    <span>Total Produk:</span>
                    <h2><?= $total_produk; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="color-block green"></div>
                <div class="stat-info">
                    <span>Produk Tersedia:</span>
                    <h2><?= $produk_tersedia; ?></h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="color-block pink"></div>
                <div class="stat-info">
                    <span>Produk Habis:</span>
                    <h2><?= $produk_habis; ?></h2>
                </div>
            </div>
        </div>

        <div class="table-container">

            <div class="table-action-bar">
                <form method="GET" action="" class="search-box">
                    <button type="submit">🔍</button>
                    <input type="text" name="search" placeholder="Cari Nama / ID Produk..." value="<?= htmlspecialchars($search); ?>">
                </form>
                <a href="form_tambah_produk.php" class="btn-tambah">Tambah Produk Baru</a>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Periksa apakah ada data produk di dalam database
                    if (mysqli_num_rows($result_produk) > 0) {
                        while ($row = mysqli_fetch_assoc($result_produk)) {
                            // Cek status ketersediaan berdasarkan nilai kolom stok
                            if ($row['stok'] > 0) {
                                $status_badge = '<div class="status-badge tersedia">🟢 Tersedia</div>';
                            } else {
                                $status_badge = '<div class="status-badge habis">🔴 Habis</div>';
                            }
                    ?>
                            <tr>
                                <td>#<?= $row['id_produk']; ?></td>
                                <td>
                                    <div class="img-container-td">🧸</div>
                                </td>
                                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td><?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori'); ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= $row['stok']; ?></td>
                                <td><?= $status_badge; ?></td>
                                <td>
                                    <div class="action-group">
                                        <a href="form_edit_produk.php?id=<?= $row['id_produk']; ?>" class="action-btn btn-edit">📝<span>Edit</span></a>
                                        <a href="proses_hapus_produk.php?id=<?= $row['id_produk']; ?>" class="action-btn btn-hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')">🗑️<span>Hapus</span></a>
                                        <a href="../detail_produk.php?id=<?= $row['id_produk']; ?>" target="_blank" class="action-btn btn-preview">👁️<span>Preview</span></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #888; padding: 30px;">
                                Belum ada data produk di dalam database.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pagination-bar">
                <a href="#">← Previous</a>
                <a href="#" class="page-num active">1</a>
                <a href="#">Next →</a>
            </div>

        </div>
    </div>

    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

</body>

</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>