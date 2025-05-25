<!-- <?php
include "../koneksi.php";

// Ambil data state & kategori untuk dropdown
$stateQuery = mysqli_query($conn, "SELECT * FROM state");
$kategoriQuery = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
</head>
<body>
    <h1>Tambah Barang</h1>
    <form action="simpan.php" method="POST">
        <label for="kode_barang">Kode Barang:</label><br>
        <input type="text" name="kode_barang" id="kode_barang" required><br><br>

        <label for="state_id">State:</label><br>
        <select name="state_id" id="state_id" required>
            <option value="">--Pilih State--</option>
            <?php while($state = mysqli_fetch_assoc($stateQuery)): ?>
                <option value="<?= htmlspecialchars($state['id_state']); ?>"><?= htmlspecialchars($state['nama']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="kategori_id">Kategori:</label><br>
        <select name="kategori_id" id="kategori_id" required>
            <option value="">--Pilih Kategori--</option>
            <?php while($kategori = mysqli_fetch_assoc($kategoriQuery)): ?>
                <option value="<?= htmlspecialchars($kategori['id_kategori']); ?>"><?= htmlspecialchars($kategori['nama']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html> -->

<?php
include "../koneksi.php";

// Ambil data state dan jenis buat dropdown
$state = mysqli_query($conn, "SELECT * FROM state");
$jenis = mysqli_query($conn, "SELECT * FROM jenis");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
</head>
<body>
    <h1>Tambah Barang</h1>
    <form action="simpan.php" method="POST">
        <label for="kode_barang">Kode Barang:</label><br>
        <input type="text" name="kode_barang" id="kode_barang" required><br><br>

        <label for="state_id">State:</label><br>
        <select name="state_id" id="state_id" required>
            <option value="">-- Pilih State --</option>
            <?php while($row = mysqli_fetch_assoc($state)): ?>
                <option value="<?= htmlspecialchars($row['state_id']); ?>">
                    <?= htmlspecialchars($row['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="jenis_id">Jenis:</label><br>
        <select name="jenis_id" id="jenis_id" required>
            <option value="">-- Pilih Jenis --</option>
            <?php while($row = mysqli_fetch_assoc($jenis)): ?>
                <option value="<?= htmlspecialchars($row['id_jenis']); ?>">
                    <?= htmlspecialchars($row['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="index.php">Kembali</a>
</body>
</html>

