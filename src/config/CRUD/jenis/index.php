<?php
include "../koneksi.php";

$query = mysqli_query($conn, "
    SELECT jenis.*, kategori.nama AS nama_kategori
    FROM jenis
    JOIN kategori ON jenis.kategori_id = kategori.id_kategori
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Jenis</title>
</head>
<body>
    <h1>Data Jenis</h1>
    <a href="tambah.php">Tambah Jenis</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID Jenis</th>
            <th>Nama</th>
            <th>Stok</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= htmlspecialchars($row['id_jenis']); ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['stok']); ?></td>
                <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id_jenis']; ?>">Edit</a> | 
                    <a href="hapus.php?id=<?= $row['id_jenis']; ?>" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>