<?php
// Cek koneksi db: pastikan koneksi database tersedia sebelum memuat data
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/config/koneksi.php';
}

// Ambil pilihan paket layanan dari database untuk mengisi dropdown pencatatan pesanan manual
$stmt_lay = mysqli_prepare($koneksi, "SELECT id, nama_layanan, harga FROM layanan ORDER BY nama_layanan ASC");
mysqli_stmt_execute($stmt_lay);
$res_lay = mysqli_stmt_get_result($stmt_lay);
$layanan_options = [];
if ($res_lay) {
    while ($row = mysqli_fetch_assoc($res_lay)) {
        $layanan_options[] = $row;
    }
}
?>

<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-2 text-white">
                Manajemen Pesanan 
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'): ?>
                    <span class="badge ms-2" style="font-size: 0.72rem; vertical-align: middle; background: rgba(239, 68, 68, 0.25); color: #fecaca; border: 1px solid rgba(239, 68, 68, 0.4); letter-spacing: 0.03em;"><i class="bi bi-shield-lock-fill me-1"></i> Area Khusus Admin</span>
                <?php endif; ?>
            </h2>
            <p class="mb-0">Kelola pesanan layanan pelanggan secara mudah dan terstruktur.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-light rounded-3 fw-semibold" id="btnTambahPesananManualTrigger">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pesanan
            </button>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success border mb-3 alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger border mb-3 alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'): ?>
