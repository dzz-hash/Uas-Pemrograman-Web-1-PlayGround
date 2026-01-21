<?php
session_start();
include '../config/koneksi.php';

if (isset($_SESSION['id_mitra'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM mitra WHERE username='$u' AND status_aktif='Y'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        if (password_verify($p, $row['password'])) {
            $_SESSION['id_mitra']    = $row['id_mitra'];
            $_SESSION['nama_mitra']  = $row['nama_mitra'];
            $_SESSION['username']    = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $err = "Password salah!";
        }
    } else { 
        $err = "Akun tidak ditemukan atau tidak aktif!"; 
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin | PlayGround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            height: 100vh; display: flex; align-items: center; justify-content: center; 
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .card-login { 
            max-width: 400px; width: 100%; border-radius: 25px; border: none;
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 40px;
        }
    </style>
</head>
<body>

<div class="card card-login shadow-lg text-center">
    <div class="mb-3">
        <i class="fas fa-user-shield fa-3x text-primary"></i>
    </div>
    <h4 class="fw-bold mb-1">LOGIN</h4>
    <p class="text-muted small mb-4">Aplikasi Booking</p>

    <?php if(isset($err)): ?>
        <div class="alert alert-danger py-2 small border-0"><?= $err ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="small fw-bold">USERNAME</label>
            <input type="text" name="username" class="form-control rounded-pill border-0 px-3 py-2 bg-white shadow-sm" required>
        </div>
        <div class="mb-4 text-start">
            <label class="small fw-bold">PASSWORD</label>
            <input type="password" name="password" class="form-control rounded-pill border-0 px-3 py-2 bg-white shadow-sm" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow">LOGIN</button>
    </form>
    
    <div class="mt-4 pt-3 border-top">
        <p class="small text-muted mb-0">Cabang Baru? <a href="daftar.php" class="text-primary text-decoration-none fw-bold">Daftar Sini</a></p>
        <a href="../index.php" class="text-muted small text-decoration-none">Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>