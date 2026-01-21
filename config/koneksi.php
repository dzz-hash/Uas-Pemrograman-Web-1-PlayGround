<?php
// config/koneksi.php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "booking_lapang";
$port = "3306"; 

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi ke Database Gagal: " . mysqli_connect_error());
}
?>