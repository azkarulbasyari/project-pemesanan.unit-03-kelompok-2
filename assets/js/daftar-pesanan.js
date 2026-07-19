document.addEventListener("DOMContentLoaded", function () {
    let tabelPesanan = null;
    const userRole = $('#pesanan-metadata').data('user-role');

    // Aksi ketika tombol tambah pesanan diklik (Dapat diakses oleh semua role user)
    $('#btnTambahPesananManualTrigger').on('click', function () {
        $('#modalTambahPesananManual').modal('show');
    });

    // Atur tabel agar menggunakan DataTables dengan bahasa Indonesia dan penomoran urut dinamis
    $(document).ready(function () {
        tabelPesanan = $('#tabelPesanan').DataTable({
            "language": {
                "sEmptyTable": "Tidak ada antrian pesanan yang tersedia pada tabel ini",
                "sProcessing": "Sedang memproses...",
                "sLengthMenu": "Tampilkan _MENU_ antrian",
                "sZeroRecords": "Tidak ditemukan antrian pesanan yang sesuai",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ antrian",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 antrian",
                "sInfoFiltered": "(disaring dari _MAX_ total antrian)",
                "sInfoPostFix": "",
                "sSearch": "Cari Pesanan:",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext": "Berikutnya",
                    "sLast": "Terakhir"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ],
            "order": [], // Tanpa pengurutan kolom NO secara terbalik
            "pageLength": 10, // Tampilkan 10 baris data per halaman
            "drawCallback": function (settings) {
                // Penomoran otomatis dimulai dari nomor terkecil (1, 2, 3...) dari paling atas
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    });

    // Fungsi bantuan untuk menampilkan pesan peringatan
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
    }

    // Kosongkan form dan peringatan saat modal ditutup
    $('#modalTambahPesananManual').on('hidden.bs.modal', function () {
        $('#alertPlaceholderTambahManual').html('');
        $('#formTambahPesananManual')[0].reset();
        $('#manual_tanggal_pesan').val(new Date().toISOString().substring(0, 10));
    });
    $('#modalUbahPesanan').on('hidden.bs.modal', function () {
        $('#alertPlaceholderEdit').html('');
    });
    $('#modalKonfirmasiHapus').on('hidden.bs.modal', function () {
        $('#alertPlaceholderHapus').html('');
    });

    // Otomatis isi harga jika layanan dipilih pada form tambah
    $('#manual_layanan_id').on('change', function () {
        const selectedOption = $(this).find(':selected');
        const harga = selectedOption.data('harga');
        if (harga !== undefined) {
            $('#manual_total_harga').val(harga);
        } else {
            $('#manual_total_harga').val('');
        }
    });

    // Otomatis isi harga jika layanan dipilih pada form edit
    $('#edit_layanan_id').on('change', function () {
        const selectedOption = $(this).find(':selected');
        const harga = selectedOption.data('harga');
        if (harga !== undefined) {
            $('#edit_total_harga').val(harga);
        } else {
            $('#edit_total_harga').val('');
        }
    });

    // Simpan data pesanan baru menggunakan AJAX tanpa reload halaman
    $('#formTambahPesananManual').on('submit', function (e) {
        e.preventDefault();

        // Validasi data input form
        const namaPelanggan = $('#manual_nama_pelanggan').val().trim();
        const noHp = $('#manual_no_hp').val().trim();
        const email = $('#manual_email').val().trim();
        const alamat = $('#manual_alamat').val().trim();
        const layananId = $('#manual_layanan_id').val();
        const tanggalPesan = $('#manual_tanggal_pesan').val();
        const tanggalSelesai = $('#manual_tanggal_selesai').val();
        const totalHarga = $('#manual_total_harga').val().trim();
        const catatan = $('#manual_catatan').val().trim();

        if (!namaPelanggan) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Silakan masukkan nama pelanggan.");
            return;
        }
        if (!noHp) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Silakan masukkan nomor HP.");
            return;
        }
        const noHpRegex = /^[0-9]{12,13}$/;
        if (!noHpRegex.test(noHp)) {
            showAlert('#alertPlaceholderTambahManual', 'danger', 'Nomor HP harus berupa angka dengan panjang 12 atau 13 digit.');
            return;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('#alertPlaceholderTambahManual', 'danger', 'Format email tidak valid.');
            return;
        }
        if (!layananId) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Silakan pilih layanan.");
            return;
        }
        if (!tanggalPesan) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Tanggal pesan wajib diisi.");
            return;
        }
        if (!tanggalSelesai) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Tanggal estimasi selesai wajib diisi.");
            return;
        }
        if (new Date(tanggalSelesai) < new Date(tanggalPesan)) {
            showAlert('#alertPlaceholderTambahManual', 'danger', 'Tanggal estimasi selesai tidak boleh mendahului tanggal pemesanan.');
            return;
        }
        if (totalHarga === '' || parseFloat(totalHarga) <= 0) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Total harga wajib diisi dan harus lebih dari nol.");
            return;
        }
        if (!catatan) {
            showAlert('#alertPlaceholderTambahManual', 'danger', "Catatan wajib diisi.");
            return;
        }

        const btnSave = $(this).find('button[type="submit"]');
        btnSave.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: 'process/pesanan.php?action=create',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                btnSave.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan Pesanan');
                if (response.status === 'success') {
                    // Tutup modal tambah pesanan
                    $('#modalTambahPesananManual').modal('hide');

                    // Tampilkan notifikasi sukses melayang tanpa refresh
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', response.message || 'Pesanan berhasil dicatat.');
                    }

                    const data = response.data;

                    // Buat badge status sesuai kondisi
                    let statusClass = 'status-baru';
                    if (data.status_pesanan === 'diproses') statusClass = 'status-diproses';
                    else if (data.status_pesanan === 'selesai') statusClass = 'status-selesai';
                    else if (data.status_pesanan === 'dibatalkan') statusClass = 'status-dibatalkan';

                    const statusBadge = `<span class="badge-status ${statusClass}">${data.status_pesanan.charAt(0).toUpperCase() + data.status_pesanan.slice(1)}</span>`;

                    // Buat badge sumber pesanan
                    let sumberBadge = '';
                    if (data.sumber_pesanan === 'telepon') {
                        sumberBadge = '<span class="badge-sumber"><i class="bi bi-telephone-fill text-success"></i> Telepon</span>';
                    } else if (data.sumber_pesanan === 'walk_in') {
                        sumberBadge = '<span class="badge-sumber"><i class="bi bi-person-fill text-info"></i> Walk In</span>';
                    } else {
                        sumberBadge = '<span class="badge-sumber"><i class="bi bi-globe text-primary"></i> Online</span>';
                    }

                    const formattedPrice = "Rp " + parseFloat(data.total_harga).toLocaleString('id-ID');

                    // Tambahkan data baru langsung ke dalam tabel
                    const newRowNode = tabelPesanan.row.add([
                        1,
                        `<strong>${data.kode_pesanan}</strong>`,
                        data.nama_pelanggan,
                        data.nama_layanan,
                        data.tanggal_pesan,
                        formattedPrice,
                        sumberBadge,
                        statusBadge,
                        `<div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-outline-primary action-btn edit-btn" data-id="${data.id}" title="Edit Pesanan">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger action-btn delete-btn" data-id="${data.id}" data-kode="${data.kode_pesanan}" title="Hapus Pesanan">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`
                    ]).draw(false).node();

                    // Hubungkan ID dan class ke baris tabel baru
                    $(newRowNode).attr('data-id', data.id);
                    $(newRowNode).find('td:nth-child(2)').addClass('col-kode-pesanan text-nowrap');
                    $(newRowNode).find('td:nth-child(3)').addClass('col-nama-pelanggan');
                    $(newRowNode).find('td:nth-child(4)').addClass('col-nama-layanan');
                    $(newRowNode).find('td:nth-child(5)').addClass('col-tanggal-pesan');
                    $(newRowNode).find('td:nth-child(6)').addClass('col-total-harga');
                    $(newRowNode).find('td:nth-child(7)').addClass('col-sumber-pesanan');
                    $(newRowNode).find('td:nth-child(8)').addClass('col-status-pesanan');

                    // Redraw tabel agar penomoran urut otomatis (drawCallback) diperbarui
                    tabelPesanan.draw(false);

                    // Efek hijau sementara pada baris baru
                    $(newRowNode).addClass('table-success');
                    $(newRowNode).css('transition', 'background-color 2s ease');
                    setTimeout(function () {
                        $(newRowNode).removeClass('table-success');
                    }, 1500);
                } else {
                    showAlert('#alertPlaceholderTambahManual', 'danger', response.message);
                }
            },
            error: function () {
                btnSave.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan Pesanan');
                showAlert('#alertPlaceholderTambahManual', 'danger', 'Terjadi kesalahan sistem saat memproses pesanan.');
            }
        });
    });

    // Ambil data pesanan dari database saat tombol edit diklik
    let barisYangSedangDiedit = null;
    $(document).on('click', '.edit-btn', function () {
        if (userRole !== 'admin') {
            $('#modalAksesTerbatas').modal('show');
            return;
        }
        barisYangSedangDiedit = $(this).closest('tr');
        const id = $(this).data('id');

        $.ajax({
            url: 'process/pesanan.php?action=get_detail&id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#edit_id').val(data.id);
                    $('#edit_kode_pesanan').val(data.kode_pesanan);
                    $('#edit_nama_pelanggan').val(data.nama_pelanggan);
                    $('#edit_no_hp').val(data.no_hp);
                    $('#edit_email').val(data.email);
                    $('#edit_layanan_id').val(data.layanan_id);
                    $('#edit_tanggal_pesan').val(data.tanggal_pesan);
                    $('#edit_tanggal_selesai').val(data.tanggal_selesai);
                    $('#edit_total_harga').val(data.total_harga);
                    $('#edit_status_pesanan').val(data.status_pesanan);
                    $('#edit_alamat').val(data.alamat);
                    $('#edit_catatan').val(data.catatan);

                    // Tampilkan modal edit
                    $('#modalUbahPesanan').modal('show');
                } else {
                    showToast('danger', response.message);
                }
            },
            error: function () {
                showToast('danger', "Gagal mengambil rincian data pesanan.");
            }
        });
    });

    // Simpan perubahan data pesanan menggunakan AJAX
    $('#formUbahPesanan').on('submit', function (e) {
        e.preventDefault();

        // Validasi data input form edit
        const id = $('#edit_id').val();
        const namaPelanggan = $('#edit_nama_pelanggan').val().trim();
        const noHp = $('#edit_no_hp').val().trim();
        const email = $('#edit_email').val().trim();
        const alamat = $('#edit_alamat').val().trim();
        const layananId = $('#edit_layanan_id').val();
        const layananNama = $('#edit_layanan_id').find(':selected').text();
        const tanggalPesan = $('#edit_tanggal_pesan').val();
        const tanggalSelesai = $('#edit_tanggal_selesai').val();
        const totalHarga = $('#edit_total_harga').val().trim();
        const statusPesanan = $('#edit_status_pesanan').val();
        const catatan = $('#edit_catatan').val().trim();

        if (!namaPelanggan) {
            showAlert('#alertPlaceholderEdit', 'danger', "Silakan masukkan nama pelanggan.");
            return;
        }
        if (!noHp) {
            showAlert('#alertPlaceholderEdit', 'danger', "Silakan masukkan nomor HP.");
            return;
        }
        const noHpRegex = /^[0-9]{12,13}$/;
        if (!noHpRegex.test(noHp)) {
            showAlert('#alertPlaceholderEdit', 'danger', 'Nomor HP harus berupa angka dengan panjang 12 atau 13 digit.');
            return;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('#alertPlaceholderEdit', 'danger', 'Format email tidak valid.');
            return;
        }
        if (!layananId) {
            showAlert('#alertPlaceholderEdit', 'danger', "Silakan pilih layanan.");
            return;
        }
        if (!tanggalPesan) {
            showAlert('#alertPlaceholderEdit', 'danger', "Tanggal pesan wajib diisi.");
            return;
        }
        if (!tanggalSelesai) {
            showAlert('#alertPlaceholderEdit', 'danger', "Tanggal estimasi selesai wajib diisi.");
            return;
        }
        if (new Date(tanggalSelesai) < new Date(tanggalPesan)) {
            showAlert('#alertPlaceholderEdit', 'danger', 'Tanggal estimasi selesai tidak boleh mendahului tanggal pemesanan.');
            return;
        }
        if (totalHarga === '' || parseFloat(totalHarga) <= 0) {
            showAlert('#alertPlaceholderEdit', 'danger', "Total harga wajib diisi dan harus lebih dari nol.");
            return;
        }
        if (!catatan) {
            showAlert('#alertPlaceholderEdit', 'danger', "Catatan wajib diisi.");
            return;
        }

        const btnUpdate = $(this).find('button[type="submit"]');
        btnUpdate.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: 'process/pesanan.php?action=update',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                btnUpdate.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Perbarui Pesanan');
                if (response.status === 'success') {
                    // Tutup modal edit
                    $('#modalUbahPesanan').modal('hide');

                    // Tampilkan notifikasi sukses melayang tanpa refresh
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', response.message || 'Pesanan berhasil diperbarui.');
                    }

                    // Format ulang tanggal dan harga agar rapi di tabel
                    const formattedDate = dateStr => {
                        const d = new Date(dateStr);
                        let day = '' + d.getDate();
                        let month = '' + (d.getMonth() + 1);
                        const year = d.getFullYear();
                        if (day.length < 2) day = '0' + day;
                        if (month.length < 2) month = '0' + month;
                        return [day, month, year].join('-');
                    };
                    const formattedDateStr = formattedDate(tanggalPesan);
                    const formattedPrice = "Rp " + parseFloat(totalHarga).toLocaleString('id-ID');

                    let statusClass = 'status-baru';
                    if (statusPesanan === 'diproses') statusClass = 'status-diproses';
                    else if (statusPesanan === 'selesai') statusClass = 'status-selesai';
                    else if (statusPesanan === 'dibatalkan') statusClass = 'status-dibatalkan';

                    const statusBadge = `<span class="badge-status ${statusClass}">${statusPesanan.charAt(0).toUpperCase() + statusPesanan.slice(1)}</span>`;

                    // Perbarui data baris tabel secara langsung
                    tabelPesanan.cell(barisYangSedangDiedit, 2).data(namaPelanggan);
                    tabelPesanan.cell(barisYangSedangDiedit, 3).data(layananNama);
                    tabelPesanan.cell(barisYangSedangDiedit, 4).data(formattedDateStr);
                    tabelPesanan.cell(barisYangSedangDiedit, 5).data(formattedPrice);
                    tabelPesanan.cell(barisYangSedangDiedit, 7).data(statusBadge);
                    tabelPesanan.draw(false);
                } else {
                    showAlert('#alertPlaceholderEdit', 'danger', response.message);
                }
            },
            error: function () {
                showAlert('#alertPlaceholderEdit', 'danger', "Terjadi kesalahan sistem saat memperbarui data.");
            }
        });
    });

    // Hapus data pesanan
    let idYangAkanDihapus = null;
    let barisYangAkanDihapus = null;

    $(document).on('click', '.delete-btn', function () {
        if (userRole !== 'admin') {
            $('#modalAksesTerbatas').modal('show');
            return;
        }
        idYangAkanDihapus = $(this).data('id');
        barisYangAkanDihapus = $(this).closest('tr');
        const kode = $(this).data('kode');

        $('#delete_kode_pesanan').text(kode);
        $('#modalKonfirmasiHapus').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        if (idYangAkanDihapus && barisYangAkanDihapus) {
            $.ajax({
                url: 'process/pesanan.php?action=delete',
                type: 'POST',
                data: { id: idYangAkanDihapus },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Sembunyikan modal
                        $('#modalKonfirmasiHapus').modal('hide');

                        // Tampilkan notifikasi sukses melayang tanpa refresh
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message || 'Pesanan berhasil dihapus.');
                        }

                        // Hapus baris tabel dengan efek menghilang secara halus
                        $(barisYangAkanDihapus).fadeOut(400, function () {
                            tabelPesanan.row(barisYangAkanDihapus).remove().draw(false);
                        });
                    } else {
                        showAlert('#alertPlaceholderHapus', 'danger', response.message);
                    }
                },
                error: function (xhr, status, error) {
                    showAlert('#alertPlaceholderHapus', 'danger', "Terjadi kesalahan sistem saat menghapus data.");
                }
            });
        }
    });
});