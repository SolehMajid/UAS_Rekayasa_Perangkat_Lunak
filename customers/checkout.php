<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/midtrans.php';
require_once '../includes/auth.php';

checkLogin();

$userId = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;
if ($userId <= 0) {
    header('Location: ../customers/login.php');
    exit;
}

$message = '';
$success = false;
$orderId = null;
$snapToken = '';

$cartItems = [];
$totalItems = 0;
$totalPrice = 0;

$cartQuery = mysqli_query($conn, "SELECT c.id_cart, c.jumlah, p.id_produk, p.nama_produk, p.harga, p.stok, p.foto
    FROM cart c
    JOIN produk p ON c.id_produk = p.id_produk
    WHERE c.id_user = $userId");

if ($cartQuery) {
    while ($row = mysqli_fetch_assoc($cartQuery)) {
        $cartItems[] = $row;
        $totalItems += intval($row['jumlah']);
        $totalPrice += intval($row['jumlah']) * intval($row['harga']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPembeli = trim($_POST['nama_pembeli'] ?? $_SESSION['nama'] ?? '');
    $nomerHp = trim($_POST['nomer_hp'] ?? '');
    $metodePembayaran = 'Midtrans';
    $maxPhoneLength = 20;

    if (empty($namaPembeli) || empty($metodePembayaran)) {
        $message = 'Nama pembeli dan metode pembayaran wajib diisi.';
    } elseif (empty($nomerHp)) {
        $message = 'Nomor HP wajib diisi.';
    } elseif (!preg_match('/^[0-9]+$/', $nomerHp)) {
        $message = 'Nomor HP harus berupa angka tanpa spasi atau tanda khusus.';
    } elseif (strlen($nomerHp) > $maxPhoneLength) {
        $message = 'Nomor HP tidak boleh lebih dari ' . $maxPhoneLength . ' digit.';
    } elseif (empty($cartItems)) {
        $message = 'Keranjang Anda kosong. Tambahkan produk terlebih dahulu.';
    } else {
        mysqli_begin_transaction($conn);

        $safeName = mysqli_real_escape_string($conn, $namaPembeli);
        $safePhone = mysqli_real_escape_string($conn, $nomerHp);
        $safeMethod = mysqli_real_escape_string($conn, $metodePembayaran);
        $userIdEscaped = mysqli_real_escape_string($conn, $userId);

        $insertOrderSql = "INSERT INTO `order` (id_user, nomer_hp, nama_pembeli, total_tagihan, status_pesanan)
            VALUES ($userIdEscaped, '$safePhone', '$safeName', $totalPrice, 'pending')";

        if (mysqli_query($conn, $insertOrderSql)) {
            $orderId = mysqli_insert_id($conn);
            if ($orderId <= 0) {
                $orderIdResult = mysqli_query($conn, "SELECT MAX(id_order) AS id_order FROM `order`");
                if ($orderIdResult && $orderRow = mysqli_fetch_assoc($orderIdResult)) {
                    $orderId = intval($orderRow['id_order']);
                }
            }

            if ($orderId > 0) {
                $paymentSql = "INSERT INTO payment (id_order, metode_pembayaran, status_pembayaran) VALUES ($orderId, '$safeMethod', 'pending')";
                $paymentOk = mysqli_query($conn, $paymentSql);

                $detailsOk = true;
                foreach ($cartItems as $item) {
                    $idProduk = intval($item['id_produk']);
                    $jumlah = intval($item['jumlah']);
                    $harga = intval($item['harga']);
                    $subtotal = $jumlah * $harga;
                    $namaProduk = mysqli_real_escape_string($conn, $item['nama_produk']);
                    $fotoProduk = mysqli_real_escape_string($conn, $item['foto']);

                    $detailSql = "INSERT INTO order_detail (id_order, id_produk, nama_produk, harga_saat_order, foto_produk, kuantitas, subtotal)
                        VALUES ($orderId, $idProduk, '$namaProduk', $harga, '$fotoProduk', $jumlah, $subtotal)";

                    if (!mysqli_query($conn, $detailSql)) {
                        $detailsOk = false;
                        break;
                    }
                }

                if ($paymentOk && $detailsOk) {
                    $clearCartSql = "DELETE FROM cart WHERE id_user = $userIdEscaped";
                    mysqli_query($conn, $clearCartSql);

                    // Ambil email user untuk dikirim ke Midtrans
                    $userEmailQuery = mysqli_query($conn, "SELECT email FROM user WHERE id_user = $userId");
                    $userRow = mysqli_fetch_assoc($userEmailQuery);
                    $userEmail = $userRow['email'] ?? '';

                    // Dapatkan detail item belanja untuk rincian Midtrans
                    $itemsForMidtrans = [];
                    foreach ($cartItems as $item) {
                        $itemsForMidtrans[] = [
                            'id' => $item['id_produk'],
                            'price' => (int)$item['harga'],
                            'quantity' => (int)$item['jumlah'],
                            'name' => substr($item['nama_produk'], 0, 50)
                        ];
                    }

                    // Meminta Token ke Midtrans
                    $customerDetails = [
                        'nama' => $namaPembeli,
                        'email' => $userEmail,
                        'phone' => $nomerHp
                    ];
                    $snapToken = getMidtransSnapToken($orderId, $totalPrice, $customerDetails, $itemsForMidtrans);

                    if ($snapToken) {
                        // Simpan token ke database order
                        mysqli_query($conn, "UPDATE `order` SET snap_token = '$snapToken' WHERE id_order = $orderId");
                        mysqli_commit($conn);
                        $success = true;
                    } else {
                        // Jika token gagal didapat, tetap commit order (sebagai pending) dan biarkan user membayar manual atau mencoba lagi nanti
                        mysqli_commit($conn);
                        $success = true;
                    }
                } else {
                    mysqli_rollback($conn);
                    $message = 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.';
                }
            } else {
                mysqli_rollback($conn);
                $message = 'Gagal mendapatkan nomor pesanan. Silakan coba lagi.';
            }
        } else {
            mysqli_rollback($conn);
            $message = 'Gagal membuat pesanan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <!-- Load Script Midtrans Snap.js (Sandbox) -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
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

        main {
            max-width: 1000px;
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

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.5fr 0.9fr;
            gap: 24px;
        }

        .card {
            background: #fff;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .field-group {
            display: grid;
            gap: 16px;
            margin-bottom: 24px;
        }

        .field-group label {
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        .field-group input,
        .field-group select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 16px;
            font-size: 15px;
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

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .summary-row.total {
            font-weight: 800;
            font-size: 18px;
        }

        .btn-primary,
        button[type="submit"] {
            display: inline-block;
            background: #FF6FB7;
            color: #fff;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 20px;
            font-weight: 800;
            border: none;
            cursor: pointer;
        }

        .btn-secondary {
            display: inline-block;
            background: #6C63FF;
            color: #fff;
            text-decoration: none;
            padding: 14px 24px;
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
                <h1>Pembayaran</h1>
                <p>Lengkapi data pembeli dan pilih metode pembayaran.</p>
            </div>
            <div>
                <strong><?= $totalItems; ?> item</strong>
            </div>
        </div>

        <?php if ($message) : ?>
            <div class="alert"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($success) : ?>
            <div class="alert">Pesanan Anda berhasil diproses. Nomor pesanan: <strong><?= intval($orderId); ?></strong>.</div>
            <div class="card">
                <h2>Terima kasih!</h2>
                <p>Pembayaran sedang menunggu konfirmasi. Silakan cek riwayat pesanan Anda.</p>
                <a href="<?= $base_url ?>customers/kategori.php" class="btn-secondary">Kembali ke Kategori</a>
            </div>
            
            <?php if (!empty($snapToken)) : ?>
                <script type="text/javascript">
                    document.addEventListener("DOMContentLoaded", function() {
                        window.snap.pay('<?= $snapToken ?>', {
                            onSuccess: function(result){
                                alert("Pembayaran sukses! Terima kasih.");
                                window.location.href = "profil.php";
                            },
                            onPending: function(result){
                                alert("Menunggu pembayaran Anda.");
                                window.location.href = "profil.php";
                            },
                            onError: function(result){
                                alert("Pembayaran gagal, silakan coba lagi.");
                                window.location.href = "profil.php";
                            },
                            onClose: function(){
                                alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                                window.location.href = "profil.php";
                            }
                        });
                    });
                </script>
            <?php endif; ?>
        <?php elseif (!empty($cartItems)) : ?>
            <div class="checkout-grid">
                <div class="card">
                    <h2>Detail Pembeli</h2>
                    <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="field-group">
                            <label for="nama_pembeli">Nama Pembeli</label>
                            <input type="text" id="nama_pembeli" name="nama_pembeli" value="<?= htmlspecialchars($_POST['nama_pembeli'] ?? $_SESSION['nama'] ?? ''); ?>" placeholder="Nama lengkap" required>
                        </div>
                        <div class="field-group">
                            <label for="nomer_hp">Nomor HP</label>
                            <input type="text" id="nomer_hp" name="nomer_hp" value="<?= htmlspecialchars($_POST['nomer_hp'] ?? ''); ?>" placeholder="08xxxxxxxxxx" maxlength="20" inputmode="numeric" pattern="[0-9]*" required>
                        </div>

                        <button type="submit">Proses Pembayaran</button>
                    </form>
                </div>
                <div class="card">
                    <h2>Ringkasan Pesanan</h2>
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
                                <p>Rp <?= number_format($item['harga'], 0, ',', '.'); ?> x <?= intval($item['jumlah']); ?></p>
                                <p>Subtotal: Rp <?= number_format(intval($item['jumlah']) * intval($item['harga']), 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-row"><span>Total Item</span><span><?= $totalItems; ?></span></div>
                    <div class="summary-row total"><span>Total Bayar</span><span>Rp <?= number_format($totalPrice, 0, ',', '.'); ?></span></div>
                </div>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <h2>Keranjang Kosong</h2>
                <p>Tambahkan produk terlebih dahulu. Pastikan Anda sudah login agar bisa melakukan pembelian.</p>
                <a href="<?= $base_url ?>customers/kategori.php" class="btn-primary">Lihat Kategori</a>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>