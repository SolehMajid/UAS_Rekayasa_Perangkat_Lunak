<?php
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Pastikan user login
checkLogin();

$userId = intval($_SESSION['id_user'] ?? 0);
$id_produk = isset($_GET['id_produk']) ? intval($_GET['id_produk']) : 0;
$id_order = isset($_GET['id_order']) ? intval($_GET['id_order']) : 0;

if ($userId <= 0 || $id_produk <= 0 || $id_order <= 0) {
    header("Location: profil.php");
    exit;
}

// Validasi pesanan: harus milik user ini, status 'selesai', dan berisi produk tersebut
$order_query = mysqli_query($conn, "
    SELECT o.id_order, o.status_pesanan, od.nama_produk, od.foto_produk
    FROM `order` o
    JOIN order_detail od ON o.id_order = od.id_order
    WHERE o.id_order = $id_order AND o.id_user = $userId AND od.id_produk = $id_produk
    LIMIT 1
");

if (!$order_query || mysqli_num_rows($order_query) === 0) {
    die("Pesanan atau produk tidak valid atau tidak ditemukan.");
}

$order_data = mysqli_fetch_assoc($order_query);

if (strtolower($order_data['status_pesanan']) !== 'selesai') {
    die("Anda hanya dapat memberikan ulasan pada pesanan yang sudah selesai.");
}

// Cek apakah ulasan sudah pernah dibuat sebelumnya
$check_review = mysqli_query($conn, "
    SELECT id_review 
    FROM review_produk 
    WHERE id_user = $userId AND id_produk = $id_produk AND id_order = $id_order
    LIMIT 1
");

if ($check_review && mysqli_num_rows($check_review) > 0) {
    header("Location: profil.php");
    exit;
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $komentar = isset($_POST['komentar']) ? mysqli_real_escape_string($conn, trim($_POST['komentar'])) : '';
    
    if ($rating < 1 || $rating > 5) {
        $error_msg = "Silakan pilih rating bintang terlebih dahulu.";
    } else {
        $foto_path = null;
        
        // Handle upload foto review (opsional)
        if (isset($_FILES['foto_review']) && $_FILES['foto_review']['error'] === UPLOAD_ERR_OK) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $uploadDir = __DIR__ . '/../assets/images_sq/';
            $uploadUrlBase = 'assets/images_sq/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['foto_review'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);
            
            if (strpos($mimeType, 'image/') !== 0 || !in_array($extension, $allowed_extensions, true)) {
                $error_msg = "Format file foto ulasan tidak valid. Hanya file gambar (.jpg, .png, .webp) yang diperbolehkan.";
            } else {
                $filename = uniqid('rev_', true) . '.' . $extension;
                $target = $uploadDir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $foto_path = $uploadUrlBase . $filename;
                } else {
                    $error_msg = "Gagal mengunggah foto ulasan.";
                }
            }
        }
        
        if (empty($error_msg)) {
            $foto_sql_val = $foto_path ? "'".mysqli_real_escape_string($conn, $foto_path)."'" : "NULL";
            $insert_query = "
                INSERT INTO review_produk (id_user, id_produk, id_order, rating, komentar, foto_review)
                VALUES ($userId, $id_produk, $id_order, $rating, '$komentar', $foto_sql_val)
            ";
            
            if (mysqli_query($conn, $insert_query)) {
                header("Location: profil.php?success=ulasan");
                exit;
            } else {
                $error_msg = "Gagal menyimpan ulasan: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Ulasan - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Nunito:wght@700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(180deg, #FFE6F3 0%, #FFF5E8 100%);
            color: #333;
            min-height: 100vh;
        }

        main {
            max-width: 700px;
            margin: 30px auto 60px;
            padding: 0 20px;
        }

        .back-link {
            text-decoration: none;
            color: #d24c7d;
            font-weight: 700;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: 0.2s;
        }

        .back-link:hover {
            color: #ff5e9f;
            transform: translateX(-3px);
        }

        .review-container {
            background: #ffffff;
            border-radius: 32px;
            border: 1px solid #ffe6f1;
            box-shadow: 0 18px 40px rgba(255, 155, 201, 0.15);
            padding: 35px;
        }

        .review-header-title {
            font-family: 'Nunito', sans-serif;
            font-size: 2rem;
            color: #3A3063;
            margin-bottom: 25px;
            text-align: center;
        }

        .product-preview-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #FFF7FB;
            border: 1px solid #ffdeec;
            border-radius: 20px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }

        .product-preview-card img {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 5px 12px rgba(0,0,0,0.05);
        }

        .product-preview-card h3 {
            font-size: 1.2rem;
            color: #3A3063;
        }

        .alert-error {
            background-color: #fdf2f2;
            border: 2px solid #fbd5d5;
            color: #b83232;
            padding: 12px 18px;
            border-radius: 15px;
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 700;
            color: #3A3063;
            margin-bottom: 10px;
            font-size: 1.05rem;
        }

        /* Pure CSS Star Rating Selector */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
            font-size: 2.8rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #e0e0e0;
            cursor: pointer;
            transition: color 0.15s ease-in-out, transform 0.1s;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #FFD93D;
        }

        .star-rating label:active {
            transform: scale(0.9);
        }

        .form-control-text {
            width: 100%;
            padding: 15px 20px;
            border-radius: 20px;
            border: 2px solid #ffe1ef;
            outline: none;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            transition: border-color 0.2s;
            resize: vertical;
        }

        .form-control-text:focus {
            border-color: #ff82b8;
        }

        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            width: 100%;
            padding: 12px 18px;
            border-radius: 20px;
            border: 2px dashed #ffe1ef;
            background: #fffdfd;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            color: #666;
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-input:hover {
            border-color: #ff82b8;
            background: #fff9fb;
        }

        .btn-submit-review {
            display: block;
            width: 100%;
            background: #ff82b8;
            color: #fff;
            padding: 16px;
            border-radius: 22px;
            border: none;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 130, 184, 0.3);
            transition: all 0.2s;
            margin-top: 15px;
        }

        .btn-submit-review:hover {
            background: #ff5e9f;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 130, 184, 0.4);
        }

        .btn-submit-review:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../components/layout/header.php'; ?>

    <main>
        <a href="profil.php" class="back-link">← Kembali ke Profil</a>

        <div class="review-container">
            <h1 class="review-header-title">Tulis Ulasan Anda</h1>

            <div class="product-preview-card">
                <img src="../<?= htmlspecialchars($order_data['foto_produk']) ?>" alt="<?= htmlspecialchars($order_data['nama_produk']) ?>">
                <div>
                    <h3><?= htmlspecialchars($order_data['nama_produk']) ?></h3>
                    <p style="color: #83526d; font-size: 0.9rem; margin-top: 4px;">Pesanan #<?= str_pad($id_order, 5, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>

            <?php if (!empty($error_msg)) : ?>
                <div class="alert-error">
                    ⚠️ <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Bagaimana Kualitas Produk Ini? ⭐</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Sangat Baik">★</label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Baik">★</label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Cukup">★</label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Buruk">★</label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sangat Buruk">★</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="komentar">Tulis Komentar atau Ulasan Anda</label>
                    <textarea id="komentar" name="komentar" rows="4" class="form-control-text" placeholder="Ceritakan bagaimana pengalaman Anda menggunakan produk ini..."></textarea>
                </div>

                <div class="form-group">
                    <label for="foto_review">Tambahkan Foto Produk (Opsional)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="foto_review" name="foto_review" class="file-upload-input" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn-submit-review">Kirim Ulasan 💖</button>
            </form>
        </div>
    </main>
</body>

</html>
