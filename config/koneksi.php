<?php
// ==============================================================
// KONFIGURASI KONEKSI DATABASE (config/koneksi.php)
// ==============================================================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_pemesanan_layanan";

// Melakukan koneksi ke database MySQL
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi apakah berhasil atau gagal
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
