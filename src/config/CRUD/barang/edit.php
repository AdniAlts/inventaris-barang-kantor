<?php
include "../koneksi.php";

$kode = $_GET['kode'];
$query = mysqli_query($conn, "SELECT * FROM barang WHERE kode_barang='$kode'");
$data = mysqli_fetch_assoc($query);

// Ambil state dan jenis buat dropdown
$state = mysqli_query($conn, "SELECT * FROM state");
$jenis = mysqli_query($conn, "SELECT * FROM jenis");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
</head>
<body>
    <h1>Edit Barang</h1>
    <form action="update.php" method="POST">
        <input type="hidden" name="kode_barang" value="<?= htmlspecialchars($data['kode_barang']); ?>">

        <label for="state_id">State:</label><br>
        <select name="state_id" id="state_id" required>
            <?php while($row = mysqli_fetch_assoc($state)): ?>
                <option value="<?= htmlspecialchars($row['state_id']); ?>"
                    <?= $data['state_id'] == $row['state_id'] ? "selected" : ""; ?>>
                    <?= htmlspecialchars($row['nama']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="jenis_id">Jenis:</label><br>
        <select name="jenis_id" id="jenis_id" required>
            <?php while($row = mysqli_fetch_assoc($jenis)): ?>
                <option value="<?= htmlspecialchars($row['id_jenis']); ?>"
                    <?= $data['jenis_id'] == $row['id_jenis'] ? "selected" : ""; ?>>
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
