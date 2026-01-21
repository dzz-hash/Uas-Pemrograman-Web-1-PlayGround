<?php
session_start();

// 1. SINKRONISASI SESSION (Ubah 'admin' menjadi 'id_mitra')
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';
include '../includes/header_admin.php';

$id_mitra = $_SESSION['id_mitra'];
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. KEAMANAN: Pastikan mitra hanya bisa mengedit booking milik cabangnya sendiri
$query = "SELECT booking.*, fasilitas.nama_fasilitas 
          FROM booking 
          JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas 
          WHERE booking.id_booking = '$id' AND booking.id_mitra = '$id_mitra'";

$data = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($data);

if (!$row) { 
    echo "<div class='container mt-5'><div class='alert alert-danger'>Data tidak ditemukan atau Anda tidak memiliki akses ke data ini.</div></div>"; 
    exit; 
}

if (isset($_POST['update'])) {
    $status = $_POST['status_bayar'];
    $update = mysqli_query($conn, "UPDATE booking SET status_bayar = '$status' WHERE id_booking = '$id' AND id_mitra = '$id_mitra'");

    if ($update) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>"; // Pastikan library ada
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Status pembayaran telah diperbarui.',
                    icon: 'success'
                }).then(() => { window.location.href='data_booking.php'; });
            }, 100);
        </script>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> 
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Verifikasi Pembayaran</h4>
                        <a href="data_booking.php" class="btn-close"></a>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold d-block mb-3 text-muted text-uppercase small">Bukti Pembayaran</label>
                            <?php if (!empty($row['bukti_bayar'])): ?>
                                <div class="position-relative group">
                                    <a href="../assets/bukti/<?= $row['bukti_bayar'] ?>" target="_blank">
                                        <img src="../assets/bukti/<?= $row['bukti_bayar'] ?>" class="img-fluid rounded shadow-sm border" style="max-height: 400px; width: 100%; object-fit: contain; background: #f8f9fa;">
                                        <div class="text-center mt-2 small text-primary"><i class="fas fa-search-plus"></i> Klik untuk perbesar</div>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border text-center py-5">
                                    <i class="fas fa-image fa-3x mb-3 text-muted opacity-25"></i>
                                    <p class="mb-0 text-muted">Customer belum mengunggah bukti pembayaran.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Penyewa</label>
                                    <div class="h6 fw-bold"><?= htmlspecialchars($row['nama_penyewa']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($row['nama_fasilitas']) ?></div>
                                </div>
                                <hr class="opacity-10">
                                <div class="mb-3 text-muted">
                                    <small><i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($row['tanggal_booking'])) ?></small><br>
                                    <small><i class="fas fa-clock me-1"></i> <?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></small>
                                </div>

                                <div class="mb-4 bg-light p-3 rounded border border-primary border-opacity-25">
                                    <label class="form-label fw-bold text-primary">Status Pembayaran</label>
                            

                                <select name="status_bayar" class="form-select border-primary">
                                    <option value="Pending" <?= ($row['status_bayar'] == 'Pending' || $row['status_bayar'] == 'Proses') ? 'selected' : '' ?>>
                                        🟡 Pending / Menunggu Verifikasi
                                    </option>
                                    
                                    <option value="Lunas" <?= ($row['status_bayar'] == 'Lunas') ? 'selected' : '' ?>>
                                        🟢 Lunas (Konfirmasi Pembayaran)
                                    </option>
                                </select>
                                    <div class="form-text small">Pilih <b>Lunas</b> jika uang sudah masuk ke rekening Anda.</div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="update" class="btn btn-primary rounded-pill py-2">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                    <a href="data_booking.php" class="btn btn-light rounded-pill text-muted">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>