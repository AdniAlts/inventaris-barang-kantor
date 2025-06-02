<!-- <?php
include "../koneksi.php";

$id_jenis = htmlspecialchars($_POST['id_jenis']);
$nama = htmlspecialchars($_POST['nama']);
$stok = htmlspecialchars($_POST['stok']);
$kategori_id = htmlspecialchars($_POST['kategori_id']);

$query = mysqli_query($conn, "
    UPDATE jenis SET nama='$nama', stok='$stok', kategori_id='$kategori_id'
    WHERE id_jenis='$id_jenis'
");

if ($query) {
    echo "Data berhasil diupdate! <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?> -->