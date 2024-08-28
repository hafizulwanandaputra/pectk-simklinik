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
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama Menu</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jadwal Makan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Petugas Gizi</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jumlah Permintaan</th>
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
                    <h5 id="deleteMessage"></h5>
                    <h6 class="mb-0" id="deleteSubmessage"></h6>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="menuModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="menuForm" enctype="multipart/form-data" class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="menuModalLabel" style="font-weight: bold;">Tambah Menu Makanan</h6>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="menuId" name="id_menu">
                    <input type="hidden" id="jumlah" name="jumlah">
                    <input type="hidden" id="id_petugas_lama" name="id_petugas_lama">
                    <fieldset class="border rounded-3 px-1 py-0 mt-1 mb-1">
                        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Menu Makanan (wajib diisi)</legend>
                        <div class="form-floating mb-1 mt-1">
                            <input type="date" class="form-control" autocomplete="off" dir="auto" placeholder="tanggal" id="tanggal" name="tanggal">
                            <label for="tanggal">Tanggal</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mb-1 mt-1">
                            <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_menu" id="nama_menu" name="nama_menu">
                            <label for="nama_menu">Nama Menu</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mb-1 mt-1">
                            <select class="form-select" id="jadwal_makan" name="jadwal_makan" aria-label="jadwal_makan">
                                <option value="">-- Pilih Jadwal Makan --</option>
                                <option value="Pagi">Pagi</option>
                                <option value="Siang">Siang</option>
                                <option value="Sore">Sore</option>
                                <option value="Malam">Malam</option>
                            </select>
                            <label for="jadwal_makan">Jadwal Makan</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mb-1 mt-1">
                            <select class="form-select" id="id_petugas" name="id_petugas" aria-label="id_petugas">
                                <option value="">-- Pilih Petugas --</option>
                            </select>
                            <label for="id_petugas">Petugas</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </fieldset>
                    <fieldset class="border rounded-3 px-1 py-0 mt-1 mb-1">
                        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Gizi (opsional)</legend>
                        <div class="form-floating mt-1 mb-1">
                            <input type="text" class="form-control rounded-3" id="protein_hewani" name="protein_hewani" autocomplete="off" dir="auto" placeholder="protein_hewani">
                            <label for="protein_hewani">Protein Hewani</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mt-1 mb-1">
                            <input type="text" class="form-control rounded-3" id="protein_nabati" name="protein_nabati" autocomplete="off" dir="auto" placeholder="protein_nabati">
                            <label for="protein_nabati">Protein Nabati</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mt-1 mb-1">
                            <input type="text" class="form-control rounded-3" id="sayur" name="sayur" autocomplete="off" dir="auto" placeholder="sayur">
                            <label for="sayur">Sayur</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-floating mt-1 mb-1">
                            <input type="text" class="form-control rounded-3" id="buah" name="buah" autocomplete="off" dir="auto" placeholder="buah">
                            <label for="buah">Buah</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </fieldset>
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
                "sEmptyTable": 'Tidak ada menu makanan. Klik "Tambah Menu" untuk menambahkan menu.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ menu",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 menu",
                "sInfoFiltered": "(di-filter dari _MAX_ menu)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ menu",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Menu makanan yang Anda cari tidak ditemukan",
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
                text: '<i class="fa-solid fa-plus"></i> Tambah Menu',
                className: 'btn-primary btn-sm bg-gradient rounded-end-3',
                attr: {
                    id: 'addMenuBtn'
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
                "url": "<?= base_url('/menu/menulist') ?>",
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
                                    <a class="btn btn-info text-nowrap bg-gradient rounded-start-3" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" href="<?= base_url('/menu/details') ?>/${row.id_menu}" role="button" data-bs-toggle="tooltip" data-bs-title="Detail"><i class="fa-solid fa-circle-info"></i></a>
                                    <button class="btn btn-secondary text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_menu}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_menu}" data-name="${row.nama_petugas}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
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
                    data: 'jadwal_makan'
                },
                {
                    data: 'nama_petugas'
                },
                {
                    data: 'jumlah',
                    render: function(data, type, row) {
                        return `<div class="date">
                                    ${data}
                                </div>`;
                    }
                },
            ],
            "order": [
                [0, 'desc']
            ],
            "columnDefs": [{
                "target": [0, 1],
                "orderable": false
            }, {
                "target": [0, 1, 2, 4, 6],
                "width": "0%"
            }, {
                "target": [3, 5],
                "width": "50%"
            }]
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Re-initialize tooltips on table redraw (server-side events like pagination, etc.)
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        async function loadPetugasOptions() {
            try {
                const response = await axios.get('<?= base_url('menu/petugasoptions') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#id_petugas');

                    // Clear existing options except the first one
                    select.find('option:not(:first)').remove();

                    // Loop through the options and append them to the select element
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                showFailedToast('Gagal mendapatkan petugas<br>' + error);
            }
        }
        // Call the function to load the options
        loadPetugasOptions();
        // Show add user modal
        $('#addMenuBtn').click(function() {
            $('#menuModalLabel').text('Tambah Menu Makanan');
            $('#menuForm')[0].reset();
            $('#menuId').val('');
            $('#jumlah').val('');
            $('#id_petugas_lama').val('');
            $('#menuModal').modal('show');
        });
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/menu/menu') ?>/${id}`);

                if (response.data) {
                    $('#menuModalLabel').text('Edit Petugas Gizi');
                    $('#menuId').val(response.data.id_menu);
                    $('#tanggal').val(response.data.tanggal);
                    $('#nama_menu').val(response.data.nama_menu);
                    $('#jadwal_makan').val(response.data.jadwal_makan);
                    $('#id_petugas').val(response.data.id_petugas);
                    $('#id_petugas_lama').val(response.data.id_petugas);
                    $('#protein_hewani').val(response.data.protein_hewani);
                    $('#protein_nabati').val(response.data.protein_nabati);
                    $('#sayur').val(response.data.sayur);
                    $('#buah').val(response.data.buah);
                    $('#jumlah').val(response.data.jumlah);
                    $('#menuModal').modal('show');
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Store the ID of the user to be deleted
        var menuId;
        var menuName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            menuId = $(this).data('id');
            menuName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + menuName + `"?`);
            $('#deleteSubmessage').html(`Mengapus menu juga akan menghapus permintaan yang menggunakan menu ini`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/menu/delete') ?>/${menuId}`);
                showSuccessToast('Data berhasil dihapus.');
                table.ajax.reload();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#menuForm').submit(async function(e) {
            e.preventDefault();
            const url = $('#menuId').val() ? '<?= base_url('/menu/update') ?>' : '<?= base_url('/menu/create') ?>';
            const formData = new FormData(this);

            // Clear previous validation states
            $('#menuForm .is-invalid').removeClass('is-invalid');
            $('#menuForm .invalid-feedback').text('').hide();

            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses, silakan tunggu...</span>
            `);

            // Disable form inputs
            $('#menuForm input, #menuForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data' // Required for FormData
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#menuModal').modal('hide');
                    table.ajax.reload();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#menuForm .is-invalid').removeClass('is-invalid');
                    $('#menuForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
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
                                console.warn("Elemen tidak ditemukan pada field:", field);
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
                $('#menuForm input, #menuForm select, #closeBtn').prop('disabled', false);
            }
        });

        $('#menuModal').on('hidden.bs.modal', function() {
            $('#menuForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
        });
        // Show toast notification
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