<div class="card border-0 rounded-4 mb-4" style="background: rgba(239, 68, 68, 0.04); border-left: 5px solid #ef4444 !important; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.05);">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="p-3 bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center" style="font-size: 1.5rem; width: 50px; height: 50px; flex-shrink: 0; box-shadow: 0 0 15px rgba(239, 68, 68, 0.15);">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold text-danger mb-1" style="letter-spacing: -0.01em;">Akses Terbatas: Panel Administrator</h5>
                <p class="text-secondary mb-0 small" style="line-height: 1.6;">
                    Halaman ini merupakan area kerja internal khusus bagi <strong>Administrator / Operator</strong> untuk mencatat, mengedit, dan memantau status pemesanan. Pengguna umum (pelanggan) tidak diperkenankan melakukan penginputan langsung pada area tabel di halaman ini.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card content-card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4">Manajemen Pesanan Layanan</h5>

        <div class="table-responsive">
            <table id="tabelPesanan" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Tanggal Pesan</th>
                        <th>Total Harga</th>
                        <th>Sumber</th>
                        <th>Status</th>
                        <th class="text-center no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk memanggil data pesanan lengkap dengan nama pelanggan dan nama layanan (menggunakan JOIN)
                    $stmt_pes = mysqli_prepare($koneksi, "SELECT p.*, pl.nama_pelanggan, l.nama_layanan 
                                   FROM pesanan p
                                   JOIN pelanggan pl ON p.pelanggan_id = pl.id
                                   JOIN layanan l ON p.layanan_id = l.id
                                   ORDER BY p.id DESC");
                    mysqli_stmt_execute($stmt_pes);
                    $pesanan_query = mysqli_stmt_get_result($stmt_pes);
                    $no = 1;

                    if ($pesanan_query && mysqli_num_rows($pesanan_query) > 0) {
                        // Looping baris data pesanan hasil query dari database
                        while ($row = mysqli_fetch_assoc($pesanan_query)) {
                            // Mengatur kelas CSS warna badge status berdasarkan status transaksi pesanan
                            $status_class = 'status-baru';
                            if ($row['status_pesanan'] === 'diproses') {
                                $status_class = 'status-diproses';
                            } elseif ($row['status_pesanan'] === 'selesai') {
                                $status_class = 'status-selesai';
                            } elseif ($row['status_pesanan'] === 'dibatalkan') {
                                $status_class = 'status-dibatalkan';
                            }
                            
                            // Format tanggal pesan (DD-MM-YYYY) dan harga (Rp dengan pemisah ribuan titik)
                            $formatted_date = date('d-m-Y', strtotime($row['tanggal_pesan']));
                            $formatted_price = "Rp " . number_format($row['total_harga'], 0, ',', '.');

                            // Mengatur visual badge media/sumber masuknya pemesanan
                            $sumber_val = isset($row['sumber_pesanan']) ? $row['sumber_pesanan'] : 'online';
                            $sumber_badge = '';
                            if ($sumber_val === 'telepon') {
                                $sumber_badge = '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.75rem;"><i class="bi bi-telephone-fill me-1 text-success"></i> ☎️ Telepon</span>';
                            } elseif ($sumber_val === 'walk_in') {
                                $sumber_badge = '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.75rem;"><i class="bi bi-person-fill me-1 text-info"></i> 🚶 Walk In</span>';
                            } else {
                                $sumber_badge = '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5" style="font-size: 0.75rem;"><i class="bi bi-globe me-1 text-primary"></i> 🌐 Online</span>';
                            }
                            ?>
                            <tr data-id="<?php echo $row['id']; ?>">
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['kode_pesanan']); ?></strong></td>
                                <td class="col-nama-pelanggan"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                <td class="col-nama-layanan"><?php echo htmlspecialchars($row['nama_layanan']); ?></td>
                                <td class="col-tanggal-pesan"><?php echo $formatted_date; ?></td>
                                <td class="col-total-harga"><?php echo $formatted_price; ?></td>
                                <td class="col-sumber-pesanan"><?php echo $sumber_badge; ?></td>
                                <td class="col-status-pesanan">
                                    <span class="badge-status <?php echo $status_class; ?>"><?php echo ucfirst(htmlspecialchars($row['status_pesanan'])); ?></span>
                                </td>
                                <!-- Tombol aksi ubah dan hapus data pesanan -->
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary action-btn edit-btn" data-id="<?php echo $row['id']; ?>" title="Edit Pesanan">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger action-btn delete-btn" data-id="<?php echo $row['id']; ?>" data-kode="<?php echo htmlspecialchars($row['kode_pesanan']); ?>" title="Hapus Pesanan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahPesananManual" tabindex="-1" aria-labelledby="modalTambahManualLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="modalTambahManualLabel"><i class="bi bi-journal-plus me-2"></i>Pencatatan Pesanan Manual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahPesananManual">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Gunakan formulir ini untuk mencatat pesanan pelanggan yang melakukan pemesanan melalui telepon atau datang langsung ke lokasi.</p>
                    <div id="alertPlaceholderTambahManual"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Nama Pelanggan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama_pelanggan" id="manual_nama_pelanggan" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Nomor HP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_hp" id="manual_no_hp" class="form-control border-start-0 ps-0" placeholder="Contoh: 081234567890 (12-13 digit)" minlength="12" maxlength="13" pattern="[0-9]{12,13}" title="Nomor HP harus berupa angka dengan panjang 12 atau 13 digit" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Email Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="manual_email" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul@gmail.com" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Sumber Pesanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-info-square"></i></span>
                                <select name="sumber_pesanan" id="manual_sumber_pesanan" class="form-select border-start-0 ps-0" required>
                                    <option value="telepon" selected>☎️ Telepon</option>
                                    <option value="walk_in">🚶 Datang Langsung (Walk In)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Layanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-box-seam"></i></span>
                                <select name="layanan_id" id="manual_layanan_id" class="form-select border-start-0 ps-0" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    <?php foreach ($layanan_options as $l): ?>
                                        <option value="<?php echo $l['id']; ?>" data-harga="<?php echo $l['harga']; ?>">
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
                                <input type="number" name="total_harga" id="manual_total_harga" class="form-control border-start-0 ps-0" min="0" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Tanggal Pesan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="tanggal_pesan" id="manual_tanggal_pesan" class="form-control border-start-0 ps-0" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Estimasi Selesai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="tanggal_selesai" id="manual_tanggal_selesai" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Status Pesanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-info-circle"></i></span>
                                <select name="status_pesanan" id="manual_status_pesanan" class="form-select border-start-0 ps-0" required>
                                    <option value="baru" selected>Baru</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Alamat Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="alamat" id="manual_alamat" class="form-control border-start-0 ps-0" rows="3" placeholder="Masukkan alamat pelanggan (opsional)"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Catatan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-pencil-square"></i></span>
                                <textarea name="catatan" id="manual_catatan" class="form-control border-start-0 ps-0" rows="3" placeholder="Masukkan detail perbaikan / catatan pelanggan..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light border-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbahPesanan" tabindex="-1" aria-labelledby="modalUbahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-warning text-dark py-3 border-0">
                <h5 class="modal-title fw-bold" id="modalUbahLabel"><i class="bi bi-pencil-square me-2"></i>Ubah Data Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUbahPesanan">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div id="alertPlaceholderEdit"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Kode Pesanan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-qr-code"></i></span>
                                <input type="text" id="edit_kode_pesanan" class="form-control border-start-0 ps-0 bg-light fw-bold text-secondary" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Pelanggan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama_pelanggan" id="edit_nama_pelanggan" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Nomor HP <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_hp" id="edit_no_hp" class="form-control border-start-0 ps-0" placeholder="Contoh: 081234567890 (12-13 digit)" minlength="12" maxlength="13" pattern="[0-9]{12,13}" title="Nomor HP harus berupa angka dengan panjang 12 atau 13 digit" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Email Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="edit_email" class="form-control border-start-0 ps-0" placeholder="Contoh: azkarul@gmail.com" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Layanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-box-seam"></i></span>
                                <select name="layanan_id" id="edit_layanan_id" class="form-select border-start-0 ps-0" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    <?php foreach ($layanan_options as $l): ?>
                                        <option value="<?php echo $l['id']; ?>" data-harga="<?php echo $l['harga']; ?>">
                                            <?php echo htmlspecialchars($l['nama_layanan']); ?> (Rp <?php echo number_format($l['harga'], 0, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Tanggal Pesan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="tanggal_pesan" id="edit_tanggal_pesan" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Tanggal Selesai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" class="form-control border-start-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Total Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success fw-bold">Rp</span>
                                <input type="number" name="total_harga" id="edit_total_harga" class="form-control border-start-0 ps-0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Status Pesanan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-info-circle"></i></span>
                                <select name="status_pesanan" id="edit_status_pesanan" class="form-select border-start-0 ps-0" required>
                                    <option value="baru">Baru</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Alamat Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-geo-alt"></i></span>
                                <textarea name="alamat" id="edit_alamat" class="form-control border-start-0 ps-0" rows="3" placeholder="Masukkan alamat pelanggan"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Catatan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted align-self-start py-2 border-end-0"><i class="bi bi-pencil-square"></i></span>
                                <textarea name="catatan" id="edit_catatan" class="form-control border-start-0 ps-0" rows="3" placeholder="Masukkan detail perbaikan / catatan pelanggan..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light border-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-save me-1"></i>Perbarui Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="modalHapusLabel"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="alertPlaceholderHapus"></div>
                <div class="text-danger mb-3" style="font-size: 3rem;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 class="fw-bold">Konfirmasi Hapus Pesanan</h5>
                <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus pesanan ini? Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer p-3 bg-light border-0 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmDelete" class="btn btn-danger px-4">Ya, Hapus</button>
            </div>
        </div>
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
                <div class="text-warning mb-3 d-inline-flex align-items-center justify-content-center" style="font-size: 3.5rem; width: 80px; height: 80px; background: #fff9db; border-radius: 50%; animation: pulseWarning 2s infinite;">
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

<div id="pesanan-metadata" class="d-none" data-user-role="<?php echo $_SESSION['role']; ?>"></div>

<script src="assets/js/daftar-pesanan.js"></script>