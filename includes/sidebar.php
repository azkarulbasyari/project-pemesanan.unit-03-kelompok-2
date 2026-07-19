<div class="sidebar p-0 position-relative">
    <!-- Tombol Floating Toggle Sidebar (Bulat 44px Premium, 50% Menonjol di Tepi Kanan Sidebar) -->
    <button type="button" class="btn-sidebar-toggle-handle btn-toggle-sidebar" title="Sembunyikan Sidebar" aria-label="Toggle Sidebar">
        <i class="bi bi-chevron-left icon-close"></i>
        <i class="bi bi-chevron-right icon-open"></i>
    </button>

    <!-- Menampilkan informasi brand aplikasi dan nama/role user yang sedang login -->
    <div class="brand">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-logo-icon">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0 text-white" style="font-size: 1.05rem; letter-spacing: -0.02em;">ServiceHub</h5>
                    <span class="badge bg-primary text-white" style="font-size: 0.55rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; letter-spacing: 0.05em;">PRO</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-pulse-dot-sm"></span>
                    <small class="text-white-50" style="font-size: 0.72rem; line-height: 1;"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?> &bull; Online</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigasi Menu Sidebar. Status kelas 'active' diatur dinamis berdasarkan isi variabel $page -->
    <nav class="p-3 d-flex flex-column gap-1.5">
        <a href="dashboard.php?page=dashboard" class="sidebar-nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2 nav-icon"></i>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="dashboard.php?page=pesanan" class="sidebar-nav-item <?php echo $page == 'pesanan' ? 'active' : ''; ?>">
            <i class="bi bi-receipt-cutoff nav-icon"></i>
            <span class="nav-text">Manajemen Pesanan</span>
        </a>
        <a href="dashboard.php?page=pesanan-tambah" class="sidebar-nav-item <?php echo $page == 'pesanan-tambah' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle-fill nav-icon"></i>
            <span class="nav-text">Tambah Pesanan</span>
        </a>
        <a href="dashboard.php?page=layanan" class="sidebar-nav-item <?php echo $page == 'layanan' ? 'active' : ''; ?>">
            <i class="bi bi-grid-fill nav-icon"></i>
            <span class="nav-text">Data Layanan</span>
        </a>

        <div class="sidebar-divider my-2.5 border-top border-secondary border-opacity-10"></div>

        <!-- Tautan ini memicu pembukaan modal konfirmasi logout Bootstrap -->
        <a href="#" class="sidebar-nav-item nav-logout" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout">
            <i class="bi bi-box-arrow-left nav-icon"></i>
            <span class="nav-text">Logout</span>
        </a>
    </nav>
</div>