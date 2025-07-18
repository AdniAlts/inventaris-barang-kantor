<?php
require_once __DIR__ . "/../../utils/get_names.php";
require_once __DIR__ . '/../../config/db.php';

// Memulai sesi hanya jika belum ada sesi yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$db = new db();
$conn = $db->conn;

// --- LOGIKA CRUD ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// CREATE
if ($action === 'add_state' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_state = trim($_POST['nama_state'] ?? '');

    if (!empty($nama_state)) {
        try {
            $sql_check = "SELECT COUNT(*) as count FROM state WHERE nama = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param('s', $nama_state);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $row_check = $result_check->fetch_assoc();

            if ($row_check['count'] > 0) {
                $_SESSION['message'] = 'Gagal! Nama state sudah ada.';
                $_SESSION['message_type'] = 'error';
            } else {
                $sql = "INSERT INTO state (nama) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $nama_state);
                $stmt->execute();
                $_SESSION['message'] = 'State berhasil ditambahkan!';
                $_SESSION['message_type'] = 'success';
                $stmt->close();
            }
            $stmt_check->close();
        } catch (\Exception $e) {
            $_SESSION['message'] = 'Gagal menambahkan state: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Nama state wajib diisi.';
        $_SESSION['message_type'] = 'error';
    }
    header("Location: /inventaris-barang-kantor/state");
    exit;
}

// UPDATE
if ($action === 'update_state' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_state = $_POST['id_state'] ?? '';
    $nama_state_edit = trim($_POST['nama_state_edit'] ?? '');

    if (!empty($id_state) && !empty($nama_state_edit)) {
        try {
            $sql_check = "SELECT COUNT(*) as count FROM state WHERE nama = ? AND id_state != ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param('si', $nama_state_edit, $id_state);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $row_check = $result_check->fetch_assoc();

            if ($row_check['count'] > 0) {
                $_SESSION['message'] = 'Gagal! Nama state sudah ada.';
                $_SESSION['message_type'] = 'error';
            } else {
                $sql = "UPDATE state SET nama = ? WHERE id_state = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $nama_state_edit, $id_state);
                $stmt->execute();
                $_SESSION['message'] = 'State berhasil diperbarui!';
                $_SESSION['message_type'] = 'success';
                $stmt->close();
            }
            $stmt_check->close();
        } catch (\Exception $e) {
            $_SESSION['message'] = 'Gagal memperbarui state: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Semua field wajib diisi untuk update.';
        $_SESSION['message_type'] = 'error';
    }
    header("Location: /inventaris-barang-kantor/state");
    exit;
}

// DELETE
if ($action === 'delete_state' && isset($_POST['id'])) {
    $id_to_delete = $_POST['id'];
    try {
        $sql = "DELETE FROM state WHERE id_state = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id_to_delete);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = 'State berhasil dihapus!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'State tidak ditemukan atau gagal dihapus.';
            $_SESSION['message_type'] = 'error';
        }
        $stmt->close();
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), '1451') !== false || strpos($e->getMessage(), 'foreign key constraint') !== false) {
            $_SESSION['message'] = 'Gagal menghapus state: State ini sedang digunakan oleh barang lain.';
        } else {
            $_SESSION['message'] = 'Gagal menghapus state: ' . $e->getMessage();
        }
        $_SESSION['message_type'] = 'error';
    }
    header("Location: /inventaris-barang-kantor/state");
    exit;
}

// READ (Fetch states for display)
$states = [];
try {
    $sql_read = "SELECT id_state, nama FROM state ORDER BY nama ASC";
    $result = $conn->query($sql_read);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $states[] = $row;
        }
        $result->free_result();
    } else {
        $_SESSION['message'] = 'Gagal mengambil data state: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
    }
} finally {
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manajemen State</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            theme: { extend: { colors: { primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554' } } } }
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
                <a href="/inventaris-barang-kantor/dashboard"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
                <a href="/inventaris-barang-kantor/kategori"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Kategori
                </a>
                <a href="/inventaris-barang-kantor/jenis"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Jenis
                </a>
                <a href="/inventaris-barang-kantor/state"
                    class="sidebar-link active flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    State
                </a>
                <a href="/inventaris-barang-kantor/barang"
                    class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Barang
                </a>
            </nav>
            <!-- Logout Button at Bottom -->
            <div class="px-4 py-4 border-t border-blue-100">
                <a href="/inventaris-barang-kantor/logout"
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
                <h2 class="text-3xl font-semibold text-blue-700">Manajemen State</h2>
                <button data-modal-target="defaultModal" data-modal-toggle="defaultModal"
                    class="block text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                    type="button">
                    Tambah State
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

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold text-blue-700 mb-4">Daftar State</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">ID</th>
                                <th scope="col" class="px-6 py-3">Nama State</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($states)): ?>
                                <?php foreach ($states as $state): ?>
                                    <tr class="bg-white border-b hover:bg-blue-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            <?php echo htmlspecialchars($state['id_state']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($state['nama']); ?></td>
                                        <td class="px-6 py-4">
                                            <button type="button" data-modal-target="editModal" data-modal-toggle="editModal"
                                                data-id_state="<?php echo htmlspecialchars($state['id_state']); ?>"
                                                data-nama="<?php echo htmlspecialchars($state['nama']); ?>"
                                                class="font-medium text-blue-600 hover:underline mr-3 edit-button">Edit</button>
                                            <form action="/inventaris-barang-kantor/state" method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_state">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($state['id_state']); ?>">
                                                <button type="submit" class="font-medium text-red-600 hover:underline bg-transparent border-0 p-0 m-0 cursor-pointer">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada state atau gagal
                                        memuat data.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah State -->
    <div id="defaultModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah State Baru</h3>
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
                <form action="/inventaris-barang-kantor/state" method="POST">
                    <input type="hidden" name="action" value="add_state">
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="nama_state_add" class="block mb-2 text-sm font-medium text-gray-900">Nama
                                State</label>
                            <input type="text" name="nama_state" id="nama_state_add"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Contoh: Tersedia" required>
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan State
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit State -->
    <div id="editModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Edit State</h3>
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
                <form action="/inventaris-barang-kantor/state" method="POST">
                    <input type="hidden" name="action" value="update_state">
                    <input type="hidden" name="id_state" id="id_state_edit">

                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="nama_state_edit" class="block mb-2 text-sm font-medium text-gray-900">Nama
                                State</label>
                            <input type="text" name="nama_state_edit" id="nama_state_edit"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                required>
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script>
        // Auto hide alert message
        setTimeout(function () {
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.style.transition = 'opacity 0.5s ease';
                alertMessage.style.opacity = '0';
                setTimeout(() => alertMessage.style.display = 'none', 500);
            }
        }, 5000);

        // Handle edit button click
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('id_state_edit').value = this.dataset.id_state;
                    document.getElementById('nama_state_edit').value = this.dataset.nama;
                });
            });
        });
    </script>
</body>

</html>