<div class="col-lg-2 col-md-3 sidebar p-0">
    <!-- Menampilkan informasi brand aplikasi dan nama/role user yang sedang login -->
    <div class="brand">
        <h5 class="fw-bold mb-1">ServiceOrder</h5>
        <small class="text-secondary"><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?> (<?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>)</small>
    </div>

    <!-- Navigasi Menu Sidebar. Status kelas 'active' diatur dinamis berdasarkan isi variabel $page -->
    <nav class="p-3">
        <a href="dashboard.php?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="dashboard.php?page=pesanan" class="nav-link <?php echo $page == 'pesanan' ? 'active' : ''; ?>">
            <i class="bi bi-receipt me-2"></i> Manajemen Pesanan
        </a>
        <a href="dashboard.php?page=pesanan-tambah" class="nav-link <?php echo $page == 'pesanan-tambah' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle me-2"></i> Tambah Pesanan
        </a>
        <a href="dashboard.php?page=layanan" class="nav-link <?php echo $page == 'layanan' ? 'active' : ''; ?>">
            <i class="bi bi-grid me-2"></i> Data Layanan
        </a>
        <!-- Tautan ini memicu pembukaan modal konfirmasi logout Bootstrap -->
        <a href="#" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiLogout">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
    </nav>
</div>