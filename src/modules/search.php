<?php
/**
 * File: search.php
 * Deskripsi: Modul ini bertanggung jawab untuk menangani logika pencarian data dari database.
 * Saat ini, mendukung pencarian berdasarkan 'barang' atau 'all'.
 */
require_once "../config/db.php";

/**
 * Fungsi `search()`.
 * Berfungsi dalam melakukan pencarian data di database berdasarkan kata kunci dan tipe yang diberikan.
 *
 * @param string|null $keyword = Kata kunci yang akan dicari. Jika null atau string kosong, fungsi akan mengembalikan null.
 * @param string $type = Tipe pencarian. Saat ini mendukung 'barang' dan 'all'.
 *                       - 'barang': Pencarian dilakukan pada kolom 'nama' di tabel 'barang'.
 *                       - 'all': Pencarian dilakukan pada kolom 'nama' dan 'status' di tabel 'barang',
 *                                serta pada kolom 'nama' di tabel 'state' (kondisi) dan 'kategori'.
 * @return mysqli_result|null Mengembalikan objek mysqli_result yang berisi hasil query jika berhasil dan ada data,
 *                            atau null jika kata kunci tidak valid, query gagal dipersiapkan, atau tidak ada hasil.
 *                            Fungsi akan menghentikan eksekusi (die) jika eksekusi query atau pengambilan hasil gagal.
 * 
 * @example search("kertas", "barang");
 *          => Akan mencari "kertas" yang merupakan nama dari sebuah "barang".
 *          => Mengembalikan hasil pencarian dari query SQL yang berupa kolom data dengan nama barang "kertas" sebagai `mysqli_result`,
 *             atau null jika tidak ada hasil.
 *
 * @example search("alat tulis", "all");
 *          => Akan mencari "alat tulis" dalam seluruh kolom (nama barang, status, kondisi, kategori).
 *          => Mengembalikan hasil pencarian (bisa dari nama barang, status, kondisi, kategori) sebagai `mysqli_result`,
 *             atau null jika tidak ada hasil.
 */
function search($keyword, string $type) {
  // Validasi input
  if (!$keyword)
    return null;

  $db = new db(); // Koneksi dari modul db.php
  $query = match ($type) { // Tipe pencarian
    'barang' => "SELECT b.kode_barang, b.nama, b.status, s.nama 'state', k.nama 'kategori' FROM barang b
                 JOIN state s ON b.state_id = s.id_state JOIN kategori k ON b.kategori_id = k.id_kategori
                 WHERE b.nama LIKE ?",
    'all' => "SELECT b.kode_barang, b.nama, b.status, s.nama 'state', k.nama 'kategori' FROM barang b
              JOIN state s ON b.state_id = s.id_state JOIN kategori k ON b.kategori_id = k.id_kategori
              WHERE b.kode_barang LIKE ? OR b.nama LIKE ? OR b.status LIKE ? OR s.nama LIKE ? OR k.nama LIKE ?",
    default => null
  };

  $sql = $db->conn->prepare($query);
  if (!$sql) {
    error_log("Persiapan query gagal. Detail error: " . $db->conn->error . ". Query: " . $query);
    $db->conn->close();
    return null;
  }

  $searchTerm = "%" . $keyword . "%";
  if ($type === 'barang')
    $sql->bind_param("s", $searchTerm);
  elseif ($type === 'all')
    $sql->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

  if ($sql->execute()) // Eksekusi query
    if ($result = $sql->get_result()) {
      $sql->close();
      $db->conn->close();
      return $result;
    } else {
      error_log("Gagal mengambil data. Detail error: " . $sql->error);
      $sql->close();
      $db->conn->close();
      return null;
    }
  else {
    error_log("Query gagal terlaksanakan. Detail error: " . $sql->error);
    $sql->close();
    $db->conn->close();
    return null;
  }
}
?>