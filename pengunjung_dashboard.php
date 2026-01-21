<?php 
session_start();
include 'config/koneksi.php'; 

// 1. TANGKAP ID MITRA DARI URL
if (isset($_GET['id_mitra'])) {
    $id_mitra = mysqli_real_escape_string($conn, $_GET['id_mitra']);
    $_SESSION['id_mitra_pilihan'] = $id_mitra; // Simpan ke session agar konsisten
} 

// 2. PROTEKSI: Jika tidak ada ID Mitra di URL maupun Session, kembalikan ke halaman awal
if (!isset($_SESSION['id_mitra_pilihan'])) {
    header("Location: index.php");
    exit;
}

$id_mitra_aktif = $_SESSION['id_mitra_pilihan'];

// 3. AMBIL INFO MITRA (Untuk Nama Cabang di Header)
$q_mitra_info = mysqli_query($conn, "SELECT * FROM mitra WHERE id_mitra = '$id_mitra_aktif'");
$mitra = mysqli_fetch_assoc($q_mitra_info);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking <?= $mitra['nama_mitra']; ?> | PlayGround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
        .navbar-custom { background: linear-gradient(135deg, #0d6efd, #0b5ed7); }
        .img-facility { height: 180px; object-fit: cover; border-radius: 15px 15px 0 0; }
        .card-facility { border: none; border-radius: 15px; transition: 0.3s; overflow: hidden; }
        .card-facility:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .sticky-form { position: sticky; top: 20px; }
        .price-tag { position: absolute; top: 10px; right: 10px; background: rgba(13, 110, 253, 0.9); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; z-index: 10; }
        .branch-info { background: white; border-radius: 15px; padding: 15px; margin-bottom: 25px; border-left: 5px solid #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="pengunjung_dashboard.php"><i class="fas fa-futbol me-2"></i> PLAYGROUND</a>
        <div class="ms-auto d-flex align-items-center">
            <a href="cek_status.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2">Cek Status</a>
            <button type="button" id="btn-keluar" class="btn btn-light btn-sm rounded-pill px-3 text-primary fw-bold">
                <i class="fas fa-sign-out-alt me-1"></i> Keluar
            </button>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-7">
            <div class="branch-info shadow-sm">
                <h4 class="fw-bold text-dark mb-1"><?= $mitra['nama_mitra']; ?></h4>
                <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?= $mitra['alamat']; ?></p>
            </div>

            <h5 class="fw-bold mb-3">Fasilitas Tersedia</h5>
            <div class="row g-3">
                <?php 
                $q = mysqli_query($conn, "SELECT * FROM fasilitas WHERE id_mitra = '$id_mitra_aktif' ORDER BY id_fasilitas DESC");
                
                if(mysqli_num_rows($q) > 0):
                    while($f = mysqli_fetch_array($q)):
                ?>
                <div class="col-md-6">
                    <div class="card card-facility shadow-sm h-100 position-relative">
                        <span class="price-tag fw-bold">Rp <?= number_format($f['harga_per_jam']); ?>/Jam</span>
                        <img src="assets/images/<?= $f['foto_fasilitas']; ?>" class="card-img-top img-facility" alt="Lapangan">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1"><?= $f['nama_fasilitas']; ?></h6>
                            <p class="text-muted small mb-0"><i class="fas fa-check-circle text-success me-1"></i> Tersedia untuk di-booking</p>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else:
                ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Maaf, belum ada fasilitas tersedia di cabang ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="sticky-form mt-4 mt-lg-0">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-edit me-2"></i>Isi Data Booking</h5>
                    
                    <form action="proses_booking.php" method="POST">
                        <input type="hidden" name="id_mitra" value="<?= $id_mitra_aktif; ?>">

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Pilih Lapangan</label>
                            <select name="id_fasilitas" class="form-select bg-light border-0 py-2" required>
                                <option value="">-- Pilih Fasilitas --</option>
                                <?php 
                                $q2 = mysqli_query($conn, "SELECT * FROM fasilitas WHERE id_mitra = '$id_mitra_aktif'");
                                while($f2 = mysqli_fetch_array($q2)) {
                                    echo "<option value='$f2[id_fasilitas]'>$f2[nama_fasilitas]</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control bg-light border-0 py-2" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Nomor WhatsApp</label>
                            <input type="number" name="telepon" class="form-control bg-light border-0 py-2" placeholder="08xxxxxxxx" required>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="small fw-bold mb-1">Tanggal Main</label>
                                <input type="date" name="tanggal" class="form-control bg-light border-0 py-2" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small fw-bold mb-1">Jam Mulai</label>
                                <input type="time" name="mulai" class="form-control bg-light border-0 py-2" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small fw-bold mb-1">Jam Selesai</label>
                                <input type="time" name="selesai" class="form-control bg-light border-0 py-2" required>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 mb-4" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                            <h6 class="small fw-bold text-dark mb-2"><i class="fas fa-info-circle me-1 text-primary"></i> Info Pembayaran:</h6>
                            <p class="small mb-0 text-muted">Anda memesan untuk mitra <strong><?= $mitra['nama_mitra']; ?></strong>. Rekening tujuan akan muncul setelah klik pesan.</p>
                        </div>
                        
                        <button type="submit" name="submit_booking" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                            PESAN SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('btn-keluar').addEventListener('click', function() {
        Swal.fire({
            title: 'Anda Yakin Ingin Keluar?',
            text: "Pilihan cabang Anda akan dikosongkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Mengarahkan ke index.php
                window.location.href = 'index.php';
            }
        });
    });
</script>

</body>
</html>