<?php
// Script untuk migrasi otomatis kolom 'dibuat_oleh' ke dalam tabel 'pesanan' jika belum ada

// Memanggil file koneksi database agar query sql bisa dijalankan
require_once 'config/koneksi.php';

// Melakukan cek apakah kolom 'dibuat_oleh' sudah pernah dibuat sebelumnya di tabel pesanan
$res = mysqli_query($koneksi, "SHOW COLUMNS FROM pesanan LIKE 'dibuat_oleh'");

// Jika kolom belum ditemukan (jumlah baris hasil cek bernilai 0), lakukan pembuatan kolom
if (mysqli_num_rows($res) == 0) {
    // Jalankan query ALTER TABLE untuk menambahkan kolom dibuat_oleh dengan tipe data ENUM
    $alter = mysqli_query($koneksi, "ALTER TABLE pesanan ADD COLUMN dibuat_oleh ENUM('admin', 'customer') DEFAULT 'customer' AFTER created_by");
    
    // Tampilkan informasi status hasil eksekusi query migrasi
    if ($alter) {
        echo "SUCCESS: Column 'dibuat_oleh' added successfully.\n";
    } else {
        echo "ERROR: Failed to add column. Error: " . mysqli_error($koneksi) . "\n";
    }
} else {
    // Informasi jika kolom tersebut memang sudah terdaftar di struktur tabel
    echo "INFO: Column 'dibuat_oleh' already exists.\n";
}
?>
