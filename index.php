<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Pemesanan Layanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">

    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <div class="card login-card p-4">
                    <div class="card-body">
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

                        <form action="dashboard.html" method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" placeholder="Masukkan username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" placeholder="Masukkan password" required>
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

                        <div class="alert alert-light border mt-4 mb-0">
     
                        </div>
                    </div>
                </div>

 
            </div>
        </div>
    </div>

</body>
</html>