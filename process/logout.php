<?php
// Memulai session PHP agar bisa dihancurkan
session_start();

// Mengosongkan seluruh array data session
$_SESSION = [];

// Menghapus data session yang tersimpan di server
session_destroy();

// Menghapus cookie session pada browser (jika ada) untuk keamanan tambahan
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Redirect ke halaman login awal dengan memberikan status logout sukses
header("Location: ../index.php?status=logout");
exit;
?>
