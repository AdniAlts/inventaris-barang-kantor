<?php
require_once "../config/db.php";
$db = new db();

// setcookie('peminjaman', 'y', time() + (-3600 * 24));
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
    <?php
    if (isset($_GET['error'])) {
        echo "<h3>{$_GET['error']}</h3>";
    }

    if (isset($_GET['success'])) {
        echo "<h3>{$_GET['success']}</h3>";
    }
    ?>
    <form action="" method="get">
        <label for="barang">Barang :</label><br>
        <select name="barang" id="barang">
            <option value="">-- PIlih Barang --</option>
            <?php
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

    <form action="/inventaris-barang-kantor/loan" method="post">
        <?php
        if (isset($_COOKIE['peminjaman'])) {
            $data = json_decode($_COOKIE['peminjaman'], true);
            foreach ($data as $index => $item) {
                echo "<input type='hidden' name='barang[$index][nama]' value='{$item['barang']}'>";
                echo "<input type='hidden' name='barang[$index][jumlah]' value='{$item['jumlah']}'>";
            }
            echo "<input type='hidden' name='total' value='{$total}'>";
        }
        ?>
        <button type="submit">Submit Peminjaman</button>
    </form>
</body>

</html>