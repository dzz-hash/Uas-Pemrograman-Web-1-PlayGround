<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PlayGround</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    @keyframes gradientAnimation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    body {
        background: linear-gradient(-45deg, #f8f9fc, #e2e8f0, #f1f5f9, #ffffff);
        background-size: 400% 400%;
        animation: gradientAnimation 10s ease infinite;
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
    }

    .navbar {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d) !important;
        background-size: 400% 400% !important;
        animation: gradientAnimation 15s ease infinite !important;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        padding: 15px 0;
    }

    .navbar .navbar-brand {
        color: #ffffff !important;
        font-weight: 700 !important;
        letter-spacing: 1px;
    }

    .navbar .nav-link {
        color: rgba(255, 255, 255, 0.85) !important;
        font-weight: 500;
        margin: 0 5px;
        transition: all 0.3s ease;
    }

    .navbar .nav-link i {
        color: #ffffff !important;
        margin-right: 5px;
    }

    .navbar .nav-link:hover, 
    .navbar .nav-link.active {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.5) !important;
        filter: brightness(0) invert(1);
    }

    .btn-logout {
        color: #ffffff !important;
        border: 2px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: 50px !important;
        padding: 5px 20px !important;
        transition: 0.3s;
    }

    .btn-logout:hover {
        background: #ffffff !important;
        color: #b21f1f !important;
        border-color: #ffffff !important;
    }

    .card {
        border: none;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .admin-content {
        padding-top: 40px;
        padding-bottom: 60px;
    }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-volleyball-ball me-2"></i>PLAYGROUND
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'data_booking.php') ? 'active' : ''; ?>" href="data_booking.php">
                        <i class="fas fa-list-ul"></i> Data Booking
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'data_fasilitas.php') ? 'active' : ''; ?>" href="data_fasilitas.php">
                        <i class="fas fa-dumbbell"></i> Fasilitas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php"><i class="fas fa-user-cog me-1"></i> Profil</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-logout btn-sm" href="javascript:void(0)" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="admin-content">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin keluar?',
                    text: "Sesi Anda akan diakhiri.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b21f1f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {   
                    if (result.isConfirmed) {
                    
                        window.location.href = 'logout.php';
                    }
                });
            });
        }
    });
</script>