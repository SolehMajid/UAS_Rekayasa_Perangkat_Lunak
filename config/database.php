<?php
// Deteksi lingkungan server secara dinamis
$is_localhost = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $host_name = $_SERVER['HTTP_HOST'];
    if (strpos($host_name, 'localhost') !== false || strpos($host_name, '127.0.0.1') !== false) {
        $is_localhost = true;
    }
} else {
    $is_localhost = true;
}

if ($is_localhost) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "squashy_db";
} else {
    $host = "sql104.infinityfree.com";
    $user = "if0_42209691";
    $pass = "J1NIGQMwZA";
    $db   = "if0_42209691_squashy_db";
}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Auto-migrate/setup pesan_chat table
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'pesan_chat'");
if (mysqli_num_rows($check_table) == 0) {
    mysqli_query($conn, "
        CREATE TABLE `pesan_chat` (
          `id_chat` int(11) NOT NULL AUTO_INCREMENT,
          `id_user` int(11) NOT NULL,
          `id_admin` int(11) DEFAULT NULL,
          `isi_chat` text NOT NULL,
          `pengirim` enum('customer','admin','bot') NOT NULL,
          `waktu_kirim` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id_chat`),
          KEY `id_user` (`id_user`),
          KEY `id_admin` (`id_admin`),
          CONSTRAINT `pesan_chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
          CONSTRAINT `pesan_chat_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} else {
    $check_col = mysqli_query($conn, "SHOW COLUMNS FROM `pesan_chat` LIKE 'pengirim'");
    if (mysqli_num_rows($check_col) == 0) {
        // Drop existing foreign keys
        @mysqli_query($conn, "ALTER TABLE `pesan_chat` DROP FOREIGN KEY `pesan_chat_ibfk_2`");
        @mysqli_query($conn, "ALTER TABLE `pesan_chat` DROP FOREIGN KEY `pesan_chat_ibfk_1`");
        
        // Modify structural types to match nullable requirements
        mysqli_query($conn, "ALTER TABLE `pesan_chat` MODIFY `id_admin` int(11) NULL");
        mysqli_query($conn, "ALTER TABLE `pesan_chat` ADD COLUMN `pengirim` enum('customer','admin','bot') NOT NULL DEFAULT 'customer'");
        mysqli_query($conn, "ALTER TABLE `pesan_chat` ADD COLUMN `waktu_kirim` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
        
        // Add foreign keys back with ON DELETE rules
        @mysqli_query($conn, "ALTER TABLE `pesan_chat` ADD CONSTRAINT `pesan_chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE");
        @mysqli_query($conn, "ALTER TABLE `pesan_chat` ADD CONSTRAINT `pesan_chat_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL");
    }
}

// Auto-migrate/setup nomor_resi in order table
$check_resi_col = mysqli_query($conn, "SHOW COLUMNS FROM `order` LIKE 'nomor_resi'");
if (mysqli_num_rows($check_resi_col) == 0) {
    mysqli_query($conn, "ALTER TABLE `order` ADD COLUMN `nomor_resi` varchar(50) DEFAULT NULL");
}


