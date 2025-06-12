<?php
include "../koneksi.php";

$id_jenis = htmlspecialchars($_POST['id_jenis']);
$nama = htmlspecialchars($_POST['nama']);
$stok = htmlspecialchars($_POST['stok']);
$kategori_id = htmlspecialchars($_POST['kategori_id']);

$query = mysqli_query($conn, "
    INSERT INTO jenis (id_jenis, nama, stok, kategori_id)
    VALUES ('$id_jenis', '$nama', '$stok', '$kategori_id')
");

if ($query) {
    echo "Data berhasil disimpan! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>