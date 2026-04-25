-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 12:52 PM
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
-- Database: `db_gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah_jual` int(11) NOT NULL,
  `total_harga` decimal(10,0) NOT NULL,
  `tanggal_jual` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `id_produk`, `jumlah_jual`, `total_harga`, `tanggal_jual`) VALUES
(1, 1, 10, 0, '2026-03-10 13:32:59'),
(2, 4, 21, 0, '2026-03-14 08:17:36'),
(3, 1, 2, 20000, '2026-03-16 12:29:29'),
(4, 1, 2, 20000, '2026-03-16 12:33:01'),
(5, 1, 2, 20000, '2026-03-16 12:45:17'),
(6, 1, 2, 20000, '2026-03-31 12:22:53'),
(7, 1, 2, 20000, '2026-03-31 12:25:27'),
(8, 1, 2, 20000, '2026-03-31 12:31:13'),
(9, 1, 2, 20000, '2026-03-31 12:36:42'),
(10, 1, 2, 20000, '2026-03-31 12:55:14');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` enum('Elektronik','Alat Kantor','Makanan','Lainnya') NOT NULL,
  `stok_sistem` int(11) NOT NULL,
  `stok_minimum` int(11) NOT NULL,
  `lokasi_rak` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `stok_sistem`, `stok_minimum`, `lokasi_rak`) VALUES
(1, 'hp s 20 samsang', 'Elektronik', 8, 5, 'A-01'),
(2, 'hp ala jovin', 'Elektronik', 20, 5, 'A-02'),
(3, 'tv 20 INC', 'Elektronik', 22, 5, 'A-03'),
(4, 'HP samsang s20', 'Elektronik', 2, 5, 'A33'),
(5, 'laptop gimang 20', 'Elektronik', 11, 5, 'A22');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_stok`
--

CREATE TABLE `riwayat_stok` (
  `id_riwayat` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `tipe_transaksi` enum('Masuk','Keluar','Penyesuaian') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_stok`
--

INSERT INTO `riwayat_stok` (`id_riwayat`, `id_produk`, `tipe_transaksi`, `jumlah`, `keterangan`, `tanggal`) VALUES
(1, 1, 'Penyesuaian', 18, 'kesold tanpa bon', '2026-03-09 13:16:42'),
(2, 2, 'Penyesuaian', 2, 'kesold tanpa bon', '2026-03-10 12:26:26'),
(3, 2, 'Penyesuaian', 0, '', '2026-03-10 12:26:36'),
(4, 1, 'Keluar', 10, 'Penjualan Retail', '2026-03-10 13:32:59'),
(5, 4, 'Keluar', 21, 'Penjualan Retail', '2026-03-14 08:17:36'),
(6, 2, 'Penyesuaian', 20, '', '2026-03-14 08:32:07'),
(7, 4, 'Penyesuaian', 2, '', '2026-03-14 08:51:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('Admin','Staff') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(3, 'carlos', 'admin123', 'Carlos Andrew Salim', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD PRIMARY KEY (`id_riwayat`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
