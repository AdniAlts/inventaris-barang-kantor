<?php
require_once "../utils/get_names.php";
require_once __DIR__ . '/../config/db.php';

// Memulai sesi hanya jika belum ada sesi yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$db = new db();
$conn = $db->conn;

require_once __DIR__ . '/../utils/get_names.php';

$kategori_list = GetNames::category($conn);
$state_list = GetNames::state($conn);


// --- LOGIKA CRUD ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);


// CREATE
if ($action === 'add_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = trim($_POST['kode_barang'] ?? '');
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $kondisi_id = $_POST['kondisi_id'] ?? null; 
    $kategori_id = $_POST['kategori_id'] ?? null;
    $status_barang = 'tersedia'; 

    if (!empty($kode_barang) && !empty($nama_barang) && !empty($kondisi_id) && !empty($kategori_id)) {
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM `barang` WHERE `kode_barang` = ?");
            $stmt_check->execute([$kode_barang]);
            if ($stmt_check->fetchColumn() > 0) {
                $_SESSION['message'] = 'Gagal! Kode Barang sudah ada.';
                $_SESSION['message_type'] = 'error';
            } else {
                $sql = "INSERT INTO `barang` (`kode_barang`, `nama`, `status`, `state_id`, `kategori_id`) VALUES (?, ?, ?, ?, ?)";               
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$kode_barang, $nama_barang, $status_barang, $kondisi_id, $kategori_id]);
                $_SESSION['message'] = 'Produk berhasil ditambahkan!';
                $_SESSION['message_type'] = 'success';
            }
        } catch (\PDOException $e) {
            $_SESSION['message'] = 'Gagal menambahkan produk: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Semua field wajib diisi.';
        $_SESSION['message_type'] = 'error';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// UPDATE
if ($action === 'update_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang_orig = $_POST['kode_barang_orig'] ?? ''; 
    $nama_barang_edit = trim($_POST['nama_barang_edit'] ?? '');
    $kondisi_id_edit = $_POST['kondisi_id_edit'] ?? null;
    $kategori_id_edit = $_POST['kategori_id_edit'] ?? null;
    $status_edit = $_POST['status_edit'] ?? 'tersedia';

    if (!empty($kode_barang_orig) && !empty($nama_barang_edit) && !empty($kondisi_id_edit) && !empty($kategori_id_edit)) {
        try {
            $sql = "UPDATE `barang` SET `nama` = ?, `status` = ?, `state_id` = ?, `kategori_id` = ? WHERE `kode_barang` = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama_barang_edit, $status_edit, $kondisi_id_edit, $kategori_id_edit, $kode_barang_orig]);
            $_SESSION['message'] = 'Produk berhasil diperbarui!';
            $_SESSION['message_type'] = 'success';
        } catch (\PDOException $e) {
            $_SESSION['message'] = 'Gagal memperbarui produk: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Semua field wajib diisi untuk update.';
        $_SESSION['message_type'] = 'error';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// DELETE
if ($action === 'delete_product' && isset($_GET['kode'])) {
    $kode_to_delete = $_GET['kode'];
    try {
        $sql = "DELETE FROM `barang` WHERE `kode_barang` = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_to_delete]);
        $_SESSION['message'] = 'Produk berhasil dihapus!';
        $_SESSION['message_type'] = 'success';
    } catch (\PDOException $e) {
        if ($e->getCode() == '23000') { 
             $_SESSION['message'] = 'Gagal menghapus produk: Produk ini mungkin sedang digunakan dalam transaksi peminjaman.';
        } else {
             $_SESSION['message'] = 'Gagal menghapus produk: ' . $e->getMessage();
        }
        $_SESSION['message_type'] = 'error';
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// Gunakan koneksi dari $db
$pdo = $db->conn;
// READ (Fetch products for display)
try {
    // Perbaikan query: konsisten menggunakan alias `b`.`nama`
    $sql_read = "SELECT `b`.`kode_barang`, `b`.`nama`, `b`.`status`, `s`.`nama` AS `nama_kondisi`, `k`.`nama` AS `nama_kategori`, `b`.`state_id`, `b`.`kategori_id`
                 FROM `barang` `b`
                 JOIN `state` `s` ON `b`.`state_id` = `s`.`id_state`
                 JOIN `kategori` `k` ON `b`.`kategori_id` = `k`.`id_kategori`                 
                 ORDER BY `b`.`nama` ASC"; // Menggunakan `b`.`nama` di ORDER BY                
    error_log("Executing SQL for READ (v3 - direct table.column for nama): " . $sql_read); 
    $result = $pdo->query($sql_read);
    $products = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $result->free_result(); 
    } else {
        $_SESSION['message'] = 'Gagal mengambil data produk: ' . $pdo->error;
        $_SESSION['message_type'] = 'error';
        error_log('MySQLi Error READ: ' . $pdo->error . ' Query: ' . $sql_read);
    }
}  finally {
     $db->close();
 }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Inventaris Barang (MySQL)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link:hover, .sidebar-link.active { background-color: #E0E7FF; color: #3B82F6; }
        .sidebar-link.active svg { color: #3B82F6; }
        .alert-success { background-color: #D1FAE5; color: #065F46; border-left: 4px solid #10B981; }
        .alert-error { background-color: #FEE2E2; color: #991B1B; border-left: 4px solid #EF4444; }
        .alert-info { background-color: #DBEAFE; color: #1E40AF; border-left: 4px solid #3B82F6; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a',950:'#172554'}}}}
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
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="sidebar-link active flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="/inventaris-barang-kantor/kategori" class="sidebar-link flex items-center px-4 py-3 text-gray-600 rounded-lg">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Kategori
                </a>
                </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-semibold text-blue-700">Manajemen Barang</h2>
                <button data-modal-target="defaultModal" data-modal-toggle="defaultModal" class="block text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="button">
                    Tambah Barang
                </button>
            </div>

            <?php if (!empty($message)): ?>
            <div id="alert-message" class="p-4 mb-4 text-sm rounded-lg <?php echo $message_type === 'success' ? 'alert-success' : ($message_type === 'error' ? 'alert-error' : 'alert-info'); ?>" role="alert">
                <span class="font-medium"><?php echo ucfirst($message_type); ?>!</span> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-current rounded-lg focus:ring-2 focus:ring-current p-1.5 hover:bg-current/10 inline-flex items-center justify-center h-8 w-8" onclick="document.getElementById('alert-message').style.display='none'" aria-label="Close">
                    <span class="sr-only">Dismiss</span><svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-xl font-semibold text-blue-700 mb-4">Daftar Barang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Kode</th>
                                <th scope="col" class="px-6 py-3">Nama Barang</th>
                                <th scope="col" class="px-6 py-3">Kategori</th>
                                <th scope="col" class="px-6 py-3">Kondisi</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                <tr class="bg-white border-b hover:bg-blue-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?php echo htmlspecialchars($product['kode_barang']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($product['nama']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($product['nama_kategori']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($product['nama_kondisi']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $product['status'] == 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($product['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button"
                                                data-modal-target="editModal"
                                                data-modal-toggle="editModal"
                                                data-kode_barang="<?php echo htmlspecialchars($product['kode_barang']); ?>"
                                                data-nama="<?php echo htmlspecialchars($product['nama']); ?>"
                                                data-kategori_id="<?php echo htmlspecialchars($product['kategori_id']); ?>"
                                                data-state_id="<?php echo htmlspecialchars($product['state_id']); ?>"
                                                data-status="<?php echo htmlspecialchars($product['status']); ?>"
                                                class="font-medium text-blue-600 hover:underline mr-3 edit-button">Edit</button>
                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete_product&kode=<?php echo htmlspecialchars($product['kode_barang']); ?>"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk: <?php echo htmlspecialchars(addslashes($product['nama'])); ?> (<?php echo htmlspecialchars($product['kode_barang']); ?>)?');"
                                           class="font-medium text-red-600 hover:underline">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada barang atau gagal memuat data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="defaultModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Barang Baru</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="defaultModal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="add_product">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="kode_barang_add" class="block mb-2 text-sm font-medium text-gray-900">Kode Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang_add" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: BRG001" required>
                        </div>
                        <div>
                            <label for="nama_barang_add" class="block mb-2 text-sm font-medium text-gray-900">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang_add" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Contoh: Laptop ASUS" required>
                        </div>
                        <div>
                            <label for="kategori_id_add" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                            <select id="kategori_id_add" name="kategori_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <option value="">Pilih kategori</option>
                                <?php foreach ($kategori_list as $kategori_item): ?>
                                    <option value="<?php echo htmlspecialchars($kategori_item['id_kategori']); ?>"><?php echo htmlspecialchars($kategori_item['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="kondisi_id_add" class="block mb-2 text-sm font-medium text-gray-900">Kondisi (State)</label>
                            <select id="kondisi_id_add" name="kondisi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <option value="">Pilih kondisi</option>
                                <?php foreach ($state_list as $state_item): ?>
                                    <option value="<?php echo htmlspecialchars($state_item['id_state']); ?>"><?php echo htmlspecialchars($state_item['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Barang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow sm:p-5">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Barang</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="editModal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="action" value="update_product">
                    <input type="hidden" name="kode_barang_orig" id="kode_barang_orig_edit"> 
                    
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="kode_barang_edit_display" class="block mb-2 text-sm font-medium text-gray-900">Kode Barang</label>
                            <input type="text" name="kode_barang_edit_display" id="kode_barang_edit_display" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" readonly>
                        </div>
                        <div>
                            <label for="nama_barang_edit" class="block mb-2 text-sm font-medium text-gray-900">Nama Barang</label>
                            <input type="text" name="nama_barang_edit" id="nama_barang_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
                        </div>
                        <div>
                            <label for="kategori_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Kategori</label>
                            <select id="kategori_id_edit" name="kategori_id_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <?php foreach ($kategori_list as $kategori_item): ?>
                                    <option value="<?php echo htmlspecialchars($kategori_item['id_kategori']); ?>"><?php echo htmlspecialchars($kategori_item['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="kondisi_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Kondisi (State)</label>
                            <select id="kondisi_id_edit" name="kondisi_id_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <?php foreach ($state_list as $state_item): ?>
                                    <option value="<?php echo htmlspecialchars($state_item['id_state']); ?>"><?php echo htmlspecialchars($state_item['nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="status_edit" class="block mb-2 text-sm font-medium text-gray-900">Status Barang</label>
                            <select id="status_edit" name="status_edit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="dipinjam">Dipinjam</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script>
        setTimeout(function() {
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.style.transition = 'opacity 0.5s ease';
                alertMessage.style.opacity = '0';
                setTimeout(() => alertMessage.style.display = 'none', 500);
            }
        }, 5000);

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('kode_barang_orig_edit').value = this.dataset.kode_barang;
                    document.getElementById('kode_barang_edit_display').value = this.dataset.kode_barang;
                    document.getElementById('nama_barang_edit').value = this.dataset.nama;
                    document.getElementById('kategori_id_edit').value = this.dataset.kategori_id;
                    document.getElementById('kondisi_id_edit').value = this.dataset.state_id;
                    document.getElementById('status_edit').value = this.dataset.status;
                });
            });
        });
    </script>
</body>
</html>
