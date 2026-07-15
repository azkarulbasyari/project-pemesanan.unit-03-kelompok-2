<?php
// ==============================================================
// PROSES PENGOLAHAN DATA PESANAN / CRUD (process/pesanan.php)
// ==============================================================

ob_start();

// Mulai session dan pastikan pengguna telah login
session_start();

// Set header agar output berupa format JSON (digunakan untuk respon AJAX)
header('Content-Type: application/json');

// Validasi: pastikan pengguna sudah login sebelum mengakses
if (!isset($_SESSION['user_id'])) {
    if (ob_get_length()) ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

// Hubungkan file koneksi ke database
require_once '../config/koneksi.php';

// Migrasi database mandiri: Cek apakah kolom 'sumber_pesanan' sudah ada di tabel 'pesanan'
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM pesanan LIKE 'sumber_pesanan'");
if (mysqli_num_rows($check_col) == 0) {
    mysqli_query($koneksi, "ALTER TABLE pesanan ADD COLUMN sumber_pesanan ENUM('online', 'telepon', 'walk_in') DEFAULT 'online' AFTER created_by");
}

// Fungsi pembantu (helper) untuk mengirim respon error dalam format JSON
function sendError($message) {
    if (ob_get_length()) ob_clean();
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Ambil parameter aksi yang dikirim melalui URL GET
$action = isset($_GET['action']) ? $_GET['action'] : '';

// --------------------------------------------------------------
// AKSI: GET_LAYANAN_DETAIL (Mengambil Rincian Data Layanan untuk Modal)
// --------------------------------------------------------------
if ($action === 'get_layanan_detail') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        sendError('ID layanan tidak valid.');
    }

    // Menggunakan Prepared Statement untuk keamanan database
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM layanan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $layanan = mysqli_fetch_assoc($result);

    if (!$layanan) {
        sendError('Layanan tidak ditemukan.');
    }

    // Ambil 3 layanan rekomendasi lainnya
    $stmt_rec = mysqli_prepare($koneksi, "SELECT * FROM layanan WHERE id != ? LIMIT 3");
    mysqli_stmt_bind_param($stmt_rec, "i", $id);
    mysqli_stmt_execute($stmt_rec);
    $result_rec = mysqli_stmt_get_result($stmt_rec);
    $rekomendasi = [];
    while ($row = mysqli_fetch_assoc($result_rec)) {
        $rekomendasi[] = $row;
    }

    // Render file template detail layanan secara aman
    ob_start();
    include '../pages/layanan-detail.php';
    $html = ob_get_clean();

    echo json_encode([
        'status' => 'success', 
        'html' => $html, 
        'nama_layanan' => $layanan['nama_layanan'], 
        'kategori' => $layanan['kategori'],
        'harga' => $layanan['harga']
    ]);
    exit;
}

// --------------------------------------------------------------
// AKSI: GET_LAYANAN_SUMMARY (Mengambil Ringkasan Data Layanan)
// --------------------------------------------------------------
if ($action === 'get_layanan_summary') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        sendError('ID layanan tidak valid.');
    }

    $stmt = mysqli_prepare($koneksi, "SELECT id, nama_layanan, kategori, harga, estimasi_pengerjaan, status_layanan, deskripsi FROM layanan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $layanan = mysqli_fetch_assoc($result);

    if (!$layanan) {
        sendError('Layanan tidak ditemukan.');
    }

    $layanan['harga_formatted'] = "Rp " . number_format($layanan['harga'], 0, ',', '.');
    $layanan['status_layanan'] = ucfirst($layanan['status_layanan']);

    echo json_encode([
        'status' => 'success',
        'data' => $layanan
    ]);
    exit;
}

// --------------------------------------------------------------
// AKSI: GET_DETAIL (Mengambil Rincian Data Pesanan untuk Form Edit)
// --------------------------------------------------------------
if ($action === 'get_detail') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        sendError('ID pesanan tidak valid.');
    }

    // Query Menggunakan Prepared Statement untuk keamanan mengambil detail pesanan
    $sql = "SELECT p.*, l.harga AS harga_layanan, pl.nama_pelanggan, pl.no_hp, pl.email, pl.alamat 
            FROM pesanan p 
            JOIN layanan l ON p.layanan_id = l.id 
            JOIN pelanggan pl ON p.pelanggan_id = pl.id
            WHERE p.id = ? LIMIT 1";
            
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        sendError('Data pesanan tidak ditemukan.');
    }
    exit;
}

