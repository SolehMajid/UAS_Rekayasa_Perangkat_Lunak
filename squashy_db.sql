-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2026 at 10:04 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `squashy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `password_hash_admin` varchar(255) NOT NULL COMMENT 'Di-hash menggunakan SHA-256 (Menyesuaikan dengan register)',
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alamat_pengiriman`
--

CREATE TABLE `alamat_pengiriman` (
  `id_alamat` int NOT NULL,
  `id_user` int NOT NULL,
  `label_alamat` varchar(255) DEFAULT NULL,
  `jalan` varchar(255) NOT NULL,
  `kecamatan` varchar(255) NOT NULL,
  `kabupaten` varchar(255) NOT NULL,
  `kodepos` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id_cart` int NOT NULL,
  `id_user` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id_order` int NOT NULL,
  `id_user` int NOT NULL,
  `nomer_hp` varchar(100) DEFAULT NULL,
  `nama_pembeli` varchar(255) NOT NULL,
  `tanggal_pesanan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_tagihan` int NOT NULL,
  `status_pesanan` enum('pending','dibayar','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
  `snap_token` varchar(255) DEFAULT NULL,
  `no_resi` varchar(50) DEFAULT '',
  `nomor_resi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id_detail` int NOT NULL,
  `id_order` int NOT NULL,
  `id_produk` int NOT NULL,
  `nama_produk` varchar(255) DEFAULT NULL,
  `harga_saat_order` int DEFAULT NULL,
  `foto_produk` varchar(255) DEFAULT NULL,
  `kuantitas` int NOT NULL,
  `subtotal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id_payment` int NOT NULL,
  `id_order` int NOT NULL,
  `metode_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` varchar(100) DEFAULT 'pending',
  `waktu_bayar` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesan_chat`
--

CREATE TABLE `pesan_chat` (
  `id_chat` int NOT NULL,
  `id_user` int NOT NULL,
  `id_admin` int DEFAULT NULL,
  `isi_chat` text,
  `pengirim` enum('customer','admin','bot') NOT NULL DEFAULT 'customer',
  `waktu_kirim` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int NOT NULL,
  `stok` int DEFAULT '0',
  `deskripsi` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_produk`
--

CREATE TABLE `review_produk` (
  `id_review` int NOT NULL,
  `id_user` int NOT NULL,
  `id_produk` int NOT NULL,
  `id_order` int NOT NULL,
  `rating` int NOT NULL,
  `komentar` text,
  `foto_review` varchar(255) DEFAULT NULL,
  `balasan_admin` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT 'Di-hash menggunakan SHA-256 (Menyesuaikan dengan register)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Pakaian'),
(2, 'Mainan'),
(3, 'Perlengkapan');

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `nama_produk`, `harga`, `stok`, `deskripsi`, `foto`) VALUES
(1, 2, 'Balok Bangun Kreatif', 85000, 25, 'Mainan edukasi balok kayu susun untuk melatih kreativitas anak.', 'assets/images_sq/Balok Bangun Kreatif.jpg'),
(2, 2, 'Boneka Beruang Mini', 35000, 40, 'Boneka beruang kecil yang lucu dan lembut, cocok sebagai gantungan atau teman main.', 'assets/images_sq/Boneka Beruang Mini.jpg'),
(3, 2, 'Boneka Beruang Premium', 120000, 15, 'Boneka beruang ukuran sedang kualitas premium dengan bulu yang sangat halus.', 'assets/images_sq/Boneka Beruang Premium.jpg'),
(4, 2, 'Boneka Polar Bear', 75000, 20, 'Boneka beruang kutub putih yang imut dan menggemaskan.', 'assets/images_sq/Boneka Polar Bear.avif'),
(5, 3, 'Botol Minum Unicorn', 45000, 30, 'Botol minum anak dengan desain karakter unicorn yang cantik dan bebas BPA.', 'assets/images_sq/Botol Minum Unicorn.jpg'),
(6, 1, 'Dress Unicorn Dream', 110000, 18, 'Gaun anak perempuan cantik dengan corak unicorn dan bahan yang nyaman.', 'assets/images_sq/Dress Unicorn Dream.webp'),
(7, 3, 'Handuk Karakter Lucu', 60000, 25, 'Handuk mandi anak dengan bahan microfiber super lembut dan motif lucu.', 'assets/images_sq/Handuk Karakter Lucu.webp'),
(8, 1, 'Hoodie Bear Cute', 135000, 12, 'Jaket hoodie anak dengan telinga beruang yang lucu di bagian penutup kepala.', 'assets/images_sq/Hoodie Bear Cute.jpg'),
(9, 1, 'Jaket Adventure Kids', 145000, 10, 'Jaket pelindung angin dan gerimis, cocok untuk aktivitas luar ruangan anak.', 'assets/images_sq/Jaket Adventure Kids.jpg'),
(10, 1, 'Kaos Dino Adventure', 49000, 30, 'Kaos katun anak bermotif petualangan dinosaurus yang seru.', 'assets/images_sq/Kaos Dino Adventure.webp'),
(11, 1, 'Kaos Dino Comic', 49000, 28, 'Kaos anak dengan grafis komik dinosaurus yang penuh warna.', 'assets/images_sq/Kaos Dino Comic.webp'),
(12, 1, 'Kaos Dino Explorer', 49000, 35, 'Kaos kasual anak bertema penjelajah dinosaurus dari bahan katun adem.', 'assets/images_sq/Kaos Dino Explorer.webp'),
(13, 1, 'Kaos Dino Roar', 49000, 40, 'Kaos anak dengan gambar dinosaurus T-Rex berteriak yang keren.', 'assets/images_sq/Kaos Dino Roar.jpg'),
(14, 1, 'Kaos Fossil Dino', 52000, 22, 'Kaos motif fosil dinosaurus yang unik dan disukai anak-anak.', 'assets/images_sq/Kaos Fossil Dino.avif'),
(15, 2, 'Kereta Api Mini', 40000, 50, 'Mainan kereta api kayu mini yang aman dimainkan anak balita.', 'assets/images_sq/Kereta Api Mini.webp'),
(16, 2, 'Kitchen Set Ceria', 150000, 8, 'Set mainan peralatan dapur mini untuk melatih peran masak-memasak.', 'assets/images_sq/Kitchen Set Ceria.jpg'),
(17, 3, 'Kotak Bekal Rainbow', 38000, 35, 'Wadah bekal makan siang bersekat dengan desain pelangi yang menarik.', 'assets/images_sq/Kotak Bekal Rainbow.webp'),
(18, 2, 'Mobil Polisi Mini', 25000, 60, 'Mainan mobil polisi berukuran kecil dengan sistem roda gesek (friction).', 'assets/images_sq/Mobil Polisi Mini.webp'),
(19, 3, 'Payung Animal Friends', 55000, 20, 'Payung lipat anak dengan motif gambar hewan yang ceria.', 'assets/images_sq/Payung Animal Friends.jpg'),
(20, 2, 'Piano Musik Anak', 95000, 15, 'Keyboard mainan elektronik mini dengan suara musik instrumen yang menyenangkan.', 'assets/images_sq/Piano Musik Anak.jpg'),
(21, 1, 'Piyama Animal Friends', 85000, 20, 'Setelan baju tidur piyama anak bermotif aneka hewan dari bahan lembut.', 'assets/images_sq/Piyama Animal Friends.jpg'),
(22, 2, 'Puzzle Hewan Edukatif', 30000, 45, 'Puzzle kayu edukasi mengenal nama dan bentuk binatang untuk balita.', 'assets/images_sq/Puzzle Hewan Edukatif.webp'),
(23, 2, 'Robot Explorer Junior', 175000, 10, 'Mainan robot interaktif dengan lampu dan suara yang bisa bergerak.', 'assets/images_sq/Robot Explorer Junior.jpg'),
(24, 3, 'Sandal Bunny Cute', 42000, 30, 'Sandal rumah anak berbentuk kelinci berbulu halus dan empuk.', 'assets/images_sq/Sandal Bunny Cute.webp'),
(25, 3, 'Sepatu Ceria Kids', 125000, 15, 'Sepatu kets anak kasual bertali karet, nyaman untuk sekolah dan bermain.', 'assets/images_sq/Sepatu Ceria Kids.jpg'),
(26, 3, 'Tas Sekolah Dino', 98000, 20, 'Tas ransel anak sekolah bergambar dinosaurus dengan kantong yang luas.', 'assets/images_sq/Tas Sekolah Dino.jpg'),
(27, 3, 'Tas Sekolah Panda', 98000, 18, 'Tas ransel sekolah anak bermotif panda yang imut dengan bahan tebal.', 'assets/images_sq/Tas Sekolah Panda.jpg'),
(28, 3, 'Tempat Pensil Galaxy', 28000, 40, 'Kotak pensil bertema luar angkasa dengan ritsleting yang kuat.', 'assets/images_sq/Tempat Pensil Galaxy.jpg'),
(29, 3, 'Topi Petualang Cilik', 35000, 25, 'Topi rimba anak untuk melindungi dari sinar matahari saat bertualang.', 'assets/images_sq/Topi Petualang Cilik.jpg'),
(30, 1, 'Rainbow Dress Kids', 115000, 15, 'Gaun anak perempuan bermotif pelangi yang cerah dan anggun.', 'assets/images_sq/rainbow dress kids.jpg');

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `password_hash_admin`, `nama_admin`) VALUES
(1, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin'), -- password: admin
(2, '4c1029697ee358715d3a14a2add817c4b01651440de808371f78165ac90dc581', 'owner'), -- password: owner
(3, '1562206543da764123c21bd524674f0a8aaf49c8a89744c97352fe677f7e4006', 'staff'); -- password: staff

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `email`, `password_hash`) VALUES
(1, 'Bunda Squashy', 'bunda@gmail.com', 'f6ae2e5d1cdfded8e19d945ed50e8f7459820c335ec5a5c490c76e7e4fc56488'), -- password: bunda123
(2, 'Andi Pratama', 'andi@gmail.com', '180348f5b22db17be014d5c1cb8151c858267cb44819e5460a7ae2528b91680e'), -- password: andi
(3, 'Siti Aisyah', 'siti@gmail.com', 'a7ad5342de24cf9ad849a41b9a265274ebc803807f9ff6734bd7e9c031e7b042'), -- password: siti
(4, 'Rudi Hartono', 'rudi@gmail.com', '802a32cf806c5c8586c70b3b9e5aa2198b7f4ab0b8259d7ce4fe782a468be335'), -- password: rudi
(5, 'Dewi Lestari', 'dewi@gmail.com', 'da979182fc9e9622ef8732bb2e4644d2ddf5f5e5445ad172267e98a2caa2d6fd'), -- password: dewi
(6, 'Ahmad Fauzi', 'ahmad@gmail.com', 'e2283de5cde23c102a0a2df380e22709e8633c706ee9db9a8f6d6c41b80c32b9'), -- password: ahmad123
(7, 'Rini Wulandari', 'rini@gmail.com', '9d072f5d54a2a1dcff52136e051c5f3e098fae852a4e402b28c50537f3747806'), -- password: rini123
(8, 'Budi Santoso', 'budi@gmail.com', '0443224b172a6b4122d250865d1b714578c7c91db0b8f673a5a73e659d64f0de'); -- password: budi123

