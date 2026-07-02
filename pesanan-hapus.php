<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Pesanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card content-card">
                    <div class="card-body text-center p-5">
                        <div class="stat-icon bg-danger-soft mx-auto mb-3">
                            <i class="bi bi-trash"></i>
                        </div>

                        <h3 class="fw-bold">Hapus Pesanan?</h3>
                        <p class="text-muted">
                            Data pesanan <strong>PSN-2026-001</strong> milik pelanggan 
                            <strong>Ahmad Fauzan</strong> akan dihapus dari daftar pesanan.
                        </p>

                        <div class="alert alert-danger">
                            Tindakan ini hanya contoh tampilan statis. Proses hapus sebenarnya membutuhkan backend.
                        </div>

                        <form action="dashboard.html" method="post">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="dashboard.html" class="btn btn-outline-secondary">
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

</body>
</html>