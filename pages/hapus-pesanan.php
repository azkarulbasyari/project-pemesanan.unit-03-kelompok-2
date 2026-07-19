<?php
// Cek koneksi db: pastikan database tersambung
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/config/koneksi.php';
}

// Menangkap ID pesanan yang dilempar dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Proses hapus pesanan jika ada pengiriman form konfirmasi (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($delete_id > 0) {
        // Ambil pelanggan_id terlebih dahulu
        $res_cust = mysqli_query($koneksi, "SELECT pelanggan_id FROM pesanan WHERE id = $delete_id LIMIT 1");
        $pelanggan_id = 0;
        if ($res_cust && $row_c = mysqli_fetch_assoc($res_cust)) {
            $pelanggan_id = intval($row_c['pelanggan_id']);
        }

        // Jalankan query DELETE untuk menghapus baris data transaksi
        $delete_query = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id = $delete_id");
        if ($delete_query) {
            // Hapus data pelanggan jika tidak memiliki pesanan lain di database
            if ($pelanggan_id > 0) {
                $res_chk = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM pesanan WHERE pelanggan_id = $pelanggan_id");
                if ($res_chk && $row_r = mysqli_fetch_assoc($res_chk)) {
                    if (intval($row_r['count']) === 0) {
                        mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id = $pelanggan_id");
                    }
                }
            }
            $_SESSION['success_message'] = "Pesanan berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus pesanan: " . mysqli_error($koneksi);
        }
    }
    // Alihkan kembali ke halaman utama dashboard
    header("Location: dashboard.php?page=dashboard");
    exit;
}

// Ambil detail buat verifikasi hapus (untuk memunculkan teks konfirmasi)
$kode_pesanan = '';
$nama_pelanggan = '';
$pesanan_exists = false;

if ($id > 0) {
    // Ambil kode pesanan dan nama pelanggan terkait
    $query = mysqli_query($koneksi, "SELECT p.kode_pesanan, pl.nama_pelanggan 
                                     FROM pesanan p
                                     JOIN pelanggan pl ON p.pelanggan_id = pl.id
                                     WHERE p.id = $id LIMIT 1");
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $kode_pesanan = $row['kode_pesanan'];
        $nama_pelanggan = $row['nama_pelanggan'];
        $pesanan_exists = true;
    }
}

// Jika data pesanan tidak valid atau tidak ada di DB, alihkan kembali
if (!$pesanan_exists) {
    $_SESSION['error_message'] = "Data pesanan tidak ditemukan.";
    header("Location: dashboard.php?page=dashboard");
    exit;
}
?>

<div class="container">
    <div class="row min-vh-100 align-items-center justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <!-- Box Kartu Konfirmasi Hapus Data -->
            <div class="card content-card">
                <div class="card-body text-center p-5">
                    
                    <div class="stat-icon bg-danger-soft mx-auto mb-3">
                        <i class="bi bi-trash"></i>
                    </div>

                    <h3 class="fw-bold">Hapus Pesanan?</h3>
                    <p class="text-muted">
                        Data pesanan <strong><?php echo htmlspecialchars($kode_pesanan); ?></strong> milik pelanggan 
                        <strong><?php echo htmlspecialchars($nama_pelanggan); ?></strong> akan dihapus dari daftar pesanan.
                    </p>

                    <!-- Form konfirmasi hapus mengarah ke halaman ini via POST -->
                    <form action="dashboard.php?page=pesanan-hapus" method="post">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="dashboard.php?page=dashboard" class="btn btn-outline-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i> Ya, Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-4">
                Sistem Pemesanan Layanan Sederhana
            </p>
        </div>
    </div>
</div>