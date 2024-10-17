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
        <div class="alert alert-warning bg-gradient rounded-3" role="alert">
            <div class="d-flex align-items-start">
                <div style="width: 12px; text-align: center;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="w-100 ms-3">
                    <strong>TIDAK DIGUNAKAN LAGI!</strong>
                    <br>
                    Data pasien di sini sudah tidak digunakan karena data pasien saat ini diambil dari Sistem Informasi Manajemen Klinik ini melalui API.
                </div>
            </div>
        </div>
        <table id="tabel" class="table table-sm table-hover" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis Kelamin</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Rekam Medis</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Registrasi</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">NIK</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tempat dan Tanggal Lahir</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Agama</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nomor Ponsel</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Alamat</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Status Kawin</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Tanggal Pendaftaran</th>
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
    <div class="modal fade" id="pasienModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="pasienModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="pasienForm" enctype="multipart/form-data" class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="pasienModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_pasien" name="id_pasien">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_pasien" id="nama_pasien" name="nama_pasien">
                        <label for="nama_pasien">Nama*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mt-1 mb-1 row">
                        <label for="jenis_kelamin" class="col-lg-3 col-form-label">Jenis Kelamin*</label>
                        <div class="col-lg col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly justify-content-lg-start">
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
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="nik" id="nik" name="nik">
                        <label for="nik">NIK</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1" id="jenis_pasien_container">
                        <select class="form-select rounded-3" id="jenis_pasien" name="jenis_pasien" aria-label="jenis_pasien">
                            <option value="" disabled selected>-- Pilih Jenis Pasien --</option>
                            <option value="UMUM">Umum</option>
                            <option value="BPJS">BPJS</option>
                        </select>
                        <label for="jenis_pasien">Jenis Pasien*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="tempat_lahir" id="tempat_lahir" name="tempat_lahir">
                        <label for="tempat_lahir">Tempat Lahir*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="date" class="form-control" autocomplete="off" dir="auto" placeholder="tanggal_lahir" id="tanggal_lahir" name="tanggal_lahir">
                        <label for="tanggal_lahir">Tanggal Lahir*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="agama_pasien" name="agama_pasien" aria-label="agama_pasien">
                            <option value="" disabled selected>-- Pilih Agama --</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen Protestan">Kristen Protestan</option>
                            <option value="Kristen Katolik">Kristen Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                        <label for="agama_pasien">Agama*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="no_hp_pasien" id="no_hp_pasien" name="no_hp_pasien">
                        <label for="no_hp_pasien">Nomor Ponsel</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="alamat_pasien" id="alamat_pasien" name="alamat_pasien">
                        <label for="alamat_pasien">Alamat*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="provinsi" id="provinsi" name="provinsi">
                        <label for="provinsi">Provinsi*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="kota" id="kota" name="kota">
                        <label for="kota">Kabupaten/Kota*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="kecamatan" id="kecamatan" name="kecamatan">
                        <label for="kecamatan">Kecamatan*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="desa" id="desa" name="desa">
                        <label for="desa">Desa/Kelurahan*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="status_kawin" name="status_kawin" aria-label="status_kawin">
                            <option value="" disabled selected>-- Pilih Status Kawin --</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        <label for="status_kawin">Agama*</label>
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
                "sEmptyTable": 'Tidak ada pasien. Klik "Tambah Pasien" untuk menambahkan pasien.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ pasien",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 pasien",
                "sInfoFiltered": "(di-filter dari _MAX_ pasien)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ pasien",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Pasien yang Anda cari tidak ditemukan",
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
                text: '<i class="fa-solid fa-plus"></i> Tambah Pasien',
                className: 'btn-primary btn-sm bg-gradient rounded-end-3',
                attr: {
                    id: 'addPasienBtn'
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
                "url": "<?= base_url('/pasien/pasienlist') ?>",
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
                        return `<span class="date" style="display: block; text-align: center;">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button class="btn btn-secondary text-nowrap bg-gradient rounded-start-3 edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_pasien}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_pasien}" data-name="${row.nama_pasien}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'nama_pasien',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'jenis_kelamin',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'no_mr',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'no_registrasi',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'nik',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'jenis_pasien',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'tanggal_lahir',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${row.tempat_lahir}<br><small>${data}</small></span>`;
                    }
                },
                {
                    data: 'agama_pasien',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'no_hp_pasien',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'alamat_pasien',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}<br><small>${row.desa}, ${row.kecamatan}, ${row.kota}, ${row.provinsi}</small></span>`;
                    }
                },
                {
                    data: 'status_kawin',
                    render: function(data, type, row) {
                        return `<span class="text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'tgl_pendaftaran',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
            ],
            "order": [
                [0, 'desc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13],
                "width": "0%"
            }, {
                "target": [2, 11],
                "width": "50%"
            }]
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Re-initialize tooltips on table redraw (server-side events like pagination, etc.)
        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        // Show add user modal
        $('#addPasienBtn').click(function() {
            $('#pasienModalLabel').text('Tambah Pasien');
            $('#pasienModal').modal('show');
        });

        $('#pasienModal').on('shown.bs.modal', function() {
            $('#nama_pasien').trigger('focus');
        });

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/pasien/pasien') ?>/${id}`);
                $('#pasienModalLabel').text('Edit Pasien');
                $('#id_pasien').val(response.data.id_pasien);
                $('#nama_pasien').val(response.data.nama_pasien);
                const selectedGender = response.data.jenis_kelamin;
                if (selectedGender) {
                    $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
                }
                $('#jenis_pasien_container').val('').hide();
                $('#nik').val(response.data.nik);
                $('#tempat_lahir').val(response.data.tempat_lahir);
                $('#tanggal_lahir').val(response.data.tanggal_lahir);
                $('#agama_pasien').val(response.data.agama_pasien);
                $('#no_hp_pasien').val(response.data.no_hp_pasien);
                $('#alamat_pasien').val(response.data.alamat_pasien);
                $('#provinsi').val(response.data.provinsi);
                $('#kota').val(response.data.kota);
                $('#kecamatan').val(response.data.kecamatan);
                $('#desa').val(response.data.desa);
                $('#status_kawin').val(response.data.status_kawin);
                $('#id_dokter').val(response.data.id_dokter);
                $('#pasienModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Store the ID of the user to be deleted
        var pasienId;
        var pasienName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            pasienId = $(this).data('id');
            pasienName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + pasienName + `"?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Mengapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/pasien/delete') ?>/${pasienId}`);
                showSuccessToast('Pasien berhasil dihapus.');
                table.ajax.reload(null, false);
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
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#pasienForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_pasien').val() ? '<?= base_url('/pasien/update') ?>' : '<?= base_url('/pasien/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#pasienForm .is-invalid').removeClass('is-invalid');
            $('#pasienForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses...</span>
            `);

            // Disable form inputs
            $('#pasienForm input, #pasienForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#pasienModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#pasienForm .is-invalid').removeClass('is-invalid');
                    $('#pasienForm .invalid-feedback').text('').hide();

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
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#pasienForm input, #pasienForm select, #closeBtn').prop('disabled', false);
            }
        });

        $('#pasienModal').on('hidden.bs.modal', function() {
            $('#pasienForm')[0].reset();
            $('#id_pasien').val('');
            $('#nama_pasien').val('');
            $('#jenis_pasien_container').val('').show();
            $('#nik').val('');
            $('#tempat_lahir').val('');
            $('#tanggal_lahir').val('');
            $('#agama_pasien').val('');
            $('#no_hp_pasien').val('');
            $('#alamat_pasien').val('');
            $('#provinsi').val('');
            $('#kota').val('');
            $('#kecamatan').val('');
            $('#desa').val('');
            $('#status_kawin').val('');
            $('#id_dokter').val('');
            $('#pasienForm .is-invalid').removeClass('is-invalid');
            $('#pasienForm .invalid-feedback').text('').hide();
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