<?php
require_once "../utils/get_names.php";
require_once __DIR__ . '/../config/db.php';

// Memulai sesi hanya jika belum ada sesi yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$db = new db();
$conn = $db->conn;

// --- HANYA LOGIKA READ ---
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Gunakan koneksi dari $db
$pdo = $db->conn;

// READ (Fetch barang dengan JOIN untuk mendapatkan nama jenis dan state)
try {
    $sql_read = "SELECT 
                    b.id_barang, 
                    b.kode_barang, 
                    b.status,
                    b.jenis_id,
                    b.state_id,
                    j.nama as nama_jenis,
                    s.nama as nama_state
                FROM `barang` b
                LEFT JOIN `jenis` j ON b.jenis_id = j.id_jenis
                LEFT JOIN `state` s ON b.state_id = s.id_state
                ORDER BY b.kode_barang ASC";
    error_log("Executing SQL for READ barang: " . $sql_read);
    $result = $pdo->query($sql_read);
    $barangs = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $barangs[] = $row;
        }
        $result->free_result();
    } else {
        $_SESSION['message'] = 'Gagal mengambil data barang: ' . $pdo->error;
        $_SESSION['message_type'] = 'error';
        error_log('MySQLi Error READ: ' . $pdo->error . ' Query: ' . $sql_read);
    }
} catch (\Exception $e) {
    $_SESSION['message'] = 'Error mengambil data barang: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
    $barangs = [];
}

// Fetch data untuk informasi tambahan (jenis dan state) - hanya untuk tampilan
$jenis_options = [];
$state_options = [];

try {
    // Ambil data jenis
    $sql_jenis = "SELECT id_jenis, nama FROM `jenis` ORDER BY nama ASC";
    $result_jenis = $pdo->query($sql_jenis);
    if ($result_jenis) {
        while ($row = $result_jenis->fetch_assoc()) {
            $jenis_options[] = $row;
        }
        $result_jenis->free_result();
    }

    // Ambil data state
    $sql_state = "SELECT id_state, nama FROM `state` ORDER BY nama ASC";
    $result_state = $pdo->query($sql_state);
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
    <title>Admin Panel - Data Barang (Read Only)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link:hover {
            background-color: #E0E7FF;
            color: #3B82F6;
        }

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

        .read-only-badge {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #FBBF24;
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
                        },
                        dashboardBg: '#dbeafe',
                        sidebarBg: '#FFFFFF',
                        sidebarText: '#3b82f6',
                        sidebarIcon: '#9CA3AF',
                        sidebarHoverBg: '#E0E7FF',
                        sidebarActiveBg: '#E0E7FF',
                        sidebarActiveText: '#3B82F6',
                        sidebarActiveIcon: '#3B82F6'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-dashboardBg">

    <div class="flex h-screen">
        <aside id="sidebar" class="w-60 bg-sidebarBg text-sidebarText flex flex-col shadow-lg
                          fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 z-50">
            <div class="h-20 flex items-center justify-center border-b border-blue-100">
                <h1 class="text-2xl font-bold text-blue-600">Inventaris</h1>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="/inventaris-barang-kantor/admin"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
                <a href="/inventaris-barang-kantor/barang_user"
                    class="sidebar-link active flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Barang
                </a>
                <a href="/inventaris-barang-kantor/peminjaman"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Peminjaman
                </a>
                <a href="/inventaris-barang-kantor/pengembalian"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pengembalian
                </a>
            </nav>

            <div class="px-4 py-4 border-t border-blue-100">
                <a href="/inventaris-barang-kantor/logout"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-red-50 hover:text-red-600">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <header class="bg-dashboardBg shadow-sm h-16 flex items-center px-8 justify-between border-b border-gray-200">
                <div class="flex items-center">
                    <button id="menu-toggle" class="md:hidden mr-4 text-blue-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-3xl font-semibold text-blue-600">Data Barang</h2>
                </div>
            </header>

            <div class="p-8 bg-dashboardBg min-h-[calc(100vh-4rem)] overflow-y-auto">
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
                    <h3 class="text-xl font-semibold text-blue-700 mb-4">Daftar Barang</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Kode Barang</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Jenis</th>
                                    <th scope="col" class="px-6 py-3">State</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($barangs)): ?>
                                    <?php foreach ($barangs as $barang): ?>
                                        <tr class="bg-white border-b hover:bg-blue-50">
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                <?php echo htmlspecialchars($barang['id_barang']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($barang['kode_barang']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($barang['status']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($barang['nama_jenis'] ?? 'N/A'); ?>
                                            </td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($barang['nama_state'] ?? 'N/A'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada barang atau gagal
                                            memuat data.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggling Logic
            const sidebar = document.getElementById('sidebar');
            const menuToggleButton = document.getElementById('menu-toggle');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
            }

            if (menuToggleButton) {
                menuToggleButton.addEventListener('click', toggleSidebar);
            }

            document.addEventListener('click', (event) => {
                if (window.innerWidth < 768 && !sidebar.contains(event.target) && !menuToggleButton.contains(event.target)) {
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            });

            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('relative');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('relative');
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize();
        });
    </script>

</body>

</html>