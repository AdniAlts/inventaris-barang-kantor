<?php
require_once __DIR__ . '/../../config/db.php';

class Barang
{

    public static function read()
    {
        $mysqli = (new db())->conn;

        $query = "SELECT k.id_kategori, k.nama AS kategori_nama, j.id_jenis, j.nama AS jenis_nama, j.stok, b.kode_barang, b.status AS barang_status, b.gambar_url, s.nama AS state_nama FROM kategori k LEFT JOIN jenis j ON k.id_kategori = j.kategori_id LEFT JOIN barang b ON j.id_jenis = b.jenis_id LEFT JOIN state s ON b.state_id = s.id_state ORDER BY k.nama, j.nama, b.kode_barang";
        $result = $mysqli->query($query);
        $categories = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kategoriId = $row['id_kategori'];

                if (!isset($categories[$kategoriId])) {
                    $categories[$kategoriId] = [
                        'id' => $kategoriId,
                        'nama' => $row['kategori_nama'],
                        'jenis_items' => []
                    ];
                }

                $jenisId = $row['id_jenis'];

                if ($jenisId && !isset($categories[$kategoriId]['jenis_items'][$jenisId])) {
                    $categories[$kategoriId]['jenis_items'][$jenisId] = [
                        'id' => $jenisId,
                        'nama' => $row['jenis_nama'],
                        'stok' => $row['stok'],
                        'barang_items' => []
                    ];
                }

                // Add barang if exists
                if ($row['kode_barang']) {
                    $categories[$kategoriId]['jenis_items'][$jenisId]['barang_items'][] = [
                        'kode' => $row['kode_barang'],
                        'status' => $row['barang_status'],
                        'gambar_url' => $row['gambar_url'],
                        'state' => $row['state_nama']
                    ];
                }
            }
        }

        // var_dump($categories['nama']);

        $result->free();
        return $categories;
    }

    public static function create()
    {
        $conn = (new db())->conn;

        $kategori = mysqli_real_escape_string($conn, $_GET['kategori']);
        $jenis = mysqli_real_escape_string($conn, $_GET['jenis']);
        $kualitas = mysqli_real_escape_string($conn, $_GET['kualitas']);
        $jumlah = (int)$_GET['jumlah'];

        $query = "SELECT id_jenis FROM jenis WHERE nama = '$jenis'";
        $result = $conn->query($query);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $jenis_id = $rows[0]["id_jenis"];

        $query2 = "SELECT kode_barang FROM barang WHERE jenis_id = '$jenis_id' ORDER BY created_at DESC LIMIT 1";
        $result2 = $conn->query($query2);
        $rows2 = $result2->fetch_all(MYSQLI_ASSOC);

        $lastNum = 0;
        if (!empty($rows2)) {
            $kodeNum = $rows2[0]['kode_barang'];
            $part = explode('_', $kodeNum);
            $lastNum = (int)$part[1];
        }

        for ($i = 1; $i <= $jumlah; $i++) {
            $num = str_pad($lastNum + $i, 4, '0', STR_PAD_LEFT);
            $newKode = $jenis_id . '_' . $num;
            $status = 'tersedia';
            $f = null;

            $stmt = $conn->prepare("INSERT INTO barang (kode_barang, status, state_id, gambar_url, jenis_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $newKode, $status, $kualitas, $f, $jenis_id);
            $s = $stmt->execute();

            if (!$s) {
                die($stmt->error);
            }
        }

        $conn->query("UPDATE jenis SET stok = stok + $jumlah, stok_tersedia = stok_tersedia + $jumlah WHERE id_jenis = '$jenis_id'");

        $conn->close();

        Helper::route("barang");
    }

    public static function update()
    {
        // die(var_dump($_POST));
        header('Content-Type: application/json');

        $conn = (new db())->conn;
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (!isset($_POST['kode_barang'], $_POST['status'], $_POST['kualitas'])) {
            die("Missing required fields");
        }

        $kode_barang = $_POST['kode_barang'];
        $status = $_POST['status'];
        $state_id = $_POST['kualitas'];

        $gambar_url = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $originalName = $file['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $randomName = bin2hex(random_bytes(16)) . '.' . $extension;

            $allowed_ext = ['jpg', 'jpeg', 'png'];
            if (!in_array(strtolower($extension), $allowed_ext)) {
                die("Invalid file type");
            }

            $dir = __DIR__ . '/../../storages/gambar/';
            $destination = $dir . $randomName;
            $relativePath = 'storages/gambar/' . $randomName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                die("Failed to upload file");
            }

            $gambar_url = $relativePath;
        }

        $stmt = $conn->prepare("UPDATE barang SET status = ?, state_id = ?, gambar_url = ? WHERE kode_barang = ?");
        $stmt->bind_param("siss", $status, $state_id, $gambar_url, $kode_barang);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
        Helper::route("barang");
    }

    public static function delete()
    {
        // die(var_dump($_POST));
        $db = (new db())->conn;
        $idDel = $db->real_escape_string($_POST['elmo']);


        $sql = "DELETE FROM barang WHERE kode_barang = '$idDel'";
        if (!$db->query($sql)) {
            die("error: " . $db->error);
        }

        Helper::route('barang');
    }
}



// Barang::read();
