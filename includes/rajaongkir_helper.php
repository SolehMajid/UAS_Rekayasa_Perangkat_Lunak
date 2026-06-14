<?php
// includes/rajaongkir_helper.php
require_once __DIR__ . '/../config/rajaongkir.php';

/**
 * Helper to make cURL requests to RajaOngkir
 */
function rajaongkir_request($endpoint, $params = [], $method = 'GET') {
    $apiKey = RAJAONGKIR_API_KEY;
    $accountType = RAJAONGKIR_ACCOUNT_TYPE;
    
    // Jika masih placeholder, return null agar memicu fallback mock data
    if ($apiKey === 'jhPJLRMBdef3560b186ff285dvt6bhEH') {
        $GLOBALS['rajaongkir_last_error'] = 'API Key masih menggunakan placeholder/belum diisi.';
        return null;
    }
    
    $url = "https://api.rajaongkir.com/" . $accountType . "/" . $endpoint;
    
    $ch = curl_init();
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    } else {
        $queryString = !empty($params) ? '?' . http_build_query($params) : '';
        curl_setopt($ch, CURLOPT_URL, $url . $queryString);
    }
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "key: " . $apiKey,
            "content-type: application/x-www-form-urlencoded"
        ]
    ]);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($err) {
        $GLOBALS['rajaongkir_last_error'] = 'cURL Error: ' . $err;
        return null;
    }
    
    $decoded = json_decode($response, true);
    
    // Cek jika response format tidak sesuai atau ada error dari API RajaOngkir
    if (isset($decoded['rajaongkir']['status']['code']) && $decoded['rajaongkir']['status']['code'] !== 200) {
        $GLOBALS['rajaongkir_last_error'] = 'API Error (' . $decoded['rajaongkir']['status']['code'] . '): ' . $decoded['rajaongkir']['status']['description'];
    }
    
    return $decoded;
}

/**
 * Mendapatkan daftar provinsi (dengan fallback mock data jika API key kosong)
 */
function rajaongkir_get_provinces() {
    $res = rajaongkir_request('province');
    if ($res && isset($res['rajaongkir']['results'])) {
        $GLOBALS['rajaongkir_is_mocked'] = false;
        return $res['rajaongkir']['results'];
    }
    
    $GLOBALS['rajaongkir_is_mocked'] = true;
    // Mock Data Fallback
    return [
        ['province_id' => '6', 'province' => 'DKI Jakarta'],
        ['province_id' => '9', 'province' => 'Jawa Barat'],
        ['province_id' => '10', 'province' => 'Jawa Tengah'],
        ['province_id' => '11', 'province' => 'Jawa Timur'],
        ['province_id' => '17', 'province' => 'Bali']
    ];
}

/**
 * Mendapatkan daftar kota berdasarkan provinsi
 */