--
-- Dumping data for table `alamat_pengiriman`
--

INSERT INTO `alamat_pengiriman` (`id_alamat`, `id_user`, `label_alamat`, `jalan`, `kecamatan`, `kabupaten`, `kodepos`) VALUES
(1, 1, 'Rumah', 'Jl. Mawar No.12', 'Lowokwaru', 'Malang', 65141),
(2, 1, 'Kantor', 'Gedung Graha Pena Lt. 5, Jl. Ahmad Yani No. 88', 'Blimbing', 'Malang', 65126),
(3, 2, 'Rumah', 'Jl. Melati No.7', 'Sukun', 'Malang', 65148),
(4, 3, 'Rumah', 'Jl. Kenanga No.22', 'Klojen', 'Malang', 65111),
(5, 4, 'Kantor', 'Jl. Sudirman No.45', 'Kedungkandang', 'Malang', 65135),
(6, 5, 'Rumah', 'Jl. Anggrek No.3', 'Blimbing', 'Malang', 65126),
(7, 6, 'Rumah', 'Jl. Pahlawan No.15', 'Lowokwaru', 'Malang', 65145),
(8, 7, 'Kos', 'Gang Kelinci No.4B', 'Lowokwaru', 'Malang', 65141),
(9, 8, 'Rumah', 'Jl. Flamboyan No.9', 'Klojen', 'Malang', 65115);

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id_order`, `id_user`, `nomer_hp`, `nama_pembeli`, `tanggal_pesanan`, `total_tagihan`, `status_pesanan`, `snap_token`, `no_resi`, `nomor_resi`) VALUES
(1, 2, '081234567890', 'Andi Pratama', '2026-05-01 10:15:00', 171000, 'selesai', 'midtrans-snap-token-111111', 'REG123456789', 'REG123456789'),
(2, 3, '081234567891', 'Siti Aisyah', '2026-05-05 13:20:00', 95000, 'selesai', 'midtrans-snap-token-222222', 'REG987654321', 'REG987654321'),
(3, 4, '081234567892', 'Rudi Hartono', '2026-05-12 15:30:00', 215000, 'dikirim', 'midtrans-snap-token-333333', 'REG112233445', 'REG112233445'),
(4, 5, '081234567893', 'Dewi Lestari', '2026-05-18 09:00:00', 135000, 'diproses', 'midtrans-snap-token-444444', '', NULL),
(5, 1, '081234567894', 'Bunda Squashy', '2026-05-20 11:45:00', 98000, 'selesai', 'midtrans-snap-token-555555', 'REG556677889', 'REG556677889'),
(6, 2, '081234567890', 'Andi Pratama', '2026-06-01 08:30:00', 175000, 'pending', 'midtrans-snap-token-666666', '', NULL),
(7, 6, '081345678901', 'Ahmad Fauzi', '2026-06-05 14:00:00', 165000, 'dibayar', 'midtrans-snap-token-777777', '', NULL),
(8, 7, '081456789012', 'Rini Wulandari', '2026-06-08 16:45:00', 170000, 'selesai', 'midtrans-snap-token-888888', 'REG990011223', 'REG990011223'),
(9, 8, '081567890123', 'Budi Santoso', '2026-06-10 11:00:00', 120000, 'dibatalkan', 'midtrans-snap-token-999999', '', NULL),
(10, 3, '081234567891', 'Siti Aisyah', '2026-06-12 10:20:00', 157000, 'selesai', 'midtrans-snap-token-101010', 'REG887766554', 'REG887766554');

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id_detail`, `id_order`, `id_produk`, `nama_produk`, `harga_saat_order`, `foto_produk`, `kuantitas`, `subtotal`) VALUES
(1, 1, 10, 'Kaos Dino Adventure', 49000, 'assets/images_sq/Kaos Dino Adventure.webp', 2, 98000),
(2, 1, 5, 'Botol Minum Unicorn', 45000, 'assets/images_sq/Botol Minum Unicorn.jpg', 1, 45000),
(3, 1, 28, 'Tempat Pensil Galaxy', 28000, 'assets/images_sq/Tempat Pensil Galaxy.jpg', 1, 28000),
(4, 2, 20, 'Piano Musik Anak', 95000, 'assets/images_sq/Piano Musik Anak.jpg', 1, 95000),
(5, 3, 8, 'Hoodie Bear Cute', 135000, 'assets/images_sq/Hoodie Bear Cute.jpg', 1, 135000),
(6, 3, 17, 'Kotak Bekal Rainbow', 38000, 'assets/images_sq/Kotak Bekal Rainbow.webp', 1, 38000),
(7, 3, 24, 'Sandal Bunny Cute', 42000, 'assets/images_sq/Sandal Bunny Cute.webp', 1, 42000),
(8, 4, 8, 'Hoodie Bear Cute', 135000, 'assets/images_sq/Hoodie Bear Cute.jpg', 1, 135000),
(9, 5, 26, 'Tas Sekolah Dino', 98000, 'assets/images_sq/Tas Sekolah Dino.jpg', 1, 98000),
(10, 6, 23, 'Robot Explorer Junior', 175000, 'assets/images_sq/Robot Explorer Junior.jpg', 1, 175000),
(11, 7, 1, 'Balok Bangun Kreatif', 85000, 'assets/images_sq/Balok Bangun Kreatif.jpg', 1, 85000),
(12, 7, 15, 'Kereta Api Mini', 40000, 'assets/images_sq/Kereta Api Mini.webp', 2, 80000),
(13, 8, 6, 'Dress Unicorn Dream', 110000, 'assets/images_sq/Dress Unicorn Dream.webp', 1, 110000),
(14, 8, 7, 'Handuk Karakter Lucu', 60000, 'assets/images_sq/Handuk Karakter Lucu.webp', 1, 60000),
(15, 9, 3, 'Boneka Beruang Premium', 120000, 'assets/images_sq/Boneka Beruang Premium.jpg', 1, 120000),
(16, 10, 30, 'Rainbow Dress Kids', 115000, 'assets/images_sq/rainbow dress kids.jpg', 1, 115000),
(17, 10, 24, 'Sandal Bunny Cute', 42000, 'assets/images_sq/Sandal Bunny Cute.webp', 1, 42000);

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id_payment`, `id_order`, `metode_pembayaran`, `status_pembayaran`, `waktu_bayar`) VALUES
(1, 1, 'Transfer Bank', 'success', '2026-05-01 10:20:00'),
(2, 2, 'QRIS', 'success', '2026-05-05 13:25:00'),
(3, 3, 'Transfer Bank', 'success', '2026-05-12 15:35:00'),
(4, 4, 'QRIS', 'success', '2026-05-18 09:05:00'),
(5, 5, 'Transfer Bank', 'success', '2026-05-20 11:50:00'),
(6, 6, 'QRIS', 'pending', '2026-06-01 08:35:00'),
(7, 7, 'Transfer Bank', 'success', '2026-06-05 14:10:00'),
(8, 8, 'QRIS', 'success', '2026-06-08 16:50:00'),
(9, 9, 'Transfer Bank', 'failed', '2026-06-10 11:30:00'),
(10, 10, 'QRIS', 'success', '2026-06-12 10:25:00');

--
-- Dumping data for table `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_order`, `rating`, `komentar`, `foto_review`, `balasan_admin`, `created_at`) VALUES
(1, 2, 10, 1, 5, 'Bahannya adem banget, sablonannya rapi dan gambar dinonya lucu banget. Anak saya suka sekali!', 'assets/reviews/review_1.jpg', 'Terima kasih kak Andi atas ulasannya! Senang mendengarnya. Ditunggu orderan berikutnya ya.', '2026-05-03 14:22:00'),
(2, 3, 20, 2, 5, 'Piano mainannya berfungsi dengan baik, suara nadanya jernih dan tidak cempreng. Cocok untuk belajar musik balita.', NULL, 'Terima kasih Bunda Siti! Semoga mainannya bermanfaat untuk tumbuh kembang si kecil.', '2026-05-06 09:12:00'),
(3, 4, 8, 3, 4, 'Hoodie-nya tebal dan lembut, telinga beruangnya bikin gemas. Sayang pengiriman agak lama dari pihak kurir.', 'assets/reviews/review_3.jpg', 'Terima kasih atas masukannya kak Rudi. Kami akan berkoordinasi dengan pihak ekspedisi agar pengiriman lebih cepat ke depannya.', '2026-05-15 11:45:00'),
(4, 1, 26, 5, 5, 'Tas sekolahnya tebal, jahitannya kuat, dan banyak kompartemennya. Muat banyak buku sekolah.', NULL, 'Terima kasih Bunda Squashy atas kepercayaannya berbelanja di toko kami!', '2026-05-22 13:30:00'),
(5, 7, 6, 8, 5, 'Dress nya cantik sekali, warnanya cerah dan bahannya tidak panas. Anak saya senang sekali memakainya ke pesta ulang tahun.', 'assets/reviews/review_5.jpg', 'Terima kasih kak Rini! Senang sekali dress-nya cocok untuk si kecil.', '2026-06-09 17:50:00'),
(6, 3, 30, 10, 5, 'Dress pelangi yang sangat indah, bahannya jatuh dan lembut. Pas sekali di badan anak saya.', NULL, 'Terima kasih kembali kak Siti atas ulasan bintang 5-nya!', '2026-06-13 11:10:00');

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id_cart`, `id_user`, `id_produk`, `jumlah`) VALUES
(1, 3, 22, 1),
(2, 3, 18, 2),
(3, 5, 30, 1),
(4, 6, 2, 3),
(5, 8, 27, 1);

