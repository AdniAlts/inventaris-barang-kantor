<?php

use Dom\HTMLElement;
require_once __DIR__ . "/../../config/helper.php";
require_once __DIR__ . "/../../modules/search.php";
require_once __DIR__ . "/../../config/db.php";

$db = new db();
$connDB = $db->conn;

register_shutdown_function(function () use ($db) {
  if ($db && $db->conn)
    $db->close();
});

$allItems = Search::getAllItems($connDB);
$categories = GetNames::category($connDB);
$allTypes = Search::getAllTypes($connDB);

$filters = [];
foreach ($categories as $category)
  $filters[strtolower(str_replace(' ', '_', $category['nama']))] = [
    'label' => $category['nama'],
    'type' => 'category',
    'value' => $category['nama']
  ];

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peminjaman - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <div class="my-5">
      <h1 class="text-3xl font-bold text-black-400">
        Peminjaman
      </h1>
    </div>
    <!-- START BAGIAN TAMBAH BARANG -->
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
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

      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="text">
          <h3 class="text-xl font-semibold text-black-700 m-3 pt-4">Tambah Barang untuk Dipinjam</h3>
        </div>
        <div class="search-input">
          <form class="max-w-2lg mx-auto px-3">
            <div class="flex">
              <button id="category-dropdown-button" class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-200 dark:hover:bg-gray-200 dark:focus:ring-gray-200 dark:text-gray-700 dark:border-gray-300" type="button">
                <span id="category-button-text">All categories</span>
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
              </button>
              <div id="category-dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-100">
                <ul id="category-list" class="py-2 text-sm text-gray-700 dark:text-gray-700" aria-labelledby="category-dropdown-button">

                </ul>
              </div>
              <div class="relative w-full">
                <input type="search" id="search-dropdown" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-200 dark:border-s-gray-300  dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray-700 dark:focus:border-blue-500" placeholder="Search Barang...." />
              </div>
            </div>
          </form>
        </div>
        <div id="types-render" class="grid grid-cols-4 gap-2 my-5 px-2">
        </div>
      </div>
      <!-- END BAGIAN AKHIR BARANG -->
      <!-- START BAGIAN TABLE BARANG DIPILIH -->
      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="text">
          <h3 class="text-xl font-semibold text-black-700 m-3 pt-4">Daftar Barang yang Akan Dipinjam</h3>
        </div>
        <div class="table w-full p-3">
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-blue-100">
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
                        <td colspan='2' class='px-6 py-4 text-center text-gray-500 '>Belum ada barang yang dipilih</td>
                      </tr>";
              }
              ?>
            </tbody>
            <tfoot class="text-xs text-gray-700 uppercase bg-blue-100">
              <tr>
                <th scope="col" class="px-6 py-3">Total Jumlah</th>
                <th scope="col" class="px-6 py-3"><?php echo $total; ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- Reset Button -->
        <div class="ms-3 py-3">
          <?php
          if (isset($_COOKIE['peminjaman'])) {
            echo '<form action="" method="get">
                    <input type="submit" name="reset" value="Reset" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                  </form>';
          }
          ?>
        </div>
      </div>
      <!-- END BAGIAN TABLE BARANG DIPILIH -->
      <!-- START BAGIAN DESKRIPSI DAN SUMBIT -->
      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="desk p-3">
          <form action="/inventaris-barang-kantor/loan" method="POST" class="w-full">
            <div class="mt-4">
              <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi:</label>
              <textarea type="number" name="deskripsi" id="deskripsi" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
            <div class="w-full">
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
              <button type="submit" class="ms-auto mt-3 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">Submit Peminjaman</button>
            </div>
          </form>
        </div>
      </div>
      <!-- END BAGIAN DESKRIPSI DAN SUMBIT -->
    </div>
  </main>

  <!-- Main modal -->
  <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-sm">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
          <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Barang</h3>
          <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="crud-modal">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <form id="modal-form" method="GET" action="" class="p-4 md:p-5">
          <div class="mb-4">
            <div class="mb-2">
              <div class="flex w-full h-48 justify-center overflow-y-hidden">
                <img id="modal-image" class="h-[inherit]" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image">
              </div>
              <p class="mt-2">Stok tersedia: <span id="modal-stok" class="font-medium">N/A</span></p>
            </div>
            <div class="col-span-2">
              <label for="modal-jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label>
              <input type="number" name="jumlah" id="modal-jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Masukkan jumlah" required min="1">
              <input type="hidden" name="barang" id="modal-form-barang-name">
            </div>
          </div>
          <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            Tambah
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="<?= Helper::basePath(); ?>src/flowbite.min.js"></script>
  <script>
    // Dropdown category event handler
    // options with default values
    const options = {
      placement: 'bottom',
      triggerType: 'click',
      offsetSkidding: 0,
      offsetDistance: 10,
      delay: 300,
      ignoreClickOutsideClass: false,
      onHide: () => {
        console.log('dropdown has been hidden');
      },
      onShow: () => {
        console.log('dropdown has been shown');
      },
      onToggle: () => {
        console.log('dropdown has been toggled');
      },
    };

    // instance options object
    const instanceOptions = {
      id: 'category-dropdown',
      override: true
    };
    // Select Category
    const allTypes = <?= json_encode($allTypes); ?>;
    const filtersData = <?= json_encode(array_values($filters)); ?>;
    const typeContainer = document.getElementById('types-render');
    const searchInput = document.getElementById('search-dropdown');

    // Dropdown elements
    const categoryDropdownButton = document.getElementById('category-dropdown-button');
    const categoryButtonText = document.getElementById('category-button-text');
    const categoryDropdownEl = document.getElementById('category-dropdown');
    const categoryList = document.getElementById('category-list');

    // Programmatically init Flowbite Dropdown
    const categoryDropdown = new Dropdown(categoryDropdownEl, categoryDropdownButton, options, instanceOptions);

    let activeCategory = '';

    function renderTypes(typesToRender) {
      typeContainer.innerHTML = '';
      if (typesToRender.length === 0) {
        typeContainer.innerHTML = '<p>Tidak ada tipe barang sesuai kriteria.</p>';
        return;
      }

      typesToRender.forEach(item => {
        let gambar = (item.gambar_url != null) ? "<?php echo Helper::basePath() . "src/"; ?>" + item.gambar_url : "https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg";
        const typeDiv = document.createElement('div');
        typeDiv.className = 'max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5';
        typeDiv.innerHTML = `
          <img class="w-full h-24 object-cover" src="${gambar}" alt="${item.nama}">
          <div class="p-2">
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">${item.nama || 'N/A'}</p>
            <button 
                data-id-jenis="${item.id_jenis}"
                data-nama="${item.nama}"
                data-stok="${item.stok_tersedia}"
                data-gambar="${gambar}"
                class="open-modal-btn block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" 
                type="button">
              Tambah
            </button>
          </div>`;
        typeContainer.appendChild(typeDiv);
      })
    }

    function applyCategories() {
      let filteredTypes = allTypes;
      const searchTerm = searchInput.value.toLowerCase().trim();

      if (searchTerm) {
        filteredTypes = filteredTypes.filter(item => {
          return (item.nama && item.nama.toLowerCase().includes(searchTerm)) ||
            (item.nama_kategori && item.nama_kategori.toLowerCase().includes(searchTerm));
        });
      }

      if (activeCategory) {
        filteredTypes = filteredTypes.filter(item => item.nama_kategori === activeCategory);
      }

      renderTypes(filteredTypes);
    }

    function renderCategoryList() {
      categoryList.innerHTML = ''; // Clear the list

      // Add a "Clear" option if a category is selected
      if (activeCategory) {
        const clearItem = document.createElement('li');
        clearItem.innerHTML = `<button type="button" id="clear-category-button" class="inline-flex w-full px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-300 dark:hover:text-gray-900">Clear selection</button>`;
        categoryList.appendChild(clearItem);
      }

      // Add all categories from the data, skipping the active one
      filtersData.forEach(data => {
        if (data.value === activeCategory) {
          return;
        }
        const listItem = document.createElement('li');
        listItem.innerHTML = `<button type="button" class="category-selector inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-300 dark:hover:text-gray-900" data-category-value="${data.value}">${data.label}</button>`;
        categoryList.appendChild(listItem);
      });
    }

    // Event listener for the category list (using event delegation)
    categoryList.addEventListener('click', (event) => {
      const target = event.target;

      // Handle clearing the category
      if (target && target.id === 'clear-category-button') {
        activeCategory = '';
        categoryButtonText.textContent = 'All categories';
        applyCategories();
        renderCategoryList(); // Re-render the list to remove the "Clear" button
        categoryDropdown.hide();
        return; // Stop further execution
      }

      const categoryButton = target.closest('.category-selector');
      if (categoryButton) {
        const categoryValue = categoryButton.dataset.categoryValue;
        const categoryLabel = categoryButton.textContent;

        activeCategory = categoryValue;
        categoryButtonText.textContent = categoryLabel; // Update button text
        applyCategories();
        renderCategoryList(); // Re-render list to add "Clear" button
        categoryDropdown.hide();
      }
    });

    searchInput.addEventListener('input', applyCategories);

    // New logic for single modal
    const modalEl = document.getElementById('crud-modal');
    const modal = new Modal(modalEl);

    const modalTitle = document.getElementById('modal-title');
    const modalImage = document.getElementById('modal-image');
    const modalStok = document.getElementById('modal-stok');
    const modalFormBarangName = document.getElementById('modal-form-barang-name');
    const modalJumlahInput = document.getElementById('modal-jumlah');

    typeContainer.addEventListener('click', (event) => {
      const button = event.target.closest('.open-modal-btn');
      if (!button) {
        return;
      }

      const itemName = button.dataset.nama;
      const itemStok = button.dataset.stok;
      const itemGambar = button.dataset.gambar;

      modalTitle.textContent = `Tambah ${itemName}`;
      modalImage.src = itemGambar;
      modalStok.textContent = itemStok;
      modalFormBarangName.value = itemName;
      modalJumlahInput.max = itemStok;

      modal.show();
    });

    // Initial render
    applyCategories();
    renderCategoryList();
  </script>
</body>

</html>