<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Barang Kantor - Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Kelas untuk sidebar links (sesuai tema biru) */
        .sidebar-link:hover {
            background-color: #E0E7FF;
            /* blue-100 */
            color: #3B82F6;
            /* blue-600 */
        }

        .sidebar-link.active {
            background-color: #E0E7FF;
            /* blue-100 */
            color: #3B82F6;
            /* blue-600 */
        }

        .sidebar-link.active svg {
            color: #3B82F6;
            /* blue-600 */
        }

        /* Alert styles (jika Anda akan menggunakannya) */
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
                        // Palet warna default Tailwind (blue-50 sampai blue-950) sudah ada di Flowbite
                        // Menambahkan warna kustom dari kedua desain
                        dashboardBg: '#dbeafe', // Latar belakang umum, mirip blue-50 tapi lebih abu
                        sidebarBg: '#FFFFFF', // Sidebar putih
                        sidebarText: '#3b82f6', // Abu-abu gelap untuk teks sidebar
                        sidebarIcon: '#9CA3AF', // Abu-abu lebih terang untuk ikon sidebar
                        sidebarHoverBg: '#E0E7FF', // blue-100
                        sidebarActiveBg: '#E0E7FF', // blue-100
                        sidebarActiveText: '#3B82F6', // blue-600
                        sidebarActiveIcon: '#3B82F6', // blue-600

                        // Warna untuk konten utama (table, buttons, modal)
                        mainContentBg: '#FFFFFF', // Kontainer utama konten (putih)
                        primaryText: '#333333', // Teks utama gelap
                        secondaryText: '#6B7280', // Teks sekunder/placeholder/label
                        borderColorLight: '#E5E7EB', // Border ringan

                        tableHeaderBgLight: '#F3F4F6', // Latar belakang header tabel terang
                        tableHeaderText: '#4B5563', // Teks header tabel gelap

                        buttonGreen: '#10B981', // Hijau untuk "Add New Category"
                        buttonRed: '#EF4444', // Merah untuk "Export PDF" / Delete
                        buttonBluePrimary: '#3B82F6', // Biru utama untuk "Export Excel" / Edit

                        paginationBgLight: '#F3F4F6', // Latar belakang pagination terang
                        paginationBorderLight: '#D1D5DB', // Border pagination terang
                        paginationTextDark: '#4B5563', // Teks pagination gelap
                        paginationActiveBgLight: '#BFDBFE', // Latar belakang aktif pagination (blue-200)
                        paginationActiveTextDark: '#1E40AF', // Teks aktif pagination (blue-800)

                        // Warna untuk Modal
                        modalOverlay: 'rgba(0,0,0,0.5)',
                        modalBg: '#FFFFFF', // Latar belakang modal putih
                        modalText: '#333333', // Teks di modal
                        modalInputBorder: '#D1D5DB', // Border input modal
                        modalInputBg: '#F9FAFB', // Latar belakang input modal
                        modalButtonCancel: '#EF4444', // Merah cancel
                        modalButtonSubmit: '#10B981', // Hijau submit
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'], // Font utama
                    },
                },
            },
            plugins: [],
        };
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
                <a href="/inventaris-barang-kantor/home"
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
            <header class="bg-dashboardBg h-16 flex items-center px-8 justify-between border-b border-gray-200">
                <div class="flex items-center">
                    <h2 class="text-3xl font-semibold text-blue-600 mt-9">List Of Categories</h2>
                </div>
            </header>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-8">
                <div class="flex items-center text-secondaryText">
                    <span class="mr-2">Show</span>
                    <select id="entries-select" class="bg-white border border-borderColorLight text-primaryText rounded-md p-1 focus:outline-none focus:ring-1 focus:ring-blue-300">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                    <span class="ml-2">entries</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 text-secondaryText">Search:</span>
                    <input type="text" id="search-input-table" class="bg-white border border-borderColorLight text-primaryText rounded-md p-1 focus:outline-none focus:ring-1 focus:ring-blue-300">
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-borderColorLight px-8 py-1">
                <div class="max-h-[323px] overflow-y-auto">
                    <table id="category-table" class="min-w-full divide-y divide-borderColorLight">
                        <thead class="bg-tableHeaderBgLight sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-tableHeaderText">
                                    NO
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-tableHeaderText">
                                    Nama Barang
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="bg-white divide-y divide-borderColorLight">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">1</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Meja</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">2</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Kursi</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">3</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Laptop</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">4</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Proyektor</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">5</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Pulpen</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">6</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Penghapus</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">7</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Sapu</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">8</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Lap</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">9</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Router</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">10</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Kabel LAN</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">11</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Dispenser</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">12</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Teko</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">13</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Speaker</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">14</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Mikrofon</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">15</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Lampu Meja</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">16</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Senter</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">17</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Bola</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">18</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Matras</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">19</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Vas Bunga</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">20</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Pigura</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">21</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">CCTV</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">22</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Alarm</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">23</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Kipas Angin</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">24</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">AC</td>

                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">25</td>
                                <td class="px-6 py-4 whitespace-nowrap text-primaryText">Obeng</td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 text-paginationTextDark text-sm gap-4">
                    <span id="showing-entries-text">Showing 1 to 10 of X entries</span>
                    <nav class="flex space-x-1" aria-label="Pagination">
                        <a href="#" class="bg-paginationBgLight border border-paginationBorderLight text-paginationTextDark px-3 py-1 rounded-md hover:bg-gray-200 transition-colors" id="prev-page">Previous</a>
                        <span id="pagination-numbers" class="flex space-x-1">
                            <a href="#" class="bg-paginationActiveBgLight text-paginationActiveTextDark px-3 py-1 rounded-md font-semibold page-link" data-page="1">1</a>
                        </span>
                        <a href="#" class="bg-paginationBgLight border border-paginationBorderLight text-paginationTextDark px-3 py-1 rounded-md hover:bg-gray-200 transition-colors" id="next-page">Next</a>
                    </nav>
                </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Sidebar Toggling Logic ---
            const sidebar = document.querySelector('aside');
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

            // --- Table Pagination & Search Logic ---
            // Variabel-variabel ini akan dideklarasikan hanya jika elemennya ada di DOM
            const entriesSelect = document.getElementById('entries-select');
            const tableBody = document.getElementById('table-body');
            // allRows akan diinisialisasi hanya jika tableBody ada
            const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
            const showingEntriesText = document.getElementById('showing-entries-text');
            const paginationNumbersContainer = document.getElementById('pagination-numbers');
            const prevPageButton = document.getElementById('prev-page');
            const nextPageButton = document.getElementById('next-page');
            // Perbaikan: Gunakan ID yang sesuai untuk search input di tabel
            const searchInputTable = document.getElementById('search-input-table');

            let currentPage = 1;
            // Gunakan nilai default yang aman jika entriesSelect tidak ditemukan
            let entriesPerPage = entriesSelect ? parseInt(entriesSelect.value) : 10;

            function updateTable() {
                // Jalankan hanya jika elemen-elemen tabel dan search input yang relevan ada
                if (!tableBody || !showingEntriesText || !searchInputTable) return;

                const searchTerm = searchInputTable.value.toLowerCase();
                let filteredRows = allRows.filter(row => {
                    const rowText = row.textContent.toLowerCase();
                    return rowText.includes(searchTerm);
                });

                const totalRows = filteredRows.length;

                const startIndex = (currentPage - 1) * entriesPerPage;
                const endIndex = Math.min(startIndex + entriesPerPage, totalRows);

                // Sembunyikan semua baris terlebih dahulu
                allRows.forEach(row => row.style.display = 'none');

                if (totalRows === 0) {
                    showingEntriesText.textContent = `No matching entries found.`;
                    updatePagination(0);
                    return;
                }

                // Tampilkan hanya baris yang difilter dan dalam rentang halaman saat ini
                filteredRows.slice(startIndex, endIndex).forEach(row => {
                    row.style.display = '';
                });

                showingEntriesText.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalRows} entries`;
                updatePagination(totalRows);
            }

            function updatePagination(totalRows) {
                // Jalankan hanya jika elemen-elemen pagination ada
                if (!paginationNumbersContainer || !prevPageButton || !nextPageButton) return;

                paginationNumbersContainer.innerHTML = '';
                const totalPages = Math.ceil(totalRows / entriesPerPage);

                for (let i = 1; i <= totalPages; i++) {
                    const pageLink = document.createElement('a');
                    pageLink.href = '#';
                    pageLink.textContent = i;
                    pageLink.classList.add(
                        'px-3', 'py-1', 'rounded-md', 'font-semibold', 'page-link',
                        'transition-colors'
                    );
                    if (i === currentPage) {
                        pageLink.classList.add('bg-paginationActiveBgLight', 'text-paginationActiveTextDark');
                        pageLink.classList.remove('bg-paginationBgLight', 'border', 'border-paginationBorderLight', 'text-paginationTextDark', 'hover:bg-gray-200');
                    } else {
                        pageLink.classList.add('bg-paginationBgLight', 'border', 'border-paginationBorderLight', 'text-paginationTextDark', 'hover:bg-gray-200');
                        pageLink.classList.remove('bg-paginationActiveBgLight', 'text-paginationActiveTextDark');
                    }
                    pageLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = i;
                        updateTable();
                    });
                    paginationNumbersContainer.appendChild(pageLink);
                }

                // Menghidupkan/mematikan tombol Previous
                prevPageButton.classList.toggle('opacity-50', currentPage === 1 || totalPages === 0);
                prevPageButton.style.pointerEvents = (currentPage === 1 || totalPages === 0) ? 'none' : 'auto';

                // Menghidupkan/mematikan tombol Next
                nextPageButton.classList.toggle('opacity-50', currentPage === totalPages || totalPages === 0);
                nextPageButton.style.pointerEvents = (currentPage === totalPages || totalPages === 0) ? 'none' : 'auto';
            }

            // Event Listeners untuk elemen tabel (hanya jika elemennya ada)
            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    entriesPerPage = parseInt(this.value);
                    currentPage = 1; // Reset halaman saat entri per halaman berubah
                    updateTable();
                });
            }

            if (searchInputTable) { // Menggunakan ID baru
                searchInputTable.addEventListener('input', function() {
                    currentPage = 1; // Reset halaman saat input pencarian berubah
                    updateTable();
                });
            }

            if (prevPageButton) {
                prevPageButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        updateTable();
                    }
                });
            }

            if (nextPageButton) {
                nextPageButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Hitung ulang total baris yang difilter untuk tombol Next
                    const currentTotalRows = allRows.filter(row => {
                        const rowText = searchInputTable ? row.textContent.toLowerCase() : '';
                        return rowText.includes(searchInputTable ? searchInputTable.value.toLowerCase() : '');
                    }).length;

                    const totalPages = Math.ceil(currentTotalRows / entriesPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateTable();
                    }
                });
            }

            // Panggil updateTable saat halaman dimuat, hanya jika elemen tabel ada
            if (tableBody && showingEntriesText && searchInputTable) {
                updateTable();
            }

            // --- Export Excel Logic (dihapus per permintaan sebelumnya) ---
            // Kode ini sudah dihapus di HTML dan JavaScript Anda
            // const exportExcelBtn = document.getElementById('export-excel-btn');
            // const categoryTable = document.getElementById('category-table');
            // (kode terkait di sini telah dihapus)

            // --- Add Category Modal Logic ---
            const addCategoryBtn = document.getElementById('add-category-btn');
            const addCategoryModal = document.getElementById('add-category-modal');
            const modalCloseBtn = document.getElementById('modal-close-btn');
            const modalCancelBtn = document.getElementById('modal-cancel-btn');
            const modalSubmitBtn = document.getElementById('modal-submit-btn');
            const categoryNameInput = document.getElementById('category-name');

            function showModal() {
                // Pastikan modal ditemukan sebelum mencoba memanipulasinya
                if (addCategoryModal) {
                    addCategoryModal.classList.remove('hidden');
                    addCategoryModal.classList.add('flex'); // Tambahkan flex saat ditampilkan
                    document.body.classList.add('overflow-hidden'); // Mencegah scroll body
                }
            }

            function hideModal() {
                // Pastikan modal ditemukan sebelum mencoba memanipulasinya
                if (addCategoryModal) {
                    addCategoryModal.classList.add('hidden');
                    addCategoryModal.classList.remove('flex'); // Hapus flex saat disembunyikan
                    document.body.classList.remove('overflow-hidden'); // Mengizinkan scroll body kembali
                    // Pastikan input ditemukan sebelum mencoba membersihkannya
                    if (categoryNameInput) {
                        categoryNameInput.value = ''; // Bersihkan input saat modal ditutup
                    }
                }
            }

            // Event Listeners untuk modal (hanya jika elemennya ada)
            if (addCategoryBtn) {
                addCategoryBtn.addEventListener('click', showModal);
            }
            if (modalCloseBtn) {
                modalCloseBtn.addEventListener('click', hideModal);
            }
            if (modalCancelBtn) {
                modalCancelBtn.addEventListener('click', hideModal);
            }

            // Menutup modal jika klik di luar modal konten (hanya jika modal ada)
            if (addCategoryModal) {
                addCategoryModal.addEventListener('click', function(event) {
                    // Periksa apakah target klik adalah modal itu sendiri, bukan konten di dalamnya
                    if (event.target === addCategoryModal) {
                        hideModal();
                    }
                });
            }

            // Logika Submit (Contoh Sederhana) (hanya jika tombol submit ada)
            if (modalSubmitBtn) {
                modalSubmitBtn.addEventListener('click', function() {
                    // Pastikan input ditemukan sebelum mencoba membaca nilainya
                    if (categoryNameInput) {
                        const categoryName = categoryNameInput.value.trim();
                        if (categoryName) {
                            // Logika untuk menambahkan kategori baru ke tabel secara dinamis
                            // Karena `allRows` adalah salinan DOM, kita harus menambahkannya ke DOM dan ke `allRows`
                            const newRow = document.createElement('tr');
                            // Hitung ID baru dengan aman
                            const lastId = allRows.length > 0 ? parseInt(allRows[allRows.length - 1].children[0].textContent) : 0;
                            const newId = lastId + 1;

                            newRow.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-primaryText">${newId}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-primaryText">${categoryName}</td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                <button class="text-blue-500 hover:text-blue-700 transition-colors text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button class="text-red-500 hover:text-red-700 transition-colors text-sm">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </button>
                            </td>
                        `;

                            // Tambahkan ke DOM dan ke array allRows
                            if (tableBody) { // Pastikan tableBody ada
                                tableBody.appendChild(newRow);
                            }
                            allRows.push(newRow); // Tambahkan baris baru ke array data

                            currentPage = 1; // Kembali ke halaman pertama untuk melihat item baru
                            updateTable(); // Perbarui tampilan tabel dan pagination
                            hideModal(); // Sembunyikan modal
                            alert(`Category "${categoryName}" added successfully!`); // Beri notifikasi
                        } else {
                            alert('Category Name cannot be empty.');
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>