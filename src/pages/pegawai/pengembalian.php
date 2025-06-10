<?php
require_once __DIR__ . '/../../config/helper.php';
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengembalian - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
</head>

<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <div class="my-5">
      <h1 class="text-3xl font-bold text-black-400">
        Pengembalian
      </h1>
    </div>
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

      <!-- START PILIH ID PEMINJAMAN -->
      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="text">
          <h3 class="text-xl font-semibold text-black-700 m-3 pt-4">Tambah Barang untuk Dipinjam</h3>
        </div>
        <form action="" method="get" class="w-full p-3">
          <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
          <div class="relative w-full">
            <input name="id_peminjaman" type="search" id="default-search" class="block w-full p-3 ps-10 text-md font-semibold text-gray-900 border border-gray-300 rounded-lg bg-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Search ID Peminjaman..." required />
            <button type="submit" class="text-white mt-3 block end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
          </div>
        </form>
      </div>
      <!-- END PILIH ID PEMINJAMAN -->
      <!-- START BAGIAN TABLE BARANG DIPINJAM -->
      <div class="h-auto mb-4 rounded bg-gray-50">
        <div class="text">
          <h3 class="text-xl font-semibold text-black-700 m-3 pt-4">Daftar Barang yang Akan Dipinjam</h3>
          <?php if (isset($_GET['id_peminjaman'])): ?>
            <h4 class="text-lg font-medium text-black-700 m-3">Peminjaman #<?php echo $_GET['id_peminjaman']; ?></h4>
            <h6 class="text-sm font-normal text-black-700 m-3"><?php echo $deskripsi; ?></h6>
          <?php endif; ?>
        </div>
        <div class="table w-full p-3">
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-blue-100">
              <tr>
                <th scope="col" class="px-6 py-3">Kode Barang</th>
                <th scope="col" class="px-6 py-3">Nama</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (isset($id)) {
                foreach ($querys2 as $query2) {
                  echo "<tr class='bg-white border-b hover:bg-blue-50'>
                        <td class='px-6 py-4 font-medium text-gray-900'>{$query2['barang_kode']}</td>
                        <td class='px-6 py-4'>{$query2['nama']}</td>
                      </tr>";
                }
              } else {
                echo "<tr>
                      <td colspan='2' class='px-6 py-4 text-center text-gray-500'>Pilih ID peminjaman untuk melihat daftar barang</td>
                    </tr>";
              }
              ?>
            </tbody>
            <tfoot class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3">Total Jumlah</th>
                <th scope="col" class="px-6 py-3">
                  <?php
                  if (isset($total_pinjam)) {
                    echo $total_pinjam;
                  } else {
                    echo "-";
                  }
                  ?>
                </th>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- Submit Pengembalian -->
        <?php if (isset($id)): ?>
          <form action="<?= Helper::basePath(); ?>return" method="post">
            <?php
            foreach ($querys2 as $index => $query2) {
              echo "<input type='hidden' name='barang[$index][barang_kode]' value='{$query2['barang_kode']}'>";
            }
            echo "<input type='hidden' name='id_peminjaman' value='{$id}'>";
            ?>
            <button type="submit" class="px-6 py-2 ms-3 mb-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">Submit Pengembalian</button>
          </form>
        <?php endif; ?>
      </div>
      <!-- END BAGIAN TABLE BARANG DIPINJAM -->
    </div>
  </main>

  <script src="<?= Helper::basePath(); ?>src/flowbite.min.js"></script>
  <script>
    
  </script>
</body>

</html>