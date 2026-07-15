<?php
// Proteksi: halaman ini hanya boleh di-include jika variabel $layanan sudah didefinisikan sebelumnya
if (!isset($layanan)) {
    exit('Akses langsung tidak diizinkan.');
}

// Daftar keunggulan paket layanan (sebagai data pendukung/statis untuk mempercantik UI)
$keunggulan = [
    "Teknisi profesional & bersertifikat resmi",
    "Peralatan & suku cadang berkualitas tinggi",
    "Jaminan pengerjaan tepat waktu & rapi",
    "Dukungan konsultasi gratis pasca-layanan"
];

// Tahapan/langkah pengerjaan layanan (sebagai data pendukung/statis)
$tahapan = [
    "Analisis & diagnosa awal secara menyeluruh untuk mengidentifikasi masalah",
    "Eksekusi perbaikan or pengerjaan layanan oleh tim ahli kami",
    "Pengujian kualitas akhir (Quality Control) sebelum diserahkan kembali kepada Anda"
];

// Menetapkan masa garansi dinamis berdasarkan kata kunci nama layanan yang dipilih
$garansi = "Garansi 30 Hari kalender penuh";
if (stripos($layanan['nama_layanan'], 'desain') !== false) {
    $garansi = "Revisi desain sebanyak 3 kali secara gratis";
} elseif (stripos($layanan['nama_layanan'], 'konsultasi') !== false) {
    $garansi = "Gratis 14 hari konsultasi lanjutan purna-jasa";
}
?>

<div class="px-2">
    <!-- Bagian 1: Deskripsi Layanan -->
    <div class="mb-5">
        <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Tentang Layanan</h6>
        <p class="text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.7; text-align: justify;">
            <?php echo htmlspecialchars($layanan['deskripsi']); ?>
        </p>
    </div>

    <hr class="my-4 text-muted opacity-25">

    <!-- Bagian 2: Parameter Rincian (Harga, Estimasi, Garansi, Status) -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Harga Mulai</h6>
            <div class="text-primary fw-bold" style="font-size: 1.45rem; letter-spacing: -0.01em;">
                Rp <?php echo number_format($layanan['harga'], 0, ',', '.'); ?>
            </div>
        </div>
        <div class="col-md-3">
            <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Estimasi Waktu</h6>
            <div class="text-dark fw-semibold" style="font-size: 1.05rem; margin-top: 4px;">
                <?php echo htmlspecialchars($layanan['estimasi_pengerjaan']); ?>
            </div>
        </div>
        <div class="col-md-3">
            <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Garansi Layanan</h6>
            <div class="text-dark fw-semibold" style="font-size: 1.05rem; margin-top: 4px;">
                <?php echo htmlspecialchars($garansi); ?>
            </div>
        </div>
        <div class="col-md-3">
            <h6 class="text-uppercase text-muted fw-bold mb-2 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Status Pengerjaan</h6>
            <div class="d-flex align-items-center text-success fw-semibold" style="font-size: 1.05rem; margin-top: 6px;">
                <span class="status-pulse-dot me-2"></span>
                Aktif
            </div>
        </div>
    </div>

    <hr class="my-4 text-muted opacity-25">

    <!-- Bagian 3: List Keunggulan (Looping Data Keunggulan Statis) -->
    <div class="mb-5">
        <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Keunggulan</h6>
        <div class="row g-3">
            <?php foreach ($keunggulan as $item): ?>
                <div class="col-md-6 d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="text-success me-2.5 flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                    </svg>
                    <span class="text-secondary" style="font-size: 0.9rem;"><?php echo htmlspecialchars($item); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <hr class="my-4 text-muted opacity-25">

    <!-- Bagian 4: Alur Proses Pengerjaan (Looping Data Tahapan Statis) -->
    <div>
        <h6 class="text-uppercase text-muted fw-bold mb-3 small" style="font-size: 0.72rem; letter-spacing: 0.08em;">Proses Pengerjaan</h6>
        <ul class="list-unstyled mb-0">
            <?php foreach ($tahapan as $idx => $step): ?>
                <li class="d-flex align-items-start mb-2.5">
                    <span class="text-primary fw-bold me-3" style="font-size: 0.95rem; line-height: 1.5;">0<?php echo $idx + 1; ?>.</span>
                    <span class="text-secondary" style="font-size: 0.9rem; line-height: 1.5;"><?php echo htmlspecialchars($step); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>