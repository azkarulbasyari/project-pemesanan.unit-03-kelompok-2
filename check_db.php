<?php
/**
 * FUNGSI FILE:
 * Berkas utility untuk memeriksa dan memigrasi kolom baru 'dibuat_oleh' pada tabel 'pesanan'.
 * Berkas ini berguna saat sinkronisasi skema database secara manual.
 */

// Memanggil koneksi database dari folder konfigurasi
require_once 'config/koneksi.php';

// QUERY DATABASE: Memeriksa apakah kolom 'dibuat_oleh' sudah ada dalam struktur tabel 'pesanan'
$res = mysqli_query($koneksi, "SHOW COLUMNS FROM pesanan LIKE 'dibuat_oleh'");

// KONDISI IF-ELSE: Jika hasil baris query sama dengan 0 (artinya kolom belum terdaftar di database)
if (mysqli_num_rows($res) == 0) {
    // QUERY DATABASE: Menambahkan kolom baru 'dibuat_oleh' sebagai ENUM ('admin', 'customer') secara dinamis
    $alter = mysqli_query($koneksi, "ALTER TABLE pesanan ADD COLUMN dibuat_oleh ENUM('admin', 'customer') DEFAULT 'customer' AFTER created_by");
    
    // KONDISI IF-ELSE (Nested): Memvalidasi hasil query ALTER TABLE
    if ($alter) {
        echo "SUCCESS: Column 'dibuat_oleh' added successfully.\n";
    } else {
        echo "ERROR: Failed to add column. Error: " . mysqli_error($koneksi) . "\n";
    }
} else {
    // KONDISI ELSE: Jika kolom 'dibuat_oleh' sudah ada di database, kirim informasi
    echo "INFO: Column 'dibuat_oleh' already exists.\n";
}
?>
