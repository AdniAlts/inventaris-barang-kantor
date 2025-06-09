<?php
require_once "../config/db.php";
require_once "../config/helper.php";
$db = new db();

$id_peminjaman = $_POST['id_peminjaman'];
$tgl_balik = date('Y-m-d');

// Update status peminjaman
$db->conn->query("UPDATE peminjaman SET tgl_balik = '$tgl_balik', status = 'dikembalikan' WHERE id_peminjaman = '$id_peminjaman'");

$barangs = $_POST['barang'];

foreach ($barangs as $index => $barang) {
    $kode_barang = $barang['barang_kode'];

    // Update status barang jadi 'tersedia'
    $db->conn->query("UPDATE barang SET status = 'tersedia' WHERE kode_barang = '$kode_barang'");

    // Ambil jenis_id dari barang tersebut
    $result = $db->conn->query("SELECT jenis_id FROM barang WHERE kode_barang = '$kode_barang'");
    $row = $result->fetch_assoc();
    $jenis_id = $row['jenis_id'];

    // Update stok_tersedia di jenis +1
    $db->conn->query("UPDATE jenis SET stok_tersedia = stok_tersedia + 1 WHERE id_jenis = '$jenis_id'");
}

Helper::route("/pengembalian", [
    "success" => "Barang berhasil dikembalikan"
]);
