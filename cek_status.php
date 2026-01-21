<?php 
include 'config/koneksi.php'; 

// Ambil input pencarian nama dari URL
$nama_cari = isset($_GET['nama']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Booking | PlayGround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
        .card-status { border: none; border-radius: 15px; }
        .status-badge { font-size: 0.75rem; padding: 5px 12px; border-radius: 20px; font-weight: 600; }
        .search-box { border-radius: 10px; border: 2px solid #eee; transition: 0.3s; }
        .search-box:focus { border-color: #0d6efd; box-shadow: none; }
        .table thead th { border: none; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="pengunjung_dashboard.php"><i class="fas fa-arrow-left me-2"></i> KEMBALI</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card card-status shadow-sm p-4 mb-4">
                <div class="mb-4">
                    <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-search me-2 text-primary"></i>Riwayat Reservasi</h5>
                    <p class="text-muted small">Cari data booking berdasarkan nama yang Anda daftarkan.</p>
                </div>
                
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-8 col-md-10">
                        <input type="text" name="nama" class="form-control search-box py-2" placeholder="Masukkan Nama Lengkap Anda..." value="<?= htmlspecialchars($nama_cari) ?>" required>
                    </div>
                    <div class="col-4 col-md-2">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">CARI</button>
                    </div>
                </form>

                <?php 
                if ($nama_cari != ""):
                    // Query JOIN 3 Tabel: Booking, Fasilitas, dan Mitra
                    $query = "SELECT booking.*, fasilitas.nama_fasilitas, mitra.nama_mitra, mitra.rekening 
                              FROM booking 
                              JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas 
                              JOIN mitra ON booking.id_mitra = mitra.id_mitra 
                              WHERE booking.nama_penyewa LIKE '%$nama_cari%' 
                              ORDER BY booking.id_booking DESC";
                    
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Informasi Penyewa</th>
                                <th>Jadwal & Fasilitas</th>
                                <th>Pembayaran Cabang</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($d = mysqli_fetch_array($result)): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold d-block text-dark"><?= $d['nama_penyewa']; ?></span>
                                    <small class="text-muted"><i class="fab fa-whatsapp me-1"></i><?= $d['telepon']; ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary mb-1"><?= $d['nama_fasilitas']; ?></span>
                                    <small class="text-muted d-block">
                                        <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($d['tanggal_booking'])); ?><br>
                                        <i class="far fa-clock me-1"></i> <?= $d['jam_mulai']; ?> - <?= $d['jam_selesai']; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block mb-1"><?= $d['nama_mitra']; ?></span>
                                    <?php if($d['status_bayar'] == 'Pending'): ?>
                                        <div class="small p-2 bg-light border rounded text-primary" style="font-size: 0.7rem; line-height: 1.4;">
                                            <i class="fas fa-university me-1"></i><strong>Transfer ke:</strong><br><?= nl2br($d['rekening']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($d['status_bayar'] == 'Pending'): ?>
                                        <span class="status-badge bg-warning text-dark border"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php elseif($d['status_bayar'] == 'Proses'): ?>
                                        <span class="status-badge bg-info text-white"><i class="fas fa-spinner fa-spin me-1"></i>Verifikasi</span>
                                    <?php else: ?>
                                        <span class="status-badge bg-success text-white"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($d['status_bayar'] == 'Pending'): ?>
                                        <a href="upload_bukti.php?id=<?= $d['id_booking']; ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-upload me-1"></i> Upload Bukti
                                        </a>
                                    <?php elseif($d['status_bayar'] == 'Proses'): ?>
                                        <span class="text-muted small fw-medium">Menunggu Konfirmasi</span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold small"><i class="fas fa-check-double me-1"></i> Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search-minus fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="text-muted">Nama "<strong><?= htmlspecialchars($nama_cari) ?></strong>" tidak ditemukan.</h6>
                        <p class="text-muted small">Pastikan ejaan nama sesuai dengan saat melakukan booking.</p>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>