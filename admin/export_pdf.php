<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include '../config/koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan PDF</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak Ke PDF / Printer</button>
        <a href="data_booking.php">Kembali</a>
        <hr>
    </div>

    <div class="header">
        <h2>LAPORAN PENYEWAAN LAPANGAN</h2>
        <p>PlayGround Center - Sistem Informasi Terintegrasi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Penyewa</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($conn, "SELECT booking.*, fasilitas.nama_fasilitas FROM booking JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas");
            while($d = mysqli_fetch_array($query)){
                echo "<tr>
                    <td>".$no++."</td>
                    <td>".$d['nama_penyewa']."</td>
                    <td>".$d['nama_fasilitas']."</td>
                    <td>".$d['tanggal_booking']."</td>
                    <td>".$d['jam_mulai']." - ".$d['jam_selesai']."</td>
                    <td>".$d['status_bayar']."</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>Dicetak pada: <?php echo date('d-m-Y H:i'); ?></p>
        <br><br>
        <p>( Administrator )</p>
    </div>

    <script>
        // Otomatis membuka jendela print saat halaman dimuat
        // window.print(); 
    </script>
</body>
</html>