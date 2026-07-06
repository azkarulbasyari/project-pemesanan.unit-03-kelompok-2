<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemesanan Layanan</title>

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
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-2">Dashboard Pemesanan</h2>
                            <p class="mb-0">Kelola pesanan layanan pelanggan secara mudah dan terstruktur.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="pesanan-tambah.html" class="btn btn-light">
                                <i class="bi bi-plus-circle me-1"></i> Buat Pesanan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Total Pesanan</small>
                                    <h3 class="fw-bold mb-0">128</h3>
                                </div>
                                <div class="stat-icon bg-primary-soft">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Pesanan Baru</small>
                                    <h3 class="fw-bold mb-0">24</h3>
                                </div>
                                <div class="stat-icon bg-primary-soft">
                                    <i class="bi bi-bell"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Diproses</small>
                                    <h3 class="fw-bold mb-0">39</h3>
                                </div>
                                <div class="stat-icon bg-warning-soft">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Selesai</small>
                                    <h3 class="fw-bold mb-0">65</h3>
                                </div>
                                <div class="stat-icon bg-success-soft">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card content-card">
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-0">Daftar Pesanan Layanan</h5>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <form action="#" method="get">
                                    <div class="input-group">
                                        <input type="text" name="keyword" class="form-control" placeholder="Cari kode, pelanggan, atau layanan" 
                                        autocomplete="off" maxlength="50">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><strong>PSN-2026-001</strong></td>
                                        <td>Ahmad Fauzan</td>
                                        <td>Servis Laptop</td>
                                        <td>01 Juli 2026</td>
                                        <td>Rp150.000</td>
                                        <td>
                                            <span class="badge-status status-baru">Baru</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="pesanan-edit.html" class="btn btn-sm btn-outline-primary action-btn">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="pesanan-hapus.html" class="btn btn-sm btn-outline-danger action-btn">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td><strong>PSN-2026-002</strong></td>
                                        <td>Siti Rahmah</td>
                                        <td>Instal Ulang Windows</td>
                                        <td>01 Juli 2026</td>
                                        <td>Rp100.000</td>
                                        <td>
                                            <span class="badge-status status-diproses">Diproses</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="pesanan-edit.html" class="btn btn-sm btn-outline-primary action-btn">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="pesanan-hapus.html" class="btn btn-sm btn-outline-danger action-btn">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td><strong>PSN-2026-003</strong></td>
                                        <td>Muhammad Ridha</td>
                                        <td>Desain Poster Digital</td>
                                        <td>01 Juli 2026</td>
                                        <td>Rp75.000</td>
                                        <td>
                                            <span class="badge-status status-selesai">Selesai</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="pesanan-edit.html" class="btn btn-sm btn-outline-primary action-btn">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="pesanan-hapus.html" class="btn btn-sm btn-outline-danger action-btn">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td><strong>PSN-2026-004</strong></td>
                                        <td>Nurul Hidayah</td>
                                        <td>Konsultasi Website</td>
                                        <td>02 Juli 2026</td>
                                        <td>Rp200.000</td>
                                        <td>
                                            <span class="badge-status status-dibatalkan">Dibatalkan</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="pesanan-edit.html" class="btn btn-sm btn-outline-primary action-btn">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="pesanan-hapus.html" class="btn btn-sm btn-outline-danger action-btn">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">Menampilkan 4 dari 128 data pesanan</small>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#">Sebelumnya</a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Berikutnya</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>