<?php
// test_rajaongkir.php
require_once 'config/rajaongkir.php';
require_once 'includes/rajaongkir_helper.php';

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>RajaOngkir Connection Tester</title>
    <link href='https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Quicksand', sans-serif; padding: 40px; background: #FFF9F3; color: #3A3063; }
        h1, h3 { font-family: 'Nunito', sans-serif; color: #FF6FB7; }
        pre { background: #FAF0E6; padding: 15px; border-radius: 12px; border: 1px solid #EED8C1; overflow-x: auto; font-size: 13px; }
        .box { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #FFF1E5; }
        .alert-warning { color: #d66400; padding: 15px; border: 1px solid #FFD0E3; background: #FFF5F7; border-radius: 12px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { color: green; padding: 15px; border: 1px solid #B2DFDB; background: #E8F5E9; border-radius: 12px; margin-bottom: 20px; font-weight: bold; }
        .btn { background: #FF6FB7; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>";

echo "<h1>RajaOngkir Connection Tester</h1>";
echo "<p>Script ini digunakan untuk menguji integrasi API RajaOngkir Anda (apakah berhasil menembak API atau menggunakan data simulasi/mock).</p>";

// Jalankan request pertama untuk cek koneksi
$provinces = rajaongkir_get_provinces();
$isMocked = $GLOBALS['rajaongkir_is_mocked'] ?? true;
$lastError = $GLOBALS['rajaongkir_last_error'] ?? '';

// Cek status API Key
if (RAJAONGKIR_API_KEY === 'MASUKKAN_API_KEY_ANDA_DI_SINI') {
    echo "<div class='alert-warning'>";
    echo "⚠️ Anda belum memasukkan API Key riil di <code>config/rajaongkir.php</code>!<br>";
    echo "Tes ini akan berjalan menggunakan data tiruan (Mock Data) untuk mendemonstrasikan kelancaran sistem.";
    echo "</div>";
} elseif ($isMocked) {
    echo "<div class='alert-warning'>";
    echo "⚠️ Koneksi API Gagal & Menggunakan Mock Data Fallback!<br>";
    echo "API Key terdeteksi, namun gagal terhubung ke server RajaOngkir.<br>";
    if ($lastError) {
        echo "<strong>Penyebab/Detail Error:</strong> <code>" . htmlspecialchars($lastError) . "</code><br>";
        if (strpos($lastError, '401') !== false || strpos($lastError, 'Unauthorized') !== false || strpos($lastError, 'Invalid key') !== false || strpos($lastError, 'key') !== false) {
            echo "<span style='font-size:12px; font-weight:normal; display:block; margin-top:5px;'>Tips: Pastikan API Key yang Anda salin dari Komerce adalah benar, atau pastikan akun Komerce/RajaOngkir Anda aktif dan bukan dalam mode kadaluwarsa/sandbox yang belum aktif.</span>";
        }
    }
    echo "</div>";
} else {
    echo "<div class='alert-success'>";
    echo "✓ Berhasil Terhubung ke API RajaOngkir Asli!<br>";
    echo "API Key Terdeteksi: <code>" . htmlspecialchars(substr(RAJAONGKIR_API_KEY, 0, 10)) . "...</code> (Tipe Akun: " . strtoupper(RAJAONGKIR_ACCOUNT_TYPE) . ")";
    echo "</div>";
}

// 1. Tes Ambil Provinsi
echo "<div class='box'>";
echo "<h3>1. Tes Mengambil Daftar Provinsi (rajaongkir_get_provinces)</h3>";
echo "<p>Berhasil mengambil <strong>" . count($provinces) . "</strong> provinsi " . ($isMocked ? '(Menggunakan Mock Data)' : '(Menggunakan API Asli)') . ".</p>";
echo "<pre>" . htmlspecialchars(json_encode(array_slice($provinces, 0, 3), JSON_PRETTY_PRINT)) . "\n... (dipotong untuk menghemat halaman)</pre>";
echo "</div>";

// 2. Tes Ambil Kota di Jawa Timur (ID: 11)
echo "<div class='box'>";
echo "<h3>2. Tes Mengambil Kota di Jawa Timur (rajaongkir_get_cities untuk Prov ID: 11)</h3>";
$cities = rajaongkir_get_cities('11');
echo "<p>Berhasil mengambil <strong>" . count($cities) . "</strong> kota/kabupaten.</p>";
echo "<pre>" . htmlspecialchars(json_encode(array_slice($cities, 0, 3), JSON_PRETTY_PRINT)) . "\n... (dipotong untuk menghemat halaman)</pre>";
echo "</div>";

// 3. Form Interaktif Hitung Ongkir
echo "<div class='box'>";
echo "<h3>3. Kalkulator Hitung Ongkos Kirim</h3>";

$dest_city = isset($_POST['dest_city']) ? trim($_POST['dest_city']) : '152'; // default Jakarta Pusat
$weight = isset($_POST['weight']) ? intval($_POST['weight']) : 1000;
$courier = isset($_POST['courier']) ? trim($_POST['courier']) : 'jne';

?>
<form method="POST" action="">
    <div class="form-group">
        <label for="dest_city">Pilih Kota Tujuan (Contoh):</label>
        <select name="dest_city" id="dest_city">
            <option value="152" <?= $dest_city === '152' ? 'selected' : '' ?>>Kota Jakarta Pusat (ID: 152)</option>
            <option value="23" <?= $dest_city === '23' ? 'selected' : '' ?>>Kota Bandung (ID: 23)</option>
            <option value="444" <?= $dest_city === '444' ? 'selected' : '' ?>>Kota Surabaya (ID: 444)</option>
            <option value="114" <?= $dest_city === '114' ? 'selected' : '' ?>>Kota Denpasar (ID: 114)</option>
        </select>
    </div>
    <div class="form-group">
        <label for="weight">Berat Paket (Gram):</label>
        <input type="number" name="weight" id="weight" value="<?= $weight ?>" min="100" step="100">
    </div>
    <div class="form-group">
        <label for="courier">Ekspedisi:</label>
        <select name="courier" id="courier">
            <option value="jne" <?= $courier === 'jne' ? 'selected' : '' ?>>JNE</option>
            <option value="tiki" <?= $courier === 'tiki' ? 'selected' : '' ?>>TIKI</option>
            <option value="pos" <?= $courier === 'pos' ? 'selected' : '' ?>>POS Indonesia</option>
        </select>
    </div>
    <button type="submit" class="btn">Hitung Ongkir</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h4>Hasil Perhitungan Tarif:</h4>";
    echo "<p>Dari ID Asal Toko: <strong>" . RAJAONGKIR_ORIGIN_CITY_ID . "</strong> ke ID Tujuan: <strong>$dest_city</strong>, Berat: <strong>$weight Gram</strong>, Kurir: <strong>" . strtoupper($courier) . "</strong></p>";
    
    $rates = rajaongkir_calculate_cost($dest_city, $weight, $courier);
    
    if ($rates) {
        echo "<pre>" . htmlspecialchars(json_encode($rates, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p style='color:red;'>Gagal menghitung ongkos kirim. Silakan cek koneksi internet atau validitas API Key Anda.</p>";
    }
}
echo "</div>";

echo "</body>
</html>";
