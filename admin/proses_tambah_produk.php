<?php
session_start();

require_once '../config/app.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kelola_produk.php');
    exit;
}

$nama = isset($_POST['nama_produk']) ? mysqli_real_escape_string($conn, trim($_POST['nama_produk'])) : '';
$id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
$harga = isset($_POST['harga']) ? intval($_POST['harga']) : 0;
$stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
$deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, trim($_POST['deskripsi'])) : null;

if ($nama === '' || $id_kategori <= 0) {
    header('Location: kelola_produk.php?error=missing');
    exit;
}

$sql = "INSERT INTO produk (id_kategori, nama_produk, harga, stok, deskripsi) VALUES ($id_kategori, '" . $nama . "', $harga, $stok, '" . ($deskripsi ?? '') . "')";

if (mysqli_query($conn, $sql)) {
    header('Location: kelola_produk.php?success=1');
    exit;
} else {
    header('Location: kelola_produk.php?error=insert');
    exit;
}
