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
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

// Statistik Ringkas (Hanya untuk mitra yang login)
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra'"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE status_bayar='Pending' AND id_mitra = '$id_mitra'"));
$lunas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE status_bayar='Lunas' AND id_mitra = '$id_mitra'"));
?>

<style>
    /* CSS Khusus Mode Cetak (PDF) */
    @media print {
        .navbar, .btn, .btn-group, .card-body form, .aksi-column, #logoutBtn, .ms-auto, .no-print, .swal2-container {
            display: none !important;
        }
        .container { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { width: 100% !important; border: 1px solid #dee2e6 !important; }
        body { background: white !important; -webkit-print-color-adjust: exact; }
        body::before {
            content: "LAPORAN DATA BOOKING - <?= $_SESSION['nama_mitra'] ?>";
            display: block; text-align: center; font-size: 18pt; font-weight: bold; margin-bottom: 10px;
        }
    }
    
    .table-hover tbody tr:hover { background-color: rgba(26, 42, 108, 0.03); }
    .img-preview { width: 50px; height: 50px; object-fit: cover; transition: 0.2s; border-radius: 8px; }
    .img-preview:hover { transform: scale(1.1); }
</style>

<div class="container py-4">
    <div class="row mb-4 align-items-end no-print">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-receipt me-2 text-primary"></i>Manajemen Booking</h3>
            <p class="text-muted small mb-0">Kelola reservasi untuk cabang <strong><?= $_SESSION['nama_mitra'] ?></strong>.</p>
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
                        <input type="text" name="cari" class="form-control rounded-pill border-light bg-light px-3" 
                               placeholder="Cari nama penyewa..." value="<?= htmlspecialchars($cari) ?>">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                        <?php if($cari != ''): ?>
                            <a href="data_booking.php" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                        <button onclick="eksporExcel()" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </button>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table id="tabelBooking" class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Penyewa</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Jadwal</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Fasilitas</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center">Bukti</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Total Bayar</th>
                        <th class="py-3 text-uppercase small fw-bold text-center text-muted">Status</th>
                        <th class="py-3 text-uppercase small fw-bold text-center pe-4 text-muted aksi-column">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // PERBAIKAN: Query difilter berdasarkan id_mitra
                    $sql = "SELECT booking.*, fasilitas.nama_fasilitas, fasilitas.harga_per_jam 
                            FROM booking 
                            JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas 
                            WHERE booking.id_mitra = '$id_mitra'";
                    
                    if ($cari != '') {
                        $sql .= " AND booking.nama_penyewa LIKE '%$cari%'";
                    }
                    $sql .= " ORDER BY booking.id_booking DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $mulai = new DateTime($row['jam_mulai']);
                            $selesai = new DateTime($row['jam_selesai']);
                            $durasi = $mulai->diff($selesai)->h ?: 1;
                            $total_bayar = $row['harga_per_jam'] * $durasi;
                            $status_class = ($row['status_bayar'] == 'Lunas') ? 'bg-success' : 'bg-warning text-dark';
                            
                            $path_bukti = "../assets/bukti/" . $row['bukti_bayar'];
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $row['nama_penyewa'] ?></div>
                                    <small class="text-muted"><?= $row['no_telepon'] ?? '-' ?></small>
                                </td>
                                <td class="small">
                                    <div class="fw-medium"><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></div>
                                    <div class="text-muted"><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></div>
                                </td>
                                <td><span class="badge bg-info-subtle text-info border border-info-subtle"><?= $row['nama_fasilitas'] ?></span></td>
                                <td class="text-center">
                                    <?php if (!empty($row['bukti_bayar']) && file_exists($path_bukti)): ?>
                                        <a href="<?= $path_bukti ?>" target="_blank">
                                            <img src="<?= $path_bukti ?>" class="img-preview border shadow-sm">
                                        </a>
                                    <?php else: ?>
                                        <small class="text-muted opacity-50 italic">No File</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
                                    <small class="text-muted" style="font-size:0.7rem"><?= $durasi ?> Jam</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $status_class ?> px-3 py-2">
                                        <?= $row['status_bayar'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4 aksi-column">
                                    <div class="btn-group shadow-sm rounded border">
                                        <a href="edit_booking.php?id=<?= $row['id_booking'] ?>" class="btn btn-sm btn-white text-secondary" title="Verifikasi">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="hapusBooking(<?= $row['id_booking'] ?>)" class="btn btn-sm btn-white text-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Belum ada data booking untuk cabang Anda.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function eksporExcel() {
    let tabel = document.getElementById("tabelBooking");
    let klonTabel = tabel.cloneNode(true); 
    
    // 1. Hapus kolom aksi agar tidak ikut ter-ekspor
    klonTabel.querySelectorAll('.aksi-column').forEach(el => el.remove());

    // 2. Ubah gambar bukti bayar menjadi Link Teks
    klonTabel.querySelectorAll('td').forEach((td) => {
        let img = td.querySelector('img');
        if (img) {
            // Ambil URL lengkap gambar
            let fullUrl = img.src; 
            // Ubah isi TD menjadi Link yang bisa diklik di Excel
            td.innerHTML = `<a href="${fullUrl}">Lihat Bukti Foto</a>`;
        } else if (td.innerText.trim() === "No File") {
            td.innerHTML = "Belum Upload";
        }
    });

    // 3. Template HTML untuk Excel
    let excelTemplate = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8">
            <style>
                table { border-collapse: collapse; }
                th { background-color: #1a2a6c; color: #ffffff; border: 1px solid #000000; text-align: center; padding: 10px; }
                td { border: 1px solid #000000; padding: 8px; vertical-align: middle; }
                .title { font-size: 16pt; font-weight: bold; text-align: center; }
                a { color: #0d6efd; text-decoration: underline; }
            </style>
        </head>
        <body>
            <table>
                <tr><td colspan="6" class="title">LAPORAN DATA BOOKING - <?= $_SESSION['nama_mitra'] ?></td></tr>
                <tr><td colspan="6" style="text-align:center">Dicetak pada: <?= date('d/m/Y H:i') ?></td></tr>
                <tr><td colspan="6"></td></tr>
            </table>
            ${klonTabel.outerHTML}
        </body>
        </html>
    `;

    let blob = new Blob([excelTemplate], { type: 'application/vnd.ms-excel' });
    let url = URL.createObjectURL(blob);
    let link = document.createElement('a');
    link.href = url;
    link.download = 'Laporan_Booking_<?= $_SESSION['nama_mitra'] ?>_<?= date('d-m-Y') ?>.xls';
    link.click();
}

function hapusBooking(id) {
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data permanen tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#1a2a6c',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) { 
            window.location.href = 'proses_hapus_booking.php?id=' + id; 
        }
    })
}
</script>

<?php include '../includes/footer.php'; ?>