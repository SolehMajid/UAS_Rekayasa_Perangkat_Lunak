<?php
require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Middleware untuk halaman yang butuh login (bebas role apa saja)
function checkLogin()
{
    global $base_url;
    if (!isset($_SESSION['login'])) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ($base_url . 'index.php');
        header("Location: " . $base_url . "customers/login.php?redirect=" . urlencode($redirect));
        exit;
    }
}

// Middleware khusus halaman Admin
function adminOnly()
{
    global $base_url;
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        echo "<script>
                alert('Akses Ditolak! Anda bukan Admin.');
                window.location='" . $base_url . "index.php';
              </script>";
        exit;
    }
}

// Middleware khusus halaman Customer
function customerOnly()
{
    global $base_url;
    checkLogin();
    if ($_SESSION['role'] !== 'customer') {
        header("Location: " . $base_url . "admin/admin_dashboard.php");
        exit;
    }
}
