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
if ($action === 'add_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_kategori = trim($_POST['nama_kategori'] ?? '');
  if (!empty($nama_kategori)) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM kategori WHERE nama = ?");
    $stmt->bind_param('s', $nama_kategori);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'Kategori sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("INSERT INTO kategori (nama) VALUES (?)");
      $stmt->bind_param('s', $nama_kategori);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Kategori berhasil ditambahkan!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Nama kategori wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "kategori");
  exit;
}

// UPDATE
if ($action === 'edit_kategori' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_kategori = $_POST['id_kategori'] ?? '';
  $nama_kategori = trim($_POST['nama_kategori'] ?? '');
  if (!empty($id_kategori) && !empty($nama_kategori)) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM kategori WHERE nama = ? AND id_kategori != ?");
    $stmt->bind_param('si', $nama_kategori, $id_kategori);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'Nama kategori sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("UPDATE kategori SET nama = ? WHERE id_kategori = ?");
      $stmt->bind_param('si', $nama_kategori, $id_kategori);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Kategori berhasil diupdate!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Semua field wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "kategori");
  exit;
}

// DELETE
if ($action === 'delete_kategori' && isset($_POST['id_kategori'])) {
  $id_kategori = $_POST['id_kategori'];
  $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
  $stmt->bind_param('i', $id_kategori);
  $stmt->execute();
  $stmt->close();
  $_SESSION['message'] = 'Kategori berhasil dihapus!';

  header("Location: " . Helper::basePath() . "kategori");
  exit;
}

// READ
$sort = $_GET['sort'] ?? 'id_asc';
$orderBy = "k.id_kategori ASC";
if ($sort === 'id_desc') $orderBy = "k.id_kategori DESC";
if ($sort === 'az') $orderBy = "k.nama ASC";
if ($sort === 'za') $orderBy = "k.nama DESC";
if ($sort === 'jenis_desc') $orderBy = "jumlah_jenis DESC, k.nama ASC";
if ($sort === 'jenis_asc') $orderBy = "jumlah_jenis ASC, k.nama ASC";

// Ambil kategori beserta jumlah jenis terkait
$kategori = [];
$result = $conn->query("
  SELECT k.id_kategori, k.nama, COUNT(j.id_jenis) AS jumlah_jenis
  FROM kategori k
  LEFT JOIN jenis j ON j.kategori_id = k.id_kategori
  GROUP BY k.id_kategori, k.nama
  ORDER BY $orderBy
");
while ($row = $result->fetch_assoc()) {
  $kategori[] = $row;
}

// Ambil jenis per kategori (untuk tampilan)
$jenis_per_kategori = [];
$res = $conn->query("SELECT id_jenis, nama, kategori_id FROM jenis ORDER BY nama ASC");
while ($row = $res->fetch_assoc()) {
  $jenis_per_kategori[$row['kategori_id']][] = $row;
}
$db->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kategori - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <style>
    select.no-arrow {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none !important;
      padding-right: 1rem;
    }
        select {
      background-color: transparent;     
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: white;                      
      border-radius: 0.5rem;             
      padding: 0.5rem 1rem;              
      outline: none;
    }

    select:focus {
      border-color: white;               
      box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.4); 
    }

    select option {
      background-color: rgba(0, 0, 0, 0.5); 
      color: white;
    }

  </style>
</head>

<!-- Modal Konfirmasi Hapus -->
<div id="modalDelete" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
  <div class="relative w-full max-w-md mx-auto mt-24">
    <div class="bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-bold mb-4 text-red-700">Konfirmasi Hapus</h3>
      <p class="mb-4">Yakin ingin menghapus kategori ini?</p>
      <form method="POST" action="<?= Helper::basePath(); ?>kategori">
        <input type="hidden" name="action" value="delete_kategori">
        <input type="hidden" name="id_kategori" id="delete_id_kategori">
        <div class="flex justify-end gap-2">
          <button type="button" data-modal-hide="modalDelete" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
          <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function showDeleteModal(id) {
    document.getElementById('delete_id_kategori').value = id;
    document.getElementById('modalDelete').classList.remove('hidden');
  }
  document.querySelectorAll('[data-modal-hide="modalDelete"]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('modalDelete').classList.add('hidden');
    });
  });
</script>

