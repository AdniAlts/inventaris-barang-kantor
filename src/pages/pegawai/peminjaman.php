<?php
require_once __DIR__ . "/../../config/helper.php";
require_once __DIR__ . "/../../modules/search.php";
require_once __DIR__ . "/../../config/db.php";

$db = new db();
$connDB = $db->conn;

register_shutdown_function(function() use ($db) {
  if ($db && $db->conn)
    $db->close();
});

$allItems = Search::getAllItems($connDB);
$categories = GetNames::category($connDB);
$allTypes = Search::getAllTypes($connDB);

$filters = [];
foreach ($categories as $category)
  $filters[strtolower(str_replace(' ', '_', $category['nama']))] = ['label' => $category['nama'],
                                                                                                                  'type' => 'category',
                                                                                                                  'value' => $category['nama']];

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
  <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-500"
    aria-label="Sidebar">
    <div class="h-full grid content-between px-3 py-4 overflow-y-auto overflow-x-hidden bg-gray-50">
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
            <a href="<?= Helper::basePath(); ?>user" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          </li>
          <li>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="3 3 18 18">
                <path
                  d="M20 14h-2.722L11 20.278a5.511 5.511 0 0 1-.9.722H20a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1ZM9 3H4a1 1 0 0 0-1 1v13.5a3.5 3.5 0 1 0 7 0V4a1 1 0 0 0-1-1ZM6.5 18.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM19.132 7.9 15.6 4.368a1 1 0 0 0-1.414 0L12 6.55v9.9l7.132-7.132a1 1 0 0 0 0-1.418Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Peminjaman</span>
            </a>
          </li>
          <li>
            <a href="<?= Helper::basePath(); ?>pengembalian" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="2 2 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Pengembalian</span>
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
              <div class="font-semibold leading-none text-gray-950 mb-1">Pegawai</div>
              <div class="text-sm text-gray-700">Klik untuk ganti mode</div>
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
          <a href="<?= Helper::basePath(); ?>admin" class="flex items-center hover:bg-gray-100 my-3 px-4 rounded">
            <svg class="shrink-0 w-8 h-8 mr-3 text-gray-500 transition duration-75" aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
              <path fill-rule="evenodd"
                d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                clip-rule="evenodd" />
            </svg>
            <div class="text-left">
              <div class="font-semibold leading text-gray-950 mb-0.5">
                Admin</div>
              <div class="text-sm text-gray-700">[username dari db]</div>
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

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <div>
      <p>TOLONG BACA AKU!!!</p>
      <p>Ini bisa jadi saran untuk membantu pembuatan peminjaman:</p>
      <ol style="list-style: decimal;">
        <li>TOLONG BANGET AMBIL DATANYA DARI DATABASE AJA, JANGAN BIKIN DUMMY SENDIRI!!!<br>
          Kalau memang gatau caranya, ada modul pembelajaran koneksi database dari bu Dian sendiri, atau ada yang namanya 'searching di Google dan YouTube'.<br>
          Tolong lah, pake AI nya di minimalisir, dan kalau memang mendesak banget. Mumpung waktu libur kita masih banyak, jadi masih bisa meluangkan waktu untuk BELAJAR.<br>
          Sama mohon TANGGUNG JAWABnya terhadap bagian tugasnya sendiri, jangan mentang-mentang lagi ada kegiatan jadinya ngga bisa meluangkan waktu untuk ngerjain.</li>
        <li>Boleh pake referensi halaman yang mirip sebelumnya, linknya bisa buka <a href="<?= Helper::basePath(); ?>src/pages/old/barang_user.php" style="text-decoration: underline;">di sini untuk tabel barang</a> dan <a href="<?= Helper::basePath(); ?>src/pages/old/peminjmaan.php" style="text-decoration: underline;">di sini untuk peminjaman</a>.</li>
        <li>Kolom tabel yang ditampilkan bisa berupa kode barang, gambar barang (bisa full screen mungkin), status barang, jenis barang, kondisi barang, sama kategori barang</li>
        <li>Boleh tambahan menampilkan berapa row. Misalnya kalo ingin menampilkan 5 row, nanti tampil tabel maksimal 5 row, terus selanjutnya bisa ada pilihan ganti halaman.</li>
        <li>Sorting tabel ascending atau descending berdasarkan kolom tabel yang dipilih.</li>
        <li>Kalau mau pake poin 5 dan 6 mending pake JavaScript aja. Ambil data dari database nya tetep pake PHP.</li>
        <li>Untuk peminjaman bisa dari tabel barang yang diakses dengan tombol "Pinjam" yang nanti akan membuka modal untuk pilihan barangnya, nanti bisa juga tambahkan jumlah barang.</li>
        <li>Daftar barang yang akan dipinjam aku saranin di simpan secara local storage dulu untuk temporary caching, nanti kalau user sudah pasti dengan pinjamannya baru di kirim ke database.</li>
        <li>Kalau mengikuti poin 8, barang yang akan dipinjam bisa juga di hapus dan juga di ganti jumlah barang nya.</li>
        <li>Daftar barang yang telah dipinjam juga bisa memiliki tampilan kotak-kotak yang dikelompkkan sesuai id peminjamannya, yang di dalamnya terdapat list barang yang dipinjam.</li>
      </ol>
      <br>
      <p>Kalau sudah selesai mengimplementasi bagian halaman ini. Bisa menghapus elemen div yang isinya teks ini.</p>
      <p>Dan kalau ada pertanyaan, jangan sungkan tanya di grup. Teman-teman kalian PASTI dan HARUSNYA bisa bantu.</p>
    </div>
    <div class="my-5">
      <h1 class="text-3xl font-bold text-black-400">
        Peminjaman
      </h1>
    </div>
    <!-- START BAGIAN TAMBAH BARANG -->
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="text">
          <h3 class="text-xl font-semibold text-black-700 m-3 pt-4">Tambah Barang untuk Dipinjam</h3>
        </div>
        <div class="search-input">
          <form class="max-w-2lg mx-auto px-3">
            <div class="flex">
              <button id="dropdown-button" data-dropdown-toggle="dropdown" class="shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 rounded-s-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-200 dark:hover:bg-gray-200 dark:focus:ring-gray-200 dark:text-gray-700 dark:border-gray-300" type="button">All categories <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg></button>
              <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-100">
                <!-- Ini ntar diisi kategori (diambil dari table kategori yaa) -->
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-700" aria-labelledby="dropdown-button">
                  <?php
                  foreach ($filters as $key => $data) {
                    echo "<li>
                            <button type='button' id='category-selector' class='inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-300 dark:hover:text-gray-900' data-category-key='{$key}' data-category-value='".htmlspecialchars($data['value'], ENT_QUOTES)."'>" . htmlspecialchars($data['label']) . "</button>
                          </li>";
                  }
                  ?>
                </ul>
              </div>
              <div class="relative w-full">
                <input type="search" id="search-dropdown" class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-e-lg border-s-gray-50 border-s-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-200 dark:border-s-gray-300  dark:border-gray-300 dark:placeholder-gray-400 dark:text-gray-700 dark:focus:border-blue-500" placeholder="Search Mockups, Logos, Design Templates..." required />
                <button type="submit" class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                  <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                  </svg>
                  <span class="sr-only">Search</span>
                </button>
              </div>
            </div>
          </form>
        </div>
        <div id="types-render" class="grid grid-cols-4 gap-2 my-5 px-2">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5">
            <img class="w-full h-24 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
            <div class="p-2">
              <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Meja</p>
              <!-- Modal toggle -->
              <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Tambah
              </button>
              <!-- Main modal -->
              <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                      <h3 class="text-lg font-semibold text-gray-900">
                        Barang
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                      <div class="mb-4">
                        <div class="mb-2">
                          <img class="w-full h-48 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
                        </div>
                        <div class="col-span-2">
                          <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label>
                          <input type="number" name="jumlah" id="jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Type product name" required="">
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
            </div>
          </div>
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5">
            <img class="w-full h-24 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
            <div class="p-2">
              <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Meja</p>
              <!-- Modal toggle -->
              <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Tambah
              </button>
              <!-- Main modal -->
              <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                      <h3 class="text-lg font-semibold text-gray-900">
                        Barang
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                      <div class="mb-4">
                        <div class="mb-2">
                          <img class="w-full h-48 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
                        </div>
                        <div class="col-span-2">
                          <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label>
                          <input type="number" name="jumlah" id="jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Type product name" required="">
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
            </div>
          </div>
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5">
            <img class="w-full h-24 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
            <div class="p-2">
              <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Meja</p>
              <!-- Modal toggle -->
              <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Tambah
              </button>
              <!-- Main modal -->
              <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                      <h3 class="text-lg font-semibold text-gray-900">
                        Barang
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                      <div class="mb-4">
                        <div class="mb-2">
                          <img class="w-full h-48 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
                        </div>
                        <div class="col-span-2">
                          <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label>
                          <input type="number" name="jumlah" id="jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Type product name" required="">
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
            </div>
          </div>
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5">
            <img class="w-full h-24 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
            <div class="p-2">
              <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Meja</p>
              <!-- Modal toggle -->
              <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Tambah
              </button>
              <!-- Main modal -->
              <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                  <!-- Modal content -->
                  <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                      <h3 class="text-lg font-semibold text-gray-900">
                        Barang
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                      </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5">
                      <div class="mb-4">
                        <div class="mb-2">
                          <img class="w-full h-48 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image" />
                        </div>
                        <div class="col-span-2">
                          <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label>
                          <input type="number" name="jumlah" id="jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Type product name" required="">
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
            </div>
          </div>
        </div>
        <div class="flex items-center justify-center">
          <button type="submit" class="text-sm font-medium mb-4">
            Lihat lebih banyak
          </button>
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
              <button type="submit"
                class="ms-auto mt-3 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Submit Peminjaman
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- END BAGIAN DESKRIPSI DAN SUMBIT -->
    </div>
  </main>

  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebar');
    const userButton = document.getElementById('dropdownUserNameButton');
    const userDropdown = document.getElementById('dropdownUserName');
    document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
      sidebar.classList.toggle('sidebar-collapsed');
    });
    sidebar.addEventListener('mouseenter', function() {
      if (sidebar.classList.contains('sidebar-collapsed') && userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });
    sidebar.addEventListener('mouseleave', function() {
      if (sidebar.classList.contains('sidebar-hover') && userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });
    userButton.addEventListener('focusout', function() {
      if (!userDropdown.classList.contains('hidden')) {
        sidebar.classList.toggle('sidebar-hover');
      }
    });

    // Select Category
    const allTypes = <?= json_encode($allTypes); ?>;
    const typeContainer = document.getElementById('types-render');
    const searchInput = document.getElementById('search-dropdown');
    const categoryFilters = document.querySelectorAll('#category-selector');
    
    let activeCategory = '';

    function renderTypes(typesToRender) {
      typeContainer.innerHTML = '';
      if (typesToRender.length === 0) {
        typeContainer.innerHTML = '<p>Tidak ada tipe barang sesuai kriteria.</p>';
        return;
      }

      typesToRender.forEach(item => {
        const typeDiv = document.createElement('div');
        typeDiv.className = 'max-w-sm rounded overflow-hidden shadow-lg bg-white mt-1 mx-1.5';
        typeDiv.innerHTML =
          '<img class="w-full h-24 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image"><div class="p-2"><p class="mb-3 font-normal text-gray-700 dark:text-gray-400">'+ (item.nama || 'N/A') +'</p><button data-modal-target="crud-modal-'+ (item.id_jenis) +'" data-modal-toggle="crud-modal-'+ (item.id_jenis) +'" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">Tambah</button><div id="crud-modal-'+ (item.id_jenis) +'" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full hidden" aria-hidden="true"><div class="relative p-4 w-full max-w-lg max-h-full"><div class="relative bg-white rounded-lg shadow-sm"><div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Barang</h3><button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal-'+ (item.id_jenis) +'"><svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"></path></svg><span class="sr-only">Close modal</span></button></div><form class="p-4 md:p-5"><div class="mb-4"><div class="mb-2"><img class="w-full h-48 object-cover" src="https://www.shutterstock.com/image-vector/fill-image-preview-icon-simple-260nw-2338969281.jpg" alt="Image"><p>'+ (item.stok || 'N/A') +'</p></div><div class="col-span-2"><label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah</label><input type="number" name="jumlah" id="jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="Type product name" required=""></div></div><button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"><svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>Tambah</button></form></div></div></div></div>';
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
    
    searchInput.addEventListener('input', applyCategories);

    categoryFilters.forEach(button => {
      button.addEventListener('click', () => {
        const categoryValue = button.dataset.categoryValue;

        if (activeCategory === categoryValue) {
          activeCategory = '';
        } else {
          activeCategory = categoryValue;
        }
        applyCategories();
      })
    });

    applyCategories();
  </script>
</body>

</html>