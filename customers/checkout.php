<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/midtrans.php';
require_once '../includes/auth.php';
require_once '../includes/rajaongkir_helper.php';

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
$totalWeight = $totalItems * 500;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPembeli = trim($_POST['nama_pembeli'] ?? $_SESSION['nama'] ?? '');
    $nomerHp = trim($_POST['nomer_hp'] ?? '');
    $metodePembayaran = 'Midtrans';
    $maxPhoneLength = 20;

    if (empty($namaPembeli)) {
        $message = 'Nama pembeli wajib diisi.';
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

        $ongkir = intval($_POST['ongkir_value'] ?? 0);
        $kurirName = trim($_POST['kurir_name'] ?? '');
        $grandTotal = $totalPrice + $ongkir;

        $safeName = mysqli_real_escape_string($conn, $namaPembeli);
        $phoneWithCourier = $nomerHp . (!empty($kurirName) ? " ($kurirName)" : "");
        $safePhone = mysqli_real_escape_string($conn, $phoneWithCourier);
        $safeMethod = mysqli_real_escape_string($conn, $metodePembayaran);
        $userIdEscaped = mysqli_real_escape_string($conn, $userId);

        $insertOrderSql = "INSERT INTO `order` (id_user, nomer_hp, nama_pembeli, total_tagihan, status_pesanan, no_resi)
            VALUES ($userIdEscaped, '$safePhone', '$safeName', $grandTotal, 'pending', '')";

        if (mysqli_query($conn, $insertOrderSql)) {
            $orderId = mysqli_insert_id($conn);
            if ($orderId <= 0) {
                $orderIdResult = mysqli_query($conn, "SELECT MAX(id_order) AS id_order FROM `order`");
                if ($orderIdResult && $orderRow = mysqli_fetch_assoc($orderIdResult)) {
                    $orderId = intval($orderRow['id_order']);
                }
            }

            if ($orderId > 0) {
                // Update/Insert shipping address
                $safeJalan = mysqli_real_escape_string($conn, $_POST['alamat_lengkap'] ?? '');
                $safeKabupaten = mysqli_real_escape_string($conn, $_POST['city_name'] ?? '');
                $safeKodePos = intval($_POST['postal_code'] ?? 0);
                
                $checkAddress = mysqli_query($conn, "SELECT id_alamat FROM alamat_pengiriman WHERE id_user = $userId LIMIT 1");
                if (mysqli_num_rows($checkAddress) > 0) {
                    mysqli_query($conn, "UPDATE alamat_pengiriman SET jalan = '$safeJalan', kabupaten = '$safeKabupaten', kodepos = $safeKodePos WHERE id_user = $userId");
                } else {
                    mysqli_query($conn, "INSERT INTO alamat_pengiriman (id_user, label_alamat, jalan, kecamatan, kabupaten, kodepos) VALUES ($userId, 'Alamat Utama', '$safeJalan', '-', '$safeKabupaten', $safeKodePos)");
                }

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

                    // Kurangi stok produk sesuai barang yang dicheckout
                    $updateStockSql = "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = $idProduk";
                    mysqli_query($conn, $updateStockSql);
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

                    if ($ongkir > 0) {
                        $itemsForMidtrans[] = [
                            'id' => 'ongkir',
                            'price' => (int)$ongkir,
                            'quantity' => 1,
                            'name' => 'Ongkos Kirim (' . (!empty($kurirName) ? $kurirName : 'Expedition') . ')'
                        ];
                    }

                    // Meminta Token ke Midtrans
                    $customerDetails = [
                        'nama' => $namaPembeli,
                        'email' => $userEmail,
                        'phone' => $nomerHp
                    ];
                    $snapToken = getMidtransSnapToken($orderId, $grandTotal, $customerDetails, $itemsForMidtrans);

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

                        <div class="field-group">
                            <label for="province">Provinsi Tujuan</label>
                            <select id="province" name="province" required>
                                <option value="">Pilih Provinsi</option>
                                <?php 
                                $provinces = rajaongkir_get_provinces();
                                foreach ($provinces as $p) : 
                                ?>
                                    <option value="<?= $p['province_id'] ?>"><?= htmlspecialchars($p['province']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="city">Kota / Kabupaten Tujuan</label>
                            <select id="city" name="city" required disabled>
                                <option value="">Pilih Kota / Kabupaten</option>
                            </select>
                            <input type="hidden" id="city_name" name="city_name">
                            <input type="hidden" id="postal_code" name="postal_code">
                        </div>

                        <div class="field-group">
                            <label for="alamat_lengkap">Alamat Lengkap (Jalan, RT/RW, No. Rumah)</label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" style="width:100%; padding:14px 16px; border:1px solid #ddd; border-radius:16px; font-size:15px; font-family:inherit; outline:none;" placeholder="Masukkan alamat lengkap pengiriman" required></textarea>
                        </div>

                        <div class="field-group">
                            <label for="courier">Pilih Kurir Ekspedisi</label>
                            <select id="courier" name="courier" required disabled>
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                <option value="tiki">TIKI (Titipan Kilat)</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>

                        <div class="field-group" id="layanan-group" style="display:none;">
                            <label for="layanan">Pilih Layanan Pengiriman</label>
                            <select id="layanan" name="layanan" required>
                                <option value="">Pilih Layanan</option>
                            </select>
                        </div>

                        <!-- Hidden fields to hold selected shipping details -->
                        <input type="hidden" id="ongkir_value" name="ongkir_value" value="0">
                        <input type="hidden" id="kurir_name" name="kurir_name" value="">
                        <input type="hidden" id="total_weight" value="<?= $totalWeight ?>">

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
                     <div class="summary-row" id="ongkir-row" style="display:none;"><span>Ongkos Kirim</span><span id="ongkir-text">Rp 0</span></div>
                     <div class="summary-row total"><span>Total Bayar</span><span id="total-bayar-text">Rp <?= number_format($totalPrice, 0, ',', '.'); ?></span></div>
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
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const cityNameInput = document.getElementById('city_name');
            const postalCodeInput = document.getElementById('postal_code');
            const courierSelect = document.getElementById('courier');
            const layananSelect = document.getElementById('layanan');
            const layananGroup = document.getElementById('layanan-group');
            const ongkirValueInput = document.getElementById('ongkir_value');
            const kurirNameInput = document.getElementById('kurir_name');
            const totalWeight = parseInt(document.getElementById('total_weight').value);
            
            const ongkirRow = document.getElementById('ongkir-row');
            const ongkirText = document.getElementById('ongkir-text');
            const totalBayarText = document.getElementById('total-bayar-text');
            const basePrice = <?= $totalPrice ?>;

            // 1. Ketika Provinsi berubah
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                citySelect.innerHTML = '<option value="">Pilih Kota / Kabupaten</option>';
                citySelect.disabled = true;
                courierSelect.value = '';
                courierSelect.disabled = true;
                layananSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                layananGroup.style.display = 'none';
                resetOngkir();

                if (!provinceId) return;

                fetch(`ajax_ongkir.php?action=get_cities&province_id=${provinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            data.cities.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.city_id;
                                option.setAttribute('data-name', `${city.type} ${city.city_name}`);
                                option.setAttribute('data-postal', city.postal_code);
                                option.textContent = `${city.type} ${city.city_name}`;
                                citySelect.appendChild(option);
                            });
                            citySelect.disabled = false;
                        }
                    });
            });

            // 2. Ketika Kota berubah
            citySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                cityNameInput.value = selectedOption.getAttribute('data-name') || '';
                postalCodeInput.value = selectedOption.getAttribute('data-postal') || '';
                
                courierSelect.value = '';
                courierSelect.disabled = !this.value;
                layananSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                layananGroup.style.display = 'none';
                resetOngkir();
            });

            // 3. Ketika Kurir berubah
            courierSelect.addEventListener('change', function() {
                const cityId = citySelect.value;
                const courier = this.value;
                
                layananSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                layananGroup.style.display = 'none';
                resetOngkir();

                if (!cityId || !courier) return;

                fetch(`ajax_ongkir.php?action=get_ongkir&city_id=${cityId}&weight=${totalWeight}&courier=${courier}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.rates.length > 0) {
                            data.rates.forEach(rate => {
                                const option = document.createElement('option');
                                option.value = rate.cost;
                                option.setAttribute('data-service', rate.service);
                                option.textContent = `${rate.service} - Rp ${rate.cost.toLocaleString('id-ID')} (${rate.etd})`;
                                layananSelect.appendChild(option);
                            });
                            layananGroup.style.display = 'block';
                        } else {
                            alert('Gagal mengambil tarif pengiriman atau kurir tidak didukung.');
                        }
                    });
            });

            // 4. Ketika Layanan berubah
            layananSelect.addEventListener('change', function() {
                const cost = parseInt(this.value) || 0;
                const selectedOption = this.options[this.selectedIndex];
                const service = selectedOption.getAttribute('data-service') || '';
                const courier = courierSelect.value.toUpperCase();

                if (cost > 0 && service) {
                    ongkirValueInput.value = cost;
                    kurirNameInput.value = `${courier} ${service}`;
                    
                    ongkirRow.style.display = 'flex';
                    ongkirText.textContent = `Rp ${cost.toLocaleString('id-ID')}`;
                    totalBayarText.textContent = `Rp ${(basePrice + cost).toLocaleString('id-ID')}`;
                } else {
                    resetOngkir();
                }
            });

            function resetOngkir() {
                ongkirValueInput.value = '0';
                kurirNameInput.value = '';
                ongkirRow.style.display = 'none';
                totalBayarText.textContent = `Rp ${basePrice.toLocaleString('id-ID')}`;
            }
        });
    </script>
</body>

</html>