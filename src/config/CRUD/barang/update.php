<?php
include "../koneksi.php";

$kode_barang = htmlspecialchars($_POST['kode_barang']);
$state_id = htmlspecialchars($_POST['state_id']);
$jenis_id = htmlspecialchars($_POST['jenis_id']);

$query = mysqli_query($conn, "
    UPDATE barang SET state_id='$state_id', jenis_id='$jenis_id'
    WHERE kode_barang='$kode_barang'
");

if ($query) {
    echo "Data berhasil diupdate! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>