--
-- Dumping data for table `pesan_chat`
--

INSERT INTO `pesan_chat` (`id_chat`, `id_user`, `id_admin`, `isi_chat`, `pengirim`, `waktu_kirim`) VALUES
(1, 2, NULL, 'Halo, apakah stok Hoodie Bear Cute masih tersedia?', 'customer', '2026-05-10 08:00:00'),
(2, 2, 1, 'Halo kak Andi! Untuk Hoodie Bear Cute stoknya masih tersedia ya. Silakan langsung melakukan pemesanan sebelum kehabisan.', 'admin', '2026-05-10 08:03:00'),
(3, 2, NULL, 'Baik kak, terima kasih informasinya. Saya order sekarang.', 'customer', '2026-05-10 08:05:00'),
(4, 3, NULL, 'Berapa lama estimasi pengiriman ke Kota Malang ya?', 'customer', '2026-05-12 09:15:00'),
(5, 3, 1, 'Untuk sesama Malang, estimasi pengiriman 1-2 hari kerja menggunakan kurir lokal atau ekspedisi reguler kak.', 'admin', '2026-05-12 09:18:00'),
(6, 3, NULL, 'Oke kak, terima kasih.', 'customer', '2026-05-12 09:20:00'),
(7, 6, NULL, 'Halo, robot explorer junior apakah ada garansinya?', 'customer', '2026-06-04 10:00:00'),
(8, 6, 1, 'Halo kak Ahmad! Semua mainan elektronik kami garansi toko 7 hari setelah barang diterima ya, pastikan ada video unboxing.', 'admin', '2026-06-04 10:05:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `alamat_pengiriman`
--
ALTER TABLE `alamat_pengiriman`
  ADD PRIMARY KEY (`id_alamat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id_cart`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `pesan_chat`
--
ALTER TABLE `pesan_chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD PRIMARY KEY (`id_review`),
  ADD UNIQUE KEY `id_user` (`id_user`,`id_produk`,`id_order`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `alamat_pengiriman`
--
ALTER TABLE `alamat_pengiriman`
  MODIFY `id_alamat` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id_cart` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id_payment` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesan_chat`
--
ALTER TABLE `pesan_chat`
  MODIFY `id_chat` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamat_pengiriman`
--
ALTER TABLE `alamat_pengiriman`
  ADD CONSTRAINT `alamat_pengiriman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`);

--
-- Constraints for table `pesan_chat`
--
ALTER TABLE `pesan_chat`
  ADD CONSTRAINT `pesan_chat_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesan_chat_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD CONSTRAINT `review_produk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `review_produk_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `review_produk_ibfk_3` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
