<?php
require_once "../config/db.php";
$db = new db();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Peminjaman</title>
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
                <a href="/inventaris-barang-kantor/"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dashboard
                </a>
                <a href="/inventaris-barang-kantor/barang_user"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Barang
                </a>
                <a href="/inventaris-barang-kantor/peminjaman"
                    class="sidebar-link active flex items-center px-4 py-3 rounded-lg">
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
                <a href="/inventaris-barang-kantor/login"
                    class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-red-50 hover:text-red-600">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Login
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
                    <h2 class="text-3xl font-semibold text-blue-600">Peminjaman</h2>
                </div>
            </header>

            <div class="p-8 bg-dashboardBg min-h-[calc(100vh-4rem)] overflow-y-auto">
                <!-- Alert Messages -->
                <?php if (isset($_GET['error'])): ?>
                    <div id="alert-error" class="p-4 mb-4 text-sm rounded-lg alert-error" role="alert">
                        <span class="font-medium">Error!</span>
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div id="alert-success" class="p-4 mb-4 text-sm rounded-lg alert-success" role="alert">
                        <span class="font-medium">Success!</span>
                        <?php echo $_GET['success']; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Tambah Barang -->
                <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
                    <h3 class="text-xl font-semibold text-blue-700 mb-4">Tambah Barang untuk Dipinjam</h3>
                    <form action="" method="GET" class="space-y-4">
                        <div>
                            <label for="barang" class="block text-sm font-medium text-gray-700 mb-2">Barang:</label>
                            <select name="barang" id="barang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Barang --</option>
                                <?php
                                foreach ($querys as $query) {
                                    echo "<option value='{$query['nama']}'>{$query['nama']} | {$query['stok']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah:</label>
                            <input type="number" name="jumlah" id="jumlah" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <input type="submit" name="tambah" value="Tambah"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    </form>
                </div>

                <!-- Tabel Daftar Barang yang Dipinjam -->
                <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
                    <h3 class="text-xl font-semibold text-blue-700 mb-4">Daftar Barang yang Akan Dipinjam</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-blue-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Barang</th>
                                    <th scope="col" class="px-6 py-3">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                if (isset($_COOKIE['peminjaman'])) {
                                    $data = json_decode($_COOKIE['peminjaman'], true);
                                    foreach ($data as $item) {
                                        $total += $item['jumlah'];
                                        echo "<tr class='bg-white border-b hover:bg-blue-50'>
                                                <td class='px-6 py-4 font-medium text-gray-900'>{$item['barang']}</td>
                                                <td class='px-6 py-4'>{$item['jumlah']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr>
                                            <td colspan='2' class='px-6 py-4 text-center text-gray-500'>Belum ada barang yang dipilih</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Total Jumlah</th>
                                    <th scope="col" class="px-6 py-3"><?php echo $total; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Reset Button -->
                    <div class="mt-4">
                        <?php
                        if (isset($_COOKIE['peminjaman'])) {
                            echo '<form action="" method="get">
                                            <input type="submit" name="reset" value="Reset"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                        </form>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <div class="flex justify-center items-center">

                        <!-- Submit Peminjaman -->
                        <form action="/inventaris-barang-kantor/loan" method="POST" class="w-full">
                            <div class="mt-4">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi:</label>
                                <textarea type="number" name="deskripsi" id="deskripsi" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>
                            <div>
                                <?php
                                if (isset($_COOKIE['peminjaman'])) {
                                    $data = json_decode($_COOKIE['peminjaman'], true);
                                    foreach ($data as $index => $item) {
                                        echo "<input type='hidden' name='barang[$index][nama]' value='{$item['barang']}'>";
                                        echo "<input type='hidden' name='barang[$index][jumlah]' value='{$item['jumlah']}'>";
                                    }
                                    echo "<input type='hidden' name='total' value='{$total}'>";
                                }
                                ?>
                                <button type="submit"
                                    class="mt-3 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                    Submit Peminjaman
                                </button>
                            </div>
                    </div>
                    </form>
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