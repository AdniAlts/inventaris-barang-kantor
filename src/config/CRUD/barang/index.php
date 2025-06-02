<!-- <?php
include "../koneksi.php";

// Ambil data barang
$query = "SELECT barang.kode_barang, barang.state_id, barang.kategori_id, state.nama AS nama_state, kategori.nama AS nama_kategori
          FROM barang
          JOIN state ON barang.state_id = state.id_state
          JOIN kategori ON barang.kategori_id = kategori.id_kategori";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
</head>
<body>
    <h1>Data Barang</h1>
    <a href="tambah.php">Tambah Barang</a>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Kode Barang</th>
            <th>State</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['kode_barang']); ?></td>
            <td><?= htmlspecialchars($row['nama_state']); ?></td>
            <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
            <td>
                <a href="edit.php?kode_barang=<?= urlencode($row['kode_barang']); ?>">Edit</a> |
                <a href="hapus.php?kode_barang=<?= urlencode($row['kode_barang']); ?>" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html> -->

<?php
include "../koneksi.php";

// $query = mysqli_query($conn, "
//     SELECT barang.*, state.nama AS nama_state, jenis.nama AS nama_jenis
//     FROM barang
//     JOIN state ON barang.state_id = state.state_id
//     JOIN jenis ON barang.jenis_id = jenis.id_jenis
// ");
// ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
</head>
<body>
    <h1>Data Barang</h1>
    <a href="tambah.php">Tambah Barang</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>Kode Barang</th>
            <th>State</th>
            <th>Jenis</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                <td><?= htmlspecialchars($row['nama_state']); ?></td>
                <td><?= htmlspecialchars($row['nama_jenis']); ?></td>
                <td>
                    <a href="edit.php?kode=<?= $row['kode_barang']; ?>">Edit</a> | 
                    <a href="hapus.php?kode=<?= $row['kode_barang']; ?>" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
