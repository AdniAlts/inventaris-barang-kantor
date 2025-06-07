<?php
// print_r($_POST);
require_once "../config/db.php";
require_once "../config/helper.php";
$db = new db();

$id_peminjaman = $_POST['id_peminjaman'];
// echo $id_peminjaman;
$tgl_balik = date('Y-m-d');

$db->conn->query("UPDATE peminjaman SET tgl_balik = '$tgl_balik', status = 'dikembalikan' WHERE id_peminjaman = '$id_peminjaman'");

$barangs = $_POST['barang'];
foreach ($barangs as $index => $barang) {
    $kode_barang = $barang['barang_kode'];
    echo $kode_barang;
    $db->conn->query("UPDATE barang SET status = 'tersedia' WHERE kode_barang = '$kode_barang'");
}

Helper::route("/pengembalian", [
    "success" => "Barang berhasil dikembalikan"
]);
?>