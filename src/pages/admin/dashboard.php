<?php
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . '/../../config/db.php';

// Koneksi Database menggunakan class db
$db = new db();

// Cek koneksi
if ($db->conn->connect_error) {
  die("Koneksi database gagal: " . $db->conn->connect_error);
}

// Query untuk mendapatkan data dashboard
// 1. Data peminjaman yang sedang berlangsung
$queryPeminjaman = "SELECT * FROM peminjaman WHERE status = 'dipinjam' ORDER BY tgl_peminjaman DESC LIMIT 5";
$resultPeminjaman = $db->conn->query($queryPeminjaman);
$dataPeminjaman = [];
if ($resultPeminjaman && $resultPeminjaman->num_rows > 0) {
  while ($row = $resultPeminjaman->fetch_assoc()) {
    $dataPeminjaman[] = $row;
  }
}

// 2. Data 5 kategori pertama
$queryKategori = "SELECT * FROM kategori ORDER BY id_kategori LIMIT 5";
$resultKategori = $db->conn->query($queryKategori);
$dataKategori = [];
if ($resultKategori && $resultKategori->num_rows > 0) {
  while ($row = $resultKategori->fetch_assoc()) {
    $dataKategori[] = $row;
  }
}

// 3. Data 5 jenis pertama dengan informasi kategori
$queryJenis = "SELECT j.*, k.nama as nama_kategori FROM jenis j 
               JOIN kategori k ON j.kategori_id = k.id_kategori 
               ORDER BY j.id_jenis LIMIT 5";
$resultJenis = $db->conn->query($queryJenis);
$dataJenis = [];
if ($resultJenis && $resultJenis->num_rows > 0) {
  while ($row = $resultJenis->fetch_assoc()) {
    $dataJenis[] = $row;
  }
}

// 4. Data 5 kondisi pertama
$queryState = "SELECT * FROM state ORDER BY id_state LIMIT 5";
$resultState = $db->conn->query($queryState);
$dataState = [];
if ($resultState && $resultState->num_rows > 0) {
  while ($row = $resultState->fetch_assoc()) {
    $dataState[] = $row;
  }
}

// 5. Data 5 barang pertama dengan informasi lengkap
$queryBarang = "SELECT b.*, j.nama as nama_jenis, k.nama as nama_kategori, s.nama as nama_state 
                FROM barang b 
                JOIN jenis j ON b.jenis_id = j.id_jenis 
                JOIN kategori k ON j.kategori_id = k.id_kategori 
                JOIN state s ON b.state_id = s.id_state 
                ORDER BY b.kode_barang LIMIT 5";
$resultBarang = $db->conn->query($queryBarang);
$dataBarang = [];
if ($resultBarang && $resultBarang->num_rows > 0) {
  while ($row = $resultBarang->fetch_assoc()) {
    $dataBarang[] = $row;
  }
}

