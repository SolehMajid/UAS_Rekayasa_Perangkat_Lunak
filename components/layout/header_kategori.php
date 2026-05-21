<?php
require_once __DIR__ . '/../../config/app.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection so header can adapt from DB
require_once __DIR__ . '/../../config/database.php';

// Determine current category from query string (used to mark active link)
$current_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';

// Fetch categories from database
$kategori_list = [];
$kq = mysqli_query($conn, "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
if ($kq) {
    while ($row = mysqli_fetch_assoc($kq)) {
        $kategori_list[] = $row;
    }
}

// Cart count (if logged in)
$cart_count = 0;
if (isset($_SESSION['login']) && !empty($_SESSION['id_user'])) {
    $uid = intval($_SESSION['id_user']);
    $cq = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM cart WHERE id_user = $uid");
    if ($cq) {
        $crow = mysqli_fetch_assoc($cq);
        $cart_count = intval($crow['total']);
    }
}
?>
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
        /* Default Theme Variables */
        --theme-color: #A3D8F4;
        --header-active: var(--pink);
    }

    /* Variasi Tema Warna */
    [data-theme="mainan"] {
        --theme-color: #A8D695;
        --header-active: #62C974;
    }

    [data-theme="pakaian"] {
        --theme-color: #F9E58B;
        --header-active: #FBC02D;
    }

    [data-theme="perlengkapan"] {
        --theme-color: #F88A8A;
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
        background-image: url('<?= $base_url ?>assets/images/tbg.png');
        background-size: cover;
        background-attachment: fixed;
        transition: background-color 0.5s ease;
        color: var(--dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ── NAVBAR ── */
    nav {
        background: transparent;
        padding: 12px 10px;
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: none;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-top: 6px solid var(--pink);
        transition: background-color 0.3s ease, box-shadow 0.3s ease, padding 0.3s ease;
    }

    nav.scrolled {
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        backdrop-filter: blur(10px);
        padding: 8px 40px;
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
        height: auto;
        display: block;
    }

    .search-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-container input {
        padding: 10px 15px;
        padding-right: 40px;
        border-radius: 20px;
        border: 2px solid #eee;
        outline: none;
        width: 180px;
        transition: 0.3s;
    }

    .search-container input:focus {
        border-color: var(--pink);
        width: 220px;
    }

    .search-btn {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .nav-links a {
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        padding: 8px 15px;
        border-radius: 20px;
        transition: all 0.3s ease;
        color: var(--dark);
        white-space: nowrap;
        cursor: pointer;
    }

    .nav-links a:hover {
        color: var(--pink);
    }

    /* Styling class active mengikuti tema */
    .nav-links a.active {
        background-color: var(--header-active);
        color: white;
    }

    .login-btn {
        background-color: var(--orange);
        color: white !important;
        padding: 10px 20px !important;
        border-radius: 20px;
    }

    .login-btn:hover {
        background-color: #e67625;
        transform: scale(1.05);
    }

    .cart-badge {
        background: var(--header-active);
        color: #fff;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 700;
        margin-left: 6px;
        font-size: 12px;
    }

    @media(max-width:768px) {
        nav {
            padding: 10px;
        }

        .nav-container {
            gap: 10px;
        }

        .search-container {
            display: none;
        }
    }
</style>

<nav>
    <div class="nav-container">
        <div class="logo">
            <a href="<?= $base_url ?>index.php"><img src="<?= $base_url ?>assets/images/logo.png" alt="Squashy Logo"></a>
        </div>

        <div class="nav-links">
            <a onclick="changeTheme('home', this)" class="<?= $current_kategori == 'all' ? 'active' : '' ?>" href="<?= $base_url ?>customers/kategori.php?kategori=all">Home</a>
            <?php foreach ($kategori_list as $k) : ?>
                <?php $name = $k['nama_kategori']; ?>
                <a onclick="changeTheme('home', this)" href="<?= $base_url ?>customers/kategori.php?kategori=<?= urlencode($name) ?>" class="<?= $current_kategori == $name ? 'active' : '' ?>"><?= htmlspecialchars($name) ?></a>
            <?php endforeach; ?>
            <a href="<?= $base_url ?>customers/keranjang.php" style="display:flex;align-items:center;gap:8px;">🛒 Keranjang <span class="cart-badge"><?= $cart_count ?></span></a>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true) : ?>
                <a href="<?= $base_url ?>customers/profil.php">Profil</a>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true) : ?>
                <a href="<?= $base_url ?>customers/logout.php"
                    class="login-btn"
                    style="background-color: #d32f2f;"
                    onclick="return confirm('Apakah Anda yakin ingin keluar dari akun ini?');">
                    🚪 Logout
                </a>
            <?php else : ?>
                <a href="<?= $base_url ?>customers/login.php" class="login-btn">🔐 Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    function changeTheme(themeName, element) {
        // Update atribut tema di body
        document.body.setAttribute('data-theme', themeName);

        // Update class active pada link navigasi
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => link.classList.remove('active'));
        if (element) element.classList.add('active');

        // Update Judul Halaman (Jika ada elemen id="pageTitle")
        const title = document.getElementById('pageTitle');
        if (title) {
            const titles = {
                'home': 'Produk Pilihan Bunda',
                'mainan': 'Koleksi Mainan Seru',
                'pakaian': 'Pakaian Anak Stylish',
                'perlengkapan': 'Perlengkapan Bayi Lengkap'
            };
            title.innerText = titles[themeName];
        }
    }

    // Efek Header Transparan -> Putih saat scroll
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
    // Jalankan langsung untuk mengantisipasi refresh di posisi ter-scroll
    handleNavbarScroll();
</script>