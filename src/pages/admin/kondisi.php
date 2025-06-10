<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helper.php';

if (session_status() == PHP_SESSION_NONE) session_start();

$db = new db();
$conn = $db->conn;

// --- CRUD Logic ---
$action = $_POST['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// CREATE
if ($action === 'add_state' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_state = trim($_POST['nama_state'] ?? '');
  if (!empty($nama_state)) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM state WHERE nama = ?");
    $stmt->bind_param('s', $nama_state);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'Kondisi sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("INSERT INTO state (nama) VALUES (?)");
      $stmt->bind_param('s', $nama_state);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Kondisi berhasil ditambahkan!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Nama kondisi wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "kondisi");
  exit;
}

// UPDATE
if ($action === 'edit_state' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_state = $_POST['id_state'] ?? '';
  $nama_state = trim($_POST['nama_state'] ?? '');
  if (!empty($id_state) && !empty($nama_state)) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM state WHERE nama = ? AND id_state != ?");
    $stmt->bind_param('si', $nama_state, $id_state);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'Nama kondisi sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("UPDATE state SET nama = ? WHERE id_state = ?");
      $stmt->bind_param('si', $nama_state, $id_state);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Kondisi berhasil diupdate!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Semua field wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "kondisi");
  exit;
}

// DELETE
if ($action === 'delete_state' && isset($_POST['id_state'])) {
  $id_state = $_POST['id_state'];
  $stmt = $conn->prepare("DELETE FROM state WHERE id_state = ?");
  $stmt->bind_param('i', $id_state);
  $stmt->execute();
  $stmt->close();
  $_SESSION['message'] = 'Kondisi berhasil dihapus!';
  $_SESSION['message_type'] = 'success';
  header("Location: " . Helper::basePath() . "kondisi");
  exit;
}

// READ - tambah logic sorting
$sort = $_GET['sort'] ?? 'id_asc';
$orderBy = "id_state ASC"; // default sort

if ($sort === 'id_desc') $orderBy = "id_state DESC";
if ($sort === 'az') $orderBy = "nama ASC";
if ($sort === 'za') $orderBy = "nama DESC";

