<?php
// require_once "../utils/get_names.php"; // This file was not provided, assuming it's not strictly necessary for this CRUD
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helper.php'; // Ensure helper.php is included for Helper::basePath()

// Memulai sesi hanya jika belum ada sesi yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$db = new db();
$conn = $db->conn;

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

try {
    $sql_read = "SELECT
                    b.kode_barang AS id_barang,
                    b.kode_barang,
                    b.status,
                    b.state_id,
                    b.gambar_url,
                    b.jenis_id,
                    j.nama as nama_jenis,
                    s.nama as nama_state
                FROM `barang` b
                LEFT JOIN `jenis` j ON b.jenis_id = j.id_jenis
                LEFT JOIN `state` s ON b.state_id = s.id_state
                ORDER BY b.jenis_id ASC, CAST(SUBSTRING_INDEX(b.kode_barang, '_', -1) AS UNSIGNED) ASC";
    $result = $conn->query($sql_read); // Use $conn from the db object
    $barangs = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $barangs[] = $row;
        }
        $result->free_result();
    } else {
        $_SESSION['message'] = 'Gagal mengambil data barang: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
        error_log('MySQLi Error READ: ' . $conn->error . ' Query: ' . $sql_read);
    }
} catch (\Exception $e) {
    $_SESSION['message'] = 'Error mengambil data barang: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
    $barangs = [];
}

// Fetch data for dropdowns (jenis and state)
$jenis_options = [];
$state_options = [];

try {
    $sql_jenis = "SELECT id_jenis, nama FROM `jenis` ORDER BY nama ASC";
    $result_jenis = $conn->query($sql_jenis);
    if ($result_jenis) {
        while ($row = $result_jenis->fetch_assoc()) {
            $jenis_options[] = $row;
        }
        $result_jenis->free_result();
    }

    $sql_state = "SELECT id_state, nama FROM `state` ORDER BY nama ASC";
    $result_state = $conn->query($sql_state);
    if ($result_state) {
        while ($row = $result_state->fetch_assoc()) {
            $state_options[] = $row;
        }
        $result_state->free_result();
    }
} catch (\Exception $e) {
    error_log('Error fetching dropdown data: ' . $e->getMessage());
}

