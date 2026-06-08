<?php
// test_midtrans.php
require_once 'config/midtrans.php';

echo "<h1>Midtrans Connection Tester</h1>";
echo "<p>Script ini akan mencoba menembak API Midtrans menggunakan Server Key yang Anda pasang.</p>";

// Cek apakah key masih default
if (MIDTRANS_SERVER_KEY === 'SB-Mid-server-YOUR_SERVER_KEY_HERE' || MIDTRANS_CLIENT_KEY === 'SB-Mid-client-YOUR_CLIENT_KEY_HERE') {
    echo "<div style='color: red; font-weight: bold; padding: 15px; border: 1px solid red; background: #fff1f0; border-radius: 8px; margin-bottom: 20px;'>";
    echo "⚠️ Peringatan: Anda belum mengganti Server Key atau Client Key di config/midtrans.php!";
    echo "</div>";
} else {
    echo "<div style='color: green; padding: 10px; border: 1px solid green; background: #f6ffed; border-radius: 8px; margin-bottom: 20px;'>";
    echo "✓ Server Key diatur ke: <code>" . htmlspecialchars(substr(MIDTRANS_SERVER_KEY, 0, 15)) . "...</code>";
    echo "</div>";
}

// Lakukan test request
$orderId = "TEST-" . time();
$grossAmount = 10000;
$customerDetails = [
    'nama' => 'Tester Squashy',
    'email' => 'test@squashy.com',
    'phone' => '081234567890'
];

$serverKey = MIDTRANS_SERVER_KEY;
$url = MIDTRANS_IS_PRODUCTION 
    ? 'https://app.midtrans.com/snap/v1/transactions' 
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

$transactionData = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => $grossAmount
    ],
    'customer_details' => [
        'first_name' => $customerDetails['nama'],
        'email' => $customerDetails['email'],
        'phone' => $customerDetails['phone']
    ]
];

$payload = json_encode($transactionData);

echo "<h3>1. Mengirim Request Test ke Midtrans...</h3>";
echo "<pre>URL: $url\nPayload: " . htmlspecialchars(json_encode($transactionData, JSON_PRETTY_PRINT)) . "</pre>";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode($serverKey . ':')
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h3>2. Hasil Respon dari Midtrans:</h3>";
if ($curlError) {
    echo "<p style='color:red;'><strong>cURL Error:</strong> $curlError</p>";
} else {
    echo "<p><strong>HTTP Status Code:</strong> $httpCode</p>";
    $result = json_decode($response, true);
    
    if ($httpCode === 201) {
        echo "<div style='color: green; font-weight: bold; padding: 15px; border: 1px solid green; background: #f6ffed; border-radius: 8px;'>";
        echo "✓ KONEKSI SUKSES!<br>";
        echo "Token Pembayaran Didapatkan: <code>" . htmlspecialchars($result['token']) . "</code><br>";
        echo "Redirect URL: <a href='" . htmlspecialchars($result['redirect_url']) . "' target='_blank'>" . htmlspecialchars($result['redirect_url']) . "</a>";
        echo "</div>";
    } else {
        echo "<div style='color: red; font-weight: bold; padding: 15px; border: 1px solid red; background: #fff1f0; border-radius: 8px;'>";
        echo "❌ KONEKSI GAGAL!<br>";
        echo "Pesan Error dari Midtrans: <pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    }
}
