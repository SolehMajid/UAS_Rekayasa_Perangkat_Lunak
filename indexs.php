<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Squashy – Toko Anak Seru!</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --pink: #FF6FB7;
            --yellow: #FFD93D;
            --blue: #4DC8F0;
            --green: #6EDB8F;
            --orange: #FF9D3D;
            --purple: #B983FF;
            --white: #FFFFFF;
            --soft: #FFF5FB;
            --dark: #3A3063;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--soft);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        nav {
            background: var(--white);
            padding: 0 40px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(255, 111, 183, .15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Fredoka One', cursive;
            font-size: 28px;
            color: var(--pink);
            text-decoration: none;
        }

        .logo span.star {
            color: var(--yellow);
            font-size: 22px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 7px 14px;
            border-radius: 30px;
            transition: all .2s;
        }

        .nav-links a.login {
            color: var(--dark);
            border: 2px solid #eee;
        }

        .nav-links a.daftar {
            color: var(--white);
            background: var(--pink);
        }

        .nav-links a.chat {
            color: var(--blue);
            border: 2px solid var(--blue);
        }

        .nav-links a.cart {
            color: var(--white);
            background: var(--orange);
        }

        .nav-links a:hover {
            transform: scale(1.06);
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #F3F3F9;
            border-radius: 30px;
            padding: 6px 16px;
            gap: 8px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
            outline: none;
            width: 160px;
            color: var(--dark);
        }

        .search-bar button {
            border: none;
            background: var(--pink);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #FFD6EC 0%, #C7EEFF 60%, #D8F5E4 100%);
            padding: 60px 60px 40px;
            position: relative;
            overflow: hidden;
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Rainbow arc */
        .hero::before {
            content: '';
            position: absolute;
            width: 900px;
            height: 900px;
            border-radius: 50%;
            border: 55px solid transparent;
            border-top: 55px solid #FF6FB7;
            border-right: 55px solid #FFD93D;
            top: -500px;
            left: 50%;
            transform: translateX(-50%);
            opacity: .45;
        }

        /* Cloud SVGs via pseudo */
        .cloud {
            position: absolute;
            background: white;
            border-radius: 50px;
            opacity: .7;
        }

        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background: white;
            border-radius: 50%;
        }

        .c1 {
            width: 120px;
            height: 40px;
            top: 22px;
            left: 5%;
        }

        .c1::before {
            width: 60px;
            height: 60px;
            top: -30px;
            left: 10px;
        }

        .c1::after {
            width: 45px;
            height: 45px;
            top: -20px;
            left: 50px;
        }

        .c2 {
            width: 90px;
            height: 30px;
            top: 40px;
            right: 8%;
        }

        .c2::before {
            width: 45px;
            height: 45px;
            top: -22px;
            left: 5px;
        }

        .c2::after {
            width: 35px;
            height: 35px;
            top: -15px;
            left: 40px;
        }

        .hero-content {
            text-align: center;
            z-index: 2;
            position: relative;
        }

        .hero-content h1 {
            font-family: 'Fredoka One', cursive;
            font-size: 52px;
            color: var(--dark);
            line-height: 1.1;
            text-shadow: 3px 3px 0 rgba(255, 111, 183, .25);
            animation: popIn .6s cubic-bezier(.26, 1.6, .52, .99) both;
        }

        .hero-content h1 span {
            color: var(--pink);
        }

        .hero-content .subtitle {
            font-size: 20px;
            font-weight: 700;
            color: #666;
            margin: 6px 0 18px;
            animation: popIn .7s .08s cubic-bezier(.26, 1.6, .52, .99) both;
        }

        .discount-badge {
            display: inline-block;
            background: var(--white);
            border: 4px solid var(--yellow);
            border-radius: 20px;
            padding: 12px 28px;
            margin-bottom: 22px;
            font-family: 'Fredoka One', cursive;
            animation: popIn .7s .16s cubic-bezier(.26, 1.6, .52, .99) both;
            box-shadow: 0 8px 24px rgba(255, 217, 61, .3);
        }

        .discount-badge .up-to {
            font-size: 16px;
            color: #aaa;
            display: block;
        }

        .discount-badge .pct {
            font-size: 64px;
            color: var(--pink);
            line-height: 1;
        }

        .discount-badge .off {
            font-size: 20px;
            color: var(--orange);
            font-weight: 800;
        }

        .btn-shop {
            display: inline-block;
            background: linear-gradient(135deg, var(--pink), var(--orange));
            color: white;
            font-family: 'Fredoka One', cursive;
            font-size: 20px;
            padding: 14px 36px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(255, 111, 183, .4);
            transition: transform .2s, box-shadow .2s;
            animation: popIn .7s .24s cubic-bezier(.26, 1.6, .52, .99) both;
        }

        .btn-shop:hover {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 12px 32px rgba(255, 111, 183, .55);
        }

        .btn-shop::after {
            content: ' ›';
        }

        /* balloons */
        .balloon {
            position: absolute;
            font-size: 34px;
            animation: float 3s ease-in-out infinite alternate;
        }

        .b1 {
            bottom: 20px;
            left: 10%;
            animation-delay: 0s;
        }

        .b2 {
            top: 30px;
            left: 18%;
            animation-delay: .4s;
        }

        .b3 {
            bottom: 40px;
            right: 12%;
            animation-delay: .8s;
        }

        .b4 {
            top: 50px;
            right: 22%;
            animation-delay: .2s;
        }

        @keyframes float {
            from {
                transform: translateY(0)
            }

            to {
                transform: translateY(-14px)
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(.7)
            }

            to {
                opacity: 1;
                transform: scale(1)
            }
        }

        /* ── CATEGORY CARDS ── */
        .categories {
            display: flex;
            gap: 16px;
            padding: 28px 40px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 160px;
            height: 110px;
            border-radius: 22px;
            font-family: 'Fredoka One', cursive;
            font-size: 18px;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .1);
            color: white;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .cat-card::after {
            content: '';
            position: absolute;
            top: -40%;
            left: -40%;
            width: 180%;
            height: 180%;
            background: radial-gradient(circle, rgba(255, 255, 255, .18), transparent 65%);
        }

        .cat-card .emoji {
            font-size: 36px;
        }

        .cat-card:hover {
            transform: translateY(-6px) scale(1.04);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
        }

        .cat-card.pakaian {
            background: linear-gradient(135deg, #FF6FB7, #FF9D3D);
        }

        .cat-card.mainan {
            background: linear-gradient(135deg, #4DC8F0, #6EDB8F);
        }

        .cat-card.bayi {
            background: linear-gradient(135deg, #B983FF, #4DC8F0);
        }

        .cat-card.promo {
            background: linear-gradient(135deg, #FFD93D, #FF6FB7);
        }

        /* ── SECTION TITLE ── */
        .section-title {
            text-align: center;
            font-family: 'Fredoka One', cursive;
            font-size: 30px;
            color: var(--dark);
            margin: 36px 0 6px;
        }

        .section-title span {
            color: var(--pink);
        }

        .section-sub {
            text-align: center;
            font-size: 14px;
            color: #999;
            margin-bottom: 20px;
        }

        /* ── PRODUCT GRID ── */
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 40px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
            transition: transform .22s, box-shadow .22s;
            cursor: pointer;
            animation: popIn .5s ease both;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .14);
        }

        .product-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: linear-gradient(135deg, #FFEEF8, #E8F7FF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }

        .badge-diskon {
            background: var(--pink);
            color: white;
            font-family: 'Fredoka One', cursive;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            margin: 12px 12px 0;
            display: inline-block;
        }

        .product-name {
            font-weight: 800;
            font-size: 14px;
            padding: 6px 12px 2px;
            color: var(--dark);
        }

        .product-price {
            padding: 0 12px 4px;
            font-family: 'Fredoka One', cursive;
            font-size: 18px;
            color: var(--pink);
        }

        .product-price del {
            font-size: 13px;
            color: #bbb;
            margin-left: 6px;
        }

        .product-stars {
            padding: 0 12px;
            font-size: 13px;
            color: var(--yellow);
        }

        .btn-add {
            display: block;
            margin: 10px 12px 14px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            color: white;
            text-align: center;
            padding: 9px;
            border-radius: 40px;
            font-family: 'Fredoka One', cursive;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
        }

        .btn-add:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 18px rgba(77, 200, 240, .4);
        }

        /* ── PROMO BANNER ── */
        .promo-banner {
            background: linear-gradient(135deg, var(--yellow), var(--orange));
            margin: 10px 40px 40px;
            border-radius: 24px;
            padding: 36px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(255, 157, 61, .3);
        }

        .promo-banner .pb-text h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 32px;
            color: white;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, .1);
        }

        .promo-banner .pb-text p {
            color: rgba(255, 255, 255, .85);
            font-weight: 600;
            margin-top: 4px;
        }

        .btn-promo {
            background: white;
            color: var(--orange);
            font-family: 'Fredoka One', cursive;
            font-size: 18px;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .12);
            transition: transform .2s;
        }

        .btn-promo:hover {
            transform: scale(1.05);
        }

        /* ── ABOUT SECTION ── */
        .about {
            background: white;
            margin: 0 40px 40px;
            border-radius: 24px;
            padding: 48px 48px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
        }

        .about h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 32px;
            color: var(--pink);
            margin-bottom: 12px;
        }

        .about p {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            max-width: 750px;
        }

        .about-features {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .feat {
            flex: 1;
            min-width: 180px;
            background: var(--soft);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
        }

        .feat .feat-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .feat h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 16px;
            color: var(--dark);
        }

        .feat p {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--dark);
            color: rgba(255, 255, 255, .75);
            text-align: center;
            padding: 24px;
            font-size: 13px;
        }

        footer a {
            color: var(--yellow);
            text-decoration: none;
        }

        /* ── FLOATING CART NOTIF ── */
        #toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--green);
            color: white;
            padding: 12px 22px;
            border-radius: 50px;
            font-family: 'Fredoka One', cursive;
            font-size: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
            opacity: 0;
            transform: translateY(20px);
            transition: all .3s;
            pointer-events: none;
            z-index: 999;
        }

        #toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── MODAL CHAT ── */
        .chat-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 200;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 20px;
        }

        .chat-modal.open {
            display: flex;
        }

        .chat-box {
            background: white;
            border-radius: 24px;
            width: 320px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            animation: popIn .3s ease both;
        }

        .chat-head {
            background: linear-gradient(135deg, var(--blue), var(--purple));
            color: white;
            padding: 16px 20px;
            font-family: 'Fredoka One', cursive;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-body {
            padding: 16px;
            height: 220px;
            overflow-y: auto;
        }

        .chat-msg {
            background: var(--soft);
            border-radius: 16px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 10px;
            max-width: 80%;
        }

        .chat-msg.me {
            background: linear-gradient(135deg, var(--pink), var(--orange));
            color: white;
            margin-left: auto;
        }

        .chat-input-row {
            display: flex;
            padding: 12px;
            gap: 8px;
            border-top: 1px solid #eee;
        }

        .chat-input-row input {
            flex: 1;
            border: 2px solid #eee;
            border-radius: 30px;
            padding: 8px 14px;
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
            outline: none;
        }

        .chat-input-row button {
            background: var(--pink);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <a href="../squashy/" class="logo">
            <span class="star"><img src="/assets/images/logo.png" alt=""></span> Squashy
        </a>
        <div class="nav-links">
            <a href="../squashy/customers/login.php" class="login">🔐 Login</a>
            <a href="#" class="chat" onclick="openChat(event)">💬 Chat</a>
            <a href="#" class="cart">🛒 Keranjang</a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Cari produk anak..." id="searchInput" />
            <button onclick="doSearch()">🔍</button>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="cloud c1"></div>
        <div class="cloud c2"></div>
        <span class="balloon b1">🎈</span>
        <span class="balloon b2">🎀</span>
        <span class="balloon b3">🎁</span>
        <span class="balloon b4">🌟</span>

        <div class="hero-content">
            <h1><span>Diskon Seru!</span></h1>
            <p class="subtitle">Ayo Belanja Hemat di Squashy!</p>
            <div class="discount-badge">
                <span class="up-to">Up to</span>
                <span class="pct">50%</span>
                <span class="off">OFF</span>
            </div>
            <br />
            <a href="#produk" class="btn-shop">Belanja Sekarang</a>
        </div>
    </section>

    <!-- CATEGORIES -->
    <div class="categories">
        <a href="../squashy/customers/kategori.php" class="cat-card pakaian">
            <span class="emoji">👕</span>Pakaian Anak
        </a>
        <a href="#" class="cat-card mainan">
            <span class="emoji">🚂</span>Mainan
        </a>
        <a href="#" class="cat-card bayi">
            <span class="emoji">🍼</span>Perlengkapan Bayi
        </a>
        <a href="#" class="cat-card promo">
            <span class="emoji">🎁</span>Promo Spesial
        </a>
    </div>

    <!-- PRODUCTS -->
    <h2 class="section-title" id="produk">Produk <span>Pilihan</span> Kami</h2>
    <p class="section-sub">Barang berkualitas, harga ramah di kantong orang tua 💕</p>



    <!-- PROMO BANNER -->
    <div class="promo-banner">
        <div class="pb-text">
            <h2>🎉 Flash Sale Hari Ini!</h2>
            <p>Dapatkan gratis ongkir untuk pembelian di atas Rp 100.000</p>
        </div>
        <a href="#" class="btn-promo">Klaim Sekarang →</a>
    </div>

    <!-- ABOUT / KETERANGAN WEB -->
    <section class="about">
        <h2>🧸 Tentang Squashy</h2>
        <p>
            <strong>Squashy</strong> adalah toko online terpercaya yang menghadirkan produk-produk terbaik untuk anak-anak — mulai dari pakaian lucu, mainan edukatif, hingga perlengkapan bayi pilihan.
            Kami percaya bahwa setiap anak berhak mendapatkan yang terbaik dengan harga yang terjangkau.
            Semua produk kami telah melalui seleksi ketat untuk memastikan keamanan dan kenyamanan si kecil.
        </p>
        <div class="about-features">
            <div class="feat">
                <div class="feat-icon">🛡️</div>
                <h3>Aman & Terpercaya</h3>
                <p>Semua produk telah bersertifikasi SNI dan aman untuk anak.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">🚚</div>
                <h3>Pengiriman Cepat</h3>
                <p>Kami bekerja sama dengan jasa kurir terbaik di seluruh Indonesia.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">💬</div>
                <h3>CS Ramah 24/7</h3>
                <p>Tim kami siap membantu kamu kapan saja dan di mana saja.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">🔄</div>
                <h3>Garansi Return</h3>
                <p>Tidak puas? Kembalikan dalam 7 hari, uang kembali!</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2025 <strong>Squashy</strong> – Toko Anak Seru 🌈 | <a href="#">Kebijakan Privasi</a> | <a href="#">Syarat & Ketentuan</a></p>
        <p style="margin-top:6px;">Dibuat dengan 💖 untuk si kecil tersayang</p>
    </footer>

    <!-- TOAST -->
    <div id="toast">🛒 Produk ditambahkan!</div>

    <!-- CHAT MODAL -->
    <div class="chat-modal" id="chatModal">
        <div class="chat-box">
            <div class="chat-head">
                💬 Chat CS Squashy
                <span onclick="closeChat()" style="cursor:pointer;font-size:20px;">✕</span>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="chat-msg">Halo! 👋 Selamat datang di Squashy! Ada yang bisa kami bantu?</div>
            </div>
            <div class="chat-input-row">
                <input type="text" id="chatInput" placeholder="Ketik pesan..." onkeydown="if(event.key==='Enter')sendChat()" />
                <button onclick="sendChat()">➤</button>
            </div>
        </div>
    </div>

    <script>
        // ── Product Data ──
        const products = [{
                name: "Baju Lucu Anak Perempuan",
                price: 75000,
                ori: 120000,
                emoji: "👗",
                stars: 5,
                disc: "37%"
            },
            {
                name: "Set Mainan Balok Warna",
                price: 89000,
                ori: 130000,
                emoji: "🧩",
                stars: 4,
                disc: "31%"
            },
            {
                name: "Sepatu Anak Anti Selip",
                price: 95000,
                ori: 150000,
                emoji: "👟",
                stars: 5,
                disc: "36%"
            },
            {
                name: "Boneka Fluffy Kelinci",
                price: 55000,
                ori: 80000,
                emoji: "🐰",
                stars: 4,
                disc: "31%"
            },
            {
                name: "Celana Jogger Anak",
                price: 45000,
                ori: 70000,
                emoji: "👖",
                stars: 4,
                disc: "35%"
            },
            {
                name: "Mainan Mobil Remote",
                price: 120000,
                ori: 200000,
                emoji: "🚗",
                stars: 5,
                disc: "40%"
            },
            {
                name: "Tas Ransel Anak SD",
                price: 85000,
                ori: 130000,
                emoji: "🎒",
                stars: 4,
                disc: "34%"
            },
            {
                name: "Botol Minum Lucu",
                price: 35000,
                ori: 55000,
                emoji: "🍼",
                stars: 5,
                disc: "36%"
            },
        ];

        const fmt = n => "Rp " + n.toLocaleString('id-ID');

        function renderProducts(data) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';
            data.forEach((p, i) => {
                const stars = '⭐'.repeat(p.stars);
                grid.innerHTML += `
    <div class="product-card" style="animation-delay:${i*0.07}s">
      <div class="product-img">${p.emoji}</div>
      <span class="badge-diskon">-${p.disc}</span>
      <div class="product-name">${p.name}</div>
      <div class="product-price">${fmt(p.price)} <del>${fmt(p.ori)}</del></div>
      <div class="product-stars">${stars}</div>
      <button class="btn-add" onclick="addToCart()">+ Keranjang</button>
    </div>`;
            });
        }
        renderProducts(products);

        // ── Search ──
        function doSearch() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const filtered = products.filter(p => p.name.toLowerCase().includes(q));
            renderProducts(filtered.length ? filtered : products);
            document.getElementById('produk').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // ── Cart Toast ──
        function addToCart() {
            const t = document.getElementById('toast');
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2000);
        }

        // ── Chat ──
        function openChat(e) {
            e.preventDefault();
            document.getElementById('chatModal').classList.add('open');
        }

        function closeChat() {
            document.getElementById('chatModal').classList.remove('open');
        }
        const autoReplies = [
            "Terima kasih! Tim kami akan segera membantu ya 😊",
            "Tentu! Produk kami aman dan berkualitas untuk si kecil 🌟",
            "Kami menyediakan gratis ongkir untuk pembelian di atas Rp 100.000 🚚",
            "Bisa dicek di halaman Promo Spesial ya! 🎁",
        ];
        let replyIdx = 0;

        function sendChat() {
            const inp = document.getElementById('chatInput');
            const msg = inp.value.trim();
            if (!msg) return;
            const body = document.getElementById('chatBody');
            body.innerHTML += `<div class="chat-msg me">${msg}</div>`;
            inp.value = '';
            setTimeout(() => {
                body.innerHTML += `<div class="chat-msg">${autoReplies[replyIdx % autoReplies.length]}</div>`;
                replyIdx++;
                body.scrollTop = body.scrollHeight;
            }, 800);
            body.scrollTop = body.scrollHeight;
        }

        // Close modal on backdrop click
        document.getElementById('chatModal').addEventListener('click', function(e) {
            if (e.target === this) closeChat();
        });
    </script>
</body>

</html>