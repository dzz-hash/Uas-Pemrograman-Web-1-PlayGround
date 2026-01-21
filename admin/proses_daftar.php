<?php
include '../config/koneksi.php';

if (isset($_POST['register'])) {
    $nama_mitra = mysqli_real_escape_string($conn, $_POST['nama_mitra']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi Keamanan
    $kontak     = mysqli_real_escape_string($conn, $_POST['kontak']);

    // Cek duplikasi username
    $cek = mysqli_query($conn, "SELECT * FROM mitra WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah terpakai cabang lain!'); window.history.back();</script>";
    } else {
        // Insert data (status_aktif default Y agar bisa langsung login)
        $insert = mysqli_query($conn, "INSERT INTO mitra (nama_mitra, alamat, kontak, username, password, status_aktif) 
                                       VALUES ('$nama_mitra', '$alamat', '$kontak', '$username', '$password', 'Y')");
        
        if ($insert) {
            echo "<script>alert('Cabang $nama_mitra berhasil didaftarkan!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar!'); window.history.back();</script>";
        }
    }
}
?>