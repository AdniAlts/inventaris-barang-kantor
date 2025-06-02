<?php
// Ensure db.php is available for database connection
require_once __DIR__ . "/../config/db.php";

class Barang
{
    public static function create()
    {
        header('Content-Type: application/json');

        try {
            // Validate required fields (before image upload, as it might depend on jenis_id)
            $status = trim($_POST['status'] ?? '');
            $jenis_id = $_POST['jenis'] ?? ''; // Adjusted name to 'jenis' from UI
            $state_id = $_POST['state'] ?? ''; // Adjusted name to 'state' from UI

            if (empty($status) || empty($jenis_id) || empty($state_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Semua field (Status, Jenis, State) wajib diisi.'
                ]);
                exit();
            }

            // Handle image upload first
            $gambar_url = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
                $imageResult = self::handleImageUpload($_FILES['gambar']);
                if (!$imageResult['success']) {
                    echo json_encode($imageResult);
                    exit();
                }
                $gambar_url = $imageResult['filename'];
            }

            $db = new db();
            $conn = $db->conn;

            // Generate unique kode_barang based on jenis_id
            $kode_barang = self::generateKodeBarang($conn, $jenis_id);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO barang (kode_barang, status, state_id, gambar_url, jenis_id) VALUES (?, ?, ?, ?, ?)");
            // 's' for kode_barang (VARCHAR), 's' for status (ENUM), 'i' for state_id (INT), 's' for gambar_url (VARCHAR), 's' for jenis_id (VARCHAR)
            $stmt->bind_param("ssiss", $kode_barang, $status, $state_id, $gambar_url, $jenis_id);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang berhasil ditambahkan!',
                    'kode_barang' => $kode_barang
                ]);
            } else {
                // If there's a DB error, delete the uploaded image if it exists
                if ($gambar_url && file_exists(__DIR__ . '/../storages/' . $gambar_url)) {
                    unlink(__DIR__ . '/../storages/' . $gambar_url);
                }
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan barang: ' . $conn->error
                ]);
            }

            $stmt->close();
            $db->close();
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    public static function update()
    {
        header('Content-Type: application/json');

        try {
            $original_kode_barang = $_POST['id_barang'] ?? ''; // This is the identifier from UI (kode_barang)
            $new_kode_barang = trim($_POST['kode_barang'] ?? ''); // This is the new value for kode_barang (should be same as original_kode_barang as it's readonly)
            $status = trim($_POST['status'] ?? '');
            $state_id = $_POST['state'] ?? '';

            if (empty($original_kode_barang) || empty($new_kode_barang) || empty($status) || empty($state_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Semua field wajib diisi untuk update.'
                ]);
                exit();
            }

            $db = new db();
            $conn = $db->conn;

            // Fetch existing jenis_id as it's not editable and not reliably sent from a disabled field
            $stmt_get_jenis = $conn->prepare("SELECT jenis_id FROM barang WHERE kode_barang = ?");
            if (!$stmt_get_jenis) {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare statement to get jenis_id: ' . $conn->error]);
                exit();
            }
            $stmt_get_jenis->bind_param("s", $original_kode_barang);
            if (!$stmt_get_jenis->execute()) {
                echo json_encode(['success' => false, 'message' => 'Failed to execute statement to get jenis_id: ' . $stmt_get_jenis->error]);
                exit();
            }
            $result_jenis = $stmt_get_jenis->get_result();
            if ($result_jenis->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Barang tidak ditemukan untuk mengambil jenis_id.']);
                exit();
            }
            $existing_item_data = $result_jenis->fetch_assoc();
            $jenis_id = $existing_item_data['jenis_id'];
            $stmt_get_jenis->close();

            // Handle image upload if new image is provided
            $gambar_url = null;
            $updateImage = false;

            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
                $imageResult = self::handleImageUpload($_FILES['gambar']);
                if (!$imageResult['success']) {
                    echo json_encode($imageResult);
                    exit();
                }
                $gambar_url = $imageResult['filename'];
                $updateImage = true;

                // Delete old image if exists
                self::deleteOldImage($conn, $original_kode_barang); // Pass kode_barang to delete old image
            }

            // No need to check if new_kode_barang exists if it's readonly and same as original.
            // If kode_barang was editable, this check would be needed:
            // $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM barang WHERE kode_barang = ? AND kode_barang != ?");
            // $stmt_check->bind_param('ss', $new_kode_barang, $original_kode_barang);
            // ... (rest of the check)

            // Update query: USE kode_barang as identifier, not id_barang
            if ($updateImage) {
                $stmt = $conn->prepare("UPDATE barang SET kode_barang = ?, status = ?, state_id = ?, gambar_url = ?, jenis_id = ? WHERE kode_barang = ?");
                // 's' for kode_barang (new), 's' for status, 'i' for state_id, 's' for gambar_url, 's' for jenis_id, 's' for kode_barang (original)
                $stmt->bind_param("ssisss", $new_kode_barang, $status, $state_id, $gambar_url, $jenis_id, $original_kode_barang);
            } else {
                $stmt = $conn->prepare("UPDATE barang SET kode_barang = ?, status = ?, state_id = ?, jenis_id = ? WHERE kode_barang = ?");
                // 's' for kode_barang (new), 's' for status, 'i' for state_id, 's' for jenis_id, 's' for kode_barang (original)
                $stmt->bind_param("ssiss", $new_kode_barang, $status, $state_id, $jenis_id, $original_kode_barang);
            }

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang berhasil diperbarui!'
                ]);
            } else {
                // If DB error, delete the newly uploaded image if it exists
                if ($updateImage && $gambar_url && file_exists(__DIR__ . '/../storages/' . $gambar_url)) {
                    unlink(__DIR__ . '/../storages/' . $gambar_url);
                }
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal memperbarui barang: ' . $conn->error
                ]);
            }

            $stmt->close();
            $db->close();
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    public static function delete()
    {
        header('Content-Type: application/json');

        try {
            $kode_barang_to_delete = $_POST['id_barang'] ?? $_GET['id'] ?? ''; // Get kode_barang as identifier

            if (empty($kode_barang_to_delete)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID barang tidak valid.'
                ]);
                exit();
            }

            $db = new db();
            $conn = $db->conn;

            // Delete associated image first
            self::deleteOldImage($conn, $kode_barang_to_delete);

            // Delete from database: USE kode_barang as identifier, not id_barang
            $stmt = $conn->prepare("DELETE FROM barang WHERE kode_barang = ?");
            $stmt->bind_param('s', $kode_barang_to_delete); // 's' for VARCHAR

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Barang berhasil dihapus!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menghapus barang: ' . $conn->error
                ]);
            }

            $stmt->close();
            $db->close();
        } catch (Exception $e) {
            // Check for foreign key constraint error
            if (strpos($e->getMessage(), '1451') !== false || strpos($e->getMessage(), 'foreign key constraint') !== false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menghapus barang: Barang ini sedang digunakan dalam transaksi lain.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        exit();
    }

    private static function handleImageUpload($file)
    {
        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $randomName = bin2hex(random_bytes(16)) . '.' . $extension;

        $uploadDir = __DIR__ . '/../storages/';
        $destination = $uploadDir . $randomName;

        if (!is_dir($uploadDir)) {
            // Attempt to create the directory if it doesn't exist
            if (!mkdir($uploadDir, 0775, true)) {
                 return [
                    'success' => false,
                    'message' => 'Upload directory does not exist and could not be created: ' . $uploadDir . ' Please check parent directory permissions.'
                ];
            }
        }

        if (!is_writable($uploadDir)) {
            return [
                'success' => false,
                'message' => 'Upload directory is not writable by the web server: ' . realpath($uploadDir) . '. Please check permissions.'
            ];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return [
                'success' => false,
                'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)
            ];
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => true,
                'filename' => $randomName,
                'path' => $destination
            ];
        } else {
            $error = error_get_last();
            return [
                'success' => false,
                'message' => 'Failed to move uploaded file',
                'error_detail' => $error ? $error['message'] : 'Unknown error',
                'temp_file' => $file['tmp_name'],
                'temp_exists' => file_exists($file['tmp_name']) ? 'yes' : 'no'
            ];
        }
    }

    private static function deleteOldImage($conn, $kode_barang)
    {
        $stmt = $conn->prepare("SELECT gambar_url FROM barang WHERE kode_barang = ?");
        $stmt->bind_param('s', $kode_barang);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $oldImage = $row['gambar_url'];
            if ($oldImage) {
                $oldImagePath = __DIR__ . '/../storages/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }
        $stmt->close();
    }

    // Modified to generate kode_barang based on jenis_id
    private static function generateKodeBarang($conn, $jenis_id)
    {
        // Get the last kode_barang for the given jenis_id
        $stmt = $conn->prepare("SELECT kode_barang FROM barang WHERE jenis_id = ? ORDER BY kode_barang DESC LIMIT 1");
        $stmt->bind_param('s', $jenis_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $number = 0;
        if ($row = $result->fetch_assoc()) {
            $lastKode = $row['kode_barang'];
            // Extract number from last kode (e.g., JNK_0001 -> 1)
            // Assuming format like PREFIX_NUMBER
            $parts = explode('_', $lastKode);
            if (count($parts) > 1) {
                $numericPart = end($parts);
                if (is_numeric($numericPart)) {
                    $number = intval($numericPart);
                }
            }
        }
        $number++; // Increment for the new code

        $stmt->close();

        // Generate new kode with format JENISID_0001
        return strtoupper($jenis_id) . '_' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
