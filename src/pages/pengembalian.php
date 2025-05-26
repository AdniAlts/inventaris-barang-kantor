<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../output.css" rel="stylesheet">
  <title>Pengembalian| Inventaris</title>
</head>
<body>
    <h1>Pengembalian</h1>
    <form action="" method="get">
        <label for="id_peminjaman">ID_peminjaman :</label><br>
        <select name="id_peminjaman" id="id_peminjaman">
            <option value="">-- PIlih ID Peminjaman --</option>
            <?php
            foreach ($querys as $query) {
                echo "<option value='{$query['id_peminjaman']}'>{$query['id_peminjaman']}</option>";
            }
            ?>
        </select><br>
        <input type="submit" name="pilih" value="pilih">
    </form><br><br>


    <?php
    if (isset($_GET['id_peminjaman'])) {
        echo "<h3>{$_GET['id_peminjaman']}</h3>";
    }
    ?>
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

    <form action="/inventaris-barang-kantor/return" method="post">
        
    </form>
</body>
</html>