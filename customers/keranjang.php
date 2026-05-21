<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../includes/auth.php';
// If not logged in and user tries to add a product, remember the pending item first
if (isset($_GET['action']) && $_GET['action'] === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['login'])) {
    $_SESSION['pending_cart_add'] = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
    header('Location: /squashy/customers/login.php?redirect=' . urlencode('/squashy/customers/keranjang.php?pending_add=1'));
    exit;
}

checkLogin();

$userId = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;
if ($userId <= 0) {
    header('Location: ../customers/login.php');
    exit;
}

$message = '';
$success = false;

if (isset($_GET['action']) && $_GET['action'] === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
    if ($productId > 0) {
        $productId = mysqli_real_escape_string($conn, $productId);
        $userId = mysqli_real_escape_string($conn, $userId);

        // Pastikan produk ada dan stok mencukupi
        $productQuery = mysqli_query($conn, "SELECT id_produk, nama_produk, harga, stok, foto FROM produk WHERE id_produk = $productId LIMIT 1");
        if ($productQuery && mysqli_num_rows($productQuery) > 0) {
            $product = mysqli_fetch_assoc($productQuery);
            if ($product['stok'] > 0) {
                $cartCheck = mysqli_query($conn, "SELECT id_cart, jumlah FROM cart WHERE id_user = $userId AND id_produk = $productId LIMIT 1");
                if ($cartCheck && mysqli_num_rows($cartCheck) > 0) {
                    $cartRow = mysqli_fetch_assoc($cartCheck);
                    $newAmount = intval($cartRow['jumlah']) + 1;
                    mysqli_query($conn, "UPDATE cart SET jumlah = $newAmount WHERE id_cart = " . intval($cartRow['id_cart']) . " LIMIT 1");
                } else {
                    mysqli_query($conn, "INSERT INTO cart (id_user, id_produk, jumlah) VALUES ($userId, $productId, 1)");
                }
                $message = 'Produk berhasil ditambahkan ke keranjang.';
                $success = true;
            } else {
                $message = 'Maaf, stok produk tidak mencukupi.';
            }
        } else {
            $message = 'Produk tidak ditemukan.';
        }
    } else {
        $message = 'Produk tidak valid.';
    }
}

if (isset($_GET['pending_add']) && isset($_SESSION['pending_cart_add'])) {
    $productId = intval($_SESSION['pending_cart_add']);
    unset($_SESSION['pending_cart_add']);
    if ($productId > 0) {
        $productQuery = mysqli_query($conn, "SELECT id_produk, nama_produk, harga, stok, foto FROM produk WHERE id_produk = $productId LIMIT 1");
        if ($productQuery && mysqli_num_rows($productQuery) > 0) {
            $product = mysqli_fetch_assoc($productQuery);
            if ($product['stok'] > 0) {
                $cartCheck = mysqli_query($conn, "SELECT id_cart, jumlah FROM cart WHERE id_user = $userId AND id_produk = $productId LIMIT 1");
                if ($cartCheck && mysqli_num_rows($cartCheck) > 0) {
                    $cartRow = mysqli_fetch_assoc($cartCheck);
                    $newAmount = intval($cartRow['jumlah']) + 1;
                    mysqli_query($conn, "UPDATE cart SET jumlah = $newAmount WHERE id_cart = " . intval($cartRow['id_cart']) . " LIMIT 1");
                } else {
                    mysqli_query($conn, "INSERT INTO cart (id_user, id_produk, jumlah) VALUES ($userId, $productId, 1)");
                }
                $message = 'Produk berhasil ditambahkan ke keranjang setelah login.';
                $success = true;
            } else {
                $message = 'Maaf, stok produk tidak mencukupi.';
            }
        } else {
            $message = 'Produk tidak ditemukan.';
        }
    }
}

