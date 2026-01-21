<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $hapus = mysqli_query($conn, "DELETE FROM booking WHERE id_booking = '$id'");

    if ($hapus) {
        // Kita gunakan session untuk trigger SweetAlert di halaman data_booking jika mau, 
        // tapi redirect langsung adalah cara tercepat.
        header("Location: data_booking.php?pesan=hapus_berhasil");
    } else {
        header("Location: data_booking.php?pesan=gagal");
    }
} else {
    header("Location: data_booking.php");
}
?>