<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'pesanan';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $base_url);
    exit;
}

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: kelola_pesanan.php");
    exit;
}

$id_order = intval($_GET['id']);

// Ambil data order
$query_order = "
SELECT o.*, p.status_pembayaran, p.metode_pembayaran
FROM `order` o
LEFT JOIN payment p ON o.id_order = p.id_order
WHERE o.id_order = $id_order
";
$result_order = mysqli_query($conn, $query_order);

if (!$result_order || mysqli_num_rows($result_order) === 0) {
    header("Location: kelola_pesanan.php");
    exit;
}

$order = mysqli_fetch_assoc($result_order);
$display_id = "PLG-" . str_pad($order['id_order'], 5, "0", STR_PAD_LEFT);

$error_msg = "";
$success_msg = "";

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = mysqli_real_escape_string($conn, $_POST['status_pesanan']);
    $nomor_resi = isset($_POST['nomor_resi']) ? mysqli_real_escape_string($conn, trim($_POST['nomor_resi'])) : '';
    
    // Validasi: Status 'selesai' hanya dapat dikonfirmasi oleh Pelanggan secara langsung, bukan Admin
    if ($new_status === 'selesai' && strtolower($order['status_pesanan']) !== 'selesai') {
        $error_msg = "Status 'Selesai' hanya dapat dikonfirmasi oleh Pelanggan secara langsung.";
    } else {
        // Mulai transaksi database agar konsisten
        mysqli_begin_transaction($conn);

        try {
            // Update tabel order
            $resi_clause = "";
            if ($new_status === 'dikirim' && isset($_POST['nomor_resi'])) {
                $resi_clause = ", nomor_resi = '$nomor_resi'";
            }
            $update_order = "UPDATE `order` SET status_pesanan = '$new_status' $resi_clause WHERE id_order = $id_order";
            mysqli_query($conn, $update_order);

            // Update tabel payment
            // Jika status baru adalah selesai, kita juga set waktu_bayar jika kosong
            $waktu_bayar_clause = "";
            if ($new_status === 'dibayar' || $new_status === 'selesai' || $new_status === 'diproses' || $new_status === 'dikirim') {
                // Set waktu bayar jika statusnya berlanjut dari pending
                $waktu_bayar_clause = ", waktu_bayar = NOW()";
            }
            
            $update_payment = "UPDATE payment SET status_pembayaran = '$new_status' $waktu_bayar_clause WHERE id_order = $id_order";
            mysqli_query($conn, $update_payment);

            mysqli_commit($conn);
            
            $_SESSION['success_msg'] = "Status pesanan <strong>$display_id</strong> berhasil diubah menjadi <strong>" . ucfirst($new_status) . "</strong>!";
            header("Location: kelola_pesanan.php");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_msg = "Gagal memperbarui status: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Perbarui Status Pesanan</title>

    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --mint-bg: #8DE3C7;
            --cream-bg: #FFEAA7;
            --dark-purple: #3A3063;
            --orange-card: #F2994A;
            --green-card: #27AE60;
            --soft-green: #A8E6CF;
            --white: #FFFFFF;
            --pink-accent: #FF6FB7;
            --gray-light: #F8F9FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--cream-bg);
            color: var(--dark-purple);
            min-height: 100vh;
            display: flex;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 260px;
            background-color: var(--mint-bg);
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .sidebar .logo-box {
            width: 160px;
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar .logo-box img {
            width: 100%;
            height: auto;
            display: block;
        }

        .menu-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0 15px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            text-decoration: none;
            color: var(--dark-purple);
            font-weight: 700;
            font-size: 15px;
            border-radius: 15px;
            transition: all 0.2s;
        }

        .menu-item.active {
            background-color: transparent;
            box-shadow: none;
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
            position: relative;
            min-height: 100vh;
        }

        .header-dash {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .header-dash h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .btn-back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 999px;
            background: var(--dark-purple);
            color: var(--white);
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-back-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        /* ================= FORM CARD ================= */
        .form-card {
            background: white;
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            max-width: 650px;
            margin: 0 auto;
        }

        .form-card h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 25px;
            text-transform: uppercase;
            text-align: center;
            color: var(--dark-purple);
            border-bottom: 2px dashed #EEEEEE;
            padding-bottom: 15px;
        }

        /* ================= INFO BOX ================= */
        .info-box {
            background-color: var(--gray-light);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 5px solid var(--mint-bg);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #888888;
        }

        /* ================= FORM CONTROL ================= */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: var(--dark-purple);
        }

        .select-status {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid var(--soft-green);
            border-radius: 18px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: var(--dark-purple);
            outline: none;
            background-color: white;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .select-status:focus {
            border-color: var(--pink-accent);
        }

        /* ================= ALERT ================= */
        .alert-error {
            background-color: #FFEBEE;
            color: #C62828;
            padding: 15px 20px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 20px;
            border-left: 5px solid #E53935;
        }

        /* ================= BUTTONS ================= */
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn-submit {
            background-color: var(--pink-accent);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 18px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 900;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(255, 111, 183, 0.3);
            text-transform: uppercase;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 111, 183, 0.4);
        }

        .btn-cancel {
            background-color: #E0E0E0;
            color: #666666;
            border: none;
            padding: 14px 28px;
            border-radius: 18px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 900;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            text-transform: uppercase;
            transition: transform 0.2s;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
        }

        /* ================= DECOR ================= */
        .decor-flower-bottom {
            position: fixed;
            bottom: 0;
            right: 0;
            height: 90px;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>

<body>

    <?php require_once '../components/layout/header_admin.php'; ?>

    <div class="main-content">

        <div class="header-dash">
            <h1>UPDATE STATUS PESANAN</h1>
            <a href="detail_pesanan.php?id=<?= $id_order ?>" class="btn-back-link">👁 Lihat Detail</a>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-error">
                ⚠️ <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3>Pembaruan Status Transaksi</h3>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">ID Pesanan</span>
                    <span><?= $display_id ?> (#<?= $order['id_order'] ?>)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Pelanggan</span>
                    <span><?= htmlspecialchars(strtoupper($order['nama_pembeli'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Tagihan</span>
                    <span>Rp. <?= number_format($order['total_tagihan'], 0, ',', '.') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Bayar</span>
                    <span><?= htmlspecialchars(strtoupper($order['metode_pembayaran'] ?? '-')) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Saat Ini</span>
                    <span style="font-weight: 900; color: var(--pink-accent); text-transform: uppercase;">
                        <?= htmlspecialchars($order['status_pesanan'] ?? 'pending') ?>
                    </span>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="status_pesanan">Pilih Status Baru</label>
                    <select name="status_pesanan" id="status_pesanan" class="select-status" required>
                        <option value="pending" <?= strtolower($order['status_pesanan']) === 'pending' ? 'selected' : '' ?>>⏳ Pending (Menunggu Pembayaran / Validasi)</option>
                        <option value="dibayar" <?= strtolower($order['status_pesanan']) === 'dibayar' ? 'selected' : '' ?>>💳 Dibayar (Pembayaran Terkonfirmasi)</option>
                        <option value="diproses" <?= strtolower($order['status_pesanan']) === 'diproses' ? 'selected' : '' ?>>⚙️ Diproses (Sedang Dipersiapkan)</option>
                        <option value="dikirim" <?= strtolower($order['status_pesanan']) === 'dikirim' ? 'selected' : '' ?>>🚚 Dikirim (Dalam Perjalanan)</option>
                        <?php if (strtolower($order['status_pesanan']) === 'selesai') : ?>
                            <option value="selesai" selected>🟢 Selesai (Sudah Diterima)</option>
                        <?php endif; ?>
                        <option value="dibatalkan" <?= strtolower($order['status_pesanan']) === 'dibatalkan' ? 'selected' : '' ?>>❌ Dibatalkan</option>
                    </select>
                </div>

                <div class="form-group" id="resi-group" style="<?= strtolower($order['status_pesanan'] ?? '') === 'dikirim' ? '' : 'display: none;' ?>">
                    <label for="nomor_resi">Nomor Resi Pengiriman</label>
                    <input type="text" name="nomor_resi" id="nomor_resi" class="select-status" style="width: 100%; border: 2px solid var(--soft-green);" placeholder="Contoh: JNE123456789" value="<?= htmlspecialchars($order['nomor_resi'] ?? '') ?>" <?= strtolower($order['status_pesanan'] ?? '') === 'dikirim' ? 'required' : '' ?>>
                </div>

                <div class="btn-group">
                    <a href="kelola_pesanan.php" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Simpan Status</button>
                </div>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const statusSelect = document.getElementById('status_pesanan');
                    const resiGroup = document.getElementById('resi-group');
                    const resiInput = document.getElementById('nomor_resi');
 
                    statusSelect.addEventListener('change', function() {
                        if (this.value === 'dikirim') {
                            resiGroup.style.display = 'block';
                            resiInput.setAttribute('required', 'required');
                        } else {
                            resiGroup.style.display = 'none';
                            resiInput.removeAttribute('required');
                        }
                    });
                });
            </script>
        </div>

    </div>

    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

</body>

</html>
