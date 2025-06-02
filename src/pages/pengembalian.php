<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Pengembalian | Inventaris</title>
</head>

<body>
    <h1>Pengembalian</h1>
    <?php
    if (isset($_GET['success'])) {
        echo "<h3>{$_GET['success']}</h3>";
    }
    ?>
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
        <button type="submit">Pilih</button>
    </form><br><br>

    <?php
    if (isset($id)) {
        echo "<h3>$id</h3>";
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($id)) {
                foreach ($querys2 as $query2) {
                    echo "<tr>
                            <td>{$query2['barang_kode']}</td>
                            <td>{$query2['nama']}</td>
                        </tr>";
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Jumlah</th>
                <th><?php if (isset($total_pinjam)) {
                        echo "<h3>$total_pinjam</h3>";
                    } ?></th>
            </tr>
        </tfoot>
    </table>

    <form action="/inventaris-barang-kantor/return" method="post">
        <?php
        if (isset($id)) {
            foreach ($querys2 as $index => $query2) {
                echo "<input type='hidden' name='barang[$index][barang_kode]' value='{$query2['barang_kode']}'>";
            }
            echo "<input type='hidden' name='id_peminjaman' value='{$id}'>";
        }
        ?>
        <button type="submit">Submit Pengembalian</button>
    </form>
</body>

</html>