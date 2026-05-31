<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

$userId = intval($_SESSION['id_user'] ?? 0);

// Handle pembatalan pesanan oleh user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order']) && $userId > 0) {
    $orderIdToCancel = intval($_POST['id_order'] ?? 0);
    if ($orderIdToCancel > 0) {
        // Validasi kepemilikan dan status (harus pending)
        $checkQuery = mysqli_query($conn, "
            SELECT o.status_pesanan, COALESCE(p.status_pembayaran, 'pending') AS status_pembayaran
            FROM `order` o
            LEFT JOIN payment p ON o.id_order = p.id_order
            WHERE o.id_order = $orderIdToCancel AND o.id_user = $userId LIMIT 1
        ");
        
        if ($checkQuery && mysqli_num_rows($checkQuery) === 1) {
            $checkRow = mysqli_fetch_assoc($checkQuery);
            $statusPesanan = strtolower($checkRow['status_pesanan'] ?? 'pending');
            $statusPembayaran = strtolower($checkRow['status_pembayaran'] ?? 'pending');
            
            if ($statusPesanan === 'pending' && $statusPembayaran === 'pending') {
                mysqli_begin_transaction($conn);
                try {
                    // Update order
                    mysqli_query($conn, "UPDATE `order` SET status_pesanan = 'dibatalkan' WHERE id_order = $orderIdToCancel");
                    
                    // Update payment
                    mysqli_query($conn, "UPDATE payment SET status_pembayaran = 'dibatalkan' WHERE id_order = $orderIdToCancel");
                    
                    mysqli_commit($conn);
                    $_SESSION['cancel_success'] = "Pesanan #" . str_pad($orderIdToCancel, 5, '0', STR_PAD_LEFT) . " berhasil dibatalkan.";
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['cancel_error'] = "Gagal membatalkan pesanan: " . $e->getMessage();
                }
            } else {
                $_SESSION['cancel_error'] = "Pesanan tidak dapat dibatalkan karena sudah dibayar atau diproses.";
            }
        } else {
            $_SESSION['cancel_error'] = "Pesanan tidak ditemukan.";
        }
    }
    header("Location: profil.php");
    exit;
}
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

