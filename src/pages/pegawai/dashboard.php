<?php
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../modules/get_peminjaman.php";

$db = new db();
$connDB = $db->conn;

$allPeminjaman = GetFromDB::peminjaman($connDB);

// Batasi hanya 5 peminjaman terakhir untuk dashboard
$peminjamanTerakhir = array_slice($allPeminjaman, 0, 5);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <!-- Header Dashboard -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Pegawai</h1>
      <p class="text-gray-600">Selamat datang! Kelola peminjaman barang kantor Anda.</p>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="mb-8">
      <div class="flex flex-wrap gap-4">
        <a href="<?= Helper::basePath(); ?>peminjaman"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Buat Peminjaman Baru
        </a>
        <a href="<?= Helper::basePath(); ?>pengembalian"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
          </svg>
          Kembalikan Barang
        </a>
      </div>
    </div>

    <!-- Statistik Singkat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Peminjaman</p>
            <p class="text-2xl font-semibold text-gray-900"><?= count($allPeminjaman); ?></p>
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
            <p class="text-2xl font-semibold text-gray-900">
              <?= count(array_filter($allPeminjaman, function ($p) {
                return $p['status'] == 'dipinjam';
              })); ?>
            </p>
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
            <p class="text-sm font-medium text-gray-600">Sudah Dikembalikan</p>
            <p class="text-2xl font-semibold text-gray-900">
              <?= count(array_filter($allPeminjaman, function ($p) {
                return $p['status'] == 'dikembalikan';
              })); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Peminjaman Terakhir -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Peminjaman Terakhir</h2>
        <a href="<?= Helper::basePath(); ?>peminjaman" class="text-blue-600 hover:text-blue-800 font-medium">
          Lihat Semua →
        </a>
      </div>

      <?php if (empty($peminjamanTerakhir)): ?>
        <div class="bg-white rounded-lg shadow-sm border p-8 text-center">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Peminjaman</h3>
          <p class="text-gray-600 mb-4">Anda belum memiliki riwayat peminjaman barang.</p>
          <a href="<?= Helper::basePath(); ?>peminjaman"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Buat Peminjaman Pertama
          </a>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach ($peminjamanTerakhir as $peminjaman_item): ?>
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
              <div class="p-6">
                <!-- Header Card -->
                <div class="flex justify-between items-start mb-4">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                      Peminjaman #<?= htmlspecialchars($peminjaman_item['id_peminjaman']); ?>
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                      <?= $peminjaman_item['status'] == 'dipinjam' ? 'bg-yellow-100 text-yellow-800' : ($peminjaman_item['status'] == 'dikembalikan' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                      <?= htmlspecialchars(ucfirst($peminjaman_item['status'])); ?>
                    </span>
                  </div>
                </div>

                <!-- Info Peminjaman -->
                <div class="space-y-3 mb-4">
                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0v4a2 2 0 01-2 2H7a3 3 0 01-3-3V7a3 3 0 013-3h7m-3 7h3"></path>
                    </svg>
                    <span><?= htmlspecialchars($peminjaman_item['deskripsi']); ?></span>
                  </div>

                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4M7 7a3 3 0 00-3 3v8a3 3 0 003 3h10a3 3 0 003-3V10a3 3 0 00-3-3H7z"></path>
                    </svg>
                    <span>Dipinjam: <?= date('d M Y', strtotime($peminjaman_item['tgl_peminjaman'])); ?></span>
                  </div>

                  <?php if ($peminjaman_item['tgl_balik']): ?>
                    <div class="flex items-center text-sm text-gray-600">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
                      </svg>
                      <span>Dikembalikan: <?= date('d M Y', strtotime($peminjaman_item['tgl_balik'])); ?></span>
                    </div>
                  <?php endif; ?>

                  <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    <span>Total: <?= htmlspecialchars($peminjaman_item['total_pinjam']); ?> barang</span>
                  </div>
                </div>

                <!-- Detail Barang -->
                <?php if (!empty($peminjaman_item['barang'])): ?>
                  <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Barang:</h4>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                      <?php foreach ($peminjaman_item['barang'] as $barang_item): ?>
                        <div class="flex justify-between items-center text-sm">
                          <span class="text-gray-700"><?= htmlspecialchars($barang_item['nama_barang']); ?></span>
                          <span class="font-medium text-gray-900"><?= htmlspecialchars($barang_item['jumlah']); ?></span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="mt-4 pt-4 border-t">
                  <div class="flex justify-between items-center">
                    <a href="<?= Helper::basePath(); ?>peminjaman?id_peminjaman=<?= $peminjaman_item['id_peminjaman']; ?>"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Lihat Detail
                    </a>

                    <?php if ($peminjaman_item['status'] == 'dipinjam'): ?>
                      <a href="<?= Helper::basePath(); ?>pengembalian?id_peminjaman=<?= $peminjaman_item['id_peminjaman']; ?>"
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        Kembalikan
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
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
</body>

</html>