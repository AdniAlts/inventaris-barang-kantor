<?php
require_once "../db.php";

$db = new db();
$db->conn->query("SET FOREIGN_KEY_CHECKS=0");

$sqlFile = __DIR__ . '/akupergi.sql';
$sql = file_get_contents($sqlFile);
if ($sql === false) die("Gagal membaca file SQL.");

if ($db->conn->multi_query($sql)) {
    do {
        if ($result = $db->conn->store_result()) $result->free();
    } while ($db->conn->more_results() && $db->conn->next_result());
    echo "SQL sudah dijalankan.\n";
} else {
    die("Gagal bersql");
}

// State barang
$barang = ['Baik', 'Patah', 'Rusak', 'Aus', 'Retak'];
foreach ($barang as $i => $kondisi) {
    $db->conn->query("INSERT INTO state (id_state, nama) VALUES (" . ($i + 1) . ", '" . $db->conn->real_escape_string($kondisi) . "')");
}

// Kategori
$kategori = [
    ['id' => 1, 'nama' => 'Elektronik'],
    ['id' => 2, 'nama' => 'Perabotan'],
    ['id' => 3, 'nama' => 'Perlengkapan Kantor'],
];
foreach ($kategori as $i) {
    $db->conn->query("INSERT INTO kategori (id_kategori, nama) VALUES ({$i['id']}, '" . $db->conn->real_escape_string($i['nama']) . "')");
}

// Jenis
$jenis = [
    ['id_jenis' => 'AA', 'nama' => 'Laptop', 'kategori_id' => 1, 'stok' => 3, 'stok_tersedia' => 3],
    ['id_jenis' => 'AB', 'nama' => 'Keyboard', 'kategori_id' => 1, 'stok' => 3, 'stok_tersedia' => 3],
    ['id_jenis' => 'AC', 'nama' => 'Monitor', 'kategori_id' => 1, 'stok' => 3, 'stok_tersedia' => 3],
    ['id_jenis' => 'AD', 'nama' => 'Kursi', 'kategori_id' => 2, 'stok' => 2, 'stok_tersedia' => 2],
    ['id_jenis' => 'AE', 'nama' => 'Pulpen', 'kategori_id' => 3, 'stok' => 1, 'stok_tersedia' => 1],
];
foreach ($jenis as $j) {
    $db->conn->query("INSERT INTO jenis (id_jenis, nama, stok, stok_tersedia, kategori_id) VALUES (
        '{$j['id_jenis']}', '{$j['nama']}', {$j['stok']}, {$j['stok_tersedia']}, {$j['kategori_id']}
    )");
}

// Pengguna
$data_pengguna = [
    ['id' => 1, 'username' => 'budi_santoso', 'name' => 'Budi Santoso', 'email' => 'budi.s@example.com', 'password' => '123', 'role' => 'Admin'],
    ['id' => 2, 'username' => 'siti_aminah', 'name' => 'Siti Aminah', 'email' => 'siti.a@example.com', 'password' => '123', 'role' => 'Pegawai'],
    ['id' => 3, 'username' => 'joko_susilo', 'name' => 'Joko Susilo', 'email' => 'joko.s@example.com', 'password' => '123', 'role' => 'Pegawai'],
];
foreach ($data_pengguna as $pengguna) {
    $db->conn->query("INSERT INTO user (id, username, name, email, password, role) VALUES (
        {$pengguna['id']}, '{$pengguna['username']}', '{$pengguna['name']}', '{$pengguna['email']}', '{$pengguna['password']}', '{$pengguna['role']}'
    )");
}

// barang
$data_barang = [
    ['kode_barang' => 'AA_001', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AA'],
    ['kode_barang' => 'AA_002', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AA'],
    ['kode_barang' => 'AA_003', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AA'],
    ['kode_barang' => 'AB_001', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AB'],
    ['kode_barang' => 'AB_002', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AB'],
    ['kode_barang' => 'AB_003', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AB'],
    ['kode_barang' => 'AC_001', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AC'],
    ['kode_barang' => 'AC_002', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AC'],
    ['kode_barang' => 'AC_003', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AC'],
    ['kode_barang' => 'AD_001', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AD'],
    ['kode_barang' => 'AD_002', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AD'],
    ['kode_barang' => 'AE_001', 'status' => 'tersedia', 'state_id' => 1, 'jenis_id' => 'AE'],
];
foreach ($data_barang as $barang) {
    $db->conn->query("INSERT INTO barang (kode_barang, status, state_id, jenis_id) VALUES (
        '{$barang['kode_barang']}', '{$barang['status']}', '{$barang['state_id']}', '{$barang['jenis_id']}'
    )");
}

$db->conn->query("SET FOREIGN_KEY_CHECKS=1");

echo "Data sudah digenerate\n";
