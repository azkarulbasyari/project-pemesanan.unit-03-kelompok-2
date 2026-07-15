<?php
/**
 * FUNGSI FILE:
 * Berkas pengujian koneksi database dan query SELECT tabel 'pesanan' secara cepat.
 * Digunakan untuk melakukan diagnosa debugging koneksi database di lingkungan server lokal.
 */

// Memanggil koneksi database secara aman menggunakan relative path
require_once __DIR__ . '/config/koneksi.php';

// KONDISI IF-ELSE: Memvalidasi apakah variabel $koneksi berhasil terhubung
if ($koneksi) {
    echo "Koneksi berhasil!\n";
    
    // QUERY DATABASE: Mengambil seluruh data pesanan dari tabel 'pesanan'
    $query = mysqli_query($koneksi, "SELECT * FROM pesanan");
    
    // KONDISI IF-ELSE (Nested): Memvalidasi keberhasilan query SELECT
    if ($query) {
        echo "Data pesanan:\n";
        
        // LOOPING WHILE: Mengiterasi dan menampilkan setiap baris data pesanan hasil query
        while ($row = mysqli_fetch_assoc($query)) {
            echo "ID: " . $row['id'] . " - Kode: " . $row['kode_pesanan'] . "\n";
        }
    } else {
        // KONDISI ELSE (Nested): Menampilkan kesalahan query jika gagal dijalankan
        echo "Gagal query pesanan: " . mysqli_error($koneksi) . "\n";
    }
} else {
    // KONDISI ELSE: Menampilkan pesan jika koneksi ke server MySQL gagal terhubung
    echo "Koneksi gagal!\n";
}
?>