<body>
  <!-- Sidebar -->
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>
  <div id="mainContent" class="min-h-screen flex flex-col transition-all duration-300 sm:ml-64 main-content ml-0">
    <!-- Header-->
    <header class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-md px-6 py-8 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
        </svg>
      </div>

      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
          <h1 class="text-2xl font-bold text-white mb-1">Manajemen Kategori</h1>
          <p class="text-indigo-100 text-sm">Total: <?= count($kategori) ?> kategori</p>
        </div>

        <!-- Action buttons -->
        <div class="flex flex-wrap items-center gap-3">
          <form method="GET" action="<?= Helper::basePath(); ?>kategori" class="flex gap-2">
            <select name="sort"
              class="appearance-none bg-white/10 backdrop-blur border border-white/20 text-black 
              py-2 px-3 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 
              focus:border-transparent text-sm no-arrow">
              <option value="id_asc" <?= ($_GET['sort'] ?? '') === 'id_asc' ? 'selected' : '' ?>>ID Terkecil</option>
              <option value="id_desc" <?= ($_GET['sort'] ?? '') === 'id_desc' ? 'selected' : '' ?>>ID Terbesar</option>
              <option value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>A-Z</option>
              <option value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>Z-A</option>
              <option value="jenis_desc" <?= ($_GET['sort'] ?? '') === 'jenis_desc' ? 'selected' : '' ?>>Jenis Terbanyak</option>
              <option value="jenis_asc" <?= ($_GET['sort'] ?? '') === 'jenis_asc' ? 'selected' : '' ?>>Jenis Terkecil</option>
            </select>
          </form>
          <script>
            document.querySelector('select[name="sort"]').addEventListener('change', function() {
              this.form.submit();
            });
          </script>

          <!-- Tombol plus-->
          <button data-modal-target="modalTambah" data-modal-toggle="modalTambah"
            class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-green-600 
            transition-all duration-300 group shadow-lg shadow-green-500/20">
            <svg class="w-5 h-5 transform group-hover:rotate-90 transition-transform duration-300"
              fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </button>
        </div>
      </div>
    </header>

    <main class="flex-1 p-6 bg-gray-50">
      <?php if ($message): ?>
        <div class="mb-4 p-4 rounded-lg <?= $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <!-- Grid  -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($kategori as $k): ?>
          <div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
            <!-- Header  -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-t-xl border-b 
              flex items-center justify-between group-hover:from-indigo-500/10 group-hover:to-purple-500/10 transition-all">
              <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-indigo-600">ID:</span>
                <span class="text-sm font-semibold text-indigo-700"><?= $k['id_kategori'] ?></span>
              </div>
              <div class="flex gap-2">
                <button class="edit-btn p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600 
                  transition-all duration-150"
                  data-id="<?= $k['id_kategori'] ?>"
                  data-nama="<?= htmlspecialchars($k['nama']) ?>"
                  data-modal-target="modalEdit"
                  data-modal-toggle="modalEdit">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button type="button"
                  class="p-1.5 rounded-lg hover:bg-red-100 text-red-600 
                  transition-all duration-150"
                  onclick="showDeleteModal(<?= $k['id_kategori'] ?>)">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content-->
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 group-hover:text-indigo-600 transition-colors">
                <?= htmlspecialchars($k['nama']) ?>
              </h3>

              <?php if (!empty($jenis_per_kategori[$k['id_kategori']])): ?>
                <button type="button"
                  id="toggle-btn-<?= $k['id_kategori'] ?>"
                  class="w-full text-left px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500/5 to-purple-500/5 
                  hover:from-indigo-500/10 hover:to-purple-500/10 text-sm font-medium text-indigo-700 transition-all"
                  onclick="toggleJenis('jenis-list-<?= $k['id_kategori'] ?>', this)">
                  Ada Jenis Terkait
                </button>
                <div id="jenis-list-<?= $k['id_kategori'] ?>" class="hidden mt-3">
                  <div class="text-xs font-medium text-gray-500 mb-2">Jenis terkait:</div>
                  <ul class="space-y-1">
                    <?php foreach ($jenis_per_kategori[$k['id_kategori']] as $j): ?>
                      <li class="text-sm text-gray-600 pl-3 border-l-2 border-gray-200">
                        <?= htmlspecialchars($j['nama']) ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php else: ?>
                <div class="text-sm text-gray-400 italic">Belum ada jenis</div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <script>
        function toggleJenis(id, btn) {
          const el = document.getElementById(id);
          if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            btn.textContent = 'Sembunyikan Jenis';
            btn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            btn.classList.add('bg-green-500', 'text-white', 'hover:bg-green-600');
          } else {
            el.classList.add('hidden');
            btn.textContent = 'Ada Jenis Terkait';
            btn.classList.remove('bg-green-500', 'text-white', 'hover:bg-green-600');
            btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
          }
        }
      </script>
    </main>
  </div>

  <!-- Modal Tambah -->
  <div id="modalTambah" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-lg font-bold text-white">Tambah Kategori</h3>
        </div>

        <form method="POST" action="<?= Helper::basePath(); ?>kategori">
          <input type="hidden" name="action" value="add_kategori">
          <div class="mb-6">
            <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Kategori</label>
            <input type="text" name="nama_kategori"
              class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 
              focus:ring-indigo-500 focus:border-transparent transition-colors"
              required
              placeholder="Masukkan nama kategori">
          </div>
          <div class="flex justify-end gap-2">
            <button type="button"
              data-modal-hide="modalTambah"
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
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 -m-6 mb-6 p-6 rounded-t-xl">
        <h3 class="text-lg font-bold text-white">Edit Kategori</h3>
      </div>

      <form method="POST" action="<?= Helper::basePath(); ?>kategori">
        <input type="hidden" name="action" value="edit_kategori">
        <input type="hidden" name="id_kategori" id="edit_id_kategori">
        <div class="mb-6">
          <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Kategori</label>
          <input type="text" name="nama_kategori" id="edit_nama_kategori"
            class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
            focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
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

  <!-- Flowbite JS (untuk modal) -->
  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const userButton = document.getElementById('dropdownUserNameButton');
    const userDropdown = document.getElementById('dropdownUserName');

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
    userButton.addEventListener('focusout', function() {
      if (!userDropdown.classList.contains('hidden')) {
        sidebar.classList.remove('sidebar-hover');
      }
    });
    document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('edit_id_kategori').value = this.dataset.id;
    document.getElementById('edit_nama_kategori').value = this.dataset.nama;
  });
});
  </script>
</body>

</html>