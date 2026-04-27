<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Konsisten Layout</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-color: #A3D8F4;
            --card-bg: #E1F5FE;
            --accent-color: #FF852D;
            --header-active: #ffffff;
        }

        [data-theme="mainan"] {
            --theme-color: #A8D695;
            --card-bg: #E8F5E9;
            --header-active: #62C974;
        }

        [data-theme="pakaian"] {
            --theme-color: #F9E58B;
            --card-bg: #FFF9C4;
            --header-active: #FBC02D;
        }

        [data-theme="perlengkapan"] {
            --theme-color: #F88A8A;
            --card-bg: #FFEBEE;
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
            /* */
            background-size: cover;
            background-attachment: fixed;
            transition: background-color 0.5s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Tetap Konsisten */
        header {
            display: flex;
            align-items: center;
            padding: 20px 50px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            width: 120px;
            margin-right: 30px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
        }

        nav ul li a {
            padding: 8px 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        nav ul li a.active {
            background-color: var(--header-active);
            color: #000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Layout Kontainer yang Konsisten */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px 120px;
            /* Padding bawah ekstra untuk footer */
        }

        .content-box {
            width: 100%;
            max-width: 1100px;
            background: var(--card-bg);
            /* Berubah warna saja, ukuran tetap */
            border: 8px solid white;
            border-radius: 40px;
            padding: 40px;
            transition: background-color 0.5s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-family: 'Nunito', sans-serif;
            text-align: center;
            font-size: 28px;
            color: #555;
            margin-bottom: 35px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Grid Produk yang Kokoh */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            padding: 20px;
            border-radius: 25px;
            text-align: center;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 250px;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            max-width: 100px;
            margin: 0 auto 15px;
        }

        .product-card h4 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .product-card p {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 16px;
        }

        .btn-buy {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 15px;
            font-size: 12px;
        }

        /* Footer About Us Konsisten */
        .custom-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background-color: white;
            display: flex;
            align-items: center;
            padding: 0 50px;
            z-index: 100;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
        }

        .about-us-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            font-weight: 700;
            margin-left: 20px;
            /* Jarak dari bunga kiri */
        }

        .info-circle {
            border: 2px solid #333;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            font-style: normal;
            font-size: 14px;
        }

        .decor-flower {
            position: absolute;
            bottom: 0;
            height: 100px;
            pointer-events: none;
        }

        .flower-left {
            left: 0;
        }

        .flower-right {
            right: 0;
        }
    </style>
</head>

<body data-theme="home">

    <header>
        <div class="logo"><a href="../index.php"><img src="../assets/images/logo.png" alt="Squashy"></a></div>
        <nav>
            <ul>
                <li><a href="#" onclick="changeTheme('mainan', this)">Mainan</a></li>
                <li><a href="#" onclick="changeTheme('pakaian', this)">Pakaian</a></li>
                <li><a href="#" onclick="changeTheme('perlengkapan', this)">Perlengkapan Bayi</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-wrapper">
        <div class="content-box">
            <h2 class="section-title" id="pageTitle">Produk Pilihan Bunda</h2>

            <div class="product-grid" id="productContainer">
                <div class="product-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/3082/3082060.png" alt="Produk">
                    <div>
                        <h4>Nama Produk</h4>
                        <p>Rp 50.000</p>
                    </div>
                    <button class="btn-buy">BELI SEKARANG</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="custom-footer">
        <img src="../assets/images/decor-left.png" class="decor-flower flower-left" alt="">
        <a href="about.php" class="about-us-link">
            <span class="info-circle">i</span> About Us
        </a>
        <img src="../assets/images/decor-right.png" class="decor-flower flower-right" alt="">
    </footer>

    <script>
        function changeTheme(themeName, element) {
            // Ubah tema pada body agar CSS Variable ter-update secara global
            document.body.setAttribute('data-theme', themeName);

            // Update Navigasi Aktif
            const links = document.querySelectorAll('nav ul li a');
            links.forEach(link => link.classList.remove('active'));
            element.classList.add('active');

            // Update Judul tanpa merusak layout
            const title = document.getElementById('pageTitle');
            const titles = {
                'home': 'Produk Pilihan Bunda',
                'mainan': 'Koleksi Mainan Seru',
                'pakaian': 'Pakaian Anak Stylish',
                'perlengkapan': 'Perlengkapan Bayi Lengkap'
            };
            title.innerText = titles[themeName];
        }
    </script>
</body>

</html>