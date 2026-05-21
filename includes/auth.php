<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Middleware untuk halaman yang butuh login (bebas role apa saja)
function checkLogin()
{
    if (!isset($_SESSION['login'])) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/squashy/index.php';
        header("Location: /squashy/customers/login.php?redirect=" . urlencode($redirect));
        exit;
    }
}

// Middleware khusus halaman Admin
function adminOnly()
{
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        echo "<script>
                alert('Akses Ditolak! Anda bukan Admin.');
                window.location='/squashy/index.php';
              </script>";
        exit;
    }
}

// Middleware khusus halaman Customer
function customerOnly()
{
    checkLogin();
    if ($_SESSION['role'] !== 'customer') {
        header("Location: /squashy/admin/dashboard.php");
        exit;
    }
}
