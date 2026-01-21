<?php
session_start();
include 'config/koneksi.php';

// Header standar untuk SweetAlert2
echo '<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: "Poppins", sans-serif; }</style>
</head>
<body>';

if (isset($_POST['submit_booking'])) {
    $id_fasilitas = mysqli_real_escape_string($conn, $_POST['id_fasilitas']);
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $tanggal = $_POST['tanggal'];
    $mulai   = $_POST['mulai'];
    $selesai = $_POST['selesai'];

    // 1. Ambil ID_MITRA dari tabel fasilitas berdasarkan id_fasilitas yang dipilih
    $get_mitra = mysqli_query($conn, "SELECT id_mitra FROM fasilitas WHERE id_fasilitas = '$id_fasilitas'");
    $data_mitra = mysqli_fetch_assoc($get_mitra);
    
    if (!$data_mitra) {
        echo "<script>
            Swal.fire('Error', 'Data lapangan tidak valid!', 'error').then(() => { window.history.back(); });
        </script>";
        exit;
    }
    $id_mitra = $data_mitra['id_mitra'];

    // 2. Validasi: Tanggal tidak boleh hari kemarin
    if ($tanggal < date('Y-m-d')) {
        echo "<script>
            Swal.fire('Gagal', 'Tidak bisa booking tanggal yang sudah lewat.', 'warning').then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 3. Validasi: Jam operasional (08:00 - 22:00)
    if ($mulai < "08:00" || $selesai > "22:00") {
        echo "<script>
            Swal.fire('Tutup', 'Jam operasional kami adalah pukul 08:00 - 22:00.', 'info').then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 4. Validasi: Jam selesai harus lebih besar dari jam mulai
    if ($selesai <= $mulai) {
        echo "<script>
            Swal.fire('Waktu Salah', 'Jam selesai harus lebih besar dari jam mulai.', 'warning').then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 5. Validasi: Minimal booking 1 jam
    $awal = strtotime($mulai);
    $akhir = strtotime($selesai);
    $durasi = ($akhir - $awal) / 3600;
    if ($durasi < 1) {
        echo "<script>
            Swal.fire('Durasi Kurang', 'Minimal durasi penyewaan adalah 1 jam.', 'warning').then(() => { window.history.back(); });
        </script>";
        exit;
    }

    // 6. Logika Anti-Bentrok (Hanya cek bentrok di fasilitas/lapangan yang sama)
    $query_cek = "SELECT * FROM booking 
                  WHERE id_fasilitas = '$id_fasilitas' 
                  AND tanggal_booking = '$tanggal'
                  AND ('$mulai' < jam_selesai AND '$selesai' > jam_mulai)";
    
    $hasil_cek = mysqli_query($conn, $query_cek);

    if (mysqli_num_rows($hasil_cek) > 0) {
        echo "<script>
            Swal.fire('Sudah Terisi', 'Maaf! Lapangan sudah terisi pada jam tersebut. Silakan pilih waktu lain.', 'error').then(() => { window.history.back(); });
        </script>";
    } else {
        // 7. Insert Data Booking dengan ID_MITRA yang sesuai
        $query = "INSERT INTO booking (id_mitra, id_fasilitas, nama_penyewa, telepon, tanggal_booking, jam_mulai, jam_selesai, status_bayar) 
                  VALUES ('$id_mitra', '$id_fasilitas', '$nama', '$telepon', '$tanggal', '$mulai', '$selesai', 'Pending')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Booking!',
                    text: 'Pesanan Anda telah masuk ke sistem. Silakan cek status untuk detail pembayaran.',
                    confirmButtonColor: '#0d6efd'
                }).then(() => { 
                    window.location.href='cek_status.php?nama=" . urlencode($nama) . "'; 
                });
            </script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
echo '</body></html>';
?>