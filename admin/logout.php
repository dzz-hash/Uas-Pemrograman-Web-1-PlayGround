<?php
// Mulai session
session_start();

// Hapus semua data session
$_SESSION = array();

// Hancurkan session secara total
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect menggunakan JavaScript sebagai cadangan jika header PHP gagal
echo "<script>
    window.location.href = 'login.php';
</script>";

// Tetap gunakan header PHP untuk standar keamanan
header("Location: login.php");
exit();
?>