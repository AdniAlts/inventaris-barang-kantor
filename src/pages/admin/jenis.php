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
if ($action === 'add_jenis' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_jenis = trim($_POST['id_jenis'] ?? '');
  $nama_jenis = trim($_POST['nama_jenis'] ?? '');
  $kategori_id = $_POST['kategori_id'] ?? '';

  if ($id_jenis && $nama_jenis && $kategori_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM jenis WHERE id_jenis = ? OR nama = ?");
    $stmt->bind_param('ss', $id_jenis, $nama_jenis);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'ID atau nama jenis sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("INSERT INTO jenis (id_jenis, nama, kategori_id) VALUES (?, ?, ?)");
      $stmt->bind_param('ssi', $id_jenis, $nama_jenis, $kategori_id);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Jenis berhasil ditambahkan!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Semua field wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "jenis");
  exit;
}

// UPDATE
if ($action === 'edit_jenis' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_jenis = $_POST['id_jenis'] ?? '';
  $nama_jenis = trim($_POST['nama_jenis'] ?? '');
  $kategori_id = $_POST['kategori_id'] ?? '';

  if ($id_jenis && $nama_jenis && $kategori_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM jenis WHERE nama = ? AND id_jenis != ?");
    $stmt->bind_param('ss', $nama_jenis, $id_jenis);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
      $_SESSION['message'] = 'Nama jenis sudah ada!';
      $_SESSION['message_type'] = 'error';
    } else {
      $stmt = $conn->prepare("UPDATE jenis SET nama = ?, kategori_id = ? WHERE id_jenis = ?");
      $stmt->bind_param('sis', $nama_jenis, $kategori_id, $id_jenis);
      $stmt->execute();
      $stmt->close();
      $_SESSION['message'] = 'Jenis berhasil diupdate!';
      $_SESSION['message_type'] = 'success';
    }
  } else {
    $_SESSION['message'] = 'Semua field wajib diisi.';
    $_SESSION['message_type'] = 'error';
  }
  header("Location: " . Helper::basePath() . "jenis");
  exit;
}

// DELETE
if ($action === 'delete_jenis' && isset($_POST['id_jenis'])) {
  $id_jenis = $_POST['id_jenis'];
  $stmt = $conn->prepare("DELETE FROM jenis WHERE id_jenis = ?");
  $stmt->bind_param('s', $id_jenis);
  $stmt->execute();
  $stmt->close();
  $_SESSION['message'] = 'Jenis berhasil dihapus!';
  $_SESSION['message_type'] = 'success';
  header("Location: " . Helper::basePath() . "jenis");
  exit;
}

// READ
$sort = $_GET['sort'] ?? 'asc';
$orderBy = "j.nama ASC";
if ($sort === 'desc') $orderBy = "j.nama DESC";
if ($sort === 'stok_desc') $orderBy = "j.stok DESC";
if ($sort === 'stok_asc') $orderBy = "j.stok ASC";
if ($sort === 'id_asc') $orderBy = "CAST(SUBSTRING(j.id_jenis, 4) AS UNSIGNED) ASC, LEFT(j.id_jenis, 3) ASC";
if ($sort === 'id_desc') $orderBy = "CAST(SUBSTRING(j.id_jenis, 4) AS UNSIGNED) DESC, LEFT(j.id_jenis, 3) DESC";

