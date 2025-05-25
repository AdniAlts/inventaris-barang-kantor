<?php
/**
 * File: search.php
 * Deskripsi: Modul ini bertanggung jawab untuk menangani logika pencarian data dari database.
 * Saat ini, mendukung pencarian berdasarkan 'barang' atau 'all' (yang perilakunya sama dengan 'barang').
 * Fungsi utama dalam file ini adalah `search()`.
 *
 * Melakukan pencarian data di database berdasarkan kata kunci dan tipe yang diberikan.
 *
 * @param string|null $keyword Kata kunci yang akan dicari. Jika null atau string kosong, fungsi akan mengembalikan null.
 * @param string $type Tipe pencarian. Saat ini mendukung 'barang' dan 'all'.
 *                     Untuk tipe 'barang' atau 'all', pencarian dilakukan pada kolom 'nama' di tabel 'barang'.
 * @return mysqli_result|null Mengembalikan objek mysqli_result yang berisi hasil query jika berhasil dan ada data,
 *                            atau null jika kata kunci tidak valid, query gagal dipersiapkan, atau tidak ada hasil.
 *                            Fungsi akan menghentikan eksekusi (die) jika eksekusi query atau pengambilan hasil gagal.
 */
require_once "../config/db.php";

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
    return null;
  }

  $searchTerm = "%" . $keyword . "%";
  $sql->bind_param("s", $searchTerm);

  if ($sql->execute()) // Eksekusi query
    if ($result = $sql->get_result())
      return $result;
    else
      error_log("Gagal mengambil data. Detail error: " . $sql->error);
  else
    error_log("Query gagal terlaksanakan. Detail error: " . $sql->error);
}
?>