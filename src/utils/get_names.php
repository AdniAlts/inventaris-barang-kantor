<?php
/**
 * File: get_names.php
 * Deskripsi: Modul ini bertanggung jawab untuk pengambilan nama untuk filter yang ada pada database.
 * Dapat digunakan sebagai tampilan atau penentuan kondisi.
 */
require_once "../config/db.php";

class GetNames {
  /**
   * Fungsi `getCategoryNames()`:
   * Berfungsi dalam pengambilan semua kategori barang dari database.
   *
   * @return array Array berisi data kategori barang yang telah di atur pada database.
   */
  public static function category() {
    $db = new db();
    $categories = [];
    $sql = "SELECT id_kategori, nama FROM kategori";
    $result = $db->conn->query($sql);

    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $categories[] = $row;
        }
      }
    } else {
      error_log("Gagal mengambil kategori dari database. Error: " . $db->conn->error);
    }

    $db->conn->close();
    return $categories;
  }

  /**
   * Fungsi `getStateNames()`:
   * Berfungsi dalam pengambilan semua state barang dari database.
   *
   * @return array Array berisi data state yang telah di atur pada database.
   */
  public static function state() {
    $db = new db();
    $states = [];
    $sql = "SELECT id_state, nama FROM state";
    $result = $db->conn->query($sql);

    if ($result) {
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $states[] = $row;
        }
      }
    } else {
      error_log("Gagal mengambil state dari database. Error: " . $db->conn->error);
    }

    $db->conn->close();
    return $states;
  }
}
?>