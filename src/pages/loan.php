<?php
$barangs = $_POST['barang']; // array of all items
foreach ($barangs as $item) {
    $nama = $item['nama'];
    $jumlah = $item['jumlah'];
    echo "$nama dan jumlahnya $jumlah";
}
