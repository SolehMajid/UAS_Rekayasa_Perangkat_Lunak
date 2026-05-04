<?php
require_once __DIR__ . '/../../config/app.php'; ?>
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
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 0 40px;
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
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
        width: 110px;
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
</style>

<nav>
    <div class="nav-container">
        <div class="logo">
            <a href="<?= $base_url ?>index.php"><img src="<?= $base_url ?>assets/images/logo.png" alt="Squashy Logo"></a>
        </div>

        <div class="nav-links">
            <a onclick="changeTheme('home', this)" class="active" href="<?= $base_url ?>index.php">Home</a>
            <a onclick="changeTheme('pakaian', this)">Pakaian</a>
            <a onclick="changeTheme('mainan', this)">Mainan</a>
            <a onclick="changeTheme('perlengkapan', this)">Perlengkapan</a>
        </div>

        <div class="nav-links">
            <?php
            // Cek apakah session sudah dimulai (pastikan session_start() ada di file utama)
            if (isset($_SESSION['login']) && $_SESSION['login'] === true) :
            ?>
                <a href="<?= $base_url ?>customers/logout.php" class="login-btn" style="background-color: #d32f2f;">🚪 Logout</a>
            <?php else : ?>
                <a href="<?= $base_url ?>customers/login.php" class="login-btn">🔐 Login</a>
            <?php endif; ?>
        </div>

        <div class="search-container">
            <input type="text" placeholder="Cari baju lucu...">
            <button class="search-btn">🔍</button>
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
        element.classList.add('active');

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
</script>