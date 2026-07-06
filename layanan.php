<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Layanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 sidebar p-0">
                <div class="brand">
                    <h5 class="fw-bold mb-1">ServiceOrder</h5>
                    <small class="text-secondary">Admin Panel</small>
                </div>

                <nav class="p-3">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="pesanan-tambah.php" class="nav-link">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Pesanan
                    </a>
                    <a href="layanan.php" class="nav-link">
                        <i class="bi bi-grid me-2"></i> Data Layanan
                    </a>
                    <a href="#" class="nav-link">
                        <i class="bi bi-box-arrow-left me-2"></i> Logout
                    </a>
                </nav>
            </div>

            <div class="col-lg-10 col-md-9 main-content">
                <div class="page-header mb-4">
                    <h2 class="fw-bold mb-2">Data Layanan</h2>
                    <p class="mb-0">Daftar layanan yang dapat dipesan oleh pelanggan.</p>
                </div>

                <div class="row g-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="stat-icon bg-primary-soft mb-3">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <h5 class="fw-bold">Servis Laptop</h5>
                                <p class="text-muted mb-3">
                                    Pemeriksaan dan perbaikan laptop ringan.
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Rp150.000</strong>
                                    <span class="badge text-bg-success">Aktif</span>
                                </div>
                                <small class="text-muted d-block mt-2">Estimasi: 2 Hari</small>
                                <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                <i class="bi bi-info-circle me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="stat-icon bg-success-soft mb-3">
                                    <i class="bi bi-windows"></i>
                                </div>
                                <h5 class="fw-bold">Instal Ulang Windows</h5>
                                <p class="text-muted mb-3">
                                    Instalasi sistem operasi dan aplikasi dasar.
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Rp100.000</strong>
                                    <span class="badge text-bg-success">Aktif</span>
                                </div>
                                <small class="text-muted d-block mt-2">Estimasi: 1 Hari</small>
                                <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">
                              <i class="bi bi-info-circle me-1"></i> Lihat Detail
                            </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="stat-icon bg-warning-soft mb-3">
                                    <i class="bi bi-palette"></i>
                                </div>
                                <h5 class="fw-bold">Desain Poster Digital</h5>
                                <p class="text-muted mb-3">
                                    Pembuatan desain poster untuk promosi.
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Rp75.000</strong>
                                    <span class="badge text-bg-success">Aktif</span>
                                </div>
                                <small class="text-muted d-block mt-2">Estimasi: 1 Hari</small>
                                <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                <i class="bi bi-info-circle me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="stat-icon bg-danger-soft mb-3">
                                    <i class="bi bi-globe"></i>
                                </div>
                                <h5 class="fw-bold">Konsultasi Website</h5>
                                <p class="text-muted mb-3">
                                    Konsultasi kebutuhan website sederhana.
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Rp200.000</strong>
                                    <span class="badge text-bg-success">Aktif</span>
                                </div>
                                <small class="text-muted d-block mt-2">Estimasi: 3 Hari</small>
                                <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">
                                <i class="bi bi-info-circle me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card content-card mt-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tabel Layanan</h5>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Layanan</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Estimasi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Servis Laptop</td>
                                        <td>Teknologi</td>
                                        <td>Rp150.000</td>
                                        <td>2 Hari</td>
                                        <td><span class="badge text-bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Instal Ulang Windows</td>
                                        <td>Teknologi</td>
                                        <td>Rp100.000</td>
                                        <td>1 Hari</td>
                                        <td><span class="badge text-bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Desain Poster Digital</td>
                                        <td>Desain</td>
                                        <td>Rp75.000</td>
                                        <td>1 Hari</td>
                                        <td><span class="badge text-bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Konsultasi Website</td>
                                        <td>Konsultasi</td>
                                        <td>Rp200.000</td>
                                        <td>3 Hari</td>
                                        <td><span class="badge text-bg-success">Aktif</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>