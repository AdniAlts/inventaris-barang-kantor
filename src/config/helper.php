<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Class khusus untuk berbagai fungsi-fungsi helper karena disini kita tidak pakai framework
 * Cara Pemanggilan : 
 * 
 * deklarasi ->
 * require_once "#isi_path_dari_file_ke_folder_sql/helper.php"
 *
 * panggil fungsi ->
 * helper::<nama fungsi>(); 
 * 
 * @example $url = helper::getEnv('APP_URL'); mengambil env APP_URL di file .env
 * @example helper::route('login'); mengalihkan browser user ke route GET login
 * 
 * Jika ada fungsi helper yang ingin ditambahkan monggo, jangan lupa dokumentasi
 * 
 * @author rehan 
 */
class Helper
{
    /**
     * Fungsi untuk mengakses env yang ada di root projej
     * 
     * @param string $name -> nama env yang mau diakses
     * @return string env
     */
    public static function getEnv($name)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        // var_dump($_ENV);


        if ($_ENV[$name])
            return trim($_ENV[$name]);
        else
            return "";
    }

    /**
     * Fungsi untuk mengakses direktori root projek 
     * 
     * contoh: http://127.0.0.1:8080/inventaris-barang-kantor/
     * 
     * @return string root direktori projek
     */
    public static function basePath(): string
    {
        // echo self::getEnv("APP_URL");
        return self::getEnv("APP_URL");
    }

    /**
     * Fungsi untuk berpindah route dan mindahin data (### JENIS GET ###)
     * 
     * @param string $route rute yang akan dituju
     * @param array $data isi semua data yang mau dipindahkan, data dapat diambil di file routes dengan $_GET[<nama data>] 
     * 
     * @example 
     * helper::route("cihuyy", [
     *      'nama' => 'rehan',
     *      'kelas' => 'itece'
     * ]);
     * 
     * Hasilnya kode diatas akan ke http://127.0.0.1:8080/inventaris-barang-kantor/cihuyy?nama=rehan&kelas=itece
     * Dan di dalam file routes.php dapat menggunakan case $loc . "cihuyy" untuk mengambil operan nya
     * 
     * @see routes.php
     * @return void
     */
    public static function route(string $route, array $data = [])
    {
        $route = ltrim($route, '/');

        $queryString = http_build_query($data);
        $url = self::basePath() . $route . ($queryString ? '?' . $queryString : '');

        header("Location: $url");
        exit();
    }

    /**
     * Fungsi untuk berpindah route dan mindahin data (### JENIS POST ###) 
     * POST disini tidak merubah url web user ya, hanya mengirim request dan akan diterima di routes.php
     * Jika sudah terkirim, bisa tambahkan return di case routes.php, nanti returnnya adalah hasil kembalian dari helper::post()
     * 
     * @param string $route rute yang akan dituju
     * @param array $data isi semua data yang mau dipindahkan, data dapat diambil di file routes dengan $_POST[<nama data>] 
     * 
     * @example
     * $isSuccess = helper::post("cihuyy", [
     *      'nama' => 'rehan',
     *      'kelas' => 'itece'
     * ]);
     * 
     * if ($isSuccess)
     *      echo "Sukses";
     * 
     * NOTE : isi dari $isiSuccess disini tergantung apa yang direturn di file routes.php
     * 
     * @see routes.php
     * @return mixed tergantung return logic
     */
    public static function post(string $route, array $data = [])
    {
        $basepath = self::basePath();
        // echo $basepath . $route
        $session = curl_init($basepath . $route);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($session);
        curl_close($session);
        return $response;
    }
}

Helper::basePath();
