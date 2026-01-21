<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayGround - Multi-Vendor Sport Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .hero-section {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 4.5rem;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            animation: scaleIn 1s ease-out;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            font-weight: 300;
            animation: fadeIn 1.5s ease-out;
            max-width: 750px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-action {
            padding: 18px 45px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.4s ease;
            animation: fadeInUp 1.8s ease-out;
            border: 2px solid transparent;
        }

        .btn-action:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 25px;
            border: none;
            overflow: hidden;
        }
        .modal-header {
            background: #1a2a6c;
            color: white;
            border: none;
        }
        .list-group-item-action {
            transition: 0.3s;
            border: none;
            margin-bottom: 5px;
            border-radius: 12px !important;
            padding: 15px 20px;
        }
        .list-group-item-action:hover {
            background: #f8f9fa;
            transform: translateX(10px);
            color: #1a2a6c;
            font-weight: 600;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Selamat Datang di <span class="text-info">Play</span><span class="text-warning">Ground</span></h1>
                <p>
                    Portal pemesanan lapangan olahraga terintegrasi. Pilih lokasi olahraga favorit Anda dan booking secara instan.
                </p>
                <div class="d-flex justify-content-center gap-4 mt-5">
                    <button class="btn btn-primary btn-action shadow-lg" data-bs-toggle="modal" data-bs-target="#pilihTempatModal">
                        <i class="fas fa-map-marker-alt me-2"></i> Pilih Tempat
                    </button>
                    
                    <a href="admin/login.php" class="btn btn-outline-light btn-action shadow-lg">
                        <i class="fas fa-user-shield me-2"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pilihTempatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Mau main di mana hari ini?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="list-group list-group-flush">
                        <?php 
                        // Mengambil data mitra dari database
                        $q_mitra = mysqli_query($conn, "SELECT * FROM mitra ORDER BY nama_mitra ASC");
                        if(mysqli_num_rows($q_mitra) > 0) {
                            while($m = mysqli_fetch_array($q_mitra)) {
                                ?>
                                <a href="pengunjung_dashboard.php?id_mitra=<?= $m['id_mitra']; ?>" class="list-group-item list-group-item-action border shadow-sm mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= $m['nama_mitra']; ?></h6>
                                            <small class="text-muted"><i class="fas fa-location-dot me-1"></i> <?= $m['alamat']; ?></small>
                                        </div>
                                        <i class="fas fa-chevron-right text-primary"></i>
                                    </div>
                                </a>
                                <?php
                            }
                        } else {
                            echo "<p class='text-center text-muted'>Belum ada mitra yang terdaftar.</p>";
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <small class="text-muted small">Ingin mendaftarkan tempat olahraga Anda? Hubungi Admin.</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>