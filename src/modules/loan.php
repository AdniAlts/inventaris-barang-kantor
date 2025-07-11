<?php
require_once "../config/db.php";
require_once "../config/helper.php";
$db = new db();

if (!isset($_POST['total'])) {
    $err = "TIDAK ADA BARANG YANG DIPILIH, Silahkan Pilih barang terlebih dahulu";

    Helper::route("/peminjaman", [
        "error" => $err
    ]);
}

$id_peminjaman = generateIdPeminjaman($db->conn);
$deskripsi = $_POST['deskripsi'];
$tgl_peminjaman = date('Y-m-d');
$total = $_POST['total'];
$status = 'dipinjam';

// Insert ke tabel peminjaman
$db->conn->query("INSERT INTO peminjaman (id_peminjaman, deskripsi, tgl_peminjaman, total_pinjam, status) VALUES ('$id_peminjaman', '$deskripsi', '$tgl_peminjaman', $total, '$status')");

$barangs = $_POST['barang'];
foreach ($barangs as $item) {
    $namaBarang = $item['nama'];
    $jumlahDiminta = (int)$item['jumlah'];

    // Ambil id_kategori berdasarkan nama barang (kategori)
    $stmt = $db->conn->prepare("SELECT id_jenis FROM jenis WHERE nama = ?");
    $stmt->bind_param("s", $namaBarang);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) continue; // Jika nama kategori tidak ditemukan, skip

    $id_jenis = $result['id_jenis']; // Misal: 'AA'

    // Ambil kode barang yang tersedia sesuai jumlah permintaan
    $stmt = $db->conn->prepare("
        SELECT kode_barang 
        FROM barang 
        WHERE jenis_id = ? 
          AND state_id = 1 
          AND status = 'tersedia' 
        ORDER BY kode_barang ASC 
        LIMIT ?
    ");
    $stmt->bind_param("si", $id_jenis, $jumlahDiminta);
    $stmt->execute();
    $barangResult = $stmt->get_result();

    while ($barang = $barangResult->fetch_assoc()) {
        $kode_barang = $barang['kode_barang'];

        // Insert ke peminjaman_detail
        $stmtInsert = $db->conn->prepare("
            INSERT INTO peminjaman_detail (peminjaman_id, barang_kode) 
            VALUES (?, ?)
        ");
        $stmtInsert->bind_param("ss", $id_peminjaman, $kode_barang);
        $stmtInsert->execute();

        // Update status barang menjadi 'dipinjam'
        $db->conn->query("UPDATE barang SET status = 'dipinjam' WHERE kode_barang = '$kode_barang'");

        // Update stok_tersedia pada jenis
        $stmtJenis = $db->conn->prepare("
        UPDATE jenis j
        JOIN barang b ON b.jenis_id = j.id_jenis
        SET j.stok_tersedia = j.stok_tersedia - 1
        WHERE b.kode_barang = ?
        ");
        $stmtJenis->bind_param("s", $kode_barang);
        $stmtJenis->execute();
    }
}

setcookie('peminjaman', 'y', time() + (-3600 * 24));
Helper::route("peminjaman", [
    "success" => "Barang berhasil dipinjam, ID Peminjaman Anda adalah $id_peminjaman"
]);

function generateIdPeminjaman($conn)
{
    require_once "../config/db.php";
    $db = new db();
    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('dmY');

    // Cari jumlah peminjaman yang sudah ada hari ini
    $stmt = $db->conn->prepare("SELECT COUNT(*) as jumlah FROM peminjaman WHERE DATE(tgl_peminjaman) = CURDATE()");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $jumlahHariIni = $result['jumlah'] + 1;

    // Format nomor menjadi tiga digit (001, 002, dst.)
    $nomor = str_pad($jumlahHariIni, 3, '0', STR_PAD_LEFT);

    return "{$tanggal}{$nomor}";
}
