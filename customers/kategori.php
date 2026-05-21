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

$where = "";

if ($kategori != 'all') {

    $kategori_safe = mysqli_real_escape_string($conn, $kategori);

    $where = "WHERE kategori.nama_kategori = '$kategori_safe'";
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

            background: rgba(255, 255, 255, 0.95);

            backdrop-filter: blur(10px);

            height: 85px;

            display: flex;

            justify-content: center;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 999;

            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .nav-container {

            width: 100%;

            max-width: 1300px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;
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

            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));

            gap: 30px;
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

                <a href="kategori.php?kategori=all"
                    class="<?= $kategori == 'all' ? 'active' : '' ?>">
                    Home
                </a>

                <a href="kategori.php?kategori=Pakaian"
                    class="<?= $kategori == 'Pakaian' ? 'active' : '' ?>">
                    Pakaian
                </a>

                <a href="kategori.php?kategori=Mainan"
                    class="<?= $kategori == 'Mainan' ? 'active' : '' ?>">
                    Mainan
                </a>

                <a href="kategori.php?kategori=Perlengkapan"
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
         PRODUK
    ==================================== -->

    <section class="product-section">

        <?php if (mysqli_num_rows($query) > 0) : ?>

            <?php while ($produk = mysqli_fetch_assoc($query)) : ?>

                <div class="product-card">

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

    </section>

</body>

</html>