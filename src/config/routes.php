<?php
require_once "db.php";
require_once "helper.php";

// PENTING: Path ini HARUS BENAR relatif terhadap lokasi file routes.php
// Berdasarkan struktur folder yang Anda tunjukkan (routes.php, helper.php, db.php di src/config/)
require_once __DIR__ . "/helper.php";
require_once __DIR__ . "/db.php";

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
        require_once "../pages/landing.php";
        break;

    case 'GET:peminjaman':
        $db = new db();
        $querys = $db->conn->query("SELECT DISTINCT k.nama, k.stok FROM kategori k JOIN barang b ON k.id_kategori = b.kategori_id WHERE b.status = 'tersedia' AND b.state_id = 1 AND k.stok > 0");

        if (isset($_GET['reset'])) {
            setcookie('peminjaman', '', time() + (-3600 * 24));
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }

        if (isset($_GET['barang']) && isset($_GET['jumlah'])) {
            $id = $_GET['id_kategori'];
            $barang = $_GET['barang'];
            $jumlah = $_GET['jumlah'];

            // Ambil data cookie yang sudah ada
            $dataPeminjaman = isset($_COOKIE['peminjaman']) ? json_decode($_COOKIE['peminjaman'], true) : [];

            // Cek apakah jumlah > stok
            $query = $db->conn->query("SELECT stok FROM kategori WHERE nama = '$barang'");
            $row = $query->fetch_assoc();
            $stok = $row['stok'];
            if ($jumlah > $stok) {
                $err = "Jumlah $barang yang Anda masukkan = $jumlah melebihi jumlah stok = $stok";

                Helper::route("/peminjaman", [
                    "error" => $err
                ]);
            }

            // Tambahkan data baru
            $dataPeminjaman[] = [
                'id' => $id,
                'barang' => $barang,
                'jumlah' => $jumlah
            ];

            // Simpan kembali ke cookie (serialize array ke JSON)
            setcookie('peminjaman', json_encode($dataPeminjaman), time() + (3600 * 24)); // berlaku 1 hari

            // Redirect
            header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
            exit;
        }
        // $error = $_GET['error'];
        require_once "../pages/peminjaman.php";
        break;

    case 'POST:loan':
        require_once "../pages/loan.php";
        break;

    case 'GET:pengembalian':
        $db = new db();

        $querys = $db->conn->query("SELECT id_peminjaman FROM peminjaman WHERE status = 'dipinjam'");

        if (isset($_GET['id_peminjaman'])) {
            $id = $_GET['id_peminjaman'];

            $query = $db->conn->query("SELECT total_pinjam FROM peminjaman WHERE id_peminjaman = '$id'");
            $row = $query->fetch_assoc();
            $total_pinjam = $row['total_pinjam'];

            $querys2 = $db->conn->query("SELECT b.nama, d.barang_kode FROM peminjaman p JOIN peminjaman_detail d ON p.id_peminjaman = d.peminjaman_id JOIN barang b ON d.barang_kode = b.kode_barang WHERE p.id_peminjaman = '$id'");
        }

        require_once "../pages/pengembalian.php";
        break;

    case 'POST:return':
        require_once "../pages/return.php";
        break;

    case 'POST:search':
    case 'GET:search':
        require_once "../pages/search_barang.php";
        break;

    case 'GET:login':
        // HANYA UNTUK MENAMPILKAN FORM LOGIN
        require_once __DIR__ . "/../pages/login.php";
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
            Helper::route("dashboard"); // Arahkan ke halaman 'home' (dashboard)
        } else {
            Helper::route("login", ['error' => urlencode($error_message)]); // Arahkan kembali ke login dengan error
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
        Helper::route("home");
        break;

    case 'GET:dashboard':
        require_once "../pages/dashboard.php";
        break;

    case 'GET:kategori':
        require_once "../pages/kategori.php";
        break;
    
    case 'GET:kategori':
        require_once "../pages/kategori.php";
        break;

    case 'POST:kategori':
        require_once "../pages/kategori.php";
        break;

    case 'GET:jenis':
        require_once "../pages/jenis.php";
        break;

    case 'GET:state':
        require_once "../pages/state.php";
        break;

    case 'GET:barang':
        require_once "../pages/barang.php";
        break;

    case 'GET:gambar':
        require_once __DIR__ . "/../pages/gambar.php";
        break;

    case 'POST:gambar':
        $randomName = bin2hex(random_bytes(16)) . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/';
        $destination = $uploadDir . $randomName;

        header('Content-Type: application/json');

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['image'];

            $originalName = $file['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);

            $randomName = bin2hex(random_bytes(16)) . '.' . $extension;

            $uploadDir = __DIR__ . '/../uploads/';

            $destination = $uploadDir . $randomName;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'success',
                    'filename' => $randomName
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to save file'
                ]);
            }
            exit();
        }

        echo json_encode([
            'success' => false,
            'message' => 'No file was uploaded or there was an upload error'
        ]);
        break;

    default:
        // Coba arahkan ke halaman utama atau login jika rute tidak ditemukan secara default
        // Agar tidak langsung 404 pada URL root
        if ($request == '') { // Jika user hanya mengakses URL dasar (misal: http://localhost/inventaris-barang-kantor/)
            Helper::route("home"); // Arahkan ke home atau dashboard
        } else {
            echo "$comp"; // Untuk debugging, bisa diaktifkan
            http_response_code(404);
            require __DIR__ . "/../pages/error/404.php";
        }
        break;
}
