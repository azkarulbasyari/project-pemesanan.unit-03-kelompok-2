<?php
session_start();

// Proteksi halaman: Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Koneksi ke database
require_once 'config/koneksi.php';

// ==========================================
// KONTROLLER & TEMPLATE UTAMA (dashboard.php)
// ==========================================

// 1. Tentukan halaman yang aktif berdasarkan parameter query 'page' di URL (Contoh: ?page=layanan)
// Jika parameter 'page' tidak diset di URL, maka default-nya adalah halaman 'dashboard' (halaman utama)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// 2. Buat whitelist (daftar halaman yang diizinkan) untuk keamanan sistem
// Hal ini sangat penting untuk mencegah kerentanan Local File Inclusion (LFI)
// Di mana pengguna tidak bisa memasukkan sembarang nama file atau path melalui URL
$allowed_pages = [
    // Halaman Utama Dashboard (Statistik & Ringkasan)
    'dashboard' => [
        'title' => 'Dashboard Pemesanan Layanan', // Judul halaman di tag <title>
        'sidebar' => true,                       // Indikator jika halaman membutuhkan sidebar layout
        'file' => 'pages/dashboard.php'          // Path file konten asli yang akan di-include
    ],
    // Halaman Daftar Pesanan
    'pesanan' => [
        'title' => 'Manajemen Pesanan',             // Judul halaman di tag <title>
        'sidebar' => true,                       // Halaman ini menggunakan layout sidebar
        'file' => 'pages/daftar-pesanan.php'     // Path file daftar pesanan
    ],
    // Halaman Tambah Pesanan
    'pesanan-tambah' => [
        'title' => 'Tambah Pesanan',             // Judul halaman di tag <title>
        'sidebar' => true,                       // Halaman ini menggunakan layout sidebar
        'file' => 'pages/tambah-pesanan.php'     // Path file formulir tambah pesanan
    ],
    // Halaman Data Layanan
    'layanan' => [
        'title' => 'Data Layanan',               // Judul halaman di tag <title>
        'sidebar' => true,                       // Halaman ini menggunakan layout sidebar
        'file' => 'pages/layanan.php'            // Path file tabel & grid layanan
    ],
    // Halaman Edit Pesanan
    'pesanan-edit' => [
        'title' => 'Edit Pesanan',               // Judul halaman di tag <title>
        'sidebar' => true,                       // Halaman ini menggunakan layout sidebar
        'file' => 'pages/edit-pesanan.php'       // Path file formulir edit pesanan
    ],
    // Halaman Hapus Pesanan
    'pesanan-hapus' => [
        'title' => 'Hapus Pesanan',              // Judul halaman di tag <title>
        'sidebar' => false,                      // Halaman ini tidak menggunakan sidebar (tampilan penuh/tengah)
        'file' => 'pages/hapus-pesanan.php'      // Path file konfirmasi hapus pesanan
    ]
];

// 3. Validasi parameter 'page' yang dikirim melalui URL
// Jika nilai variabel $page tidak ada di dalam daftar kunci whitelist ($allowed_pages),
// maka secara otomatis akan dialihkan kembali ke halaman utama ('dashboard') untuk keamanan
if (!array_key_exists($page, $allowed_pages)) {
    $page = 'dashboard';
}

// 4. Ambil konfigurasi halaman yang dipilih setelah berhasil divalidasi
$page_config = $allowed_pages[$page];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Pengaturan karakter encoding dokumen -->
    <meta charset="UTF-8">
    <!-- Pengaturan viewport agar tampilan web responsif di berbagai perangkat (Mobile/Tablet/PC) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul halaman dinamis sesuai konfigurasi halaman yang dipanggil -->
    <title><?php echo $page_config['title']; ?></title>

    <!-- Import stylesheet Bootstrap 5.3.3 untuk CSS UI framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Bootstrap Icons untuk ikon grafis dalam aplikasi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Import DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Import stylesheet kustom lokal (style.css) -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- 5. Kondisional Layout: Memeriksa apakah halaman memerlukan sidebar -->
    <?php if ($page_config['sidebar']): ?>
        <!-- Layout dengan Sidebar (Container Fluid agar lebar penuh) -->
        <div class="container-fluid">
            <div class="row">
                <!-- Sisi Kiri: Sidebar Menu (Include Component) -->
                <?php include 'includes/sidebar.php'; ?>

                <!-- Sisi Kanan: Konten Utama yang Dinamis -->
                <div class="col-lg-10 col-md-9 main-content">
                    <!-- Memanggil secara dinamis file konten halaman yang aktif -->
                    <?php include $page_config['file']; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Layout Tanpa Sidebar (Tampilan Penuh, contoh: halaman konfirmasi hapus pesanan) -->
        <!-- Memanggil secara dinamis file konten halaman tanpa pembungkus grid sidebar -->
        <?php include $page_config['file']; ?>
    <?php endif; ?>

    <!-- Include Modal Logout Component -->
    <?php include 'includes/modal-logout.php'; ?>

    <!-- Import jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Import Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Import DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Include Toast Container Component & JS Helper -->
    <?php include 'includes/toast.php'; ?>

</body>
</html>