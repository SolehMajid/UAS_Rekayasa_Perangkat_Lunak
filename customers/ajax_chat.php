<?php
session_start();
header('Content-Type: application/json');

// Pastikan user sudah login sebagai customer
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'customer' || !isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'send_message') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    $id_user = $_SESSION['id_user'];
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $is_quick = isset($_POST['is_quick']) ? (int)$_POST['is_quick'] : 0;

    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong']);
        exit;
    }

    $message_esc = mysqli_real_escape_string($conn, $message);
    
    // 1. Simpan pesan dari customer
    $query_user = "INSERT INTO pesan_chat (id_user, id_admin, isi_chat, pengirim) VALUES ('$id_user', NULL, '$message_esc', 'customer')";
    if (!mysqli_query($conn, $query_user)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesan: ' . mysqli_error($conn)]);
        exit;
    }

    // 2. Jika merupakan pesan cepat (quick button), kirim balasan chatbot otomatis
    if ($is_quick) {
        $bot_reply = "";
        
        // Deteksi tombol berdasarkan isi pesan
        if (stripos($message, 'ready stok') !== false || stripos($message, 'Stok') !== false) {
            $bot_reply = "Halo Bunda! Semua produk yang bisa dimasukkan ke keranjang ready stok dan siap dikirim ya. Yuk langsung checkout! 📦";
        } else if (stripos($message, 'retur') !== false || stripos($message, 'Retur') !== false) {
            $bot_reply = "Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄";
        } else if (stripos($message, 'ongkir') !== false || stripos($message, 'Ongkir') !== false) {
            $bot_reply = "Ongkir ke Jakarta mulai dari Rp 9.000 saja Bun! Dan ada promo Gratis Ongkir lho untuk minimal belanja Rp 100.000! 🚚";
        } else if (stripos($message, 'promo') !== false || stripos($message, 'Promo') !== false) {
            $bot_reply = "Ada dong Bun! Banyak diskon menarik hingga 50% untuk produk mainan dan pakaian anak hari ini. Cek kategori promo ya! 🎁";
        } else {
            $bot_reply = "Terima kasih atas pertanyaannya! Tim admin kami akan segera membalas pesan Bunda/Ayah di sini ya 😊";
        }

        if (!empty($bot_reply)) {
            $bot_reply_esc = mysqli_real_escape_string($conn, $bot_reply);
            $query_bot = "INSERT INTO pesan_chat (id_user, id_admin, isi_chat, pengirim) VALUES ('$id_user', NULL, '$bot_reply_esc', 'bot')";
            mysqli_query($conn, $query_bot);
        }
    }

    echo json_encode(['status' => 'success']);
    exit;

} else if ($action === 'get_messages') {
    $id_user = $_SESSION['id_user'];
    
    // Ambil riwayat chat terurut waktu kirim
    $query = "SELECT * FROM pesan_chat WHERE id_user = '$id_user' ORDER BY waktu_kirim ASC";
    $result = mysqli_query($conn, $query);
    
    $messages = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'id_chat' => (int)$row['id_chat'],
                'pengirim' => $row['pengirim'],
                'isi_chat' => $row['isi_chat'],
                'waktu' => date('H:i', strtotime($row['waktu_kirim']))
            ];
        }
    }

    echo json_encode(['status' => 'success', 'messages' => $messages]);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Action not found']);
    exit;
}
