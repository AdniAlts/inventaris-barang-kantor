<?php
include "../koneksi.php";

$kode = $_GET['kode'];

$query = mysqli_query($conn, "DELETE FROM barang WHERE kode_barang='$kode'");

if ($query) {
    header("Location: index.php");
} else {
    echo "Gagal hapus: " . mysqli_error($conn);
}
?>
