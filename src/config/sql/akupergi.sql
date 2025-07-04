

DROP TABLE IF EXISTS
  admin,
  barang,
  jenis,
  peminjaman,
  peminjaman_detail,
  kategori,
  state,
  user;



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table: kategori
-- --------------------------------------------------------

CREATE TABLE kategori (
  id_kategori INT(11) NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: jenis
-- --------------------------------------------------------

CREATE TABLE jenis (
  id_jenis VARCHAR(10) NOT NULL,
  nama VARCHAR(30) NOT NULL,
  stok INT(11) NOT NULL DEFAULT 0,
  stok_tersedia INT(11) NOT NULL DEFAULT 0,
  kategori_id INT(11) NOT NULL,
  PRIMARY KEY (id_jenis),
  KEY kategori_id (kategori_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: state
-- --------------------------------------------------------

CREATE TABLE state (
  id_state INT(11) NOT NULL AUTO_INCREMENT,
  nama VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: barang
-- --------------------------------------------------------

CREATE TABLE barang (
  kode_barang VARCHAR(30) NOT NULL,
  status ENUM('dipinjam','tersedia') NOT NULL,
  state_id INT(11) NOT NULL,
  gambar_url VARCHAR(150),
  jenis_id VARCHAR(10) NOT NULL,
  PRIMARY KEY (kode_barang),
  KEY state_id (state_id),
  KEY jenis_id (jenis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: peminjaman
-- --------------------------------------------------------

CREATE TABLE peminjaman (
  id_peminjaman VARCHAR(20) NOT NULL,
  deskripsi TEXT NOT NULL,
  tgl_peminjaman DATE NOT NULL,
  tgl_balik DATE DEFAULT NULL,
  total_pinjam INT(11) NOT NULL,
  status ENUM('dipinjam','dikembalikan','menunggu') NOT NULL,
  PRIMARY KEY (id_peminjaman)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: peminjaman_detail
-- --------------------------------------------------------

CREATE TABLE peminjaman_detail (
  id_peminjaman_detail INT(11) NOT NULL AUTO_INCREMENT,
  barang_kode VARCHAR(30) NOT NULL,
  peminjaman_id VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_peminjaman_detail),
  KEY barang_kode (barang_kode),
  KEY peminjaman_id (peminjaman_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: user
-- --------------------------------------------------------

CREATE TABLE user (
  id INT(100) UNSIGNED AUTO_INCREMENT NOT NULL,
  username VARCHAR(100) NOT NULL,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(191) NOT NULL,
  role ENUM('Admin', 'Pegawai') NOT NULL,
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

ALTER TABLE barang ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

COMMIT;

