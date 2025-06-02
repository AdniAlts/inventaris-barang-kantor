<?php
include "../koneksi.php";

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori='$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
</head>
<body>
    <h1>Edit Kategori</h1>
    <form action="update.php" method="POST">
        <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($data['id_kategori']); ?>">
        <label for="nama">Nama Kategori:</label><br>
        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($data['nama']); ?>" required><br><br>

        <button type="submit">Update</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html>