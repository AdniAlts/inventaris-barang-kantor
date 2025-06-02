<?php
include "../koneksi.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori</title>
</head>
<body>
    <h1>Data Kategori</h1>

    <a href="tambah.php">Tambah Kategori</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>ID Kategori</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori ASC");
        while ($row = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['id_kategori']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id_kategori']; ?>">Edit</a> |
                <a href="hapus.php?id=<?php echo $row['id_kategori']; ?>" onclick="return confirm('Yakin mau hapus?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>