// Query dengan ORDER BY
$state = [];
$result = $conn->query("SELECT id_state, nama FROM state ORDER BY $orderBy");
while ($row = $result->fetch_assoc()) {
  $state[] = $row;
}
$db->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kondisi - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <style>
      select.no-arrow {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none !important;
      padding-right: 1rem;
    }
  </style>
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>
  <div id="mainContent" class="min-h-screen flex flex-col transition-all duration-300 sm:ml-64 main-content">
    <!-- Header-->
    <header class="bg-gradient-to-r from-blue-700 to-cyan-600 shadow-md px-6 py-8 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <path d="M0 0L100 0L100 100L0 100Z" fill="url(#pattern)" />
        </svg>
        <defs>
          <pattern id="pattern" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
            <path d="M0 10L10 0" stroke="white" stroke-width="0.5" />
          </pattern>
        </defs>
      </div>

      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
          <h1 class="text-2xl font-bold text-white mb-1">Manajemen Kondisi</h1>
          <p class="text-blue-100 text-sm">Total: <?= count($state) ?> kondisi</p>
        </div>
    </header>

    <main class="flex-1 p-6 bg-gradient-to-br from-gray-50 via-blue-50/30 to-indigo-50/30">
      <div class="absolute inset-0 opacity-[0.015]">
        <svg class="w-full h-full">
          <defs>
            <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="2" cy="2" r="1" fill="currentColor" />
            </pattern>
            <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
              <path d="M0 .5h32M.5 0v32" fill="none" stroke="currentColor" stroke-opacity="0.1" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid)" />
          <rect width="100%" height="100%" fill="url(#dots)" />
        </svg>
      </div>

      <!-- Card table -->
      <div class="relative bg-white/60 backdrop-blur-sm rounded-xl shadow-sm overflow-hidden border border-white/80">
        <?php if ($message): ?>
          <div class="mb-4 p-4 rounded-lg <?= $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-3.5 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">
                  <div class="flex items-center gap-2">
                    ID
        <form id="sortForm" method="GET" action="<?= Helper::basePath(); ?>kondisi" class="inline-block">
          <select name="sort"
            class="appearance-none bg-white/10 backdrop-blur border border-white/20 text-gray-800 font-semibold
            py-1 px-2 pr-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400/50
            focus:border-blue-400 text-xs no-arrow ml-2"
            onchange="document.getElementById('sortForm').submit()">
            <option class="bg-white text-gray-900" value="id_asc" <?= ($_GET['sort'] ?? '') === 'id_asc' ? 'selected' : '' ?>>
              ID Kecil
            </option>
            <option class="bg-white text-gray-900" value="id_desc" <?= ($_GET['sort'] ?? '') === 'id_desc' ? 'selected' : '' ?>>
              ID Besar
            </option>
            <option class="bg-white text-gray-900" value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>
              Nama A-Z
            </option>
            <option class="bg-white text-gray-900" value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>
              Nama Z-A
            </option>
          </select>
        </form>
                  </div>
                </th>
                <th class="px-6 py-3.5 text-xs font-semibold text-gray-700 uppercase tracking-wider text-left">
                  <div class="flex items-center gap-2">
                    Nama Kondisi
                  </div>
                </th>
                <th class="px-6 py-3.5 text-xs font-semibold text-gray-700 uppercase tracking-wider text-right">
                  <div class="flex items-center justify-end gap-2">
                    Aksi
                    <!-- Tombol Tambah di dalam table -->
                    <button data-modal-target="modalTambah" data-modal-toggle="modalTambah"
                      class="ml-2 p-2 bg-blue-700 text-white rounded-lg hover:bg-green-600 
                      transition-all duration-300 group shadow-lg shadow-blue-500/20">
                      <svg class="w-5 h-5 transform group-hover:rotate-90 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <?php foreach ($state as $s): ?>
                <tr class="group hover:bg-gray-50/50 transition-colors cursor-default">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span class="px-2 py-1 text-xs font-medium bg-blue-50 text-blue-600 rounded-md">
                        <?= $s['id_state'] ?>
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($s['nama']) ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button class="edit-btn p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-blue-600 
                        transition-all duration-150"
                        data-id="<?= $s['id_state'] ?>"
                        data-nama="<?= htmlspecialchars($s['nama']) ?>"
                        data-modal-target="modalEdit"
                        data-modal-toggle="modalEdit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                      </button>
                      <button type="button"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-red-600 
                        transition-all duration-150"
                        onclick="showDeleteModal(<?= $s['id_state'] ?>)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal Tambah -->
  <div id="modalTambah" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <!-- Header dengan gradient -->
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-lg font-bold text-white">Tambah Kondisi</h3>
        </div>

        <form method="POST" action="<?= Helper::basePath(); ?>kondisi">
          <input type="hidden" name="action" value="add_state">
          <div class="mb-6">
            <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Kondisi</label>
            <input type="text" name="nama_state"
              class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
              focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              required
              placeholder="Masukkan nama kondisi">
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-hide="modalTambah"
              class="px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium 
              border border-gray-200 transition-all duration-300">
              Batal
            </button>
            <button type="submit"
              class="px-4 py-2.5 rounded-lg bg-gradient-to-r from-green-500 to-green-600 
              hover:from-green-600 hover:to-green-700 text-white font-medium 
              transition-all duration-300 shadow-md hover:shadow-lg">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div id="modalEdit" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <!-- Header dengan gradient -->
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-lg font-bold text-white">Edit Kondisi</h3>
        </div>

        <form method="POST" action="<?= Helper::basePath(); ?>kondisi">
          <input type="hidden" name="action" value="edit_state">
          <input type="hidden" name="id_state" id="edit_id_state">
          <div class="mb-6">
            <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Kondisi</label>
            <input type="text" name="nama_state" id="edit_nama_state"
              class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
              focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              required>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-hide="modalEdit"
              class="px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium 
              border border-gray-200 transition-all duration-300">
              Batal
            </button>
            <button type="submit"
              class="px-4 py-2.5 rounded-lg bg-gradient-to-r from-green-500 to-green-600 
              hover:from-green-600 hover:to-green-700 text-white font-medium 
              transition-all duration-300 shadow-md hover:shadow-lg">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Hapus -->
  <div id="modalDelete" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4 text-red-700">Konfirmasi Hapus</h3>
        <p class="mb-4">Yakin ingin menghapus kondisi ini?</p>
        <form method="POST" action="<?= Helper::basePath(); ?>kondisi">
          <input type="hidden" name="action" value="delete_state">
          <input type="hidden" name="id_state" id="delete_id_state">
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-hide="modalDelete" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
            <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Hapus</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Flowbite JS (untuk modal) -->
  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
  <script>
    // Modal Edit
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.getElementById('edit_id_state').value = this.dataset.id;
        document.getElementById('edit_nama_state').value = this.dataset.nama;
        document.getElementById('modalEdit').classList.remove('hidden');
      });
    });

    // Modal Hapus
    function showDeleteModal(id) {
      document.getElementById('delete_id_state').value = id;
      document.getElementById('modalDelete').classList.remove('hidden');
    }
    document.querySelectorAll('[data-modal-hide="modalDelete"]').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('modalDelete').classList.add('hidden');
      });
    });

    // Sidebar behaviour (collapse/expand)
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('sidebar-collapsed');
      mainContent.classList.toggle('sm:ml-64');
      mainContent.classList.toggle('ml-0');
    });
    sidebar.addEventListener('mouseenter', function() {
      if (sidebar.classList.contains('sidebar-collapsed')) {
        sidebar.classList.add('sidebar-hover');
      }
    });
    sidebar.addEventListener('mouseleave', function() {
      if (sidebar.classList.contains('sidebar-hover')) {
        sidebar.classList.remove('sidebar-hover');
      }
    });
  </script>
</body>

</html>