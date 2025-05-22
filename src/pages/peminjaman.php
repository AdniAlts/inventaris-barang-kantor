<?php
require_once "../config/db.php";

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
            <tr>
                <td>Meja</td>
                <td>10</td>
            </tr>
            <tr>
                <td>Kursi</td>
                <td>5</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>Jumlah</td>
                <td>15</td>
            </tr>
        </tfoot>
    </table>

    <form action="loan.php" method="post">
        <input type="hidden" name="">
    </form>
</body>
</html>