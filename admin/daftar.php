<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Cabang Baru | PlayGround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .card-regis { border-radius: 20px; border: none; overflow: hidden; }
        .bg-regis { 
            background: url('https://plus.unsplash.com/premium_vector-1720768432450-f210195e56fa?q=80&w=759&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3Dhttps://plus.unsplash.com/premium_vector-1750154283637-9f9b102d4298?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1yZWxhdGVkfDd8fHxlbnwwfHx8fHw%3D'); 
            background-size: cover; 
            background-position: center;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-regis shadow-lg">
                <div class="row g-0">
                    <div class="col-md-5 d-none d-md-block bg-regis"></div>
                    <div class="col-md-7 p-5 bg-white">
                        <h3 class="fw-bold text-dark mb-1">Daftar</h3>
                        <p class="text-muted small mb-4">Tambahkan lokasi olahraga baru</p>

                        <form action="proses_daftar.php" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-muted">NAMA TEMPAT</label>
                                    <input type="text" name="nama_mitra" class="form-control rounded-pill px-3" placeholder="Contoh: GIB Sport Center" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label small fw-bold text-muted">ALAMAT LENGKAP</label>
                                    <textarea name="alamat" class="form-control rounded-4 px-3" rows="2" placeholder="Alamat..." required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">USERNAME LOGIN</label>
                                    <input type="text" name="username" class="form-control rounded-pill px-3" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">PASSWORD</label>
                                    <input type="password" name="password" class="form-control rounded-pill px-3" required>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label small fw-bold text-muted">NOMOR WHATSAPP (INFO BOOKING)</label>
                                    <input type="number" name="kontak" class="form-control rounded-pill px-3" placeholder="08..." required>
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow">
                                DAFTARKAN SEKARANG
                            </button>
                            
                            <div class="text-center mt-4">
                                <p class="small text-muted">Kembali ke panel <a href="login.php" class="text-primary text-decoration-none fw-bold">Login Admin</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>