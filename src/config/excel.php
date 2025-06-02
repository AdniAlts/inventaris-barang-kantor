<?php
require_once '../../vendor/autoload.php';
require_once 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$mysqli = new db();

$mysqli->conn->set_charset("utf8mb4");

try {
    $tables = [
        'admin',
        'kategori',
        'jenis',
        'state',
        'barang',
        'peminjaman',
        'peminjaman_detail',
        'user'
    ];

    $spreadsheet = new Spreadsheet();

    // hapus defaultanya
    $spreadsheet->removeSheetByIndex(0);

    foreach ($tables as $index => $table) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($table);

        $result = $mysqli->conn->query("SELECT * FROM $table");
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        if (!empty($data)) {
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

            foreach ($headers as $index => $header) {
                $columnLetter = chr(65 + $index); // A, B, C, etc.
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }
        } else {
            try {
                $result = $mysqli->conn->query("DESCRIBE $table");
                if ($result) {
                    $colIndex = 'A';
                    while ($row = $result->fetch_assoc()) {
                        $sheet->setCellValue($colIndex . '1', $row['Field']);
                        $colIndex++;
                    }
                }
            } catch (Exception $e) {
                $sheet->setCellValue('A1', 'No data available');
            }
        }
    }

    $spreadsheet->setActiveSheetIndex(0);

    $writer = new Xlsx($spreadsheet);
    $filename = "database-export_";
    $uploadDir = __DIR__ . '/../storages/excel/';
    date_default_timezone_set('Asia/Jakarta');

    $writer->save($uploadDir . $filename . date('d-m-Y_H:i') . ".xlsx");

    $mysqli->conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
