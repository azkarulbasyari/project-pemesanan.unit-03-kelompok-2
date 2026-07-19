<!-- Wadah toast notifikasi melayang di pojok kanan bawah -->
<div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 1150;">
    <div id="globalToast" class="toast custom-premium-toast border-0 position-relative animate-card" role="alert" aria-live="assertive" aria-atomic="true">
        <!-- Bar vertikal penanda warna di sisi kiri toast -->
        <div id="toastAccentBar" class="toast-accent-bar"></div>
        
        <div class="d-flex align-items-center p-3 ps-4">
            <!-- Ikon indikator status -->
            <div id="toastIconWrapper" class="toast-icon-wrapper me-3">
                <span id="toastIcon"></span>
            </div>
            
            <!-- Judul dan pesan detail notifikasi -->
            <div class="flex-grow-1">
                <div id="toastTitle" class="fw-bold text-dark mb-1" style="font-size: 0.85rem; letter-spacing: 0.01em;">NOTIFIKASI</div>
                <div id="toastMessage" class="text-secondary small fw-medium" style="line-height: 1.4;">Pesan notifikasi.</div>
            </div>
            
            <!-- Tombol silang untuk menutup toast -->
            <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close" style="font-size: 0.72rem; box-shadow: none;"></button>
        </div>
    </div>
</div>

<!-- Helper Javascript untuk menampilkan toast notification secara dinamis -->
<script>
    // Ekspos fungsi showToast ke window object agar dapat dipanggil dari skrip AJAX mana pun tanpa refresh
    window.showToast = function(type, message) {
        const toastEl = document.getElementById('globalToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        const toastIconWrapper = document.getElementById('toastIconWrapper');
        const toastAccentBar = document.getElementById('toastAccentBar');
        const toastTitle = document.getElementById('toastTitle');
        
        if (!toastEl || !toastMessage || !toastIcon || !toastIconWrapper || !toastAccentBar || !toastTitle) return;
        
        // Reset kelas kustom sebelumnya agar tidak bertumpuk
        toastIconWrapper.className = 'toast-icon-wrapper me-3';
        toastAccentBar.className = 'toast-accent-bar';
        
        // Atur warna aksen, warna tulisan, judul, dan ikon sesuai jenis respon ('success', 'danger', 'warning', 'info')
        if (type === 'success') {
            toastAccentBar.classList.add('toast-accent-success');
            toastIconWrapper.classList.add('toast-icon-success');
            toastIcon.className = 'bi bi-check-circle-fill';
            toastTitle.className = 'fw-bold text-success mb-1';
            toastTitle.textContent = 'Berhasil';
        } else if (type === 'danger' || type === 'error') {
            toastAccentBar.classList.add('toast-accent-danger');
            toastIconWrapper.classList.add('toast-icon-danger');
            toastIcon.className = 'bi bi-x-circle-fill';
            toastTitle.className = 'fw-bold text-danger mb-1';
            toastTitle.textContent = 'Kesalahan';
        } else if (type === 'warning') {
            toastAccentBar.classList.add('toast-accent-warning');
            toastIconWrapper.classList.add('toast-icon-warning');
            toastIcon.className = 'bi bi-exclamation-triangle-fill';
            toastTitle.className = 'fw-bold text-warning mb-1';
            toastTitle.textContent = 'Peringatan';
        } else {
            toastAccentBar.classList.add('toast-accent-info');
            toastIconWrapper.classList.add('toast-icon-info');
            toastIcon.className = 'bi bi-info-circle-fill';
            toastTitle.className = 'fw-bold text-primary mb-1';
            toastTitle.textContent = 'Informasi';
        }
        
        // Mengisi teks pesan detail ke dalam elemen notifikasi
        toastMessage.textContent = message;
        
        // Inisialisasi komponen Toast Bootstrap dengan autohide: true dan durasi tepat 5 detik (5000 ms)
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            // Hapus instansi sebelumnya jika ada untuk memperbarui timer 5 detik secara bersih
            const oldInstance = bootstrap.Toast.getInstance(toastEl);
            if (oldInstance) {
                oldInstance.dispose();
            }

            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        } else {
            // Fallback manual jika library Bootstrap JS belum terinisialisasi
            toastEl.classList.add('show');
            if (window.toastTimeoutHandle) clearTimeout(window.toastTimeoutHandle);
            window.toastTimeoutHandle = setTimeout(function() {
                toastEl.classList.remove('show');
            }, 5000);
        }
    };

    // Otomatis menampilkan toast notifikasi jika terdapat pesan session dari server PHP atau sessionStorage
    document.addEventListener("DOMContentLoaded", function() {
        // Cek notifikasi pending dari sessionStorage (tampil 1x lalu langsung dihapus)
        const pendingToastMessage = sessionStorage.getItem('pending_toast_message');
        if (pendingToastMessage) {
            sessionStorage.removeItem('pending_toast_message');
            window.showToast("success", pendingToastMessage);
            return;
        }

        <?php if (isset($_SESSION['success_message'])): ?>
            window.showToast("success", "<?php echo addslashes($_SESSION['success_message']); ?>");
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            window.showToast("danger", "<?php echo addslashes($_SESSION['error_message']); ?>");
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning_message'])): ?>
            window.showToast("warning", "<?php echo addslashes($_SESSION['warning_message']); ?>");
            <?php unset($_SESSION['warning_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info_message'])): ?>
            window.showToast("info", "<?php echo addslashes($_SESSION['info_message']); ?>");
            <?php unset($_SESSION['info_message']); ?>
        <?php endif; ?>
    });
</script>