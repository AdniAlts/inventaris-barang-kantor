<?php
require_once __DIR__ . '/../../config/helper.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <style>
  </style>
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>
  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <div>
      <p>TOLONG BACA AKU!!!</p>
      <p>Ini bisa jadi saran untuk membantu pembuatan dashboard admin:</p>
      <ol style="list-style: decimal;">
        <li>TOLONG BANGET AMBIL DATANYA DARI DATABASE AJA, JANGAN BIKIN DUMMY SENDIRI!!!<br>
          Kalau memang gatau caranya, ada modul pembelajaran koneksi database dari bu Dian sendiri, atau ada yang namanya 'searching di Google dan YouTube'.<br>
          Tolong lah, pake AI nya di minimalisir, dan kalau memang mendesak banget. Mumpung waktu libur kita masih banyak, jadi masih bisa meluangkan waktu untuk BELAJAR.<br>
          Sama mohon TANGGUNG JAWABnya terhadap bagian tugasnya sendiri, jangan mentang-mentang lagi ada kegiatan jadinya ngga bisa meluangkan waktu untuk ngerjain.</li>
        <li>Karena ini mode admin, boleh ditambahkan list peminjaman pegawai yang sedang ada, tapi tidak perlu create, update, sama delete, cuma perlu read DARI DATABASE aja.</li>
        <li>Tampilan 5 kategori pertama yang ada DI DATABASE, boleh tambahin tombol untuk menuju ke halaman kategori untuk selengkapnya.</li>
        <li>Tampilan 5 jenis pertama yang ada DI DATABASE, boleh tambahin tombol untuk menuju ke halaman jenis untuk selengkapnya.</li>
        <li>Tampilan 5 kondisi pertama yang ada DI DATABASE, boleh tambahin tombol untuk menuju ke halaman kondisi untuk selengkapnya.</li>
        <li>Tampilan 5 barang pertama yang ada DI DATABASE, boleh tambahin tombol untuk menuju ke halaman barang untuk selengkapnya.</li>
      </ol>
      <br>
      <p>Kalau sudah selesai mengimplementasi bagian halaman ini. Bisa menghapus elemen div yang isinya teks ini.</p>
      <p>Dan kalau ada pertanyaan, jangan sungkan tanya di grup. Teman-teman kalian PASTI dan HARUSNYA bisa bantu.</p>
    </div>

    <div class="my-5">
      <h1 class="text-3xl font-bold text-black-400">Dashboard</h1>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
          <tr>
            <th scope="col" class="px-6 py-3">
              Kategori Barang
            </th>
            <th scope="col" class="px-6 py-3">
              Jenis Barang
            </th>
            <th scope="col" class="px-6 py-3">
              Kondisi Barang
            </th>
            <th scope="col" class="px-6 py-3">
              Barang tersedia
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
              Barang 1
            </th>
            <td class="px-6 py-4">
              Jenis Barang
            </td>
            <td class="px-6 py-4">
              Kondisi Barang
            </td>
            <td class="px-6 py-4">
              2
            </td>
          </tr>
          <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
              Barang 2
            </th>
            <td class="px-6 py-4">
              Jenis Barang
            </td>
            <td class="px-6 py-4">
              Kondisi Barang
            </td>
            <td class="px-6 py-4">
              3
            </td>
          </tr>
          <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
              Barang 3
            </th>
            <td class="px-6 py-4">
              Jenis Barang
            </td>
            <td class="px-6 py-4">
              Kondisi Barang
            </td>
            <td class="px-6 py-4">
              1
            </td>
          </tr>
          <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
              Barang 4
            </th>
            <td class="px-6 py-4">
              Jenis Barang
            </td>
            <td class="px-6 py-4">
              Kondisi Barang
            </td>
            <td class="px-6 py-4">
              5
            </td>
          </tr>
          <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
              Barang 5
            </th>
            <td class="px-6 py-4">
              Jenis Barang
            </td>
            <td class="px-6 py-4">
              Kondisi Barang
            </td>
            <td class="px-6 py-4">
              4
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- nyoba -->

    <!-- Tampilan 5 barang pertama -->

    <!-- Tampilan 5 barang pertama -->

    <!-- Tampilan 5 barang pertama -->

    <!-- Tampilan 5 barang pertama -->

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
  </script>
</body>

</html>