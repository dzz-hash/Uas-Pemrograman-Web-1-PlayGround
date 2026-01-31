<?php
include 'config/koneksi.php';


echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: "Poppins", sans-serif; }</style>
</head>
<body>';

if (isset($_POST['id_booking'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_booking']);
    $nama_penyewa = mysqli_real_escape_string($conn, $_POST['nama_penyewa']);
    
    // Informasi File
    $file_name = $_FILES['bukti']['name'];
    $file_tmp  = $_FILES['bukti']['tmp_name'];
    $file_size = $_FILES['bukti']['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // 1. Validasi Ekstensi
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    
    if (!in_array($file_ext, $allowed_ext)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Format Tidak Didukung',
                text: 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan.',
                confirmButtonColor: '#0d6efd'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 2. Validasi Ukuran (Contoh: Maksimal 2MB)
    if ($file_size > 2097152) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'File Terlalu Besar',
                text: 'Ukuran maksimal file adalah 2MB.',
                confirmButtonColor: '#0d6efd'
            }).then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 3. Penamaan File Unik
    $nama_baru = "BUKTI_" . $id . "_" . date('YmdHis') . "." . $file_ext;
    $target_path = "assets/bukti/" . $nama_baru;

    // Pastikan folder tujuan ada
    if (!is_dir('assets/bukti/')) {
        mkdir('assets/bukti/', 0777, true);
    }

    // 4. Proses Pindah File dan Update Database
    if (move_uploaded_file($file_tmp, $target_path)) {
        
        // Status diatur ke 'Proses' agar Admin tahu ada bukti masuk
        $sql = "UPDATE booking SET 
                bukti_bayar = '$nama_baru', 
                status_bayar = 'Proses' 
                WHERE id_booking = '$id'";
                
        if (mysqli_query($conn, $sql)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Terkirim!',
                    text: 'Terima kasih. Bukti pembayaran Anda sedang kami verifikasi.',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Oke'
                }).then(() => { 
                    window.location.href='cek_status.php?nama=" . urlencode($nama_penyewa) . "'; 
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Database',
                    text: 'Gagal memperbarui status di sistem.',
                }).then(() => { window.history.back(); });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Unggah',
                text: 'Terjadi kesalahan saat memindahkan file ke server.',
            }).then(() => { window.history.back(); });
        </script>";
    }
} else {
    header("Location: cek_status.php");
}

echo '</body></html>';
?>