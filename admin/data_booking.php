<?php
session_start();
// 1. PROTEKSI LOGIN MITRA
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';
include '../includes/header_admin.php';

$id_mitra = $_SESSION['id_mitra'];
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

// 2. LOGIKA VERIFIKASI CEPAT (Tanpa Masuk Menu Edit)
if (isset($_GET['verifikasi'])) {
    $id_v = mysqli_real_escape_string($conn, $_GET['verifikasi']);
    mysqli_query($conn, "UPDATE booking SET status_bayar = 'Lunas' WHERE id_booking = '$id_v' AND id_mitra = '$id_mitra'");
    echo "<script>window.location.href='data_booking.php?pesan=verifikasi_berhasil';</script>";
}

// Statistik Ringkas
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra'"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE status_bayar='Pending' AND id_mitra = '$id_mitra'"));
$lunas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE status_bayar='Lunas' AND id_mitra = '$id_mitra'"));
?>

<?php if (isset($_GET['pesan'])): ?>
    <script>
        const pesan = "<?= $_GET['pesan'] ?>";
        if (pesan === 'hapus_berhasil') {
            Swal.fire('Terhapus!', 'Data booking berhasil dibuang.', 'success');
        } else if (pesan === 'verifikasi_berhasil') {
            Swal.fire('Lunas!', 'Status pembayaran telah diperbarui.', 'success');
        } else if (pesan === 'gagal') {
            Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
        }
    </script>
<?php endif; ?>

<style>
    @media print {
        .navbar, .btn, .btn-group, .card-body form, .aksi-column, #logoutBtn, .ms-auto, .no-print, .swal2-container {
            display: none !important;
        }
        .container { width: 100% !important; max-width: 100% !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { width: 100% !important; border: 1px solid #000 !important; }
        body { background: white !important; }
        body::before {
            content: "LAPORAN DATA BOOKING - <?= $_SESSION['nama_mitra'] ?>";
            display: block; text-align: center; font-size: 16pt; font-weight: bold; margin-bottom: 20px;
        }
    }
    .img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; cursor: pointer; }
    .status-badge { font-size: 0.75rem; padding: 5px 12px; }
</style>

<div class="container py-4">
    <div class="row mb-4 align-items-end no-print">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-receipt me-2 text-primary"></i>Data Booking</h3>
            <p class="text-muted small mb-0">Cabang: <strong><?= $_SESSION['nama_mitra'] ?></strong></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2">
                <div class="bg-white px-3 py-2 rounded-3 shadow-sm border-start border-primary border-4">
                    <small class="text-muted d-block text-start">Total</small>
                    <span class="fw-bold"><?= $total ?></span>
                </div>
                <div class="bg-white px-3 py-2 rounded-3 shadow-sm border-start border-warning border-4">
                    <small class="text-muted d-block text-warning text-start">Pending</small>
                    <span class="fw-bold text-warning"><?= $pending ?></span>
                </div>
                <div class="bg-white px-3 py-2 rounded-3 shadow-sm border-start border-success border-4">
                    <small class="text-muted d-block text-success text-start">Lunas</small>
                    <span class="fw-bold text-success"><?= $lunas ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-7">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="cari" class="form-control rounded-pill bg-light border-0 px-3" 
                               placeholder="Cari nama penyewa..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                    </form>
                </div>
                <div class="col-md-5 text-md-end">
                    <button onclick="eksporExcel()" class="btn btn-outline-success rounded-pill me-2">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button onclick="window.print()" class="btn btn-dark rounded-pill">
                        <i class="fas fa-print me-1"></i> Cetak PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table id="tabelBooking" class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold">Penyewa</th>
                        <th class="py-3 small fw-bold">Jadwal Main</th>
                        <th class="py-3 small fw-bold">Lapangan</th>
                        <th class="py-3 small fw-bold text-center">Bukti</th>
                        <th class="py-3 small fw-bold">Total</th>
                        <th class="py-3 small fw-bold text-center">Status</th>
                        <th class="py-3 small fw-bold text-center pe-4 aksi-column">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT booking.*, fasilitas.nama_fasilitas, fasilitas.harga_per_jam 
                            FROM booking 
                            JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas 
                            WHERE booking.id_mitra = '$id_mitra'";
                    
                    if ($cari != '') { $sql .= " AND booking.nama_penyewa LIKE '%$cari%'"; }
                    $sql .= " ORDER BY booking.id_booking DESC";
                    
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            // Hitung Durasi
                            $mulai = new DateTime($row['jam_mulai']);
                            $selesai = new DateTime($row['jam_selesai']);
                            $durasi = $mulai->diff($selesai)->h ?: 1;
                            $total_bayar = $row['harga_per_jam'] * $durasi;
                            
                            $is_lunas = ($row['status_bayar'] == 'Lunas');
                            $status_class = $is_lunas ? 'bg-success' : 'bg-warning text-dark';
                            $path_bukti = "../assets/bukti/" . $row['bukti_bayar'];
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $row['nama_penyewa'] ?></div>
                                    <small class="text-muted"><?= $row['no_telepon'] ?? '-' ?></small>
                                </td>
                                <td>
                                    <div class="small fw-medium"><?= date('d/m/Y', strtotime($row['tanggal_booking'])) ?></div>
                                    <div class="text-muted small"><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary border-0"><?= $row['nama_fasilitas'] ?></span></td>
                                <td class="text-center">
                                    <?php if (!empty($row['bukti_bayar']) && file_exists($path_bukti)): ?>
                                        <img src="<?= $path_bukti ?>" class="img-preview shadow-sm" onclick="lihatFoto('<?= $path_bukti ?>')">
                                    <?php else: ?>
                                        <span class="text-muted small italic">Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
                                    <small class="text-muted"><?= $durasi ?> Jam</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $status_class ?> status-badge">
                                        <?= $row['status_bayar'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4 aksi-column">
                                    <div class="btn-group border rounded shadow-sm">
                                        <?php if(!$is_lunas): ?>
                                            <a href="data_booking.php?verifikasi=<?= $row['id_booking'] ?>" class="btn btn-sm btn-white text-success" title="Set Lunas">
                                                <i class="fas fa-check-double"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="edit_booking.php?id=<?= $row['id_booking'] ?>" class="btn btn-sm btn-white text-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="hapusBooking(<?= $row['id_booking'] ?>)" class="btn btn-sm btn-white text-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Tidak ada data ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Fungsi Lihat Foto Bukti
function lihatFoto(url) {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Bukti Pembayaran',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#0d6efd'
    });
}

// Fungsi Konfirmasi Hapus
function hapusBooking(id) {
    Swal.fire({
        title: 'Hapus Pesanan?',
        text: "Data ini akan hilang selamanya!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) { 
            window.location.href = 'proses_hapus_booking.php?id=' + id; 
        }
    })
}

// Fungsi Ekspor Excel Sederhana
function eksporExcel() {
    let filename = 'Laporan_Booking_<?= date('d-m-Y') ?>.xls';
    let content = document.getElementById("tabelBooking").outerHTML;
    let blob = new Blob(['\ufeff', content], { type: 'application/vnd.ms-excel' });
    let url = URL.createObjectURL(blob);
    let link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
}
</script>

<?php include '../includes/footer.php'; ?>