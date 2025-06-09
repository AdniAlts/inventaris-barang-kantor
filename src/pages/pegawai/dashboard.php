<?php
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../modules/get_peminjaman.php";

$db = new db();
$connDB = $db->conn;

$allPeminjaman = GetFromDB::peminjaman($connDB);

// dari variabel $allPeminjaman itu nanti bisa dipake buat tampilin keterangan peminjaman e sama detail barang yang dipinjam
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>
  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <div>
      <p>TOLONG BACA AKU!!!</p>
      <p>Ini bisa jadi saran untuk membantu pembuatan dashboard pegawai:</p>
      <ol style="list-style: decimal;">
        <li>TOLONG BANGET AMBIL DATANYA DARI DATABASE AJA, JANGAN BIKIN DUMMY SENDIRI!!!<br>
          Kalau memang gatau caranya, ada modul pembelajaran koneksi database dari bu Dian sendiri, atau ada yang namanya 'searching di Google dan YouTube'.<br>
          Tolong lah, pake AI nya di minimalisir, dan kalau memang mendesak banget. Mumpung waktu libur kita masih banyak, jadi masih bisa meluangkan waktu untuk BELAJAR.<br>
          Sama mohon TANGGUNG JAWABnya terhadap bagian tugasnya sendiri, jangan mentang-mentang lagi ada kegiatan jadinya ngga bisa meluangkan waktu untuk ngerjain.</li>
        <li>Karena ini mode pegawai, boleh ditambahkan list 4 atau 5 peminjaman terakhir pegawai yang sedang ada, bisa langsung mengembalikan pinjaman dengan tombol.<br>
          List nya berupa kotak yang isinya sedikit preview jenis barang dan jumlah yang dipinjam. Untuk detail selengkapnya bisa diarahkan ke halaman peminjaman.<br>
          Susunan kotaknya horizontal bersampingan antara satu dengan yang lain.<br>
          Detail kotaknya bisa ada id peminjaman, list jenis barang yang dipinjam sama jumlah nya, dan tombol untuk melihat detail peminjaman yang mengarah ke halaman peminjaman dengan id peminjaman itu selected.<br>
          Untuk sumber-sumber data peminjamannya bisa ambil DARI DATABASE.</li>
        <li>Kalau mau melakukan pinjaman bisa bikin tombol yang ngarah ke halaman peminjaman barang.</li>
        <li>Bisa tambahin list jumlah jenis barang yang lagi "tersedia" dan "tidak rusak" agar bisa dipinjam. Informasi nya di ambil DARI DATABASE.</li>
      </ol>
      <br>
      <p>Kalau sudah selesai mengimplementasi bagian halaman ini. Bisa menghapus elemen div yang isinya teks ini.</p>
      <p>Dan kalau ada pertanyaan, jangan sungkan tanya di grup. Teman-teman kalian PASTI dan HARUSNYA bisa bantu.</p>
    </div>

    <div class="my-5">
      <h1 class="text-3xl font-bold text-black-400">Barang Terakhir Dipinjam</h1>
    </div>

    <?php if (empty($peminjaman)): ?>
      <p>Tidak ada data peminjaman yang ditemukan.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($peminjaman as $peminjaman_item): ?>
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjaman: <?php echo htmlspecialchars($peminjaman_item['id_peminjaman']); ?></h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">
              **Deskripsi**: <?php echo htmlspecialchars($peminjaman_item['deskripsi']); ?>
            </p>
            <p class="mb font-normal text-gray-700 dark:text-black">
              **Tanggal Peminjaman**: <?php echo htmlspecialchars($peminjaman_item['tgl_peminjaman']); ?>
            </p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">
              **Tanggal Kembali**: <?php echo $peminjaman_item['tgl_balik'] ? htmlspecialchars($peminjaman_item['tgl_balik']) : 'Belum Dikembalikan'; ?>
            </p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">
              **Jumlah Barang Yang Dipinjam**: <?php echo htmlspecialchars($peminjaman_item['total_pinjam']); ?>
            </p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">
              **Status**: <?php echo htmlspecialchars(ucfirst($peminjaman_item['status'])); ?>
            </p>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black mt-4">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">Nama Barang</th>
                      <th scope="col" class="px-3 py-3">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($peminjaman_item['barang'])): ?>
                      <?php foreach ($peminjaman_item['barang'] as $barang_item): ?>
                        <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                          <td scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                            <?php echo htmlspecialchars($barang_item['nama_barang']); ?>
                          </td>
                          <td class="px-3 py-4">
                            <?php echo htmlspecialchars($barang_item['jumlah']); ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr class="bg-white dark:bg-gray-50">
                        <td colspan="2" class="px-3 py-4 text-center text-gray-500">Tidak ada barang dalam peminjaman ini.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (empty($peminjaman)): ?>
      <p>Tidak ada data peminjaman yang ditemukan.</p>
    <?php else: ?>
      <?php foreach ($peminjaman as $data_peminjaman => $Q): ?>
        <div class="grid grid-cols-5 gap-4">
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjam <?php echo htmlspecialchars($data_peminjaman['id_peminjaman']); ?></h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">Deskirpsi</p>
            <p class="mb font-normal text-gray-700 dark:text-black">Tanggal Peminjaman</p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">Jumlah Barang Yang Dipinjam</p>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black">
              <!-- Tabel dalam -->
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">
                        Kategori Barang
                      </th>
                      <th scope="col" class="px-3 py-3">
                        Jenis Barang
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 1
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 2
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 3
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 4
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 5
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Akhir Tabel -->
            </div>
          </div>
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjam</h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">Deskirpsi</p>
            <p class="mb font-normal text-gray-700 dark:text-black">Tanggal Peminjaman</p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">Jumlah Barang Yang Dipinjam</p>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black">
              <!-- Tabel dalam -->
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">
                        Kategori Barang
                      </th>
                      <th scope="col" class="px-3 py-3">
                        Jenis Barang
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 1
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 2
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 3
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 4
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 5
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Akhir Tabel -->
            </div>
          </div>
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjam</h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">Deskirpsi</p>
            <p class="mb font-normal text-gray-700 dark:text-black">Tanggal Peminjaman</p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">Jumlah Barang Yang Dipinjam</p>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black">
              <!-- Tabel dalam -->
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">
                        Kategori Barang
                      </th>
                      <th scope="col" class="px-3 py-3">
                        Jenis Barang
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 1
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 2
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 3
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 4
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 5
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Akhir Tabel -->
            </div>
          </div>
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjam</h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">Deskirpsi</p>
            <p class="mb font-normal text-gray-700 dark:text-black">Tanggal Peminjaman</p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">Jumlah Barang Yang Dipinjam</p>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black">
              <!-- Tabel dalam -->
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">
                        Kategori Barang
                      </th>
                      <th scope="col" class="px-3 py-3">
                        Jenis Barang
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 1
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 2
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 3
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 4
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 5
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Akhir Tabel -->
            </div>
          </div>
          <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-50">
            <a>
              <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">ID Peminjam</h5>
            </a>
            <p class="mb font-normal text-gray-700 dark:text-black">Deskirpsi</p>
            <p class="mb font-normal text-gray-700 dark:text-black">Tanggal Peminjaman</p>
            <p class="mb-3 font-normal text-gray-700 dark:text-black">Jumlah Barang Yang Dipinjam</p>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-150 dark:border-black">
              <!-- Tabel dalam -->
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900 dark:text-black">
                  <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-50 text-black">
                    <tr>
                      <th scope="col" class="px-3 py-3">
                        Kategori Barang
                      </th>
                      <th scope="col" class="px-3 py-3">
                        Jenis Barang
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 1
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 2
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 3
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 4
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-50 hover:bg-gray-100">
                      <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                        Barang 5
                      </th>
                      <td class="px-3 py-4">
                        Jenis Barang
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Akhir Tabel -->
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
      <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="flex items-center justify-center h-24 rounded bg-gray-50">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center h-24 rounded bg-gray-50">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center h-24 rounded bg-gray-50">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
      </div>
      <div class="flex items-center justify-center h-48 mb-4 rounded bg-gray-50">
        <p class="text-2xl text-gray-400">
          <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 1v16M1 9h16" />
          </svg>
        </p>
      </div>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
      </div>
      <div class="flex items-center justify-center h-48 mb-4 rounded bg-gray-50">
        <p class="text-2xl text-gray-400">
          <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 18 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 1v16M1 9h16" />
          </svg>
        </p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28">
          <p class="text-2xl text-gray-400">
            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 18 18">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 1v16M1 9h16" />
            </svg>
          </p>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
</body>

</html>