// --------------------------------------------------------------
// AKSI: CREATE (Menyimpan Data Pesanan Baru ke Database)
// --------------------------------------------------------------
if ($action === 'create') {
    // Ambil data POST yang dikirim oleh form tambah pesanan
    $nama_pelanggan = isset($_POST['nama_pelanggan']) ? trim($_POST['nama_pelanggan']) : '';
    $no_hp          = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $email          = isset($_POST['email']) ? trim($_POST['email']) : '';
    $alamat         = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $layanan_id     = isset($_POST['layanan_id']) ? intval($_POST['layanan_id']) : 0;
    $tanggal_pesan  = isset($_POST['tanggal_pesan']) ? trim($_POST['tanggal_pesan']) : '';
    $tanggal_selesai = isset($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : null;
    $catatan        = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
    $total_harga    = isset($_POST['total_harga']) ? floatval($_POST['total_harga']) : 0.00;
    $status_pesanan = isset($_POST['status_pesanan']) ? trim($_POST['status_pesanan']) : 'baru';
    $sumber_pesanan = isset($_POST['sumber_pesanan']) ? trim($_POST['sumber_pesanan']) : 'online';
    $created_by     = $_SESSION['user_id'];

    // Validasi data sisi server (Server-side Validations)
    if (empty($nama_pelanggan)) {
        sendError('Nama pelanggan tidak boleh kosong.');
    }
    if (empty($no_hp)) {
        sendError('Nomor HP tidak boleh kosong.');
    }
    if (!preg_match('/^[0-9]{12,13}$/', $no_hp)) {
        sendError('Nomor HP harus berupa angka dengan panjang 12 atau 13 digit.');
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Format email tidak valid.');
    }
    if ($layanan_id <= 0) {
        sendError('Silakan pilih layanan.');
    }
    if (empty($tanggal_pesan)) {
        sendError('Tanggal pesan wajib diisi.');
    }
    if (empty($tanggal_selesai)) {
        sendError('Tanggal selesai wajib diisi.');
    }
    if (empty($catatan)) {
        sendError('Catatan wajib diisi.');
    }
    if ($total_harga <= 0) {
        sendError('Total harga wajib diisi dan harus lebih dari nol.');
    }
    $allowed_statuses = ['baru', 'diproses', 'selesai', 'dibatalkan'];
    if (!in_array($status_pesanan, $allowed_statuses)) {
        sendError('Status pesanan tidak valid.');
    }
    $allowed_sumber = ['online', 'telepon', 'walk_in'];
    if (!in_array($sumber_pesanan, $allowed_sumber)) {
        $sumber_pesanan = 'online';
    }

    // Periksa apakah pelanggan sudah pernah terdaftar (Prepared Statement)
    $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM pelanggan WHERE LOWER(nama_pelanggan) = LOWER(?) LIMIT 1");
    mysqli_stmt_bind_param($stmt_check, "s", $nama_pelanggan);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if ($res_check && mysqli_num_rows($res_check) > 0) {
        $pelanggan_row = mysqli_fetch_assoc($res_check);
        $pelanggan_id = intval($pelanggan_row['id']);

        // Perbarui info kontak pelanggan (Prepared Statement)
        $stmt_up_cust = mysqli_prepare($koneksi, "UPDATE pelanggan SET no_hp = ?, email = ?, alamat = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_up_cust, "sssi", $no_hp, $email, $alamat, $pelanggan_id);
        mysqli_stmt_execute($stmt_up_cust);
    } else {
        // Jika pelanggan belum ada, buat data pelanggan baru (Prepared Statement)
        $stmt_ins_cust = mysqli_prepare($koneksi, "INSERT INTO pelanggan (nama_pelanggan, no_hp, email, alamat) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_ins_cust, "ssss", $nama_pelanggan, $no_hp, $email, $alamat);
        if (mysqli_stmt_execute($stmt_ins_cust)) {
            $pelanggan_id = mysqli_insert_id($koneksi);
        } else {
            sendError('Gagal menyimpan data pelanggan baru.');
        }
    }

    // Logika pembuatan otomatis kode_pesanan secara urut
    $year = date('Y', strtotime($tanggal_pesan));
    if (!$year || $year === '1970') {
        $year = date('Y');
    }
    $prefix = "PSN-" . $year . "-";
    
    // Cari urutan angka terakhir pada tahun ini (Prepared Statement)
    $like_prefix = $prefix . "%";
    $stmt_seq = mysqli_prepare($koneksi, "SELECT kode_pesanan FROM pesanan WHERE kode_pesanan LIKE ? ORDER BY id DESC LIMIT 1");
    mysqli_stmt_bind_param($stmt_seq, "s", $like_prefix);
    mysqli_stmt_execute($stmt_seq);
    $res_seq = mysqli_stmt_get_result($stmt_seq);
    
    $next_num = 1;
    if ($res_seq && mysqli_num_rows($res_seq) > 0) {
        $last_row = mysqli_fetch_assoc($res_seq);
        $last_kode = $last_row['kode_pesanan'];
        $parts = explode('-', $last_kode);
        if (count($parts) === 3) {
            $next_num = intval($parts[2]) + 1;
        }
    }
    
    $kode_pesanan = $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);
    $tanggal_selesai_val = !empty($tanggal_selesai) ? $tanggal_selesai : null;

    // Query SQL menggunakan Prepared Statement untuk menyimpan data transaksi pesanan baru
    $sql_ins = "INSERT INTO pesanan (kode_pesanan, pelanggan_id, layanan_id, tanggal_pesan, tanggal_selesai, catatan, total_harga, status_pesanan, created_by, sumber_pesanan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_ins_p = mysqli_prepare($koneksi, $sql_ins);
    mysqli_stmt_bind_param($stmt_ins_p, "siisssdsis", $kode_pesanan, $pelanggan_id, $layanan_id, $tanggal_pesan, $tanggal_selesai_val, $catatan, $total_harga, $status_pesanan, $created_by, $sumber_pesanan);

    if (mysqli_stmt_execute($stmt_ins_p)) {
        $new_id = mysqli_insert_id($koneksi);
        
        // Ambil nama layanan untuk respon ajax
        $stmt_lay_name = mysqli_prepare($koneksi, "SELECT nama_layanan FROM layanan WHERE id = ?");
        mysqli_stmt_bind_param($stmt_lay_name, "i", $layanan_id);
        mysqli_stmt_execute($stmt_lay_name);
        $res_lay_name = mysqli_stmt_get_result($stmt_lay_name);
        $lay_name_row = mysqli_fetch_assoc($res_lay_name);
        $nama_layanan = $lay_name_row ? $lay_name_row['nama_layanan'] : '';

        $_SESSION['success_message'] = "Pesanan dengan kode $kode_pesanan berhasil dicatat. Terima kasih.";
        echo json_encode([
            'status' => 'success', 
            'message' => "Pesanan berhasil dicatat. Terima kasih.", 
            'kode' => $kode_pesanan,
            'data' => [
                'id' => $new_id,
                'kode_pesanan' => $kode_pesanan,
                'nama_pelanggan' => $nama_pelanggan,
                'nama_layanan' => $nama_layanan,
                'tanggal_pesan' => date('d-m-Y', strtotime($tanggal_pesan)),
                'total_harga' => $total_harga,
                'sumber_pesanan' => $sumber_pesanan,
                'status_pesanan' => $status_pesanan
            ]
        ]);
    } else {
        sendError('Gagal menambahkan pesanan ke database.');
    }
    exit;
}

// --------------------------------------------------------------
// AKSI: UPDATE (Memperbarui Data Pesanan yang Sudah Ada)
// --------------------------------------------------------------
if ($action === 'update') {
    // Ambil data POST untuk pembaruan data pesanan
    $id             = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama_pelanggan = isset($_POST['nama_pelanggan']) ? trim($_POST['nama_pelanggan']) : '';
    $no_hp          = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $email          = isset($_POST['email']) ? trim($_POST['email']) : '';
    $alamat         = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $layanan_id     = isset($_POST['layanan_id']) ? intval($_POST['layanan_id']) : 0;
    $tanggal_pesan  = isset($_POST['tanggal_pesan']) ? trim($_POST['tanggal_pesan']) : '';
    $tanggal_selesai = isset($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : null;
    $catatan        = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
    $total_harga    = isset($_POST['total_harga']) ? floatval($_POST['total_harga']) : 0.00;
    $status_pesanan = isset($_POST['status_pesanan']) ? trim($_POST['status_pesanan']) : 'baru';

    // Validasi data sisi server (Server-side Validations)
    if ($id <= 0) {
        sendError('ID pesanan tidak valid.');
    }
    if (empty($nama_pelanggan)) {
        sendError('Nama pelanggan tidak boleh kosong.');
    }
    if (empty($no_hp)) {
        sendError('Nomor HP tidak boleh kosong.');
    }
    if (!preg_match('/^[0-9]{12,13}$/', $no_hp)) {
        sendError('Nomor HP harus berupa angka dengan panjang 12 atau 13 digit.');
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Format email tidak valid.');
    }
    if ($layanan_id <= 0) {
        sendError('Silakan pilih layanan.');
    }
    if (empty($tanggal_pesan)) {
        sendError('Tanggal pesan wajib diisi.');
    }
    if (empty($tanggal_selesai)) {
        sendError('Tanggal selesai wajib diisi.');
    }
    if (empty($catatan)) {
        sendError('Catatan wajib diisi.');
    }
    if ($total_harga <= 0) {
        sendError('Total harga wajib diisi dan harus lebih dari nol.');
    }
    $allowed_statuses = ['baru', 'diproses', 'selesai', 'dibatalkan'];
    if (!in_array($status_pesanan, $allowed_statuses)) {
        sendError('Status pesanan tidak valid.');
    }

    // Periksa apakah pelanggan sudah terdaftar (Prepared Statement)
    $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM pelanggan WHERE LOWER(nama_pelanggan) = LOWER(?) LIMIT 1");
    mysqli_stmt_bind_param($stmt_check, "s", $nama_pelanggan);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if ($res_check && mysqli_num_rows($res_check) > 0) {
        $pelanggan_row = mysqli_fetch_assoc($res_check);
        $pelanggan_id = intval($pelanggan_row['id']);

        // Perbarui info kontak pelanggan (Prepared Statement)
        $stmt_up_cust = mysqli_prepare($koneksi, "UPDATE pelanggan SET no_hp = ?, email = ?, alamat = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_up_cust, "sssi", $no_hp, $email, $alamat, $pelanggan_id);
        mysqli_stmt_execute($stmt_up_cust);
    } else {
        // Buat data pelanggan baru jika tidak ditemukan (Prepared Statement)
        $stmt_ins_cust = mysqli_prepare($koneksi, "INSERT INTO pelanggan (nama_pelanggan, no_hp, email, alamat) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_ins_cust, "ssss", $nama_pelanggan, $no_hp, $email, $alamat);
        if (mysqli_stmt_execute($stmt_ins_cust)) {
            $pelanggan_id = mysqli_insert_id($koneksi);
        } else {
            sendError('Gagal menyimpan data pelanggan.');
        }
    }

    $tanggal_selesai_val = !empty($tanggal_selesai) ? $tanggal_selesai : null;

    // Query SQL menggunakan Prepared Statement untuk memperbarui data pesanan
    $sql_up = "UPDATE pesanan SET 
                pelanggan_id = ?, 
                layanan_id = ?, 
                tanggal_pesan = ?, 
                tanggal_selesai = ?, 
                catatan = ?, 
                total_harga = ?, 
                status_pesanan = ? 
            WHERE id = ?";
            
    $stmt_up_p = mysqli_prepare($koneksi, $sql_up);
    mysqli_stmt_bind_param($stmt_up_p, "iisssdsi", $pelanggan_id, $layanan_id, $tanggal_pesan, $tanggal_selesai_val, $catatan, $total_harga, $status_pesanan, $id);

    if (mysqli_stmt_execute($stmt_up_p)) {
        $_SESSION['success_message'] = "Pesanan berhasil diperbarui.";
        echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil diperbarui.']);
    } else {
        sendError('Gagal memperbarui data pesanan.');
    }
    exit;
}

// --------------------------------------------------------------
// AKSI: DELETE (Menghapus Data Pesanan dari Database)
// --------------------------------------------------------------
if ($action === 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        sendError('ID pesanan tidak valid.');
    }

    // Query SQL menggunakan Prepared Statement untuk menghapus pesanan berdasarkan ID
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM pesanan WHERE id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    
    if (mysqli_stmt_execute($stmt_del)) {
        if (mysqli_stmt_affected_rows($stmt_del) > 0) {
            if (ob_get_length()) ob_clean();
            $_SESSION['success_message'] = "Pesanan berhasil dihapus.";
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dihapus.']);
        } else {
            sendError('Pesanan tidak ditemukan atau sudah dihapus sebelumnya.');
        }
    } else {
        sendError('Gagal menghapus pesanan dari database.');
    }
    exit;
}

// Respon default jika parameter aksi tidak terdaftar
sendError('Aksi tidak dikenali.');
?>
