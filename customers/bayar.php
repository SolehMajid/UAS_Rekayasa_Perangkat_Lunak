<?php
// customers/bayar.php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/midtrans.php';
require_once '../includes/auth.php';

checkLogin();

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$userId = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

$query = mysqli_query($conn, "SELECT * FROM `order` WHERE id_order = $orderId AND id_user = $userId");
$order = mysqli_fetch_assoc($query);

if (!$order) {
    header("Location: profil.php");
    exit;
}

// Fallback: Jika snap_token kosong, buat token baru dari Midtrans
if (empty($order['snap_token'])) {
    $userEmailQuery = mysqli_query($conn, "SELECT email FROM user WHERE id_user = $userId");
    $userRow = mysqli_fetch_assoc($userEmailQuery);
    $userEmail = $userRow['email'] ?? '';

    // Ambil item details dari order_detail
    $itemsForMidtrans = [];
    $detailsQuery = mysqli_query($conn, "SELECT id_produk, nama_produk, harga_saat_order, kuantitas FROM order_detail WHERE id_order = $orderId");
    if ($detailsQuery) {
        while ($item = mysqli_fetch_assoc($detailsQuery)) {
            $itemsForMidtrans[] = [
                'id' => $item['id_produk'],
                'price' => (int)$item['harga_saat_order'],
                'quantity' => (int)$item['kuantitas'],
                'name' => substr($item['nama_produk'], 0, 50)
            ];
        }
    }

    $customerDetails = [
        'nama' => $order['nama_pembeli'],
        'email' => $userEmail,
        'phone' => $order['nomer_hp']
    ];
    $snapToken = getMidtransSnapToken($orderId, $order['total_tagihan'], $customerDetails, $itemsForMidtrans);

    if ($snapToken) {
        $snapTokenEscaped = mysqli_real_escape_string($conn, $snapToken);
        mysqli_query($conn, "UPDATE `order` SET snap_token = '$snapTokenEscaped' WHERE id_order = $orderId");
        $order['snap_token'] = $snapToken;
    } else {
        // Jika gagal mendapat token dari Midtrans, redirect kembali ke profil
        header("Location: profil.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran - Squashy</title>
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
            background: #FFF9F3;
            color: #2B2B2B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .box {
            background: white;
            max-width: 500px;
            width: 100%;
            padding: 40px;
            border-radius: 28px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.06);
            text-align: center;
            border: 3px solid #FFF1E5;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        h1 {
            font-family: 'Nunito', sans-serif;
            color: #FF6FB7;
            margin-bottom: 16px;
            font-size: 28px;
        }
        p {
            color: #666;
            margin-bottom: 24px;
            font-size: 16px;
            line-height: 1.6;
        }
        .amount-card {
            background: #FFF5F7;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 32px;
            border: 1px dashed #FFD0E3;
        }
        .amount-label {
            font-size: 14px;
            color: #888;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .amount-value {
            font-size: 28px;
            font-weight: 800;
            color: #FF6FB7;
            font-family: 'Nunito', sans-serif;
        }
        .btn-pay {
            display: block;
            width: 100%;
            background: #FF6FB7;
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 18px;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
            box-shadow: 0 8px 20px rgba(255, 111, 183, 0.3);
        }
        .btn-pay:hover {
            transform: scale(1.02);
            background: #FF52A3;
        }
        .btn-pay:active {
            transform: scale(0.98);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6C63FF;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <?php require_once "../components/layout/header.php"; ?>

    <main>
        <div class="box">
            <span class="icon">🎁</span>
            <h1>Satu Langkah Lagi! 💖</h1>
            <p>Pesanan Anda telah dicatat. Silakan lakukan pembayaran agar pesanan Anda dapat segera kami proses.</p>
            
            <div class="amount-card">
                <div class="amount-label">Total Tagihan</div>
                <div class="amount-value">Rp <?= number_format($order['total_tagihan'], 0, ',', '.') ?></div>
            </div>
            
            <button id="pay-button" class="btn-pay">Bayar Sekarang 💳</button>
            <a href="profil.php" class="back-link">Bayar Nanti (Lihat Riwayat Pesanan)</a>
        </div>
    </main>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            // Panggil snap.pay dengan token dari database
            window.snap.pay('<?= $order['snap_token'] ?>', {
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
                }
            });
        });
    </script>
</body>
</html>