// 6. Statistik umum
$queryStats = "SELECT 
                (SELECT COUNT(*) FROM barang WHERE status = 'tersedia') as total_tersedia,
                (SELECT COUNT(*) FROM barang WHERE status = 'dipinjam') as total_dipinjam,
                (SELECT COUNT(*) FROM barang) as total_barang,
                (SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam') as peminjaman_aktif";
$resultStats = $db->conn->query($queryStats);
$stats = [];
if ($resultStats && $resultStats->num_rows > 0) {
  $stats = $resultStats->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <!-- Header Dashboard -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Admin</h1>
      <p class="text-gray-600">Selamat datang di sistem inventaris barang kantor</p>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="mb-8">
      <div class="flex flex-wrap gap-4">
        <a href="<?= Helper::basePath(); ?>barang"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Tambah Barang
        </a>
        <a href="<?= Helper::basePath(); ?>jenis"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          Kelola Jenis
        </a>
        <a href="<?= Helper::basePath(); ?>kategori"
          class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
          </svg>
          Kelola Kategori
        </a>
      </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Barang</p>
            <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_barang'] ?? 0 ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Barang Tersedia</p>
            <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_tersedia'] ?? 0 ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Sedang Dipinjam</p>
            <p class="text-2xl font-semibold text-gray-900"><?= $stats['total_dipinjam'] ?? 0 ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Peminjaman Aktif</p>
            <p class="text-2xl font-semibold text-gray-900"><?= $stats['peminjaman_aktif'] ?? 0 ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Peminjaman Aktif -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Peminjaman Aktif</h2>
        <a href="<?= Helper::basePath(); ?>admin/peminjaman" class="text-blue-600 hover:text-blue-800 font-medium">
          Lihat Semua →
        </a>
      </div>

      <?php if (empty($dataPeminjaman)): ?>
        <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Peminjaman Aktif</h3>
          <p class="text-gray-600">Semua barang sedang dalam kondisi tersedia.</p>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($dataPeminjaman as $peminjaman): ?>
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
              <div class="p-6">
                <!-- Header Card -->
                <div class="flex justify-between items-start mb-4">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                      Peminjaman #<?= htmlspecialchars($peminjaman['id_peminjaman']) ?>
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      <?= ucfirst(htmlspecialchars($peminjaman['status'])) ?>
                    </span>
                  </div>
                </div>

                <!-- Info Peminjaman -->
                <div class="space-y-3 mb-4">
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0v4a2 2 0 01-2 2H7a3 3 0 01-3-3V7a3 3 0 013-3h7m-3 7h3"></path>
                    </svg>
                    <span><?= htmlspecialchars($peminjaman['deskripsi']) ?></span>
                  </div>

                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4M7 7a3 3 0 00-3 3v8a3 3 0 003 3h10a3 3 0 003-3V10a3 3 0 00-3-3H7z"></path>
                    </svg>
                    <span>Dipinjam: <?= date('d M Y', strtotime($peminjaman['tgl_peminjaman'])) ?></span>
                  </div>

                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    <span>Total: <?= htmlspecialchars($peminjaman['total_pinjam']) ?> barang</span>
                  </div>
                </div>

                <!-- Actions -->
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Grid untuk 4 tabel -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- 5 Kategori Pertama -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Kategori Barang</h2>
          <a href="<?= Helper::basePath(); ?>admin/kategori" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-900">
            <thead class="text-xs uppercase bg-gray-50">
              <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Nama Kategori</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dataKategori)): ?>
                <tr>
                  <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data kategori</td>
                </tr>
              <?php else: ?>
                <?php foreach ($dataKategori as $kategori): ?>
                  <tr class="bg-white hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?= $kategori['id_kategori'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($kategori['nama']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 5 Jenis Pertama -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Jenis Barang</h2>
          <a href="<?= Helper::basePath(); ?>admin/jenis" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-900">
            <thead class="text-xs uppercase bg-gray-50">
              <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Nama Jenis</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Stok</th>
                <th class="px-6 py-3">Tersedia</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dataJenis)): ?>
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data jenis</td>
                </tr>
              <?php else: ?>
                <?php foreach ($dataJenis as $jenis): ?>
                  <tr class="bg-white hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($jenis['id_jenis']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($jenis['nama']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($jenis['nama_kategori']) ?></td>
                    <td class="px-6 py-4"><?= $jenis['stok'] ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    <?= $jenis['stok_tersedia'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <?= $jenis['stok_tersedia'] ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 5 Kondisi Pertama -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Kondisi Barang</h2>
          <a href="<?= Helper::basePath(); ?>admin/kondisi" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-900">
            <thead class="text-xs uppercase bg-gray-50">
              <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Nama Kondisi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dataState)): ?>
                <tr>
                  <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada data kondisi</td>
                </tr>
              <?php else: ?>
                <?php foreach ($dataState as $state): ?>
                  <tr class="bg-white hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?= $state['id_state'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($state['nama']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 5 Barang Pertama -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Barang</h2>
          <a href="<?= Helper::basePath(); ?>admin/barang" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-900">
            <thead class="text-xs uppercase bg-gray-50">
              <tr>
                <th class="px-6 py-3">Kode Barang</th>
                <th class="px-6 py-3">Jenis</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Kondisi</th>
                <th class="px-6 py-3">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($dataBarang)): ?>
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data barang</td>
                </tr>
              <?php else: ?>
                <?php foreach ($dataBarang as $barang): ?>
                  <tr class="bg-white hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium"><?= htmlspecialchars($barang['kode_barang']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($barang['nama_jenis']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($barang['nama_kategori']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($barang['nama_state']) ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    <?= $barang['status'] == 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                        <?= ucfirst($barang['status']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </main>

  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const userButton = document.getElementById('dropdownUserNameButton');
    const userDropdown = document.getElementById('dropdownUserName');
    document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
      sidebar.classList.toggle('sidebar-collapsed');
    });
    sidebar.addEventListener('mouseenter', function() {
      if (sidebar.classList.contains('sidebar-collapsed') && userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });
    sidebar.addEventListener('mouseleave', function() {
      if (sidebar.classList.contains('sidebar-hover') && userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });
    userButton.addEventListener('focusout', function() {
      if (!userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });
  </script>

  <?php
  // Jangan lupa tutup koneksi database
  $db->close();
  ?>
</body>

</html>