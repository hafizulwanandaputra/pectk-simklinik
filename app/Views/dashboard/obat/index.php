<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/floating'); ?>
<style>
    .no-fluid-content {
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-right: auto;
        margin-left: auto;
        max-width: 1320px;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> obat</div>
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
    <div id="filterFields" class="sticky-top px-3 pt-2" style="z-index: 99; display: none;">
        <ul class="list-group no-fluid-content shadow-sm border border-bottom-0">
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
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari merek, nama obat, atau isi obat">
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
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Merek</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Isi</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Kategori</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Bentuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Harga Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">PPN</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Mark Up</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Pembulatan Harga</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Penyesuaian Harga</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Harga Jual</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Total Stok</th>
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
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="obatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="obatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="obatForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="obatModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_obat" name="id_obat">
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select " id="id_supplier" name="id_supplier" aria-label="id_supplier">
                            <option value="" disabled selected>-- Pilih Merek dan Pemasok --</option>
                        </select>
                        <label for="id_supplier">Merek dan Pemasok<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_obat" id="nama_obat" name="nama_obat">
                        <label for="nama_obat">Nama<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="isi_obat" id="isi_obat" name="isi_obat">
                        <label for="isi_obat">Isi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="kategori_obat" id="kategori_obat" name="kategori_obat" list="list_kategori_obat">
                        <label for="kategori_obat">Kategori Obat</label>
                        <div class="invalid-feedback"></div>
                        <datalist id="list_kategori_obat">
                            <option value="Antibiotik">
                            <option value="Antiinflamasi">
                            <option value="Antihistamin">
                            <option value="Dekongestan">
                            <option value="Pelumas">
                            <option value="Antiglaukoma">
                            <option value="Antivirus">
                            <option value="Antijamur">
                            <option value="Suplemen">
                        </datalist>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select " id="bentuk_obat" name="bentuk_obat" aria-label="bentuk_obat">
                            <option value="" disabled selected>-- Pilih Bentuk --</option>
                            <optgroup label="Obat Luar">
                                <option value="Tetes">Tetes</option>
                                <option value="Salep">Salep</option>
                            </optgroup>
                            <optgroup label="Obat Dalam">
                                <option value="Tablet/Kapsul">Tablet/Kapsul</option>
                                <option value="Sirup">Sirup</option>
                            </optgroup>
                            <optgroup label="Lainnya">
                                <option value="Alat Kesehatan">Alat Kesehatan</option>
                            </optgroup>
                        </select>
                        <label for="bentuk_obat">Bentuk<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="input-group has-validation mb-1 mt-1">
                        <span class="input-group-text">Rp</span>
                        <div class="form-floating">
                            <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="harga_obat" id="harga_obat" name="harga_obat">
                            <label for="harga_obat">Harga Obat<span class="text-danger">*</span></label>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="input-group has-validation mb-1 mt-1">
                        <div class="form-floating">
                            <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="ppn" id="ppn" name="ppn">
                            <label for="ppn">PPN<span class="text-danger">*</span></label>
                        </div>
                        <span class="input-group-text">%</span>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="input-group has-validation mb-1 mt-1">
                        <div class="form-floating">
                            <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="mark_up" id="mark_up" name="mark_up">
                            <label for="mark_up">Mark Up<span class="text-danger">*</span></label>
                        </div>
                        <span class="input-group-text">%</span>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="input-group has-validation mb-1 mt-1">
                        <span class="input-group-text">Rp</span>
                        <div class="form-floating">
                            <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="penyesuaian_harga" id="penyesuaian_harga" name="penyesuaian_harga">
                            <label for="penyesuaian_harga">Penyesuaian Harga<span class="text-danger">*</span></label>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <div>
                        Harga Jual: <span class="fw-bold date" id="hasil_harga_jual">Rp0</span>
                    </div>
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
                "sEmptyTable": 'Tidak ada obat. Klik "Tambah Obat" untuk menambahkan obat.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ obat",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 obat",
                "sInfoFiltered": "(di-filter dari _MAX_ obat)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ obat",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Obat yang Anda cari tidak ditemukan",
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
                text: '<i class="fa-solid fa-plus"></i> Tambah Obat',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addObatBtn'
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
                "url": "<?= base_url('/obat/obatlist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_obat}" data-name="${row.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'merek',
                    render: function(data, type, row) {
                        const merek = data ? data : '<em>Tanpa merek</em>';
                        return `<span>${merek}<br><small>${row.nama_supplier}</small></span>`;
                    }
                },
                {
                    data: 'nama_obat'
                },
                {
                    data: 'isi_obat'
                },
                {
                    data: 'kategori_obat'
                },
                {
                    data: 'bentuk_obat'
                },
                {
                    data: 'harga_obat',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'ppn',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${data}%</span>`;
                    }
                },
                {
                    data: 'mark_up',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${data}%</span>`;
                    }
                },
                {
                    data: 'selisih_harga',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'penyesuaian_harga',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'harga_jual',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'total_stok',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedTotalStok = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedTotalStok}</span>`;
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
                "target": [0, 1, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                "width": "0%"
            }, {
                "target": [2, 3],
                "width": "50%"
            }]
        });

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

        // Fungsi untuk mengambil opsi supplier dari server
        async function fetchSupplierOptions() {
            try {
                // Mengirim permintaan GET untuk mendapatkan daftar supplier
                const response = await axios.get('<?= base_url('obat/supplierlist') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#id_supplier');

                    // Hapus opsi yang sudah ada, kecuali yang pertama (placeholder)
                    select.find('option:not(:first)').remove();

                    // Tambahkan opsi supplier ke dalam elemen select
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                // Tampilkan pesan error jika terjadi kegagalan
                showFailedToast('Gagal mendapatkan dokter.<br>' + error);
            }
        }

        // Fungsi untuk menghitung harga jual
        function hitungHargaJual() {
            let hargaObat = parseFloat($('#harga_obat').val()) || 0;
            let ppn = parseFloat($('#ppn').val()) || 0;
            let markUp = parseFloat($('#mark_up').val()) || 0;
            let penyesuaianHarga = parseFloat($('#penyesuaian_harga').val()) || 0;

            // 1. Hitung PPN
            let jumlahPpn = (hargaObat * ppn) / 100;
            let totalHargaPpn = hargaObat + jumlahPpn;

            // 2. Hitung Mark Up
            let jumlahMarkUp = (totalHargaPpn * markUp) / 100;
            let totalHarga = totalHargaPpn + jumlahMarkUp;

            // 3. Bulatkan ke ratusan terdekat ke atas
            let hargaBulat = Math.ceil(totalHarga / 100) * 100;

            // 4. Tambahkan penyesuaian harga
            let hargaJual = hargaBulat + penyesuaianHarga;

            // 5. Tampilkan hasil dengan format Rupiah
            $('#hasil_harga_jual').text('Rp' + new Intl.NumberFormat('id-ID').format(hargaJual));
        }

        // Event listener untuk setiap input
        $('#harga_obat, #ppn, #mark_up, #penyesuaian_harga').on('input', function() {
            hitungHargaJual(); // Panggil fungsi saat ada perubahan nilai
        });

        // Event handler untuk menampilkan modal tambah obat
        $('#addObatBtn').click(async function() {
            $(this).prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Memuat
            `);
            try {
                await fetchSupplierOptions();
                $('#obatModalLabel').text('Tambah Obat');
                $('#stok_label').html(`Jumlah Stok Awal<span class="text-danger">*</span>`);
                $('#obatModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $(this).prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah Obat
                `);
            }
        });

        // Inisialisasi select2 untuk elemen #id_supplier
        $('#id_supplier').select2({});

        // Konfigurasi tambahan select2 dengan parent dropdown dari modal
        $('#obatModal').on('shown.bs.modal', function() {
            $('#id_supplier').select2({
                dropdownParent: $('#obatModal'),
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
                const response = await axios.get(`<?= base_url('/obat/obat') ?>/${id}`);
                await fetchSupplierOptions();
                const stok_obat_masuk = parseInt(response.data.jumlah_masuk).toLocaleString('id-ID');
                const stok_obat_keluar = parseInt(response.data.jumlah_keluar).toLocaleString('id-ID');
                const stok_sisa = parseInt(response.data.jumlah_masuk) - parseInt(response.data.jumlah_keluar);
                $('#obatModalLabel').text('Edit Obat');
                $('#id_obat').val(response.data.id_obat);
                $('#id_supplier').val(response.data.id_supplier);
                $('#nama_obat').val(response.data.nama_obat);
                $('#isi_obat').val(response.data.isi_obat);
                $('#kategori_obat').val(response.data.kategori_obat);
                $('#bentuk_obat').val(response.data.bentuk_obat);
                $('#harga_obat').val(response.data.harga_obat);
                $('#ppn').val(response.data.ppn);
                $('#mark_up').val(response.data.mark_up);
                $('#penyesuaian_harga').val(response.data.penyesuaian_harga);
                hitungHargaJual();
                $('#obatModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Variabel untuk menyimpan ID dan nama obat yang akan dihapus
        var obatId;
        var obatName;

        // Event handler untuk menampilkan modal konfirmasi hapus
        $(document).on('click', '.delete-btn', function() {
            obatId = $(this).data('id');
            obatName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + obatName + `"?`);
            $('#deleteModal').modal('show');
        });

        // Event handler untuk konfirmasi penghapusan obat
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/obat/delete') ?>/${obatId}`);
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
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        // Event handler untuk submit form obat
        $('#obatForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_obat').val() ? '<?= base_url('/obat/update') ?>' : '<?= base_url('/obat/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            $('#obatForm .is-invalid').removeClass('is-invalid');
            $('#obatForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses...</span>
            `);
            $('#obatForm input, #obatForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#obatModal').modal('hide');
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
                $('#obatForm input, #obatForm select, #closeBtn').prop('disabled', false);
            }
        });

        // Reset form dan status validasi saat modal obat ditutup
        $('#obatModal').on('hidden.bs.modal', function() {
            $('#obatForm')[0].reset();
            $('#id_obat').val('');
            $('#id_supplier').val(null).trigger('change');
            $('#id_supplier').find('option:not(:first)').remove();
            $('#hasil_harga_jual').text('Rp0');
            $('#obatForm .is-invalid').removeClass('is-invalid');
            $('#obatForm .invalid-feedback').text('').hide();
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>