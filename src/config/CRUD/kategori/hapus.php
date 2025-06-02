<?php
include "../koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");

if ($query) {
    header("Location: index.php");
} else {
    echo "Gagal hapus: " . mysqli_error($conn);
}
?>