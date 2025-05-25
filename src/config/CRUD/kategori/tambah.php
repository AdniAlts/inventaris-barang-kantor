<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
</head>
<body>
    <h1>Tambah Kategori</h1>
    <form action="simpan.php" method="POST">
        <label for="id_kategori">ID Kategori:</label><br>
        <input type="text" name="id_kategori" id="id_kategori" required><br><br>

        <label for="nama">Nama Kategori:</label><br>
        <input type="text" name="nama" id="nama" required><br><br>

        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html>