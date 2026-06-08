-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 08, 2026 at 08:06 AM
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
  `password_hash_admin` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `password_hash_admin`, `nama_admin`) VALUES
(1, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'Budi Santoso'),
(2, '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Siti Rahayu'),
(3, 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Ahmad Fauzi'),
(4, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin');

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

--
-- Dumping data for table `alamat_pengiriman`
--

INSERT INTO `alamat_pengiriman` (`id_alamat`, `id_user`, `label_alamat`, `jalan`, `kecamatan`, `kabupaten`, `kodepos`) VALUES
(1, 1, 'Rumah', 'Jl. Mawar No. 12', 'Gubeng', 'Surabaya', 60281),
(2, 1, 'Kantor', 'Jl. Pemuda No. 45', 'Genteng', 'Surabaya', 60271),
(3, 2, 'Rumah', 'Jl. Kenanga No. 7', 'Wonokromo', 'Surabaya', 60243),
(4, 3, 'Rumah', 'Jl. Melati No. 3', 'Mulyorejo', 'Surabaya', 60115),
(5, 4, 'Rumah', 'Jl. Anggrek No. 21', 'Rungkut', 'Surabaya', 60293),
(6, 5, 'Rumah', 'Jl. Dahlia No. 9', 'Tenggilis Mejoyo', 'Surabaya', 60292),
(7, 6, 'Rumah', 'Jl. Merpati No. 5', 'Sukodono', 'Sidoarjo', 61258),
(8, 7, 'Rumah', 'Jl. Cendana No. 15', 'Buduran', 'Sidoarjo', 61252),
(9, 8, 'Rumah', 'Jl. Pinus No. 8', 'Waru', 'Sidoarjo', 61256),
(10, 9, 'Rumah', 'Jl. Cemara No. 4', 'Gedangan', 'Sidoarjo', 61254),
(11, 10, 'Rumah', 'Jl. Mangga No. 17', 'Taman', 'Sidoarjo', 61257),
(12, 11, 'Rumah', 'Jl. Apel No. 6', 'Klojen', 'Malang', 65119),
(13, 12, 'Rumah', 'Jl. Jeruk No. 11', 'Lowokwaru', 'Malang', 65141),
(14, 13, 'Rumah', 'Jl. Salak No. 2', 'Blimbing', 'Malang', 65126),
(15, 14, 'Rumah', 'Jl. Nangka No. 30', 'Kedungkandang', 'Malang', 65136),
(16, 15, 'Rumah', 'Jl. Rambutan No. 13', 'Sukun', 'Malang', 65148),
(17, 16, 'Rumah', 'Jl. Teratai No. 22', 'Mojosari', 'Mojokerto', 61382),
(18, 17, 'Rumah', 'Jl. Flamboyan No. 1', 'Prajuritkulon', 'Mojokerto', 61321),
(19, 18, 'Rumah', 'Jl. Bougenville No. 19', 'Sooko', 'Mojokerto', 61361),
(20, 19, 'Rumah', 'Jl. Kamboja No. 14', 'Driyorejo', 'Gresik', 61177),
(21, 20, 'Rumah', 'Jl. Mawar Merah No. 3', 'Kebomas', 'Gresik', 61121),
(22, 21, 'Rumah', 'Jl. Tulip No. 8', 'Lamongan', 'Lamongan', 62215),
(23, 22, 'Rumah', 'Jl. Sakura No. 5', 'Tuban', 'Tuban', 62315),
(24, 23, 'Rumah', 'Jl. Lavender No. 10', 'Kediri', 'Kediri', 64131),
(25, 24, 'Rumah', 'Jl. Sunflower No. 7', 'Blitar', 'Blitar', 66131),
(26, 25, 'Rumah', 'Jl. Orchid No. 4', 'Jombang', 'Jombang', 61415),
(27, 26, 'Rumah', 'Jl. Lily No. 16', 'Bojonegoro', 'Bojonegoro', 62119),
(28, 27, 'Rumah', 'Jl. Rosemary No. 9', 'Nganjuk', 'Nganjuk', 64419),
(29, 28, 'Rumah', 'Jl. Jasmine No. 6', 'Madiun', 'Madiun', 63119),
(30, 29, 'Rumah', 'Jl. Violet No. 12', 'Ponorogo', 'Ponorogo', 63419),
(31, 30, 'Rumah', 'Jl. Daisy No. 3', 'Trenggalek', 'Trenggalek', 66319);

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

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Pakaian'),
(2, 'Mainan'),
(3, 'Perlengkapan');

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
  `status_pesanan` enum('pending','dibayar','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id_order`, `id_user`, `nomer_hp`, `nama_pembeli`, `tanggal_pesanan`, `total_tagihan`, `status_pesanan`) VALUES
(1, 1, '081234567890', 'Dewi Kusuma', '2026-05-20 03:15:00', 195000, 'selesai'),
(2, 2, '082345678901', 'Rizky Pratama', '2026-05-25 04:30:00', 410000, 'selesai'),
(3, 3, '083456789012', 'Rina Wulandari', '2026-05-02 02:00:00', 135000, 'selesai'),
(4, 4, '084567890123', 'Hendra Wijaya', '2026-05-08 07:20:00', 250000, 'selesai'),
(5, 5, '085678901234', 'Nurul Hidayah', '2026-05-14 09:00:00', 285000, 'selesai'),
(6, 6, '086789012345', 'Fajar Setiawan', '2026-05-20 03:30:00', 390000, 'selesai'),
(7, 7, '087890123456', 'Yuni Astuti', '2026-05-25 06:45:00', 155000, 'dibayar'),
(8, 8, '088901234567', 'Eko Prasetyo', '2026-05-03 01:15:00', 520000, 'selesai'),
(9, 9, '089012345678', 'Lestari Ningsih', '2026-05-08 08:00:00', 225000, 'selesai'),
(10, 10, '081123456789', 'Agus Salim', '2026-05-12 04:00:00', 430000, 'selesai'),
(11, 11, '082234567890', 'Mega Pratiwi', '2026-05-18 02:30:00', 185000, 'selesai'),
(12, 12, '083345678901', 'Doni Firmansyah', '2026-05-22 07:00:00', 325000, 'selesai'),
(13, 13, '084456789012', 'Sari Indah', '2026-05-28 03:15:00', 265000, 'dibayar'),
(14, 1, '081234567890', 'Dewi Kusuma', '2026-05-01 02:00:00', 375000, 'selesai'),
(15, 14, '085567890123', 'Bambang Susilo', '2026-05-05 06:30:00', 450000, 'selesai'),
(16, 15, '086678901234', 'Fitri Handayani', '2026-05-08 03:00:00', 190000, 'selesai'),
(17, 2, '082345678901', 'Rizky Pratama', '2026-05-10 04:45:00', 730000, 'selesai'),
(18, 16, '087789012345', 'Irwan Hakim', '2026-05-12 07:15:00', 340000, 'selesai'),
(19, 17, '088890123456', 'Putri Melania', '2026-05-15 02:30:00', 260000, 'selesai'),
(20, 18, '089901234567', 'Hendri Kurniawan', '2026-05-18 09:00:00', 480000, 'selesai'),
(21, 3, '083456789012', 'Rina Wulandari', '2026-05-20 03:00:00', 310000, 'selesai'),
(22, 19, '081012345678', 'Wati Susanti', '2026-05-22 04:30:00', 175000, 'selesai'),
(23, 20, '082123456789', 'Rudi Hartono', '2026-05-25 06:00:00', 395000, 'dibayar'),
(24, 21, '083234567890', 'Anita Permata', '2026-05-28 02:15:00', 545000, 'dibayar'),
(25, 4, '084567890123', 'Hendra Wijaya', '2026-05-01 03:30:00', 220000, 'selesai'),
(26, 22, '084345678901', 'Joko Widodo', '2026-05-03 07:00:00', 285000, 'selesai'),
(27, 5, '085678901234', 'Nurul Hidayah', '2026-05-05 02:00:00', 420000, 'selesai'),
(28, 23, '085456789012', 'Sri Wahyuni', '2026-05-07 04:15:00', 160000, 'selesai'),
(29, 6, '086789012345', 'Fajar Setiawan', '2026-05-09 06:30:00', 575000, 'selesai'),
(30, 24, '086567890123', 'Tono Supriadi', '2026-05-11 03:00:00', 335000, 'selesai'),
(31, 7, '087890123456', 'Yuni Astuti', '2026-05-13 02:45:00', 490000, 'selesai'),
(32, 25, '087678901234', 'Diana Permatasari', '2026-05-15 07:30:00', 265000, 'selesai'),
(33, 8, '088901234567', 'Eko Prasetyo', '2026-05-17 03:15:00', 380000, 'selesai'),
(34, 26, '088789012345', 'Wahyu Nugroho', '2026-05-01 02:00:00', 710000, 'selesai'),
(35, 9, '089012345678', 'Lestari Ningsih', '2026-05-05 04:30:00', 195000, 'selesai'),
(36, 27, '089890123456', 'Endah Sulistyowati', '2026-05-10 06:00:00', 440000, 'selesai'),
(37, 10, '081123456789', 'Agus Salim', '2026-05-15 02:30:00', 325000, 'selesai'),
(38, 28, '081901234567', 'Surya Darma', '2026-05-20 07:15:00', 560000, 'selesai'),
(39, 11, '082234567890', 'Mega Pratiwi', '2026-05-25 03:00:00', 280000, 'selesai'),
(40, 29, '082012345678', 'Novita Sari', '2026-05-28 04:45:00', 390000, 'selesai'),
(41, 12, '083345678901', 'Doni Firmansyah', '2026-05-03 02:15:00', 455000, 'selesai'),
(42, 30, '083123456789', 'Dian Rachmawati', '2026-05-08 06:30:00', 215000, 'selesai'),
(43, 13, '084456789012', 'Sari Indah', '2026-05-12 03:00:00', 340000, 'selesai'),
(44, 1, '081234567890', 'Dewi Kusuma', '2026-05-18 02:00:00', 625000, 'selesai'),
(45, 14, '085567890123', 'Bambang Susilo', '2026-05-22 07:30:00', 295000, 'selesai'),
(46, 15, '086678901234', 'Fitri Handayani', '2026-05-28 04:00:00', 470000, 'dibayar'),
(47, 2, '082345678901', 'Rizky Pratama', '2026-05-02 02:30:00', 385000, 'selesai'),
(48, 16, '087789012345', 'Irwan Hakim', '2026-05-08 06:15:00', 515000, 'selesai'),
(49, 17, '088890123456', 'Putri Melania', '2026-05-14 03:00:00', 240000, 'selesai'),
(50, 18, '089901234567', 'Hendri Kurniawan', '2026-05-20 08:30:00', 660000, 'selesai'),
(51, 19, '081012345678', 'Wati Susanti', '2026-05-02 02:00:00', 310000, 'dikirim'),
(52, 20, '082123456789', 'Rudi Hartono', '2026-05-05 04:30:00', 430000, 'dikirim'),
(53, 21, '083234567890', 'Anita Permata', '2026-05-08 06:00:00', 185000, 'dikirim'),
(54, 22, '084345678901', 'Joko Widodo', '2026-05-10 02:15:00', 540000, 'diproses'),
(55, 23, '085456789012', 'Sri Wahyuni', '2026-05-12 03:30:00', 275000, 'diproses'),
(56, 24, '086567890123', 'Tono Supriadi', '2026-05-13 07:00:00', 395000, 'dibayar'),
(57, 25, '087678901234', 'Diana Permatasari', '2026-05-14 02:30:00', 160000, 'dibayar'),
(58, 26, '088789012345', 'Wahyu Nugroho', '2026-05-14 04:00:00', 480000, 'pending'),
(59, 27, '089890123456', 'Endah Sulistyowati', '2026-05-15 06:30:00', 225000, 'pending'),
(60, 28, '081901234567', 'Surya Darma', '2026-05-15 08:00:00', 345000, 'pending'),
(61, 31, '081243456789', 'aji@gmail.com', '2026-05-22 02:44:09', 680000, 'selesai'),
(62, 31, '081243456789', 'aji@gmail.com', '2026-05-22 03:03:01', 228000, 'selesai'),
(63, 31, '081243456789', 'aji@gmail.com', '2026-05-22 03:06:51', 179000, 'selesai'),
(64, 31, '081243456789', 'aji@gmail.com', '2026-05-22 03:20:38', 141000, 'selesai'),
(65, 31, '081243456789', 'aji@gmail.com', '2026-05-30 04:39:20', 183000, 'dibayar'),
(66, 31, '081243456789', 'aji@gmail.com', '2026-05-30 04:51:53', 90000, 'dibatalkan'),
(67, 31, '09807878767', 'aji@gmail.com', '2026-06-02 01:38:18', 90000, 'pending'),
(68, 31, '123456789', 'aji@gmail.com', '2026-06-02 01:42:46', 45000, 'pending');

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

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id_detail`, `id_order`, `id_produk`, `nama_produk`, `harga_saat_order`, `foto_produk`, `kuantitas`, `subtotal`) VALUES
(1, 1, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 'assets/images_sq/mm.png', 2, 90000),
(2, 1, 31, 'Kaos Kaki Anak Motif Karakter 5 Pasang', 45000, 'assets/images_sq/mm.png', 1, 45000),
(3, 1, 76, 'Botol Minum Anak Anti Tumpah 500ml', 65000, 'assets/images_sq/mm.png', 1, 65000),
(4, 2, 45, 'Mobil Remote Control 4WD Anak', 325000, 'assets/images_sq/mm.png', 1, 325000),
(5, 2, 54, 'Set Crayon 48 Warna Anak', 55000, 'assets/images_sq/mm.png', 1, 55000),
(6, 2, 64, 'Balon Sabun Anak 500ml', 20000, 'assets/images_sq/mm.png', 1, 20000),
(7, 3, 9, 'Dress Anak Perempuan Motif Bunga', 110000, 'assets/images_sq/mm.png', 1, 110000),
(8, 3, 39, 'Bando Anak Perempuan Set 5 pcs', 30000, 'assets/images_sq/mm.png', 1, 30000),
(9, 4, 41, 'Lego Duplo Set Rumah 80 Pcs', 250000, 'assets/images_sq/mm.png', 1, 250000),
(10, 5, 22, 'Gamis Anak Perempuan Biru Muda', 150000, 'assets/images_sq/mm.png', 1, 150000),
(11, 5, 14, 'Rok Mini Anak Perempuan Polkadot', 55000, 'assets/images_sq/mm.png', 1, 55000),
(12, 5, 87, 'Sedotan Silikon Anak Reusable Set', 45000, 'assets/images_sq/mm.png', 1, 45000),
(13, 6, 51, 'Sepeda Roda Tiga Anak 2-4 Tahun', 385000, 'assets/images_sq/mm.png', 1, 385000),
(14, 6, 89, 'Pelindung Lutut dan Siku Anak Set', 65000, 'assets/images_sq/mm.png', 1, 65000),
(15, 7, 47, 'Action Figure Superhero Set 6 Pcs', 155000, 'assets/images_sq/mm.png', 1, 155000),
(16, 8, 60, 'Piano Keyboard Anak 37 Tuts', 275000, 'assets/images_sq/mm.png', 1, 275000),
(17, 8, 80, 'Tas Sekolah Anak SD Motif Superhero', 185000, 'assets/images_sq/mm.png', 1, 185000),
(18, 8, 79, 'Kotak Pensil Anak Motif Karakter', 45000, 'assets/images_sq/mm.png', 1, 45000),
(19, 9, 78, 'Lunch Box Anak 3 Sekat Anti Tumpah', 85000, 'assets/images_sq/mm.png', 1, 85000),
(20, 9, 77, 'Botol Minum Stainless Steel Anak 350ml', 95000, 'assets/images_sq/mm.png', 1, 95000),
(21, 10, 50, 'Trampolin Mini Anak Indoor', 450000, 'assets/images_sq/mm.png', 1, 450000),
(22, 11, 46, 'Boneka Bayi Reborn Lucu', 185000, 'assets/images_sq/mm.png', 1, 185000),
(23, 12, 45, 'Mobil Remote Control 4WD Anak', 325000, 'assets/images_sq/mm.png', 1, 325000),
(24, 13, 10, 'Dress Anak Tutu Skirt Pink', 125000, 'assets/images_sq/mm.png', 1, 125000),
(25, 13, 39, 'Bando Anak Perempuan Set 5 pcs', 30000, 'assets/images_sq/mm.png', 1, 30000),
(26, 13, 5, 'Kaos Unicorn Anak Perempuan Ukuran S', 55000, 'assets/images_sq/mm.png', 1, 55000),
(27, 14, 17, 'Jaket Hoodie Anak Laki-laki Hitam', 135000, 'assets/images_sq/mm.png', 1, 135000),
(28, 14, 105, 'Karpet Bermain Anak Foam 150x200cm', 285000, 'assets/images_sq/mm.png', 1, 285000),
(29, 15, 71, 'Perosotan Plastik Anak Outdoor', 550000, 'assets/images_sq/mm.png', 1, 550000),
(30, 16, 81, 'Tas Sekolah Anak Perempuan Motif Bunga', 185000, 'assets/images_sq/mm.png', 1, 185000),
(31, 17, 70, 'Robot Edukatif Interaktif Anak', 395000, 'assets/images_sq/mm.png', 1, 395000),
(32, 17, 62, 'Tenda Bermain Anak Indoor Teepee', 275000, 'assets/images_sq/mm.png', 1, 275000),
(33, 18, 51, 'Sepeda Roda Tiga Anak 2-4 Tahun', 385000, 'assets/images_sq/mm.png', 1, 385000),
(34, 19, 22, 'Gamis Anak Perempuan Biru Muda', 150000, 'assets/images_sq/mm.png', 1, 150000),
(35, 19, 91, 'Handuk Anak Motif Hewan 60x120cm', 75000, 'assets/images_sq/mm.png', 1, 75000),
(36, 20, 104, 'Rak Buku Anak Kayu Motif Kartun', 350000, 'assets/images_sq/mm.png', 1, 350000),
(37, 20, 103, 'Lampu Belajar Anak LED Anti Silau', 115000, 'assets/images_sq/mm.png', 1, 115000),
(38, 21, 44, 'Mainan Masak-Masakan Set Lengkap', 135000, 'assets/images_sq/mm.png', 1, 135000),
(39, 21, 15, 'Setelan Baju Tidur Anak Motif Bintang', 95000, 'assets/images_sq/mm.png', 1, 95000),
(40, 21, 94, 'Sikat Gigi Anak Motif Karakter Soft', 25000, 'assets/images_sq/mm.png', 2, 50000),
(41, 22, 48, 'Playdough Set Warna-Warni 12 Warna', 75000, 'assets/images_sq/mm.png', 1, 75000),
(42, 22, 92, 'Sabun Mandi Anak Cair 500ml No Tears', 45000, 'assets/images_sq/mm.png', 1, 45000),
(43, 23, 61, 'Koper Trolley Anak Motif Kartun', 350000, 'assets/images_sq/mm.png', 1, 350000),
(44, 23, 82, 'Bantal Perjalanan Anak Motif Kartun', 75000, 'assets/images_sq/mm.png', 1, 75000),
(45, 24, 29, 'Sepatu Sneakers Anak Laki-laki Putih', 195000, 'assets/images_sq/mm.png', 1, 195000),
(46, 24, 35, 'Baju Renang Anak Laki-laki Motif Ikan', 115000, 'assets/images_sq/mm.png', 1, 115000),
(47, 24, 90, 'Kacamata Renang Anak Anti Kabut', 55000, 'assets/images_sq/mm.png', 1, 55000),
(48, 25, 7, 'Kemeja Flanel Anak Kotak-Kotak Biru', 89000, 'assets/images_sq/mm.png', 1, 89000),
(49, 25, 11, 'Celana Jogger Anak Laki-laki Abu-abu', 65000, 'assets/images_sq/mm.png', 1, 65000),
(50, 26, 21, 'Baju Koko Anak Putih Polos', 105000, 'assets/images_sq/mm.png', 1, 105000),
(51, 26, 42, 'Puzzle Kayu Hewan Laut 48 Keping', 65000, 'assets/images_sq/mm.png', 1, 65000),
(52, 26, 95, 'Pasta Gigi Anak Rasa Buah 75gr', 35000, 'assets/images_sq/mm.png', 2, 70000),
(53, 27, 105, 'Karpet Bermain Anak Foam 150x200cm', 285000, 'assets/images_sq/mm.png', 1, 285000),
(54, 27, 85, 'Bantal Anak Anti Alergi 40x60cm', 85000, 'assets/images_sq/mm.png', 1, 85000),
(55, 27, 84, 'Guling Anak Motif Binatang', 65000, 'assets/images_sq/mm.png', 1, 65000),
(56, 28, 56, 'Buku Mewarnai Anak Seri Dinosaurus', 25000, 'assets/images_sq/mm.png', 2, 50000),
(57, 28, 54, 'Set Crayon 48 Warna Anak', 55000, 'assets/images_sq/mm.png', 1, 55000),
(58, 29, 71, 'Perosotan Plastik Anak Outdoor', 550000, 'assets/images_sq/mm.png', 1, 550000),
(59, 30, 23, 'Seragam Olahraga Anak SD Merah Putih', 110000, 'assets/images_sq/mm.png', 1, 110000),
(60, 30, 80, 'Tas Sekolah Anak SD Motif Superhero', 185000, 'assets/images_sq/mm.png', 1, 185000),
(61, 30, 79, 'Kotak Pensil Anak Motif Karakter', 45000, 'assets/images_sq/mm.png', 1, 45000),
(62, 31, 62, 'Tenda Bermain Anak Indoor Teepee', 275000, 'assets/images_sq/mm.png', 1, 275000),
(63, 31, 63, 'Mainan Pasir Kinetik Warna Set', 85000, 'assets/images_sq/mm.png', 1, 85000),
(64, 32, 9, 'Dress Anak Perempuan Motif Bunga', 110000, 'assets/images_sq/mm.png', 1, 110000),
(65, 32, 28, 'Sandal Crocs Anak Warna-Warni', 85000, 'assets/images_sq/mm.png', 1, 85000),
(66, 33, 29, 'Sepatu Sneakers Anak Laki-laki Putih', 195000, 'assets/images_sq/mm.png', 1, 195000),
(67, 33, 24, 'Setelan Kaos dan Celana Anak 2-3 Tahun', 85000, 'assets/images_sq/mm.png', 1, 85000),
(68, 34, 70, 'Robot Edukatif Interaktif Anak', 395000, 'assets/images_sq/mm.png', 1, 395000),
(69, 34, 60, 'Piano Keyboard Anak 37 Tuts', 275000, 'assets/images_sq/mm.png', 1, 275000),
(70, 35, 58, 'Mainan Dokter-Dokteran Set', 95000, 'assets/images_sq/mm.png', 1, 95000),
(71, 35, 93, 'Sampo Anak 2 in 1 400ml', 55000, 'assets/images_sq/mm.png', 1, 55000),
(72, 36, 105, 'Karpet Bermain Anak Foam 150x200cm', 285000, 'assets/images_sq/mm.png', 1, 285000),
(73, 36, 104, 'Rak Buku Anak Kayu Motif Kartun', 350000, 'assets/images_sq/mm.png', 1, 350000),
(74, 37, 88, 'Helm Sepeda Anak Motif Dinosaurus', 125000, 'assets/images_sq/mm.png', 1, 125000),
(75, 37, 52, 'Skateboard Anak 3-8 Tahun', 215000, 'assets/images_sq/mm.png', 1, 215000),
(76, 38, 71, 'Perosotan Plastik Anak Outdoor', 550000, 'assets/images_sq/mm.png', 1, 550000),
(77, 39, 44, 'Mainan Masak-Masakan Set Lengkap', 135000, 'assets/images_sq/mm.png', 1, 135000),
(78, 39, 16, 'Setelan Baju Tidur Anak Motif Bulan', 95000, 'assets/images_sq/mm.png', 1, 95000),
(79, 40, 46, 'Boneka Bayi Reborn Lucu', 185000, 'assets/images_sq/mm.png', 1, 185000),
(80, 40, 10, 'Dress Anak Tutu Skirt Pink', 125000, 'assets/images_sq/mm.png', 1, 125000),
(81, 40, 39, 'Bando Anak Perempuan Set 5 pcs', 30000, 'assets/images_sq/mm.png', 1, 30000),
(82, 41, 51, 'Sepeda Roda Tiga Anak 2-4 Tahun', 385000, 'assets/images_sq/mm.png', 1, 385000),
(83, 41, 89, 'Pelindung Lutut dan Siku Anak Set', 65000, 'assets/images_sq/mm.png', 1, 65000),
(84, 42, 25, 'Setelan Kaos dan Celana Anak 4-5 Tahun', 90000, 'assets/images_sq/mm.png', 1, 90000),
(85, 42, 31, 'Kaos Kaki Anak Motif Karakter 5 Pasang', 45000, 'assets/images_sq/mm.png', 1, 45000),
(86, 42, 94, 'Sikat Gigi Anak Motif Karakter Soft', 25000, 'assets/images_sq/mm.png', 2, 50000),
(87, 43, 78, 'Lunch Box Anak 3 Sekat Anti Tumpah', 85000, 'assets/images_sq/mm.png', 2, 170000),
(88, 43, 77, 'Botol Minum Stainless Steel Anak 350ml', 95000, 'assets/images_sq/mm.png', 1, 95000),
(89, 44, 101, 'Meja Belajar Anak Lipat Portabel', 225000, 'assets/images_sq/mm.png', 1, 225000),
(90, 44, 102, 'Kursi Belajar Anak Ergonomis', 275000, 'assets/images_sq/mm.png', 1, 275000),
(91, 44, 103, 'Lampu Belajar Anak LED Anti Silau', 115000, 'assets/images_sq/mm.png', 1, 115000),
(92, 45, 59, 'Gitar Mini Anak Kayu', 145000, 'assets/images_sq/mm.png', 1, 145000),
(93, 45, 74, 'Tambur Drum Mini Anak', 85000, 'assets/images_sq/mm.png', 1, 85000),
(94, 46, 18, 'Jaket Hoodie Anak Perempuan Pink', 135000, 'assets/images_sq/mm.png', 1, 135000),
(95, 46, 26, 'Sweater Rajut Anak Musim Dingin', 175000, 'assets/images_sq/mm.png', 1, 175000),
(96, 46, 38, 'Syal Anak Motif Binatang', 40000, 'assets/images_sq/mm.png', 1, 40000),
(97, 47, 52, 'Skateboard Anak 3-8 Tahun', 215000, 'assets/images_sq/mm.png', 1, 215000),
(98, 47, 88, 'Helm Sepeda Anak Motif Dinosaurus', 125000, 'assets/images_sq/mm.png', 1, 125000),
(99, 48, 60, 'Piano Keyboard Anak 37 Tuts', 275000, 'assets/images_sq/mm.png', 1, 275000),
(100, 48, 59, 'Gitar Mini Anak Kayu', 145000, 'assets/images_sq/mm.png', 1, 145000),
(101, 49, 9, 'Dress Anak Perempuan Motif Bunga', 110000, 'assets/images_sq/mm.png', 1, 110000),
(102, 49, 30, 'Sepatu Flat Anak Perempuan Glitter', 175000, 'assets/images_sq/mm.png', 1, 175000),
(103, 50, 70, 'Robot Edukatif Interaktif Anak', 395000, 'assets/images_sq/mm.png', 1, 395000),
(104, 50, 105, 'Karpet Bermain Anak Foam 150x200cm', 285000, 'assets/images_sq/mm.png', 1, 285000),
(105, 51, 48, 'Playdough Set Warna-Warni 12 Warna', 75000, 'assets/images_sq/mm.png', 2, 150000),
(106, 51, 49, 'Slime Kit DIY Anak', 55000, 'assets/images_sq/mm.png', 1, 55000),
(107, 52, 23, 'Seragam Olahraga Anak SD Merah Putih', 110000, 'assets/images_sq/mm.png', 2, 220000),
(108, 52, 80, 'Tas Sekolah Anak SD Motif Superhero', 185000, 'assets/images_sq/mm.png', 1, 185000),
(109, 53, 46, 'Boneka Bayi Reborn Lucu', 185000, 'assets/images_sq/mm.png', 1, 185000),
(110, 54, 45, 'Mobil Remote Control 4WD Anak', 325000, 'assets/images_sq/mm.png', 1, 325000),
(111, 54, 53, 'Bola Basket Mini Anak Ukuran 3', 75000, 'assets/images_sq/mm.png', 1, 75000),
(112, 55, 22, 'Gamis Anak Perempuan Biru Muda', 150000, 'assets/images_sq/mm.png', 1, 150000),
(113, 55, 91, 'Handuk Anak Motif Hewan 60x120cm', 75000, 'assets/images_sq/mm.png', 1, 75000),
(114, 56, 41, 'Lego Duplo Set Rumah 80 Pcs', 250000, 'assets/images_sq/mm.png', 1, 250000),
(115, 56, 72, 'Magnetic Drawing Board Anak', 65000, 'assets/images_sq/mm.png', 1, 65000),
(116, 56, 66, 'Ular Tangga Jumbo Lipat', 85000, 'assets/images_sq/mm.png', 1, 85000),
(117, 57, 5, 'Kaos Unicorn Anak Perempuan Ukuran S', 55000, 'assets/images_sq/mm.png', 1, 55000),
(118, 57, 14, 'Rok Mini Anak Perempuan Polkadot', 55000, 'assets/images_sq/mm.png', 1, 55000),
(119, 57, 39, 'Bando Anak Perempuan Set 5 pcs', 30000, 'assets/images_sq/mm.png', 1, 30000),
(120, 58, 50, 'Trampolin Mini Anak Indoor', 450000, 'assets/images_sq/mm.png', 1, 450000),
(121, 59, 15, 'Setelan Baju Tidur Anak Motif Bintang', 95000, 'assets/images_sq/mm.png', 1, 95000),
(122, 59, 92, 'Sabun Mandi Anak Cair 500ml No Tears', 45000, 'assets/images_sq/mm.png', 1, 45000),
(123, 59, 93, 'Sampo Anak 2 in 1 400ml', 55000, 'assets/images_sq/mm.png', 1, 55000),
(124, 60, 4, 'Kaos Bergambar Astronot Anak Laki-laki', 52000, 'assets/images_sq/mm.png', 1, 52000),
(125, 60, 11, 'Celana Jogger Anak Laki-laki Abu-abu', 65000, 'assets/images_sq/mm.png', 1, 65000),
(126, 60, 97, 'Termometer Digital Anak Aksila', 85000, 'assets/images_sq/mm.png', 1, 85000),
(127, 60, 99, 'Lotion Nyamuk Anak 100ml DEET Free', 55000, 'assets/images_sq/mm.png', 2, 110000),
(128, 61, 105, 'Karpet Bermain Anak Foam 150x200cm', 285000, 'assets/images_sq/mm.png', 1, 285000),
(129, 61, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 1, 45000),
(130, 61, 3, 'Kaos Oblong Anak Dinosaurus Ukuran L', 48000, 'assets/images_sq/mm.png', 1, 48000),
(131, 61, 4, 'Kaos Bergambar Astronot Anak Laki-laki', 52000, 'assets/images_sq/mm.png', 1, 52000),
(132, 61, 41, 'Lego Duplo Set Rumah 80 Pcs', 250000, 'assets/images_sq/mm.png', 1, 250000),
(133, 62, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 'assets/images_sq/mm.png', 2, 90000),
(134, 62, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 2, 90000),
(135, 62, 3, 'Kaos Oblong Anak Dinosaurus Ukuran L', 48000, 'assets/images_sq/mm.png', 1, 48000),
(136, 63, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 2, 90000),
(137, 63, 8, 'Kemeja Flanel Anak Kotak-Kotak Merah', 89000, 'assets/images_sq/mm.png', 1, 89000),
(138, 64, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 1, 45000),
(139, 64, 3, 'Kaos Oblong Anak Dinosaurus Ukuran L', 48000, 'assets/images_sq/mm.png', 2, 96000),
(140, 65, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 2, 90000),
(141, 65, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 'assets/images_sq/mm.png', 1, 45000),
(142, 65, 3, 'Kaos Oblong Anak Dinosaurus Ukuran L', 48000, 'assets/images_sq/mm.png', 1, 48000),
(143, 66, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 'assets/images_sq/mm.png', 1, 45000),
(144, 66, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 1, 45000),
(145, 67, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 'assets/images_sq/mm.png', 1, 45000),
(146, 67, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 1, 45000),
(147, 68, 2, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 'assets/images_sq/mm.png', 1, 45000);

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

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id_payment`, `id_order`, `metode_pembayaran`, `status_pembayaran`, `waktu_bayar`) VALUES
(1, 1, 'Transfer BCA', 'lunas', '2026-05-20 03:30:00'),
(2, 2, 'GoPay', 'lunas', '2026-05-25 04:45:00'),
(3, 3, 'Transfer Mandiri', 'lunas', '2026-05-02 02:20:00'),
(4, 4, 'OVO', 'lunas', '2026-05-08 07:35:00'),
(5, 5, 'Transfer BNI', 'lunas', '2026-05-14 09:20:00'),
(6, 6, 'QRIS', 'lunas', '2026-05-20 03:45:00'),
(7, 7, 'Dana', 'dibayar', '2026-05-22 03:07:49'),
(8, 8, 'Transfer BCA', 'lunas', '2026-05-03 01:30:00'),
(9, 9, 'ShopeePay', 'lunas', '2026-05-08 08:15:00'),
(10, 10, 'GoPay', 'lunas', '2026-05-12 04:15:00'),
(11, 11, 'Transfer Mandiri', 'lunas', '2026-05-18 02:45:00'),
(12, 12, 'QRIS', 'lunas', '2026-05-22 07:15:00'),
(13, 13, 'OVO', 'dibayar', '2026-05-22 02:56:16'),
(14, 14, 'Transfer BCA', 'lunas', '2026-05-01 02:15:00'),
(15, 15, 'Dana', 'lunas', '2026-05-05 06:45:00'),
(16, 16, 'GoPay', 'lunas', '2026-05-08 03:15:00'),
(17, 17, 'Transfer BNI', 'lunas', '2026-05-10 05:00:00'),
(18, 18, 'QRIS', 'lunas', '2026-05-12 07:30:00'),
(19, 19, 'ShopeePay', 'lunas', '2026-05-15 02:45:00'),
(20, 20, 'Transfer BCA', 'lunas', '2026-05-18 09:15:00'),
(21, 21, 'GoPay', 'lunas', '2026-05-20 03:15:00'),
(22, 22, 'Dana', 'lunas', '2026-05-22 04:45:00'),
(23, 23, 'Transfer Mandiri', 'dibayar', '2026-05-22 03:08:15'),
(24, 24, 'QRIS', 'dibayar', '2026-05-22 03:04:16'),
(25, 25, 'OVO', 'lunas', '2026-05-01 03:45:00'),
(26, 26, 'Transfer BCA', 'lunas', '2026-05-03 07:15:00'),
(27, 27, 'GoPay', 'lunas', '2026-05-05 02:15:00'),
(28, 28, 'ShopeePay', 'lunas', '2026-05-07 04:30:00'),
(29, 29, 'Transfer BNI', 'lunas', '2026-05-09 06:45:00'),
(30, 30, 'QRIS', 'lunas', '2026-05-11 03:15:00'),
(31, 31, 'Dana', 'lunas', '2026-05-13 03:00:00'),
(32, 32, 'Transfer BCA', 'lunas', '2026-05-15 07:45:00'),
(33, 33, 'GoPay', 'lunas', '2026-05-17 03:30:00'),
(34, 34, 'Transfer Mandiri', 'lunas', '2026-05-01 02:15:00'),
(35, 35, 'QRIS', 'lunas', '2026-05-05 04:45:00'),
(36, 36, 'OVO', 'lunas', '2026-05-10 06:15:00'),
(37, 37, 'Transfer BCA', 'lunas', '2026-05-15 02:45:00'),
(38, 38, 'ShopeePay', 'lunas', '2026-05-20 07:30:00'),
(39, 39, 'Dana', 'lunas', '2026-05-25 03:15:00'),
(40, 40, 'GoPay', 'selesai', '2026-05-21 18:57:38'),
(41, 41, 'Transfer BNI', 'lunas', '2026-05-03 02:30:00'),
(42, 42, 'QRIS', 'lunas', '2026-05-08 06:45:00'),
(43, 43, 'Transfer BCA', 'lunas', '2026-05-12 03:15:00'),
(44, 44, 'GoPay', 'lunas', '2026-05-18 02:15:00'),
(45, 45, 'Transfer Mandiri', 'lunas', '2026-05-22 07:45:00'),
(46, 46, 'Dana', 'dibayar', '2026-05-22 02:57:05'),
(47, 47, 'OVO', 'lunas', '2026-05-02 02:45:00'),
(48, 48, 'Transfer BCA', 'lunas', '2026-05-08 06:30:00'),
(49, 49, 'ShopeePay', 'lunas', '2026-05-14 03:15:00'),
(50, 50, 'QRIS', 'lunas', '2026-05-20 08:45:00'),
(51, 51, 'GoPay', 'lunas', '2026-05-02 02:15:00'),
(52, 52, 'Transfer BCA', 'lunas', '2026-05-05 04:45:00'),
(53, 53, 'Dana', 'lunas', '2026-05-08 06:15:00'),
(54, 54, 'Transfer Mandiri', 'lunas', '2026-05-10 02:30:00'),
(55, 55, 'OVO', 'lunas', '2026-05-12 03:45:00'),
(56, 56, 'QRIS', 'lunas', '2026-05-13 07:15:00'),
(57, 57, 'GoPay', 'lunas', '2026-05-14 02:45:00'),
(58, 58, 'Transfer BCA', 'pending', '2026-05-14 04:00:00'),
(59, 59, 'ShopeePay', 'pending', '2026-05-15 06:30:00'),
(60, 60, 'Dana', 'pending', '2026-05-15 08:00:00'),
(61, 61, 'E-Wallet', 'selesai', '2026-05-22 02:47:19'),
(62, 62, 'Transfer Bank', 'selesai', '2026-05-22 03:08:10'),
(63, 63, 'Transfer Bank', 'selesai', '2026-05-22 03:08:01'),
(64, 64, 'Transfer Bank', 'selesai', '2026-05-22 03:22:00'),
(65, 65, 'Transfer Bank', 'dibayar', '2026-05-30 04:50:19'),
(66, 66, 'Transfer Bank', 'dibatalkan', '2026-05-30 04:51:53'),
(67, 67, 'Transfer Bank', 'pending', '2026-06-02 01:38:18'),
(68, 68, 'Transfer Bank', 'pending', '2026-06-02 01:42:46');

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

--
-- Dumping data for table `pesan_chat`
--

INSERT INTO `pesan_chat` (`id_chat`, `id_user`, `id_admin`, `isi_chat`, `pengirim`, `waktu_kirim`) VALUES
(1, 1, 1, 'Halo min, saya mau tanya apakah kaos dinosaurus masih ada stoknya untuk ukuran S?', 'customer', '2026-05-21 18:52:09'),
(2, 1, 1, 'Masih ada Kak, stok masih banyak. Mau pesan berapa pcs?', 'customer', '2026-05-21 18:52:09'),
(3, 1, 1, 'Oke saya pesan 2 ya, bagaimana cara ordernya?', 'customer', '2026-05-21 18:52:09'),
(4, 1, 1, 'Bisa langsung klik tombol Beli di halaman produk Kak, nanti tinggal pilih ukuran dan jumlah :)', 'customer', '2026-05-21 18:52:09'),
(5, 3, 1, 'Min, untuk dress motif bunga apakah tersedia ukuran untuk anak usia 5 tahun?', 'customer', '2026-05-21 18:52:09'),
(6, 3, 1, 'Tersedia Kak Rina! Dress kami ada ukuran untuk anak usia 3-8 tahun. Berat badan si kecil berapa Kak?', 'customer', '2026-05-21 18:52:09'),
(7, 3, 1, 'Sekitar 18 kg. Kira-kira ukuran apa yang cocok?', 'customer', '2026-05-21 18:52:09'),
(8, 3, 1, 'Untuk berat 18 kg biasanya cocok ukuran M (5-6 tahun) Kak. Silakan dicoba!', 'customer', '2026-05-21 18:52:09'),
(9, 5, 2, 'Kak admin, gamis biru muda yang saya beli kemarin sudah dikirim belum ya?', 'customer', '2026-05-21 18:52:09'),
(10, 5, 2, 'Sudah Kak Nurul, sudah dikirim tadi pagi via JNE. No resi: JNE123456789. Estimasi 2-3 hari kerja.', 'customer', '2026-05-21 18:52:09'),
(11, 5, 2, 'Oke terima kasih min!', 'customer', '2026-05-21 18:52:09'),
(12, 5, 2, 'Sama-sama Kak, kalau ada pertanyaan lain jangan sungkan ya :)', 'customer', '2026-05-21 18:52:09'),
(13, 7, 2, 'Min, action figure yang saya beli ada catnya yang mengelupas, bisa komplain?', 'customer', '2026-05-21 18:52:09'),
(14, 7, 2, 'Mohon maaf Kak Yuni atas ketidaknyamanannya. Boleh kirim foto kondisi produknya ke sini?', 'customer', '2026-05-21 18:52:09'),
(15, 7, 2, 'Oke saya kirim fotonya ya min', 'customer', '2026-05-21 18:52:09'),
(16, 7, 2, 'Baik Kak, nanti kami proses untuk penggantian atau kompensasi ya. Mohon ditunggu.', 'customer', '2026-05-21 18:52:09'),
(17, 10, 3, 'Min, trampolin yang saya beli bisa dipakai untuk anak sampai berat berapa kg?', 'customer', '2026-05-21 18:52:09'),
(18, 10, 3, 'Halo Kak Agus! Trampolin kami kapasitas maksimal 50 kg Kak, cocok untuk anak usia 3-12 tahun.', 'customer', '2026-05-21 18:52:09'),
(19, 10, 3, 'Oke, cocok untuk anak saya yang 7 tahun. Makasih infonya!', 'customer', '2026-05-21 18:52:09'),
(20, 10, 3, 'Sama-sama Kak! Selamat bermain ya :)', 'customer', '2026-05-21 18:52:09'),
(21, 12, 1, 'Min apakah ada diskon untuk pembelian lebih dari 3 item?', 'customer', '2026-05-21 18:52:09'),
(22, 12, 1, 'Halo Kak Doni! Saat ini kami ada promo gratis ongkir untuk pembelian di atas 200rb Kak.', 'customer', '2026-05-21 18:52:09'),
(23, 12, 1, 'Wah oke bagus! Nanti saya coba order lebih dari itu deh.', 'customer', '2026-05-21 18:52:09'),
(24, 14, 2, 'Min, perosotan yang saya beli instruksi rakitnya kurang jelas, ada video tutorialnya tidak?', 'customer', '2026-05-21 18:52:09'),
(25, 14, 2, 'Halo Kak Bambang! Mohon maaf atas ketidaknyamanannya. Kami sedang siapkan video tutorialnya ya Kak.', 'customer', '2026-05-21 18:52:09'),
(26, 14, 2, 'Oke min, ditunggu ya. Tapi Alhamdulillah sudah bisa rakit sendiri kok.', 'customer', '2026-05-21 18:52:09'),
(27, 14, 2, 'Wah keren Kak Bambang! Semoga anaknya senang ya :)', 'customer', '2026-05-21 18:52:09'),
(28, 17, 3, 'Min, apakah robot edukatif bisa untuk anak umur 3 tahun?', 'customer', '2026-05-21 18:52:09'),
(29, 17, 3, 'Halo Kak Putri! Robot edukatifnya direkomendasikan untuk anak usia 3 tahun ke atas Kak, aman dan edukatif.', 'customer', '2026-05-21 18:52:09'),
(30, 20, 1, 'Min, meja belajar yang saya order kapan sampainya ya? Sudah 3 hari belum sampai.', 'customer', '2026-05-21 18:52:09'),
(31, 20, 1, 'Halo Kak Hendri, kami cek dulu ya. Mohon maaf atas keterlambatannya.', 'customer', '2026-05-21 18:52:09'),
(32, 20, 1, 'Barusan kami cek, ternyata masih di hub Surabaya. Besok pagi sudah dikirim ke alamat Kak. Mohon maaf!', 'customer', '2026-05-21 18:52:09'),
(33, 25, 2, 'Min, sepatu sneakers anaknya ada ukuran 28 tidak?', 'customer', '2026-05-21 18:52:09'),
(34, 25, 2, 'Halo Kak Diana! Ada Kak, ukuran 24-34 tersedia semua. Silakan pilih di halaman produk ya!', 'customer', '2026-05-21 18:52:09'),
(35, 28, 3, 'Min, perosotan outdoor apakah kuat untuk cuaca hujan?', 'customer', '2026-05-21 18:52:09'),
(36, 28, 3, 'Halo Kak Surya! Perosotan kami bahan HDPE tahan cuaca Kak, aman di luar ruangan. Tapi tetap simpan di tempat teduh kalau tidak dipakai ya.', 'customer', '2026-05-21 18:52:09'),
(37, 29, 1, 'Min saya mau tanya baju tidur anak ukuran berapa yang cocok untuk usia 6 tahun?', 'customer', '2026-05-21 18:52:09'),
(38, 29, 1, 'Halo Kak Novita! Untuk usia 6 tahun biasanya cocok ukuran L (6-7 tahun) Kak. Tapi tergantung tinggi dan berat badannya juga ya.', 'customer', '2026-05-21 18:52:09'),
(39, 31, NULL, 'Apakah produk ini ready stok?', 'customer', '2026-05-22 02:45:24'),
(40, 31, NULL, 'Halo Bunda! Semua produk yang bisa dimasukkan ke keranjang ready stok dan siap dikirim ya. Yuk langsung checkout! 📦', 'bot', '2026-05-22 02:45:24'),
(41, 31, NULL, 'test', 'customer', '2026-05-22 02:45:30'),
(42, 31, 4, 'ppppp', 'admin', '2026-05-22 02:46:11'),
(43, 31, NULL, 'Apakah produk ini ready stok?', 'customer', '2026-05-22 03:02:35'),
(44, 31, NULL, 'Halo Bunda! Semua produk yang bisa dimasukkan ke keranjang ready stok dan siap dikirim ya. Yuk langsung checkout! 📦', 'bot', '2026-05-22 03:02:35'),
(45, 31, NULL, 'Bagaimana cara melakukan retur?', 'customer', '2026-05-22 03:02:36'),
(46, 31, NULL, 'Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄', 'bot', '2026-05-22 03:02:36'),
(47, 31, NULL, 'test', 'customer', '2026-05-22 03:02:39'),
(48, 31, 4, 'kiw kiw', 'admin', '2026-05-22 03:04:23'),
(49, 31, NULL, 'Bagaimana cara melakukan retur?', 'customer', '2026-05-22 03:06:15'),
(50, 31, NULL, 'Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄', 'bot', '2026-05-22 03:06:15'),
(51, 31, NULL, 'Berapa biaya ongkir ke Jakarta?', 'customer', '2026-05-22 03:06:17'),
(52, 31, NULL, 'Ongkir ke Jakarta mulai dari Rp 9.000 saja Bun! Dan ada promo Gratis Ongkir lho untuk minimal belanja Rp 100.000! 🚚', 'bot', '2026-05-22 03:06:17'),
(53, 31, NULL, 'admin', 'customer', '2026-05-22 03:06:21'),
(54, 31, 4, 'ui', 'admin', '2026-05-22 03:08:21'),
(55, 31, NULL, 'Bagaimana cara melakukan retur?', 'customer', '2026-05-22 03:20:11'),
(56, 31, NULL, 'Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄', 'bot', '2026-05-22 03:20:11'),
(57, 31, NULL, 'p', 'customer', '2026-05-22 03:20:15'),
(58, 31, 4, 'apa', 'admin', '2026-05-22 03:21:46'),
(59, 31, NULL, 'Bagaimana cara melakukan retur?', 'customer', '2026-05-30 04:38:05'),
(60, 31, NULL, 'Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄', 'bot', '2026-05-30 04:38:05'),
(61, 31, NULL, 'Apakah produk ini ready stok?', 'customer', '2026-06-02 01:37:12'),
(62, 31, NULL, 'Halo Bunda! Semua produk yang bisa dimasukkan ke keranjang ready stok dan siap dikirim ya. Yuk langsung checkout! 📦', 'bot', '2026-06-02 01:37:12'),
(63, 31, NULL, 'Bagaimana cara melakukan retur?', 'customer', '2026-06-02 01:37:17'),
(64, 31, NULL, 'Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Hubungi admin kami dengan melampirkan video unboxing ya! 🔄', 'bot', '2026-06-02 01:37:17'),
(65, 31, NULL, 'Berapa biaya ongkir ke Jakarta?', 'customer', '2026-06-02 01:37:21'),
(66, 31, NULL, 'Ongkir ke Jakarta mulai dari Rp 9.000 saja Bun! Dan ada promo Gratis Ongkir lho untuk minimal belanja Rp 100.000! 🚚', 'bot', '2026-06-02 01:37:21'),
(67, 31, NULL, 'Apakah ada promo hari ini?', 'customer', '2026-06-02 01:37:24'),
(68, 31, NULL, 'Ada dong Bun! Banyak diskon menarik hingga 50% untuk produk mainan dan pakaian anak hari ini. Cek kategori promo ya! 🎁', 'bot', '2026-06-02 01:37:24'),
(69, 31, NULL, 'Apakah ada promo hari ini?', 'customer', '2026-06-02 01:37:25'),
(70, 31, NULL, 'Ada dong Bun! Banyak diskon menarik hingga 50% untuk produk mainan dan pakaian anak hari ini. Cek kategori promo ya! 🎁', 'bot', '2026-06-02 01:37:25'),
(71, 31, NULL, 'barang tidak terkirim', 'customer', '2026-06-02 01:45:54');

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

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `nama_produk`, `harga`, `stok`, `deskripsi`, `foto`) VALUES
(1, 1, 'Kaos Oblong Anak Dinosaurus Ukuran S', 45000, 80, 'Kaos oblong anak motif dinosaurus bahan cotton combed 30s, nyaman dipakai sehari-hari. Ukuran S (2-4 tahun).', 'assets/images_sq/mm.png'),
(2, 1, 'Kaos Oblong Anak Dinosaurus Ukuran M', 45000, 75, 'Kaos oblong anak motif dinosaurus bahan cotton combed 30s, nyaman dipakai sehari-hari. Ukuran M (4-6 tahun).', 'assets/images_sq/mm.png'),
(3, 1, 'Kaos Oblong Anak Dinosaurus Ukuran L', 48000, 60, 'Kaos oblong anak motif dinosaurus bahan cotton combed 30s, nyaman dipakai sehari-hari. Ukuran L (6-8 tahun).', 'assets/images_sq/mm.png'),
(4, 1, 'Kaos Bergambar Astronot Anak Laki-laki', 52000, 70, 'Kaos anak laki-laki motif astronot, bahan cotton 100%, anti luntur, cocok untuk kegiatan sehari-hari.', 'assets/images_sq/mm.png'),
(5, 1, 'Kaos Unicorn Anak Perempuan Ukuran S', 55000, 65, 'Kaos anak perempuan motif unicorn lucu berwarna pink, bahan halus dan lembut di kulit anak.', 'assets/images_sq/mm.png'),
(6, 1, 'Kaos Unicorn Anak Perempuan Ukuran M', 55000, 60, 'Kaos anak perempuan motif unicorn lucu berwarna pink, bahan halus dan lembut di kulit anak. Ukuran M.', 'assets/images_sq/mm.png'),
(7, 1, 'Kemeja Flanel Anak Kotak-Kotak Biru', 89000, 45, 'Kemeja flanel anak motif kotak-kotak warna biru, cocok untuk acara casual maupun semi formal.', 'assets/images_sq/mm.png'),
(8, 1, 'Kemeja Flanel Anak Kotak-Kotak Merah', 89000, 40, 'Kemeja flanel anak motif kotak-kotak warna merah, cocok untuk acara casual maupun semi formal.', 'assets/images_sq/mm.png'),
(9, 1, 'Dress Anak Perempuan Motif Bunga', 110000, 35, 'Dress cantik anak perempuan motif bunga-bunga, bahan katun adem, cocok untuk pesta atau jalan-jalan.', 'assets/images_sq/mm.png'),
(10, 1, 'Dress Anak Tutu Skirt Pink', 125000, 30, 'Dress anak perempuan dengan tutu skirt warna pink yang lucu dan menggemaskan, bahan aman untuk anak.', 'assets/images_sq/mm.png'),
(11, 1, 'Celana Jogger Anak Laki-laki Abu-abu', 65000, 55, 'Celana jogger anak laki-laki warna abu-abu, bahan fleece lembut, elastis dan nyaman untuk bermain.', 'assets/images_sq/mm.png'),
(12, 1, 'Celana Jogger Anak Navy', 65000, 50, 'Celana jogger anak warna navy bahan fleece lembut elastis nyaman untuk aktivitas sehari-hari.', 'assets/images_sq/mm.png'),
(13, 1, 'Celana Pendek Anak Cargo Khaki', 75000, 45, 'Celana pendek cargo anak warna khaki dengan banyak kantong, bahan cotton twill yang kuat dan nyaman.', 'assets/images_sq/mm.png'),
(14, 1, 'Rok Mini Anak Perempuan Polkadot', 55000, 40, 'Rok mini anak perempuan motif polkadot lucu, bahan katun adem dan mudah dicuci.', 'assets/images_sq/mm.png'),
(15, 1, 'Setelan Baju Tidur Anak Motif Bintang', 95000, 50, 'Setelan baju tidur anak terdiri dari atasan dan celana panjang, motif bintang-bintang, bahan katun lembut.', 'assets/images_sq/mm.png'),
(16, 1, 'Setelan Baju Tidur Anak Motif Bulan', 95000, 45, 'Setelan baju tidur anak motif bulan dan bintang, bahan katun lembut, nyaman dipakai tidur.', 'assets/images_sq/mm.png'),
(17, 1, 'Jaket Hoodie Anak Laki-laki Hitam', 135000, 40, 'Jaket hoodie anak laki-laki warna hitam, bahan fleece tebal hangat, ada kantong depan dan tali hoodie.', 'assets/images_sq/mm.png'),
(18, 1, 'Jaket Hoodie Anak Perempuan Pink', 135000, 38, 'Jaket hoodie anak perempuan warna pink, bahan fleece tebal hangat, ada kantong depan dan tali hoodie.', 'assets/images_sq/mm.png'),
(19, 1, 'Jaket Zipper Anak Motif Superhero', 145000, 35, 'Jaket zipper anak motif superhero, bahan cotton fleece, ada dua kantong di sisi kanan kiri.', 'assets/images_sq/mm.png'),
(20, 1, 'Rompi Anak Laki-laki Denim', 120000, 30, 'Rompi anak laki-laki bahan denim berkualitas, tampil keren dan stylish untuk kegiatan sehari-hari.', 'assets/images_sq/mm.png'),
(21, 1, 'Baju Koko Anak Putih Polos', 105000, 40, 'Baju koko anak laki-laki warna putih polos, bahan katun halus, cocok untuk sholat Jumat atau acara islami.', 'assets/images_sq/mm.png'),
(22, 1, 'Gamis Anak Perempuan Biru Muda', 150000, 25, 'Gamis anak perempuan warna biru muda dengan detail bordir cantik, bahan katun premium anti gerah.', 'assets/images_sq/mm.png'),
(23, 1, 'Seragam Olahraga Anak SD Merah Putih', 110000, 60, 'Seragam olahraga anak SD kombinasi merah putih, bahan jersey aktif menyerap keringat.', 'assets/images_sq/mm.png'),
(24, 1, 'Setelan Kaos dan Celana Anak 2-3 Tahun', 85000, 55, 'Setelan kaos dan celana pendek anak usia 2-3 tahun, bahan cotton nyaman untuk kegiatan harian.', 'assets/images_sq/mm.png'),
(25, 1, 'Setelan Kaos dan Celana Anak 4-5 Tahun', 90000, 50, 'Setelan kaos dan celana pendek anak usia 4-5 tahun, bahan cotton nyaman untuk kegiatan harian.', 'assets/images_sq/mm.png'),
(26, 1, 'Sweater Rajut Anak Musim Dingin', 175000, 25, 'Sweater rajut anak hangat untuk musim dingin, bahan wool blend lembut, motif lucu di bagian dada.', 'assets/images_sq/mm.png'),
(27, 1, 'Topi Baseball Anak Motif Kartun', 35000, 80, 'Topi baseball anak motif kartun lucu, bahan cotton, ada klip penyetel ukuran di belakang.', 'assets/images_sq/mm.png'),
(28, 1, 'Sandal Crocs Anak Warna-Warni', 85000, 60, 'Sandal crocs anak warna-warni cerah, bahan EVA ringan dan tahan air, ada lubang ventilasi.', 'assets/images_sq/mm.png'),
(29, 1, 'Sepatu Sneakers Anak Laki-laki Putih', 195000, 40, 'Sepatu sneakers anak laki-laki warna putih, bahan kanvas berkualitas, sol karet anti selip.', 'assets/images_sq/mm.png'),
(30, 1, 'Sepatu Flat Anak Perempuan Glitter', 175000, 35, 'Sepatu flat anak perempuan dengan detail glitter cantik, nyaman dipakai untuk acara formal dan kasual.', 'assets/images_sq/mm.png'),
(31, 1, 'Kaos Kaki Anak Motif Karakter 5 Pasang', 45000, 100, 'Kaos kaki anak motif karakter lucu, 1 pack isi 5 pasang, bahan cotton lembut menyerap keringat.', 'assets/images_sq/mm.png'),
(32, 1, 'Ikat Pinggang Anak Motif Lucu', 25000, 90, 'Ikat pinggang anak motif lucu warna cerah, bahan elastis fleksibel, ukuran bisa disesuaikan.', 'assets/images_sq/mm.png'),
(33, 1, 'Celana Dalam Anak 3 in 1 Laki-laki', 55000, 70, 'Celana dalam anak laki-laki 1 pack isi 3, bahan cotton lembut dan menyerap keringat dengan baik.', 'assets/images_sq/mm.png'),
(34, 1, 'Celana Dalam Anak 3 in 1 Perempuan', 55000, 70, 'Celana dalam anak perempuan 1 pack isi 3, bahan cotton lembut dan menyerap keringat dengan baik.', 'assets/images_sq/mm.png'),
(35, 1, 'Baju Renang Anak Laki-laki Motif Ikan', 115000, 30, 'Baju renang anak laki-laki motif ikan, bahan spandex elastis tahan air, UV protection.', 'assets/images_sq/mm.png'),
(36, 1, 'Baju Renang Anak Perempuan One Piece', 125000, 28, 'Baju renang anak perempuan one piece, bahan spandex elastis tahan air dan UV protection.', 'assets/images_sq/mm.png'),
(37, 1, 'Sarung Tangan Anak Rajut Wol', 35000, 50, 'Sarung tangan anak bahan rajut wol lembut dan hangat, motif lucu dengan warna cerah.', 'assets/images_sq/mm.png'),
(38, 1, 'Syal Anak Motif Binatang', 40000, 45, 'Syal anak motif binatang lucu, bahan fleece hangat dan lembut, ukuran pas untuk anak.', 'assets/images_sq/mm.png'),
(39, 1, 'Bando Anak Perempuan Set 5 pcs', 30000, 85, 'Bando anak perempuan berbagai motif lucu, 1 set isi 5 pcs, bahan elastis dan tidak menyakiti kepala.', 'assets/images_sq/mm.png'),
(40, 1, 'Tas Ransel Anak TK Motif Beruang', 145000, 40, 'Tas ransel anak TK motif beruang lucu, bahan waterproof, ada bantalan di bagian punggung.', 'assets/images_sq/mm.png'),
(41, 2, 'Lego Duplo Set Rumah 80 Pcs', 250000, 30, 'Lego Duplo set rumah isi 80 pcs, cocok untuk anak usia 2-5 tahun, melatih kreativitas dan motorik.', 'assets/images_sq/mm.png'),
(42, 2, 'Puzzle Kayu Hewan Laut 48 Keping', 65000, 50, 'Puzzle kayu bergambar hewan laut sebanyak 48 keping, melatih konsentrasi dan daya ingat anak.', 'assets/images_sq/mm.png'),
(43, 2, 'Puzzle Kayu Hewan Darat 48 Keping', 65000, 45, 'Puzzle kayu bergambar hewan darat 48 keping, melatih konsentrasi dan daya ingat anak.', 'assets/images_sq/mm.png'),
(44, 2, 'Mainan Masak-Masakan Set Lengkap', 135000, 35, 'Set mainan masak-masakan lengkap termasuk kompor, wajan, panci, dan peralatan masak mini.', 'assets/images_sq/mm.png'),
(45, 2, 'Mobil Remote Control 4WD Anak', 325000, 20, 'Mobil remote control 4WD untuk anak, bisa dikendarai di berbagai medan, baterai isi ulang.', 'assets/images_sq/mm.png'),
(46, 2, 'Boneka Bayi Reborn Lucu', 185000, 25, 'Boneka bayi reborn yang lucu dan realistis, bahan vinyl lembut, bisa dimandikan, cocok untuk anak perempuan.', 'assets/images_sq/mm.png'),
(47, 2, 'Action Figure Superhero Set 6 Pcs', 155000, 30, 'Action figure superhero set isi 6 pcs, detail figurin yang bagus, cocok untuk koleksi dan bermain.', 'assets/images_sq/mm.png'),
(48, 2, 'Playdough Set Warna-Warni 12 Warna', 75000, 55, 'Playdough set 12 warna cerah, aman untuk anak, bahan non-toxic, melatih kreativitas motorik halus.', 'assets/images_sq/mm.png'),
(49, 2, 'Slime Kit DIY Anak', 55000, 60, 'Kit membuat slime sendiri untuk anak, termasuk bahan dan petunjuk, aman dan menyenangkan.', 'assets/images_sq/mm.png'),
(50, 2, 'Trampolin Mini Anak Indoor', 450000, 10, 'Trampolin mini untuk indoor diameter 100cm, frame besi kuat dengan jaring pengaman, cocok anak 3-8 tahun.', 'assets/images_sq/mm.png'),
(51, 2, 'Sepeda Roda Tiga Anak 2-4 Tahun', 385000, 12, 'Sepeda roda tiga anak usia 2-4 tahun, rangka besi kuat, dilengkapi keranjang dan klakson.', 'assets/images_sq/mm.png'),
(52, 2, 'Skateboard Anak 3-8 Tahun', 215000, 18, 'Skateboard anak usia 3-8 tahun, deck kayu maple kuat, roda PU anti selip, cocok untuk belajar.', 'assets/images_sq/mm.png'),
(53, 2, 'Bola Basket Mini Anak Ukuran 3', 75000, 40, 'Bola basket mini anak ukuran 3, bahan karet berkualitas, cocok untuk latihan dasar basket.', 'assets/images_sq/mm.png'),
(54, 2, 'Set Crayon 48 Warna Anak', 55000, 70, 'Set crayon 48 warna untuk anak, bahan non-toxic aman, warna cerah dan tidak mudah patah.', 'assets/images_sq/mm.png'),
(55, 2, 'Cat Air Anak Non-Toxic 24 Warna', 45000, 65, 'Cat air anak 24 warna cerah, bahan non-toxic aman untuk anak, mudah dibersihkan dengan air.', 'assets/images_sq/mm.png'),
(56, 2, 'Buku Mewarnai Anak Seri Dinosaurus', 25000, 90, 'Buku mewarnai anak seri dinosaurus, 50 halaman gambar, cocok untuk anak usia 4-8 tahun.', 'assets/images_sq/mm.png'),
(57, 2, 'Buku Mewarnai Anak Seri Putri', 25000, 85, 'Buku mewarnai anak perempuan seri putri, 50 halaman gambar cantik, cocok anak usia 4-8 tahun.', 'assets/images_sq/mm.png'),
(58, 2, 'Mainan Dokter-Dokteran Set', 95000, 40, 'Set mainan dokter-dokteran lengkap, berisi stetoskop, termometer, dan perlengkapan medis mini.', 'assets/images_sq/mm.png'),
(59, 2, 'Gitar Mini Anak Kayu', 145000, 22, 'Gitar mini anak berbahan kayu, 6 senar, ukuran kecil cocok untuk anak belajar bermusik.', 'assets/images_sq/mm.png'),
(60, 2, 'Piano Keyboard Anak 37 Tuts', 275000, 15, 'Piano keyboard anak 37 tuts, bisa merekam dan playback, ada mode demo dan pengaturan volume.', 'assets/images_sq/mm.png'),
(61, 2, 'Koper Trolley Anak Motif Kartun', 350000, 10, 'Koper trolley anak motif kartun lucu, ukuran cabin, bahan ABS kuat, ada kunci TSA.', 'assets/images_sq/mm.png'),
(62, 2, 'Tenda Bermain Anak Indoor Teepee', 275000, 15, 'Tenda bermain anak indoor tipe teepee, rangka kayu dengan kain kanvas, mudah pasang bongkar.', 'assets/images_sq/mm.png'),
(63, 2, 'Mainan Pasir Kinetik Warna Set', 85000, 45, 'Mainan pasir kinetik warna-warni, tidak lengket di tangan, bisa dibentuk macam-macam kreasi.', 'assets/images_sq/mm.png'),
(64, 2, 'Balon Sabun Anak 500ml', 20000, 120, 'Balon sabun anak, 1 botol 500ml, menghasilkan banyak balon, aman dan tidak berbahaya untuk anak.', 'assets/images_sq/mm.png'),
(65, 2, 'Congklak Kayu Tradisional', 55000, 40, 'Permainan congklak kayu tradisional Indonesia, melatih berhitung dan strategi, dilengkapi biji congklak.', 'assets/images_sq/mm.png'),
(66, 2, 'Ular Tangga Jumbo Lipat', 85000, 35, 'Ular tangga jumbo versi lipat, ukuran 100x100cm, bisa dilipat, dilengkapi dadu dan pion.', 'assets/images_sq/mm.png'),
(67, 2, 'Catur Anak Magnet Portable', 75000, 30, 'Set catur anak berbahan magnet portable, kotak bisa jadi papan catur, cocok dibawa bepergian.', 'assets/images_sq/mm.png'),
(68, 2, 'Mainan Tembak Air Pistol Anak', 35000, 80, 'Pistol mainan tembak air anak, kapasitas 200ml, cocok untuk bermain di luar saat musim panas.', 'assets/images_sq/mm.png'),
(69, 2, 'Gelang Karet Craft DIY 500 pcs', 35000, 70, 'Kit membuat gelang karet 500 pcs warna-warni, melatih kreativitas dan motorik halus anak.', 'assets/images_sq/mm.png'),
(70, 2, 'Robot Edukatif Interaktif Anak', 395000, 8, 'Robot edukatif interaktif untuk anak, bisa menari, bercerita, dan merespons suara, USB rechargeable.', 'assets/images_sq/mm.png'),
(71, 2, 'Perosotan Plastik Anak Outdoor', 550000, 6, 'Perosotan plastik anak untuk outdoor, tinggi 120cm, bahan HDPE aman, mudah dipasang dan dibongkar.', 'assets/images_sq/mm.png'),
(72, 2, 'Magnetic Drawing Board Anak', 65000, 50, 'Magnetic drawing board anak, bisa digambar dan dihapus berkali-kali, bebas tinta dan bersih.', 'assets/images_sq/mm.png'),
(73, 2, 'Mainan Lempar Tangkap Bola Set', 45000, 55, 'Set mainan lempar tangkap bola anak, berisi 2 ring net dan 1 bola, latih koordinasi tangan-mata.', 'assets/images_sq/mm.png'),
(74, 2, 'Tambur Drum Mini Anak', 85000, 30, 'Tambur drum mini anak dengan 2 drumstick, bahan plastik kuat, warna cerah menarik perhatian anak.', 'assets/images_sq/mm.png'),
(75, 2, 'Mobil Mobilan Kayu Anak Push Toy', 75000, 40, 'Mobil mobilan kayu untuk anak, bisa didorong dan digenggam, melatih motorik kasar anak.', 'assets/images_sq/mm.png'),
(76, 3, 'Botol Minum Anak Anti Tumpah 500ml', 65000, 80, 'Botol minum anak anti tumpah kapasitas 500ml, bahan BPA-free, ada tali untuk digantung.', 'assets/images_sq/mm.png'),
(77, 3, 'Botol Minum Stainless Steel Anak 350ml', 95000, 60, 'Botol minum stainless steel anak 350ml, menjaga suhu minuman, aman dan tahan lama.', 'assets/images_sq/mm.png'),
(78, 3, 'Lunch Box Anak 3 Sekat Anti Tumpah', 85000, 55, 'Lunch box anak 3 sekat anti tumpah, bahan BPA-free food grade, bisa masuk microwave.', 'assets/images_sq/mm.png'),
(79, 3, 'Kotak Pensil Anak Motif Karakter', 45000, 70, 'Kotak pensil anak motif karakter lucu, ada 2 ritsleting, cukup untuk menyimpan banyak alat tulis.', 'assets/images_sq/mm.png'),
(80, 3, 'Tas Sekolah Anak SD Motif Superhero', 185000, 35, 'Tas sekolah anak SD motif superhero, bahan waterproof, bantalan punggung ergonomis.', 'assets/images_sq/mm.png'),
(81, 3, 'Tas Sekolah Anak Perempuan Motif Bunga', 185000, 38, 'Tas sekolah anak perempuan motif bunga cantik, bahan waterproof, bantalan punggung ergonomis.', 'assets/images_sq/mm.png'),
(82, 3, 'Bantal Perjalanan Anak Motif Kartun', 75000, 45, 'Bantal perjalanan anak motif kartun lucu, bisa dilipat jadi bantal tidur, bahan microfiber lembut.', 'assets/images_sq/mm.png'),
(83, 3, 'Selimut Anak Motif Dinosaurus 100x150cm', 95000, 40, 'Selimut anak motif dinosaurus ukuran 100x150cm, bahan microfiber lembut dan hangat.', 'assets/images_sq/mm.png'),
(84, 3, 'Guling Anak Motif Binatang', 65000, 50, 'Guling anak motif binatang lucu, bahan katun lembut, ukuran pas untuk anak, mudah dicuci.', 'assets/images_sq/mm.png'),
(85, 3, 'Bantal Anak Anti Alergi 40x60cm', 85000, 45, 'Bantal anak anti alergi ukuran 40x60cm, isi dakron premium, sarung bantal motif lucu.', 'assets/images_sq/mm.png'),
(86, 3, 'Tempat Makan Anak Karakter Set', 95000, 50, 'Tempat makan anak karakter lucu set berisi piring, mangkuk, dan sendok garpu, BPA-free.', 'assets/images_sq/mm.png'),
(87, 3, 'Sedotan Silikon Anak Reusable Set', 45000, 65, 'Sedotan silikon anak reusable set isi 4+1 sikat pembersih, aman BPA-free, ramah lingkungan.', 'assets/images_sq/mm.png'),
(88, 3, 'Helm Sepeda Anak Motif Dinosaurus', 125000, 30, 'Helm sepeda anak motif dinosaurus, bahan EPS+ABS, ventilasi udara baik, ukuran adjustable.', 'assets/images_sq/mm.png'),
(89, 3, 'Pelindung Lutut dan Siku Anak Set', 65000, 35, 'Set pelindung lutut dan siku anak untuk sepeda atau skateboard, bahan EVA empuk.', 'assets/images_sq/mm.png'),
(90, 3, 'Kacamata Renang Anak Anti Kabut', 55000, 40, 'Kacamata renang anak anti kabut dan UV, silikon lembut tidak melukai kulit, strap adjustable.', 'assets/images_sq/mm.png'),
(91, 3, 'Handuk Anak Motif Hewan 60x120cm', 75000, 50, 'Handuk anak motif hewan ukuran 60x120cm, bahan cotton terry lembut dan menyerap air dengan baik.', 'assets/images_sq/mm.png'),
(92, 3, 'Sabun Mandi Anak Cair 500ml No Tears', 45000, 80, 'Sabun mandi anak cair 500ml formula no tears, pH balanced, tidak perih di mata, aroma lembut.', 'assets/images_sq/mm.png'),
(93, 3, 'Sampo Anak 2 in 1 400ml', 55000, 75, 'Sampo anak 2 in 1 (sampo dan kondisioner) 400ml, formula lembut no tears, cocok untuk rambut halus.', 'assets/images_sq/mm.png'),
(94, 3, 'Sikat Gigi Anak Motif Karakter Soft', 25000, 100, 'Sikat gigi anak motif karakter lucu, bulu sikat ultra soft, aman untuk gusi anak yang sensitif.', 'assets/images_sq/mm.png'),
(95, 3, 'Pasta Gigi Anak Rasa Buah 75gr', 35000, 90, 'Pasta gigi anak rasa buah-buahan, mengandung fluoride rendah, aman bila tertelan sedikit.', 'assets/images_sq/mm.png'),
(96, 3, 'Gunting Kuku Anak Set 3 in 1', 45000, 60, 'Set perawatan kuku anak 3 in 1: gunting kuku, kikir, dan spatula, bahan stainless food grade.', 'assets/images_sq/mm.png'),
(97, 3, 'Termometer Digital Anak Aksila', 85000, 40, 'Termometer digital anak untuk penggunaan aksila (ketiak), hasil cepat 10 detik, layar LCD besar.', 'assets/images_sq/mm.png'),
(98, 3, 'Obat Nyamuk Bakar Anak Natural', 35000, 70, 'Obat nyamuk bakar formula natural aman untuk anak, bahan aktif serai dan lavender.', 'assets/images_sq/mm.png'),
(99, 3, 'Lotion Nyamuk Anak 100ml DEET Free', 55000, 65, 'Lotion anti nyamuk anak 100ml formula DEET-free aman, mengandung citronella dan eucalyptus.', 'assets/images_sq/mm.png'),
(100, 3, 'Sunscreen Anak SPF 50 100ml', 75000, 50, 'Sunscreen anak SPF 50 volume 100ml, formula lembut bebas paraben dan pewangi, tahan air.', 'assets/images_sq/mm.png'),
(101, 3, 'Meja Belajar Anak Lipat Portabel', 225000, 20, 'Meja belajar anak lipat portabel, ketinggian bisa disesuaikan, ada laci penyimpanan kecil.', 'assets/images_sq/mm.png'),
(102, 3, 'Kursi Belajar Anak Ergonomis', 275000, 15, 'Kursi belajar anak ergonomis, sandaran punggung mendukung postur, ketinggian adjustable.', 'assets/images_sq/mm.png'),
(103, 3, 'Lampu Belajar Anak LED Anti Silau', 115000, 25, 'Lampu belajar anak LED anti silau, cahaya hangat untuk mata, USB charging, portabel.', 'assets/images_sq/mm.png'),
(104, 3, 'Rak Buku Anak Kayu Motif Kartun', 350000, 10, 'Rak buku anak kayu motif kartun lucu, bahan MDF kuat, mudah dirakit, warna cerah.', 'assets/images_sq/mm.png'),
(105, 3, 'Karpet Bermain Anak Foam 150x200cm', 285000, 12, 'Karpet bermain anak bahan foam EVA ukuran 150x200cm, tebal 1cm, motif lucu, anti selip.', 'assets/images_sq/mm.png'),
(106, 2, 'Mainan Iman', 1000, 1, 'Mainan iman', 'assets/images_sq/prod_6a0fc5aab0f9a1.35670037.png');

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

--
-- Dumping data for table `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_order`, `rating`, `komentar`, `foto_review`, `balasan_admin`, `created_at`) VALUES
(1, 1, 1, 1, 5, 'Kaosnya bagus banget, bahan adem dan lembut. Anak saya suka banget pakainya, motif dinosaurusnya lucu!', NULL, 'Terima kasih atas ulasannya Kak Dewi! Kami senang putranya suka. Semoga baju barunya awet ya :)', '2026-05-27 03:00:00'),
(2, 1, 76, 1, 4, 'Botol minumnya tidak tumpah meski dikocok-kocok anak. Bagus tapi tali bawaannya agak kaku.', NULL, 'Terima kasih ulasannya Kak Dewi! Masukan soal tali akan kami perbaiki ke depannya.', '2026-05-27 03:15:00'),
(3, 2, 45, 2, 5, 'RC-nya kencang banget! Anak laki-laki saya sangat senang. Baterai tahan lama juga. Recommended!', NULL, 'Wah senang dengarnya Kak Rizky! Semoga makin asyik mainnya bersama si kecil :D', '2026-05-30 02:00:00'),
(4, 3, 9, 3, 5, 'Dressnya cantik sekali, cocok untuk pesta ulang tahun teman anak saya. Jahitannya rapi dan bahannya adem.', NULL, 'Makasih banyak ulasannya Kak Rina, senang bisa membantu si kecil tampil cantik!', '2026-05-08 04:00:00'),
(5, 4, 41, 4, 4, 'Lego Duplo set rumahnya lengkap, instruksi jelas. Anak 3 tahun saya bisa ikut-ikutan rakit meski perlu bantuan.', NULL, 'Terima kasih Kak Hendra! Lego Duplo memang cocok untuk stimulasi kreativitas si kecil. Selamat bermain!', '2026-05-15 07:00:00'),
(6, 5, 22, 5, 5, 'Gamis anaknya cantik banget, jahitannya rapi, bahannya adem tidak bikin gerah. Anak saya minta pakai terus!', NULL, 'MasyaAllah, terima kasih Kak Nurul! Semoga gamis barunya berkah ya :)', '2026-05-20 02:00:00'),
(7, 6, 51, 6, 5, 'Sepedanya kokoh dan aman. Anak saya (2,5 tahun) langsung bisa naik sendiri. Pengirimannya juga cepat!', NULL, 'Terima kasih Kak Fajar! Senang si kecil sudah bisa sepedaan sendiri. Hati-hati ya :D', '2026-05-28 03:00:00'),
(8, 7, 47, 7, 4, 'Action figure detailnya bagus, warnanya cerah. Tapi salah satu figurin ada catnya yang sedikit mengelupas.', NULL, 'Mohon maaf atas kekurangannya Kak Yuni. Untuk komplain produk bisa DM kami ya, kami siap bantu.', '2026-05-05 04:00:00'),
(9, 8, 60, 8, 5, 'Piano keyboardnya mantap! Anaknya senang banget, sudah hafal beberapa lagu. Suaranya jernih dan tidak berisik.', NULL, 'Wah luar biasa Kak Eko! Siapa tahu si kecil jadi musisi hebat kelak :)', '2026-05-10 02:00:00'),
(10, 9, 78, 9, 5, 'Lunch box-nya anti tumpah beneran, sudah diuji anak saya yang suka guncang-guncang tas. Sekatnya pas banget buat menu makan siang.', NULL, 'Terima kasih Kak Lestari! Senang lunch box-nya bisa membantu si kecil makan lebih tertata :)', '2026-05-15 03:00:00'),
(11, 10, 50, 10, 5, 'Trampolinnya mantap, anak saya main tiap hari. Bisa untuk 2 anak sekaligus. Jaring pengamannya juga aman.', NULL, 'Yeay! Terima kasih Kak Agus, semoga si kecil makin aktif dan sehat :D', '2026-05-20 04:00:00'),
(12, 11, 46, 11, 5, 'Bonekanya sangat realistis dan lucu! Anak perempuan saya langsung jatuh cinta. Kualitas bahan juga bagus.', NULL, 'Terima kasih Kak Mega! Senang si kecil menyukai boneka barunya :)', '2026-05-28 02:00:00'),
(13, 12, 45, 12, 4, 'RC-nya bagus, tapi pengiriman agak lama dari estimasi. Produk oke, packaging aman.', NULL, 'Mohon maaf atas keterlambatan pengirimannya Kak Doni. Kami akan terus tingkatkan pelayanan. Terima kasih!', '2026-05-02 03:00:00'),
(14, 13, 10, 13, 5, 'Dress tutu-nya menggemaskan! Pas dipakai anak saya untuk foto ulang tahun, hasilnya bagus banget. Recommended!', NULL, 'Cantik sekali pasti Kak Sari! Terima kasih sudah berbagi. Selamat ulang tahun untuk si kecil :)', '2026-05-05 04:00:00'),
(15, 1, 17, 14, 5, 'Jaket hoodie-nya tebal dan hangat, anak saya pakai waktu pergi ke Malang dan tidak kedinginan. Kualitas oke!', NULL, 'Terima kasih Kak Dewi! Senang jaket barunya bisa menemani liburan ke Malang :)', '2026-05-10 02:00:00'),
(16, 14, 71, 15, 4, 'Perosotan kokoh dan anak-anak suka. Proses rakit agak susah tapi ada panduan. Overall puas!', NULL, 'Terima kasih Kak Bambang! Kami akan pertimbangkan perbaikan instruksi perakitan. Selamat bermain!', '2026-05-12 04:00:00'),
(17, 15, 81, 16, 5, 'Tas sekolahnya bagus dan waterproof beneran. Anak saya suka motif bunganya, punggungnya juga nyaman ada bantalannya.', NULL, 'Wah senang sekali Kak Fitri! Semoga si kecil semangat sekolah dengan tas barunya :D', '2026-05-15 03:00:00'),
(18, 2, 70, 17, 5, 'Robot edukatifnya keren banget! Anak saya tidak bosan-bosan main sama robot ini. Fitur ceritanya seru.', NULL, 'Terima kasih Kak Rizky! Senang robot edukatifnya jadi teman belajar yang menyenangkan :)', '2026-05-18 02:00:00'),
(19, 16, 51, 18, 5, 'Sepeda roda tiganya bagus, warnanya cerah dan menarik. Anak saya senang banget, langsung minta main terus!', NULL, 'Terima kasih Kak Irwan! Hati-hati main sepedanya ya :D', '2026-05-20 04:00:00'),
(20, 17, 22, 19, 4, 'Gamis anaknya cantik, tapi ukurannya agak kecil dari deskripsi. Bahan bagus dan jahitan rapi.', NULL, 'Mohon maaf soal ukurannya Kak Putri. Silakan DM kami untuk info size chart lebih detail. Terima kasih!', '2026-05-22 03:00:00'),
(21, 18, 104, 20, 5, 'Rak bukunya bagus dan warnanya cerah, cocok untuk kamar anak. Rakitnya mudah, sekitar 30 menit selesai.', NULL, 'Terima kasih Kak Hendri! Semoga si kecil makin rajin membaca dengan rak baru yang kece :)', '2026-05-28 02:00:00'),
(22, 3, 44, 21, 5, 'Set masak-masakannya lengkap banget, anak saya betah main berjam-jam. Bahannya aman dan tidak tajam.', NULL, 'Terima kasih Kak Rina! Chef cilik berbakat nich :D', '2026-05-28 03:00:00'),
(23, 19, 48, 22, 4, 'Playdough warnanya cerah dan tidak cepat kering kalau ditutup rapat. Tapi aromanya agak menyengat di awal.', NULL, 'Terima kasih Kak Wati! Aromanya memang khas, tapi aman ya. Bisa angin-anginkan dulu sebelum main :)', '2026-05-02 04:00:00'),
(24, 20, 61, 23, 5, 'Koper trolley anaknya kuat dan warnanya lucu. Cocok dibawa anak untuk liburan, ukurannya pas buat cabin.', NULL, 'Terima kasih Kak Rudi! Semoga si kecil makin banyak petualangan seru :D', '2026-05-05 02:00:00'),
(25, 21, 29, 24, 5, 'Sepatu sneakers anaknya kualitasnya bagus, tidak licin dan nyaman dipakai seharian. Pengiriman cepat!', NULL, 'Terima kasih Kak Anita! Semoga sepatunya awet dan si kecil makin percaya diri :)', '2026-05-05 03:00:00'),
(26, 22, 21, 26, 5, 'Baju kokonya bagus dan rapi, cocok untuk anak sholat Jumat. Bahannya adem dan tidak bikin gerah.', NULL, 'MasyaAllah terima kasih Kak Joko! Semoga putranya makin rajin beribadah :)', '2026-05-10 02:00:00'),
(27, 23, 56, 28, 5, 'Buku mewarnainya gambarnya lucu-lucu, anak saya suka sekali. Kertasnya tebal tidak tembus ke balik halaman.', NULL, 'Terima kasih Kak Sri! Semoga si kecil makin kreatif ya :D', '2026-05-12 03:00:00'),
(28, 24, 41, 56, 4, 'Lego Duplo bagus, tapi saya pesan 2 hari lalu dan baru sampai sekarang. Produknya sendiri oke.', NULL, 'Mohon maaf atas pengirimannya Kak Tono. Semoga Lego-nya menyenangkan untuk si kecil ya!', '2026-05-15 03:00:00'),
(29, 25, 5, 57, 5, 'Kaos unicornnya lucu banget! Warnanya tidak luntur meski dicuci beberapa kali. Anak saya minta beli lagi.', NULL, 'Terima kasih Kak Diana! Senang kaosnya awet dan disukai si kecil :)', '2026-05-16 02:00:00'),
(30, 31, 2, 63, 4, 'jelek njir', 'assets/images_sq/rev_6a0fc8cc0b3659.92638661.png', NULL, '2026-05-22 03:09:00'),
(31, 31, 8, 63, 4, 'bagus', NULL, 'mang eak', '2026-05-22 03:21:02'),
(32, 31, 2, 64, 5, 'jelek', NULL, NULL, '2026-05-30 04:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Dewi Kusuma', 'dewi.kusuma@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-05 01:12:00'),
(2, 'Rizky Pratama', 'rizky.pratama@yahoo.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-10 02:30:00'),
(3, 'Rina Wulandari', 'rina.wulandari@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-15 03:00:00'),
(4, 'Hendra Wijaya', 'hendra.wijaya@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-20 04:00:00'),
(5, 'Nurul Hidayah', 'nurul.hidayah@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-22 06:00:00'),
(6, 'Fajar Setiawan', 'fajar.setiawan@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-01 01:00:00'),
(7, 'Yuni Astuti', 'yuni.astuti@hotmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-05 02:15:00'),
(8, 'Eko Prasetyo', 'eko.prasetyo@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-10 03:30:00'),
(9, 'Lestari Ningsih', 'lestari.ningsih@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-14 04:00:00'),
(10, 'Agus Salim', 'agus.salim@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-18 07:00:00'),
(11, 'Mega Pratiwi', 'mega.pratiwi@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-20 01:45:00'),
(12, 'Doni Firmansyah', 'doni.firmansyah@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-01 02:00:00'),
(13, 'Sari Indah', 'sari.indah@yahoo.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-05 03:00:00'),
(14, 'Bambang Susilo', 'bambang.susilo@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-10 04:30:00'),
(15, 'Fitri Handayani', 'fitri.handayani@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-12 06:00:00'),
(16, 'Irwan Hakim', 'irwan.hakim@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-15 01:00:00'),
(17, 'Putri Melania', 'putri.melania@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-20 02:30:00'),
(18, 'Hendri Kurniawan', 'hendri.kurniawan@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-25 03:00:00'),
(19, 'Wati Susanti', 'wati.susanti@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-01 04:00:00'),
(20, 'Rudi Hartono', 'rudi.hartono@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-05 07:00:00'),
(21, 'Anita Permata', 'anita.permata@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-08 01:30:00'),
(22, 'Joko Widodo', 'joko.widodo88@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-10 02:00:00'),
(23, 'Sri Wahyuni', 'sri.wahyuni@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-12 03:15:00'),
(24, 'Tono Supriadi', 'tono.supriadi@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-15 04:00:00'),
(25, 'Diana Permatasari', 'diana.permatasari@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-18 06:30:00'),
(26, 'Wahyu Nugroho', 'wahyu.nugroho@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-20 01:00:00'),
(27, 'Endah Sulistyowati', 'endah.sulistyo@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-22 02:30:00'),
(28, 'Surya Darma', 'surya.darma@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-25 03:00:00'),
(29, 'Novita Sari', 'novita.sari@yahoo.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-01 04:00:00'),
(30, 'Dian Rachmawati', 'dian.rachmawati@gmail.com', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', '2026-05-05 07:00:00'),
(31, 'aji@gmail.com', 'aji@gmail.com', 'e6db1ce5668bdf25ef038c4bb8e32d331c9e9b3df629cf61b444cd115c35a7de', '2026-05-22 02:43:01');

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
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `alamat_pengiriman`
--
ALTER TABLE `alamat_pengiriman`
  MODIFY `id_alamat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id_cart` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id_payment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `pesan_chat`
--
ALTER TABLE `pesan_chat`
  MODIFY `id_chat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
