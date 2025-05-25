<?php
include "../koneksi.php";

$id_kategori = htmlspecialchars($_POST['id_kategori']);
$nama = htmlspecialchars($_POST['nama']);

$query = mysqli_query($conn, "UPDATE kategori SET nama='$nama' WHERE id_kategori='$id_kategori'");

if ($query) {
    echo "Data berhasil diupdate! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>