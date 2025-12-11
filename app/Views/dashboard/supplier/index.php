<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> pemasok</div>
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
    <div id="filterFields" class="sticky-top px-2 pt-2" style="z-index: 99; display: none;">
        <ul class="list-group no-fluid-content-list-group shadow-sm border border-bottom-0">
            <li class="list-group-item px-2 border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-row gap-2">
                        <select class="form-select form-select-sm w-auto" id="length-menu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama pemasok atau merek">
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
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">No</th>
                            <th scope="col" class="text-nowrap" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Nama</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Merek</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Alamat</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Nomor Telepon</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Jumlah Obat</th>
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
                    <h5 id="deleteMessage"></h5>
                    <h6 class="mb-0 fw-normal" id="deleteSubmessage"></h6>
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
    <div class="modal fade" id="supplierModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="supplierForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="supplierModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div id="mediaAlert" class="alert alert-warning  mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                Minimal isi salah satu kolom nama, merek, atau alamat (tidak boleh kosong atau "-").
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id_supplier" name="id_supplier">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_supplier" id="nama_supplier" name="nama_supplier">
                        <label for="nama_supplier">Nama</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="merek" id="merek" name="merek">
                        <label for="merek">Merek</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="alamat_supplier" id="alamat_supplier" name="alamat_supplier">
                        <label for="alamat_pasien">Alamat</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="kontak_supplier" id="kontak_supplier" name="kontak_supplier">
                        <label for="kontak_supplier">Nomor Telepon</label>
                        <div class="invalid-feedback"></div>
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
                "sEmptyTable": 'Tidak ada supplier. Klik "Tambah Pemasok" untuk menambahkan supplier.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ supplier",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 supplier",
                "sInfoFiltered": "(di-filter dari _MAX_ supplier)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ supplier",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Supplier yang Anda cari tidak ditemukan",
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
                // Tombol Tambah Pemasok
                text: '<i class="fa-solid fa-plus"></i> Tambah Pemasok',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addSupplierBtn'
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
                "url": "<?= base_url('/supplier/supplierlist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_supplier}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_supplier}" data-name="${row.nama_supplier}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'nama_supplier',
                    render: function(data, type, row) {
                        const nama_supplier = data ? data : '<em>Tanpa nama</em>';
                        return `${nama_supplier}`;
                    }
                },
                {
                    data: 'merek',
                    render: function(data, type, row) {
                        const merek = data ? data : '<em>Tanpa merek</em>';
                        return `${merek}`;
                    }
                },
                {
                    data: 'alamat_supplier',
                    render: function(data, type, row) {
                        const alamat_supplier = data ? data : '<em>Tidak ada alamat</em>';
                        return `${alamat_supplier}`;
                    }
                },
                {
                    data: 'kontak_supplier',
                    render: function(data, type, row) {
                        const kontak_supplier = data ? data : '<em>Tidak ada</em>';
                        return `<span class="date text-nowrap">${kontak_supplier}</span>`;
                    }
                },
                {
                    data: 'jumlah_obat',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<div class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</div>`;
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
                "target": [0, 1, 5, 6],
                "width": "0%"
            }, {
                "target": [2, 3, 4],
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

        // Tampilkan modal tambah supplier
        $('#addSupplierBtn').click(function() {
            $('#supplierModalLabel').text('Tambah Pemasok');
            $('#supplierModal').modal('show');
        });

        // Fokuskan input nama supplier saat modal ditampilkan
        $('#supplierModal').on('shown.bs.modal', function() {
            $('#nama_supplier').trigger('focus');
        });

        // Fungsi untuk menangani tombol edit supplier
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id'); // Ambil ID supplier
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Sembunyikan tooltip
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);

            try {
                const response = await axios.get(`<?= base_url('/supplier/supplier') ?>/${id}`);
                $('#supplierModalLabel').text('Edit Pemasok');
                $('#id_supplier').val(response.data.id_supplier);
                $('#nama_supplier').val(response.data.nama_supplier);
                $('#merek').val(response.data.merek);
                $('#alamat_supplier').val(response.data.alamat_supplier);
                $('#kontak_supplier').val(response.data.kontak_supplier);
                $('#supplierModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Variabel untuk menyimpan ID dan nama supplier yang akan dihapus
        var supplierId;
        var supplierName;

        // Tampilkan modal konfirmasi hapus supplier
        $(document).on('click', '.delete-btn', function() {
            supplierId = $(this).data('id');
            supplierName = $(this).data('name');
            if (supplierName == 'null' || supplierName == '' || supplierName == null) {
                supplierName = 'Hapus pemasok tanpa nama ini?';
            } else {
                supplierName = `Hapus "${supplierName}"?`;
            }
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(supplierName);
            $('#deleteSubmessage').html(`Pemasok tidak dapat dihapus jika ada obat yang berasal dari pemasok ini!`);
            $('#deleteModal').modal('show');
        });

        // Fungsi untuk mengonfirmasi penghapusan supplier
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/supplier/delete') ?>/${supplierId}`);
                table.ajax.reload(null, false); // Refresh data setelah penghapusan
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error;
                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = 'Tidak dapat menghapus data ini karena sedang digunakan.';
                }
                showFailedToast(errorMessage);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        // Fungsi untuk menangani submit form supplier
        $('#supplierForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_supplier').val() ? '<?= base_url('/supplier/update') ?>' : '<?= base_url('/supplier/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            // Hapus status validasi sebelumnya
            $('#supplierForm .is-invalid').removeClass('is-invalid');
            $('#supplierForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses, silakan tunggu...</span>
            `);

            // Nonaktifkan input form sementara
            $('#supplierForm input, #supplierForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#supplierModal').modal('hide');
                    table.ajax.reload(null, false); // Refresh data setelah operasi berhasil
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Tampilkan pesan kesalahan validasi
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
                            const feedbackElement = fieldElement.siblings('.invalid-feedback');

                            if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                fieldElement.addClass('is-invalid');
                                feedbackElement.text(response.data.errors[field]).show();

                                // Hapus pesan kesalahan saat input berubah
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
                $('#supplierForm input, #supplierForm select, #closeBtn').prop('disabled', false);
            }
        });

        // Reset form dan status validasi saat modal ditutup
        $('#supplierModal').on('hidden.bs.modal', function() {
            $('#supplierForm')[0].reset();
            $('#id_supplier').val('');
            $('#nama_supplier').val('');
            $('#merek').val('');
            $('#alamat_supplier').val('');
            $('#kontak_supplier').val('');
            $('#supplierForm .is-invalid').removeClass('is-invalid');
            $('#supplierForm .invalid-feedback').text('').hide();
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>