$cartQuery = mysqli_query($conn, "SELECT c.id_cart, c.jumlah, p.id_produk, p.nama_produk, p.harga, p.stok, p.foto
    FROM cart c
    JOIN produk p ON c.id_produk = p.id_produk
    WHERE c.id_user = $userId");

$totalItems = 0;
$totalPrice = 0;
$cartItems = [];
if ($cartQuery) {
    while ($cartRow = mysqli_fetch_assoc($cartQuery)) {
        $cartItems[] = $cartRow;
        $totalItems += intval($cartRow['jumlah']);
        $totalPrice += intval($cartRow['jumlah']) * intval($cartRow['harga']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            color: #2B2B2B;
        }

        /* header is provided by components/layout/header_kategori.php */

        main {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 28px;
        }

        .page-header p {
            color: #555;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 20px;
            margin-bottom: 24px;
            background: <?= $success ? '#E6FFED' : '#FFF1F0' ?>;
            color: <?= $success ? '#1F7A3B' : '#B31B1B' ?>;
            border: 1px solid <?= $success ? '#9EE3B9' : '#F1A2A2' ?>;
        }

        .cart-grid {
            display: grid;
            grid-template-columns: 1.5fr 0.8fr;
            gap: 24px;
        }

        .cart-list {
            background: #fff;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .cart-item {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #F0F0F0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-thumb {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            overflow: hidden;
            background: #F4F4F4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .item-details p {
            color: #666;
            margin-bottom: 6px;
        }

        .item-actions {
            text-align: right;
        }

        .summary {
            background: #fff;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .summary h2 {
            margin-bottom: 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .summary-row.total {
            font-weight: 800;
            font-size: 18px;
        }

        .btn-primary {
            display: inline-block;
            background: #FF6FB7;
            color: #fff;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 20px;
            font-weight: 800;
        }

        .empty-state {
            background: #fff;
            border-radius: 28px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>

<body>
    <?= require_once __DIR__ . "/../components/layout/header.php" ?>

    <main>
        <div class="page-header">
            <div>
                <h1>Keranjang Belanja</h1>
                <p>Halo, <?= htmlspecialchars($_SESSION['nama'] ?? 'Bunda'); ?>. Pastikan semua produk sudah cocok sebelum checkout.</p>
            </div>
            <div>
                <strong><?= $totalItems; ?> item</strong>
            </div>
        </div>

        <?php if ($message) : ?>
            <div class="alert"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($cartItems)) : ?>
            <div class="cart-grid">
                <div class="cart-list">
                    <?php foreach ($cartItems as $item) : ?>
                        <div class="cart-item">
                            <div class="cart-thumb">
                                <?php if (!empty($item['foto'])) : ?>
                                    <img src="../<?= htmlspecialchars($item['foto']); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>">
                                <?php else : ?>
                                    <span>Gambar</span>
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['nama_produk']); ?></h3>
                                <p>Harga: Rp <?= number_format($item['harga'], 0, ',', '.'); ?> x <?= intval($item['jumlah']); ?></p>
                                <p>Subtotal: Rp <?= number_format(intval($item['jumlah']) * intval($item['harga']), 0, ',', '.'); ?></p>
                            </div>
                            <div class="item-actions">
                                <p>Stok: <?= intval($item['stok']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary">
                    <h2>Ringkasan Pesanan</h2>
                    <div class="summary-row"><span>Total Item</span><span><?= $totalItems; ?></span></div>
                    <div class="summary-row total"><span>Total Bayar</span><span>Rp <?= number_format($totalPrice, 0, ',', '.'); ?></span></div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                        <a href="<?= $base_url ?>customers/checkout.php" class="btn-primary">Lanjut Pembayaran</a>
                        <a href="<?= $base_url ?>customers/kategori.php" class="btn-primary" style="background:#6C63FF;">Tambah Produk Lagi</a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <h2>Keranjang Kosong</h2>
                <p>Tambahkan produk terlebih dahulu. Pastikan Anda sudah login agar bisa menambah ke keranjang.</p>
                <a href="<?= $base_url ?>customers/kategori.php" class="btn-primary">Lihat Kategori</a>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>