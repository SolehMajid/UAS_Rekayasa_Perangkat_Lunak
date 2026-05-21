<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Prefix digunakan untuk penyesuaian path gambar/form aksi antara root dan subfolder
$prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';

if ($id <= 0) {
    echo "<p style='text-align:center; padding: 20px;'>Produk tidak valid 😢</p>";
    exit;
}

// Fetch detail produk
$query_produk = mysqli_query($conn, "
    SELECT p.*, k.nama_kategori 
    FROM produk p 
    JOIN kategori k ON p.id_kategori = k.id_kategori 
    WHERE p.id_produk = $id 
    LIMIT 1
");

if (!$query_produk || mysqli_num_rows($query_produk) === 0) {
    echo "<p style='text-align:center; padding: 20px;'>Produk tidak ditemukan 😢</p>";
    exit;
}

$produk = mysqli_fetch_assoc($query_produk);

// Fetch ulasan produk
$query_ulasan = mysqli_query($conn, "
    SELECT r.*, u.nama_lengkap 
    FROM review_produk r 
    JOIN user u ON r.id_user = u.id_user 
    WHERE r.id_produk = $id 
    ORDER BY r.created_at DESC
");

$ulasan_list = [];
if ($query_ulasan) {
    while ($row = mysqli_fetch_assoc($query_ulasan)) {
        $ulasan_list[] = $row;
    }
}

// Menghitung rerata rating jika ada ulasan
$total_rating = 0;
$average_rating = 0;
$jumlah_ulasan = count($ulasan_list);
if ($jumlah_ulasan > 0) {
    foreach ($ulasan_list as $ulasan) {
        $total_rating += $ulasan['rating'];
    }
    $average_rating = round($total_rating / $jumlah_ulasan, 1);
}

// Tentukan path foto produk
$foto_path = !empty($produk['foto']) ? $prefix . htmlspecialchars($produk['foto']) : $prefix . 'assets/images/ada.png';
?>

<div class="modal-product-grid">
    <!-- Kolom Kiri: Foto Produk -->
    <div class="modal-product-image">
        <img src="<?= $foto_path ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
    </div>

    <!-- Kolom Kanan: Detail Dasar & Tombol Beli -->
    <div class="modal-product-info">
        <span class="modal-badge"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
        <h2><?= htmlspecialchars($produk['nama_produk']) ?></h2>
        
        <!-- Rating Rerata -->
        <?php if ($jumlah_ulasan > 0) : ?>
            <div class="modal-rating-summary">
                <span class="stars"><?= str_repeat('⭐', round($average_rating)) ?></span>
                <span class="rating-text"><strong><?= $average_rating ?></strong> / 5.0 (<?= $jumlah_ulasan ?> Ulasan)</span>
            </div>
        <?php else : ?>
            <div class="modal-rating-summary no-reviews">
                <span>Belum ada ulasan ⭐</span>
            </div>
        <?php endif; ?>

        <div class="modal-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>
        
        <div class="modal-stok">
            <strong>Ketersediaan:</strong> 
            <span class="<?= $produk['stok'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                <?= $produk['stok'] > 0 ? 'Stok Tersedia (' . $produk['stok'] . ' pcs)' : 'Stok Habis 😢' ?>
            </span>
        </div>

        <!-- Form Add to Cart -->
        <?php if ($produk['stok'] > 0) : ?>
            <form action="<?= $prefix ?>customers/keranjang.php?action=add" method="POST" class="modal-cart-form">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                <div class="buy-action-wrapper">
                    <button type="submit" class="modal-buy-btn">
                        🛒 Tambah ke Keranjang
                    </button>
                </div>
            </form>
        <?php else : ?>
            <button class="modal-buy-btn disabled" disabled>
                Stok Habis
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Deskripsi Produk -->
<div class="modal-product-description">
    <h3>Deskripsi Produk</h3>
    <p><?= nl2br(htmlspecialchars($produk['deskripsi'] ?: 'Tidak ada deskripsi uuntuk produk ini.')) ?></p>
</div>

<!-- Section Ulasan -->
<div class="modal-reviews-section">
    <h3>Ulasan Pembeli (<?= $jumlah_ulasan ?>)</h3>
    
    <?php if ($jumlah_ulasan > 0) : ?>
        <div class="reviews-list">
            <?php foreach ($ulasan_list as $ulasan) : ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-avatar">
                            <?= mb_substr(htmlspecialchars($ulasan['nama_lengkap']), 0, 1) ?>
                        </div>
                        <div class="reviewer-meta">
                            <h4><?= htmlspecialchars($ulasan['nama_lengkap']) ?></h4>
                            <span class="review-date"><?= date('d F Y, H:i', strtotime($ulasan['created_at'])) ?></span>
                        </div>
                        <div class="review-stars">
                            <?= str_repeat('⭐', intval($ulasan['rating'])) ?>
                        </div>
                    </div>
                    
                    <div class="review-content">
                        <p><?= nl2br(htmlspecialchars($ulasan['komentar'])) ?></p>
                        
                        <?php if (!empty($ulasan['foto_review'])) : ?>
                            <div class="review-photo">
                                <img src="<?= $prefix . htmlspecialchars($ulasan['foto_review']) ?>" alt="Foto Ulasan">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Balasan Admin jika ada -->
                    <?php if (!empty($ulasan['balasan_admin'])) : ?>
                        <div class="admin-reply">
                            <div class="reply-header">
                                <span class="reply-badge">Balasan Admin 🐰</span>
                            </div>
                            <p><?= nl2br(htmlspecialchars($ulasan['balasan_admin'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-reviews">
            <p>Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan! 💖</p>
        </div>
    <?php endif; ?>
</div>
