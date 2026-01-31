<?php
session_start();
// 1. Cek Login
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

// 2. Koneksi Database
include '../config/koneksi.php';

$id_mitra = $_SESSION['id_mitra'];

// 3. Logika Update Status (Pengganti Fitur Hapus)
if (isset($_GET['ubah_status'])) {
    $id = mysqli_real_escape_string($conn, $_GET['ubah_status']);
    $status_baru = mysqli_real_escape_string($conn, $_GET['ke']);
    
    $query = "UPDATE fasilitas SET status_lapangan = '$status_baru' 
              WHERE id_fasilitas = '$id' AND id_mitra = '$id_mitra'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: data_fasilitas.php");
        exit;
    }
}

// 4. Logika Tambah Fasilitas
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $status = mysqli_real_escape_string($conn, $_POST['status_lapangan']);
    
    $foto_name = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];
    $ekstensi  = pathinfo($foto_name, PATHINFO_EXTENSION);
    
    $foto_baru = "lapangan_" . time() . "." . $ekstensi;
    $path      = "../assets/images/" . $foto_baru;

    if (move_uploaded_file($tmp_name, $path)) {
        mysqli_query($conn, "INSERT INTO fasilitas (id_mitra, nama_fasilitas, harga_per_jam, foto_fasilitas, status_lapangan) 
                             VALUES ('$id_mitra', '$nama', '$harga', '$foto_baru', '$status')");
        header("Location: data_fasilitas.php");
        exit;
    }
}

// 5. Baru Panggil Header (Setelah tidak ada lagi proses redirect)
include '../includes/header_admin.php';
?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Lapangan</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">NAMA LAPANGAN</label>
                        <input type="text" name="nama" class="form-control bg-light border-0" placeholder="Contoh: Lapangan A" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">HARGA / JAM (RP)</label>
                        <input type="number" name="harga" class="form-control bg-light border-0" placeholder="100000" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">STATUS AWAL</label>
                        <select name="status_lapangan" class="form-select bg-light border-0">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Renovasi">Sedang Renovasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">FOTO LAPANGAN</label>
                        <input type="file" name="foto" class="form-control bg-light border-0" accept="image/*" required>
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
                    <h6 class="fw-bold mb-0"><i class="fas fa-table me-2"></i>Manajemen Status Lapangan</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-dark text-center border-0">
                            <tr>
                                <th>Foto</th>
                                <th>Nama Lapangan</th>
                                <th>Harga / Jam</th>
                                <th>Status & Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT * FROM fasilitas WHERE id_mitra = '$id_mitra' ORDER BY id_fasilitas DESC"); 
                            while($d = mysqli_fetch_array($q)): 
                                $is_tersedia = ($d['status_lapangan'] == 'Tersedia');
                                $badge_color = $is_tersedia ? 'bg-success' : 'bg-danger';
                            ?>
                            <tr>
                                <td class="text-center">
                                    <img src="../assets/images/<?= $d['foto_fasilitas']; ?>" class="rounded-3 shadow-sm" style="width: 80px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= $d['nama_fasilitas']; ?></span>
                                    <span class="badge <?= $badge_color; ?> rounded-pill" style="font-size: 0.7rem;">
                                        <?= $d['status_lapangan']; ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    Rp <?= number_format($d['harga_per_jam'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <?php if($is_tersedia): ?>
                                        <a href="data_fasilitas.php?ubah_status=<?= $d['id_fasilitas']; ?>&ke=Renovasi" 
                                           class="btn btn-sm btn-outline-warning px-3 rounded-pill fw-bold">
                                            <i class="fas fa-tools me-1"></i> Set Renovasi
                                        </a>
                                    <?php else: ?>
                                        <a href="data_fasilitas.php?ubah_status=<?= $d['id_fasilitas']; ?>&ke=Tersedia" 
                                           class="btn btn-sm btn-outline-success px-3 rounded-pill fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Set Tersedia
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if(mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data lapangan.</td>
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