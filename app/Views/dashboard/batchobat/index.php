<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/floating'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> faktur</div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <a id="toggleFilter" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Pencarian"><i class="fa-solid fa-magnifying-glass"></i></a>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div id="filterFields" class="sticky-top" style="z-index: 99; display: none;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-row gap-2">
                        <select class="form-select form-select-sm w-auto" id="length-menu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari obat, nomor faktur, nama batch, atau tanggal kedaluwarsa">
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="mb-3">
                <table id="tabel" class="table table-sm table-hover m-0 p-0" style="width:100%; font-size: 0.75rem;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No</th>
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Faktur</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama <em>Batch</em></th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Kedaluwarsa</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jumlah Masuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jumlah Keluar</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Sisa Stok</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody class="align-top">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                    <div class="row gx-2 pt-3">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Tidak</button>
                        </div>
                        <div class="col d-grid">
                            <button type="submit" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Ya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bacthObatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="batchObatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="batchObatForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="batchObatModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_batch_obat" name="id_batch_obat">
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select " id="id_obat" name="id_obat" aria-label="id_obat">
                            <option value="" disabled selected>-- Pilih Obat --</option>
                        </select>
                        <label for="id_obat">Obat<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="no_faktur" id="no_faktur" name="no_faktur" list="no_faktur_list">
                        <label for="no_faktur">Nomor Faktur</label>
                        <datalist id="no_faktur_list">
                        </datalist>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_batch" id="nama_batch" name="nama_batch">
                        <label for="nama_batch">Nama</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="date" class="form-control " autocomplete="off" dir="auto" placeholder="tgl_kedaluwarsa" id="tgl_kedaluwarsa" name="tgl_kedaluwarsa">
                        <label for="tgl_kedaluwarsa">Kedaluwarsa<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="jumlah_masuk" id="jumlah_masuk" name="jumlah_masuk">
                        <label for="jumlah_masuk" id="stok_label"></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="stok_jumlah" class="mb-1 mt-1">
                        <div class="mb-0 row g-1 overflow-hidden d-flex align-items-end">
                            <div class="col fw-medium text-nowrap">Obat Masuk</div>
                            <div class="col text-end">
                                <div class="date text-truncate" id="stok_obat_masuk">
                                </div>
                            </div>
                        </div>
                        <div class="mb-0 row g-1 overflow-hidden d-flex  align-items-end">
                            <div class="col fw-medium text-nowrap">Obat Keluar</div>
                            <div class="col text-end">
                                <div class="date text-truncate" id="stok_obat_keluar">
                                </div>
                            </div>
                        </div>
                        <div class="mb-0 row g-1 overflow-hidden d-flex  align-items-end">
                            <div class="col fw-medium text-nowrap">Sisa Stok</div>
                            <div class="col text-end">
                                <div class="date text-truncate" id="stok_sisa">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient ">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('datatable'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#tabel').DataTable({
            "oLanguage": {
                "sDecimal": ",",
                "sEmptyTable": 'Tidak ada faktur obat. Klik "Tambah Faktur Obat" untuk menambahkan faktur obat.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ faktur obat",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 faktur obat",
                "sInfoFiltered": "(di-filter dari _MAX_ faktur obat)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ faktur obat",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Faktur obat yang Anda cari tidak ditemukan",
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
            'dom': "<'d-grid'<'mt-0 mb-md-2'B>>" + "<'row'<'col-md-12'tr>>" + "<'d-lg-flex justify-content-lg-between align-items-lg-center'<'text-md-center text-lg-start'><'d-md-flex justify-content-md-center d-lg-block'p>>",
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
                $('#total_datatables').html(infoText);
            },
            'buttons': [{
                // Tombol Tambah Obat
                text: '<i class="fa-solid fa-plus"></i> Tambah Faktur Obat',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addBatchObatBtn'
                },
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }],
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
                "url": "<?= base_url('/batchobat/batchobatlist') ?>",
                "type": "POST", // Metode HTTP yang digunakan untuk permintaan (POST)
                "data": function(d) {
                    // Menambahkan parameter tambahan pada data yang dikirim
                    d.search = {
                        "value": $('#externalSearch').val() // Mengambil nilai input pencarian
                    };
                },
                beforeSend: function() {
                    // Menampilkan spinner loading sebelum permintaan dikirim
                    $('#loadingSpinner').show(); // Menampilkan elemen spinner loading
                },
                complete: function() {
                    // Menyembunyikan spinner loading setelah permintaan selesai
                    $('#loadingSpinner').hide(); // Menyembunyikan elemen spinner loading
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Menyembunyikan spinner loading jika terjadi kesalahan
                    $('#loadingSpinner').hide(); // Menyembunyikan elemen spinner loading
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
                        return `<div class="btn-group" role="group">
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_batch_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_batch_obat}" data-name1="${row.no_faktur}" data-name2="${row.nama_batch}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'obat',
                    render: function(data, type, row) {
                        const kategori_obat = row.kategori_obat ? `${row.kategori_obat}, ` : ``;
                        return `<div>${row.nama_obat}
                        <small>
                            <ul class="ps-3 mb-0">
                                <li>${kategori_obat}${row.bentuk_obat}</li>
                            </ul>
                        </small></div>`;
                    }
                },
                {
                    data: 'no_faktur',
                    render: function(data, type, row) {
                        const no_faktur = data ? data : '<em>Tidak ada nomor faktur</em>';
                        return no_faktur;
                    }
                },
                {
                    data: 'nama_batch',
                    render: function(data, type, row) {
                        const nama_batch = data ? data : '<em>Tanpa nama</em>';
                        return nama_batch;
                    }
                },
                {
                    data: 'tgl_kedaluwarsa',
                    render: function(data, type, row) {
                        const now = new Date();
                        const expiryDate = new Date(data);

                        let badgeClass = '';
                        let statusText = '';

                        if (expiryDate < now) {
                            badgeClass = 'bg-danger';
                            statusText = 'Kedaluwarsa';
                        } else {
                            // Salin tanggal untuk perhitungan
                            let start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                            let end = new Date(expiryDate.getFullYear(), expiryDate.getMonth(), expiryDate.getDate());

                            // Hitung selisih bulan
                            let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());

                            // Hitung selisih hari dengan membandingkan tanggal
                            let anchor = new Date(start.getFullYear(), start.getMonth() + months, start.getDate());
                            let days = Math.round((end - anchor) / (1000 * 60 * 60 * 24));

                            if (days < 0) {
                                months--;
                                anchor = new Date(start.getFullYear(), start.getMonth() + months, start.getDate());
                                days = Math.round((end - anchor) / (1000 * 60 * 60 * 24));
                            }

                            if (months < 6 || (months === 6 && days === 0)) {
                                badgeClass = 'bg-warning text-dark';

                                if (months === 0 && days === 0) {
                                    statusText = 'Hari terakhir';
                                } else if (months === 0) {
                                    statusText = `${days} hari lagi`;
                                } else if (days === 0) {
                                    statusText = `${months} bulan lagi`;
                                } else {
                                    statusText = `${months} bulan ${days} hari lagi`;
                                }
                            } else {
                                badgeClass = 'bg-success';
                                statusText = 'Aktif';
                            }
                        }

                        return `<span class="date text-nowrap">${data}<br><span class="badge ${badgeClass} bg-gradient">${statusText}</span></span>`;
                    }
                },
                {
                    data: 'jumlah_masuk',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'jumlah_keluar',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'sisa_stok',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'diperbarui',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },

            ],
            "order": [
                [2, 'asc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1, 5, 6, 7, 8, 9],
                "width": "0%"
            }, {
                "target": [2, 3, 4],
                "width": "25%"
            }]
        });

        // Fungsi untuk mengambil opsi obat dari server
        async function fetchObatOptions() {
            try {
                // Mengirim permintaan GET untuk mendapatkan daftar obat
                const response = await axios.get('<?= base_url('batchobat/obatlist') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#id_obat');

                    // Hapus opsi yang sudah ada, kecuali yang pertama (placeholder)
                    select.find('option:not(:first)').remove();

                    // Tambahkan opsi obat ke dalam elemen select
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                // Tampilkan pesan error jika terjadi kegagalan
                showFailedToast('Gagal mendapatkan obat.<br>' + error);
            }
        }

        async function fetchNoFakturOptions() {
            try {
                // Mengirim permintaan GET untuk mendapatkan daftar nomor faktur
                const response = await axios.get('<?= base_url('batchobat/fakturlist') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const datalist = $('#no_faktur_list');

                    // Hapus opsi yang sudah ada, kecuali yang pertama (placeholder)
                    datalist.find('option').remove();

                    // Tambahkan opsi obat ke dalam elemen datalist
                    options.forEach(option => {
                        datalist.append(`<option value="${option.value}">`);
                    });
                }
            } catch (error) {
                // Tampilkan pesan error jika terjadi kegagalan
                showFailedToast('Gagal mendapatkan nomor faktur.<br>' + error);
            }
        }

        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update) {
                console.log("Received update from WebSocket");
                table.ajax.reload(null, false);
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

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

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                table.ajax.reload(null, false); // Reload data tanpa reset paging
            }
        });

        $('#refreshButton').on('click', function(e) {
            e.preventDefault();
            table.ajax.reload(null, false);
        });

        // Event handler untuk menampilkan modal tambah obat
        $('#addBatchObatBtn').click(async function() {
            $(this).prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Memuat
            `);
            try {
                await fetchObatOptions();
                $('#batchObatModalLabel').html('Tambah Faktur Obat');
                $('#stok_label').html(`Jumlah Stok Awal<span class="text-danger">*</span>`);
                $('#stok_obat_masuk').text(``);
                $('#stok_obat_keluar').text(``);
                $('#stok_sisa').text(``);
                $('#stok_jumlah').hide();
                $('#bacthObatModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $(this).prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah Faktur Obat
                `);
            }
        });

        // Inisialisasi select2 untuk elemen #id_obat
        $('#id_obat').select2({});

        // Konfigurasi tambahan select2 dengan parent dropdown dari modal
        $('#bacthObatModal').on('shown.bs.modal', function() {
            $('#id_obat').select2({
                dropdownParent: $('#bacthObatModal'),
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
            });
        });

        // Event handler untuk menampilkan modal edit obat
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);

            try {
                const response = await axios.get(`<?= base_url('/batchobat/batchobat') ?>/${id}`);
                await fetchObatOptions();
                const stok_obat_masuk = parseInt(response.data.jumlah_masuk).toLocaleString('id-ID');
                const stok_obat_keluar = parseInt(response.data.jumlah_keluar).toLocaleString('id-ID');
                const stok_sisa = parseInt(response.data.jumlah_masuk) - parseInt(response.data.jumlah_keluar);
                $('#batchObatModalLabel').html('Edit Faktur Obat');
                $('#id_batch_obat').val(response.data.id_batch_obat);
                $('#id_obat').val(response.data.id_obat);
                $('#no_faktur').val(response.data.no_faktur);
                $('#nama_batch').val(response.data.nama_batch);
                $('#tgl_kedaluwarsa').val(response.data.tgl_kedaluwarsa);
                $('#jumlah_masuk').val('0');
                $('#stok_label').html(`Tambah/Kurangi Stok<span class="text-danger">*</span>`);
                $('#stok_obat_masuk').text(stok_obat_masuk);
                $('#stok_obat_keluar').text(stok_obat_keluar);
                $('#stok_sisa').text(stok_sisa.toLocaleString('id-ID'));
                $('#stok_jumlah').show();
                $('#bacthObatModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Variabel untuk menyimpan ID dan nama obat yang akan dihapus
        var batchObatId;
        var fakturObatName;
        var batchObatName;

        // Event handler untuk menampilkan modal konfirmasi hapus
        $(document).on('click', '.delete-btn', function() {
            batchObatId = $(this).data('id');
            const name1 = $(this).data('name1');
            fakturObatName = name1 ? ` dari ${name1}` : '';
            batchObatName = $(this).data('name2');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "${batchObatName}"${fakturObatName}?`);
            $('#deleteModal').modal('show');
        });

        // Event handler untuk konfirmasi penghapusan obat
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Mengapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/batchobat/delete') ?>/${batchObatId}`);
                table.ajax.reload(null, false);
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error;
                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = 'Tidak dapat menghapus data ini karena sedang digunakan.'
                }
                showFailedToast(errorMessage);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        // Event handler untuk submit form obat
        $('#batchObatForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_batch_obat').val() ? '<?= base_url('/batchobat/update') ?>' : '<?= base_url('/batchobat/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            $('#batchObatForm .is-invalid').removeClass('is-invalid');
            $('#batchObatForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses...</span>
            `);
            $('#batchObatForm input, #batchObatForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#bacthObatModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    console.log("Validation Errors:", response.data.errors);
                    for (const field in response.data.errors) {
                        const fieldElement = $('#' + field);
                        let feedbackElement = fieldElement.siblings('.invalid-feedback');

                        // Handle input-group cases
                        if (fieldElement.closest('.input-group').length) {
                            feedbackElement = fieldElement.closest('.input-group').find('.invalid-feedback');
                        }

                        if (fieldElement.length && feedbackElement.length) {
                            fieldElement.addClass('is-invalid');
                            feedbackElement.text(response.data.errors[field]).show();

                            fieldElement.on('input change', function() {
                                $(this).removeClass('is-invalid');
                                $(this).siblings('.invalid-feedback').text('').hide();
                            });
                        }
                    }
                }
            } catch (error) {
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#batchObatForm input, #batchObatForm select, #closeBtn').prop('disabled', false);
            }
        });

        // Reset form dan status validasi saat modal obat ditutup
        $('#bacthObatModal').on('hidden.bs.modal', function() {
            $('#batchObatForm')[0].reset();
            $('#id_batch_obat').val('');
            $('#id_obat').val(null).trigger('change');
            $('#id_obat').find('option:not(:first)').remove();
            $('#no_faktur_list').find('option').remove();
            $('#batchObatForm .is-invalid').removeClass('is-invalid');
            $('#batchObatForm .invalid-feedback').text('').hide();
        });

        $('#bacthObatModal').on('shown.bs.modal', function() {
            fetchNoFakturOptions();
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>