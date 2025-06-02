<?php
include "../koneksi.php";

$kode_barang = htmlspecialchars($_POST['kode_barang']);
$state_id = htmlspecialchars($_POST['state_id']);
$jenis_id = htmlspecialchars($_POST['jenis_id']);

$query = mysqli_query($conn, "
    INSERT INTO barang (kode_barang, state_id, jenis_id)
    VALUES ('$kode_barang', '$state_id', '$jenis_id')
");

if ($query) {
    echo "Data barang berhasil disimpan! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>
