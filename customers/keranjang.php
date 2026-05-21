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

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $cartId = isset($_GET['id_cart']) ? intval($_GET['id_cart']) : 0;
    if ($cartId > 0) {
        $cartId = mysqli_real_escape_string($conn, $cartId);
        $userId = mysqli_real_escape_string($conn, $userId);
        
        $deleteQuery = mysqli_query($conn, "DELETE FROM cart WHERE id_cart = $cartId AND id_user = $userId LIMIT 1");
        if ($deleteQuery) {
            $message = 'Produk berhasil dihapus dari keranjang.';
            $success = true;
        } else {
            $message = 'Gagal menghapus produk dari keranjang.';
        }
    } else {
        $message = 'ID keranjang tidak valid.';
    }
}

if (isset($_GET['action']) && ($_GET['action'] === 'increase' || $_GET['action'] === 'decrease')) {
    $cartId = isset($_GET['id_cart']) ? intval($_GET['id_cart']) : 0;
    if ($cartId > 0) {
        $cartId = mysqli_real_escape_string($conn, $cartId);
        $userId = mysqli_real_escape_string($conn, $userId);
        
        $checkItemQuery = mysqli_query($conn, "
            SELECT c.jumlah, p.stok, p.nama_produk 
            FROM cart c 
            JOIN produk p ON c.id_produk = p.id_produk 
            WHERE c.id_cart = $cartId AND c.id_user = $userId 
            LIMIT 1
        ");
        if ($checkItemQuery && mysqli_num_rows($checkItemQuery) > 0) {
            $item = mysqli_fetch_assoc($checkItemQuery);
            $currentQty = intval($item['jumlah']);
            $maxStok = intval($item['stok']);
            $productName = $item['nama_produk'];
            
            if ($_GET['action'] === 'increase') {
                if ($currentQty < $maxStok) {
                    $newQty = $currentQty + 1;
                    mysqli_query($conn, "UPDATE cart SET jumlah = $newQty WHERE id_cart = $cartId LIMIT 1");
                    $success = true;
                    $message = "Jumlah produk " . htmlspecialchars($productName) . " berhasil ditambah.";
                } else {
                    $message = "Stok untuk produk " . htmlspecialchars($productName) . " tidak mencukupi.";
                }
            } else if ($_GET['action'] === 'decrease') {
                if ($currentQty > 1) {
                    $newQty = $currentQty - 1;
                    mysqli_query($conn, "UPDATE cart SET jumlah = $newQty WHERE id_cart = $cartId LIMIT 1");
                    $success = true;
                    $message = "Jumlah produk " . htmlspecialchars($productName) . " berhasil dikurangi.";
                } else {
                    $deleteQuery = mysqli_query($conn, "DELETE FROM cart WHERE id_cart = $cartId LIMIT 1");
                    if ($deleteQuery) {
                        $success = true;
                        $message = "Produk " . htmlspecialchars($productName) . " dihapus dari keranjang.";
                    }
                }
            }
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
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .btn-delete {
            display: inline-block;
            background: #FF4D4D;
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(255, 77, 77, 0.2);
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #E03333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 77, 77, 0.3);
        }

        .btn-qty {
            display: inline-flex;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #FFF0F8;
            color: #FF6FB7;
            text-decoration: none;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            transition: all 0.2s ease;
            border: 2px solid #FFB9D2;
            cursor: pointer;
            user-select: none;
        }

        .btn-qty:hover {
            background: #FF6FB7;
            color: #fff;
            transform: scale(1.1);
        }

        .qty-val {
            font-weight: 800;
            font-size: 16px;
            min-width: 24px;
            text-align: center;
            color: #3A3063;
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
                                <p style="color: #666; margin-bottom: 4px;">Harga: Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                                <div style="display: flex; align-items: center; gap: 10px; margin: 8px 0;">
                                    <span style="font-size: 14px; color: #555;">Kuantitas:</span>
                                    <a href="keranjang.php?action=decrease&id_cart=<?= $item['id_cart']; ?>" class="btn-qty">-</a>
                                    <span class="qty-val"><?= intval($item['jumlah']); ?></span>
                                    <a href="keranjang.php?action=increase&id_cart=<?= $item['id_cart']; ?>" class="btn-qty">+</a>
                                </div>
                                <p style="font-weight: 700; color: #FF6FB7; margin-top: 6px;">Subtotal: Rp <?= number_format(intval($item['jumlah']) * intval($item['harga']), 0, ',', '.'); ?></p>
                            </div>
                             <div class="item-actions" style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                                 <p style="color: #666; font-size: 14px;">Stok: <?= intval($item['stok']); ?></p>
                                 <a href="keranjang.php?action=delete&id_cart=<?= $item['id_cart']; ?>" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus <?= htmlspecialchars($item['nama_produk']); ?> dari keranjang?')" 
                                    class="btn-delete">
                                     🗑️ Hapus
                                 </a>
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