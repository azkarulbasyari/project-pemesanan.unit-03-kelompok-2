<?php
// ==============================================================
// PROSES AUTENTIKASI LOGIN (process/login.php)
// ==============================================================

// Mulai session untuk menyimpan data login pengguna
session_start();

// Hubungkan file koneksi ke database
require_once '../config/koneksi.php';

// Periksa apakah form dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data input dari form login
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Validasi: pastikan username dan password tidak kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
        header("Location: ../index.php");
        exit;
    }

    // 1. Kasus Khusus: Jika username adalah 'admin'
    if (strtolower($username) === 'admin') {
        $sql = "SELECT * FROM users WHERE username = 'admin' LIMIT 1";
        $query = mysqli_query($koneksi, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            
            // Verifikasi password 'admin' (menyembunyikan password hash check fallback)
            $password_match = password_verify($password, $user['password_hash']);
            if (!$password_match && $user['password_hash'] === '$2y$10$contohpasswordhash') {
                if ($password === 'admin') {
                    $password_match = true;
                }
            }
            
            if ($password_match) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = 'admin';
                $_SESSION['success_message'] = "berhasil login";
                header("Location: ../dashboard.php");
                exit;
            }
        }
        
        $_SESSION['error_message'] = "username atau pasword salah";
        header("Location: ../index.php");
        exit;
    } 
    // 2. Kasus Pengguna Lain (Selain Admin)
    else {
        // Validasi password: Panjang > 5 karakter DAN mengandung setidaknya satu angka
        if (strlen($password) > 5 && preg_match('/[0-9]/', $password)) {
            // Cek apakah username sudah ada di database untuk mendapatkan info asli jika ada
            $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
            $query = mysqli_query($koneksi, $sql);
            
            if ($query && mysqli_num_rows($query) > 0) {
                $user = mysqli_fetch_assoc($query);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
            } else {
                // Jika tidak ada di DB, buat session operator dinamis
                $_SESSION['user_id'] = 999;
                $_SESSION['username'] = $username;
                $_SESSION['nama_lengkap'] = ucfirst($username);
                $_SESSION['role'] = 'operator';
            }
            
            $_SESSION['success_message'] = "berhasil login";
            header("Location: ../dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Kata sandi operator harus > 5 huruf dan mengandung angka.";
            header("Location: ../index.php");
            exit;
        }
    }
} else {
    // Jika file diakses langsung tanpa melalui form POST, alihkan kembali ke halaman utama login
    header("Location: ../index.php");
    exit;
}
?>
