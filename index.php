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
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1100px;
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
            min-height: 350px;
        }

        .product-card-seru:hover {
            transform: translateY(-10px);
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
        }

        .btn-buy-seru {
            background: var(--orange);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 15px;
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
    </style>
</head>

<body data-theme="home">

    <nav>
        <div class="logo">
            <img src="assets/images/logo.png" alt="Squashy Logo">
        </div>
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="../squashy/customers/kategori.php">Pakaian</a>
            <a href="../squashy/customers/kategori.php">Mainan</a>
            <a href="../squashy/customers/kategori.php">Perlengkapan</a>
            <a href="../squashy/customers/login.php">🔐 Login</a>
        </div>
    </nav>

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
            <a href="#" class="cat-card-seru"><span>👕</span>Pakaian Anak</a>
            <a href="#" class="cat-card-seru"><span>🚂</span>Mainan Seru</a>
            <a href="#" class="cat-card-seru"><span>🍼</span>Perlengkapan</a>
            <a href="#" class="cat-card-seru"><span>🎁</span>Promo Spesial</a>
        </div>

        <h2 class="section-title-seru">Produk <span>Pilihan</span> Bunda</h2>

        <div class="product-grid-seru" id="productGridSeru">
        </div>
    </div>

    <footer class="custom-footer-seru">

        <div class="footer-left-content-seru">
            <a href="about.php" class="about-us-link-seru">
                <span class="info-circle-seru">i</span> About Us
            </a>
        </div>

    </footer>

    <script>
        const productsSeru = [{
                name: "Baju Lucu Anak",
                price: 75000,
                emoji: "👗"
            },
            {
                name: "Mainan Edukasi",
                price: 89000,
                emoji: "🧩"
            },
            {
                name: "Sepatu Nyaman",
                price: 95000,
                emoji: "👟"
            },
            {
                name: "Botol Minum",
                price: 35000,
                emoji: "🍼"
            }
        ];

        const gridSeru = document.getElementById('productGridSeru');
        productsSeru.forEach(p => {
            gridSeru.innerHTML += `
                <div class="product-card-seru">
                    <div class="product-img-box-seru">${p.emoji}</div>
                    <div>
                        <h4 style="margin-bottom:5px">${p.name}</h4>
                        <p style="color:var(--pink); font-weight:800">Rp ${p.price.toLocaleString('id-ID')}</p>
                    </div>
                    <button class="btn-buy-seru">BELI SEKARANG</button>
                </div>
            `;
        });
    </script>
</body>

</html>