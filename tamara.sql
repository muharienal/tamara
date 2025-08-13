-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 13, 2025 at 04:24 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tamara`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_log`
--

CREATE TABLE `approval_log` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `role` varchar(32) NOT NULL,
  `status` enum('APPROVED','REJECTED') NOT NULL,
  `created_by` int(11) NOT NULL COMMENT 'users.id',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `id` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudang_tarif`
--

CREATE TABLE `gudang_tarif` (
  `id` int(11) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `jenis_transaksi` enum('BONGKAR','MUAT') NOT NULL,
  `tarif_normal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tarif_lembur` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `jenis_pupuk` varchar(255) NOT NULL,
  `gudang_id` int(11) NOT NULL,
  `jenis_transaksi` enum('BONGKAR','MUAT') NOT NULL,
  `uraian_pekerjaan` varchar(255) NOT NULL,
  `tarif_normal` decimal(10,2) NOT NULL,
  `tarif_lembur` decimal(10,2) NOT NULL,
  `total_bongkar_normal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_bongkar_lembur` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `current_role` enum('ADMIN_GUDANG','KEPALA_GUDANG','ADMIN_WILAYAH','PERWAKILAN_PI','ADMIN_PCS','KEUANGAN','SUPERADMIN') NOT NULL DEFAULT 'ADMIN_GUDANG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_line`
--

CREATE TABLE `invoice_line` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `sto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sto`
--

CREATE TABLE `sto` (
  `id` int(11) NOT NULL,
  `nomor_sto` varchar(50) NOT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `gudang_id` int(11) NOT NULL,
  `jenis_transaksi` enum('BONGKAR','MUAT') NOT NULL,
  `transportir` varchar(100) DEFAULT NULL,
  `tonase_normal` decimal(10,2) DEFAULT 0.00,
  `tonase_lembur` decimal(10,2) DEFAULT 0.00,
  `status` enum('NOT_USED','USED') DEFAULT 'NOT_USED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `jumlah` decimal(10,2) GENERATED ALWAYS AS (`tonase_normal` + `tonase_lembur`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sto_files`
--

CREATE TABLE `sto_files` (
  `id` int(11) NOT NULL,
  `sto_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `mime` varchar(150) DEFAULT NULL,
  `size_bytes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ADMIN_GUDANG','KEPALA_GUDANG','ADMIN_WILAYAH','PERWAKILAN_PI','ADMIN_PCS','KEUANGAN','SUPERADMIN') NOT NULL DEFAULT 'ADMIN_GUDANG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Super Admin', 'superadmin', '$2y$10$mwqWU.iFgDvGF8RShoMl/ORB7AnOWaZwt7Ku3v8SU15MeMH4iG3FO', 'SUPERADMIN'),
(2, 'Admin Gudang', 'admin_gudang', '$2y$12$66yLOsKpfVJ1yn3E8uRrf.ZsEzbv.xp5fFT5OZ/KPCLffzcirR.N.', 'ADMIN_GUDANG'),
(3, 'Kepala Gudang', 'kepala_gudang', '$2y$12$BuyeH3BAR/T4iBSYEw.1QOvNBjHm3cggWBDGe5vOAFD4mOJxt/jPC', 'KEPALA_GUDANG'),
(4, 'Admin Wilayah', 'admin_wilayah', '$2y$12$MqSKT2MIZKjoR7UEw4lQPOpjnaZ2GkfSOtGM/n6c8gAEXW.YB/yr2', 'ADMIN_WILAYAH'),
(5, 'Admin PCS', 'admin_pcs', '$2y$12$EaNAbZUN1HTTAFGBPDRuN.Eya.qowpLMBN13.Y5.M1AQPNv5eHhhu', 'ADMIN_PCS'),
(6, 'Keuangan', 'keuangan', '$2y$12$7uhwSJOLGfYzm7G0uDRvWu359/Si.SY7qrODiXsXMJr/KkILfo8RO', 'KEUANGAN'),
(7, 'Perwakilan PI', 'perwakilan_pi', '$2y$12$5CDrlehv/FkXZfp8/kCyjevSysHPtcnmPzdFEkFu6mrhlzsjJVGS2', 'PERWAKILAN_PI');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_log`
--
ALTER TABLE `approval_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gudang_tarif`
--
ALTER TABLE `gudang_tarif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gudang_id` (`gudang_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gudang_id` (`gudang_id`);

--
-- Indexes for table `invoice_line`
--
ALTER TABLE `invoice_line`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `sto_id` (`sto_id`);

--
-- Indexes for table `sto`
--
ALTER TABLE `sto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_sto` (`nomor_sto`);

--
-- Indexes for table `sto_files`
--
ALTER TABLE `sto_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sto_id` (`sto_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_log`
--
ALTER TABLE `approval_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudang_tarif`
--
ALTER TABLE `gudang_tarif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_line`
--
ALTER TABLE `invoice_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto`
--
ALTER TABLE `sto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_files`
--
ALTER TABLE `sto_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_log`
--
ALTER TABLE `approval_log`
  ADD CONSTRAINT `approval_log_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`),
  ADD CONSTRAINT `approval_log_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `gudang_tarif`
--
ALTER TABLE `gudang_tarif`
  ADD CONSTRAINT `gudang_tarif_ibfk_1` FOREIGN KEY (`gudang_id`) REFERENCES `gudang` (`id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`gudang_id`) REFERENCES `gudang` (`id`);

--
-- Constraints for table `invoice_line`
--
ALTER TABLE `invoice_line`
  ADD CONSTRAINT `invoice_line_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_line_ibfk_2` FOREIGN KEY (`sto_id`) REFERENCES `sto` (`id`);

--
-- Constraints for table `sto_files`
--
ALTER TABLE `sto_files`
  ADD CONSTRAINT `fk_sto_files_sto` FOREIGN KEY (`sto_id`) REFERENCES `sto` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
