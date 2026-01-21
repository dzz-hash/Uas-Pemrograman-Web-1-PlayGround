<?php
session_start();
// Pastikan hanya mitra yang sudah login bisa akses
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';
include '../includes/header_admin.php';

$id_mitra = $_SESSION['id_mitra'];
$pesan = "";

// Logika Update Profil
if (isset($_POST['update_profil'])) {
    $nama_mitra = mysqli_real_escape_string($conn, $_POST['nama_mitra']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rekening   = mysqli_real_escape_string($conn, $_POST['rekening']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $password   = $_POST['password'];

    // Update dasar
    $query = "UPDATE mitra SET 
                nama_mitra = '$nama_mitra', 
                alamat = '$alamat', 
                rekening = '$rekening', 
                username = '$username' 
              WHERE id_mitra = '$id_mitra'";
    
    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success'>Profil berhasil diperbarui!</div>";
        
        // Jika password diisi, maka update password juga
        if (!empty($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE mitra SET password = '$pass_hash' WHERE id_mitra = '$id_mitra'");
        }
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui profil.</div>";
    }
}

// Ambil data terbaru dari database
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mitra WHERE id_mitra = '$id_mitra'"));
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i>Pengaturan Profil Cabang</h5>
                </div>
                <div class="card-body p-4">
                    <?= $pesan; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1">NAMA CABANG / MITRA</label>
                                <input type="text" name="nama_mitra" class="form-control bg-light" value="<?= $data['nama_mitra']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1">USERNAME LOGIN</label>
                                <input type="text" name="username" class="form-control bg-light" value="<?= $data['username']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">ALAMAT CABANG</label>
                            <textarea name="alamat" class="form-control bg-light" rows="2" required><?= $data['alamat']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1 text-primary">INFORMASI REKENING (Tampil di Pengunjung)</label>
                            <textarea name="rekening" class="form-control bg-light border-primary" rows="3" placeholder="Contoh: BANK BCA 123456789 a/n Nama Pemilik" required><?= $data['rekening']; ?></textarea>
                            <div class="form-text mt-1 small">Informasi ini akan muncul saat pengunjung mengecek status booking mereka.</div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <label class="small fw-bold mb-1 text-danger">GANTI PASSWORD (Kosongkan jika tidak ingin ganti)</label>
                            <input type="password" name="password" class="form-control bg-light" placeholder="Masukkan password baru">
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="update_profil" class="btn btn-primary py-2 fw-bold">
                                <i class="fas fa-save me-2"></i>SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>