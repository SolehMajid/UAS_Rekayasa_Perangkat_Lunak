<?php
// customers/ajax_lacak.php
session_start();
require_once '../config/database.php';
require_once '../config/rajaongkir.php';
require_once '../includes/auth.php';

checkLogin();

header('Content-Type: application/json');

$resi = isset($_GET['resi']) ? trim($_GET['resi']) : '';
$courier = isset($_GET['courier']) ? strtolower(trim($_GET['courier'])) : 'jne';

if (empty($resi)) {
    echo json_encode(['success' => false, 'message' => 'Nomor resi wajib diisi.']);
    exit;
}

// JNE adalah kurir default jika tidak ditentukan
if (empty($courier)) {
    $courier = 'jne';
}

$useMock = false;

if (RAJAONGKIR_API_KEY === 'MASUKKAN_API_KEY_ANDA_DI_SINI') {
    $useMock = true;
} else {
    // Integrasi Riil ke API RajaOngkir
    $url = "https://api.rajaongkir.com/" . RAJAONGKIR_ACCOUNT_TYPE . "/waybill";
    $payload = "waybill=" . urlencode($resi) . "&courier=" . urlencode($courier);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            "content-type: application/x-www-form-urlencoded",
            "key: " . RAJAONGKIR_API_KEY
        ],
    ]);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($err || $httpCode !== 200) {
        $useMock = true;
    } else {
        $data = json_decode($response, true);
        if (isset($data['rajaongkir']['result'])) {
            $result = $data['rajaongkir']['result'];
            
            $formattedHistory = [];
            if (isset($result['manifest']) && is_array($result['manifest'])) {
                foreach ($result['manifest'] as $item) {
                    $formattedHistory[] = [
                        'date' => date('d M Y, H:i', strtotime($item['manifest_date'] . ' ' . $item['manifest_time'])),
                        'description' => $item['manifest_description'] . ' [' . $item['city_name'] . ']'
                    ];
                }
            }
            
            $formattedResponse = [
                'success' => true,
                'is_mockup' => false,
                'summary' => [
                    'waybill_number' => $result['summary']['waybill_number'] ?? $resi,
                    'courier_name' => $result['summary']['courier_name'] ?? strtoupper($courier),
                    'status' => $result['summary']['status'] ?? 'ON PROCESS',
                    'shipper' => $result['summary']['shipper_name'] ?? 'SQUASHY STORE',
                    'receiver' => $result['summary']['receiver_name'] ?? '',
                    'origin' => $result['summary']['origin'] ?? '',
                    'destination' => $result['summary']['destination'] ?? ''
                ],
                'history' => $formattedHistory
            ];
            echo json_encode($formattedResponse);
            exit;
        } else {
            // Jika response API menyatakan error (misal akun starter tidak support waybill)
            $useMock = true;
        }
    }
}

if ($useMock) {
    // Simulasi respons sukses dari RajaOngkir
    $mockData = [
        'success' => true,
        'is_mockup' => true,
        'summary' => [
            'waybill_number' => $resi,
            'courier_name' => strtoupper($courier),
            'status' => 'DELIVERED',
            'shipper' => 'SQUASHY OFFICIAL',
            'receiver' => $_SESSION['nama'] ?? 'Pelanggan Squashy',
            'origin' => 'SURABAYA',
            'destination' => 'JAKARTA'
        ],
        'history' => [
            [
                'date' => date('d M Y, H:i', strtotime('-1 hour')),
                'description' => 'Paket telah diterima oleh yang bersangkutan (' . ($_SESSION['nama'] ?? 'Penerima') . ').'
            ],
            [
                'date' => date('d M Y, H:i', strtotime('-4 hours')),
                'description' => 'Paket sedang dibawa oleh kurir ' . strtoupper($courier) . ' menuju alamat penerima.'
            ],
            [
                'date' => date('d M Y, H:i', strtotime('-1 day')),
                'description' => 'Paket transit di HUB Utama (Sorting Center).'
            ],
            [
                'date' => date('d M Y, H:i', strtotime('-1 day -2 hours')),
                'description' => 'Paket telah dikirim/diberangkatkan dari cabang Surabaya.'
            ],
            [
                'date' => date('d M Y, H:i', strtotime('-1 day -6 hours')),
                'description' => 'Paket telah diserahkan ke kurir ' . strtoupper($courier) . ' (Manifested).'
            ]
        ]
    ];
    echo json_encode($mockData);
    exit;
}
