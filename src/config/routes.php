<?php

// PENTING: Path ini HARUS BENAR relatif terhadap lokasi file routes.php
// Berdasarkan struktur folder yang Anda tunjukkan (routes.php, helper.php, db.php di src/config/)
require_once __DIR__ . "/helper.php";
require_once __DIR__ . "/../modules/barang.php";
require_once __DIR__ . "/../config/user_handler.php";

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
 * Satu route bisa untuk dua method, Semisal GET:login untuk menampilkan halaman login dan POST:login untuk proses login
 * @example
 * case 'GET:login':
 * require_once "src/pages/login.php";
 * break;
 * case 'POST:login':
 * $emailUser = $_POST['email'];
 * $passwordUser = $_POST['password'];
 * $login = cekKredensial($emailUser, $passwordUser);
 *
 * return $login ? true : false; #kalau true berhasil login, false gagl
 * break;
 *
 * Jika ada logic yang harus digeluti, bisa ditaruh diantara keyword case dan (require_once/return) (penjelasan di step selanjutnya)
 * @example
 * case 'POST:cihuyy':
 * $num = $_GET['num'];
 * $total = $num + 99;  # logicnya taruh disini
 * return $total;
 * break;
 *
 * Kenapa diantara case dan require_once/return, kalau return jelas logicnya akan berakhir jika sudah return nilai,
 * kalau require, sistem akan memanggil file tsb untuk ditampilkan ke user, dan SEMUA variabel yang dideklarasikan sebelumnya bisa DIAKSES di page tsb
 * @example
 * case 'GET:login':
 * $data = $_GET['data'];
 *
 * $nama = ambildaridb();
 * $umur = ambildaridb();
 *
 * require_once "src/pages/login.php";
 * break;
 *
 * Lalu didalam kode login.php
 * @example
 * <html>
 * <body>
 * <h1>Nama : <?php echo $nama ?> </h1>
 * <h2>Umur : <?php echo $umur ?> </h2>
 * <p>Data : <?php echo $data ?> </p> #bisa diakses
 * </body>
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

// Periksa apakah port 8080 masih digunakan di URL Anda.
// Jika Anda mengaksesnya di http://localhost/inventaris-barang-kantor/, maka portnya 80 (default).
// Jika Anda mengaksesnya di http://localhost:8080/inventaris-barang-kantor/, maka portnya 8080.
// Error "Server at localhost Port 80" menunjukkan Apache berjalan di port 80.
// Pastikan URL di browser Anda sesuai dengan port ini.
$loc = "/" . Helper::getEnv("APP_NAME");

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$request = (Helper::getEnv("APP_MODE") == "PROD") ? ltrim($request, '/') : str_replace($loc, "", $request);

$comp = "$method:$request";
$comp = preg_replace('/\?.*$/', '', $comp);

$comp = preg_replace('/\?.*$/', '', $comp);

session_start();

