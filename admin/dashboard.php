<?php
session_start();
// PERBAIKAN: Cek session id_mitra sesuai dengan yang dibuat di login.php
if (!isset($_SESSION['id_mitra'])) { 
    header("Location: login.php"); 
    exit; 
}

include '../config/koneksi.php';
// Pastikan file header_admin.php ada di folder includes
include '../includes/header_admin.php';

$id_mitra = $_SESSION['id_mitra'];
$nama_mitra = $_SESSION['nama_mitra'];

// Ambil data statistik KHUSUS untuk mitra yang sedang login
$total_booking = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra'"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra' AND status_bayar='Pending'"));
$lunas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra' AND status_bayar='Lunas'"));
?>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
        background-size: 400% 400%;
        animation: gradientAnimation 15s ease infinite;
        color: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .welcome-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 9rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }

    .card-stat {
        border: none;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    
    .border-blue { border-left: 5px solid #1a2a6c !important; }
    .border-maroon { border-left: 5px solid #b21f1f !important; }
    .border-gold { border-left: 5px solid #fdbb2d !important; }

    .stat-icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
    }

    @keyframes gradientAnimation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>

<div class="container py-2">
    <div class="welcome-banner shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold">Halo, <?= $nama_mitra; ?>! 👋</h2>
                
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <h3 id="clock" class="fw-bold mb-0">00:00:00</h3>
                <span class="small opacity-75"><?= date('l, d F Y'); ?></span>
            </div>
        </div>
        <i class="fas fa-shield-alt welcome-icon"></i>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-stat border-blue shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Pesanan</h6>
                        <h2 class="fw-bold mb-0" style="color: #1a2a6c;"><?= $total_booking; ?></h2>
                    </div>
                    <div class="stat-icon-box" style="background: rgba(26, 42, 108, 0.1); color: #1a2a6c;">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-maroon shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Menunggu Konfirmasi</h6>
                        <h2 class="fw-bold mb-0" style="color: #b21f1f;"><?= $pending; ?></h2>
                    </div>
                    <div class="stat-icon-box" style="background: rgba(178, 31, 31, 0.1); color: #b21f1f;">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-gold shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Sudah Lunas</h6>
                        <h2 class="fw-bold mb-0" style="color: #fdbb2d;"><?= $lunas; ?></h2>
                    </div>
                    <div class="stat-icon-box" style="background: rgba(253, 187, 45, 0.1); color: #fdbb2d;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <h5 class="fw-bold mb-4"><i class="fas fa-chart-bar me-2 text-primary"></i>Statistik Per Lapangan</h5>
                <div style="height: 300px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <h5 class="fw-bold mb-4"><i class="fas fa-chart-pie me-2 text-danger"></i>Status Bayar</h5>
                <div style="height: 250px;">
                    <canvas id="doughnutChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Distribusi Pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0">Log Booking Terbaru (<?= $nama_mitra ?>)</h5>
                    <a href="data_booking.php" class="btn btn-sm btn-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Penyewa</th>
                                <th>Tanggal Booking</th>
                                <th>Status Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent = mysqli_query($conn, "SELECT * FROM booking WHERE id_mitra = '$id_mitra' ORDER BY id_booking DESC LIMIT 5");
                            if(mysqli_num_rows($recent) > 0):
                                while($r = mysqli_fetch_array($recent)):
                            ?>
                            <tr>
                                <td class="fw-bold"><?= $r['nama_penyewa']; ?></td>
                                <td><?= date('d M Y', strtotime($r['tanggal_booking'])); ?></td>
                                <td>
                                    <?php if($r['status_bayar'] == 'Lunas'): ?>
                                        <span class="badge bg-success rounded-pill px-3">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada pesanan masuk.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Digital Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.getHours().toString().padStart(2, '0') + ":" + 
                                                      now.getMinutes().toString().padStart(2, '0') + ":" + 
                                                      now.getSeconds().toString().padStart(2, '0');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barGradient = ctxBar.createLinearGradient(0, 0, 0, 400);
    barGradient.addColorStop(0, '#1a2a6c');
    barGradient.addColorStop(1, '#b21f1f');

    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [<?php 
                $f = mysqli_query($conn, "SELECT nama_fasilitas FROM fasilitas WHERE id_mitra = '$id_mitra'");
                while($row = mysqli_fetch_array($f)) echo "'".$row['nama_fasilitas']."',"; 
            ?>],
            datasets: [{
                label: 'Jumlah Pesanan',
                data: [<?php 
                    $f2 = mysqli_query($conn, "SELECT id_fasilitas FROM fasilitas WHERE id_mitra = '$id_mitra'");
                    while($row2 = mysqli_fetch_array($f2)){
                        $c = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM booking WHERE id_fasilitas='$row2[id_fasilitas]' AND id_mitra='$id_mitra'"));
                        echo $c.",";
                    }
                ?>],
                backgroundColor: barGradient,
                borderRadius: 10,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 3. Doughnut Chart
    const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Pending'],
            datasets: [{
                data: [<?= $lunas; ?>, <?= $pending; ?>],
                backgroundColor: ['#1a2a6c', '#fdbb2d'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
        }
    });
</script>

<?php include '../includes/footer.php'; ?>