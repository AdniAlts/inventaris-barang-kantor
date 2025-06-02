<?php
$host = "localhost";     
$user = "root";          
$pass = "";              
$db   = "inventaris_barang"; // nama database dari file SQL kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// echo "whats up bitches";
?>