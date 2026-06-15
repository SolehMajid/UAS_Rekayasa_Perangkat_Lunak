<?php
// payment_notification.php
require_once 'config/database.php';
require_once 'config/midtrans.php';

// Terima input JSON dari Midtrans
$jsonRaw = file_get_contents('php://input');
$data = json_decode($jsonRaw, true);

if (!$data) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// 1. Ambil data penting dari payload Midtrans
$orderId = mysqli_real_escape_string($conn, $data['order_id']);
$statusCode = $data['status_code'];
$grossAmount = $data['gross_amount'];
$signatureKey = $data['signature_key'];

$serverKey = MIDTRANS_SERVER_KEY;
// Validasi signature key untuk keamanan (memastikan pengirim adalah Midtrans)
$hash = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

if ($hash !== $signatureKey) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

// 2. Baca Status Transaksi
$transactionStatus = $data['transaction_status'];
$paymentType = mysqli_real_escape_string($conn, $data['payment_type']);

$statusOrder = 'pending';
$statusBayar = 'pending';

if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
    // Pembayaran sukses lunas
    $statusOrder = 'dibayar';
    $statusBayar = 'lunas';
} elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
    // Pembayaran gagal, kadaluwarsa, atau dibatalkan
    $statusOrder = 'dibatalkan';
    $statusBayar = 'dibatalkan';
}

// 3. Update Database `order` dan `payment` secara Atomic
mysqli_begin_transaction($conn);

// Dapatkan status lama untuk mencegah pengembalian stok berulang
$currentStatus = '';
$currentStatusQuery = mysqli_query($conn, "SELECT status_pesanan FROM `order` WHERE id_order = $orderId LIMIT 1");
if ($currentStatusQuery && mysqli_num_rows($currentStatusQuery) > 0) {
    $currentStatusRow = mysqli_fetch_assoc($currentStatusQuery);
    $currentStatus = strtolower($currentStatusRow['status_pesanan']);
}

$updateOrder = mysqli_query($conn, "UPDATE `order` SET status_pesanan = '$statusOrder' WHERE id_order = $orderId");
$updatePayment = mysqli_query($conn, "UPDATE payment SET status_pembayaran = '$statusBayar', metode_pembayaran = '$paymentType', waktu_bayar = NOW() WHERE id_order = $orderId");

if ($updateOrder && $updatePayment) {
    // Kembalikan stok jika status berubah ke dibatalkan dan status sebelumnya bukan dibatalkan
    if ($statusOrder === 'dibatalkan' && $currentStatus !== 'dibatalkan') {
        $itemsQuery = mysqli_query($conn, "SELECT id_produk, kuantitas FROM order_detail WHERE id_order = $orderId");
        while ($item = mysqli_fetch_assoc($itemsQuery)) {
            $idProduk = intval($item['id_produk']);
            $kuantitas = intval($item['kuantitas']);
            mysqli_query($conn, "UPDATE produk SET stok = stok + $kuantitas WHERE id_produk = $idProduk");
        }
    }
    mysqli_commit($conn);
    header("HTTP/1.1 200 OK");
    echo "Status pembayaran order $orderId berhasil diupdate ke: $statusOrder";
} else {
    mysqli_rollback($conn);
    header("HTTP/1.1 500 Internal Server Error");
    echo "Gagal mengupdate database.";
}
