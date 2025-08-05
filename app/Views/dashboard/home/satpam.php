<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .main-content-inside {
        margin-left: 0px;
    }

    .ratio-onecol {
        --bs-aspect-ratio: 33%;
    }

    #img_bpjs {
        color: inherit;
    }

    @media (max-width: 991.98px) {
        .ratio-onecol {
            --bs-aspect-ratio: 75%;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm" id="tanggal"></div>
            <div class="fw-medium lh-sm date" id="waktu" style="font-size: 0.75em;"></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3">
    <div class="no-fluid-content">
        <div class="text-center">
            <div class="mt-3 mb-2">
                <span class="text-center lh-sm d-flex justify-content-center align-items-center" style="font-size: 16pt;">
                    <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="56px">
                    <div class="ps-3 text-start text-success-emphasis fw-bold d-none d-lg-block">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                </span>
            </div>
            <h6><em>Melayani dengan Hati</em></h6>
            <div class="my-4">
                <h5><strong>Selamat Datang di Klinik Utama Mata Padang Eye Center Teluk Kuantan</strong></h5>
                <h6>Silakan ambil nomor antrean bagi pasien yang ingin berobat</h6>
            </div>
        </div>
        <div class="mb-3">
            <div class="row row-cols-1 row-cols-lg-3 g-3">
                <div class="col">
                    <div class="card h-100 rounded-5">
                        <div class="card-body text-center py-1">
                            <div style="font-size: 80pt;"><i class="fa-solid fa-users"></i></div>
                            <div class="fs-5 fw-bold">UMUM</div>
                        </div>
                        <div class="card-footer rounded-bottom-5 p-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-lg btn-primary bg-gradient rounded-4 btn-apply" data-name="UMUM">
                                    Buat Antrean
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 rounded-5">
                        <div class="card-body text-center py-1">
                            <div style="font-size: 80pt;">
                                <?= file_get_contents(FCPATH . 'assets/images/logo-bpjs.svg') ?>
                            </div>
                            <div class="fs-5 fw-bold">BPJS KESEHATAN</div>
                        </div>
                        <div class="card-footer rounded-bottom-5 p-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-lg btn-primary bg-gradient rounded-4 btn-apply" data-name="BPJS KESEHATAN">
                                    Buat Antrean
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 rounded-5">
                        <div class="card-body text-center py-1">
                            <div style="font-size: 80pt;"><i class="fa-solid fa-user-shield"></i></div>
                            <div class="fs-5 fw-bold">ASURANSI</div>
                        </div>
                        <div class="card-footer rounded-bottom-5 p-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-lg btn-primary bg-gradient rounded-4 btn-apply" data-name="ASURANSI">
                                    Buat Antrean
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2 mb-3">
            <button type="button" class="btn btn-body bg-gradient rounded-4" id="list_antrean_btn" data-bs-toggle="modal" data-bs-target="#listAntreanModal">Lihat Nomor Antrean Sebelumnya</button>
        </div>
    </div>
    <div class="modal fade" id="listAntreanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="listAntreanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable ">
            <div id="rajaldiv" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <div class="d-flex flex-row gap-2 me-2 w-100">
                        <select class="form-select form-select-sm w-auto" id="length-menu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="date" class="form-control form-control-sm" id="externalSearch">
                            <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                    <button id="listAntreanCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <table id="tabel" class="table table-sm table-hover m-0 p-0" style="width:100%; font-size: 0.75rem;">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No.</th>
                                <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jaminan</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Antrean</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tanggal dan Waktu Antrean</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Satpam</th>
                            </tr>
                        </thead>
                        <tbody class="align-top">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer pt-2 pb-2 d-flex justify-content-between" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <div id="loading"></div>
                    <button id="refreshButton" type="button" class="btn btn-primary btn-sm bg-gradient"><i class="fa-solid fa-arrows-rotate"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="printModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <p class="mb-0">Nomor antrean Anda adalah:</p>
                    <h1 class="mb-0 fw-medium" id="antrean"></h1>
                    <p class="mb-0">Jaminan: <span id="nama_jaminan"></span></p>
                    <p>Tanggal dan waktu: <span id="tanggal_antrean"></span></p>
                    <p class="mb-0">Nomor antrean ini akan dicetak secara otomatis. Jika tidak, klik "Cetak Nomor Antrean" untuk mencetaknya lagi.</p>
                    <iframe id="print_frame" style="display: none;"></iframe>
                    <div class="row gy-2 pt-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="cetak-btn">Cetak Nomor Antrean</button>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" id="closeModalBtn" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    })
