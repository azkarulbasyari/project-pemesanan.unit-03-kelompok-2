<?php
// Memulai session PHP
session_start();

// Memanggil file koneksi database
require_once '../config/koneksi.php';

// Memastikan file ini diproses hanya ketika ada pengiriman data via form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bersihkan input username agar aman dari SQL Injection, dan trim spasi kosong pada password
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Validasi awal: username dan password tidak boleh dikirim kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
        header("Location: ../index.php");
        exit;
    }

    // Alur pengecekan login jika role yang dimasukkan adalah admin
    if (strtolower($username) === 'admin') {
        $sql = "SELECT * FROM users WHERE username = 'admin' LIMIT 1";
        $query = mysqli_query($koneksi, $sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            
            // Verifikasi kecocokan password admin menggunakan password_verify
            $password_match = password_verify($password, $user['password_hash']);
            
            // Bypass/fallback login jika hash password bawaan database adalah contoh placeholder
            if (!$password_match && $user['password_hash'] === '$2y$10$contohpasswordhash') {
                if ($password === 'admin') {
                    $password_match = true;
                }
            }
            
            // Jika password admin cocok, daftarkan session data admin
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
        
        // Kirim pesan error jika akun admin tidak terdaftar atau password salah
        $_SESSION['error_message'] = "username atau pasword salah";
        header("Location: ../index.php");
        exit;
    } 
    // Alur pengecekan login jika role yang dimasukkan adalah operator (selain admin)
    else {
        // Validasi password operator: wajib lebih dari 5 karakter dan memiliki minimal satu angka
        if (strlen($password) > 5 && preg_match('/[0-9]/', $password)) {
            // Cari data user di database berdasarkan username
            $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
            $query = mysqli_query($koneksi, $sql);
            
            if ($query && mysqli_num_rows($query) > 0) {
                $user = mysqli_fetch_assoc($query);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
            } else {
                // Buat user operator baru di database agar memiliki user_id valid (mencegah Foreign Key constraint error pada tabel pesanan)
                $stmt_ins_user = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'operator', 'aktif')");
                $nama_lengkap_new = ucfirst($username);
                $email_new = strtolower($username) . "@layanan.com";
                $pass_hash_new = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt_ins_user, "ssss", $nama_lengkap_new, $username, $email_new, $pass_hash_new);
                
                if (mysqli_stmt_execute($stmt_ins_user)) {
                    $_SESSION['user_id'] = mysqli_insert_id($koneksi);
                    $_SESSION['username'] = $username;
                    $_SESSION['nama_lengkap'] = $nama_lengkap_new;
                    $_SESSION['role'] = 'operator';
                } else {
                    $_SESSION['user_id'] = null;
                    $_SESSION['username'] = $username;
                    $_SESSION['nama_lengkap'] = ucfirst($username);
                    $_SESSION['role'] = 'operator';
                }
            }
            
            $_SESSION['success_message'] = "berhasil login";
            header("Location: ../dashboard.php");
            exit;
        } else {
            // Peringatan jika kriteria password operator tidak terpenuhi
            $_SESSION['error_message'] = "Kata sandi operator harus > 5 huruf dan mengandung angka.";
            header("Location: ../index.php");
            exit;
        }
    }
} else {
    // Alihkan langsung ke index jika diakses tanpa form post
    header("Location: ../index.php");
    exit;
}
?>
