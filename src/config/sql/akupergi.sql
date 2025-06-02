SET GLOBAL FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS
  admin,
  barang,
  jenis,
  peminjaman,
  peminjaman_detail,
  kategori,
  state,
  user;

SET GLOBAL FOREIGN_KEY_CHECKS=1;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table: admin
-- --------------------------------------------------------

CREATE TABLE admin (
  id_admin INT(11) NOT NULL,
  username VARCHAR(30) NOT NULL,
  password VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: kategori
-- --------------------------------------------------------

CREATE TABLE kategori (
  id_kategori INT(11) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: jenis
-- --------------------------------------------------------

CREATE TABLE jenis (
  id_jenis VARCHAR(10) NOT NULL,
  nama VARCHAR(30) NOT NULL,
  stok INT(11) NOT NULL,
  kategori_id INT(11) NOT NULL,
  PRIMARY KEY (id_jenis),
  KEY kategori_id (kategori_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: state
-- --------------------------------------------------------

CREATE TABLE state (
  id_state INT(11) NOT NULL,
  nama VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: barang
-- --------------------------------------------------------

CREATE TABLE barang (
  kode_barang VARCHAR(30) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  status ENUM('dipinjam','tersedia') NOT NULL,
  state_id INT(11) NOT NULL,
  jenis_id VARCHAR(10) NOT NULL,
  PRIMARY KEY (kode_barang),
  KEY state_id (state_id),
  KEY jenis_id (jenis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: peminjaman
-- --------------------------------------------------------

CREATE TABLE peminjaman (
  id_peminjaman INT(11) NOT NULL,
  tgl_peminjaman DATE NOT NULL,
  tgl_balik DATE DEFAULT NULL,
  total_pinjam INT(11) NOT NULL,
  status ENUM('dipinjam','dikembalikan') NOT NULL,
  PRIMARY KEY (id_peminjaman)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: peminjaman_detail
-- --------------------------------------------------------

CREATE TABLE peminjaman_detail (
  id_peminjaman_detail INT(11) NOT NULL,
  jumlah INT(11) NOT NULL,
  barang_kode VARCHAR(30) NOT NULL,
  peminjaman_id INT(11) NOT NULL,
  PRIMARY KEY (id_peminjaman_detail),
  KEY barang_kode (barang_kode),
  KEY peminjaman_id (peminjaman_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: user
-- --------------------------------------------------------

CREATE TABLE user (
  id INT(11) NOT NULL,
  nama VARCHAR(200) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(191) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE barang
  ADD CONSTRAINT barang_ibfk_1 FOREIGN KEY (state_id) REFERENCES state (id_state),
  ADD CONSTRAINT barang_ibfk_2 FOREIGN KEY (jenis_id) REFERENCES jenis (id_jenis);

ALTER TABLE peminjaman_detail
  ADD CONSTRAINT peminjaman_detail_ibfk_1 FOREIGN KEY (peminjaman_id) REFERENCES peminjaman (id_peminjaman),
  ADD CONSTRAINT peminjaman_detail_ibfk_2 FOREIGN KEY (barang_kode) REFERENCES barang (kode_barang);

ALTER TABLE jenis
  ADD CONSTRAINT jenis_ibfk_1 FOREIGN KEY (kategori_id) REFERENCES kategori (id_kategori);

COMMIT;

