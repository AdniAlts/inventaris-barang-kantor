<?php
/**
 * File: search.php
 * Deskripsi: Modul ini bertanggung jawab untuk menangani logika pencarian data dari database.
 * Saat ini, mendukung pencarian berdasarkan 'barang' atau 'all' (yang perilakunya sama dengan 'barang').
 */
require_once "../config/db.php";

/**
 * Fungsi `search()`.
 * Berfungsi dalam melakukan pencarian data di database berdasarkan kata kunci dan tipe yang diberikan.
 *
 * @param string|null $keyword = Kata kunci yang akan dicari. Jika null atau string kosong, fungsi akan mengembalikan null.
 * @param string $type = Tipe pencarian. Saat ini mendukung 'barang' dan 'all'.
 *                       Untuk tipe 'barang' atau 'all', pencarian dilakukan pada kolom 'nama' di tabel 'barang'.
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
 *          => Mengembalikan hasil pencarian dari query SQL yang berupa kolom data dengan kata kunci "alat tulis" (bisa dari nama barang, status, kondisi, kategori) sebagai `mysqli_result`,
 *             atau null jika tidak ada hasil.
 */
function search($keyword, string $type) {
  // Validasi input
  if (!$keyword)
    return null;

  $db = new db(); // Koneksi dari modul db.php
  $query = match ($type) { // Tipe pencarian
    'barang' => "SELECT * FROM barang WHERE nama LIKE ?",
    'all' => "SELECT * FROM barang WHERE nama LIKE ?",
    default => null
  };

  $sql = $db->conn->prepare($query);
  if (!$sql) {
    error_log("Persiapan query gagal. Detail error: " . $db->conn->error . ". Query: " . $query);
    $db->conn->close();
    return null;
  }

  $searchTerm = "%" . $keyword . "%";
  $sql->bind_param("s", $searchTerm);

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