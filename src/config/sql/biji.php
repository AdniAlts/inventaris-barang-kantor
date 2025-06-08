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
    ['id_jenis' => 'JNS01', 'nama' => 'Laptop', 'kategori_id' => 1, 'stok' => 2],
    ['id_jenis' => 'JNS02', 'nama' => 'Keyboard', 'kategori_id' => 1, 'stok' => 2],
    ['id_jenis' => 'JNS03', 'nama' => 'Monitor', 'kategori_id' => 1, 'stok' => 1],
    ['id_jenis' => 'JNS06', 'nama' => 'Kursi', 'kategori_id' => 2, 'stok' => 2],
    ['id_jenis' => 'JNS09', 'nama' => 'Pulpen', 'kategori_id' => 3, 'stok' => 1],
];
foreach ($jenis as $j) {
    $db->conn->query("INSERT INTO jenis (id_jenis, nama, stok, kategori_id) VALUES (
        '{$j['id_jenis']}', '{$j['nama']}', {$j['stok']}, {$j['kategori_id']}
    )");
}

// Pengguna
$data_pengguna = [
    ['id' => 1, 'nama' => 'Budi Santoso', 'email' => 'budi.s@example.com', 'password' => '123'],
    ['id' => 2, 'nama' => 'Siti Aminah', 'email' => 'siti.a@example.com', 'password' => '123'],
    ['id' => 3, 'nama' => 'Joko Susilo', 'email' => 'joko.s@example.com', 'password' => '123'],
];
foreach ($data_pengguna as $pengguna) {
    $db->conn->query("INSERT INTO user (id, nama, email, password) VALUES (
        {$pengguna['id']}, '{$pengguna['nama']}', '{$pengguna['email']}', '{$pengguna['password']}'
    )");
}

// Admin
$db->conn->query("INSERT INTO admin (id_admin, username, password) VALUES 
    (1, 'budi_santoso', 'admin123'), 
    (3, 'joko_susilo', 'admin123')");

// Barang
$kode = 1;
$barang_list = [];
foreach ($jenis as $j) {
    for ($i = 0; $i < $j['stok']; $i++) {
        $kode_barang = "BRG" . str_pad($kode++, 3, '0', STR_PAD_LEFT);
        // $nama_barang = $j['nama'] . " Unit " . ($i + 1);
        $db->conn->query("INSERT INTO barang (kode_barang, status, state_id, jenis_id) VALUES (
            '{$kode_barang}', 'tersedia', 1, '{$j['id_jenis']}')");
        $barang_list[] = $kode_barang;
    }
}

$peminjaman = [
    [
        'id' => 1,
        'tgl_peminjaman' => '2025-05-05',
        'tgl_balik' => '2025-05-10',
        'total' => 2,
        'status' => 'ready',
        'barang' => [$barang_list[0], $barang_list[1]]
    ],
    [
        'id' => 2,
        'tgl_peminjaman' => '2025-05-12',
        'tgl_balik' => '2025-05-20',
        'total' => 1,
        'status' => 'ready',
        'barang' => [$barang_list[2]]
    ],
    [
        'id' => 3,
        'tgl_peminjaman' => '2025-05-25',
        'tgl_balik' => null,
        'total' => 2,
        'status' => 'dipinjam',
        'barang' => [$barang_list[3], $barang_list[4]]
    ],
];

$detail_id = 1;
foreach ($peminjaman as $p) {
    $tgl_balik = $p['tgl_balik'] ? "'{$p['tgl_balik']}'" : "NULL";
    $db->conn->query("INSERT INTO peminjaman (id_peminjaman, tgl_peminjaman, tgl_balik, total_pinjam, status) 
        VALUES ({$p['id']}, '{$p['tgl_peminjaman']}', {$tgl_balik}, {$p['total']}, '{$p['status']}')");

    foreach ($p['barang'] as $kode_barang) {
        $db->conn->query("INSERT INTO peminjaman_detail (id_peminjaman_detail, barang_kode, peminjaman_id) 
            VALUES ({$detail_id}, '{$kode_barang}', {$p['id']})");
        $detail_id++;
    }
}

$db->conn->query("SET FOREIGN_KEY_CHECKS=1");

echo "Data sudah digenerate\n";
