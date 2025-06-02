<?php
/**
 * File: get_names.php
 * Deskripsi: Modul ini bertanggung jawab untuk pengambilan nama untuk filter yang ada pada database.
 * Dapat digunakan sebagai tampilan atau penentuan kondisi.
 */

class GetNames {
  /**
   * Fungsi `category()`:
   * Berfungsi dalam pengambilan semua kategori barang dari database.
   *
   * @param mysqli $connDB Obyek koneksi database yang sudah ada.
   * 
   * @return array Array berisi data kategori barang yang telah di atur pada database.
   */
  public static function category(mysqli $connDB) {
    $categories = [];
    $sql = "SELECT id_kategori, nama FROM kategori";
    $result = $connDB->query($sql);

    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $categories[] = $row;
        }
      }
    } else {
      error_log("Gagal mengambil kategori dari database. Error: " . $connDB->error);
    }

    return $categories;
  }

  /**
   * Fungsi `state()`:
   * Berfungsi dalam pengambilan semua state barang dari database.
   *
   * @param mysqli $connDB Obyek koneksi database yang sudah ada.
   * 
   * @return array Array berisi data state yang telah di atur pada database.
   */
  public static function state(mysqli $connDB) {
    $states = [];
    $sql = "SELECT id_state, nama FROM state";
    $result = $connDB->query($sql);

    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $states[] = $row;
        }
      }
    } else {
      error_log("Gagal mengambil state dari database. Error: " . $connDB->error);
    }

    return $states;
  }

  /**
   * Fungsi `type()`:
   * Berfungsi dalam pengambilan semua jenis barang dari database.
   *
   * @param mysqli $connDB Obyek koneksi database yang sudah ada.
   * 
   * @return array Array berisi data jenis yang telah di atur pada database.
   */
  public static function type(mysqli $connDB) {
    $types = [];
    $sql = "SELECT id_state, nama FROM jenis";
    $result = $connDB->query($sql);

    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $types[] = $row;
        }
      }
    } else {
      error_log("Gagal mengambil state dari database. Error: " . $connDB->error);
    }

    return $types;
  }
}
?>