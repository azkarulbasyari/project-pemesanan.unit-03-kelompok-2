<?php
// Cek koneksi db: pastikan database terkoneksi sebelum membaca paket layanan
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/config/koneksi.php';
}

// Query untuk mengambil seluruh data paket layanan yang didaftarkan di sistem
$query_layanan = mysqli_query($koneksi, "SELECT * FROM layanan ORDER BY id ASC");
$layanan_list = [];
if ($query_layanan) {
    while ($row = mysqli_fetch_assoc($query_layanan)) {
        $layanan_list[] = $row;
    }
}

// Map warna badge kategori untuk membedakan gaya visual tiap kategori
$badge_color_map = [
    'teknologi'          => 'badge-tech',
    'desain'             => 'badge-design',
    'konsultasi'          => 'badge-consult',
    'upgrade hardware'   => 'badge-hardware',
    'perbaikan hardware' => 'badge-hardware',
    'web development'    => 'badge-web'
];
?>

<link href="assets/css/layanan.css" rel="stylesheet">

<div class="page-header mb-4 text-white">
    <h2 class="fw-bold mb-2 text-white">Data Layanan</h2>
    <p class="text-white-50 mb-0">Daftar layanan berkualitas tinggi yang kami sediakan untuk Anda.</p>
</div>

<div class="d-flex justify-content-center mb-5 animate-card" style="animation-delay: 0.05s;">
    <div class="position-relative" style="min-width: 290px; max-width: 400px; width: 100%;">
        <div class="custom-filter-capsule shadow-sm d-flex align-items-center px-3 py-2 bg-white rounded-pill border" style="border-color: #cbd5e1 !important; transition: all 0.3s ease;">
            <span class="text-primary me-2 d-flex align-items-center justify-content-center" style="font-size: 1.15rem; width: 32px; height: 32px; background: #eff6ff; border-radius: 50%;">
                <i class="bi bi-funnel-fill"></i>
            </span>
            <div class="flex-grow-1">
                <label class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.58rem; letter-spacing: 0.08em; margin-bottom: 1px;">Kategori Layanan</label>
                <select id="filterKategori" class="form-select border-0 p-0 fw-semibold text-dark" style="font-size: 0.88rem; background-image: none; box-shadow: none; cursor: pointer; background: transparent;">
                    <option value="" style="font-weight: 600;">✨ Tampilkan Semua Kategori</option>
                    <?php 
                    // Logika mengelompokkan kategori unik (unique) dari list layanan untuk opsi filter dropdown
                    $kategori_list = [];
                    foreach ($layanan_list as $l) {
                        if (!in_array($l['kategori'], $kategori_list)) {
                            $kategori_list[] = $l['kategori'];
                        }
                    }
                    foreach ($kategori_list as $kat):
                    ?>
                        <option value="<?php echo htmlspecialchars($kat); ?>">📁 <?php echo htmlspecialchars($kat); ?></option>
                     <?php endforeach; ?>
                </select>
            </div>
            <span class="text-secondary ms-2" style="pointer-events: none;"><i class="bi bi-chevron-down"></i></span>
        </div>
    </div>
</div>

<!-- Bagian 1: Grid Kartu Minimal Layanan (Menampilkan list layanan dalam bentuk kartu interaktif) -->
<div class="row g-4">
    <?php 
    $index = 0;
    foreach ($layanan_list as $l): 
        $id_layanan = intval($l['id']);
        $kat_lower = strtolower($l['kategori']);
        $badge_class = isset($badge_color_map[$kat_lower]) ? $badge_color_map[$kat_lower] : 'badge-default';
    ?>
        <div class="col-xl-3 col-md-6 layanan-card-item animate-card" data-kategori="<?php echo htmlspecialchars($l['kategori']); ?>" style="animation-delay: <?php echo ($index + 1) * 0.08; ?>s;">
            <div class="card service-card-minimal h-100 p-4">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge-category <?php echo $badge_class; ?>"><?php echo htmlspecialchars($l['kategori']); ?></span>
                            <span class="d-inline-flex align-items-center text-success" style="font-size: 0.72rem; font-weight: 600;">
                                <span class="status-pulse-dot me-2"></span>
                                Aktif
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-2 card-title-name" style="font-size: 1.15rem; letter-spacing: -0.01em;"><?php echo htmlspecialchars($l['nama_layanan']); ?></h5>
                        <p class="text-secondary" style="font-size: 0.85rem; line-height: 1.6; min-height: 60px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 20px; text-align: justify;">
                            <?php echo htmlspecialchars($l['deskripsi']); ?>
                        </p>
                    </div>

                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3 text-secondary" style="font-size: 0.8rem;">
                            <span>Estimasi Pengerjaan</span>
                            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($l['estimasi_pengerjaan']); ?></span>
                        </div>
                        
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color: #f1f5f9 !important;">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Harga Mulai</span>
                                <span class="fw-semibold text-dark" style="font-size: 1.05rem; letter-spacing: -0.01em;">Rp <?php echo number_format($l['harga'], 0, ',', '.'); ?></span>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3 py-1.5 rounded-pill fw-semibold view-detail-btn" data-id="<?php echo $l['id']; ?>" style="font-size: 0.76rem; border: none; box-shadow: 0 4px 10px rgba(37,99,235,0.08); transition: all 0.2s ease;">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
    $index++;
    endforeach; 
    ?>
</div>

<!-- Bagian 2: Tabel Rincian Data Layanan (Untuk pencarian data secara tabular struktural) -->
<div class="card content-card mt-4 shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3 text-dark">Tabel Layanan</h5>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th>Nama Layanan</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Estimasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    foreach ($layanan_list as $l): 
                    ?>
                        <tr class="layanan-table-row" data-kategori="<?php echo htmlspecialchars($l['kategori']); ?>">
                            <td><?php echo $no++; ?></td>
                            <td class="fw-semibold text-dark table-service-name"><?php echo htmlspecialchars($l['nama_layanan']); ?></td>
                            <td><?php echo htmlspecialchars($l['kategori']); ?></td>
                            <td>Rp <?php echo number_format($l['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($l['estimasi_pengerjaan']); ?></td>
                            <td>
                                <span class="d-inline-flex align-items-center text-success small fw-semibold">
                                    <span class="status-pulse-dot me-1.5" style="width: 5px; height: 5px;"></span>
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bagian 3: Modal Bootstrap Detail Layanan (Akan memuat data detail dinamis via AJAX) -->
<div class="modal fade" id="modalDetailLayanan" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden bg-white">
            <div class="modal-header border-0 pb-0 bg-white p-4">
                <div>
                    <span class="text-uppercase text-muted fw-bold d-block mb-1" id="modal_kategori_layanan" style="font-size: 0.72rem; letter-spacing: 0.08em;">Kategori</span>
                    <h3 class="modal-title fw-bold text-dark mb-0" id="modal_nama_layanan" style="letter-spacing: -0.02em;">Nama Layanan</h3>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-white" id="modal_body_layanan_detail">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat data...</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-white border-0 p-4 pt-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal" style="font-size: 0.85rem;">Tutup</button>
                <a href="#" id="modal_btn_pesan" class="btn btn-primary px-4 py-2 rounded-pill text-white fw-bold shadow-sm" style="font-size: 0.85rem;">Pesan Sekarang</a>
            </div>
        </div>
    </div>
</div>

<!-- Bagian 4: Skrip Kontrol Halaman Jasa Layanan -->
<script src="assets/js/layanan.js"></script>