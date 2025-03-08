-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 04:30 PM
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
-- Database: `blue_lake`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('Head of Production','Co-Founder','HRGA Staff') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'sherlyn', '123456789', '2025-02-10 06:12:08', 'Head of Production'),
(2, 'Hendrik Wijaya', '123456789', '2025-02-12 01:50:09', 'Head of Production'),
(3, 'Agnes Kurnia', '123456789', '2025-02-12 01:51:00', 'Co-Founder'),
(5, 'Nevyorita', '123456789', '2025-02-12 01:51:42', 'HRGA Staff');

-- --------------------------------------------------------

--
-- Table structure for table `foto_lembur`
--

CREATE TABLE `foto_lembur` (
  `foto_id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `jenis_foto` varchar(50) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT NULL COMMENT 'Size of file in bytes',
  `mime_type` varchar(100) DEFAULT NULL COMMENT 'MIME type of the file',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Soft delete flag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `karyawan_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `divisi` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`karyawan_id`, `username`, `password`, `divisi`, `created_at`) VALUES
(1, 'sherlynn', '123456789', 'mahasiswa', '2025-02-10 06:23:23'),
(2, 'hariachmad', '123456789', 'mahasiswa', '2025-03-05 21:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `log_id` int(11) NOT NULL,
  `user_type` enum('admin','karyawan') NOT NULL,
  `user_id` int(11) NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`log_id`, `user_type`, `user_id`, `aktivitas`, `detail`, `created_at`) VALUES
