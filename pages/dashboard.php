<?php
// Cek koneksi db: pastikan database terkoneksi
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/config/koneksi.php';
}

// 1. Mengkalkulasi jumlah pesanan berdasarkan statusnya masing-masing
// Hitung seluruh total pesanan tanpa filter status
$total_pesanan_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$total_pesanan = $total_pesanan_query ? mysqli_fetch_assoc($total_pesanan_query)['total'] : 0;

// Hitung pesanan baru masuk (status 'baru')
$baru_pesanan_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status_pesanan = 'baru'");
$baru_pesanan = $baru_pesanan_query ? mysqli_fetch_assoc($baru_pesanan_query)['total'] : 0;

// Hitung pesanan yang sedang dikerjakan (status 'diproses')
$proses_pesanan_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status_pesanan = 'diproses'");
$proses_pesanan = $proses_pesanan_query ? mysqli_fetch_assoc($proses_pesanan_query)['total'] : 0;

// Hitung pesanan yang telah rampung (status 'selesai')
$selesai_pesanan_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status_pesanan = 'selesai'");
$selesai_pesanan = $selesai_pesanan_query ? mysqli_fetch_assoc($selesai_pesanan_query)['total'] : 0;

// Hitung pesanan yang dibatalkan admin/customer (status 'dibatalkan')
$dibatalkan_pesanan_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status_pesanan = 'dibatalkan'");
$dibatalkan_pesanan = $dibatalkan_pesanan_query ? mysqli_fetch_assoc($dibatalkan_pesanan_query)['total'] : 0;

// 2. Logika penentuan ucapan selamat (salam) berdasarkan jam saat ini
date_default_timezone_set('Asia/Jakarta');
$hour = date('H');
$greeting = 'Selamat Malam';
if ($hour >= 5 && $hour < 11) {
    $greeting = 'Selamat Pagi';
} elseif ($hour >= 11 && $hour < 15) {
    $greeting = 'Selamat Siang';
} elseif ($hour >= 15 && $hour < 18) {
    $greeting = 'Selamat Sore';
}

// Menetapkan parameter tanggal hari ini dan bulan berjalan
$hari_ini = date('Y-m-d');
$bulan_ini = date('Y-m');

// 3. Menghitung total omset pendapatan bersih hari ini (kecuali transaksi yang dibatalkan)
$pendapatan_hari_ini_query = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total FROM pesanan WHERE DATE(tanggal_pesan) = '$hari_ini' AND status_pesanan != 'dibatalkan'");
$pendapatan_hari_ini = ($pendapatan_hari_ini_query && ($row = mysqli_fetch_assoc($pendapatan_hari_ini_query)) && $row['total']) ? $row['total'] : 0;

// 4. Menghitung estimasi omset pendapatan kotor bulan ini
$pendapatan_bulan_ini_query = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total FROM pesanan WHERE DATE_FORMAT(tanggal_pesan, '%Y-%m') = '$bulan_ini' AND status_pesanan != 'dibatalkan'");
$pendapatan_bulan_ini = ($pendapatan_bulan_ini_query && ($row = mysqli_fetch_assoc($pendapatan_bulan_ini_query)) && $row['total']) ? $row['total'] : 0;

// 5. Menentukan paket layanan yang paling sering dipesan oleh pelanggan (terpopuler)
$terpopuler_query = mysqli_query($koneksi, "SELECT l.nama_layanan, COUNT(p.id) as qty FROM pesanan p JOIN layanan l ON p.layanan_id = l.id GROUP BY p.layanan_id ORDER BY qty DESC LIMIT 1");
$layanan_terpopuler = ($terpopuler_query && mysqli_num_rows($terpopuler_query) > 0) ? mysqli_fetch_assoc($terpopuler_query)['nama_layanan'] : 'Belum Ada Data';

// 6. Menghitung jumlah unik pelanggan yang pernah bertransaksi
$pelanggan_aktif_query = mysqli_query($koneksi, "SELECT COUNT(DISTINCT pelanggan_id) AS total FROM pesanan");
$pelanggan_aktif = $pelanggan_aktif_query ? mysqli_fetch_assoc($pelanggan_aktif_query)['total'] : 0;