// Tutup koneksi
$db->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manajemen Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #E0E7FF;
            color: #3B82F6;
        }

        .sidebar-link.active svg {
            color: #3B82F6;
        }

        .alert-success {
            background-color: #D1FAE5;
            color: #065F46;
            border-left: 4px solid #10B981;
        }

        .alert-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }

        .alert-info {
            background-color: #DBEAFE;
            color: #1E40AF;
            border-left: 4px solid #3B82F6;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-blue-50">

    <div class="flex h-screen">
        <aside class="w-60 bg-white text-gray-700 flex flex-col shadow-lg">
            <div class="h-20 flex items-center justify-center border-b border-blue-100">
                <h1 class="text-2xl font-bold text-blue-600">Inventaris</h1>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="<?php echo Helper::basePath(); ?>dashboard"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
                <a href="<?php echo Helper::basePath(); ?>kategori"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Kategori
                </a>
                <a href="<?php echo Helper::basePath(); ?>jenis"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Jenis
                </a>
                <a href="<?php echo Helper::basePath(); ?>state"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    State
                </a>
                <a href="<?php echo Helper::basePath(); ?>barang"
                    class="sidebar-link active flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Barang
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-blue-100">
                <a href="<?php echo Helper::basePath(); ?>logout"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </a>
            </div>

        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-semibold text-blue-700">Manajemen Barang</h2>
                <button data-modal-target="defaultModal" data-modal-toggle="defaultModal"
                    class="block text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    type="button">
                    Tambah Barang
                </button>
            </div>

            <?php if (!empty($message)): ?>
                <div id="alert-message"
                    class="p-4 mb-4 text-sm rounded-lg <?php echo $message_type === 'success' ? 'alert-success' : ($message_type === 'error' ? 'alert-error' : 'alert-info'); ?>"
                    role="alert">
                    <span class="font-medium"><?php echo ucfirst($message_type); ?>!</span>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button"
                        class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-current rounded-lg focus:ring-2 focus:ring-current p-1.5 hover:bg-current/10 inline-flex items-center justify-center h-8 w-8"
                        onclick="document.getElementById('alert-message').style.display='none'" aria-label="Close">
                        <span class="sr-only">Dismiss</span><svg class="w-3 h-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
            <div id="dynamic-alert" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert">
                <span class="font-medium" id="alert-title"></span>
                <span id="alert-text"></span>
                <button type="button"
                    class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-current rounded-lg focus:ring-2 focus:ring-current p-1.5 hover:bg-current/10 inline-flex items-center justify-center h-8 w-8"
                    onclick="document.getElementById('dynamic-alert').style.display='none'" aria-label="Close">
                    <span class="sr-only">Dismiss</span><svg class="w-3 h-3" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold text-blue-700 mb-4">Daftar Barang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID (Kode Barang)</th>
                                <th scope="col" class="px-6 py-3">Gambar</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Jenis</th>
                                <th scope="col" class="px-6 py-3">State</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($barangs)): ?>
                                <?php foreach ($barangs as $barang): ?>
                                    <tr class="bg-white border-b hover:bg-blue-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            <?php echo htmlspecialchars($barang['id_barang']); ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($barang['gambar_url']): ?>
                                                <img src="<?php echo Helper::basePath() . 'storages/' . htmlspecialchars($barang['gambar_url']); ?>"
                                                    alt="Gambar Barang" class="w-16 h-16 object-cover rounded-md">
                                            <?php else: ?>
                                                Tidak ada gambar
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($barang['status']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($barang['nama_jenis'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($barang['nama_state'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button type="button" data-modal-target="editModal" data-modal-toggle="editModal"
                                                data-id_barang="<?php echo htmlspecialchars($barang['id_barang']); ?>"
                                                data-kode_barang="<?php echo htmlspecialchars($barang['kode_barang']); ?>"
                                                data-status="<?php echo htmlspecialchars($barang['status']); ?>"
                                                data-jenis_id="<?php echo htmlspecialchars($barang['jenis_id']); ?>"
                                                data-state_id="<?php echo htmlspecialchars($barang['state_id']); ?>"
                                                data-gambar_url="<?php echo htmlspecialchars($barang['gambar_url']); ?>"
                                                class="font-medium text-blue-600 hover:underline mr-3 edit-button">Edit</button>
                                            <button type="button"
                                                onclick="deleteBarang('<?php echo htmlspecialchars($barang['id_barang']); ?>', '<?php echo htmlspecialchars(addslashes($barang['kode_barang'])); ?>')"
                                                class="font-medium text-red-600 hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada barang atau gagal
                                        memuat data.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="defaultModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Barang Baru</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        data-modal-toggle="defaultModal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="addBarangForm" enctype="multipart/form-data">
                    <div class="grid gap-4 mb-4 ml-[20px]">
                        <div>
                            <label for="gambar_add" class="block mb-2 text-sm font-medium text-gray-900">Gambar</label>
                            <input type="file" id="gambar_add" name="gambar" accept="image/*"
                                class="pl-[27px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                        </div>
                        <div>
                            <label for="status_add" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                            <select name="status" id="status_add"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="" disabled selected>Pilih status</option>
                                <option value="tersedia">tersedia</option>
                                <option value="dipinjam">dipinjam</option>
                            </select>
                        </div>
                        <div>
                            <label for="jenis_add" class="block mb-2 text-sm font-medium text-gray-900">Jenis</label>
                            <select name="jenis" id="jenis_add"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="" disabled>Pilih Jenis</option>
                                <?php foreach ($jenis_options as $jenis): ?>
                                    <option value="<?php echo htmlspecialchars($jenis['id_jenis']); ?>">
                                        <?php echo htmlspecialchars($jenis['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="state_add" class="block mb-2 text-sm font-medium text-gray-900">State</label>
                            <select name="state" id="state_add"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="" disabled>Pilih State</option>
                                <?php foreach ($state_options as $state): ?>
                                    <option value="<?php echo htmlspecialchars($state['id_state']); ?>">
                                        <?php echo htmlspecialchars($state['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Barang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Barang</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        data-modal-toggle="editModal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="editBarangForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_barang" id="id_barang_edit">
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="kode_barang_edit" class="block mb-2 text-sm font-medium text-gray-900">Kode
                                Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang_edit"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required readonly>
                        </div>
                        <div>
                            <label for="gambar_edit" class="block mb-2 text-sm font-medium text-gray-900">Gambar (Biarkan
                                kosong jika tidak diubah)</label>
                            <input type="file" id="gambar_edit" name="gambar" accept="image/*"
                                class="pl-[27px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            <img id="current_gambar_preview" src="" alt="Current Image" class="w-24 h-24 object-cover mt-2 rounded-md hidden">
                        </div>
                        <div>
                            <label for="status_edit_modal" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                            <select name="status" id="status_edit_modal"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="tersedia">tersedia</option>
                                <option value="dipinjam">dipinjam</option>
                            </select>
                        </div>
                        <div>
                            <label for="jenis_edit_modal" class="block mb-2 text-sm font-medium text-gray-900">Jenis</label>
                            <select name="jenis" id="jenis_edit_modal"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="" disabled>Pilih Jenis</option>
                                <?php foreach ($jenis_options as $jenis): ?>
                                    <option value="<?php echo htmlspecialchars($jenis['id_jenis']); ?>">
                                        <?php echo htmlspecialchars($jenis['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="state_edit_modal" class="block mb-2 text-sm font-medium text-gray-900">State</label>
                            <select name="state" id="state_edit_modal"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                                <option value="" disabled>Pilih State</option>
                                <?php foreach ($state_options as $state): ?>
                                    <option value="<?php echo htmlspecialchars($state['id_state']); ?>">
                                        <?php echo htmlspecialchars($state['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Perbarui Barang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const basePath = "<?php echo Helper::basePath(); ?>";

        function showDynamicAlert(type, title, message) {
            const alertDiv = document.getElementById('dynamic-alert');
            const alertTitle = document.getElementById('alert-title');
            const alertText = document.getElementById('alert-text');

            alertDiv.className = `p-4 mb-4 text-sm rounded-lg alert-${type}`; // Reset classes and add new
            alertDiv.classList.remove('hidden');
            alertTitle.textContent = title;
            alertText.textContent = message;

            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertDiv.classList.add('hidden');
            }, 5000);
        }

        // Add Barang Form Submission
        document.getElementById('addBarangForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            try {
                const response = await fetch(basePath + 'barang/create', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showDynamicAlert('success', 'Sukses!', result.message);
                    // Perbaikan: Tambahkan pengecekan sebelum mengakses Flowbite.flowbite.Modal
                    if (typeof Flowbite !== 'undefined' && Flowbite.flowbite && Flowbite.flowbite.Modal) {
                        const addModal = Flowbite.flowbite.Modal.getInstance(document.getElementById('defaultModal'));
                        if (addModal) addModal.hide();
                    } else {
                        console.warn("Flowbite Modal object not yet available or not found for addModal. Attempting manual close.");
                        // Fallback manual close
                        document.getElementById('defaultModal').classList.add('hidden');
                        document.getElementById('defaultModal').setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('overflow-hidden');
                        // Optional: remove padding if Flowbite applies it to body
                        // document.body.style.paddingRight = ''; 
                    }
                    setTimeout(() => {
                        location.reload(); // Reload page to show new data
                    }, 1000);
                } else {
                    showDynamicAlert('error', 'Gagal!', result.message);
                }
            } catch (error) {
                console.error('Error adding barang:', error);
                showDynamicAlert('error', 'Error!', 'Terjadi kesalahan saat menambahkan barang.');
            }
        });

        // Edit Barang Form Submission
        document.getElementById('editBarangForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            try {
                const response = await fetch(basePath + 'barang/update', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showDynamicAlert('success', 'Sukses!', result.message);
                    // Perbaikan: Tambahkan pengecekan sebelum mengakses Flowbite.flowbite.Modal
                    if (typeof Flowbite !== 'undefined' && Flowbite.flowbite && Flowbite.flowbite.Modal) {
                        const editModal = Flowbite.flowbite.Modal.getInstance(document.getElementById('editModal'));
                        if (editModal) editModal.hide();
                    } else {
                        console.warn("Flowbite Modal object not yet available or not found for editModal. Attempting manual close.");
                        // Fallback manual close
                        document.getElementById('editModal').classList.add('hidden');
                        document.getElementById('editModal').setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('overflow-hidden');
                        // Optional: remove padding if Flowbite applies it to body
                        // document.body.style.paddingRight = '';
                    }
                    setTimeout(() => {
                        location.reload(); // Reload page to show updated data
                    }, 1000);
                } else {
                    showDynamicAlert('error', 'Gagal!', result.message);
                }
            } catch (error) {
                console.error('Error updating barang:', error);
                showDynamicAlert('error', 'Error!', 'Terjadi kesalahan saat memperbarui barang.');
            }
        });

        // Populate Edit Modal
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                const idBarang = this.dataset.id_barang; // This contains kode_barang from PHP loop
                const kodeBarang = this.dataset.kode_barang;
                const status = this.dataset.status;
                const jenisId = this.dataset.jenis_id;
                const stateId = this.dataset.state_id;
                const gambarUrl = this.dataset.gambar_url;

                // Populate form fields
                document.getElementById('id_barang_edit').value = idBarang; // Hidden input for identifier
                document.getElementById('kode_barang_edit').value = kodeBarang;
                document.getElementById('status_edit_modal').value = status;
                document.getElementById('jenis_edit_modal').value = jenisId;
                document.getElementById('state_edit_modal').value = stateId;

                // Handle image preview
                const currentGambarPreview = document.getElementById('current_gambar_preview');
                if (gambarUrl) {
                    currentGambarPreview.src = basePath + 'storages/' + gambarUrl;
                    currentGambarPreview.classList.remove('hidden');
                } else {
                    currentGambarPreview.classList.add('hidden');
                    currentGambarPreview.src = '';
                }
            });
        });

        // Delete Barang Function
        async function deleteBarang(idBarang, kodeBarang) {
            if (confirm(`Apakah Anda yakin ingin menghapus barang: ${kodeBarang}?`)) {
                try {
                    const response = await fetch(basePath + 'barang/delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id_barang=${encodeURIComponent(idBarang)}` // Pass kode_barang as id_barang
                    });

                    const result = await response.json();

                    if (result.success) {
                        showDynamicAlert('success', 'Sukses!', result.message);
                        setTimeout(() => {
                            location.reload(); // Reload page to reflect deletion
                        }, 1000);
                    } else {
                        showDynamicAlert('error', 'Gagal!', result.message);
                    }
                } catch (error) {
                    console.error('Error deleting barang:', error);
                    showDynamicAlert('error', 'Error!', 'Terjadi kesalahan saat menghapus barang.');
                }
            }
        }
    </script>
</body>

</html>