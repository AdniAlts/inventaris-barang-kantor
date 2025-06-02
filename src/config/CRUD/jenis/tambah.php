<!-- <?php
include "../koneksi.php";

// Ambil data kategori buat dropdown
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jenis</title>
</head>
<body>
    <h1>Tambah Jenis</h1>
    <form action="simpan.php" method="POST">
        <label for="id_jenis">ID Jenis:</label><br>
        <input type="text" name="id_jenis" id="id_jenis" required><br><br>

        <label for="nama">Nama Jenis:</label><br>
        <input type="text" name="nama" id="nama" required><br><br>

        <label for="stok">Stok:</label><br>
        <input type="number" name="stok" id="stok" required><br><br>

        <label for="kategori_id">Kategori:</label><br>
        <select name="kategori_id" id="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while($row = mysqli_fetch_assoc($kategori)): ?>
                <option value="<?= htmlspecialchars($row['id_kategori']); ?>">
                    <?= htmlspecialchars($row['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html> -->