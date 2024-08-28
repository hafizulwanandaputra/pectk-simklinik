<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <div class="mb-2">
        <table id="tabel" class="table table-sm table-hover" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tanggal</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Menu</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Petugas Gizi</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama Pasien</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tanggal Lahir</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis Kelamin</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Kamar</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Diet</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Keterangan</th>
                </tr>
            </thead>
            <tbody class="align-top">
            </tbody>
        </table>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="demandDetails" tabindex="-1" aria-labelledby="demandDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-lg modal-dialog-centered modal-dialog-scrollable rounded-3">
            <div class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="staticBackdropLabel" style="font-weight: bold;">Detail Permintaan Pasien</h6>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <fieldset class="border rounded-3 px-2 py-0 my-2" style="border-color: var(--bs-border-color-translucent)!important;">
                        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Menu</legend>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Tanggal</div>
                            <div class="col-lg">
                                <div class="date" id="tanggal_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Nama Menu</div>
                            <div class="col-lg">
                                <div>
                                    <div class="mb-1 date fw-bold" id="nama_menu_d"></div>
                                    <div class="mb-1 row fs-6">
                                        <div class="col-5 fw-medium">Protein Hewani</div>
                                        <div class="col">
                                            <div id="protein_hewani_d"></div>
                                        </div>
                                    </div>
                                    <div class="mb-1 row fs-6">
                                        <div class="col-5 fw-medium">Protein Nabati</div>
                                        <div class="col">
                                            <div id="protein_nabati_d"></div>
                                        </div>
                                    </div>
                                    <div class="mb-1 row fs-6">
                                        <div class="col-5 fw-medium">Sayur</div>
                                        <div class="col">
                                            <div id="sayur_d"></div>
                                        </div>
                                    </div>
                                    <div class="mb-1 row fs-6">
                                        <div class="col-5 fw-medium">Buah</div>
                                        <div class="col">
                                            <div id="buah_d"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Jadwal Makan</div>
                            <div class="col-lg">
                                <div class="date" id="jadwal_makan_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Petugas Gizi</div>
                            <div class="col-lg">
                                <div class="date" id="nama_petugas_d"></div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="border rounded-3 px-2 py-0 my-2" style="border-color: var(--bs-border-color-translucent)!important;">
                        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Identitas Pasien</legend>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Nama Pasien</div>
                            <div class="col-lg">
                                <div id="nama_pasien_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Tanggal Lahir</div>
                            <div class="col-lg">
                                <div class="date" id="tanggal_lahir_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Jenis Kelamin</div>
                            <div class="col-lg">
                                <div class="date" id="jenis_kelamin_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Kamar</div>
                            <div class="col-lg">
                                <div class="date" id="kamar_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Jenis Tindakan</div>
                            <div class="col-lg">
                                <div class="date" id="jenis_tindakan_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Diet</div>
                            <div class="col-lg">
                                <div class="date" id="diet_d"></div>
                            </div>
                        </div>
                        <div class="mb-2 row fs-6">
                            <div class="col-lg-3 fw-medium">Keterangan</div>
                            <div class="col-lg">
                                <div class="date" id="keterangan_d"></div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button class="btn btn-primary bg-gradient rounded-3" target="_blank" id="printeticket"><i class="fa-solid fa-print"></i> Cetak E-tiket</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="demandModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="demandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-lg modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="demandForm" enctype="multipart/form-data" class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="demandModalLabel" style="font-weight: bold;">Tambah Permintaan</h6>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="demandId" name="id_permintaan">
                    <input type="hidden" id="id_menu_lama" name="id_menu_lama">
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="id_menu" name="id_menu" aria-label="id_menu">
                            <option value="">-- Pilih Menu dan Petugas --</option>
                        </select>
                        <label for="id_menu">Menu*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="text" class="form-control rounded-3" id="nama_pasien" name="nama_pasien" autocomplete="off" dir="auto" placeholder="nama_pasien">
                        <label for="nama_pasien">Nama Pasien*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="date" class="form-control rounded-3" id="tanggal_lahir" name="tanggal_lahir" autocomplete="off" dir="auto" placeholder="tanggal_lahir">
                        <label for="tanggal_lahir">Tanggal Lahir*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mt-1 mb-1 row">
                        <label for="jenis_kelamin" class="col-xl-3 col-form-label">Jenis Kelamin*</label>
                        <div class="col-lg col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly justify-content-xl-start">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin1" value="Laki-Laki">
                                    <label class="form-check-label" for="jenis_kelamin1">
                                        Laki-Laki
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin2" value="Perempuan">
                                    <label class="form-check-label" for="jenis_kelamin2">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="text" class="form-control rounded-3" id="kamar" name="kamar" autocomplete="off" dir="auto" placeholder="kamar">
                        <label for="kamar">Kamar*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="text" class="form-control rounded-3" id="jenis_tindakan" name="jenis_tindakan" autocomplete="off" dir="auto" placeholder="jenis_tindakan">
                        <label for="jenis_tindakan">Jenis Tindakan*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <input type="text" class="form-control rounded-3" id="diet" name="diet" autocomplete="off" dir="auto" placeholder="diet">
                        <label for="diet">Diet*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <textarea style="resize: none; height: 96px; white-space: pre-wrap;" class="form-control rounded-3" id="keterangan" name="keterangan" dir="auto" placeholder="keterangan"></textarea>
                        <label for="keterangan">Keterangan (Alergi/Pantangan Makanan)</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient rounded-3">
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
    /* For Export Buttons available inside jquery-datatable "server side processing" - Start
- due to "server side processing" jquery datatble doesn't support all data to be exported
- below function makes the datatable to export all records when "server side processing" is on */

    function newexportaction(e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function(e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function(e, settings) {
                // Call the original action function
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function(e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(dt.ajax.reload, 0);
                // Prevent rendering of the full data to the DOM
                return false;
            });
        });
        // Requery the server with the new one-time export settings
        dt.ajax.reload();
    };
    //For Export Buttons available inside jquery-datatable "server side processing" - End
    // Inisialisasi Datatables
    $(document).ready(function() {
        var table = $('#tabel').DataTable({
            "oLanguage": {
                "sDecimal": ",",
                "sEmptyTable": 'Tidak ada permintaan pasien. Klik "Tambah Permintaan" untuk menambahkan permintaan.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ permintaan",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 permintaan",
                "sInfoFiltered": "(di-filter dari _MAX_ permintaan)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ permintaan",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Permintaan pasien yang Anda cari tidak ditemukan",
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
            'dom': "<'d-lg-flex justify-content-lg-between align-items-lg-center mb-0'<'text-md-center text-lg-start'i><'d-md-flex justify-content-md-center d-lg-block'f>>" +
                "<'d-lg-flex justify-content-lg-between align-items-lg-center'<'text-md-center text-lg-start mt-2'l><'mt-2 mb-2 mb-lg-0'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'d-lg-flex justify-content-lg-between align-items-lg-center'<'text-md-center text-lg-start'><'d-md-flex justify-content-md-center d-lg-block'p>>",
            'initComplete': function(settings, json) {
                $("#tabel").wrap("<div class='overflow-auto position-relative'></div>");
                $('.dataTables_filter input[type="search"]').css({
                    'width': '220px'
                });
                $('.dataTables_info').css({
                    'padding-top': '0',
                    'font-variant-numeric': 'tabular-nums'
                });
            },
            "drawCallback": function() {
                var api = this.api();
                api.column(0, {
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                    $(cell).css({
                        'font-variant-numeric': 'tabular-nums'
                    });
                });
                $(".pagination").wrap("<div class='overflow-auto'></div>");
                $(".pagination").addClass("pagination-sm");
                $('.pagination-sm').css({
                    '--bs-pagination-border-radius': 'var(--bs-border-radius-lg)'
                });
                $(".page-item .page-link").addClass("bg-gradient");
                $(".form-control").addClass("rounded-3");
                $(".form-select").addClass("rounded-3");
                feather.replace({
                    'aria-hidden': 'true'
                });
            },
            'buttons': [{
                action: function(e, dt, node, config) {
                    dt.ajax.reload(null, false);
                },
                text: '<i class="fa-solid fa-arrows-rotate"></i> Refresh',
                className: 'btn-primary btn-sm bg-gradient rounded-start-3',
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }, {
                text: '<i class="fa-solid fa-plus"></i> Tambah Permintaan',
                className: 'btn-primary btn-sm bg-gradient rounded-end-3',
                attr: {
                    id: 'addDemandBtn'
                },
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }],
            "search": {
                "caseInsensitive": true
            },
            'pageLength': 25,
            'lengthMenu': [
                [25, 50, 100, 250, 500],
                [25, 50, 100, 250, 500]
            ],
            "autoWidth": true,
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('/permintaan/permintaanlist') ?>",
                "type": "POST",
                "data": function(d) {
                    // Additional parameters
                    d.search = {
                        "value": $('.dataTables_filter input[type="search"]').val()
                    };
                },
                beforeSend: function() {
                    // Show the custom processing spinner
                    $('#loadingSpinner').show();
                },
                complete: function() {
                    // Hide the custom processing spinner after the request is complete
                    $('#loadingSpinner').hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Hide the custom processing spinner on error
                    $('#loadingSpinner').hide();
                    // Show the Bootstrap error toast when the AJAX request fails
                    showFailedToast('Gagal memuat data. Silakan coba lagi.');
                }
            },
            columns: [{
                    data: null
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button class="btn btn-info text-nowrap bg-gradient rounded-start-3 details-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id}" data-bs-toggle="tooltip" data-bs-title="Detail"><i class="fa-solid fa-circle-info"></i></button>
                                    <button class="btn btn-secondary text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id}" data-name="${row.nama_pasien}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'tanggal',
                    render: function(data, type, row) {
                        return `<div class="date text-nowrap">
                                    ${data}
                                </div>`;
                    }
                },
                {
                    data: 'nama_menu',
                    render: function(data, type, row) {
                        return `
                        <strong>${data}</strong><div class="text-nowrap">Protein Hewani: ${row.protein_hewani}<br>Protein Nabati: ${row.protein_nabati}<br>Sayur: ${row.sayur}<br>Buah: ${row.buah}</div>
                        `;
                    }
                },
                {
                    data: 'nama_petugas'
                },
                {
                    data: 'nama_pasien'
                },
                {
                    data: 'tanggal_lahir',
                    render: function(data, type, row) {
                        return `<div class="date text-nowrap">
                                    ${data}
                                </div>`;
                    }
                },
                {
                    data: 'jenis_kelamin'
                },
                {
                    data: 'kamar'
                },
                {
                    data: 'jenis_tindakan'
                },
                {
                    data: 'diet'
                },
                {
                    data: 'keterangan'
                },
            ],
            "order": [
                [5, 'asc']
            ],
            "columnDefs": [{
                "target": [0, 1],
                "orderable": false
            }, {
                "target": [0, 1, 2, 6, 7, 8],
                "width": "0%"
            }, {
                "target": [3, 4, 5, 9, 10, 11],
                "width": "12.5%"
            }]
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Re-initialize tooltips on table redraw (server-side events like pagination, etc.)
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        async function fetchMenuOptions() {
            try {
                const response = await axios.get('<?= base_url('permintaan/menuoptions') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#id_menu');

                    // Clear existing options except the first one
                    select.find('option:not(:first)').remove();

                    // Loop through the options and append them to the select element
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                console.error('Gagal mendapatkan menu options.', error);
            }
        }

        // Call the function to fetch and populate the menu options
        fetchMenuOptions();

        // Show add user modal
        $('#addDemandBtn').click(function() {
            $('#demandModalLabel').text('Tambah Permintaan');
            $('#demandForm')[0].reset();
            $('#demandId').val('');
            $('#demandModal').modal('show');
        });
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/permintaan/permintaan') ?>/${id}`);

                $('#demandModalLabel').text('Edit Petugas Gizi');
                $('#demandId').val(response.data.id);
                $('#id_menu_lama').val(response.data.id_menu);
                $('#id_menu').val(response.data.id_menu);
                $('#nama_pasien').val(response.data.nama_pasien);
                $('#tanggal_lahir').val(response.data.tanggal_lahir);
                const selectedGender = response.data.jenis_kelamin;
                if (selectedGender) {
                    $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
                }
                $('#kamar').val(response.data.kamar);
                $('#jenis_tindakan').val(response.data.jenis_tindakan);
                $('#diet').val(response.data.diet);
                $('#keterangan').val(response.data.keterangan);
                $('#demandModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $(document).on('click', '.details-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/permintaan/permintaan') ?>/${id}`);

                $('#tanggal_d').text(response.data.tanggal);
                $('#nama_menu_d').text(response.data.nama_menu);
                $('#protein_hewani_d').text(response.data.protein_hewani);
                $('#protein_nabati_d').text(response.data.protein_nabati);
                $('#sayur_d').text(response.data.sayur);
                $('#buah_d').text(response.data.buah);
                $('#jadwal_makan_d').text(response.data.jadwal_makan);
                $('#nama_petugas_d').text(response.data.nama_petugas);
                $('#nama_pasien_d').text(response.data.nama_pasien);
                $('#tanggal_lahir_d').text(response.data.tanggal_lahir);
                $('#jenis_kelamin_d').text(response.data.jenis_kelamin);
                $('#kamar_d').text(response.data.kamar);
                $('#jenis_tindakan_d').text(response.data.jenis_tindakan);
                $('#diet_d').text(response.data.diet);
                $('#keterangan_d').text(response.data.keterangan);
                $('#printeticket').attr('onclick', `window.open("<?= base_url('/permintaan/eticketprint') ?>/${id}", "Window","status=1,toolbar=1,width=500,height=400,resizable=yes")`);
                $('#demandDetails').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-circle-info"></i>`);
            }
        });

        // Store the ID of the user to be deleted
        var permintaanId;
        var permintaanName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            permintaanId = $(this).data('id');
            permintaanName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + permintaanName + `"?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html(`Mengapus, silakan tunggu...`);

            try {
                await axios.delete(`<?= base_url('/permintaan/delete') ?>/${permintaanId}`);
                showSuccessToast('Data berhasil dihapus.');
                table.ajax.reload();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#demandForm').submit(async function(e) {
            e.preventDefault();
            const url = $('#demandId').val() ? '<?= base_url('/permintaan/update') ?>' : '<?= base_url('/permintaan/create') ?>';
            const formData = new FormData(this);

            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#demandForm .is-invalid').removeClass('is-invalid');
            $('#demandForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses, silakan tunggu...</span>
            `);

            // Disable form inputs
            $('#demandForm input, #demandForm select, #demandForm textarea, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#demandModal').modal('hide');
                    table.ajax.reload();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#demandForm .is-invalid').removeClass('is-invalid');
                    $('#demandForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'jenis_kelamin') {
                                const radioGroup = $("input[name='jenis_kelamin']");
                                const feedbackElement = radioGroup.closest('.col-form-label').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[name='jenis_kelamin']").removeClass('is-invalid');
                                        feedbackElement.removeAttr('style').hide();
                                    });
                                }
                            } else {
                                const feedbackElement = fieldElement.siblings('.invalid-feedback');

                                if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                    fieldElement.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user corrects the input
                                    fieldElement.on('input change', function() {
                                        $(this).removeClass('is-invalid');
                                        $(this).siblings('.invalid-feedback').text('').hide();
                                    });
                                } else {
                                    console.warn("Element not found for field:", field);
                                }
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#demandForm input, #demandForm select, #demandForm textarea, #closeBtn').prop('disabled', false);
            }
        });

        $('#demandModal').on('hidden.bs.modal', function() {
            $('#demandForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
        });

        function showSuccessToast(message) {
            var toastHTML = `<div id="toast" class="toast fade show align-items-center text-bg-success border border-success rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start">
                    <div style="width: 24px; text-align: center;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="w-100 mx-2 text-start" id="toast-message">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
            var toastElement = $(toastHTML);
            $('#toastContainer').append(toastElement); // Make sure there's a container with id `toastContainer`
            var toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        function showFailedToast(message) {
            var toastHTML = `<div id="toast" class="toast fade show align-items-center text-bg-danger border border-danger rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start">
                    <div style="width: 24px; text-align: center;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="w-100 mx-2 text-start" id="toast-message">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
            var toastElement = $(toastHTML);
            $('#toastContainer').append(toastElement); // Make sure there's a container with id `toastContainer`
            var toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    });
</script>
<?= $this->endSection(); ?>