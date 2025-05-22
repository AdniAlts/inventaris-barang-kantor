<?php
require_once "helper.php";

/**
 * Class untuk interaksi ke database
 * 
 * @author reyhan 
 */
class db
{
    /**
     * Properti untuk mengakses database
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
     * 
     * @return void 
     */
    public function close()
    {
        mysqli_close($this->conn);
    }
}