// Ambil semua ulasan yang sudah dibuat oleh user ini
$reviewedItems = [];
if ($userId > 0) {
    $reviewedQuery = mysqli_query($conn, "SELECT id_produk, id_order FROM review_produk WHERE id_user = $userId");
    if ($reviewedQuery) {
        while ($row = mysqli_fetch_assoc($reviewedQuery)) {
            $reviewedItems[$row['id_order']][$row['id_produk']] = true;
        }
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
            color: var(--dark, #3A3063);
            min-height: 100vh;
        }

        main {
            max-width: 1200px;
            margin: 24px auto 60px;
            padding: 0 20px;
        }

        /* ── Glassmorphism Card ── */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 28px;
            box-shadow: 0 10px 30px rgba(58, 48, 99, 0.06);
            padding: 28px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 15px 35px rgba(58, 48, 99, 0.1);
        }

        /* ── Hero Banner ── */
        .hero-banner {
            background: linear-gradient(135deg, var(--pink, #FF6FB7) 0%, var(--orange, #FF852D) 100%);
            border-radius: 32px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(255, 111, 183, 0.25);
            animation: fadeIn 0.6s ease;
        }

        .hero-banner-content {
            position: relative;
            z-index: 2;
        }

        .hero-banner h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-banner p {
            font-size: 1.05rem;
            opacity: 0.95;
            line-height: 1.6;
            max-width: 750px;
        }

        .hero-banner-decor {
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-size: 11rem;
            opacity: 0.12;
            transform: rotate(-15deg);
            pointer-events: none;
            user-select: none;
        }

        /* ── Grid Layout ── */
        .profile-grid {
            display: grid;
            grid-template-columns: 1.1fr 1.9fr;
            gap: 28px;
            align-items: start;
        }

        /* ── Profile Summary Card ── */
        .profile-summary {
            display: grid;
            gap: 24px;
        }

        .avatar-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 10px;
        }

        .avatar-ring {
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--pink, #FF6FB7), var(--yellow, #FFD93D), var(--blue, #4DC8F0), var(--green, #6EDB8F));
            animation: rotateRing 8s linear infinite;
        }

        @keyframes rotateRing {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .profile-avatar {
            position: relative;
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 3.5rem;
            box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 4px solid white;
            z-index: 2;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 1.8rem;
            color: var(--dark, #3A3063);
            margin-bottom: 6px;
        }

        .profile-info p {
            color: #7A5C7F;
            font-size: 0.95rem;
            margin-bottom: 16px;
        }

        /* ── Buttons ── */
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .btn-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 25px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.15);
            font-size: 0.92rem;
            cursor: pointer;
            border: none;
        }

        .btn-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        .btn-pill:active {
            transform: translateY(0);
        }

        .btn-pink {
            background: var(--pink, #FF6FB7);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 111, 183, 0.25);
        }

        .btn-pink:hover {
            background: #ff56a5;
        }

        .btn-outline-pink {
            background: transparent;
            border: 2px solid var(--pink, #FF6FB7);
            color: var(--pink, #FF6FB7);
        }

        .btn-outline-pink:hover {
            background: rgba(255, 111, 183, 0.08);
        }

        .btn-red {
            background: #FF5C5C;
            color: white;
            box-shadow: 0 4px 12px rgba(255, 92, 92, 0.25);
        }

        .btn-red:hover {
            background: #ff4242;
        }

        /* ── Detail List ── */
        .detail-list {
            display: grid;
            gap: 10px;
            margin-top: 5px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.5);
            padding: 12px 18px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #7A5C7F;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .detail-value {
            font-weight: 700;
            color: var(--dark, #3A3063);
            font-size: 0.92rem;
            max-width: 60%;
            text-align: right;
            word-break: break-all;
        }

        /* ── Statistics Grid ── */
        .stats-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--dark, #3A3063);
            margin: 15px 0 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-left: 5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 14px 8px;
            border-radius: 18px;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease;
        }

        .stat-item:hover {
            transform: scale(1.03);
        }

        .stat-item-blue {
            background: rgba(77, 200, 240, 0.1);
            color: #1b89ab;
            border-color: rgba(77, 200, 240, 0.2);
        }

        .stat-item-orange {
            background: rgba(255, 133, 45, 0.1);
            color: #cb5900;
            border-color: rgba(255, 133, 45, 0.2);
        }

        .stat-item-green {
            background: rgba(110, 219, 143, 0.12);
            color: #276d37;
            border-color: rgba(110, 219, 143, 0.2);
        }

        .stat-num {
            display: block;
            font-size: 1.5rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .stat-name {
            font-size: 0.72rem;
            font-weight: 700;
            display: block;
            line-height: 1.2;
        }

        /* ── Order History Panel ── */
        .order-history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 5px;
        }

        .order-history-header h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 1.5rem;
            color: var(--dark, #3A3063);
        }

        .order-history-header p {
            color: #7A5C7F;
            font-size: 0.92rem;
            margin-top: 3px;
        }

        /* ── Tabs Filter ── */
        .tabs-filter {
            display: flex;
            background: rgba(58, 48, 99, 0.05);
            padding: 5px;
            border-radius: 999px;
            margin: 20px 0;
            gap: 2px;
            width: fit-content;
            overflow-x: auto;
            max-width: 100%;
            scrollbar-width: none;
        }

        .tabs-filter::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--dark, #3A3063);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--pink, #FF6FB7);
        }

        .tab-btn.active {
            background: white;
            color: var(--pink, #FF6FB7);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* ── Order Card List ── */
        .orders-container {
            max-height: 650px;
            overflow-y: auto;
            padding-right: 10px;
            margin-right: -10px;
            scrollbar-width: thin;
            scrollbar-color: var(--pink, #FF6FB7) rgba(255, 111, 183, 0.05);
        }

        .orders-container::-webkit-scrollbar {
            width: 6px;
        }

        .orders-container::-webkit-scrollbar-track {
            background: rgba(255, 111, 183, 0.05);
            border-radius: 10px;
        }

        .orders-container::-webkit-scrollbar-thumb {
            background: var(--pink, #FF6FB7);
            border-radius: 10px;
        }

        /* ── Order Card ── */
        .order-card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            margin-bottom: 20px;
            border: 1px solid rgba(238, 238, 238, 0.7);
            box-shadow: 0 4px 15px rgba(58, 48, 99, 0.015);
            transition: all 0.3s ease;
            border-left: 6px solid var(--blue, #4DC8F0);
            animation: fadeIn 0.4s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(58, 48, 99, 0.04);
        }

        /* Border highlights based on status */
        .order-card.status-selesai {
            border-left-color: var(--green, #6EDB8F);
        }

        .order-card.status-dikirim {
            border-left-color: #9B5DE5; /* Purple for delivery */
        }

        .order-card.status-pending {
            border-left-color: var(--orange, #FF852D);
        }

        .order-card.status-dibayar,
        .order-card.status-diproses {
            border-left-color: var(--yellow, #FFD93D);
        }

        .order-card.status-dibatalkan {
            border-left-color: #A0A0A0;
        }

        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px dashed rgba(58, 48, 99, 0.08);
            padding-bottom: 12px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-id {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            color: var(--dark, #3A3063);
            font-size: 1.05rem;
        }

        .order-date {
            color: #7A5C7F;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .order-status-badge {
            font-size: 0.8rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-pending {
            background: rgba(255, 133, 45, 0.1);
            color: #d66400;
        }

        .badge-dibayar,
        .badge-diproses {
            background: rgba(255, 217, 61, 0.12);
            color: #ab8000;
        }

        .badge-dikirim {
            background: rgba(155, 93, 229, 0.1);
            color: #7e3bd4;
        }

        .badge-selesai {
            background: rgba(110, 219, 143, 0.12);
            color: #276d37;
        }

        .badge-dibatalkan {
            background: rgba(160, 160, 160, 0.1);
            color: #555555;
        }

        /* ── Order Item Row ── */
        .order-products {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
        }

        .order-product-row {
            display: grid;
            grid-template-columns: 68px 1fr auto;
            gap: 15px;
            align-items: center;
            background: #FAF9FC;
            padding: 12px 16px;
            border-radius: 16px;
            border: 1px solid rgba(255, 111, 183, 0.03);
            transition: all 0.2s ease;
        }

        .order-product-row:hover {
            background: #F6F4FA;
        }

        .order-product-img {
            width: 68px;
            height: 68px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            background: #f5f5f5;
        }

        .order-product-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .order-product-name {
            font-weight: 700;
            color: var(--dark, #3A3063);
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .order-product-price {
            color: #7A5C7F;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .order-product-subtotal {
            color: var(--pink, #FF6FB7);
            font-weight: 700;
            font-size: 0.88rem;
        }

        .order-product-action {
            display: flex;
            align-items: center;
        }

        .btn-ulas {
            background-color: var(--pink, #FF6FB7);
            color: white;
            padding: 8px 16px;
            border-radius: 18px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(255, 111, 183, 0.15);
            white-space: nowrap;
        }

        .btn-ulas:hover {
            background-color: #ff509e;
            transform: scale(1.03) translateY(-1px);
            box-shadow: 0 6px 12px rgba(255, 111, 183, 0.25);
        }

        .badge-reviewed {
            background-color: rgba(110, 219, 143, 0.12);
            color: #276d37;
            padding: 6px 12px;
            border-radius: 18px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-block;
            white-space: nowrap;
            border: 1px solid rgba(110, 219, 143, 0.2);
        }

        /* ── Order Card Footer ── */
        .order-card-footer {
            border-top: 1.5px dashed rgba(58, 48, 99, 0.08);
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-payment-meta {
            font-size: 0.85rem;
            color: #6d5e75;
            display: grid;
            gap: 4px;
        }

        .order-payment-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .order-total-info {
            text-align: right;
        }

        .order-total-label {
            font-size: 0.82rem;
            color: #7A5C7F;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }

        .order-total-amount {
            font-size: 1.25rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            color: var(--orange, #FF852D);
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 26px;
            border: 2px dashed rgba(255, 111, 183, 0.25);
            animation: fadeIn 0.5s ease;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 15px;
            display: inline-block;
            animation: floatEmpty 3s ease-in-out infinite;
        }

        @keyframes floatEmpty {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        .empty-title {
            font-family: 'Nunito', sans-serif;
            font-size: 1.35rem;
            color: var(--dark, #3A3063);
            margin-bottom: 8px;
            font-weight: 800;
        }

        .empty-desc {
            color: #7A5C7F;
            font-size: 0.95rem;
            margin-bottom: 22px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
        }

        /* ── Animations ── */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Responsive Styling ── */
        @media (max-width: 992px) {
            .profile-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }
        }

        @media (max-width: 576px) {
            .hero-banner {
                padding: 25px;
            }

            .hero-banner h1 {
                font-size: 1.8rem;
            }

            .profile-grid {
                gap: 20px;
            }

            .order-product-row {
                grid-template-columns: 60px 1fr;
                gap: 12px;
                position: relative;
            }

            .order-product-action {
                grid-column: 1 / -1;
                justify-content: flex-start;
                margin-top: 8px;
                padding-top: 8px;
                border-top: 1px dashed rgba(58, 48, 99, 0.05);
            }

            .order-card-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .order-total-info {
                text-align: left;
                margin-bottom: 10px;
            }
            
            .order-card-footer .btn-pill {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../components/layout/header.php'; ?>

    <main>
        <!-- Banner Hero -->
        <section class="hero-banner">
            <div class="hero-banner-content">
                <span class="btn-pill btn-pink" style="padding: 6px 14px; font-size: 0.8rem; pointer-events: none; margin-bottom: 15px; box-shadow: none;">✨ Member Squashy</span>
                <h1>Halo, <?= htmlspecialchars($user['nama_lengkap']); ?>!</h1>
                <p>Senang melihatmu kembali! Di halaman profil ini, kamu bisa melihat info detail akunmu, ringkasan belanjaanmu, dan status pesanan dengan desain yang ceria.</p>
            </div>
            <div class="hero-banner-decor">🐰</div>
        </section>

        <!-- Pesan Sukses Ulasan -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'ulasan') : ?>
            <div style="background-color: rgba(110, 219, 143, 0.15); border: 2px solid rgba(110, 219, 143, 0.3); color: #276d37; padding: 15px 22px; border-radius: 22px; margin-bottom: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.4s ease;">
                🎉 Ulasan berhasil dikirim! Terima kasih atas feedback berhargamu untuk Squashy!
            </div>
        <?php endif; ?>

        <!-- Pesan Pembatalan Pesanan -->
        <?php if (isset($_SESSION['cancel_success'])) : ?>
            <div style="background-color: rgba(110, 219, 143, 0.15); border: 2px solid rgba(110, 219, 143, 0.3); color: #276d37; padding: 15px 22px; border-radius: 22px; margin-bottom: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.4s ease;">
                🎉 <?= htmlspecialchars($_SESSION['cancel_success']); ?>
            </div>
            <?php unset($_SESSION['cancel_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['cancel_error'])) : ?>
            <div style="background-color: rgba(255, 92, 92, 0.15); border: 2px solid rgba(255, 92, 92, 0.3); color: #C62828; padding: 15px 22px; border-radius: 22px; margin-bottom: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.4s ease;">
                ⚠️ <?= htmlspecialchars($_SESSION['cancel_error']); ?>
            </div>
            <?php unset($_SESSION['cancel_error']); ?>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Kiri: Detail Pengguna & Statistik -->
            <div class="profile-summary">
                <div class="glass-card">
                    <div class="avatar-wrapper">
                        <div class="avatar-ring"></div>
                        <div class="profile-avatar">🐰</div>
                    </div>
                    <div class="profile-info">
                        <h2><?= htmlspecialchars($user['nama_lengkap']); ?></h2>
                        <p><?= htmlspecialchars($user['email']); ?></p>
                        <div class="btn-group">
                            <a href="<?= $base_url ?>customers/logout.php" class="btn-pill btn-red" onclick="return confirm('Yakin ingin logout dari Squashy?');">🚪 Keluar</a>
                            <a href="<?= $base_url ?>customers/kategori.php" class="btn-pill btn-pink">🛍️ Belanja</a>
                        </div>
                    </div>
                </div>

                <div class="glass-card">
                    <h3 class="stats-title" style="margin-top:0;">🔑 Detail Akun</h3>
                    <div class="detail-list">
                        <div class="detail-row">
                            <span class="detail-label">👤 Nama Lengkap</span>
                            <strong class="detail-value"><?= htmlspecialchars($user['nama_lengkap']); ?></strong>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">✉️ Email Aktif</span>
                            <strong class="detail-value"><?= htmlspecialchars($user['email']); ?></strong>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">⭐ Level Member</span>
                            <strong class="detail-value" style="color: var(--pink, #FF6FB7);">Squashy Lover 💖</strong>
                        </div>
                    </div>

                    <h3 class="stats-title">📊 Statistik Pesanan</h3>
                    <div class="stats-grid">
                        <div class="stat-item stat-item-blue">
                            <strong class="stat-num"><?= $totalOrders; ?></strong>
                            <span class="stat-name">Total Pesan</span>
                        </div>
                        <div class="stat-item stat-item-orange">
                            <strong class="stat-num"><?= $pendingOrders; ?></strong>
                            <span class="stat-name">Diproses</span>
                        </div>
                        <div class="stat-item stat-item-green">
                            <strong class="stat-num"><?= $completedOrders; ?></strong>
                            <span class="stat-name">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Riwayat Pesanan -->
            <div class="glass-card">
                <div class="order-history-header">
                    <div>
                        <h3>📦 Riwayat Pesanan</h3>
                        <p>Pantau semua daftar transaksi dan status pengiriman barangmu di sini.</p>
                    </div>
                    <a href="<?= $base_url ?>customers/keranjang.php" class="btn-pill btn-outline-pink" style="padding: 10px 18px;">🛒 Keranjang Saya</a>
                </div>

                <?php if ($totalOrders === 0) : ?>
                    <div class="empty-state" style="margin-top: 20px;">
                        <span class="empty-icon">🛍️</span>
                        <h4 class="empty-title">Belum ada pesanan</h4>
                        <p class="empty-desc">Wah, sepertinya kamu belum pernah memesan produk di Squashy. Yuk, mulai cari produk favoritmu sekarang juga!</p>
                        <a href="<?= $base_url ?>customers/kategori.php" class="btn-pill btn-pink">Cari Produk Lucu</a>
                    </div>
                <?php else : ?>
                    <!-- Tab Filter interaktif -->
                    <div class="tabs-filter">
                        <button class="tab-btn active" data-filter="all">✨ Semua</button>
                        <button class="tab-btn" data-filter="proses">⏳ Diproses</button>
                        <button class="tab-btn" data-filter="selesai">🟢 Selesai</button>
                        <button class="tab-btn" data-filter="dibatalkan">❌ Dibatalkan</button>
                    </div>

                    <!-- Tempat Filter Kosong -->
                    <div class="no-orders-filtered" style="display: none; padding: 40px 20px; text-align: center; background: rgba(255, 255, 255, 0.4); border-radius: 20px; border: 2px dashed rgba(58,48,99,0.1); color: #7A5C7F; margin-bottom: 20px;">
                        <span style="font-size: 3rem; display: block; margin-bottom: 12px; animation: floatEmpty 3s ease-in-out infinite;">🔍</span>
                        <strong style="font-family: 'Nunito', sans-serif; font-size: 1.15rem; color: var(--dark, #3A3063);">Tidak Ada Pesanan</strong>
                        <p style="font-size: 0.9rem; margin-top: 5px;">Belum ada riwayat transaksi dengan status ini.</p>
                    </div>

                    <div class="orders-container">
                        <?php 
                        foreach ($orders as $order) :
                            $orderIdValue = intval($order['id_order']);
                            $orderDetailsQuery = mysqli_query($conn, "SELECT id_produk, nama_produk, foto_produk, harga_saat_order, kuantitas, subtotal FROM order_detail WHERE id_order = $orderIdValue ORDER BY id_detail ASC LIMIT 3");
                            $orderItems = [];
                            if ($orderDetailsQuery) {
                                while ($detail = mysqli_fetch_assoc($orderDetailsQuery)) {
                                    $orderItems[] = $detail;
                                }
                            }
                            $statusText = orderStatusLabel($order['status_pesanan']);
                        ?>
                            <article class="order-card status-<?= strtolower($order['status_pesanan']); ?>" data-status="<?= strtolower($order['status_pesanan']); ?>">
                                <div class="order-card-header">
                                    <div>
                                        <span class="order-id">Pesanan #<?= str_pad($orderIdValue, 5, '0', STR_PAD_LEFT); ?></span>
                                        <span class="order-date" style="margin-left: 10px;">📅 <?= date('d M Y', strtotime($order['tanggal_pesanan'])); ?></span>
                                    </div>
                                    <div>
                                        <span class="order-status-badge badge-<?= strtolower($order['status_pesanan']); ?>">
                                            <?php 
                                            $statusIcon = '📦';
                                            $statusLower = strtolower($order['status_pesanan']);
                                            if ($statusLower === 'pending') $statusIcon = '⏳';
                                            else if ($statusLower === 'dibayar' || $statusLower === 'diproses') $statusIcon = '⚙️';
                                            else if ($statusLower === 'dikirim') $statusIcon = '🚚';
                                            else if ($statusLower === 'selesai') $statusIcon = '🟢';
                                            else if ($statusLower === 'dibatalkan') $statusIcon = '❌';
                                            echo $statusIcon . ' ' . htmlspecialchars($statusText);
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($orderItems)) : ?>
                                    <div class="order-products">
                                        <?php foreach ($orderItems as $item) : ?>
                                            <?php 
                                            $reviewed = isset($reviewedItems[$orderIdValue][$item['id_produk']]);
                                            ?>
                                            <div class="order-product-row">
                                                <img class="order-product-img" src="../<?= htmlspecialchars($item['foto_produk']); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>">
                                                <div class="order-product-info">
                                                    <span class="order-product-name"><?= htmlspecialchars($item['nama_produk']); ?></span>
                                                    <span class="order-product-price"><?= intval($item['kuantitas']); ?> x <?= formatRupiah($item['harga_saat_order']); ?></span>
                                                    <span class="order-product-subtotal">Subtotal: <?= formatRupiah($item['subtotal']); ?></span>
                                                </div>
                                                <div class="order-product-action">
                                                    <?php if (strtolower($order['status_pesanan']) === 'selesai') : ?>
                                                        <?php if ($reviewed) : ?>
                                                            <span class="badge-reviewed">✓ Sudah Diulas</span>
                                                        <?php else : ?>
                                                            <a href="tambah_ulasan.php?id_produk=<?= $item['id_produk'] ?>&id_order=<?= $orderIdValue ?>" class="btn-ulas">✍ Beri Ulasan</a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="order-card-footer">
                                    <div class="order-payment-meta">
                                        <span>💳 Metode: <strong><?= htmlspecialchars($order['metode_pembayaran']); ?></strong></span>
                                        <span>🪙 Status Bayar: <strong><?= htmlspecialchars(ucfirst($order['status_pembayaran'])); ?></strong></span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                        <div class="order-total-info">
                                            <span class="order-total-label">Total Tagihan</span>
                                            <span class="order-total-amount"><?= formatRupiah($order['total_tagihan']); ?></span>
                                        </div>
                                        <?php if (strtolower($order['status_pesanan']) === 'pending' && strtolower($order['status_pembayaran']) === 'pending') : ?>
                                            <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');" style="display: inline-block;">
                                                <input type="hidden" name="id_order" value="<?= $orderIdValue; ?>">
                                                <button type="submit" name="cancel_order" class="btn-pill btn-red" style="padding: 8px 16px; font-size: 0.85rem;">❌ Batalkan Pesanan</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="<?= $base_url ?>customers/checkout.php" class="btn-pill btn-outline-pink" style="padding: 8px 16px; font-size: 0.85rem;">Beli Lagi</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- JavaScript Filter Tab Interaktif -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const orderCards = document.querySelectorAll('.order-card');
            const noOrdersDiv = document.querySelector('.no-orders-filtered');

            if (tabBtns.length > 0) {
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        // Hilangkan kelas aktif dari semua tab
                        tabBtns.forEach(b => b.classList.remove('active'));
                        // Tambahkan kelas aktif pada tab yang di-klik
                        this.classList.add('active');

                        const filterValue = this.getAttribute('data-filter');
                        let visibleCount = 0;

                        orderCards.forEach(card => {
                            const status = card.getAttribute('data-status');
                            
                            // Logika Pencocokan
                            let isMatch = false;
                            if (filterValue === 'all') {
                                isMatch = true;
                            } else if (filterValue === 'proses') {
                                // pending, dibayar, diproses, dikirim
                                isMatch = ['pending', 'dibayar', 'diproses', 'dikirim'].includes(status);
                            } else if (filterValue === 'selesai') {
                                isMatch = (status === 'selesai');
                            } else if (filterValue === 'dibatalkan') {
                                isMatch = (status === 'dibatalkan');
                            }

                            if (isMatch) {
                                card.style.display = 'block';
                                card.style.animation = 'fadeIn 0.4s ease forwards';
                                visibleCount++;
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        // Tampilkan pesan kosong jika tidak ada pesanan yang sesuai filter
                        if (visibleCount === 0) {
                            if (noOrdersDiv) {
                                noOrdersDiv.style.display = 'block';
                                noOrdersDiv.style.animation = 'fadeIn 0.4s ease forwards';
                            }
                        } else {
                            if (noOrdersDiv) {
                                noOrdersDiv.style.display = 'none';
                            }
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>