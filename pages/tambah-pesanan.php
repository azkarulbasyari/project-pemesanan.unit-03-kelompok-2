<?php
// Cek koneksi db: pastikan database tersambung sebelum memuat form pemesanan
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/config/koneksi.php';
}

// Ambil rincian paket layanan dari database untuk di-render ke dropdown select paket layanan
$stmt_layanan = mysqli_prepare($koneksi, "SELECT id, nama_layanan, harga FROM layanan ORDER BY nama_layanan ASC");
mysqli_stmt_execute($stmt_layanan);
$result_layanan = mysqli_stmt_get_result($stmt_layanan);
$layanan_options = [];
if ($result_layanan) {
    while ($row = mysqli_fetch_assoc($result_layanan)) {
        $layanan_options[] = $row;
    }
}
?>

<!-- Bagian 1: Header Halaman Tambah Pesanan -->
<div class="page-header mb-4 text-white">
    <h2 class="fw-bold mb-2 text-white">Tambah Pesanan Baru</h2>
    <p class="text-white-50 mb-0">Isi data pelanggan dan layanan yang ingin dipesan dengan lengkap.</p>
</div>

<!-- Bagian 2: Panel Info Ringkas Layanan (Akan otomatis muncul setelah paket layanan dipilih) -->
<div id="detail_layanan_section" class="mb-4" style="display: none;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-primary text-uppercase px-3 py-1.5 rounded-pill mb-2" id="info_kategori" style="font-size: 0.7rem; letter-spacing: 0.06em;">KATEGORI</span>
                    <h3 class="fw-bold mb-2 text-white" id="info_nama_layanan" style="letter-spacing: -0.02em;">Nama Layanan</h3>
                    <p class="text-white-50 mb-0 small text-truncate-2" id="info_deskripsi" style="line-height: 1.5;">Deskripsi singkat mengenai layanan.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="text-white-50 small mb-1">Total Biaya Layanan</div>
                    <h2 class="fw-bold text-success mb-2" id="info_harga" style="letter-spacing: -0.02em;">Rp 0</h2>
                    <div class="d-flex align-items-center justify-content-md-end gap-3 text-white-50 small">
                        <span><i class="bi bi-clock me-1"></i> Estimasi: <strong class="text-white" id="info_estimasi">-</strong></span>
                        <span><i class="bi bi-circle-fill text-success me-1.5" style="font-size: 0.45rem;"></i> Status: <strong class="text-white" id="info_status">Aktif</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="alertPlaceholder"></div>

<!-- Bagian 3: Formulir Pencatatan Transaksi Baru -->
<div class="card content-card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-body p-4">
        <form id="formTambahPesanan">
            <input type="hidden" name="sumber_pesanan" value="online">
            <div class="row g-4">
                
                <!-- Kolom Kiri: Formulir Data Identitas Pelanggan -->
                <div class="col-lg-6 pe-lg-4 border-lg-end">
                    <h5 class="fw-bold mb-4 text-dark d-flex align-items-center">
                        <span class="bg-primary-subtle text-primary p-2 rounded-3 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        Informasi Pelanggan
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Nama Lengkap Pelanggan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Nomor Telepon / HP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_hp" id="no_hp" class="form-control border-start-0 ps-0" placeholder="Contoh: 081234567890 (12-13 digit)" minlength="12" maxlength="13" pattern="[0-9]{12,13}" title="Nomor HP harus berupa angka dengan panjang 12 or 13 digit" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul@gmail.com">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Alamat Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="alamat" id="alamat" class="form-control border-start-0 ps-0" rows="4" placeholder="Contoh: Jl. Merdeka No. 10, Jakarta Pusat"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Formulir Detail Layanan, Waktu Pengerjaan, dan Total Harga -->
                <div class="col-lg-6 ps-lg-4">
                    <h5 class="fw-bold mb-4 text-dark d-flex align-items-center">
                        <span class="bg-success-subtle text-success p-2 rounded-3 me-2 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-cart-fill"></i>
                        </span>
                        Detail Layanan & Pesanan
                    </h5>
                    
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Tanggal Pesan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="tanggal_pesan" id="tanggal_pesan" class="form-control border-start-0 ps-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Tanggal Selesai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Paket Layanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-box-seam"></i></span>
                                <select name="layanan_id" id="layanan_id" class="form-select border-start-0 ps-0" required>
                                    <option value="">Pilih layanan</option>
                                    <?php 
                                    // Looping pilihan paket layanan dan tandai 'selected' jika ID dicantumkan pada parameter URL
                                    foreach ($layanan_options as $l): 
                                        $selected = (isset($_GET['layanan_id']) && intval($_GET['layanan_id']) === intval($l['id'])) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $l['id']; ?>" data-harga="<?php echo $l['harga']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($l['nama_layanan']); ?> (Rp <?php echo number_format($l['harga'], 0, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Total Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success fw-bold">Rp</span>
                                <input type="number" name="total_harga" id="total_harga" class="form-control" placeholder="Contoh: 150000" min="0" required readonly>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Catatan Pesanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-pencil-square"></i></span>
                                <textarea name="catatan" id="catatan" class="form-control border-start-0 ps-0" rows="4" placeholder="Contoh: Laptop lambat, minta backup data terlebih dahulu..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                <a href="dashboard.php?page=pesanan" class="btn btn-light px-4 py-2 border rounded-3 fw-semibold">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" id="btnSimpanPesanan" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAksesTerbatas" tabindex="-1" aria-labelledby="modalAksesTerbatasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden" style="background: #ffffff;">
            <div class="modal-header bg-warning py-3 border-0 text-dark d-flex align-items-center">
                <h5 class="modal-title fw-bold" id="modalAksesTerbatasLabel">
                    <i class="bi bi-shield-slash-fill me-2"></i>Akses Terbatas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-warning mb-3 d-inline-flex align-items-center justify-content-center" style="font-size: 3.5rem; width: 80px; height: 80px; background: #fff9db; border-radius: 50%;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Area Khusus Admin!</h4>
                <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 0.92rem;">
                    Maaf, Anda tidak memiliki otoritas Administrator untuk melakukan tindakan ini. Halaman dan data ini dikunci untuk mencegah perubahan data yang tidak sah. Silakan login sebagai <strong>Admin</strong> untuk melanjutkan.
                </p>
            </div>
            <div class="modal-footer p-3 bg-light border-0 d-flex justify-content-center">
                <button type="button" class="btn btn-warning px-4 text-dark fw-bold rounded-3" data-bs-dismiss="modal" style="border: none;">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<div id="tambah-pesanan-metadata" class="d-none"
     data-user-role="<?php echo $_SESSION['role']; ?>"
     data-layanan-id-url="<?php echo isset($_GET['layanan_id']) ? intval($_GET['layanan_id']) : ''; ?>">
</div>

<script src="assets/js/tambah-pesanan.js?v=20260715"></script>