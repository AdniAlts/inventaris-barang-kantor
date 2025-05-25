<?php
require_once "../config/db.php";
$db = new db();

$id_peminjaman = generateIdPeminjaman($conn);
$tgl_peminjaman = date('Ymd');
$total = $_POST['total'];
$status = 'dipinjam'; 

$db->conn->query("INSERT INTO peminjaman (id_peminjaman, tgl_peminjaman, total_pinjam, status) values ($id_peminjaman, $tgl_peminjaman, $total, $status)");


function generateIdPeminjaman($conn) {
    $tanggal = date('dmY'); // Contoh: 25052025

    // Ambil jumlah peminjaman hari ini
    $stmt = $conn->prepare("SELECT COUNT(*) as jumlah FROM peminjaman WHERE DATE(tgl_peminjaman) = CURDATE()");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $jumlahHariIni = $result['jumlah'] + 1;

    // Format nomor jadi tiga digit (001, 002, dst.)
    $nomor = str_pad($jumlahHariIni, 3, '0', STR_PAD_LEFT);

    // Gabungkan jadi integer (misal: 25052025001)
    return (int)($tanggal . $nomor);
}