switch ($comp) {
    case 'GET:home':
        Helper::route("dashboard");
        break;

    case 'GET:peminjaman':
        UserHandler::page_verify('pegawai');
        $db = new db();

        if (isset($_GET['reset'])) {
            setcookie('peminjaman', '', time() + (-3600 * 24));
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        if (isset($_GET['barang']) && isset($_GET['jumlah'])) {
            $barang = $_GET['barang'];
            $jumlahBaru = (int)$_GET['jumlah'];

            // Ambil data cookie yang sudah ada
            $dataPeminjaman = isset($_COOKIE['peminjaman']) ? json_decode($_COOKIE['peminjaman'], true) : [];

            // Hitung total jumlah peminjaman barang yang sama
            $jumlahSebelumnya = 0;
            foreach ($dataPeminjaman as $item) {
                if ($item['barang'] === $barang) {
                    $jumlahSebelumnya += (int)$item['jumlah'];
                }
            }

            $jumlahTotal = $jumlahSebelumnya + $jumlahBaru;

            // Ambil stok_tersedia dari database
            $stmt = $db->conn->prepare("SELECT stok_tersedia FROM jenis WHERE nama = ?");
            $stmt->bind_param("s", $barang);
            $stmt->execute();
            $result = $stmt->get_result();
            $stokRow = $result->fetch_assoc();

            if (!$stokRow) {
                $err = "Barang '$barang' tidak ditemukan dalam database.";
                Helper::route("/peminjaman", ["error" => $err]);
            }

            $stokTersedia = (int)$stokRow['stok_tersedia'];

            // Jika jumlah total melebihi stok
            if ($jumlahTotal > $stokTersedia) {
                $err = "Jumlah total $barang ($jumlahTotal) melebihi stok tersedia ($stokTersedia).";
                Helper::route("/peminjaman", ["error" => $err]);
            }

            // Tambahkan atau update cookie
            $found = false;
            foreach ($dataPeminjaman as &$item) {
                if ($item['barang'] === $barang) {
                    $item['jumlah'] += $jumlahBaru;
                    $found = true;
                    break;
                }
            }
            unset($item);

            if (!$found) {
                $dataPeminjaman[] = [
                    'barang' => $barang,
                    'jumlah' => $jumlahBaru
                ];
            }

            setcookie('peminjaman', json_encode($dataPeminjaman), time() + (3600 * 24)); // 1 hari
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        // $error = $_GET['error'];
        require_once __DIR__ . "/../pages/pegawai/peminjaman.php";
        break;

    case 'GET:pengembalian':
        UserHandler::page_verify('pegawai');
        $db = new db();

        // Ambil semua ID peminjaman yang masih aktif (status = 'dipinjam')
        $querys = $db->conn->query("SELECT id_peminjaman FROM peminjaman WHERE status = 'dipinjam'");

        if (isset($_GET['id_peminjaman'])) {
            $id = $_GET['id_peminjaman'];

            // Cek apakah ID peminjaman valid dan masih dipinjam
            $stmt = $db->conn->prepare("SELECT total_pinjam, deskripsi FROM peminjaman WHERE id_peminjaman = ? AND status = 'dipinjam'");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Ambil deskripsi
            $row = $result->fetch_assoc();
            $total_pinjam = $row['total_pinjam'];
            $deskripsi = $row['deskripsi'];
            $total_pinjam = $row['total_pinjam'];

            if ($result->num_rows === 0) {
                $err = "ID Peminjaman ($id) yang Anda masukkan tidak ditemukan atau sudah dikembalikan.";
                Helper::route("/pengembalian", ["error" => $err]);
            }

            // Ambil detail barang
            $stmt2 = $db->conn->prepare("
            SELECT j.nama, d.barang_kode
            FROM peminjaman p
            JOIN peminjaman_detail d ON p.id_peminjaman = d.peminjaman_id
            JOIN barang b ON d.barang_kode = b.kode_barang
            JOIN jenis j ON j.id_jenis = b.jenis_id
            WHERE p.id_peminjaman = ?
        ");
            $stmt2->bind_param("s", $id);
            $stmt2->execute();
            $querys2 = $stmt2->get_result();
        }

        require_once __DIR__ . "/../pages/pegawai/pengembalian.php";
        break;

    case 'POST:return':
        require_once __DIR__ . "/../pages/old/return.php";
        break;

    case 'POST:search':
    case 'GET:search':
        require_once __DIR__ . "/../pages/old/search_barang.php";
        break;

    case 'POST:loan':
        require_once __DIR__ . "/../pages/old/loan.php";
        break;

    case 'GET:login':
        $status = UserHandler::login_verify();
        if (!$status) {
            require_once __DIR__ . "/../pages/login.php";
        }
        break;
    
    case 'POST:login': // LOGIKA PROSES LOGIN ADA DI SINI (metode POST)
        $status = UserHandler::login($_POST['username'], $_POST['password']);
        if ($status) {
            if (!empty($_SESSION['redirect_url'])) {
                $redirect_url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
                header("Location: " . $redirect_url);
                exit;
            } else {
                Helper::route("dashboard");
            }
        } else {
            if (!empty($_SESSION['login_errmsg'])) {
                Helper::route("login");
            } else {
                require_once __DIR__ . "/../pages/login.php";
            }
        }
        break; // Penting: Jangan lupa break!

    case 'GET:register':
        $status = UserHandler::login_verify();
        if (!$status) {
            require_once __DIR__ . "/../pages/register.php";
        }
        break;

    case 'POST:register':
        $status = UserHandler::register($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['role']);
        if ($status) {
            Helper::route("login");
        } else {
            if (!empty($_SESSION['register_errmsg'])) {
                Helper::route("register");
            } else {
                require_once __DIR__ . "/../pages/register.php";
            }
        }
        break;
        
    case 'GET:logout':
        UserHandler::logout();
        break;

    case 'GET:dashboard':
        // This route's only job is to figure out where the user should go.
        UserHandler::dashboard_verify();
        break;

    case 'GET:admin':
        UserHandler::switch("admin");
        break;

    case 'GET:pegawai':
        UserHandler::switch("pegawai");
        break;

    case 'POST:kategori':
    case 'GET:kategori':
        UserHandler::page_verify('admin');
        require_once __DIR__ . "/../pages/admin/kategori.php";
        break;

    case 'POST:jenis':
    case 'GET:jenis':
        UserHandler::page_verify('admin');
        require_once __DIR__ . "/../pages/admin/jenis.php";
        break;

    case 'POST:kondisi':
    case 'GET:kondisi':
        UserHandler::page_verify('admin');
        require_once __DIR__ . "/../pages/admin/kondisi.php";
        break;

    case 'POST:excel':
        UserHandler::page_verify('admin');
        require_once __DIR__ . "/../config/excel.php";
        excel::generate();
        break;

    case 'POST:barang':
    case 'GET:barang':
        UserHandler::page_verify('admin');
        require_once __DIR__ . "/../pages/admin/barang.php";
        break;
        
    case 'GET:barang/create':
    case 'POST:barang/create':
        require_once __DIR__ . "/../pages/admin/barang_logic.php";
        Barang::create();
        break;

    case 'POST:barang/update':
        require_once __DIR__ . "/../pages/admin/barang_logic.php";
        Barang::update();
        break;

    case 'POST:barang/delete':
        require_once __DIR__ . "/../pages/admin/barang_logic.php";
        Barang::delete();
        break;
    case 'GET:barang/delete':
        require_once __DIR__ . "/../pages/admin/barang.php";
        // Barang::delete();
        break;

    default:
        // Coba arahkan ke halaman utama atau login jika rute tidak ditemukan secara default
        // Agar tidak langsung 404 pada URL root
        if ($request == '') { // Jika user hanya mengakses URL dasar (misal: http://localhost/inventaris-barang-kantor/)
            Helper::route("admin"); // Arahkan ke home atau dashboard
        } else {
            echo "$comp"; // Untuk debugging, bisa diaktifkan
            http_response_code(404);
            require __DIR__ . "/../pages/error/404.php";
        }
        break;
}
