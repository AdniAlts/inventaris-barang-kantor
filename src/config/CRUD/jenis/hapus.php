<!-- <?php
include "../koneksi.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "DELETE FROM jenis WHERE id_jenis='$id'");

if ($query) {
    header("Location: index.php");
} else {
    echo "Gagal hapus: " . mysqli_error($conn);
}
?> -->