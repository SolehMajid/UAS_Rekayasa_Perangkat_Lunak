<?php
session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

/*
|--------------------------------------------------------------------------
| FILTER KATEGORI
|--------------------------------------------------------------------------
*/

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$where_clauses = [];

if ($kategori != 'all') {
    $kategori_safe = mysqli_real_escape_string($conn, $kategori);
    $where_clauses[] = "kategori.nama_kategori = '$kategori_safe'";
}

if ($keyword !== '') {
    $keyword_safe = mysqli_real_escape_string($conn, $keyword);
    $where_clauses[] = "(produk.nama_produk LIKE '%$keyword_safe%' OR produk.deskripsi LIKE '%$keyword_safe%')";
}

$where = "";
if (count($where_clauses) > 0) {
    $where = "WHERE " . implode(" AND ", $where_clauses);
}

/*
|--------------------------------------------------------------------------
| QUERY PRODUK
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT produk.*, kategori.nama_kategori
    FROM produk
    JOIN kategori
    ON produk.id_kategori = kategori.id_kategori
    $where
");

/*
|--------------------------------------------------------------------------
| TEMA DINAMIS
|--------------------------------------------------------------------------
*/

$theme = "home";

if ($kategori == "Mainan") {
    $theme = "mainan";
} elseif ($kategori == "Pakaian") {
    $theme = "pakaian";
} elseif ($kategori == "Perlengkapan") {
    $theme = "perlengkapan";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Squashy - Kategori Produk</title>

    <!-- FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">

    <style>
        :root {

            --pink: #FF6FB7;
            --yellow: #FFD93D;
            --blue: #4DC8F0;
            --green: #6EDB8F;
            --orange: #FF852D;
            --white: #FFFFFF;
            --dark: #3A3063;

            --theme-color: #A3D8F4;
            --header-active: var(--pink);
        }

        [data-theme="mainan"] {
            --theme-color: #A8D695;
            --header-active: #62C974;
        }

        [data-theme="pakaian"] {
            --theme-color: #FFE082;
            --header-active: #FBC02D;
        }

        [data-theme="perlengkapan"] {
            --theme-color: #FFABAB;
            --header-active: #E57373;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {

            background-color: var(--theme-color);

            background-image: url('../assets/images/tbg.png');

            background-size: cover;
            background-attachment: fixed;

            min-height: 100vh;

            transition: 0.5s ease;
        }

        /* ===================================
           NAVBAR
        =================================== */

        nav {

            background: transparent;

            height: 85px;

            display: flex;

            justify-content: center;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 999;

            box-shadow: none;

            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        nav.scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            backdrop-filter: blur(10px);
        }

        .nav-container {

            display: flex;

            align-items: center;

            gap: 30px;

            max-width: 1200px;

            width: 100%;

            justify-content: center;
        }

        .logo img {

            width: 120px;
        }

        .nav-links {

            display: flex;

            gap: 10px;

            align-items: center;
        }

        .nav-links a {

            text-decoration: none;

            color: var(--dark);

            font-weight: 700;

            padding: 10px 18px;

            border-radius: 25px;

            transition: 0.3s;
        }

        .nav-links a:hover {

            transform: translateY(-2px);
        }

        .nav-links a.active {

            background: var(--header-active);

            color: white;
        }

        .login-btn {

            background: var(--orange);

            color: white !important;
        }

        /* ===================================
           HERO
        =================================== */

        .hero {

            text-align: center;

            padding: 70px 20px 40px;
        }

        .hero h1 {

            font-size: 60px;

            color: white;

            font-family: 'Nunito', sans-serif;

            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .hero p {

            margin-top: 15px;

            font-size: 20px;

            color: white;
        }

        /* ===================================
           PRODUCT
        =================================== */

        .product-section {

            padding: 50px;

            display: grid;

            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));

            gap: 30px;

            max-width: 1200px;

            margin: 0 auto;

            width: 100%;
        }

        .product-card {

            background: white;

            border-radius: 30px;

            padding: 20px;

            position: relative;

            overflow: hidden;

            text-align: center;

            transition: 0.3s;

            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);

            animation: float 4s ease-in-out infinite;
        }

        .product-card:hover {

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

        .badge {

            position: absolute;

            top: 15px;

            left: 15px;

            background: var(--header-active);

            color: white;

            padding: 5px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
        }

        .product-image {

            width: 100%;

            height: 230px;

            overflow: hidden;

            border-radius: 20px;

            background: #f5f5f5;
        }

        .product-image img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            transition: 0.3s;
        }

        .product-card:hover img {

            transform: scale(1.08);
        }

        .product-card h3 {

            margin-top: 20px;

            font-size: 24px;

            color: var(--dark);
        }

        .price {

            color: var(--orange);

            font-size: 22px;

            font-weight: bold;

            margin: 15px 0;
        }

        .stok {

            color: #666;

            margin-bottom: 15px;
        }

        .cart-form {
            display: inline-block;
            margin: 0;
        }

        .buy-btn {

            border: none;

            background: var(--header-active);

            color: white;

            padding: 12px 20px;

            border-radius: 20px;

            cursor: pointer;

            font-weight: bold;

            transition: 0.3s;
        }

        .buy-btn:hover {

            transform: scale(1.05);
        }

        .empty {

            grid-column: 1/-1;

            text-align: center;

            color: white;

            font-size: 30px;

            font-weight: bold;

            padding: 80px;
        }

        /* ===================================
           SEARCH BAR
        =================================== */
        .search-container {
            max-width: 650px;
            width: 90%;
            margin: -20px auto 40px auto;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .search-form {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px 8px 6px 16px;
            border-radius: 40px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
            border: 3px solid white;
            align-items: center;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .search-form:focus-within {
            border-color: var(--header-active);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            padding: 8px 10px;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
        }

        .search-input::placeholder {
            color: #bbb;
            font-weight: 500;
        }

        .search-btn {
            background: var(--header-active);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .search-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }

        .clear-search-btn {
            text-decoration: none;
            color: #aaa;
            font-size: 16px;
            padding: 0 12px;
            font-weight: 800;
            transition: color 0.2s;
        }

        .clear-search-btn:hover {
            color: var(--pink);
        }

        /* ===================================
           RESPONSIVE
        =================================== */

        @media(max-width:768px) {

            .hero h1 {

                font-size: 38px;
            }

            .nav-container {

                flex-direction: column;

                gap: 10px;

                padding: 15px;
            }

            nav {

                height: auto;

                padding: 15px 0;
            }

            .nav-links {

                flex-wrap: wrap;

                justify-content: center;
            }

            .product-section {

                padding: 20px;
            }
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
            background: var(--header-active);
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
            background: var(--theme-color);
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

        .product-card {
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

<body data-theme="<?= $theme ?>">

    <!-- ===================================
         NAVBAR
    ==================================== -->

    <nav>

        <div class="nav-container">

            <div class="logo">

                <a href="../index.php">

                    <img src="../assets/images/logo.png">

                </a>

            </div>

            <div class="nav-links">

                <a href="kategori.php?kategori=all<?= $keyword !== '' ? '&keyword=' . urlencode($keyword) : '' ?>"
                    class="<?= $kategori == 'all' ? 'active' : '' ?>">
                    Home
                </a>

                <a href="kategori.php?kategori=Pakaian<?= $keyword !== '' ? '&keyword=' . urlencode($keyword) : '' ?>"
                    class="<?= $kategori == 'Pakaian' ? 'active' : '' ?>">
                    Pakaian
                </a>

                <a href="kategori.php?kategori=Mainan<?= $keyword !== '' ? '&keyword=' . urlencode($keyword) : '' ?>"
                    class="<?= $kategori == 'Mainan' ? 'active' : '' ?>">
                    Mainan
                </a>

                <a href="kategori.php?kategori=Perlengkapan<?= $keyword !== '' ? '&keyword=' . urlencode($keyword) : '' ?>"
                    class="<?= $kategori == 'Perlengkapan' ? 'active' : '' ?>">
                    Perlengkapan
                </a>

            </div>

            <div class="nav-links">

                <?php if (isset($_SESSION['login'])) : ?>

                    <a href="logout.php"
                        class="login-btn"
                        onclick="return confirm('Yakin logout?')">

                        🚪 Logout

                    </a>

                <?php else : ?>

                    <a href="login.php"
                        class="login-btn">

                        🔐 Login

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </nav>

    <!-- ===================================
         HERO
    ==================================== -->

    <section class="hero">

        <?php if ($kategori == 'Pakaian') : ?>

            <h1>Pakaian Anak Stylish 👕</h1>
            <p>Koleksi pakaian lucu dan nyaman untuk si kecil</p>

        <?php elseif ($kategori == 'Mainan') : ?>

            <h1>Mainan Anak Seru 🎈</h1>
            <p>Mainan edukatif dan menyenangkan setiap hari</p>

        <?php elseif ($kategori == 'Perlengkapan') : ?>

            <h1>Perlengkapan Bayi 👶</h1>
            <p>Semua kebutuhan bayi lengkap dan berkualitas</p>

        <?php else : ?>

            <h1>Selamat Datang di Squashy ✨</h1>
            <p>Dunia lucu perlengkapan anak dan bayi</p>

        <?php endif; ?>

    </section>

    <!-- ===================================
         PENCARIAN PRODUK
    ==================================== -->
    <div class="search-container">
        <form action="kategori.php" method="GET" class="search-form">
            <!-- Simpan filter kategori saat mencari -->
            <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori) ?>">
            
            <input type="text" name="keyword" class="search-input" 
                   placeholder="Cari produk anak impian Anda..." 
                   value="<?= htmlspecialchars($keyword) ?>">
            
            <?php if ($keyword !== '') : ?>
                <a href="kategori.php?kategori=<?= htmlspecialchars($kategori) ?>" class="clear-search-btn" title="Hapus Pencarian">✕</a>
            <?php endif; ?>
            
            <button type="submit" class="search-btn">🔍 Cari</button>
        </form>
    </div>

    <!-- ===================================
         PRODUK
    ==================================== -->

    <section class="product-section">

        <?php if (mysqli_num_rows($query) > 0) : ?>

            <?php while ($produk = mysqli_fetch_assoc($query)) : ?>

                <div class="product-card" data-id="<?= $produk['id_produk'] ?>">

                    <span class="badge">

                        <?= $produk['nama_kategori'] ?>

                    </span>

                    <div class="product-image">

                        <?php if (!empty($produk['foto'])) : ?>
                            <img src="../<?= htmlspecialchars($produk['foto']); ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>">
                        <?php else : ?>
                            <img src="../assets/images/ada.png" alt="No image">
                        <?php endif; ?>

                    </div>

                    <h3>

                        <?= $produk['nama_produk'] ?>

                    </h3>

                    <div class="price">

                        Rp <?= number_format($produk['harga']) ?>

                    </div>

                    <div class="stok">

                        Stok : <?= $produk['stok'] ?>

                    </div>
                    <form action="keranjang.php?action=add" method="POST" class="cart-form">
                        <input type="hidden" name="id_produk" value="<?= $produk['id_produk']; ?>">
                        <button type="submit" class="buy-btn">
                            🛒 Tambah ke Keranjang
                        </button>
                    </form>

                </div>

            <?php endwhile; ?>

        <?php else : ?>

            <div class="empty">

                Produk belum tersedia 😢

            </div>

        <?php endif; ?>

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
        function handleNavbarScroll() {
            const nav = document.querySelector('nav');
            if (nav) {
                if (window.scrollY > 20) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            }
        }

        window.addEventListener('scroll', handleNavbarScroll);
        window.addEventListener('DOMContentLoaded', handleNavbarScroll);
        handleNavbarScroll();

        // Modal logic
        const modal = document.getElementById('productDetailModal');
        const modalBody = document.getElementById('modalBody');
        const modalLoading = document.getElementById('modalLoading');
        const closeModal = document.querySelector('.close-modal');

        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Jangan picu modal jika klik di tombol keranjang / form beli
                if (e.target.closest('.cart-form') || e.target.closest('.buy-btn')) {
                    return;
                }
                
                const idProduk = this.getAttribute('data-id');
                if (!idProduk) return;

                // Tampilkan modal dengan loading
                modal.classList.add('show');
                modalLoading.style.display = 'block';
                modalBody.innerHTML = '';

                // Fetch detail
                fetch('get_product_detail.php?id=' + idProduk + '&prefix=../')
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