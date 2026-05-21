<?php
session_start();
header('Content-Type: application/json');

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['id_admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_customers') {
    // Mengambil daftar customer yang pernah bertransaksi chat, diurutkan dari chat terbaru
    $query = "
        SELECT u.id_user, u.nama_lengkap, u.email,
               (SELECT isi_chat FROM pesan_chat WHERE id_user = u.id_user ORDER BY waktu_kirim DESC LIMIT 1) as last_message,
               (SELECT waktu_kirim FROM pesan_chat WHERE id_user = u.id_user ORDER BY waktu_kirim DESC LIMIT 1) as last_message_time
        FROM user u
        WHERE EXISTS (SELECT 1 FROM pesan_chat WHERE id_user = u.id_user)
        ORDER BY last_message_time DESC
    ";
    
    $result = mysqli_query($conn, $query);
    $customers = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formatted_time = "";
            if (!empty($row['last_message_time'])) {
                $formatted_time = date('d M, H:i', strtotime($row['last_message_time']));
            }
            
            $customers[] = [
                'id_user' => (int)$row['id_user'],
                'nama_lengkap' => $row['nama_lengkap'],
                'email' => $row['email'],
                'last_message' => $row['last_message'] ? $row['last_message'] : 'Belum ada pesan',
                'last_time' => $formatted_time
            ];
        }
    }
    
    echo json_encode(['status' => 'success', 'customers' => $customers]);
    exit;

} else if ($action === 'get_chat') {
    $id_user = isset($_GET['id_user']) ? (int)$_GET['id_user'] : 0;
    
    if ($id_user <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID Customer tidak valid']);
        exit;
    }
    
    // Mengambil seluruh percakapan customer tertentu beserta nama admin yang membalas jika ada
    $query = "
        SELECT p.*, a.nama_admin 
        FROM pesan_chat p 
        LEFT JOIN admin a ON p.id_admin = a.id_admin 
        WHERE p.id_user = '$id_user' 
        ORDER BY p.waktu_kirim ASC
    ";
    
    $result = mysqli_query($conn, $query);
    $messages = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'id_chat' => (int)$row['id_chat'],
                'pengirim' => $row['pengirim'],
                'isi_chat' => $row['isi_chat'],
                'id_admin' => $row['id_admin'] ? (int)$row['id_admin'] : null,
                'nama_admin' => $row['nama_admin'] ? $row['nama_admin'] : 'Admin',
                'waktu' => date('H:i', strtotime($row['waktu_kirim']))
            ];
        }
    }
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
    exit;

} else if ($action === 'send_reply') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }
    
    $id_user = isset($_POST['id_user']) ? (int)$_POST['id_user'] : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $id_admin = $_SESSION['id_admin'];
    
    if ($id_user <= 0 || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }
    
    $message_esc = mysqli_real_escape_string($conn, $message);
    
    // Menyimpan balasan dari admin
    $query = "
        INSERT INTO pesan_chat (id_user, id_admin, isi_chat, pengirim) 
        VALUES ('$id_user', '$id_admin', '$message_esc', 'admin')
    ";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesan: ' . mysqli_error($conn)]);
    }
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Action not found']);
    exit;
}
