<?php
// Memulai session
session_start();

// Pengkondisian jika user sudah dalam status login, langsung alihkan ke halaman dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Pemesanan Layanan</title>

    <!-- Memuat file CSS pendukung dan style kustom -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="./assets/css/style.css?v=20260715" rel="stylesheet">
</head>

<body class="login-page">

    <div class="container">
        <!-- Pengaturan grid agar box login berada tepat di tengah layar -->
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <div class="card login-card p-4">
                    <div class="card-body">
                        <!-- Logo / Brand aplikasi -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="brand-box">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">ServiceOrder</h4>
                                <small class="text-muted">Sistem Pemesanan Layanan</small>
                            </div>
                        </div>

                        <h3 class="fw-bold mb-2">Masuk Akun</h3>
                        <p class="text-muted mb-4">Silakan login untuk mengelola data pesanan layanan.</p>

                        <?php 
                        // Tampilkan pesan sukses jika baru saja logout (diterima dari query parameter ?status=logout)
                        if (isset($_GET['status']) && $_GET['status'] === 'logout'): 
                        ?>
                            <div class="alert alert-success border mb-3">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Anda telah berhasil keluar dari akun.
                            </div>
                        <?php endif; ?>

                        <?php 
                        // Tampilkan alert box & javascript alert jika kredensial login salah
                        if (isset($_SESSION['error_message'])): 
                        ?>
                            <div class="alert alert-danger border mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo $_SESSION['error_message']; ?>
                            </div>
                            <script>
                                alert("<?php echo $_SESSION['error_message']; ?>");
                            </script>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <!-- Form pengiriman data login ke process/login.php -->
                        <form action="process/login.php" method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" minlength="4" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" minlength="4" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ingat">
                                    <label class="form-check-label" for="ingat">Ingat saya</label>
                                </div>
                                <a href="#" class="text-decoration-none">Lupa password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                        </form>

                        <!-- Box petunjuk username & password bawaan sistem -->
                        <div class="alert alert-light border mt-4 mb-0 text-center text-muted" style="font-size: 0.85rem;">
                            Gunakan username & password default (<strong>admin</strong> atau <strong>operator</strong>) untuk masuk.
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

</body>

</html>