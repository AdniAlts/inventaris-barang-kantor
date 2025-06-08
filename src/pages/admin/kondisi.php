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

// READ
$state = [];
$result = $conn->query("SELECT id_state, nama FROM state ORDER BY id_state ASC");
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
</head>

<body>
  <!-- Sidebar (copy dari dashboard, aktifkan menu Kondisi) -->
  <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-500 bg-gray-50"
    aria-label="Sidebar">
    <div class="h-full grid content-between px-3 py-4 overflow-y-auto overflow-x-hidden">
      <div class="self-start">
        <div class="flex justify-between ps-1.25 mb-5">
          <a href="<?= Helper::basePath(); ?>home" class="flex items-center sidebar-logo">
            <img src="https://flowbite.com/docs/images/logo.svg" class="h-7 sm:h-7" alt="Flowbite Logo" />
            <span class="self-center ms-4 text-xl font-extrabold whitespace-nowrap">InventaBox</span>
          </a>
          <button id="toggleSidebarBtn" type="button"
            class="inline-flex items-center p-2 text-sm text-gray-500 hover:text-gray-900 rounded hover:bg-gray-100 focus:outline-none focus:ring-3 focus:ring-gray-300">
            <span class="sr-only">Toggle sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
              </path>
            </svg>
          </button>
        </div>
        <ul class="pt-4 mt-4 space-y-2 font-semibold border-t border-gray-200">
          <li>
            <a href="<?= Helper::basePath(); ?>admin"
              class="flex items-center p-2 rounded-lg group pointer-btn transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200' : 'text-gray-600 hover:bg-gray-100' ?>">
              <svg class="shrink-0 w-5 h-5 transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false ? 'text-indigo-700' : 'text-gray-500' ?>"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                <path
                  d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="<?= Helper::basePath(); ?>kategori"
              class="flex items-center p-2 rounded-lg group pointer-btn transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/kategori') !== false ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200' : 'text-gray-600 hover:bg-gray-100' ?>">
              <svg class="shrink-0 w-5 h-5 transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/kategori') !== false ? 'text-indigo-700' : 'text-gray-500' ?>"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                  d="M4.07141 14v6h5.99999v-6H4.07141Zm4.5-4h6.99999l-3.5-6-3.49999 6Zm7.99999 10c1.933 0 3.5-1.567 3.5-3.5s-1.567-3.5-3.5-3.5-3.5 1.567-3.5 3.5 1.567 3.5 3.5 3.5Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kategori</span>
            </a>
          </li>
          <li>
            <a href="<?= Helper::basePath(); ?>jenis"
              class="flex items-center p-2 rounded-lg group pointer-btn transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/jenis') !== false ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200' : 'text-gray-600 hover:bg-gray-100' ?>">
              <svg class="shrink-0 w-5 h-5 transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/jenis') !== false ? 'text-indigo-700' : 'text-gray-500' ?>"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                  d="M7.111 20A3.111 3.111 0 0 1 4 16.889v-12C4 4.398 4.398 4 4.889 4h4.444a.89.89 0 0 1 .89.889v12A3.111 3.111 0 0 1 7.11 20Zm0 0h12a.889.889 0 0 0 .889-.889v-4.444a.889.889 0 0 0-.889-.89h-4.389a.889.889 0 0 0-.62.253l-3.767 3.665a.933.933 0 0 0-.146.185c-.868 1.433-1.581 1.858-3.078 2.12Zm0-3.556h.009m7.933-10.927 3.143 3.143a.889.889 0 0 1 0 1.257l-7.974 7.974v-8.8l3.574-3.574a.889.889 0 0 1 1.257 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Jenis</span>
            </a>
          </li>
          <li>
            <a href="<?= Helper::basePath(); ?>kondisi"
              class="flex items-center p-2 rounded-lg group pointer-btn transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/kondisi') !== false ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200' : 'text-gray-600 hover:bg-gray-100' ?>">
              <svg class="shrink-0 w-5 h-5 transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/kondisi') !== false ? 'text-indigo-700' : 'text-gray-500' ?>"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="2 2 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kondisi</span>
            </a>
          </li>
          <li>
            <a href="<?= Helper::basePath(); ?>barang"
              class="flex items-center p-2 rounded-lg group pointer-btn transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/barang') !== false ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-200' : 'text-gray-600 hover:bg-gray-100' ?>">
              <svg class="shrink-0 w-5 h-5 transition-all duration-300 <?= strpos($_SERVER['REQUEST_URI'], '/barang') !== false ? 'text-indigo-700' : 'text-gray-500' ?>"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 13h3.439a.991.991 0 0 1 .908.6 3.978 3.978 0 0 0 7.306 0 .99.99 0 0 1 .908-.6H20M4 13v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6M4 13l2-9h12l2 9M9 7h6m-7 3h8" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Barang</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="mt-4 space-y-2 border-t border-gray-200 sidebar-user">
        <button id="dropdownUserNameButton" data-dropdown-toggle="dropdownUserName"
          class="flex justify-between items-center p-2 ps-1 mt-4 w-full h-14.5 rounded-lg hover:bg-gray-100 focus:ring-3 focus:ring-gray-300 pointer-btn"
          type="button">
          <div class="flex items-center">
            <!-- Perlu pengecekan user saat ini sama ambil data dari database -->
            <!-- <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
              class="mr-3 w-8 h-8 rounded-full" alt="Bonnie avatar"> -->
            <svg class="shrink-0 w-7 h-7 text-gray-500 transition duration-75" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
              <path fill-rule="evenodd"
                d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                clip-rule="evenodd" />
            </svg>
            <div class="text-left ms-4 sidebar-user-details">
              <div class="font-semibold leading-none text-gray-950 mb-1">Admin</div>
              <div class="text-sm text-gray-700">[username dari db]</div>
            </div>
          </div>
          <svg class="w-5 h-5 text-gray-500 dropdown-arrow" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
              d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
              clip-rule="evenodd"></path>
          </svg>
        </button>
        <div id="dropdownUserName" class="z-10 w-60 bg-white rounded shadow-sm hidden"
          data-popper-placement="top">
          <!-- Perlu pengecekan user saat ini sama ambil data dari database -->
          <a href="<?= Helper::basePath(); ?>user" class="flex items-center hover:bg-gray-100 my-3 px-4 rounded">
            <svg class="shrink-0 w-8 h-8 mr-3 text-gray-500 transition duration-75" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
              <path fill-rule="evenodd"
                d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                clip-rule="evenodd" />
            </svg>
            <div class="text-left">
              <div class="font-semibold leading text-gray-950 mb-0.5">
                Pegawai</div>
              <div class="text-sm text-gray-700">Klik untuk ganti mode</div>
            </div>
          </a>
          <a href="#" class="flex items-center hover:bg-gray-100 my-3 px-4 rounded hidden">
            <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/roberta-casas.png"
              class="mr-3 w-8 h-8 rounded-full" alt="Roberta avatar">
            <div class="text-left">
              <div class="font-semibold leading text-gray-950 mb-0.5">
                Roberta Casas</div>
              <div class="text-sm text-gray-700">roberta@flowbite.com</div>
            </div>
          </a>
        </div>
        <a href="<?= Helper::basePath(); ?>logout"
          class="flex items-center p-2 text-rose-500 rounded-lg  hover:bg-gray-100 group">
          <svg class="shrink-0 w-5 h-5 text-rose-400 transition duration-75 group-hover:text-rose-600"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
          </svg>
          <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Keluar</span>
        </a>
      </div>
    </div>
  </aside>
  <div id="mainContent" class="min-h-screen flex flex-col transition-all duration-300 sm:ml-64 main-content">
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Manajemen Kondisi</h1>
      <button data-modal-target="modalTambah" data-modal-toggle="modalTambah"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
        + Tambah Kondisi
      </button>
    </header>
    <main class="flex-1 p-6">
      <?php if ($message): ?>
        <div class="mb-4 p-4 rounded-lg <?= $message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($state as $s): ?>
          <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between">
            <div>
              <div class="text-sm text-gray-400 mb-1">ID: <?= $s['id_state'] ?></div>
              <div class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($s['nama']) ?></div>
            </div>
            <div class="flex gap-2 mt-4">
              <button
                class="edit-btn bg-yellow-100 text-yellow-700 px-3 py-1 rounded hover:bg-yellow-200 font-semibold"
                data-id="<?= $s['id_state'] ?>"
                data-nama="<?= htmlspecialchars($s['nama']) ?>"
                data-modal-target="modalEdit"
                data-modal-toggle="modalEdit">Edit</button>
              <button
                type="button"
                class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 font-semibold"
                onclick="showDeleteModal(<?= $s['id_state'] ?>)">
                Hapus
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <!-- Modal Tambah -->
  <div id="modalTambah" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">Tambah Kondisi</h3>
        <form method="POST" action="<?= Helper::basePath(); ?>kondisi">
          <input type="hidden" name="action" value="add_state">
          <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700">Nama Kondisi</label>
            <input type="text" name="nama_state" class="w-full border rounded px-3 py-2" required>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-hide="modalTambah" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div id="modalEdit" tabindex="-1" class="hidden fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto h-modal h-full bg-black/40">
    <div class="relative w-full max-w-md mx-auto mt-24">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">Edit Kondisi</h3>
        <form method="POST" action="<?= Helper::basePath(); ?>kondisi">
          <input type="hidden" name="action" value="edit_state">
          <input type="hidden" name="id_state" id="edit_id_state">
          <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700">Nama Kondisi</label>
            <input type="text" name="nama_state" id="edit_nama_state" class="w-full border rounded px-3 py-2" required>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-hide="modalEdit" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
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