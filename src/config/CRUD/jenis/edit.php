<?php
include "../koneksi.php";

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM jenis WHERE id_jenis='$id'");
$data = mysqli_fetch_assoc($query);

// Ambil kategori buat dropdown
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Jenis</title>
</head>
<body>
    <h1>Edit Jenis</h1>
    <form action="update.php" method="POST">
        <input type="hidden" name="id_jenis" value="<?= htmlspecialchars($data['id_jenis']); ?>">

        <label for="nama">Nama Jenis:</label><br>
        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($data['nama']); ?>" required><br><br>

        <label for="stok">Stok:</label><br>
        <input type="number" name="stok" id="stok" value="<?= htmlspecialchars($data['stok']); ?>" required><br><br>

        <label for="kategori_id">Kategori:</label><br>
        <select name="kategori_id" id="kategori_id" required>
            <?php while($row = mysqli_fetch_assoc($kategori)): ?>
                <option value="<?= htmlspecialchars($row['id_kategori']); ?>"
                    <?= $data['kategori_id'] == $row['id_kategori'] ? "selected" : ""; ?>>
                    <?= htmlspecialchars($row['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Update</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html>