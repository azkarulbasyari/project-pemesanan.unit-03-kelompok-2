<!-- Halaman Edit Pesanan (Sebagai Template Antarmuka Statis) -->
<div class="page-header mb-4">
    <h2 class="fw-bold mb-2">Edit Data Pesanan</h2>
    <p class="mb-0">Perbarui data pesanan layanan pelanggan.</p>
</div>

<div class="card content-card">
    <div class="card-body">
        <!-- Form statis untuk simulasi layout perbaikan data -->
        <form action="dashboard.php" method="post">
            <div class="row g-3">
                
                <!-- Input Kode Pesanan (Read-only) -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kode Pesanan</label>
                    <input type="text" class="form-control" value="PSN-2026-001" required>
                </div>

                <!-- Input Tanggal Masuk Transaksi -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Pesan</label>
                    <input type="date" class="form-control" value="2026-07-01" required>
                </div>

                <!-- Input Detail Nama Pelanggan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Pelanggan</label>
                    <input type="text" class="form-control" value="Ahmad Fauzan" required>
                </div>

                <!-- Input Kontak Nomor HP -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor HP</label>
                    <input type="text" class="form-control" value="081234567890" required>
                </div>

                <!-- Input Surat Elektronik / Email -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Pelanggan</label>
                    <input type="email" class="form-control" value="ahmad@email.com">
                </div>

                <!-- Dropdown Pilihan Paket Layanan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Layanan</label>
                    <select class="form-select" required>
                        <option selected>Servis Laptop</option>
                        <option>Instal Ulang Windows</option>
                        <option>Desain Poster Digital</option>
                        <option>Konsultasi Website</option>
                    </select>
                </div>

                <!-- Input Nominal Total Biaya -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Total Harga</label>
                    <input type="number" class="form-control" value="150000" required>
                </div>

                <!-- Dropdown Status Progres Pengerjaan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status Pesanan</label>
                    <select class="form-select" required>
                        <option selected>Baru</option>
                        <option>Diproses</option>
                        <option>Selesai</option>
                        <option>Dibatalkan</option>
                    </select>
                </div>

                <!-- Input Deskripsi Alamat Tempat Tinggal -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Alamat Pelanggan</label>
                    <textarea class="form-control" rows="3">Banda Aceh</textarea>
                </div>

                <!-- Catatan Mengenai Kerusakan atau Request Layanan -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Catatan Pesanan</label>
                    <textarea class="form-control" rows="4">Laptop lambat dan sering restart.</textarea>
                </div>
            </div>

            <!-- Tombol Navigasi Batal or Simpan -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="dashboard.php?page=dashboard" class="btn btn-outline-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Update Pesanan
                </button>
            </div>
        </form>
    </div>
</div>