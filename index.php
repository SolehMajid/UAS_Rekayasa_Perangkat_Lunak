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
            --soft-bg: #BCE7FC; /* Warna langit biru cerah yang hangat */
            --cream-yellow: #FFF9E3;
            --mint-green: #E2FBE9;
            --soft-pink: #FFEAF2;
            --dark: #3A3063;
            --jelly-shadow: rgba(255, 133, 45, 0.4);
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

        /* ── MAIN CONTENT ── */
        .main-wrapper {
            flex: 1;
            padding: 40px 20px 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Banner Cloud Playground */
        .promo-banner-seru {
            background: linear-gradient(135deg, #FFFDFD 0%, #F5FBFD 100%);
            width: 100%;
            max-width: 1100px;
            border-radius: 50px;
            border: 8px solid white;
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(58, 48, 99, 0.08);
            margin-bottom: 50px;
        }

        /* Floating Decors */
        .floating-decor {
            position: absolute;
            pointer-events: none;
            user-select: none;
            z-index: 1;
        }

        .fd-cloud-1 {
            font-size: 45px;
            top: 20px;
            left: 5%;
            animation: floatAnim 6s ease-in-out infinite;
        }

        .fd-cloud-2 {
            font-size: 55px;
            bottom: 30px;
            right: 8%;
            animation: floatAnim 8s ease-in-out infinite 1s;
        }

        .fd-balloon {
            font-size: 48px;
            top: 40px;
            right: 25%;
            animation: floatAnim 7s ease-in-out infinite 0.5s;
        }

        .fd-star-1 {
            font-size: 26px;
            top: 100px;
            left: 25%;
            animation: pulseAnim 4s ease-in-out infinite;
        }

        .fd-star-2 {
            font-size: 32px;
            bottom: 120px;
            left: 45%;
            animation: pulseAnim 3s ease-in-out infinite 0.5s;
        }

        .fd-bear {
            font-size: 40px;
            bottom: 25px;
            left: 3%;
            animation: floatAnim 9s ease-in-out infinite;
        }

        @keyframes floatAnim {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(5deg); }
        }

        @keyframes pulseAnim {
            0%, 100% { transform: scale(1) opacity: 0.6; }
            50% { transform: scale(1.2) opacity: 1; }
        }

        /* Banner Content Layout */
        .banner-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
            align-items: center;
            position: relative;
            z-index: 5;
        }

        .hero-text-seru {
            text-align: left;
        }

        .hero-text-seru h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 44px;
            color: var(--dark);
            margin-bottom: 15px;
            line-height: 1.2;
            text-shadow: 2px 2px 0px var(--yellow), 4px 4px 0px rgba(58, 48, 99, 0.05);
        }

        .hero-text-seru h1 span {
            color: var(--pink);
        }

        .hero-text-seru .subtitle {
            font-size: 16px;
            color: #615E7A;
            font-weight: 700;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Tips Ceria Box */
        .tips-container-box {
            background: var(--cream-yellow);
            border: 3px dashed var(--orange);
            border-radius: 24px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
            position: relative;
            box-shadow: 0 8px 20px rgba(58, 48, 99, 0.02);
            transition: all 0.3s ease;
        }

        .tips-badge {
            background: var(--orange);
            color: white;
            font-size: 11px;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 12px;
            display: inline-block;
            position: absolute;
            top: -14px;
            left: 20px;
            box-shadow: 0 4px 10px rgba(255, 133, 45, 0.2);
        }

        #parenting-tip-text {
            font-size: 14.5px;
            color: var(--dark);
            font-weight: 700;
            margin-top: 5px;
            line-height: 1.6;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .tips-btn-next {
            background: var(--white);
            border: 2px solid var(--orange);
            color: var(--orange);
            font-weight: 800;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 14px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.2s;
            float: right;
        }

        .tips-btn-next:hover {
            background: var(--orange);
            color: var(--white);
            transform: scale(1.05);
        }

        .tips-btn-next:active {
            transform: scale(0.95);
        }

        /* Mascot Bunny Styles */
        .mascot-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            height: 100%;
        }

        .mascot-speech-bubble {
            position: absolute;
            bottom: 160px;
            background: var(--white);
            border: 4px solid var(--dark);
            padding: 12px 18px;
            border-radius: 25px;
            font-weight: 800;
            font-size: 13.5px;
            color: var(--dark);
            box-shadow: 0 10px 25px rgba(58, 48, 99, 0.08);
            z-index: 100;
            width: 210px;
            text-align: center;
            animation: bubbleFloat 4s ease-in-out infinite;
        }

        .mascot-speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 15px 12px 0;
            border-style: solid;
            border-color: var(--dark) transparent;
            display: block;
            width: 0;
        }

        .mascot-speech-bubble::before {
            content: '';
            position: absolute;
            bottom: -9px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 11px 9px 0;
            border-style: solid;
            border-color: var(--white) transparent;
            display: block;
            width: 0;
            z-index: 1;
        }

        @keyframes bubbleFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-6px) scale(1.02); }
        }

        .squashy-bunny-mascot {
            width: 150px;
            height: 150px;
            position: relative;
            cursor: pointer;
            z-index: 10;
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: 50px;
        }

        .squashy-bunny-mascot:hover {
            transform: scale(1.1) rotate(2deg);
        }

        .bunny-ear {
            width: 34px;
            height: 75px;
            background: #FFFFFF;
            border: 5px solid var(--dark);
            border-radius: 50% 50% 0 0;
            position: absolute;
            top: -48px;
            z-index: 1;
            transform-origin: bottom center;
            transition: transform 0.3s ease;
        }

        .bunny-ear::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 48px;
            background: var(--pink);
            border-radius: 50% 50% 0 0;
            top: 12px;
            left: 4px;
        }

        .ear-left {
            left: 28px;
            transform: rotate(-10deg);
        }

        .ear-right {
            right: 28px;
            transform: rotate(10deg);
        }

        .bunny-body {
            width: 124px;
            height: 114px;
            background: #FFFFFF;
            border: 6px solid var(--dark);
            border-radius: 50% 50% 45% 45%;
            position: absolute;
            bottom: 0;
            left: 13px;
            box-shadow: 0 12px 30px rgba(58, 48, 99, 0.15);
            z-index: 2;
            overflow: hidden;
        }

        .bunny-eyes {
            display: flex;
            justify-content: space-between;
            width: 58px;
            position: absolute;
            top: 36px;
            left: 33px;
        }

        .eye {
            width: 14px;
            height: 14px;
            background: var(--dark);
            border-radius: 50%;
            position: relative;
        }

        .pupil {
            width: 5px;
            height: 5px;
            background: #FFF;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
        }

        .bunny-blush {
            width: 18px;
            height: 9px;
            background: rgba(255, 111, 183, 0.5);
            border-radius: 50%;
            position: absolute;
            top: 52px;
        }

        .blush-left { left: 16px; }
        .blush-right { right: 16px; }

        .bunny-nose {
            width: 8px;
            height: 6px;
            background: var(--pink);
            border-radius: 50%;
            position: absolute;
            top: 50px;
            left: 58px;
        }

        .bunny-mouth {
            position: absolute;
            top: 51px;
            left: 55px;
            font-size: 14px;
            font-weight: bold;
            color: var(--dark);
            user-select: none;
            transform: scaleX(1.3);
        }

        .bunny-hands {
            display: flex;
            justify-content: space-between;
            width: 96px;
            position: absolute;
            bottom: 8px;
            left: 14px;
        }

        .hand {
            width: 24px;
            height: 16px;
            background: #FFF;
            border: 4px solid var(--dark);
            border-radius: 50%;
        }

        .squashy-bunny-mascot.jump {
            animation: bunnyJump 0.6s ease;
        }

        @keyframes bunnyJump {
            0%, 100% { transform: scaleY(1) translateY(0); }
            20% { transform: scaleY(0.8) scaleX(1.1) translateY(0); }
            50% { transform: scaleY(1.1) scaleX(0.9) translateY(-40px) rotate(15deg); }
            80% { transform: scaleY(0.9) scaleX(1.05) translateY(0); }
        }

        /* Bouncy Jelly Buttons */
        .btn-jelly-cta {
            background: var(--pink);
            color: white !important;
            border: none;
            padding: 16px 36px;
            border-radius: 30px;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 22px rgba(255, 111, 183, 0.3);
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .btn-jelly-cta:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 28px rgba(255, 111, 183, 0.45);
            background: #FF85C4;
        }

        .btn-jelly-cta:active {
            transform: scale(0.95);
            box-shadow: 0 4px 10px rgba(255, 111, 183, 0.3);
        }

        /* ── KATEGORI BALON CELESTIAL ── */
        .categories-seru {
            display: flex;
            gap: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
        }

        .cat-card-seru {
            background: white;
            width: 180px;
            height: 110px;
            border-radius: 35px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 4px solid white;
            box-shadow: 0 10px 25px rgba(58, 48, 99, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Kategori Color Themes */
        .cat-card-seru:nth-child(1) { background-color: var(--soft-pink); border-color: #FFAAD2; }
        .cat-card-seru:nth-child(2) { background-color: var(--mint-green); border-color: #A7FFC3; }
        .cat-card-seru:nth-child(3) { background-color: var(--cream-yellow); border-color: #FFE69F; }
        .cat-card-seru:nth-child(4) { background-color: #E8F7FE; border-color: #BBE5FB; }

        .cat-card-seru:hover {
            transform: translateY(-8px) rotate(2deg) scale(1.05);
            box-shadow: 0 15px 30px rgba(58, 48, 99, 0.1);
        }

        .cat-card-seru span {
            font-size: 38px;
            margin-bottom: 5px;
            transition: transform 0.4s ease;
            display: inline-block;
        }

        .cat-card-seru:hover span {
            transform: rotate(360deg) scale(1.2);
        }

        /* Judul Section Ceria */
        .section-title-seru {
            font-family: 'Nunito', sans-serif;
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            text-shadow: 1px 1px 0px var(--yellow), 2px 2px 0px rgba(58, 48, 99, 0.02);
        }

        .section-title-seru span {
            color: var(--pink);
        }

        /* ── DUSTY-PINK CLOUD PRODUCTS GRID ── */
        .product-grid-seru {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 35px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card-seru {
            background: white;
            border-radius: 40px;
            padding: 24px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 420px;
            box-shadow: 0 12px 30px rgba(58, 48, 99, 0.05);
            border: 4px dashed var(--soft-pink);
            position: relative;
            cursor: pointer;
        }

        .product-card-seru:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 45px rgba(58, 48, 99, 0.1);
            border-color: var(--pink);
            background: #FFFDFE;
        }

        .product-card-seru::after {
            content: '⭐';
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 16px;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .product-card-seru:hover::after {
            opacity: 1;
            transform: scale(1.2) rotate(15deg);
        }

        .product-img-box-seru {
            background: #FFFBFB;
            border-radius: 30px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden;
            border: 3px solid #FFF5F7;
            transition: 0.3s;
        }

        .product-card-seru:hover .product-img-box-seru {
            border-color: var(--soft-pink);
            transform: scale(0.98);
        }

        .btn-buy-seru {
            background: var(--orange);
            color: white !important;
            border: none;
            padding: 14px;
            border-radius: 25px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            margin-top: 18px;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 15px var(--jelly-shadow);
            letter-spacing: 0.5px;
        }

        .btn-buy-seru:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 133, 45, 0.5);
            background: #FF9240;
        }

        .btn-buy-seru:active {
            transform: scale(0.92);
            box-shadow: 0 3px 8px var(--jelly-shadow);
        }

        /* ── MODAL DETAIL PRODUK (SQUASHY THEME) ── */
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
            .banner-layout {
                grid-template-columns: 1fr;
            }
            .mascot-wrapper {
                margin-top: 20px;
            }
            .squashy-bunny-mascot {
                margin-top: 20px;
            }
            .mascot-speech-bubble {
                position: relative;
                bottom: 0;
                margin-bottom: 10px;
                right: 0;
                width: 100%;
            }
            .mascot-speech-bubble::after, .mascot-speech-bubble::before {
                display: none;
            }
        }

        /* ── SEARCH BAR PREMIUM ── */
        .search-bar-container {
            max-width: 600px;
            margin: 0 auto 40px auto;
            padding: 0 20px;
            width: 100%;
        }

        .search-form-body {
            display: flex;
            gap: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px;
            border-radius: 35px;
            border: 3px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            align-items: center;
        }

        .search-form-body:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--pink);
            background: white;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            padding-left: 15px;
        }

        .search-icon {
            font-size: 18px;
            color: var(--dark);
            opacity: 0.6;
            margin-right: 10px;
        }

        .search-input-wrapper input {
            width: 100%;
            border: none;
            background: transparent;
            padding: 10px 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            outline: none;
        }

        .search-input-wrapper input::placeholder {
            color: #a0a0a0;
            font-weight: 500;
        }

        .search-submit-btn {
            background: var(--pink);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 28px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .search-submit-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            opacity: 0.95;
        }

        @media (max-width: 576px) {
            .search-form-body {
                flex-direction: column;
                border-radius: 20px;
                padding: 10px;
                gap: 8px;
            }
            .search-input-wrapper {
                width: 100%;
                padding-left: 5px;
            }
            .search-submit-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body data-theme="home">

    <?= require_once __DIR__ . "/components/layout/header.php" ?>

    <div class="main-wrapper">
        <section class="promo-banner-seru">
            <!-- Floating Accents -->
            <div class="floating-decor fd-cloud-1">🌤️</div>
            <div class="floating-decor fd-cloud-2">☁️</div>
            <div class="floating-decor fd-balloon">🎈</div>
            <div class="floating-decor fd-star-1">⭐</div>
            <div class="floating-decor fd-star-2">✨</div>
            <div class="floating-decor fd-bear">🧸</div>

            <div class="banner-layout">
                <div class="hero-text-seru">
                    <h1>Dunia Ceria <span>Si Kecil!</span> 🧸🍭</h1>
                    <p class="subtitle">Tempat keajaiban tumbuh! Temukan mainan edukatif yang menyenangkan & pakaian gemas berkualitas premium untuk buah hati tercinta.</p>
                    
                    <!-- Tips Ceria Box (Mengganti Promo Membosankan) -->
                    <div class="tips-container-box">
                        <div class="tips-badge">💡 Tips Ceria Bunda Hari Ini</div>
                        <p id="parenting-tip-text">Ajak si kecil bermain di luar selama 15 menit hari ini untuk melatih motorik kasarnya! 🌳</p>
                        <button class="tips-btn-next" onclick="nextParentingTip()">Tips Lain ✨</button>
                    </div>
                    
                    <a href="#produk" class="btn-jelly-cta">Main & Belanja Sekarang ➔</a>
                </div>

                <!-- Squashy Mascot Bunny -->
                <div class="mascot-wrapper">
                    <!-- Speech bubble for Squashy mascot -->
                    <div class="mascot-speech-bubble" id="mascotBubble">
                        Halo Bunda! Yuk, main bareng Squashy! Pencet aku dong! 🐰✨
                    </div>
                    <div class="squashy-bunny-mascot" id="squashyBunny" onclick="triggerMascotFun()">
                        <!-- CSS Bunny Shapes -->
                        <div class="bunny-ear ear-left"></div>
                        <div class="bunny-ear ear-right"></div>
                        <div class="bunny-body">
                            <div class="bunny-eyes">
                                <div class="eye eye-left"><div class="pupil"></div></div>
                                <div class="eye eye-right"><div class="pupil"></div></div>
                            </div>
                            <div class="bunny-blush blush-left"></div>
                            <div class="bunny-blush blush-right"></div>
                            <div class="bunny-nose"></div>
                            <div class="bunny-mouth">w</div>
                            <div class="bunny-hands">
                                <div class="hand hand-left"></div>
                                <div class="hand hand-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="categories-seru">
            <a href="<?= $base_url ?>customers/kategori.php?kategori=Pakaian" class="cat-card-seru"><span>👕</span>Pakaian Anak</a>
            <a href="<?= $base_url ?>customers/kategori.php?kategori=Mainan" class="cat-card-seru"><span>🚂</span>Mainan Seru</a>
            <a href="<?= $base_url ?>customers/kategori.php?kategori=Perlengkapan" class="cat-card-seru"><span>🍼</span>Perlengkapan</a>
            <a href="<?= $base_url ?>customers/kategori.php?kategori=all" class="cat-card-seru"><span>🎁</span>Promo Spesial</a>
        </div>

        <!-- SEARCH BAR -->
        <div class="search-bar-container">
            <form action="customers/kategori.php" method="GET" class="search-form-body">
                <input type="hidden" name="kategori" value="all">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="keyword" placeholder="Cari mainan atau baju anak lucu...">
                </div>
                <button type="submit" class="search-submit-btn">Cari</button>
            </form>
        </div>

        <h2 class="section-title-seru" id="produk">Produk <span>Terfavorit</span> Pilihan Bunda 🌟</h2>

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
                            <h4 style="margin-bottom:8px; font-weight: 800; font-size:16px; color: var(--dark);"><?= htmlspecialchars($product['nama_produk']); ?></h4>
                            <p style="color:var(--pink); font-weight:900; font-size:17px;">Rp <?= number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p style="font-size:12.5px; color:#777; margin-top:6px; font-weight:700;">🔥 Terjual <?= number_format($product['total_sold'], 0, ',', '.'); ?> pcs</p>
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
        // Parenting Tips Switching
        const parentingTips = [
            "Ajak si kecil bermain di luar selama 15 menit hari ini untuk melatih motorik kasarnya! 🌳",
            "Membaca dongeng sebelum tidur meningkatkan imajinasi si kecil secara luar biasa! 📚✨",
            "Pura-pura jadi koki bersama si kecil melatih kreativitas dan kerja sama lho, Bun! 🍳",
            "Puji usaha si kecil, bukan hanya hasil akhir, untuk melatih growth mindset. 🌟",
            "Bermain cilukba membantu si kecil memahami konsep kepermanenan objek secara menyenangkan. 🙈",
            "Batasi screen time si kecil dan ganti dengan menggambar atau menyusun balok lego bersama! 🎨",
            "Minum susu hangat dan memeluk si kecil sebelum tidur membuatnya merasa dicintai & aman. 🍼💕"
        ];

        let currentTipIndex = 0;

        function nextParentingTip() {
            const tipTextEl = document.getElementById('parenting-tip-text');
            
            // Add fade-out effect
            tipTextEl.style.opacity = 0;
            tipTextEl.style.transform = 'translateY(5px)';
            
            setTimeout(() => {
                currentTipIndex = (currentTipIndex + 1) % parentingTips.length;
                tipTextEl.innerText = parentingTips[currentTipIndex];
                
                // Fade-in
                tipTextEl.style.opacity = 1;
                tipTextEl.style.transform = 'translateY(0)';
            }, 200);
        }

        // Mascot Squashy click and jump behavior
        function triggerMascotFun() {
            const bunny = document.getElementById('squashyBunny');
            const bubble = document.getElementById('mascotBubble');
            
            // Play jump animation
            bunny.classList.remove('jump');
            void bunny.offsetWidth; // Trigger reflow
            bunny.classList.add('jump');
            
            // Change speech bubble text randomly
            const messages = [
                "Yeeay! Kakak pencet aku! Lompat! 🐰💖",
                "Baju Squashy lucu banget loh Bun! 👗",
                "Yuk mainan bareng Squashy, asik! 🚂⭐",
                "Peralatan bayi di sini lengkap banget! 🍼",
                "Squashy sayang Bunda & Adek! 💕",
                "Wuiih! Banyak bintang bertebaran! ✨",
                "Ada promo spesial tersembunyi loh! 🎁"
            ];
            const randomMsg = messages[Math.floor(Math.random() * messages.length)];
            bubble.innerText = randomMsg;
            
            // Generate star particle burst
            const rect = bunny.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            createBurst(x, y);
        }

        function createBurst(x, y) {
            const emojis = ['✨', '⭐', '🎈', '🍬', '🎨', '🎉'];
            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.innerText = emojis[Math.floor(Math.random() * emojis.length)];
                
                const angle = Math.random() * Math.PI * 2;
                const speed = 2 + Math.random() * 4;
                const vx = Math.cos(angle) * speed;
                const vy = Math.sin(angle) * speed - 1.5; // Upward bias
                
                particle.style.left = (x + window.scrollX) + 'px';
                particle.style.top = (y + window.scrollY) + 'px';
                particle.style.position = 'absolute';
                particle.style.fontSize = '22px';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '10000';
                particle.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
                
                document.body.appendChild(particle);
                
                void particle.offsetWidth; // Force reflow
                
                particle.style.transform = `translate(${vx * 25}px, ${vy * 25}px) scale(0)`;
                particle.style.opacity = '0';
                
                setTimeout(() => {
                    particle.remove();
                }, 600);
            }
        }

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

                // Fetch detail
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