$jenis = [];
$result = $conn->query("SELECT j.id_jenis, j.nama, j.stok, j.stok_tersedia, k.nama AS kategori_nama, j.kategori_id 
                        FROM jenis j LEFT JOIN kategori k ON j.kategori_id = k.id_kategori 
                        ORDER BY $orderBy");
while ($row = $result->fetch_assoc()) {
  $jenis[] = $row;
}

// Kategori untuk dropdown
$kategori = [];
$res = $conn->query("SELECT id_kategori, nama FROM kategori ORDER BY nama ASC");
while ($row = $res->fetch_assoc()) {
  $kategori[] = $row;
}
$db->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jenis - Inventaris Barang Kantor</title>
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

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>
  <div id="mainContent" class="min-h-screen flex flex-col transition-all duration-300 sm:ml-64 main-content">
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
        <!-- Judul -->
        <div>
          <h1 class="text-2xl font-bold text-white mb-1">Manajemen Jenis</h1>
          <p class="text-indigo-100 text-sm">Total: <?= count($jenis) ?> jenis</p>
        </div>

        <!-- Action buttons -->
        <div class="flex flex-wrap items-center gap-3">
          <form method="GET" action="<?= Helper::basePath(); ?>jenis" class="flex gap-2">
            <select name="sort"
              class="appearance-none bg-white/10 backdrop-blur border border-white/20 text-white 
              py-2 px-3 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 
              focus:border-transparent text-sm no-arrow">
              <option value="asc" <?= ($_GET['sort'] ?? '') === 'asc' ? 'selected' : '' ?>>A-Z</option>
              <option value="desc" <?= ($_GET['sort'] ?? '') === 'desc' ? 'selected' : '' ?>>Z-A</option>
              <option value="stok_desc" <?= ($_GET['sort'] ?? '') === 'stok_desc' ? 'selected' : '' ?>>Stok Terbanyak</option>
              <option value="stok_asc" <?= ($_GET['sort'] ?? '') === 'stok_asc' ? 'selected' : '' ?>>Stok Terkecil</option>
              <option value="id_asc" <?= ($_GET['sort'] ?? '') === 'id_asc' ? 'selected' : '' ?>>ID Terkecil</option>
              <option value="id_desc" <?= ($_GET['sort'] ?? '') === 'id_desc' ? 'selected' : '' ?>>ID Terbesar</option>
            </select>
            <script>
              document.querySelector('select[name="sort"]').addEventListener('change', function() {
                this.form.submit();
              });
            </script>
          </form>

          <!-- Tombol plus -->
          <button data-modal-target="modalTambah" data-modal-toggle="modalTambah"
            class="p-2 bg-blue-700 text-white rounded-lg hover:bg-green-600 
            transition-all duration-300 group shadow-lg shadow-blue-500/20">
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

      <!-- Grid-->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($jenis as $j): ?>
          <div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group">
            <!-- Header-->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500/5 to-blue-600/5 rounded-t-xl border-b 
              flex items-center justify-between group-hover:from-blue-500/10 group-hover:to-blue-600/10 transition-all">
              <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-blue-600">ID:</span>
                <span class="text-sm font-semibold text-blue-700"><?= htmlspecialchars($j['id_jenis']) ?></span>
              </div>
              <!-- Tombol aksi -->
              <div class="flex gap-2">
                <button class="edit-btn p-1.5 rounded-md hover:bg-blue-100 text-blue-600"
                  data-id="<?= htmlspecialchars($j['id_jenis']) ?>"
                  data-nama="<?= htmlspecialchars($j['nama']) ?>"
                  data-stok="<?= htmlspecialchars($j['stok']) ?>"
                  data-kategori="<?= htmlspecialchars($j['kategori_id']) ?>"
                  data-modal-target="modalEdit"
                  data-modal-toggle="modalEdit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button type="button" class="p-1.5 rounded-md hover:bg-red-100 text-red-600"
                  onclick="showDeleteModal('<?= htmlspecialchars($j['id_jenis']) ?>')">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content-->
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 group-hover:text-indigo-600 transition-colors">
                <?= htmlspecialchars($j['nama']) ?>
              </h3>

              <!-- Info badges -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-lg">
                  Stok: <?= htmlspecialchars($j['stok']) ?>
                </span>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-lg">
                  Stok Tersedia: <?= htmlspecialchars($j['stok_tersedia']) ?>
                </span>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-lg">
                  Kategori: <?= htmlspecialchars($j['kategori_nama'] ?? '-') ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <!-- Modal Tambah -->
  <div id="modalTambah" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <!-- Header modal -->
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-lg font-bold text-white">Tambah Jenis</h3>
        </div>

        <form method="POST" action="<?= Helper::basePath(); ?>jenis">
          <input type="hidden" name="action" value="add_jenis">
          <div class="space-y-4">
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">ID Jenis</label>
              <input type="text" name="id_jenis"
                class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                maxlength="10" required>
            </div>
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Jenis</label>
              <input type="text" name="nama_jenis"
                class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required>
            </div>
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Kategori</label>
              <select name="kategori_id"
                class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategori as $k): ?>
                  <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-6">
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
        <!-- Header-->
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-lg font-bold text-white">Edit Jenis</h3>
        </div>

        <form method="POST" action="<?= Helper::basePath(); ?>jenis">
          <input type="hidden" name="action" value="edit_jenis">
          <input type="hidden" name="id_jenis" id="edit_id_jenis">
          <div class="space-y-4">
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Jenis</label>
              <input type="text" name="nama_jenis" id="edit_nama_jenis"
                class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required>
            </div>
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Kategori</label>
              <select name="kategori_id" id="edit_kategori_id"
                class="w-full px-4 py-2.5 text-gray-700 bg-gray-50 border border-gray-200 rounded-lg 
                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategori as $k): ?>
                  <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-6">
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
        <p class="mb-4">Yakin ingin menghapus jenis ini?</p>
        <form method="POST" action="<?= Helper::basePath(); ?>jenis">
          <input type="hidden" name="action" value="delete_jenis">
          <input type="hidden" name="id_jenis" id="delete_id_jenis">
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
        document.getElementById('edit_id_jenis').value = this.dataset.id;
        document.getElementById('edit_nama_jenis').value = this.dataset.nama;
        document.getElementById('edit_stok').value = this.dataset.stok;
        document.getElementById('edit_kategori_id').value = this.dataset.kategori;
        document.getElementById('modalEdit').classList.remove('hidden');
      });
    });

    // Modal Hapus
    function showDeleteModal(id) {
      document.getElementById('delete_id_jenis').value = id;
      document.getElementById('modalDelete').classList.remove('hidden');
    }
    document.querySelectorAll('[data-modal-hide="modalDelete"]').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('modalDelete').classList.add('hidden');
      });
    });
  </script>
</body>

</html>