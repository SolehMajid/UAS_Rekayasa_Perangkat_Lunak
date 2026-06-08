<?php
// customers/ajax_ongkir.php
session_start();
require_once '../includes/rajaongkir_helper.php';
require_once '../includes/auth.php';

checkLogin();

header('Content-Type: application/json');

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

if ($action === 'get_cities') {
    $provinceId = isset($_GET['province_id']) ? trim($_GET['province_id']) : '';
    if (empty($provinceId)) {
        echo json_encode(['success' => false, 'message' => 'ID Provinsi tidak boleh kosong.']);
        exit;
    }
    
    $cities = rajaongkir_get_cities($provinceId);
    echo json_encode(['success' => true, 'cities' => $cities]);
    exit;
} 

elseif ($action === 'get_ongkir') {
    $cityId = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';
    $weight = isset($_GET['weight']) ? intval($_GET['weight']) : 500; // default 500g
    $courier = isset($_GET['courier']) ? trim($_GET['courier']) : 'jne';
    
    if (empty($cityId)) {
        echo json_encode(['success' => false, 'message' => 'ID Kota tujuan tidak boleh kosong.']);
        exit;
    }
    
    if ($weight <= 0) {
        $weight = 500;
    }
    
    // Hitung ongkir untuk kurir tertentu
    $rates = rajaongkir_calculate_cost($cityId, $weight, $courier);
    
    echo json_encode(['success' => true, 'rates' => $rates]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
