<?php
require_once "db.php";
require_once "helper.php";

// PENTING: Path ini HARUS BENAR relatif terhadap lokasi file routes.php
// Berdasarkan struktur folder yang Anda tunjukkan (routes.php, helper.php, db.php di src/config/)
require_once __DIR__ . "/helper.php";
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/../modules/barang.php";

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
$loc = "/inventaris-barang-kantor/";


$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$request = str_replace($loc, "", $request);

$comp = "$method:$request";
$comp = preg_replace('/\?.*$/', '', $comp);

$comp = preg_replace('/\?.*$/', '', $comp);

// Mulai sesi PHP jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


switch ($comp) {
    case 'GET:home':
        require_once __DIR__ . "/../pages/old/landing.php";
        break;

    case 'GET:peminjaman':
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

    case 'POST:loan':
        require_once __DIR__ . "/../pages/old/loan.php";
        break;

    case 'GET:pengembalian':
        $db = new db();

        // Ambil semua ID peminjaman yang masih aktif (status = 'dipinjam')
        $querys = $db->conn->query("SELECT id_peminjaman FROM peminjaman WHERE status = 'dipinjam'");

        if (isset($_GET['id_peminjaman'])) {
            $id = $_GET['id_peminjaman'];

            // Cek apakah ID peminjaman valid dan masih dipinjam
            $stmt = $db->conn->prepare("SELECT total_pinjam FROM peminjaman WHERE id_peminjaman = ? AND status = 'dipinjam'");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $err = "ID Peminjaman ($id) yang Anda masukkan tidak ditemukan atau sudah dikembalikan.";
                Helper::route("/pengembalian", ["error" => $err]);
            }

            $row = $result->fetch_assoc();
            $total_pinjam = $row['total_pinjam'];

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

    case 'GET:login':
        require_once __DIR__ . "/../pages/login.php";
        break;

    case 'GET:admin':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/dashboard.php";
        break;



    case 'POST:login': // LOGIKA PROSES LOGIN ADA DI SINI (metode POST)
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $login_successful = false;
        $error_message = "";

        if (empty($username) || empty($password)) {
            $error_message = "Username dan password harus diisi.";
        } else {
            $db = new db(); // Membuat objek database

            $stmt = $db->conn->prepare("SELECT id_admin, username, password FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                if ($password === $admin['password']) { // INGAT: HARUSNYA PAKAI password_verify()
                    $_SESSION['admin_id'] = $admin['id_admin'];
                    $_SESSION['username'] = $admin['username'];
                    $login_successful = true;
                } else {
                    $error_message = "Username atau password salah.";
                }
            } else {
                $error_message = "Username atau password salah.";
            }

            $stmt->close();
            $db->close();
        }

        if ($login_successful) {
            $_SESSION['error_message'] = '';
            Helper::route("admin"); // Arahkan ke halaman 'home' (dashboard)
        } else {
            $_SESSION['error_message'] = $error_message;
            Helper::route("login");
            // Helper::route("login"); ['error' => urlencode($error_message)]); // Arahkan kembali ke login dengan error
        }
        break; // Penting: Jangan lupa break!

    case 'GET:logout':
        $_SESSION = array();

        // Hancurkan sesi
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        // Arahin ke halaman login
        Helper::route("login");
        break;

    case 'GET:user':

        require_once __DIR__ . "/../pages/pegawai/dashboard.php";
        break;

    case 'GET:kategori':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/kategori.php";
        break;

    case 'POST:kategori':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/kategori.php";
        break;

    case 'GET:jenis':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/jenis.php";
        break;
    case 'POST:jenis':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/jenis.php";
        break;

    case 'GET:kondisi':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/kondisi.php";
        break;

    case 'POST:kondisi':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/kondisi.php";
        break;

    case 'GET:barang':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/barang.php";
        break;
    case 'POST:barang/create':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/barang.php";
        Barang::create();
        break;

    case 'POST:barang/update':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/barang.php";
        Barang::update();
        break;

    case 'POST:barang/delete':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/barang.php";
        break;
    case 'GET:barang/delete':
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }
        require_once __DIR__ . "/../pages/admin/barang.php";
        Barang::delete();
        break;

    // Remove the old POST:gambar case since it's now integrated into the Barang class

    // case 'GET:gambar':
    //     require_once __DIR__ . "/../pages/gambar.php";
    //     break;

    case 'POST:gambar':
        header('Content-Type: application/json');

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $originalName = $file['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $randomName = bin2hex(random_bytes(16)) . '.' . $extension;

            // MASUKKAN $randomName ke gambar_url di DB saat masukan entry data

            $uploadDir = __DIR__ . '/../storages/';
            $destination = $uploadDir . $randomName;

            if (!is_writable($uploadDir)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Upload directory is not writable: ' . $uploadDir
                ]);
                exit();
            }

            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)
                ]);
                exit();
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'success',
                    'filename' => $randomName,
                    'path' => $destination
                ]);
            } else {
                $error = error_get_last();
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to move uploaded file',
                    'destination' => $destination,
                    'error' => $error ? $error['message'] : 'Unknown error',
                    'temp_file' => $file['tmp_name'],
                    'temp_exists' => file_exists($file['tmp_name']) ? 'yes' : 'no'
                ]);
            }
        } else {
            // Handle different upload errors
            $errorMessage = 'No file was uploaded or there was an upload error';
            if (isset($_FILES['image']['error'])) {
                switch ($_FILES['image']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errorMessage = 'File exceeds upload_max_filesize directive';
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errorMessage = 'File exceeds MAX_FILE_SIZE directive';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $errorMessage = 'File was only partially uploaded';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $errorMessage = 'No file was uploaded';
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errorMessage = 'Missing temporary folder';
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errorMessage = 'Failed to write file to disk';
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $errorMessage = 'File upload stopped by extension';
                        break;
                }
            }

            echo json_encode([
                'success' => false,
                'message' => $errorMessage
            ]);
        }
        exit();
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
