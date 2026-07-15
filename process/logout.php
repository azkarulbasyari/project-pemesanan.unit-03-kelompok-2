<?php
// ==============================================================
// PROSES LOGOUT (process/logout.php)
// ==============================================================

// Mulai session agar kita bisa mengakses dan menghapusnya
session_start();

// Hapus seluruh variabel session yang terdaftar
$_SESSION = array();

// Jika ingin menghapus session secara total, hapus juga cookie session di browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session yang sedang aktif di server
session_destroy();

// Alihkan pengguna ke halaman login dengan status logout berhasil
header("Location: ../index.php?status=logout");
exit;
?>
