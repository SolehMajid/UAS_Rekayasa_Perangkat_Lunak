<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'status';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// Proses update status pesanan
if (isset($_GET['action'], $_GET['id'])) {
    $order_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($order_id > 0) {
        if ($action === 'confirm') {
            // Update status_pesanan di tabel order
            mysqli_query($conn, "UPDATE `order` SET status_pesanan = 'selesai' WHERE id_order = $order_id");
            // Update status_pembayaran di tabel payment
            mysqli_query($conn, "UPDATE payment SET status_pembayaran = 'selesai' WHERE id_order = $order_id");
        } elseif ($action === 'reset') {
            // Reset status_pesanan
            mysqli_query($conn, "UPDATE `order` SET status_pesanan = 'dikirim' WHERE id_order = $order_id");
            mysqli_query($conn, "UPDATE payment SET status_pembayaran = 'dikirim' WHERE id_order = $order_id");
        }
    }

    header('Location: kelola_status_pesanan.php');
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'Semua Status';

$where_clauses = ["o.status_pesanan = 'dikirim' OR o.status_pesanan = 'selesai'"];

if (!empty($search)) {
    $search_term = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(o.id_order LIKE '%$search_term%' OR o.nama_pembeli LIKE '%$search_term%')";
}

if ($status !== 'Semua Status') {
    $status_lower = strtolower($status);
    $where_clauses[] = "LOWER(o.status_pesanan) = '$status_lower'";
}

$where_sql = implode(' AND ', $where_clauses);

$query_orders = "
SELECT
    o.id_order,
    o.nama_pembeli,
    o.tanggal_pesanan,
    o.total_tagihan,
    o.status_pesanan
FROM `order` o
WHERE $where_sql
ORDER BY o.tanggal_pesanan DESC, o.id_order DESC
";

$result_orders = mysqli_query($conn, $query_orders);

// Ringkasan status pengiriman
$summary_sql = "
SELECT
    o.status_pesanan
FROM `order` o
WHERE o.status_pesanan IN ('dikirim', 'selesai')
";

$summary_result = mysqli_query($conn, $summary_sql);
$total_selesai = 0;
$sudah_sampai = 0;
$belum_sampai = 0;

if ($summary_result) {
    while ($row = mysqli_fetch_assoc($summary_result)) {
        $total_selesai++;
        if (strtolower($row['status_pesanan']) === 'selesai') {
            $sudah_sampai++;
        } else {
            $belum_sampai++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Status Pesanan</title>
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

        .stat-card.small {
            padding: 18px 22px;
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

        .chip-belum {
            background: #FF6FB7;
        }

        .chip-sudah {
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
        }

        .filter-form-bar button {
            border: none;
            background: var(--mint-bg);
            color: var(--dark-purple);
            border-radius: 16px;
            padding: 12px 20px;
            font-weight: 700;
            cursor: pointer;
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
                <h1>STATUS PESANAN</h1>
                <p style="color: var(--text-muted); margin-top: 8px;">Kelola konfirmasi pengiriman untuk pesanan yang telah selesai. Default status pengiriman adalah <strong>Belum Sampai</strong>.</p>
            </div>
            <a href="kelola_pesanan.php" class="btn-status-link">📋 Kembali ke Kelola Pesanan</a>
        </div>

        <div class="top-cards-grid">
            <div class="stat-card">
                <span>Total Pesanan Selesai</span>
                <h2><?= number_format($total_selesai, 0, ',', '.') ?></h2>
            </div>
            <div class="stat-card">
                <span>Sudah Sampai</span>
                <h2><?= number_format($sudah_sampai, 0, ',', '.') ?></h2>
            </div>
            <div class="stat-card">
                <span>Belum Sampai</span>
                <h2><?= number_format($belum_sampai, 0, ',', '.') ?></h2>
            </div>
        </div>

        <div class="info-note">
            Klik tombol "Konfirmasi Tiba" untuk menandai pesanan sebagai sudah sampai. Jika status berubah karena kesalahan, gunakan "Reset" untuk mengembalikan ke Belum Sampai.
        </div>

        <div class="table-box">
            <h3>DAFTAR PESANAN SELESAI</h3>

            <form method="GET" class="filter-form-bar">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari ID / Nama Pelanggan..."
                    value="<?= htmlspecialchars($search) ?>">

                <select name="status">
                    <option value="Semua Status" <?= $status == 'Semua Status' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="dikirim" <?= $status == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <option value="selesai" <?= $status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>

                <button type="submit">Terapkan Filter</button>
            </form>

            <table class="squashy-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Status Pesanan</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_orders && mysqli_num_rows($result_orders) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_orders)): ?>
                            <?php $display_id = "PLG-" . str_pad($row['id_order'], 5, "0", STR_PAD_LEFT); ?>
                            <tr>
                                <td><?= $display_id ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_pesanan'])) ?></td>
                                <td><?= htmlspecialchars(strtoupper($row['nama_pembeli'])) ?></td>
                                <td>Rp. <?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
                                <td>
                                    <?php $status_label = strtolower($row['status_pesanan']) === 'selesai' ? 'chip-sudah' : 'chip-belum'; ?>
                                    <span class="status-chip <?= $status_label ?>"><?= ucfirst(htmlspecialchars($row['status_pesanan'])) ?></span>
                                </td>
                                <td style="text-align:center;">
                                    <div class="actions-cell">
                                        <?php if (strtolower($row['status_pesanan']) !== 'selesai'): ?>
                                            <a href="kelola_status_pesanan.php?id=<?= $row['id_order'] ?>&action=confirm" class="btn-action bg-confirm">Konfirmasi Selesai</a>
                                        <?php else: ?>
                                            <a href="kelola_status_pesanan.php?id=<?= $row['id_order'] ?>&action=reset" class="btn-action bg-reset">Reset</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:32px; color:#888;">Tidak ada pesanan dikirim/selesai ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>