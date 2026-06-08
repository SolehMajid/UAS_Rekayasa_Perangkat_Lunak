<?php
// config/midtrans.php

// 1. Tentukan API Keys dari Dashboard Sandbox Midtrans Anda
// Buka Dashboard Midtrans Sandbox > Settings > Access Keys
define('MIDTRANS_SERVER_KEY', env('MIDTRANS_SERVER_KEY', 'Mid-server-4Nhcw0ISEJkzpMAxUWi3fQFA')); // Masukkan Server Key Anda di sini
define('MIDTRANS_CLIENT_KEY', env('MIDTRANS_CLIENT_KEY', 'Mid-client-rWQQ5feezij-GPyh')); // Masukkan Client Key Anda di sini
define('MIDTRANS_IS_PRODUCTION', false); // Set ke false untuk Sandbox (testing) dan true untuk Production (live)

/**
 * Fungsi untuk meminta Snap Token ke Midtrans menggunakan cURL
 * 
 * @param string $orderId ID pesanan unik dari database Squashy
 * @param int $grossAmount Total tagihan belanja (rupiah)
 * @param array $customerDetails Informasi pembeli (nama, email, phone)
 * @param array $itemDetails Rincian produk belanja (opsional)
 * @return string|null Token pembayaran Snap dari Midtrans
 */
function getMidtransSnapToken($orderId, $grossAmount, $customerDetails, $itemDetails = []) {
    $serverKey = MIDTRANS_SERVER_KEY;
    $url = MIDTRANS_IS_PRODUCTION 
        ? 'https://app.midtrans.com/snap/v1/transactions' 
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    // Format data yang dibutuhkan oleh Midtrans API
    $transactionData = [
        'transaction_details' => [
            'order_id' => $orderId,
            'gross_amount' => (int)$grossAmount
        ],
        'customer_details' => [
            'first_name' => $customerDetails['nama'],
            'email' => $customerDetails['email'] ?? '',
            'phone' => $customerDetails['phone'] ?? ''
        ],
        'item_details' => $itemDetails
    ];

    $payload = json_encode($transactionData);

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
    curl_close($ch);

    if ($httpCode === 201) {
        $result = json_decode($response, true);
        return $result['token'] ?? null;
    }
    
    // Log error jika request gagal
    error_log("Midtrans API Error: " . $response);
    return null;
}

/**
 * Fungsi untuk mengecek status transaksi langsung ke Midtrans API
 * 
 * @param string $orderId ID pesanan dari database Squashy
 * @return array|null Detail status transaksi dari Midtrans
 */
function checkMidtransStatus($orderId) {
    $serverKey = MIDTRANS_SERVER_KEY;
    $url = MIDTRANS_IS_PRODUCTION 
        ? "https://api.midtrans.com/v2/$orderId/status" 
        : "https://api.sandbox.midtrans.com/v2/$orderId/status";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    return null;
}

