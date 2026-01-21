<?php
session_start();
// PERBAIKAN: Gunakan id_mitra sesuai sistem login baru
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';
include '../includes/header_admin.php';

$id_mitra = $_SESSION['id_mitra'];

// Logika Tambah Fasilitas dengan Upload Foto
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    
    // Proses File Gambar
    $foto_name = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
    
    // Validasi Ekstensi
    $ekstensi_boleh = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($ekstensi), $ekstensi_boleh)) {
        echo "<script>alert('Format file tidak didukung!'); window.location='data_fasilitas.php';</script>";
        exit;
    }

    // Beri nama unik untuk file agar tidak bentrok
    $foto_baru = "lapangan_" . time() . "." . $ekstensi;
    $path      = "../assets/images/" . $foto_baru;

    if (move_uploaded_file($tmp_name, $path)) {
        // PERBAIKAN: Masukkan id_mitra saat INSERT
        mysqli_query($conn, "INSERT INTO fasilitas (id_mitra, nama_fasilitas, harga_per_jam, foto_fasilitas) 
                             VALUES ('$id_mitra', '$nama', '$harga', '$foto_baru')");
        echo "<script>window.location='data_fasilitas.php';</script>";
    } else {
        echo "<script>alert('Gagal mengunggah gambar!');</script>";
    }
}

// Logika Hapus Fasilitas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // PERBAIKAN: Pastikan hanya bisa menghapus jika id_mitra cocok (keamanan)
    $cek = mysqli_query($conn, "SELECT foto_fasilitas FROM fasilitas WHERE id_fasilitas='$id' AND id_mitra='$id_mitra'");
    $data = mysqli_fetch_array($cek);
    
    if ($data) {
        // Hapus file fisik jika bukan gambar default
        if($data['foto_fasilitas'] != 'default.jpg' && file_exists("../assets/images/" . $data['foto_fasilitas'])) {
            unlink("../assets/images/" . $data['foto_fasilitas']);
        }
        
        mysqli_query($conn, "DELETE FROM fasilitas WHERE id_fasilitas='$id' AND id_mitra='$id_mitra'");
    }
    header("Location: data_fasilitas.php");
    exit;
}
?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Lapangan</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">NAMA LAPANGAN</label>
                        <input type="text" name="nama" class="form-control bg-light border-0" placeholder="Contoh: Lapangan Futsal A" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">HARGA / JAM (RP)</label>
                        <input type="number" name="harga" class="form-control bg-light border-0" placeholder="100000" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">FOTO LAPANGAN</label>
                        <input type="file" name="foto" id="fotoInput" class="form-control bg-light border-0" accept="image/*" required onchange="previewImage()">
                        <div class="form-text mt-1 small text-muted">Format: JPG, PNG, WEBP.</div>
                        <img id="imgPreview" class="mt-2 img-fluid rounded-3 d-none" style="max-height: 150px; border: 2px dashed #ddd; padding: 5px; width: 100%; object-fit: cover;">
                    </div>
                    <button name="tambah" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i>SIMPAN DATA
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="fas fa-table me-2"></i>Daftar Lapangan Cabang Anda</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-dark text-center border-0">
                            <tr>
                                <th>Foto</th>
                                <th>Nama Lapangan</th>
                                <th>Harga / Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // PERBAIKAN: Hanya tampilkan fasilitas milik id_mitra yang login
                            $q = mysqli_query($conn, "SELECT * FROM fasilitas WHERE id_mitra = '$id_mitra' ORDER BY id_fasilitas DESC"); 
                            while($d = mysqli_fetch_array($q)): 
                            ?>
                            <tr>
                                <td class="text-center">
                                    <a href="../assets/images/<?= $d['foto_fasilitas']; ?>" target="_blank">
                                        <img src="../assets/images/<?= $d['foto_fasilitas']; ?>" class="rounded-3 shadow-sm" style="width: 80px; height: 50px; object-fit: cover;">
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= $d['nama_fasilitas']; ?></span>
                                    <small class="text-muted">ID: #<?= $d['id_fasilitas']; ?></small>
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    Rp <?= number_format($d['harga_per_jam'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" 
                                       class="btn btn-sm btn-outline-danger px-3 rounded-pill btn-hapus-fasilitas" 
                                       data-id="<?= $d['id_fasilitas']; ?>">
                                         <i class="fas fa-trash me-1"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted small">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                                    Belum ada data lapangan di cabang ini.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Preview Gambar sebelum upload
function previewImage() {
    const input = document.getElementById('fotoInput');
    const preview = document.getElementById('imgPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Konfirmasi Hapus SweetAlert2
document.querySelectorAll('.btn-hapus-fasilitas').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        Swal.fire({
            title: 'Hapus Lapangan?',
            text: "Data lapangan dan file foto akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'data_fasilitas.php?hapus=' + id;
            }
        })
    });
});
</script>