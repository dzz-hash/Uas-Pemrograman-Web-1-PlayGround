<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include '../config/koneksi.php';

// Header untuk memberi tahu browser bahwa ini adalah file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Booking_PlayGround.xls");
?>

<center>
    <h2>LAPORAN DATA BOOKING PLAYGROUND</h2>
</center>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>Telepon</th>
            <th>Lapangan</th>
            <th>Tanggal</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT booking.*, fasilitas.nama_fasilitas 
                                     FROM booking 
                                     JOIN fasilitas ON booking.id_fasilitas = fasilitas.id_fasilitas 
                                     ORDER BY id_booking DESC");
        while($d = mysqli_fetch_array($query)){
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $d['nama_penyewa']; ?></td>
            <td>'<?php echo $d['telepon']; ?></td> <td><?php echo $d['nama_fasilitas']; ?></td>
            <td><?php echo $d['tanggal_booking']; ?></td>
            <td><?php echo $d['jam_mulai']; ?></td>
            <td><?php echo $d['jam_selesai']; ?></td>
            <td><?php echo $d['status_bayar']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
