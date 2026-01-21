<?php 
include 'config/koneksi.php'; 

// Ambil ID dari URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($id)) {
    header("Location: cek_status.php");
    exit;
}

// Ambil data booking untuk ditampilkan sebagai informasi
$query = "SELECT b.*, f.nama_fasilitas, m.nama_mitra 
          FROM booking b 
          JOIN fasilitas f ON b.id_fasilitas = f.id_fasilitas 
          JOIN mitra m ON b.id_mitra = m.id_mitra
          WHERE b.id_booking = '$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data booking tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Bayar | PlayGround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
        .card-upload { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .preview-container { 
            width: 100%; 
            min-height: 200px; 
            border: 2px dashed #ddd; 
            border-radius: 15px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden;
            background: #fafafa;
        }
        #imagePreview { max-width: 100%; max-height: 400px; display: none; }
        .btn-upload { border-radius: 10px; padding: 12px; font-weight: 600; transition: 0.3s; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-upload">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                        <h4 class="fw-bold">Upload Bukti</h4>
                        <p class="text-muted small">Pesanan di <strong><?= $data['nama_mitra'] ?></strong></p>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mb-4" style="font-size: 0.85rem;">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td>Penyewa</td><td>: <?= $data['nama_penyewa'] ?></td></tr>
                            <tr><td>Fasilitas</td><td>: <?= $data['nama_fasilitas'] ?></td></tr>
                            <tr><td>Tanggal</td><td>: <?= date('d M Y', strtotime($data['tanggal_booking'])) ?></td></tr>
                        </table>
                    </div>

                    <form id="formUpload" action="proses_upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_booking" value="<?= $id ?>">
                        <input type="hidden" name="nama_penyewa" value="<?= $data['nama_penyewa'] ?>">

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Pilih Foto Bukti Transfer</label>
                            <input type="file" name="bukti" id="buktiInput" class="form-control" accept="image/*" required onchange="previewImage()">
                            <div class="form-text mt-2">Format: JPG, JPEG, atau PNG.</div>
                        </div>

                        <div class="preview-container mb-4">
                            <div id="placeholderText" class="text-muted text-center p-3">
                                <i class="fas fa-image fa-2x d-block mb-2 opacity-25"></i>
                                <span class="small">Preview foto akan muncul di sini</span>
                            </div>
                            <img src="" id="imagePreview">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" onclick="konfirmasiKirim()" class="btn btn-primary btn-upload shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Bukti Sekarang
                            </button>
                            <a href="cek_status.php?nama=<?= urlencode($data['nama_penyewa']) ?>" class="btn btn-link text-muted btn-sm">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk melihat preview gambar sebelum upload
function previewImage() {
    const file = document.getElementById('buktiInput').files[0];
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('placeholderText');
    const reader = new FileReader();

    reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

// Fungsi Pop-up Konfirmasi SweetAlert2
function konfirmasiKirim() {
    const fileInput = document.getElementById('buktiInput');
    
    if (fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih foto bukti transfer Anda terlebih dahulu.',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }

    Swal.fire({
        title: 'Kirim Bukti Sekarang?',
        text: "Pastikan foto bukti transfer terlihat jelas agar admin mudah melakukan verifikasi.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Kirim!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading saat proses upload
            Swal.fire({
                title: 'Sedang Mengunggah...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('formUpload').submit();
        }
    });
}
</script>

</body>
</html>