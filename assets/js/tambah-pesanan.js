document.addEventListener("DOMContentLoaded", function() {
    // Membaca session role dan query parameter layanan_id dari metadata HTML
    const userRole = $('#tambah-pesanan-metadata').data('user-role');
    const layananIdFromUrl = $('#tambah-pesanan-metadata').data('layanan-id-url');

    // Menentukan default input tanggal pemesanan dengan tanggal hari ini
    const inputTanggalPesan = document.getElementById('tanggal_pesan');
    if (inputTanggalPesan && !inputTanggalPesan.value) {
        inputTanggalPesan.value = new Date().toISOString().substring(0, 10);
    }

    // Fungsi pembantu untuk memunculkan alert box dan menggulir (scroll) halaman otomatis ke arah alert tersebut
    function showAlert(placeholderId, type, message) {
        const placeholder = $(placeholderId);
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const alertHtml = `
            <div class="alert alert-${type} border mb-3 alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi ${icon} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        placeholder.html(alertHtml);
        // Menggulir ke atas wadah alert dengan efek animasi halus
        $('html, body').animate({ scrollTop: placeholder.offset().top - 100 }, 300);
    }

    // Mengambil info ringkas layanan (harga, estimasi, deskripsi) via AJAX
    function fetchLayananSummary(layananId) {
        if (!layananId) {
            $('#detail_layanan_section').fadeOut(200);
            $('#total_harga').val('');
            return;
        }

        $.ajax({
            url: 'process/pesanan.php?action=get_layanan_summary&id=' + layananId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // Isi data rincian di panel info ringkas
                    $('#info_nama_layanan').text(data.nama_layanan);
                    $('#info_kategori').text(data.kategori);
                    $('#info_harga').text(data.harga_formatted);
                    $('#info_estimasi').text(data.estimasi_pengerjaan);
                    $('#info_deskripsi').text(data.deskripsi || '-');
                    
                    // Isi input tersembunyi total_harga dengan nominal mentah angka desimal
                    $('#total_harga').val(data.harga);
                    
                    $('#detail_layanan_section').fadeIn(200);
                } else {
                    showAlert('#alertPlaceholder', 'danger', 'Gagal memuat rincian layanan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                showAlert('#alertPlaceholder', 'danger', 'Terjadi kesalahan sistem saat mengambil data layanan: ' + error);
            }
        });
    }

    // Listener saat pilihan select paket layanan berubah
    $('#layanan_id').on('change', function() {
        const layananId = $(this).val();
        fetchLayananSummary(layananId);
    });

    // Otomatis men-trigger pemuatan info layanan jika dilempar parameter dari halaman daftar layanan
    if (layananIdFromUrl) {
        $('#layanan_id').val(layananIdFromUrl).trigger('change');
    }

    // Memproses form submit tambah pesanan baru via AJAX POST
    $('#formTambahPesanan').on('submit', function(e) {
        e.preventDefault();

        $('#alertPlaceholder').html('');

        const namaPelanggan = $('#nama_pelanggan').val().trim();
        const noHp = $('#no_hp').val().trim();
        const email = $('#email').val().trim();
        const alamat = $('#alamat').val().trim();
        const layananId = $('#layanan_id').val();
        const tanggalPesan = $('#tanggal_pesan').val();
        const tanggalSelesai = $('#tanggal_selesai').val();
        const totalHarga = $('#total_harga').val().trim();
        const catatan = $('#catatan').val().trim();

        // Validasi input form sisi klien (Client-side Validation)
        if (!namaPelanggan) {
            showAlert('#alertPlaceholder', 'danger', 'Nama pelanggan tidak boleh kosong.');
            return;
        }
        if (!noHp) {
            showAlert('#alertPlaceholder', 'danger', 'Nomor HP tidak boleh kosong.');
            return;
        }
        const noHpRegex = /^[0-9]{12,13}$/;
        if (!noHpRegex.test(noHp)) {
            showAlert('#alertPlaceholder', 'danger', 'Nomor HP wajib angka dengan panjang antara 12 dan 13 digit.');
            return;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('#alertPlaceholder', 'danger', 'Format email pelanggan tidak valid.');
            return;
        }
        if (!layananId) {
            showAlert('#alertPlaceholder', 'danger', 'Silakan pilih layanan.');
            return;
        }
        if (!tanggalPesan) {
            showAlert('#alertPlaceholder', 'danger', 'Tanggal pemesanan wajib diisi.');
            return;
        }
        if (!tanggalSelesai) {
            showAlert('#alertPlaceholder', 'danger', 'Tanggal estimasi selesai wajib diisi.');
            return;
        }
        if (new Date(tanggalSelesai) < new Date(tanggalPesan)) {
            showAlert('#alertPlaceholder', 'danger', 'Tanggal estimasi selesai pengerjaan tidak boleh sebelum tanggal pemesanan.');
            return;
        }
        if (totalHarga === '' || parseFloat(totalHarga) <= 0) {
            showAlert('#alertPlaceholder', 'danger', 'Total harga tidak valid atau kosong.');
            return;
        }
        if (!catatan) {
            showAlert('#alertPlaceholder', 'danger', 'Catatan pesanan wajib diisi.');
            return;
        }

        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

        // Kirim data formulir ke endpoint controller PHP via AJAX POST
        $.ajax({
            url: 'process/pesanan.php?action=create',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'text',
            success: function(rawResponse, textStatus, xhr) {
                btnSubmit.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan Pesanan');

                const contentType = (xhr.getResponseHeader('content-type') || '').toLowerCase();
                const responseText = typeof rawResponse === 'string' ? rawResponse : '';
                let response = null;

                if (contentType.includes('application/json')) {
                    try {
                        response = JSON.parse(responseText);
                    } catch (e) {
                        showAlert('#alertPlaceholder', 'danger', 'Respons server tidak valid. Silakan coba lagi.');
                        return;
                    }
                } else {
                    showAlert('#alertPlaceholder', 'danger', 'Respons server tidak valid. Silakan coba lagi.');
                    return;
                }

                if (response && response.status === 'success') {
                    // Alihkan halaman ke dashboard utama saat penyimpanan pesanan berhasil
                    window.location.href = 'dashboard.php?page=dashboard';
                } else {
                    showAlert('#alertPlaceholder', 'danger', response && response.message ? response.message : 'Terjadi kesalahan saat menyimpan pesanan.');
                }
            },
            error: function(xhr, status, error) {
                btnSubmit.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan Pesanan');
                const responseText = xhr.responseText || '';
                let message = 'Terjadi kesalahan sistem saat mengirim data.';
                if (responseText) {
                    try {
                        const parsed = JSON.parse(responseText);
                        if (parsed && parsed.message) {
                            message = parsed.message;
                        }
                    } catch (e) {
                        const contentType = (xhr.getResponseHeader('content-type') || '').toLowerCase();
                        if (contentType.includes('application/json')) {
                            message = 'Respons server tidak valid. Silakan coba lagi.';
                        } else {
                            message = 'Terjadi kesalahan sistem saat mengirim data.';
                        }
                    }
                }
                showAlert('#alertPlaceholder', 'danger', message);
            }
        });
    });
});