<?php
require_once "../config/db.php";
$db = new db();
// setcookie('peminjaman', 'y', time() + (-3600 * 24));
if (isset($_GET['barang']) && isset($_GET['jumlah'])) {
    $id = $_GET['id_kategori'];
    $barang = $_GET['barang'];
    $jumlah = $_GET['jumlah'];

    // Ambil data cookie yang sudah ada
    $dataPeminjaman = isset($_COOKIE['peminjaman']) ? json_decode($_COOKIE['peminjaman'], true) : [];

    // Tambahkan data baru
    $dataPeminjaman[] = [
        'id' => $id,
        'barang' => $barang,
        'jumlah' => $jumlah
    ];

    // Simpan kembali ke cookie (serialize array ke JSON)
    setcookie('peminjaman', json_encode($dataPeminjaman), time() + (3600 * 24)); // berlaku 1 hari

    // Redirect
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
?>

<!-- buat penyimpanan sementaranya make cookie aja ntar oke
ngambil valuenya dari url GET
terus ntar saat di submit akan otomatis membuat enrty untuk peminjaman dan detail detailnya yang diambil dari cookie sebelumnya -->

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Peminjaman | Inventaris</title>
</head>

<body>
    <h1>Peminjaman</h1>
    <form action="" method="get">
        <label for="barang">Barang :</label><br>
        <select name="barang" id="barang">
            <option value="">-- PIlih Barang --</option>
            <?php
            $querys = $db->conn->query("SELECT k.id_kategori, k.nama, k.stok FROM kategori k JOIN barang b ON k.id_kategori = b.kategori_id WHERE b.status = 'tersedia' AND b.state_id = 1 AND k.stok > 0");
            foreach ($querys as $query) {
                echo "<option value='{$query['nama']}'>{$query['nama']} | {$query['stok']}</option>";
            }
            ?>
        </select><br>
        <label for="jumlah">Jumlah :</label><br>
        <input type="number" name="jumlah" id="jumlah"><br>
        <input type="submit" name="tambah" value="tambah">
    </form><br><br>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Banyak</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            if (isset($_COOKIE['peminjaman'])) {
                $data = json_decode($_COOKIE['peminjaman'], true);
                foreach ($data as $item) {
                    $total += $item['jumlah'];
                    echo "<tr>
                            <td>{$item['barang']}</td>
                            <td>{$item['jumlah']}</td>
                          </tr>";
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Jumlah</th>
                <th><?php echo $total ?></th>
            </tr>
        </tfoot>
    </table>

    <form action="loan.php" method="post">
        <input type="hidden" name="">
    </form>
</body>

</html>