function rajaongkir_get_cities($province_id) {
    $res = rajaongkir_request('city', ['province' => $province_id]);
    if ($res && isset($res['rajaongkir']['results'])) {
        $GLOBALS['rajaongkir_is_mocked'] = false;
        return $res['rajaongkir']['results'];
    }
    
    $GLOBALS['rajaongkir_is_mocked'] = true;
    // Mock Data Fallback
    $mockCities = [
        '6' => [ // DKI Jakarta
            ['city_id' => '151', 'city_name' => 'Jakarta Barat', 'type' => 'Kota', 'postal_code' => '11610'],
            ['city_id' => '152', 'city_name' => 'Jakarta Pusat', 'type' => 'Kota', 'postal_code' => '10110'],
            ['city_id' => '153', 'city_name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12110'],
            ['city_id' => '154', 'city_name' => 'Jakarta Timur', 'type' => 'Kota', 'postal_code' => '13110'],
            ['city_id' => '155', 'city_name' => 'Jakarta Utara', 'type' => 'Kota', 'postal_code' => '14110']
        ],
        '9' => [ // Jawa Barat
            ['city_id' => '23', 'city_name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
            ['city_id' => '78', 'city_name' => 'Bogor', 'type' => 'Kota', 'postal_code' => '16111'],
            ['city_id' => '115', 'city_name' => 'Depok', 'type' => 'Kota', 'postal_code' => '16411'],
            ['city_id' => '54', 'city_name' => 'Bekasi', 'type' => 'Kota', 'postal_code' => '17111']
        ],
        '10' => [ // Jawa Tengah
            ['city_id' => '398', 'city_name' => 'Semarang', 'type' => 'Kota', 'postal_code' => '50111'],
            ['city_id' => '427', 'city_name' => 'Surakarta (Solo)', 'type' => 'Kota', 'postal_code' => '57111']
        ],
        '11' => [ // Jawa Timur
            ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota', 'postal_code' => '60111'],
            ['city_id' => '409', 'city_name' => 'Sidoarjo', 'type' => 'Kabupaten', 'postal_code' => '61211'],
            ['city_id' => '247', 'city_name' => 'Malang', 'type' => 'Kota', 'postal_code' => '65111'],
            ['city_id' => '142', 'city_name' => 'Gresik', 'type' => 'Kabupaten', 'postal_code' => '61111']
        ],
        '17' => [ // Bali
            ['city_id' => '114', 'city_name' => 'Denpasar', 'type' => 'Kota', 'postal_code' => '80111'],
            ['city_id' => '17', 'city_name' => 'Badung', 'type' => 'Kabupaten', 'postal_code' => '80361']
        ]
    ];
    
    return $mockCities[$province_id] ?? [];
}

/**
 * Menghitung ongkos kirim ke kota tujuan
 */
function rajaongkir_calculate_cost($destination_city_id, $weight, $courier) {
    $params = [
        'origin' => RAJAONGKIR_ORIGIN_CITY_ID,
        'destination' => $destination_city_id,
        'weight' => $weight,
        'courier' => strtolower($courier)
    ];
    
    $res = rajaongkir_request('cost', $params, 'POST');
    if ($res && isset($res['rajaongkir']['results'][0]['costs'])) {
        $GLOBALS['rajaongkir_is_mocked'] = false;
        $costs = $res['rajaongkir']['results'][0]['costs'];
        $formattedCosts = [];
        foreach ($costs as $c) {
            $formattedCosts[] = [
                'service' => $c['service'],
                'description' => $c['description'],
                'cost' => $c['cost'][0]['value'],
                'etd' => $c['cost'][0]['etd']
            ];
        }
        return $formattedCosts;
    }
    
    $GLOBALS['rajaongkir_is_mocked'] = true;
    // Mock Data Fallback jika gagal atau API Key masih placeholder
    // Mengubah harga ongkir berdasarkan jarak kota tujuan
    $baseOngkir = 9000;
    
    // Simulasi penambahan ongkir berdasarkan kota tujuan
    $destId = intval($destination_city_id);
    if ($destId >= 151 && $destId <= 155) {
        $baseOngkir = 12000; // Ke Jakarta
    } elseif ($destId === 23 || $destId === 78) {
        $baseOngkir = 11000; // Ke Jabar
    } elseif ($destId === 398 || $destId === 427) {
        $baseOngkir = 10000; // Ke Jateng
    } elseif ($destId === 114 || $destId === 17) {
        $baseOngkir = 18000; // Ke Bali
    } elseif ($destId === 444) {
        $baseOngkir = 5000;  // Sesama Surabaya
    } else {
        $baseOngkir = 8000;  // Kab Sidoarjo, Gresik dll dekat Surabaya
    }
    
    // Multiplier berdasarkan berat (per kg)
    $weightKg = ceil($weight / 1000);
    $baseOngkir = $baseOngkir * $weightKg;

    if ($courier === 'jne') {
        return [
            ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'cost' => $baseOngkir - 2000, 'etd' => '3-4 HARI'],
            ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => $baseOngkir, 'etd' => '2-3 HARI'],
            ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => $baseOngkir + 10000, 'etd' => '1 HARI']
        ];
    } elseif ($courier === 'tiki') {
        return [
            ['service' => 'ECO', 'description' => 'Economy Service', 'cost' => $baseOngkir - 2500, 'etd' => '3-5 HARI'],
            ['service' => 'REG', 'description' => 'Regular Service', 'cost' => $baseOngkir - 500, 'etd' => '2-3 HARI'],
            ['service' => 'ONS', 'description' => 'Over Night Service', 'cost' => $baseOngkir + 9000, 'etd' => '1 HARI']
        ];
    } else { // pos
        return [
            ['service' => 'KILAT', 'description' => 'Pos Kilat Khusus', 'cost' => $baseOngkir - 1000, 'etd' => '2-4 HARI'],
            ['service' => 'EXPRESS', 'description' => 'Pos Express', 'cost' => $baseOngkir + 8000, 'etd' => '1 HARI']
        ];
    }
}
