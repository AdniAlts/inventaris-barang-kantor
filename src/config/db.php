<?php
require_once "helper.php";

/**
 * Class untuk interaksi ke database
 * 
 * PANDUAN MENGGUNAKAN DATABASE
 * 
 * 1. Deklarasikan objek db                             --> $db = new db;
 * 2. (optional) cek jika gagal koneksi                 --> if ($db->conn->connect_error) echo "gagal koneksi";
 * 3. Gunakan fungsi db normal seperti biasanya 
 *    memakai property conn                             --> $db->conn->query("INSERT ...");
 * 4. Jika sudah selesai, jangan lupa tutup koneksi
 *    pakai fungsi close()                              --> $db->conn->close();
 * 
 * Untuk skrip sql bisa langsung 
 * @see sql/akupergi.sql
 * 
 * @author reyhan 
 */
class db
{
    /**
     * Properti untuk mengakses database
     * @example $db->conn->query();
     */
    public $conn;

    /**
     * Informasi untuk ke database diambil dari file .env
     * 
     * @see ../../.env
     * @return void  
     */
    public function __construct()
    {
        $servername = Helper::getEnv('DB_HOST');
        $username = Helper::getEnv('DB_USERNAME');
        $password = Helper::getEnv('DB_PASSWORD');
        $dbname = Helper::getEnv('DB_DATABASE');
        $dbport = Helper::getEnv('DB_PORT');

        $this->conn = new mysqli(
            $servername,
            $username,
            $password,
            $dbname,
            $dbport
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    /**
     * Fungsi untuk menutup koneksi database
     * JANGAN LUPA TUTUP
     * 
     * @return void 
     */
    public function close()
    {
        mysqli_close($this->conn);
    }
}
