<?php
// Mulai session untuk mencocokkan status login user
session_start();

// Validasi akses: jika tidak ada session user_id, tendang kembali ke halaman login (index.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Hubungkan ke database MySQL
require_once 'config/koneksi.php';

// Ambil parameter halaman (?page=...) dari URL, default-nya ke 'dashboard'
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Daftar halaman resmi (routing) yang diizinkan untuk diakses di dalam panel admin
$allowed_pages = [
    'dashboard' => [
        'title' => 'Dashboard Pemesanan Layanan',
        'sidebar' => true,
        'file' => 'pages/dashboard.php'
    ],
    'pesanan' => [
        'title' => 'Manajemen Pesanan',
        'sidebar' => true,
        'file' => 'pages/daftar-pesanan.php'
    ],
    'pesanan-tambah' => [
        'title' => 'Tambah Pesanan',
        'sidebar' => true,
        'file' => 'pages/tambah-pesanan.php'
    ],
    'layanan' => [
        'title' => 'Data Layanan',
        'sidebar' => true,
        'file' => 'pages/layanan.php'
    ],
    'pesanan-edit' => [
        'title' => 'Edit Pesanan',
        'sidebar' => true,
        'file' => 'pages/edit-pesanan.php'
    ],
    'pesanan-hapus' => [
        'title' => 'Hapus Pesanan',
        'sidebar' => false,
        'file' => 'pages/hapus-pesanan.php'
    ]
];

// Proteksi router: jika halaman tidak terdaftar, arahkan paksa ke halaman dashboard utama
if (!array_key_exists($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Simpan konfigurasi halaman aktif untuk digunakan saat me-render layout
$page_config = $allowed_pages[$page];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_config['title']; ?></title>

    <!-- Memuat library Bootstrap 5, Bootstrap Icons, dan DataTables secara online (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="./assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php if ($page === 'dashboard'): ?>
        <link href="./assets/css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php elseif ($page === 'layanan'): ?>
        <link href="./assets/css/layanan.css?v=<?php echo time(); ?>" rel="stylesheet">
    <?php endif; ?>

    <!-- Memuat library JavaScript pendukung di head agar dapat digunakan oleh script di halaman yang di-include -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

    <!-- Pengkondisian layout: me-render sidebar jika halaman mewajibkannya -->
    <?php if ($page_config['sidebar']): ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sertakan komponen navigasi sidebar -->
                <?php include 'includes/sidebar.php'; ?>

                <!-- Bagian wadah konten halaman aktif -->
                <div class="col-lg-10 col-md-9 main-content">
                    <?php include $page_config['file']; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Render halaman secara penuh tanpa sidebar -->
        <?php include $page_config['file']; ?>
    <?php endif; ?>

    <!-- Sertakan modal konfirmasi logout di setiap halaman -->
    <?php include 'includes/modal-logout.php'; ?>

    <!-- Sertakan toast notifier untuk memunculkan notifikasi melayang -->
    <?php include 'includes/toast.php'; ?>

</body>
</html>