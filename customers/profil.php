<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

$userId = intval($_SESSION['id_user'] ?? 0);
$user = [
    'nama_lengkap' => $_SESSION['nama'] ?? 'Bunda',
    'email' => '-',
];

if ($userId > 0) {
    $userQuery = mysqli_query($conn, "SELECT nama_lengkap, email FROM user WHERE id_user = $userId LIMIT 1");
    if ($userQuery && mysqli_num_rows($userQuery) === 1) {
        $userRow = mysqli_fetch_assoc($userQuery);
        $user['nama_lengkap'] = $userRow['nama_lengkap'] ?: $user['nama_lengkap'];
        $user['email'] = $userRow['email'] ?: '-';
    }
}

$orderQuery = mysqli_query($conn, "SELECT o.id_order, o.tanggal_pesanan, o.total_tagihan, o.status_pesanan, COALESCE(p.metode_pembayaran, '-') AS metode_pembayaran, COALESCE(p.status_pembayaran, 'pending') AS status_pembayaran
    FROM `order` o
    LEFT JOIN payment p ON o.id_order = p.id_order
    WHERE o.id_user = $userId
    ORDER BY o.tanggal_pesanan DESC, o.id_order DESC");

$orders = [];
if ($orderQuery) {
    while ($row = mysqli_fetch_assoc($orderQuery)) {
        $orders[] = $row;
    }
}

$totalOrders = count($orders);
$pendingOrders = 0;
$completedOrders = 0;
foreach ($orders as $order) {
    $status = strtolower($order['status_pesanan']);
    if ($status === 'selesai' || $status === 'dikirim') {
        $completedOrders++;
    } else {
        $pendingOrders++;
    }
}

function formatRupiah($value)
{
    return 'Rp ' . number_format(intval($value), 0, ',', '.');
}

function orderStatusLabel($status)
{
    $lower = strtolower($status);
    if ($lower === 'selesai') {
        return 'Selesai';
    }
    if ($lower === 'dikirim') {
        return 'Dikirim';
    }
    return ucfirst($status);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Nunito:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(180deg, #FFE6F3 0%, #FFF5E8 100%);
            color: #333;
            min-height: 100vh;
        }

        main {
            max-width: 1120px;
            margin: 24px auto 40px;
            padding: 0 18px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            margin-bottom: 28px;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #FFD8E8;
            border-radius: 28px;
            box-shadow: 0 14px 40px rgba(255, 139, 194, 0.18);
            padding: 28px;
        }

        .hero-card h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .hero-card p {
            color: #7A5C7F;
            line-height: 1.75;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1.2fr 1.8fr;
            gap: 24px;
        }

        .profile-summary,
        .order-history {
            background: #ffffff;
            border-radius: 32px;
            border: 1px solid #ffe6f1;
            box-shadow: 0 18px 40px rgba(255, 155, 201, 0.18);
            padding: 28px;
        }

        .profile-card {
            display: grid;
            gap: 18px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            background: #FFF0F8;
            color: #d24c7d;
            font-weight: 700;
            font-size: 0.95rem;
            width: fit-content;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #FFB9E3, #FF85CD);
            color: white;
            font-size: 3rem;
            border-radius: 36px;
            box-shadow: 0 14px 24px rgba(255, 129, 214, 0.25);
            margin-bottom: 8px;
        }

        .profile-info h2 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .profile-info p {
            color: #6b5d75;
            font-size: 0.98rem;
            margin-bottom: 10px;
        }

        .profile-details {
            display: grid;
            gap: 12px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            background: #FFF7FB;
            border-radius: 18px;
            padding: 14px 18px;
            color: #5d4862;
            font-weight: 600;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 13px 22px;
            border-radius: 24px;
            border: none;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #ff82b8;
            color: #fff;
        }

        .btn-secondary {
            background: #ffe4f2;
            color: #c03e84;
        }

        .btn-logout {
            background: #ff5f7a;
            color: #fff;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .stat-card {
            background: #FFF1F8;
            border-radius: 22px;
            padding: 18px;
            text-align: center;
            border: 1px solid #ffd7ea;
        }

        .stat-card strong {
            display: block;
            font-size: 1.4rem;
            margin-bottom: 6px;
            color: #d3487e;
        }

        .stat-card span {
            color: #83526d;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .order-header p {
            color: #6b5d75;
        }

        .order-card {
            border-radius: 28px;
            border: 1px solid #ffd6e8;
            padding: 20px;
            margin-bottom: 18px;
            background: #fff;
        }

        .order-card h4 {
            margin-bottom: 10px;
            font-size: 1.12rem;
        }

        .order-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            color: #6d4b6b;
            font-size: 0.95rem;
        }

        .order-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .order-status {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-pending {
            background: #fff2f6;
            color: #d46c85;
        }

        .status-dikirim,
        .status-selesai {
            background: #e5f8f0;
            color: #2f7f5e;
        }

        .order-items {
            display: grid;
            gap: 12px;
        }

        .order-item {
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 14px;
            align-items: center;
            border-radius: 20px;
            padding: 14px;
            background: #fff5fb;
            border: 1px solid #ffdeec;
        }

        .order-item img {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            object-fit: cover;
            background: #f8e6f2;
        }

        .order-item-details {
            display: grid;
            gap: 4px;
        }

        .order-item-details strong {
            font-size: 0.98rem;
        }

        .order-item-details span {
            color: #7a5c7f;
            font-size: 0.92rem;
        }

        .no-orders {
            background: #fff6f9;
            border: 1px dashed #ffb6d2;
            border-radius: 26px;
            padding: 28px;
            text-align: center;
            color: #8f5f7f;
        }

        .no-orders h4 {
            margin-bottom: 12px;
            font-size: 1.25rem;
        }

        @media (max-width: 960px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .summary-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?= require_once __DIR__ . '/../components/layout/header.php' ?>

    <main>
        <section class="hero">
            <div class="hero-card">
                <span class="tag">✨ Profil Lucu</span>
                <h1>Halo, <?= htmlspecialchars($user['nama_lengkap']); ?>!</h1>
                <p>Ini adalah halaman profilmu. Kamu bisa melihat informasi akun, menonton status logout jika sudah login, dan memeriksa pesanan yang sudah dibeli dengan desain yang ceria.</p>
            </div>
        </section>

        <section class="profile-grid">
            <div class="profile-summary">
                <div class="profile-card">
                    <div class="profile-avatar">🐰</div>
                    <div class="profile-info">
                        <h2><?= htmlspecialchars($user['nama_lengkap']); ?></h2>
                        <p><?= htmlspecialchars($user['email']); ?></p>
                        <div class="btn-group">
                            <a href="<?= $base_url ?>customers/logout.php" class="btn btn-logout" onclick="return confirm('Yakin ingin logout?');">🚪 Logout</a>
                            <a href="<?= $base_url ?>customers/kategori.php" class="btn btn-secondary">Belanja Lagi</a>
                        </div>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="detail-item"><span>Nama Lengkap</span><strong><?= htmlspecialchars($user['nama_lengkap']); ?></strong></div>
                    <div class="detail-item"><span>Email</span><strong><?= htmlspecialchars($user['email']); ?></strong></div>
                    <div class="detail-item"><span>Total Pesanan</span><strong><?= $totalOrders; ?></strong></div>
                </div>

                <div class="summary-stats">
                    <div class="stat-card">
                        <strong><?= $totalOrders; ?></strong>
                        <span>Pesanan</span>
                    </div>
                    <div class="stat-card">
                        <strong><?= $pendingOrders; ?></strong>
                        <span>Menunggu / Diproses</span>
                    </div>
                    <div class="stat-card">
                        <strong><?= $completedOrders; ?></strong>
                        <span>Selesai / Dikirim</span>
                    </div>
                </div>
            </div>

            <div class="order-history">
                <div class="order-header">
                    <div>
                        <h3>Riwayat Pesanan</h3>
                        <p>Semua pembelianmu ditampilkan di sini. Ketuk 'Detail' untuk melihat produk yang dibeli.</p>
                    </div>
                    <div class="btn-group">
                        <a href="<?= $base_url ?>customers/keranjang.php" class="btn btn-primary">Lihat Keranjang</a>
                    </div>
                </div>

                <?php if ($totalOrders === 0) : ?>
                    <div class="no-orders">
                        <h4>Belum ada pesanan.</h4>
                        <p>Yuk mulai belanja! Semua pesanan yang kamu lakukan akan muncul di halaman ini setelah checkout.</p>
                        <a href="<?= $base_url ?>customers/kategori.php" class="btn btn-primary">Cari Produk Lucu</a>
                    </div>
                <?php else : ?>
                    <?php foreach ($orders as $order) :
                        $orderIdValue = intval($order['id_order']);
                        $orderDetailsQuery = mysqli_query($conn, "SELECT nama_produk, foto_produk, harga_saat_order, kuantitas, subtotal FROM order_detail WHERE id_order = $orderIdValue ORDER BY id_detail ASC LIMIT 3");
                        $orderItems = [];
                        if ($orderDetailsQuery) {
                            while ($detail = mysqli_fetch_assoc($orderDetailsQuery)) {
                                $orderItems[] = $detail;
                            }
                        }
                        $statusClass = 'status-pending';
                        $statusText = orderStatusLabel($order['status_pesanan']);
                        if (strtolower($order['status_pesanan']) === 'selesai' || strtolower($order['status_pesanan']) === 'dikirim') {
                            $statusClass = 'status-selesai';
                        }
                    ?>
                        <article class="order-card">
                            <div class="order-meta">
                                <span>Pesanan #<?= str_pad($orderIdValue, 5, '0', STR_PAD_LEFT); ?></span>
                                <span><?= date('d M Y', strtotime($order['tanggal_pesanan'])); ?></span>
                                <span class="order-status <?= $statusClass; ?>">📦 <?= htmlspecialchars($statusText); ?></span>
                            </div>
                            <h4>Total Pembayaran: <?= formatRupiah($order['total_tagihan']); ?></h4>
                            <div class="order-meta">
                                <span>Metode: <?= htmlspecialchars($order['metode_pembayaran']); ?></span>
                                <span>Status Pembayaran: <?= htmlspecialchars(ucfirst($order['status_pembayaran'])); ?></span>
                            </div>

                            <?php if (!empty($orderItems)) : ?>
                                <div class="order-items">
                                    <?php foreach ($orderItems as $item) : ?>
                                        <div class="order-item">
                                            <img src="../<?= htmlspecialchars($item['foto_produk']); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>">
                                            <div class="order-item-details">
                                                <strong><?= htmlspecialchars($item['nama_produk']); ?></strong>
                                                <span><?= intval($item['kuantitas']); ?> x <?= formatRupiah($item['harga_saat_order']); ?></span>
                                                <span>Subtotal: <?= formatRupiah($item['subtotal']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="btn-group" style="justify-content: flex-end; margin-top: 14px;">
                                <a href="<?= $base_url ?>customers/checkout.php" class="btn btn-secondary">Beli Lagi</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>