</script>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script>
    let countdownTimer = null; // Untuk menyimpan referensi timer agar bisa dibatalkan

    // Aktifkan plugin dan set locale ke Bahasa Indonesia
    dayjs.extend(dayjs_plugin_localizedFormat);
    dayjs.locale('id');

    function updateDateTime() {
        const now = dayjs();
        $('#tanggal').text(now.format('dddd, D MMMM YYYY'));
        $('#waktu').text(now.format('HH.mm.ss'));
    }
    $(document).ready(async function() {
        var table = $('#tabel').DataTable({
            "oLanguage": {
                "sDecimal": ",",
                "sEmptyTable": 'Silakan pilih tanggal untuk melihat daftar antrean',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ antrean",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 antrean",
                "sInfoFiltered": "(di-filter dari _MAX_ antrean)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ antrean",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Antrean yang Anda cari tidak ditemukan",
                "oAria": {
                    "sOrderable": "Urutkan menurut kolom ini",
                    "sOrderableReverse": "Urutkan terbalik kolom ini"
                },
                "oPaginate": {
                    "sFirst": '<i class="fa-solid fa-angles-left"></i>',
                    "sLast": '<i class="fa-solid fa-angles-right"></i>',
                    "sPrevious": '<i class="fa-solid fa-angle-left"></i>',
                    "sNext": '<i class="fa-solid fa-angle-right"></i>'
                }
            },
            'dom': "<'row'<'col-md-12'tr>>" + "<'d-flex justify-content-center align-items-center'<'text-md-center text-lg-start'><'d-md-flex justify-content-md-center d-lg-block'p>>",
            'initComplete': function(settings, json) {
                $("#tabel").wrap("<div class='card shadow-sm  mb-3 overflow-auto position-relative datatables-height'></div>");
                $('.dataTables_filter input[type="search"]').css({
                    'width': '220px'
                });
                $('.dataTables_info').css({
                    'padding-top': '0',
                    'font-variant-numeric': 'tabular-nums'
                });
            },
            "drawCallback": function() {
                $(".pagination").wrap("<div class='overflow-auto'></div>");
                $(".pagination").addClass("pagination-sm");
                $(".page-item .page-link").addClass("bg-gradient date");
                var pageInfo = this.api().page.info();
                var infoText = `${pageInfo.recordsDisplay}`;
                $('#loading').html(`${infoText} antrean`);
            },
            "search": {
                "caseInsensitive": true
            },
            "searching": false, // Disable the internal search bar
            'pageLength': 25,
            'lengthMenu': "",
            "autoWidth": true,
            "processing": false,
            "serverSide": true,
            "ajax": {
                // URL endpoint untuk melakukan permintaan AJAX
                "url": "<?= base_url('/home/list_antrean') ?>",
                "type": "POST", // Metode HTTP yang digunakan untuk permintaan (POST)
                "data": function(d) {
                    // Menambahkan parameter tambahan pada data yang dikirim
                    d.search = {
                        "value": $('#externalSearch').val() // Mengambil nilai input pencarian
                    };
                },
                beforeSend: function() {
                    $('#loading').html(`<?= $this->include('spinner/spinner'); ?> Memuat...`);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Menampilkan toast error Bootstrap ketika permintaan AJAX gagal
                    showFailedToast('Gagal memuat data. Silakan coba lagi.'); // Menampilkan pesan kesalahan
                }
            },
            columns: [{
                    data: 'no',
                    render: function(data, type, row) {
                        return `<span class="date" style="display: block; text-align: center;">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="d-grid">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient cetak-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_antrean}" data-bs-toggle="tooltip" data-bs-title="Cetak"><i class="fa-solid fa-print"></i></button>
                            </div>
                            </div>`;
                    }
                },
                {
                    data: 'nama_jaminan'
                },
                {
                    data: 'kode_antrean',
                    render: function(data, type, row) {
                        return `<span class="date">${data}-${row.nomor_antrean}</span>`;
                    }
                },
                {
                    data: 'tanggal_antrean',
                    render: function(data, type, row) {
                        return `<span class="date">${data}</span>`;
                    }
                },
                {
                    data: 'satpam'
                },
            ],
            "order": [
                [3, 'desc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1, 3],
                "width": "0%"
            }, {
                "target": [2, 4, 5],
                "width": "33%"
            }],
        });
        // Menginisialisasi tooltip untuk elemen dengan atribut data-bs-toggle="tooltip"
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Memperbarui tooltip setiap kali tabel digambar ulang
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Bind the external search input to the table search
        $('#externalSearch').on('input', function() {
            table.search(this.value).draw(); // Trigger search on the table
        });

        // Kendalikan jumlah baris dengan dropdown custom
        $('#length-menu').on('change', function() {
            var length = $(this).val(); // Ambil nilai dari dropdown
            table.page.len(length).draw(); // Atur jumlah baris dan refresh tabel
        });
        $('#refreshButton').on('click', function(e) {
            e.preventDefault();
            table.ajax.reload(null, false);
        });
        $('#clearTglButton').on('click', function() {
            $('#externalSearch').val('');
            table.ajax.reload(null, false);
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                table.ajax.reload(null, false); // Reload data tanpa reset paging
            }
        });
        $('#listAntreanModal').on('shown.bs.modal', function() {
            table.columns.adjust();
            table.ajax.reload(null, false);
        });
        $('#listAntreanModal').on('hidden.bs.modal', function() {
            $('#externalSearch').val('');
            table.ajax.reload(null, false);
        });

        function cetakAntrean(id) {
            const $btn = $('#cetak-btn');
            const $closeBtn = $('#closeModalBtn');
            const $iframe = $('#print_frame');

            // Batalkan countdown sebelumnya jika ada
            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                $closeBtn.text('Tutup');
            }

            // Tampilkan loading di tombol cetak
            $closeBtn.prop('disabled', true).text('Tutup');
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Mencetak. Silakan tunggu...`);

            // Muat PDF ke iframe
            $iframe.attr('src', `<?= base_url("home/cetak_antrean") ?>/${id}`);

            // Saat iframe selesai dimuat
            $iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();

                    // Setelah berhasil, mulai countdown 5 detik untuk tutup modal
                    let countdown = 5;
                    $closeBtn.text(`Menutup dalam ${countdown} detik`);

                    countdownTimer = setInterval(() => {
                        countdown--;
                        if (countdown > 0) {
                            $closeBtn.text(`Menutup dalam ${countdown} detik`);
                        } else {
                            clearInterval(countdownTimer);
                            countdownTimer = null;
                            $('#printModal').modal('hide');
                            $closeBtn.text('Tutup');
                        }
                    }, 1000);

                } catch (e) {
                    showFailedToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`);
                } finally {
                    $btn.prop('disabled', false).html('Cetak Nomor Antrean');
                    $closeBtn.prop('disabled', false);
                }
            });
        }

        // Pemicu klik tombol cetak
        $('#cetak-btn').on('click', function() {
            const id = $(this).data('id');

            // Jika sedang menghitung mundur, batalkan
            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                $('#closeModalBtn').text('Tutup');
            }

            cetakAntrean(id);
        });
        $(document).on('click', '.cetak-btn', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $('.cetak-btn, #listAntreanCloseBtn, #refreshButton, #length-menu, #externalSearch, #clearTglButton').prop('disabled', true);
            $('.pagination .page-item:not(.previous):not(.next)').addClass('disabled');
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);
            $('[data-bs-toggle="tooltip"]').tooltip('hide');

            $('tr .sorting').css('pointer-events', 'none');

            // Muat PDF ke iframe
            var iframe = $('#print_frame');
            iframe.attr('src', `<?= base_url("home/cetak_antrean") ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`);
                } finally {
                    // Kembalikan tampilan tombol cetak
                    $('.cetak-btn, #listAntreanCloseBtn, #refreshButton, #length-menu, #externalSearch, #clearTglButton').prop('disabled', false);
                    $('.pagination .page-item:not(.previous):not(.next)').removeClass('disabled');
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-print"></i>');
                    $('tr .sorting').css('pointer-events', 'auto');
                }
            });
        });
        $('.btn-apply').on('click', async function(ə) {
            ə.preventDefault();
            const jaminan = $(this).data('name');
            const $btn = $(this);
            $('.btn-apply').prop('disabled', true);
            $btn.html(`
                <?= $this->include('spinner/spinner'); ?> Tunggu...
            `);

            try {
                const response = await axios.post(`<?= base_url('/home/buat_antrean') ?>?jaminan=${jaminan}`);

                // Simpan dulu opsi yang disabled
                const disabledOptions = $('#kode_antrean option:disabled').map(function() {
                    return this.value;
                }).get();

                // Aktifkan sementara
                $('#kode_antrean option').prop('disabled', false);

                // Reset nilai
                $('#kode_antrean').val('');

                // Kembalikan opsi yang tadi disabled
                disabledOptions.forEach(val => {
                    $(`#kode_antrean option[value="${val}"]`).prop('disabled', true);
                });
                const data = response.data.data;
                await Promise.all([
                    $('#antrean').text(data.antrean),
                    $('#nama_jaminan').text(data.nama_jaminan),
                    $('#tanggal_antrean').text(data.tanggal_antrean),
                    $('#cetak-btn').attr('data-id', data.id_antrean),
                ]);
                await $('#printModal').modal('show');
                cetakAntrean(data.id_antrean);
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('.btn-apply').prop('disabled', false);
                $btn.html(`
                    Buat Antrean
                `);
            }
        });
        $('#printModal').on('hidden.bs.modal', function() {
            $('#cetak-btn').attr('data-id', '');
            $('#antrean').text('');
            $('#nama_jaminan').text('');
            $('#tanggal_antrean').text('');
            // Jika sedang menghitung mundur, batalkan
            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                $('#closeModalBtn').text('Tutup');
            }
        });
        $('#loadingSpinner').hide();
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>