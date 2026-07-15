document.addEventListener("DOMContentLoaded", function() {
    
    // Inisialisasi widget jam digital dan tanggal hari ini secara real-time
    function updateClock() {
        const now = new Date();
        // Mengubah format waktu ke zona WIB (Waktu Indonesia Barat)
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        // Mengubah format tanggal lengkap berbahasa Indonesia
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        
        const clockEl = document.getElementById('realtime-clock');
        const dateEl = document.getElementById('realtime-date');
        
        // Render waktu dan tanggal ke elemen HTML
        if (clockEl) clockEl.textContent = timeStr;
        if (dateEl) dateEl.textContent = dateStr;
    }
    // Lakukan pembaruan jam setiap 1 detik
    setInterval(updateClock, 1000);
    updateClock();

    // Membaca data statistik dari metadata element HTML untuk pembuatan grafik Chart.js
    const metadata = $('#dashboard-metadata');
    if (metadata.length) {
        const trenDates = metadata.data('tren-dates');
        const trenCounts = metadata.data('tren-counts');
        const baruPesanan = parseInt(metadata.data('baru-pesanan'));
        const prosesPesanan = parseInt(metadata.data('proses-pesanan'));
        const selesaiPesanan = parseInt(metadata.data('selesai-pesanan'));
        const dibatalkanPesanan = parseInt(metadata.data('dibatalkan-pesanan'));

        // Pembuatan grafik garis (Line Chart) untuk tren jumlah pesanan 7 hari terakhir
        const ctxVolume = document.getElementById('chartVolume');
        if (ctxVolume) {
            new Chart(ctxVolume.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trenDates,
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: trenCounts,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 2.5,
                        fill: true, // Berikan warna gradasi transparan di bawah garis
                        tension: 0.35, // Membuat garis melengkung halus
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } // Sembunyikan label keterangan atas
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#94a3b8' },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Pembuatan grafik lingkaran (Doughnut Chart) untuk persentase status pesanan
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus) {
            new Chart(ctxStatus.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Baru', 'Diproses', 'Selesai', 'Dibatalkan'],
                    datasets: [{
                        data: [
                            baruPesanan,
                            prosesPesanan,
                            selesaiPesanan,
                            dibatalkanPesanan
                        ],
                        // Sesuaikan warna status dengan standar warna UI web
                        backgroundColor: ['#2563eb', '#f59e0b', '#16a34a', '#dc2626'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, color: '#475569', font: { size: 10 } }
                        }
                    },
                    cutout: '70%' // Mengatur ukuran lubang tengah doughnut
                }
            });
        }
    }
});