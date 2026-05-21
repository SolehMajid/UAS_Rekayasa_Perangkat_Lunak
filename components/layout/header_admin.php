<?php
require_once __DIR__ . '/../../config/app.php';

if (!isset($active_page)) {
    $active_page = '';
}
?>

<style>
    :root {
        --mint-bg: #8DE3C7;
        --cream-bg: #FFEAA7;
        --dark-purple: #3A3063;
        --white: #FFFFFF;
    }

    body {
        margin: 0;
        font-family: 'Quicksand', sans-serif;
        background-color: var(--cream-bg);
        color: var(--dark-purple);
        display: flex;
    }

    .sidebar {
        width: 260px;
        background-color: var(--mint-bg);
        padding: 30px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
    }

    .sidebar .logo-box {
        width: 160px;
        text-align: center;
        margin-bottom: 40px;
    }

    .sidebar .logo-box img {
        width: 100%;
        display: block;
    }

    .menu-list {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 0 15px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 14px 20px;
        text-decoration: none;
        color: var(--dark-purple);
        font-weight: 700;
        font-size: 15px;
        border-radius: 15px;
        transition: all 0.2s;
    }

    .menu-item.active {
        background-color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .menu-item:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.4);
    }

    .main-content {
        margin-left: 260px;
        flex: 1;
        padding: 40px;
        min-height: 100vh;
    }
</style>

<div class="sidebar">

    <div class="logo-box">
        <a href="<?= $base_url ?>index.php"><img src="<?= $base_url ?>assets/images/logo.png" alt="Squashy Logo"></a>
    </div>

    <div class="menu-list">

        <a href="admin_dashboard.php"
            class="menu-item <?= $active_page == 'dashboard' ? 'active' : '' ?>">
            📈 Dashboard
        </a>

        <a href="kelola_produk.php"
            class="menu-item <?= $active_page == 'produk' ? 'active' : '' ?>">
            📦 Kelola Produk
        </a>

        <a href="kelola_pesanan.php"
            class="menu-item <?= $active_page == 'pesanan' ? 'active' : '' ?>">
            📋 Pesanan
        </a>
        <a href="kelola_pembayaran.php"
            class="menu-item <?= $active_page == 'status' ? 'active' : '' ?>">
            💳 Kelola Pembayaran
        </a>
        <a href="#"
            class="menu-item <?= $active_page == 'pelanggan' ? 'active' : '' ?>">
            👥 Pelanggan
        </a>

        <a href="#"
            class="menu-item <?= $active_page == 'promo' ? 'active' : '' ?>">
            🎟️ Promo
        </a>

        <a href="../customers/logout.php" class="menu-item">
            🚪 Logout
        </a>

    </div>

</div>