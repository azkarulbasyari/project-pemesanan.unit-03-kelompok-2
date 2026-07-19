document.addEventListener("DOMContentLoaded", function() {
    // 1. Memuat detail rincian layanan via AJAX ke dalam modal
    function loadLayananDetail(id) {
        // Tampilkan animasi loading spinner selama request AJAX berjalan
        $('#modal_body_layanan_detail').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat rincian layanan...</span>
                </div>
            </div>
        `);
        
        $('#modalDetailLayanan').modal('show');
        
        // Panggil controller process/pesanan.php dengan parameter id layanan
        $.ajax({
            url: 'process/pesanan.php?action=get_layanan_detail&id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update judul modal, kategori, dan isi detail layanan
                    $('#modal_nama_layanan').text(response.nama_layanan);
                    $('#modal_kategori_layanan').text(response.kategori);
                    $('#modal_body_layanan_detail').html(response.html);
                    
                    // Hubungkan tombol pemesanan langsung menuju halaman tambah pesanan dengan menyematkan query param
                    $('#modal_btn_pesan').attr('href', 'dashboard.php?page=pesanan-tambah&layanan_id=' + id);
                } else {
                    $('#modal_body_layanan_detail').html(`
                        <div class="alert alert-danger m-3" role="alert">
                            Gagal memuat rincian data: ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#modal_body_layanan_detail').html(`
                    <div class="alert alert-danger m-3" role="alert">
                        Terjadi kesalahan sistem saat mengambil data detail. Detail: ${error}
                    </div>
                `);
            }
        });
    }

    // 2. Melakukan filter kategori layanan baik pada tampilan kartu (grid) maupun tabel
    function filterLayanan() {
        const selectedKategori = $.trim($('#filterKategori').val()).toLowerCase();

        // Filter tampilan kartu minimal
        $('.layanan-card-item').each(function() {
            const cardKategori = $.trim($(this).attr('data-kategori') || $(this).data('kategori') || '').toLowerCase();
            const matchesKategori = (selectedKategori === '') || (cardKategori === selectedKategori);

            if (matchesKategori) {
                $(this).removeClass('d-none').stop(true, true).css('opacity', 0).animate({ opacity: 1 }, 200);
            } else {
                $(this).addClass('d-none');
            }
        });

        // Filter tampilan baris tabel data layanan
        $('.layanan-table-row').each(function() {
            const rowKategori = $.trim($(this).attr('data-kategori') || $(this).data('kategori') || '').toLowerCase();
            const matchesKategori = (selectedKategori === '') || (rowKategori === selectedKategori);

            if (matchesKategori) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    }

    // Inisialisasi kontrol dropdown kustom kategori layanan
    const dropdownContainer = $('.custom-dropdown-container');
    const hiddenInput = $('#filterKategori');
    const selectedLabel = $('#selectedKategoriLabel');

    // Buka / Tutup menu dropdown kustom
    $(document).on('click', '#customFilterTrigger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.custom-dropdown-container').toggleClass('open');
    });

    // Tutup dropdown saat mengklik di luar bidang elemen
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.custom-dropdown-container').length) {
            $('.custom-dropdown-container').removeClass('open');
        }
    });

    // Pilihan item dari menu dropdown kustom
    $(document).on('click', '.dropdown-item-custom', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const val = $(this).attr('data-value') || '';
        const text = $.trim($(this).find('span.fw-bold, span.fw-semibold').first().text() || val);
        
        $('.dropdown-item-custom').removeClass('active');
        $('.dropdown-item-custom .check-mark').addClass('opacity-0');
        
        $(this).addClass('active');
        $(this).find('.check-mark').removeClass('opacity-0');

        hiddenInput.val(val);
        selectedLabel.text(val === '' ? '✨ Semua Kategori' : text);

        $('.custom-dropdown-container').removeClass('open');
        filterLayanan();
    });

    // Picu fungsi filter ketika input hidden kategori berubah
    $('#filterKategori').on('change', function() {
        filterLayanan();
    });

    // Event handler klik tombol Lihat Detail pada kartu layanan
    $(document).on('click', '.view-detail-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadLayananDetail(id);
    });

    // Event handler klik tombol rekomendasi pada modal detail
    $(document).on('click', '.recommendation-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadLayananDetail(id);
    });

    // 3. Inisialisasi efek interaktif kemiringan 3D (tilt) pada kartu layanan saat disorot mouse
    function initTiltEffect() {
        $('.service-card-minimal').each(function() {
            const card = this;
            
            $(card).on('mousemove', function(e) {
                const cardRect = card.getBoundingClientRect();
                const cardWidth = cardRect.width;
                const cardHeight = cardRect.height;
                
                // Cari posisi kursor mouse relatif terhadap bidang kartu
                const mouseX = e.clientX - cardRect.left;
                const mouseY = e.clientY - cardRect.top;
                
                // Konversi koordinat kursor ke nilai persentase berkisar antara -0.5 hingga 0.5
                const pctX = (mouseX / cardWidth) - 0.5;
                const pctY = (mouseY / cardHeight) - 0.5;
                
                const maxRotate = 10; // Sudut maksimal kemiringan kartu (derajat)
                
                const rotateX = -pctY * maxRotate;
                const rotateY = pctX * maxRotate;
                
                const shadowX = pctX * 22;
                const shadowY = pctY * 22;
                
                // Terapkan transformasi 3D perspektif secara langsung (inline style)
                card.style.transition = 'transform 0.1s ease-out, box-shadow 0.1s ease-out, border-color 0.3s ease';
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.03, 1.03, 1.03)`;
                card.style.boxShadow = `${shadowX}px ${shadowY}px 24px rgba(15, 23, 42, 0.07)`;
                card.style.borderColor = 'rgba(37, 99, 235, 0.25)';
            });
            
            $(card).on('mouseleave', function() {
                // Kembalikan posisi kartu ke bentuk semula saat kursor keluar bidang kartu
                card.style.transition = 'transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease';
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                card.style.boxShadow = '0 4px 12px rgba(15, 23, 42, 0.015)';
                card.style.borderColor = '#e2e8f0';
            });
        });
    }

    // Jalankan inisialisasi efek tilt
    initTiltEffect();
});