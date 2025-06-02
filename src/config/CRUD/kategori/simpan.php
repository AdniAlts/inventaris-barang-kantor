<?php
include "../koneksi.php";

$id_kategori = htmlspecialchars($_POST['id_kategori']);
$nama = htmlspecialchars($_POST['nama']);

$query = mysqli_query($conn, "INSERT INTO kategori (id_kategori, nama) VALUES ('$id_kategori', '$nama')");

if ($query) {
    echo "Data berhasil disimpan! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>