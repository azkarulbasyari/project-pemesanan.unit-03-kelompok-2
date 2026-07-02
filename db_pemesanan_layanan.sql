-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 05:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pemesanan_layanan`
--

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id` int(11) NOT NULL,
  `nama_layanan` varchar(120) NOT NULL,
  `kategori` varchar(80) NOT NULL,
  `harga` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estimasi_pengerjaan` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status_layanan` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id`, `nama_layanan`, `kategori`, `harga`, `estimasi_pengerjaan`, `deskripsi`, `status_layanan`, `created_at`, `updated_at`) VALUES
(1, 'Servis Laptop', 'Teknologi', 150000.00, '2 Hari', 'Pemeriksaan dan perbaikan laptop ringan.', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(2, 'Instal Ulang Windows', 'Teknologi', 100000.00, '1 Hari', 'Instalasi sistem operasi dan aplikasi dasar.', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(3, 'Desain Poster Digital', 'Desain', 75000.00, '1 Hari', 'Pembuatan desain poster untuk promosi.', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(4, 'Konsultasi Website', 'Konsultasi', 200000.00, '3 Hari', 'Konsultasi kebutuhan website sederhana.', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `no_hp`, `email`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Fauzan', '081234567890', 'ahmad@email.com', 'Banda Aceh', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(2, 'Siti Rahmah', '082345678901', 'siti@email.com', 'Aceh Besar', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(3, 'Muhammad Ridha', '083456789012', 'ridha@email.com', 'Pidie', '2026-07-01 01:34:51', '2026-07-01 01:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `kode_pesanan` varchar(30) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `layanan_id` int(11) NOT NULL,
  `tanggal_pesan` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `total_harga` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_pesanan` enum('baru','diproses','selesai','dibatalkan') DEFAULT 'baru',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `kode_pesanan`, `pelanggan_id`, `layanan_id`, `tanggal_pesan`, `tanggal_selesai`, `catatan`, `total_harga`, `status_pesanan`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PSN-2026-001', 1, 1, '2026-07-01', NULL, 'Laptop lambat dan sering restart.', 150000.00, 'baru', 1, '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(2, 'PSN-2026-002', 2, 2, '2026-07-01', '2026-07-02', 'Backup data terlebih dahulu.', 100000.00, 'diproses', 2, '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(3, 'PSN-2026-003', 3, 3, '2026-07-01', '2026-07-01', 'Poster kegiatan kampus.', 75000.00, 'selesai', 1, '2026-07-01 01:34:51', '2026-07-01 01:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','operator') DEFAULT 'operator',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'admin@layanan.com', '$2y$10$contohpasswordhash', 'admin', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51'),
(2, 'Operator Layanan', 'operator', 'operator@layanan.com', '$2y$10$contohpasswordhash', 'operator', 'aktif', '2026-07-01 01:34:51', '2026-07-01 01:34:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  ADD KEY `fk_pesanan_pelanggan` (`pelanggan_id`),
  ADD KEY `fk_pesanan_layanan` (`layanan_id`),
  ADD KEY `fk_pesanan_user` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_layanan` FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pesanan_pelanggan` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
