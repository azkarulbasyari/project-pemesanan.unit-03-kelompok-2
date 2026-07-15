<!-- Modal Konfirmasi Logout menggunakan Bootstrap -->
<div class="modal fade" id="modalKonfirmasiLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Pengaturan desain custom border-radius premium -->
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: var(--card-shadow);">
            <!-- Header modal dengan latar merah (danger) -->
            <div class="modal-header bg-danger text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="modalLogoutLabel">
                    <i class="bi bi-box-arrow-left me-2"></i>Konfirmasi Logout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body modal berisi pesan konfirmasi kepada user -->
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3" style="font-size: 3.5rem;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Logout</h5>
                <p class="text-muted mb-0">Apakah Anda yakin ingin keluar dari akun ini?</p>
            </div>
            <!-- Footer modal berisi tombol batal (tutup modal) dan tombol konfirmasi keluar (arahkan ke process/logout.php) -->
            <div class="modal-footer p-3 bg-light border-0 d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                <a href="process/logout.php" class="btn btn-danger px-4 py-2" style="border-radius: 10px; font-weight: 600;">Ya, Keluar</a>
            </div>
        </div>
    </div>
</div>