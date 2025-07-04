<?php
require_once '../../vendor/autoload.php';
require_once 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excel
{
    public static function generate()
    {
        $mysqli = new db();
        $mysqli->conn->set_charset("utf8mb4");

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle("Laporan Peminjaman");

            // Query join
            $query = "
        SELECT 
            p.id_peminjaman,
            u.name AS nama_user,
            p.tgl_peminjaman,
            p.tgl_balik,
            p.status,
            j.nama AS nama_jenis,
            b.kode_barang,
            pd.id_peminjaman_detail
        FROM peminjaman_detail pd
        JOIN peminjaman p ON pd.peminjaman_id = p.id_peminjaman
        JOIN barang b ON pd.barang_kode = b.kode_barang
        JOIN jenis j ON b.jenis_id = j.id_jenis
        LEFT JOIN user u ON p.id_peminjaman LIKE CONCAT(u.id, '%')
        ORDER BY p.tgl_peminjaman DESC
    ";

            $result = $mysqli->conn->query($query);

            if ($result && $result->num_rows > 0) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }

                $headers = array_keys($data[0]);

                $colIndex = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($colIndex . '1', $header);
                    $colIndex++;
                }

                $rowIndex = 2;
                foreach ($data as $record) {
                    $colIndex = 'A';
                    foreach ($record as $value) {
                        $sheet->setCellValue($colIndex . $rowIndex, $value);
                        $colIndex++;
                    }
                    $rowIndex++;
                }

                foreach ($headers as $i => $header) {
                    $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
                }
            } else {
                $sheet->setCellValue('A1', 'Tidak ada data peminjaman.');
            }

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            date_default_timezone_set('Asia/Jakarta');
            $filename = "laporan-peminjaman_" . date('Y-m-d_H-i') . ".xlsx";
            $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename); // Safe filename

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            header('Cache-Control: max-age=0');

            if (ob_get_length()) ob_end_clean();
            $writer->save('php://output');

            $mysqli->conn->close();
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    }
}

// excel::generate();