(3, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-08', '2025-02-11 02:48:26'),
(4, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-01', '2025-02-11 02:57:56'),
(5, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-08', '2025-02-11 03:07:24'),
(6, 'admin', 1, 'Persetujuan Head of Production', 'Pengajuan lembur ID 7 disetujui oleh Head of Production', '2025-02-12 02:47:59'),
(7, 'admin', 1, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 7 ditolak', '2025-02-12 02:48:14'),
(8, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-01 menunggu persetujuan', '2025-02-12 02:52:12'),
(9, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-01 menunggu persetujuan', '2025-02-12 02:52:23'),
(10, 'admin', 5, 'Persetujuan HRGA Staff', 'Pengajuan lembur ID 8 disetujui oleh HRGA Staff', '2025-02-12 03:27:23'),
(11, 'admin', 5, 'Persetujuan HRGA Staff', 'Pengajuan lembur ID 9 disetujui oleh HRGA Staff', '2025-02-12 03:35:15'),
(12, 'admin', 3, 'Persetujuan Co-Founder', 'Pengajuan lembur ID 8 disetujui oleh Co-Founder', '2025-02-12 03:44:49'),
(13, 'admin', 3, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 9 ditolak', '2025-02-12 03:44:50'),
(14, 'admin', 2, 'Persetujuan Head of Production', 'Pengajuan lembur ID 8 disetujui oleh Head of Production', '2025-02-12 03:45:12'),
(15, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-01 menunggu persetujuan', '2025-02-12 04:33:22'),
(22, 'admin', 1, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 8 ditolak', '2025-02-13 01:17:12'),
(23, 'admin', 1, 'Persetujuan Head of Production', 'Pengajuan lembur ID 10 disetujui oleh Head of Production', '2025-02-13 01:31:11'),
(24, 'admin', 1, 'Persetujuan Head of Production', 'Pengajuan lembur ID 10 disetujui oleh Head of Production', '2025-02-13 04:45:12'),
(25, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-01 menunggu persetujuan', '2025-02-14 03:16:08'),
(29, 'admin', 1, 'Login', 'Admin berhasil login', '2025-02-14 04:43:24'),
(30, 'admin', 5, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 11 ditolak', '2025-02-14 07:08:34'),
(31, 'admin', 5, 'Penolakan HRGA Staff', 'Pengajuan lembur ID 11 ditolak oleh HRGA Staff', '2025-02-14 07:08:34'),
(32, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-05 menunggu persetujuan', '2025-02-14 08:49:16'),
(33, 'admin', 3, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 12 ditolak', '2025-02-14 08:50:42'),
(34, 'admin', 3, 'Penolakan Co-Founder', 'Pengajuan lembur ID 12 ditolak oleh Co-Founder', '2025-02-14 08:50:42'),
(35, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-06 menunggu persetujuan', '2025-02-14 09:20:32'),
(36, 'karyawan', 1, 'Pengajuan lembur baru', 'Pengajuan lembur untuk tanggal 2025-02-06 menunggu persetujuan', '2025-02-14 09:23:34'),
(37, 'admin', 5, 'Pengajuan lembur ditolak', 'Pengajuan lembur ID 14 ditolak', '2025-02-14 09:32:36'),
(38, 'admin', 5, 'Penolakan HRGA Staff', 'Pengajuan lembur ID 14 ditolak oleh HRGA Staff', '2025-02-14 09:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_lembur`
--

CREATE TABLE `pengajuan_lembur` (
  `pengajuan_id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_lembur` date NOT NULL,
  `jenis_proyek` varchar(50) NOT NULL,
  `nama_proyek` varchar(255) DEFAULT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `durasi_lembur` decimal(10,0) NOT NULL,
  `alasan_lembur` text NOT NULL,
  `daftar_pekerjaan` text NOT NULL,
  `status_pengajuan` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `disetujui_oleh` int(11) DEFAULT NULL,
  `tanggal_persetujuan` timestamp NULL DEFAULT NULL,
  `co_founder_approval_by` int(11) DEFAULT NULL,
  `co_founder_approval_time` datetime DEFAULT NULL,
  `hrga_staff_approval` tinyint(1) DEFAULT 0,
  `hrga_staff_approval_by` int(11) DEFAULT NULL,
  `hrga_staff_approval_time` datetime DEFAULT NULL,
  `ditolak_oleh` int(11) DEFAULT NULL,
  `tanggal_penolakan` datetime DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `rejected_by` varchar(100) DEFAULT NULL,
  `last_updated_by` varchar(100) DEFAULT NULL,
  `last_approval` varchar(255) DEFAULT NULL,
  `approval_status` varchar(255) DEFAULT NULL,
  `foto_sebelum_path` varchar(255) DEFAULT NULL,
  `foto_sesudah_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan_lembur`
--

INSERT INTO `pengajuan_lembur` (`pengajuan_id`, `karyawan_id`, `tanggal_pengajuan`, `tanggal_lembur`, `jenis_proyek`, `nama_proyek`, `jam_mulai`, `jam_selesai`, `durasi_lembur`, `alasan_lembur`, `daftar_pekerjaan`, `status_pengajuan`, `disetujui_oleh`, `tanggal_persetujuan`, `co_founder_approval_by`, `co_founder_approval_time`, `hrga_staff_approval`, `hrga_staff_approval_by`, `hrga_staff_approval_time`, `ditolak_oleh`, `tanggal_penolakan`, `approved_by`, `rejected_by`, `last_updated_by`, `last_approval`, `approval_status`, `foto_sebelum_path`, `foto_sesudah_path`) VALUES
(11, 1, '2025-02-14 03:16:08', '2025-02-01', 'sipil', 'a', '10:15:00', '12:25:00', 2, 'a', 'a', 'ditolak', NULL, NULL, NULL, NULL, 0, NULL, NULL, 5, '2025-02-14 08:08:34', NULL, NULL, NULL, NULL, 'Ditolak oleh HRGA Staff', 'uploads/1739502968_before_Blush_Deluxe.jpg', 'uploads/1739502968_after_Family_Paradise.jpg'),
(12, 1, '2025-02-14 08:49:16', '2025-02-05', 'lainnya', 'a', '15:50:00', '17:55:00', 2, 'a', 'a', 'ditolak', NULL, NULL, NULL, NULL, 0, NULL, NULL, 3, '2025-02-14 09:50:42', NULL, NULL, NULL, NULL, 'Ditolak oleh Co-Founder', 'uploads/1739522956_before_at1.jpg', 'uploads/1739522956_after_at2.jpg'),
(13, 1, '2025-02-14 09:20:32', '2025-02-06', 'furniture', 'a', '16:20:00', '18:30:00', 2, 'a', 'a', 'disetujui', 5, '2025-03-04 02:43:25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh HRGA Staff', 'uploads/1739524832_before_background.png', 'uploads/1739524832_after_banner2.jpg'),
(14, 1, '2025-02-14 09:23:34', '2025-02-06', 'furniture', 'a', '16:20:00', '18:30:00', 3, 'a', 'a', 'ditolak', NULL, NULL, NULL, NULL, 0, NULL, NULL, 5, '2025-02-14 10:32:36', NULL, NULL, NULL, NULL, 'Ditolak oleh HRGA Staff', 'uploads/1739525014_before_background.png', 'uploads/1739525014_after_banner2.jpg'),
(16, 2, '2025-03-05 21:37:16', '2025-03-06', 'sipil', 'Sipil', '04:36:00', '05:36:00', 1, 'Full Time', 'administrasi', 'disetujui', 1, '2025-03-08 14:53:55', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh Head of Production', '../uploads/1741210636_before_logo.png', '../uploads/1741210636_after_logo.png'),
(17, 2, '2025-03-05 21:54:42', '2025-03-06', 'sipil', 'Sipil', '04:54:00', '08:58:00', 4, 'fulltime', 'administrasi', 'disetujui', 1, '2025-03-08 14:53:58', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Disetujui oleh Head of Production', 'uploads/1741211682_before_logo.png', 'uploads/1741211682_after_logo.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_admin_username` (`username`);

--
-- Indexes for table `foto_lembur`
--
ALTER TABLE `foto_lembur`
  ADD PRIMARY KEY (`foto_id`),
  ADD KEY `idx_pengajuan_foto` (`pengajuan_id`),
  ADD KEY `idx_path_file` (`path_file`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`karyawan_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_karyawan_username` (`username`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `pengajuan_lembur`
--
ALTER TABLE `pengajuan_lembur`
  ADD PRIMARY KEY (`pengajuan_id`),
  ADD KEY `karyawan_id` (`karyawan_id`),
  ADD KEY `disetujui_oleh` (`disetujui_oleh`),
  ADD KEY `idx_pengajuan_tanggal` (`tanggal_lembur`),
  ADD KEY `idx_pengajuan_status` (`status_pengajuan`),
  ADD KEY `fk_co_founder_approval` (`co_founder_approval_by`),
  ADD KEY `fk_hrga_staff_approval` (`hrga_staff_approval_by`),
  ADD KEY `fk_ditolak_oleh` (`ditolak_oleh`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `foto_lembur`
--
ALTER TABLE `foto_lembur`
  MODIFY `foto_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `karyawan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `pengajuan_lembur`
--
ALTER TABLE `pengajuan_lembur`
  MODIFY `pengajuan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `foto_lembur`
--
ALTER TABLE `foto_lembur`
  ADD CONSTRAINT `fk_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_lembur` (`pengajuan_id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_lembur`
--
ALTER TABLE `pengajuan_lembur`
  ADD CONSTRAINT `fk_co_founder_approval` FOREIGN KEY (`co_founder_approval_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ditolak_oleh` FOREIGN KEY (`ditolak_oleh`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hrga_staff_approval` FOREIGN KEY (`hrga_staff_approval_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengajuan_lembur_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`karyawan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengajuan_lembur_ibfk_2` FOREIGN KEY (`disetujui_oleh`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
