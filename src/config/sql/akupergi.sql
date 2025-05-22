-- MATIKAN "ENABLE FOREIGN KEY CHECK DI MYSQL, ADA DI BAGIAN "SQL", REKOMENDASI COPY AJA INI SEMUA TRS PASTE TRS GO DI BAGIAN SQL

SET GLOBAL FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS 
    admin, 
    barang, 
    jenis, 
    peminjaman, 
    peminjaman_detail, 
    kategori,
    state;
SET GLOBAL FOREIGN_KEY_CHECKS=1;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 11:55 AM
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
-- Database: db_inventaris
--

-- --------------------------------------------------------

--
-- Table structure for table admin
--

CREATE TABLE admin (
  id_admin int(11) NOT NULL,
  username varchar(30) NOT NULL,
  password varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table barang
--

CREATE TABLE barang (
  kode_barang varchar(30) NOT NULL,
  nama varchar(100) NOT NULL,
  status enum('dipinjam','tersedia') NOT NULL,
  state_id int(11) NOT NULL,
  kategori_id varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table kategori
--

CREATE TABLE kategori (
  id_kategori varchar(10) NOT NULL,
  nama varchar(30) NOT NULL,
  stok int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table peminjaman
--

CREATE TABLE peminjaman (
  id_peminjaman int(11) NOT NULL,
  tgl_peminjaman date NOT NULL,
  tgl_balik date DEFAULT NULL,
  total_pinjam int(11) NOT NULL,
  status enum('dipinjam','dikembalikan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table peminjaman_detail
--

CREATE TABLE peminjaman_detail (
  id_peminjaman_detail int(11) NOT NULL,
  jumlah int(11) NOT NULL,
  barang_kode varchar(30) NOT NULL,
  peminjaman_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table state
--

CREATE TABLE state (
  id_state int(11) NOT NULL,
  nama varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table admin
--
ALTER TABLE admin
  ADD PRIMARY KEY (id_admin);

--
-- Indexes for table barang
--
ALTER TABLE barang
  ADD PRIMARY KEY (kode_barang),
  ADD KEY state_id (state_id,kategori_id),
  ADD KEY kategori_id (kategori_id);

--
-- Indexes for table kategori
--
ALTER TABLE kategori
  ADD PRIMARY KEY (id_kategori);

--
-- Indexes for table peminjaman
--
ALTER TABLE peminjaman
  ADD PRIMARY KEY (id_peminjaman);

--
-- Indexes for table peminjaman_detail
--
ALTER TABLE peminjaman_detail
  ADD PRIMARY KEY (id_peminjaman_detail),
  ADD KEY barang_kode (barang_kode),
  ADD KEY peminjaman_id (peminjaman_id);

--
-- Indexes for table state
--
ALTER TABLE state
  ADD PRIMARY KEY (id_state);

--
-- Constraints for dumped tables
--

--
-- Constraints for table barang
--
ALTER TABLE barang
  ADD CONSTRAINT barang_ibfk_1 FOREIGN KEY (state_id) REFERENCES state (id_state),
  ADD CONSTRAINT barang_ibfk_2 FOREIGN KEY (kategori_id) REFERENCES kategori (id_kategori);

--
-- Constraints for table peminjaman_detail
--
ALTER TABLE peminjaman_detail
  ADD CONSTRAINT peminjaman_detail_ibfk_1 FOREIGN KEY (peminjaman_id) REFERENCES peminjaman (id_peminjaman),
  ADD CONSTRAINT peminjaman_detail_ibfk_2 FOREIGN KEY (barang_kode) REFERENCES barang (kode_barang);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;