// Hitung persentase pengerjaan pesanan selesai dibandingkan seluruh pesanan masuk
$persentase = $total_pesanan > 0 ? round(($selesai_pesanan / $total_pesanan) * 100) : 0;

// 7. Mengambil histori data 7 hari terakhir untuk grafik volume pesanan
$tren_dates = [];
$tren_counts = [];
for ($i = 6; $i >= 0; $i--) {
    // Kurangi tanggal sebanyak $i hari dari hari ini
    $d = date('Y-m-d', strtotime("-$i days"));
    $formatted_d = date('d M', strtotime($d)); // Format tanggal menjadi DD Bulan (misal: 15 Jul)
    
    // Hitung jumlah pesanan masuk pada tanggal tersebut
    $query_tren = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal_pesan) = '$d'");
    $total_d = $query_tren ? mysqli_fetch_assoc($query_tren)['total'] : 0;

    $tren_dates[] = $formatted_d;
    $tren_counts[] = $total_d;
}

// 8. Query untuk memanggil 5 data transaksi pesanan terbaru masuk ke sistem
$latest_pesanan_query = mysqli_query($koneksi, "SELECT p.kode_pesanan, pl.nama_pelanggan, l.nama_layanan, p.status_pesanan 
                                            FROM pesanan p 
                                            JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                                            JOIN layanan l ON p.layanan_id = l.id 
                                            ORDER BY p.id DESC LIMIT 5");

// 9. Query untuk memanggil 4 data teratas untuk komponen linimasa (timeline) aktivitas sistem
$timeline_query = mysqli_query($koneksi, "SELECT p.kode_pesanan, pl.nama_pelanggan, p.status_pesanan, p.created_at
                                        FROM pesanan p 
                                        JOIN pelanggan pl ON p.pelanggan_id = pl.id 
                                        ORDER BY p.id DESC LIMIT 4");
?>

<link href="assets/css/dashboard.css" rel="stylesheet">

<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-7 col-sm-12">
            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Panel Kendali Utama</small>
            <h2 class="fw-bold mb-1"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin'); ?>!</h2>
            <p class="mb-0 text-white-50">Selamat datang kembali. Dashboard ini dirancang untuk membantu Anda mengelola pesanan pelanggan, memantau progres pengerjaan, serta memperoleh gambaran menyeluruh mengenai aktivitas layanan secara real-time.</p>
        </div>
        <div class="col-md-5 col-sm-12 text-md-end mt-3 mt-md-0">
            <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill me-2 border border-success-subtle">
                <i class="bi bi-shield-fill-check me-1"></i> Operasional: Normal
            </span>
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 me-1"></i> <?php echo date('d M Y'); ?>
            </span>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Total Pesanan</small>
                    <h3 class="fw-bold mb-0"><?php echo $total_pesanan; ?></h3>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;"><i class="bi bi-arrow-up-right text-success me-1"></i>Akumulasi transaksi</small>
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
                    <h3 class="fw-bold mb-0"><?php echo $baru_pesanan; ?></h3>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;"><i class="bi bi-bell-fill text-primary me-1"></i>Antrean teknisi</small>
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
                    <h3 class="fw-bold mb-0"><?php echo $proses_pesanan; ?></h3>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;"><i class="bi bi-hourglass-split text-warning me-1"></i>Pengerjaan aktif</small>
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
                    <h3 class="fw-bold mb-0"><?php echo $selesai_pesanan; ?></h3>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;"><i class="bi bi-check-circle-fill text-success me-1"></i>Siap diambil</small>
                </div>
                <div class="stat-icon bg-success-soft">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        <div class="card content-card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;"><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Tren Volume Pesanan (7 Hari Terakhir)</h5>
                <div style="height: 250px; position: relative;">
                    <canvas id="chartVolume"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card content-card h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Distribusi Status Pengerjaan</h5>
                        <div style="height: 220px; position: relative;" class="d-flex justify-content-center">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card content-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;"><i class="bi bi-clock-history me-2 text-primary"></i>5 Transaksi Terbaru</h5>
                            <a href="dashboard.php?page=pesanan" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size: 0.75rem;">Semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-sm mb-0" style="font-size: 0.82rem;">
                                <thead>
                                    <tr class="table-light text-secondary">
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($latest_pesanan_query && mysqli_num_rows($latest_pesanan_query) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($latest_pesanan_query)):
                                            $status_badge = 'bg-primary';
                                            if ($row['status_pesanan'] === 'diproses') {
                                                $status_badge = 'bg-warning text-dark';
                                            } elseif ($row['status_pesanan'] === 'selesai') {
                                                $status_badge = 'bg-success';
                                            } elseif ($row['status_pesanan'] === 'dibatalkan') {
                                                $status_badge = 'bg-danger';
                                            }
                                        ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row['kode_pesanan']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_layanan']); ?></td>
                                                <td><span class="badge <?php echo $status_badge; ?>" style="font-size: 0.68rem; padding: 4px 6px;"><?php echo ucfirst(htmlspecialchars($row['status_pesanan'])); ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Penyelesaian Pekerjaan</h5>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-semibold text-muted"><?php echo $selesai_pesanan; ?> dari <?php echo $total_pesanan; ?> pesanan telah selesai (<?php echo $persentase; ?>%)</span>
                    <span class="small fw-bold text-success"><?php echo $persentase; ?>%</span>
                </div>
                <div class="progress" style="height: 12px; border-radius: 6px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $persentase; ?>%" aria-valuenow="<?php echo $persentase; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="card content-card mb-4 text-center p-3">
            <div class="card-body py-2">
                <h6 class="text-uppercase fw-bold text-secondary mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Waktu & Tanggal</h6>
                <div id="realtime-clock" class="fw-bold fs-4 text-primary mb-1"></div>
                <div id="realtime-date" class="small text-muted mb-3"></div>
                <div class="d-flex align-items-center justify-content-center gap-2 pt-2 border-top border-light">
                    <span class="text-warning fs-5"><i class="bi bi-sun-fill"></i></span>
                    <span class="small text-muted">Banda Aceh: 29°C, Cerah Berawan</span>
                </div>
            </div>
        </div>

        <div class="card content-card">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;"><i class="bi bi-activity me-2 text-primary"></i>Aktivitas Sistem</h5>
                <div class="timeline" style="border-left: 2px solid var(--soft-border); margin-left: 10px; padding-left: 20px;">
                    <?php if ($timeline_query && mysqli_num_rows($timeline_query) > 0): ?>
                        <?php 
                        // Looping linimasa aktivitas untuk me-render status log pesanan
                        while ($row = mysqli_fetch_assoc($timeline_query)):
                            $status = $row['status_pesanan'];
                            $kode = htmlspecialchars($row['kode_pesanan']);
                            $nama = htmlspecialchars($row['nama_pelanggan']);
                            $time = date('d/m H:i', strtotime($row['created_at']));

                            // Menentukan format teks penjelasan berdasarkan status pesanan yang didapat
                            $activity_text = "";
                            if ($status === 'baru') {
                                $activity_text = "Penerimaan pesanan baru <strong>$kode</strong> atas nama <strong>$nama</strong>.";
                            } elseif ($status === 'diproses') {
                                $activity_text = "Unit pesanan <strong>$kode</strong> ($nama) sedang dikerjakan oleh teknisi.";
                            } elseif ($status === 'selesai') {
                                $activity_text = "Pengerjaan unit pesanan <strong>$kode</strong> ($nama) telah selesai.";
                            } elseif ($status === 'dibatalkan') {
                                $activity_text = "Pembatalan transaksi pesanan <strong>$kode</strong> ($nama).";
                            }
                        ?>
                            <div class="timeline-item status-<?php echo $status; ?>">
                                <span class="d-block text-muted" style="font-size: 0.7rem;"><?php echo $time; ?></span>
                                <span class="d-block text-dark" style="font-size: 0.8rem; line-height: 1.4;"><?php echo $activity_text; ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted small py-3">Belum ada aktivitas transaksi tercatat.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div id="dashboard-metadata" class="d-none"
    data-tren-dates='<?php echo json_encode($tren_dates); ?>'
    data-tren-counts='<?php echo json_encode($tren_counts); ?>'
    data-baru-pesanan="<?php echo $baru_pesanan; ?>"
    data-proses-pesanan="<?php echo $proses_pesanan; ?>"
    data-selesai-pesanan="<?php echo $selesai_pesanan; ?>"
    data-dibatalkan-pesanan="<?php echo $dibatalkan_pesanan; ?>">
</div>

<script src="assets/js/dashboard.js"></script>