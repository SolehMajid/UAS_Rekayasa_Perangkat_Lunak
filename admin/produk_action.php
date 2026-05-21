<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

// Simple admin check (optional) - adjust as needed
// if (!isset($_SESSION['admin_logged_in'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$uploadDir = __DIR__ . '/../assets/images_sq';
$uploadUrlBase = 'assets/images_sq/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function upload_product_image($file, $uploadDir, $uploadUrlBase, $allowed_extensions)
{
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);
    if (strpos($mimeType, 'image/') !== 0) {
        return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions, true)) {
        return null;
    }

    $filename = uniqid('prod_', true) . '.' . $extension;
    $target = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $uploadUrlBase . $filename;
    }

    return null;
}

if ($action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }
    $id = mysqli_real_escape_string($conn, $id);
    $q = "DELETE FROM produk WHERE id_produk = $id LIMIT 1";
    if (mysqli_query($conn, $q)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($action === 'get') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }
    $id = mysqli_real_escape_string($conn, $id);
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not found']);
    }
    exit;
}

if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }
    $nama = isset($_POST['nama_produk']) ? mysqli_real_escape_string($conn, trim($_POST['nama_produk'])) : '';
    $id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
    $harga = isset($_POST['harga']) ? intval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, trim($_POST['deskripsi'])) : '';

    if ($nama === '' || $id_kategori <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing fields']);
        exit;
    }

    $foto_path = null;
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $file = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];
            $saved = upload_product_image($file, $uploadDir, $uploadUrlBase, $allowed_extensions);
            if ($saved !== null) {
                $foto_path = $saved;
                break;
            }
        }
    }

    $columns = 'id_kategori, nama_produk, harga, stok, deskripsi';
    $values = "$id_kategori, '" . $nama . "', $harga, $stok, '" . $deskripsi . "'";
    if ($foto_path) {
        $columns .= ', foto';
        $values .= ", '" . mysqli_real_escape_string($conn, $foto_path) . "'";
    }

    $sql = "INSERT INTO produk ($columns) VALUES ($values)";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($action === 'update') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }
    $id = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
    $nama = isset($_POST['nama_produk']) ? mysqli_real_escape_string($conn, trim($_POST['nama_produk'])) : '';
    $id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
    $harga = isset($_POST['harga']) ? intval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, trim($_POST['deskripsi'])) : '';

    if ($id <= 0 || $nama === '' || $id_kategori <= 0) {
        echo json_encode(['success' => false, 'message' => 'Missing fields']);
        exit;
    }

    $id = mysqli_real_escape_string($conn, $id);
    $foto_path = null;
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $file = [
                'name' => $_FILES['images']['name'][$i],
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];
            $saved = upload_product_image($file, $uploadDir, $uploadUrlBase, $allowed_extensions);
            if ($saved !== null) {
                $foto_path = $saved;
                break;
            }
        }
    }

    $foto_sql = '';
    if ($foto_path) {
        $foto_sql = ", foto = '" . mysqli_real_escape_string($conn, $foto_path) . "'";
        $oldFoto = null;
        $resultOld = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk = $id LIMIT 1");
        if ($resultOld && mysqli_num_rows($resultOld) > 0) {
            $rowOld = mysqli_fetch_assoc($resultOld);
            $oldFoto = $rowOld['foto'];
        }
        if ($oldFoto) {
            $oldPath = __DIR__ . '/../' . $oldFoto;
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }
    }

    $sql = "UPDATE produk SET id_kategori = $id_kategori, nama_produk = '$nama', harga = $harga, stok = $stok, deskripsi = '$deskripsi' $foto_sql WHERE id_produk = $id LIMIT 1";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'No action']);
