<?php
session_start();
require_once __DIR__ . '/config/database.php';

$top_products = [];
$query_best = mysqli_query($conn, "
    SELECT p.*, COALESCE(SUM(od.kuantitas), 0) AS total_sold
    FROM produk p
    LEFT JOIN order_detail od ON p.id_produk = od.id_produk
    WHERE p.stok > 0
    GROUP BY p.id_produk
    ORDER BY total_sold DESC, p.id_produk DESC
    LIMIT 4
");
if ($query_best) {
    while ($row = mysqli_fetch_assoc($query_best)) {
        $top_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Squashy – Toko Anak Seru!</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --pink: #FF6FB7;
            --yellow: #FFD93D;
            --blue: #4DC8F0;
            --green: #6EDB8F;
            --orange: #FF852D;
            --white: #FFFFFF;
            --soft-bg: #A3D8F4;
            --dark: #3A3063;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--soft-bg);
            background-image: url('assets/images/tbg.png');
            /* Motif awan transparan */
            background-size: cover;
            background-attachment: fixed;
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0 50px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            width: 130px;
            height: auto;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            color: var(--dark);
        }

        .nav-links a.active {
            background-color: var(--pink);
            color: white;
        }

        /* ── MAIN CONTENT (PERSIS GAMBAR BUNDA) ── */
        .main-wrapper {
            flex: 1;
            padding: 40px 20px 100px;
            /* Padding bawah untuk space footer */
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Banner Diskon Seru */
        .promo-banner-seru {
            background: white;
            width: 100%;
            max-width: 1100px;
            border-radius: 40px;
            border: 8px solid white;
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        /* Elemen Ceria (Awan & Balon) di Banner */
        .cloud-seru {
            position: absolute;
            background: white;
            border-radius: 50px;
            opacity: .6;
        }

        .cloud-seru.c1 {
            width: 120px;
            height: 40px;
            top: 20px;
            left: 5%;
        }

        .cloud-seru.c2 {
            width: 90px;
            height: 30px;
            top: 40px;
            right: 8%;
        }

        .balloon-seru {
            position: absolute;
            font-size: 30px;
        }

        .b1-seru {
            bottom: 20px;
            left: 10%;
        }

        .b2-seru {
            top: 30px;
            left: 18%;
        }

        .b3-seru {
            bottom: 40px;
            right: 12%;
        }

        .b4-seru {
            top: 50px;
            right: 22%;
        }

        .hero-text-seru h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 48px;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .hero-text-seru h1 span {
            color: var(--pink);
        }

        .hero-text-seru .subtitle {
            font-size: 18px;
            color: #666;
            font-weight: 700;
        }

        .discount-badge-seru {
            background: var(--yellow);
            display: inline-block;
            padding: 15px 30px;
            border-radius: 20px;
            font-weight: 900;
            font-size: 24px;
            margin: 20px 0;
            transform: rotate(-2deg);
        }

        .discount-badge-seru .pct {
            color: var(--pink);
        }

        /* Kategori Cards */
        .categories-seru {
            display: flex;
            gap: 16px;
            margin-bottom: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cat-card-seru {
            background: white;
            width: 140px;
            height: 90px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            color: #555;
            text-decoration: none;
            transition: transform 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .cat-card-seru:hover {
            transform: translateY(-3px);
        }

        .cat-card-seru span {
            font-size: 30px;
            margin-bottom: 5px;
        }

        /* Judul Section */
        .section-title-seru {
            font-family: 'Nunito', sans-serif;
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .section-title-seru span {
            color: var(--pink);
        }

        /* Grid Produk yang Konsisten */
        .product-grid-seru {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card-seru {
            background: white;
            border-radius: 30px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 380px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            animation: float 4s ease-in-out infinite;
        }

        .product-card-seru:hover {
            transform: translateY(-10px) scale(1.03);
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .product-img-box-seru {
            font-size: 70px;
            background: #f9f9f9;
            border-radius: 20px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .btn-buy-seru {
            background: var(--orange);
            color: white !important;
            border: none;
            padding: 12px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            transition: 0.3s;
        }

        .btn-buy-seru:hover {
            transform: scale(1.05);
        }

        /* ── FOOTER ABOUT US (KONSISTEN DI KIRI) ── */
        .custom-footer-seru {
            background: white;
            height: 80px;
            display: flex;
            align-items: center;
            padding: 0 50px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
        }

        .footer-left-content-seru {
            display: flex;
            align-items: center;
            z-index: 2;
        }

        .about-us-link-seru {
            text-decoration: none;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 20px;
            /* Jarak agar tidak tertutup bunga kiri */
            transition: color 0.3s;
        }

        .about-us-link-seru:hover {
            color: var(--pink);
        }

        .info-circle-seru {
            border: 2px solid currentColor;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Dekorasi Tanaman di Footer */
        .decor-flower-seru {
            position: absolute;
            bottom: 0;
            height: 110px;
            pointer-events: none;
        }

        .flower-left-seru {
            left: 0;
        }

        .flower-right-seru {
            right: 0;
        }

        /* ===================================
           MODAL DETAIL PRODUK (SQUASHY THEME)
        =================================== */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(58, 48, 99, 0.65);
            backdrop-filter: blur(8px);
            transition: opacity 0.3s ease;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.show {
            display: flex;
            animation: fadeIn 0.3s forwards;
        }

        .modal-content {
            background-color: #ffffff;
            margin: auto;
            padding: 30px;
            border-radius: 35px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            border: 6px solid white;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: var(--pink) #f0f0f0;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-content::-webkit-scrollbar {
            width: 8px;
        }
        .modal-content::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: var(--pink);
            border-radius: 10px;
        }

        .close-modal {
            position: absolute;
            right: 25px;
            top: 20px;
            color: var(--dark);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            z-index: 10;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            border-radius: 50%;
        }

        .close-modal:hover {
            color: var(--pink);
            transform: rotate(90deg) scale(1.1);
            background: #eee;
        }

        /* Grid Layout */
        .modal-product-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 30px;
            margin-top: 15px;
            text-align: left;
        }

        .modal-product-image {
            width: 100%;
            height: 320px;
            border-radius: 25px;
            overflow: hidden;
            background: #f9f9f9;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .modal-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-product-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }

        .modal-badge {
            align-self: flex-start;
            background: var(--pink);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .modal-product-info h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 32px;
            color: var(--dark);
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .modal-rating-summary {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        .modal-rating-summary .stars {
            font-size: 18px;
        }
        .modal-rating-summary .rating-text {
            font-size: 14px;
            color: #666;
        }

        .modal-price {
            font-size: 30px;
            color: var(--orange);
            font-weight: 800;
            margin-bottom: 15px;
        }

        .modal-stok {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
        }
        .modal-stok .in-stock {
            color: var(--green);
            font-weight: 700;
        }
        .modal-stok .out-of-stock {
            color: #d32f2f;
            font-weight: 700;
        }

        .modal-buy-btn {
            border: none;
            background: var(--orange);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: 0.3s;
            box-shadow: 0 6px 15px rgba(255, 133, 45, 0.3);
            width: 100%;
        }
        .modal-buy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 133, 45, 0.4);
        }
        .modal-buy-btn.disabled {
            background: #ccc;
            box-shadow: none;
            cursor: not-allowed;
        }

        .modal-product-description {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px dashed #f0f0f0;
            text-align: left;
        }
        .modal-product-description h3, .modal-reviews-section h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 12px;
        }
        .modal-product-description p {
            color: #555;
            line-height: 1.6;
            font-size: 15px;
        }

        /* Reviews Section */
        .modal-reviews-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px dashed #f0f0f0;
            text-align: left;
        }
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .review-item {
            background: #fcfcfc;
            border: 1px solid #f0f0f0;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.01);
            text-align: left;
        }
        .review-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }
        .reviewer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--soft-bg);
            color: var(--dark);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .reviewer-meta {
            flex: 1;
        }
        .reviewer-meta h4 {
            color: var(--dark);
            font-size: 16px;
            margin: 0;
        }
        .reviewer-meta .review-date {
            font-size: 12px;
            color: #999;
        }
        .review-stars {
            font-size: 14px;
        }
        .review-content p {
            font-size: 14.5px;
            color: #444;
            line-height: 1.5;
        }
        .review-photo {
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            max-width: 150px;
            height: 100px;
            border: 2px solid #eee;
        }
        .review-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Balasan Admin */
        .admin-reply {
            margin-top: 15px;
            padding: 15px;
            background: #f0faf2;
            border-left: 4px solid var(--green);
            border-radius: 0 15px 15px 0;
        }
        .reply-badge {
            font-size: 12px;
            font-weight: bold;
            color: #2e7d32;
            display: inline-block;
            margin-bottom: 5px;
        }
        .admin-reply p {
            margin: 0;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }

        .empty-reviews {
            text-align: center;
            padding: 30px;
            background: #fafafa;
            border-radius: 20px;
            color: #777;
            font-size: 15px;
        }

        .product-card-seru {
            cursor: pointer;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media(max-width: 768px) {
            .modal-product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .modal-product-image {
                height: 240px;
            }
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body data-theme="home">

    <?= require_once __DIR__ . "/components/layout/header.php" ?>

    <div class="main-wrapper">
        <section class="promo-banner-seru">
            <div class="cloud-seru c1"></div>
            <div class="cloud-seru c2"></div>
            <span class="balloon-seru b1-seru">🎈</span>
            <span class="balloon-seru b2-seru">🎀</span>
            <span class="balloon-seru b3-seru">🎁</span>
            <span class="balloon-seru b4-seru">🌟</span>

            <div class="hero-text-seru">
                <h1>Diskon <span>Seru!</span></h1>
                <p class="subtitle">Ayo Belanja Hemat di Squashy!</p>
                <div class="discount-badge-seru">Up To <span class="pct">50%</span> OFF</div>
                <br>
                <a href="#produk" style="color: var(--pink); font-weight: 800; text-decoration: none;">Belanja Sekarang →</a>
            </div>
        </section>

        <div class="categories-seru">
            <a href="/customers/kategori.php" class="cat-card-seru"><span>👕</span>Pakaian Anak</a>
            <a href="/customers/kategori.php" class="cat-card-seru"><span>🚂</span>Mainan Seru</a>
            <a href="/customers/kategori.php" class="cat-card-seru"><span>🍼</span>Perlengkapan</a>
            <a href="/customers/kategori.php" class="cat-card-seru"><span>🎁</span>Promo Spesial</a>
        </div>

        <h2 class="section-title-seru">Produk <span>Pilihan</span> Bunda</h2>

        <div class="product-grid-seru" id="productGridSeru">
            <?php if (!empty($top_products)) : ?>
                <?php foreach ($top_products as $product) : ?>
                    <div class="product-card-seru" data-id="<?= $product['id_produk'] ?>">
                        <?php if (!empty($product['foto'])) : ?>
                            <div class="product-img-box-seru">
                                <img src="<?= htmlspecialchars($product['foto']); ?>" alt="<?= htmlspecialchars($product['nama_produk']); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:20px;" />
                            </div>
                        <?php else : ?>
                            <div class="product-img-box-seru">🎁</div>
                        <?php endif; ?>
                        <div>
                            <h4 style="margin-bottom:5px"><?= htmlspecialchars($product['nama_produk']); ?></h4>
                            <p style="color:var(--pink); font-weight:800">Rp <?= number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p style="font-size:12px; color:#777; margin-top:6px;">Terjual <?= number_format($product['total_sold'], 0, ',', '.'); ?> pcs</p>
                        </div>
                        <?php if (isset($_SESSION['login'])) : ?>
                            <form action="customers/keranjang.php?action=add" method="POST" style="margin: 0; width: 100%;">
                                <input type="hidden" name="id_produk" value="<?= $product['id_produk']; ?>">
                                <button type="submit" class="btn-buy-seru">BELI SEKARANG</button>
                            </form>
                        <?php else : ?>
                            <a href="customers/login.php" class="btn-buy-seru">BELI SEKARANG</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 30px;">
                    Belum ada produk terlaris tersedia.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL DETAIL PRODUK -->
    <div id="productDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalLoading" style="text-align: center; padding: 40px; font-size: 18px; color: var(--dark);">
                <p>Loading detail produk... 🐰</p>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        // Modal logic
        const modal = document.getElementById('productDetailModal');
        const modalBody = document.getElementById('modalBody');
        const modalLoading = document.getElementById('modalLoading');
        const closeModal = document.querySelector('.close-modal');

        document.querySelectorAll('.product-card-seru').forEach(card => {
            card.addEventListener('click', function(e) {
                // Jangan picu modal jika klik di tombol beli / form beli
                if (e.target.closest('form') || e.target.closest('.btn-buy-seru')) {
                    return;
                }
                
                const idProduk = this.getAttribute('data-id');
                if (!idProduk) return;

                // Tampilkan modal dengan loading
                modal.classList.add('show');
                modalLoading.style.display = 'block';
                modalBody.innerHTML = '';

                // Fetch detail (prefix kosong karena dari index.php berada di root)
                fetch('customers/get_product_detail.php?id=' + idProduk + '&prefix=')
                    .then(response => response.text())
                    .then(html => {
                        modalLoading.style.display = 'none';
                        modalBody.innerHTML = html;
                    })
                    .catch(err => {
                        modalLoading.style.display = 'none';
                        modalBody.innerHTML = '<p style="text-align:center; padding:20px; color:#d32f2f;">Gagal mengambil data produk 😢</p>';
                        console.error(err);
                    });
            });
        });

        // Close modal handlers
        closeModal.addEventListener('click', () => {
            modal.classList.remove('show');
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    </script>
</body>

</html>