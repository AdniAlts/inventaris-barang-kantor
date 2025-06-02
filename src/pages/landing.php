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
        <a href="/inventaris-barang-kantor/admin"
          class="sidebar-link active flex items-center px-4 py-3 rounded-lg">
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
      <header class="bg-dashboardBg shadow-sm h-16 flex items-center px-8 justify-between border-b border-gray-200">
        <div class="flex items-center">
          <h2 class="text-3xl font-semibold text-blue-600 mt-9">Dashboard</h2>
        </div>
      </header>

      <div class="p-8 bg-dashboardBg min-h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="flex items-center bg-white rounded-lg p-3 mb-8 shadow-md border border-gray-200">
          <i class="fas fa-search text-blue-600 text-xl mr-3"></i>
          <input
            type="text"
            placeholder="Cari barang"
            id="search-input"
            class="flex-grow bg-transparent text-blue-600 placeholder-blue-600 text-lg border-0 focus:ring-0 focus:outline-none">
          <button class="ml-4 text-blue-600 text-2xl focus:outline-none">
            <i class="fas fa-sliders-h"></i>
          </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6">
          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span> <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>

          <div class="bg-white text-blue-600 rounded-lg shadow-md p-6 flex flex-col justify-between h-40">
            <div class="flex justify-between items-center mb-4">
              <span class="text-4xl font-bold">3</span>
              <i class="fas fa-book text-6xl opacity-20"></i>
            </div>
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold mb-1">Daftar Pinjam</h3>
              <a href="#" class="text-sm flex items-center hover:underline mx-auto">
                More info <i class="fas fa-arrow-circle-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div id="add-category-modal" class="fixed inset-0 bg-modalOverlay justify-center items-center z-50 hidden">
    <div class="bg-modalBg rounded-lg shadow-xl p-8 w-full max-w-md">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-modalText">Add Categories</h3>
        <button id="modal-close-btn" class="text-gray-400 hover:text-gray-600 text-3xl focus:outline-none">
          &times;
        </button>
      </div>
      <div class="mb-4">
        <label for="category-name" class="block text-modalText text-lg font-semibold mb-2">Name</label>
        <input type="text" id="category-name" class="w-full px-4 py-2 rounded-md bg-modalInputBg border border-modalInputBorder text-modalText focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., Laptop">
      </div>
      <div class="flex justify-end space-x-4">
        <button id="modal-cancel-btn" class="bg-modalButtonCancel text-white px-5 py-2 rounded-md hover:opacity-90 transition-colors">Cancel</button>
        <button id="modal-submit-btn" class="bg-modalButtonSubmit text-white px-5 py-2 rounded-md hover:opacity-90 transition-colors">Submit</button>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // --- Sidebar Toggling Logic ---
      const sidebar = document.getElementById('sidebar'); // Menggunakan ID 'sidebar'
      const menuToggleButton = document.getElementById('menu-toggle');

      function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
      }

      if (menuToggleButton) {
        menuToggleButton.addEventListener('click', toggleSidebar);
      }

      document.addEventListener('click', (event) => {
        // Pastikan sidebar hanya tertutup jika klik di luar sidebar DAN di luar tombol toggle
        // Dan hanya berlaku di mode mobile (layar < 768px)
        if (window.innerWidth < 768 && !sidebar.contains(event.target) && !menuToggleButton.contains(event.target)) {
          if (!sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.add('-translate-x-full');
          }
        }
      });

      function handleResize() {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('-translate-x-full');
          sidebar.classList.add('relative'); // Kembali ke posisi relatif untuk desktop
        } else {
          sidebar.classList.add('-translate-x-full'); // Sembunyikan di mobile
          sidebar.classList.remove('relative'); // Gunakan fixed untuk mobile overlay
        }
      }

      window.addEventListener('resize', handleResize);
      handleResize(); // Jalankan saat halaman pertama kali dimuat

      // --- Table Pagination & Search Logic ---
      const entriesSelect = document.getElementById('entries-select');
      const tableBody = document.getElementById('table-body');
      const allRows = Array.from(tableBody.querySelectorAll('tr'));
      const showingEntriesText = document.getElementById('showing-entries-text');
      const paginationNumbersContainer = document.getElementById('pagination-numbers');
      const prevPageButton = document.getElementById('prev-page');
      const nextPageButton = document.getElementById('next-page');
      const searchInput = document.getElementById('search-input');

      let currentPage = 1;
      let entriesPerPage = parseInt(entriesSelect.value);

      function updateTable() {
        const searchTerm = searchInput.value.toLowerCase();
        let filteredRows = allRows.filter(row => {
          const rowText = row.textContent.toLowerCase();
          return rowText.includes(searchTerm);
        });

        const totalRows = filteredRows.length;

        const startIndex = (currentPage - 1) * entriesPerPage;
        const endIndex = Math.min(startIndex + entriesPerPage, totalRows);

        allRows.forEach(row => row.style.display = 'none'); // Sembunyikan semua baris

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

        prevPageButton.classList.toggle('opacity-50', currentPage === 1 || totalPages === 0);
        prevPageButton.style.pointerEvents = (currentPage === 1 || totalPages === 0) ? 'none' : 'auto';

        nextPageButton.classList.toggle('opacity-50', currentPage === totalPages || totalPages === 0);
        nextPageButton.style.pointerEvents = (currentPage === totalPages || totalPages === 0) ? 'none' : 'auto';
      }

      entriesSelect.addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        updateTable();
      });

      searchInput.addEventListener('input', function() {
        currentPage = 1;
        updateTable();
      });

      prevPageButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage > 1) {
          currentPage--;
          updateTable();
        }
      });

      nextPageButton.addEventListener('click', function(e) {
        e.preventDefault();
        const currentTotalRows = allRows.filter(row => {
          const rowText = row.textContent.toLowerCase();
          return rowText.includes(searchInput.value.toLowerCase());
        }).length;

        const totalPages = Math.ceil(currentTotalRows / entriesPerPage);
        if (currentPage < totalPages) {
          currentPage++;
          updateTable();
        }
      });

      updateTable(); // Panggil updateTable saat halaman pertama kali dimuat

      // --- Export Excel Logic ---
      const exportExcelBtn = document.getElementById('export-excel-btn');
      const categoryTable = document.getElementById('category-table');

      if (exportExcelBtn && categoryTable) {
        exportExcelBtn.addEventListener('click', function() {
          const tableClone = categoryTable.cloneNode(true);

          // Hapus kolom 'Action' dari kloning tabel
          const headerRow = tableClone.querySelector('thead tr');
          const actionHeaderIndex = Array.from(headerRow.children).findIndex(th => th.textContent.trim().toLowerCase().includes('action'));

          if (actionHeaderIndex !== -1) {
            headerRow.children[actionHeaderIndex].remove(); // Hapus header kolom 'Action'

            tableClone.querySelectorAll('tbody tr').forEach(row => {
              if (row.children[actionHeaderIndex]) { // Pastikan sel ada sebelum menghapus
                row.children[actionHeaderIndex].remove(); // Hapus sel kolom 'Action' di setiap baris
              }
            });
          }

          const ws = XLSX.utils.table_to_sheet(tableClone);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, ws, "Categories");
          XLSX.writeFile(wb, "Categories.xlsx");
        });
      }

      // --- Add Category Modal Logic ---
      const addCategoryBtn = document.getElementById('add-category-btn');
      const addCategoryModal = document.getElementById('add-category-modal');
      const modalCloseBtn = document.getElementById('modal-close-btn');
      const modalCancelBtn = document.getElementById('modal-cancel-btn');
      const modalSubmitBtn = document.getElementById('modal-submit-btn');
      const categoryNameInput = document.getElementById('category-name');

      function showModal() {
        addCategoryModal.classList.remove('hidden');
        addCategoryModal.classList.add('flex'); // Pastikan modal menjadi flex saat ditampilkan
        document.body.classList.add('overflow-hidden');
      }

      function hideModal() {
        addCategoryModal.classList.add('hidden');
        addCategoryModal.classList.remove('flex'); // Hapus flex saat disembunyikan
        document.body.classList.remove('overflow-hidden');
        categoryNameInput.value = '';
      }

      if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', showModal);
      }
      if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', hideModal);
      }
      if (modalCancelBtn) {
        modalCancelBtn.addEventListener('click', hideModal);
      }

      addCategoryModal.addEventListener('click', function(event) {
        if (event.target === addCategoryModal) {
          hideModal();
        }
      });

      if (modalSubmitBtn) {
        modalSubmitBtn.addEventListener('click', function() {
          const categoryName = categoryNameInput.value.trim();
          if (categoryName) {
            // Logika untuk menambahkan kategori baru ke tabel secara dinamis
            const newRow = document.createElement('tr');
            const newId = allRows.length > 0 ? parseInt(allRows[allRows.length - 1].children[0].textContent) + 1 : 1;
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
            tableBody.appendChild(newRow);
            allRows.push(newRow); // Tambahkan baris baru ke allRows

            currentPage = 1; // Kembali ke halaman pertama untuk melihat item baru
            updateTable(); // Perbarui tabel dan pagination
            hideModal();
            alert(`Category "${categoryName}" added successfully!`);
          } else {
            alert('Category Name cannot be empty.');
          }
        });
      }
    });
  </script>
</body>

</html>