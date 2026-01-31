<?php
session_start();

// PERBAIKAN: Sesuaikan dengan session yang digunakan di data_booking
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id_mitra = $_SESSION['id_mitra'];

    // PERBAIKAN KEAMANAN: Pastikan hanya bisa menghapus data miliknya sendiri
    // Dan ambil nama file bukti untuk dihapus dari folder
    $cek = mysqli_query($conn, "SELECT bukti_bayar FROM booking WHERE id_booking = '$id' AND id_mitra = '$id_mitra'");
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        // Hapus file fisik bukti bayar jika ada di folder
        if (!empty($data['bukti_bayar']) && file_exists("../assets/bukti/" . $data['bukti_bayar'])) {
            unlink("../assets/bukti/" . $data['bukti_bayar']);
        }

        // Jalankan perintah hapus
        $hapus = mysqli_query($conn, "DELETE FROM booking WHERE id_booking = '$id' AND id_mitra = '$id_mitra'");

        if ($hapus) {
            header("Location: data_booking.php?pesan=hapus_berhasil");
        } else {
            header("Location: data_booking.php?pesan=gagal");
        }
    } else {
        // Jika ID tidak ditemukan atau ID milik mitra lain
        header("Location: data_booking.php?pesan=tidak_ditemukan");
    }
} else {
    header("Location: data_booking.php");
}
?>