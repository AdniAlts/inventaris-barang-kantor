<?php

/**
 * P BACA, PENTING
 * 
 * Jadi konteksnya begini, harapannya jika user searching misal /login, url user tidak perlu ke /src/pages/login.php
 * Maka dibuatlah routing seperti ini,
 * 
 * Jika user searching /login (automatis dengan metode GET), maka akan diproses disini sebagai "GET:login"
 * Maka bisa ditambahkan sendiri case GET:LOGIN dibawah
 * 
 * Untuk menampilkan tampilan pages, maka bisa pakai require_once ke arah file
 * @example require_once "../pages/login.php";
 * 
 * Jika ada data yang harus diambil, bisa pakai $_GET untuk method GET dan $_POST untuk method POST (Untuk method POST sebaiknya ada return)
 * @example $nama = $_GET['nama']  atau  $nama = $_POST['nama'];
 * 
 * Untuk metode GET, disarankan/harus memakai require_once. Jika metodenya POST maka memakai return
 * @example require_once "../pages/login.php"; //GET
 * @example return $data; //POST
 *  
 * Jika ada logic yang harus digeluti, bisa ditaruh diantara keyword case dan (require_once/return) (penjelasan di step selanjutnya)
 * @example 
 * case 'POST:cihuyy':
 *      $num = $_GET['num'];
 *      $total = $num + 99;  # logicnya taruh disini
 *      return $total;
 *      break;
 * 
 * Kenapa diantara case dan require_once/return, kalau return jelas logicnya akan berakhir jika sudah return nilai,
 * kalau require, sistem akan memanggil file tsb untuk ditampilkan ke user, dan SEMUA variabel yang dideklarasikan sebelumnya bisa DIAKSES di page tsb
 * @example 
 * case 'GET:login':
 *      $data = $_GET['data'];
 * 
 *      $nama = ambildaridb();
 *      $umur = ambildaridb();
 * 
 *      require_once "src/pages/login.php";
 *      break;
 * 
 * Lalu didalam kode login.php
 * @example 
 * <html>
 *      <body>
 *          <h1>Nama : <?php echo $nama ?> </h1>
 *          <h2>Umur : <?php echo $umur ?> </h2>
 *          <p>Data : <?php echo $data ?> </p> #bisa diakses
 *      </body>
 * </html>
 * 
 * Lalu reminder, jika ingin mengakses route, misal pengen bikin route /hitung, arahkan ke /inventaris-barang-kantor/<nama route>
 * @example jika GET:hitung maka set metode ke GET dan arahkan ke inventaris-barang-kantor/hitung
 * 
 * Selain ini, baca juga dokumentasi dari:
 * @see helper.php
 * @see db.php
 * 
 * Selamat mengerjakan, dari @author rehan, kalau masih bingung chat aja
 */

/**
 * File untuk routing, agar url tidak menunjukan file
 *
 * @author rehan 
 */

$loc = "/inventaris-barang-kantor/";

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$request = str_replace($loc, "", $request);

// echo $loc . "<br>";
// echo $request . "<br>";

$comp = "$method:$request";

// echo $comp;

switch ($comp) {

    case 'GET:home':
        require_once "../pages/dashboard.php";
        break;

    default:
        http_response_code(404);
        require "../pages/error/404.php";
        // echo $request;
        break;
}
