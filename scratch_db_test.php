<?php
// File uji coba untuk mengetes koneksi database & mencetak data transaksi pesanan ke console/terminal

// Menyertakan file koneksi database secara absolut agar aman saat dieksekusi di background
require_once __DIR__ . '/config/koneksi.php';

// Memastikan variabel koneksi database bernilai true/tersambung
if ($koneksi) {
    echo "Koneksi berhasil!\n";
    
    // Melakukan query select sederhana untuk mengambil data dari tabel pesanan
    $query = mysqli_query($koneksi, "SELECT * FROM pesanan");
    
    if ($query) {
        echo "Data pesanan:\n";
        
        // Looping untuk membaca seluruh baris data pesanan yang didapat dari database
        while ($row = mysqli_fetch_assoc($query)) {
            echo "ID: " . $row['id'] . " - Kode: " . $row['kode_pesanan'] . "\n";
        }
    } else {
        // Tampilkan pesan error jika query gagal dieksekusi oleh MySQL
        echo "Gagal query pesanan: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "Koneksi gagal!\n";
}
?>
