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
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tarif</th>
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
    <div class="modal fade" id="layananModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="layananModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="layananForm" enctype="multipart/form-data" class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="layananModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_layanan" name="id_layanan">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_layanan" id="nama_layanan" name="nama_layanan">
                        <label for="nama_layanan">Nama*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="jenis_layanan" name="jenis_layanan" aria-label="jenis_layanan">
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="Rawat Jalan">Rawat Jalan</option>
                            <option value="Pemeriksaan Penunjang">Pemeriksaan Penunjang</option>
                            <option value="Operasi">Operasi</option>
                        </select>
                        <label for="jenis_layanan">Jenis*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="tarif" id="tarif" name="tarif">
                        <label for="tarif">Tarif (Rp)*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="keterangan" id="keterangan" name="keterangan">
                        <label for="keterangan">Keterangan</label>
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
    $(document).ready(function() {
        var table = $('#tabel').DataTable({
            "oLanguage": {
                "sDecimal": ",",
                "sEmptyTable": 'Tidak ada tindakan. Klik "Tambah Tindakan" untuk menambahkan tindakan.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ tindakan",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 tindakan",
                "sInfoFiltered": "(di-filter dari _MAX_ tindakan)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ tindakan",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Tindakan yang Anda cari tidak ditemukan",
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
                $("#tabel").wrap("<div class='overflow-auto position-relative datatables-height'></div>");
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
                className: 'btn-success btn-sm bg-gradient rounded-start-3',
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }, {
                text: '<i class="fa-solid fa-plus"></i> Tambah Tindakan',
                className: 'btn-primary btn-sm bg-gradient rounded-end-3',
                attr: {
                    id: 'addLayananBtn'
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
                "url": "<?= base_url('/layanan/layananlist') ?>",
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
                    data: 'no',
                    render: function(data, type, row) {
                        return `<span class="date">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button class="btn btn-secondary text-nowrap bg-gradient rounded-start-3 edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_layanan}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_layanan}" data-name="${row.nama_layanan}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'nama_layanan'
                },
                {
                    data: 'jenis_layanan',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'tarif',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedTarif = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap">Rp${formattedTarif}</span>`;
                    }
                },
                {
                    data: 'keterangan'
                },
            ],
            "order": [
                [3, 'asc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1, 3, 4, 5],
                "width": "0%"
            }, {
                "target": [2],
                "width": "100%"
            }]
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Re-initialize tooltips on table redraw (server-side events like pagination, etc.)
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        // Show add user modal
        $('#addLayananBtn').click(function() {
            $('#layananModalLabel').text('Tambah Tindakan');
            $('#layananModal').modal('show');
        });

        $('#layananModal').on('shown.bs.modal', function() {
            $('#nama_layanan').trigger('focus');
        });

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/layanan/layanan') ?>/${id}`);
                $('#layananModalLabel').text('Edit Tindakan');
                $('#id_layanan').val(response.data.id_layanan);
                $('#nama_layanan').val(response.data.nama_layanan);
                $('#jenis_layanan').val(response.data.jenis_layanan);
                $('#tarif').val(response.data.tarif);
                $('#keterangan').val(response.data.keterangan);
                $('#layananModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Store the ID of the user to be deleted
        var layananId;
        var layananName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            layananId = $(this).data('id');
            layananName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + layananName + `"?`);
            $('#deleteSubmessage').html(`Layanan tidak dapat dihapus jika ada transaksi yang menggunakan layanan ini!`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/layanan/delete') ?>/${layananId}`);
                showSuccessToast('Layanan berhasil dihapus.');
                table.ajax.reload();
            } catch (error) {
                // Check if the error has a response and extract the message
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error;
                if (error.response && error.response.data && error.response.data.error) {
                    console.error(error.response.data.error);
                    errorMessage = 'Tidak dapat menghapus data ini karena sedang digunakan.'
                }
                showFailedToast(errorMessage);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#layananForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_layanan').val() ? '<?= base_url('/layanan/update') ?>' : '<?= base_url('/layanan/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#layananForm .is-invalid').removeClass('is-invalid');
            $('#layananForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses, silakan tunggu...</span>
            `);

            // Disable form inputs
            $('#layananForm input, #layananForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#layananModal').modal('hide');
                    table.ajax.reload();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#layananForm .is-invalid').removeClass('is-invalid');
                    $('#layananForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
                            const feedbackElement = fieldElement.siblings('.invalid-feedback');

                            console.log("Target Field:", fieldElement);
                            console.log("Target Feedback:", feedbackElement);

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
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#layananForm input, #layananForm select, #closeBtn').prop('disabled', false);
            }
        });

        $('#layananModal').on('hidden.bs.modal', function() {
            $('#layananForm')[0].reset();
            $('#id_layanan').val('');
            $('#nama_layanan').val('');
            $('#jenis_layanan').val('');
            $('#tarif').val('');
            $('#keterangan').val('');
            $('#layananForm .is-invalid').removeClass('is-invalid');
            $('#layananForm .invalid-feedback').text('').hide();
        });
        // Show toast notification
        function showSuccessToast(message) {
            var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-success border border-success rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
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
            var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-danger border border-danger rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
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