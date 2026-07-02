<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan</title>

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
                    <h2 class="fw-bold mb-2">Tambah Pesanan Baru</h2>
                    <p class="mb-0">Isi data pelanggan dan layanan yang ingin dipesan.</p>
                </div>

                <div class="card content-card">
                    <div class="card-body">
                        <form action="dashboard.html" method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Pesanan</label>
                                    <input type="text" class="form-control" value="PSN-2026-005" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal Pesan</label>
                                    <input type="date" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Pelanggan</label>
                                    <input type="text" class="form-control" placeholder="Contoh: Ahmad Fauzan" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor HP</label>
                                    <input type="text" class="form-control" placeholder="Contoh: 081234567890" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Pelanggan</label>
                                    <input type="email" class="form-control" placeholder="Contoh: pelanggan@email.com">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Layanan</label>
                                    <select class="form-select" required>
                                        <option value="">Pilih layanan</option>
                                        <option>Servis Laptop</option>
                                        <option>Instal Ulang Windows</option>
                                        <option>Desain Poster Digital</option>
                                        <option>Konsultasi Website</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Harga</label>
                                    <input type="number" class="form-control" placeholder="Contoh: 150000" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status Pesanan</label>
                                    <select class="form-select" required>
                                        <option value="baru">Baru</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="dibatalkan">Dibatalkan</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Alamat Pelanggan</label>
                                    <textarea class="form-control" rows="3" placeholder="Masukkan alamat pelanggan"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Catatan Pesanan</label>
                                    <textarea class="form-control" rows="4" placeholder="Contoh: Laptop lambat, minta backup data terlebih dahulu"></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="dashboard.html" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>