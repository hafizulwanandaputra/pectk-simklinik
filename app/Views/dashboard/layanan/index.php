<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> layanan</div>
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
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama layanan">
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
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Layanan</th>
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
    <div class="modal fade" id="layananModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="layananModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="layananForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="layananModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_layanan" name="id_layanan">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_layanan" id="nama_layanan" name="nama_layanan">
                        <label for="nama_layanan">Nama<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select  " id="jenis_layanan" name="jenis_layanan" aria-label="jenis_layanan">
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="Rawat Jalan">Rawat Jalan</option>
                            <option value="Pemeriksaan Penunjang">Pemeriksaan Penunjang</option>
                            <option value="Operasi">Operasi</option>
                        </select>
                        <label for="jenis_layanan">Jenis<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="input-group has-validation mb-1 mt-1">
                        <span class="input-group-text">Rp</span>
                        <div class="form-floating">
                            <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="tarif" id="tarif" name="tarif">
                            <label for="tarif">Tarif<span class="text-danger">*</span></label>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="keterangan" id="keterangan" name="keterangan">
                        <label for="keterangan">Keterangan</label>
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
                "sEmptyTable": 'Tidak ada layanan. Klik "Tambah Layanan" untuk menambahkan layanan.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ layanan",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 layanan",
                "sInfoFiltered": "(di-filter dari _MAX_ layanan)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ layanan",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Layanan yang Anda cari tidak ditemukan",
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
                // Tombol Tambah Layanan
                text: '<i class="fa-solid fa-plus"></i> Tambah Layanan',
                className: 'btn-primary btn-sm bg-gradient ',
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
            "searching": false, // Disable the internal search bar
            'pageLength': 25,
            'lengthMenu': "",
            "autoWidth": true,
            "processing": false,
            "serverSide": true,
            "ajax": {
                // URL endpoint untuk melakukan permintaan AJAX
                "url": "<?= base_url('/layanan/layananlist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_layanan}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_layanan}" data-name="${row.nama_layanan}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
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

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedTarif}</span>`;
                    },
                },
                {
                    data: 'keterangan'
                },
            ],
            "order": [
                [3, 'desc']
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

        // Tampilkan modal tambah layanan
        $('#addLayananBtn').click(function() {
            $('#layananModalLabel').text('Tambah Layanan'); // Ubah judul modal menjadi 'Tambah Layanan'
            $('#layananModal').modal('show'); // Tampilkan modal layanan
        });

        // Fokuskan kursor ke field 'nama_layanan' saat modal ditampilkan
        $('#layananModal').on('shown.bs.modal', function() {
            $('#nama_layanan').trigger('focus');
        });

        // Event klik untuk tombol edit
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id'); // Dapatkan ID layanan
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Sembunyikan tooltip
            $this.prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
            `); // Ubah tombol menjadi indikator loading

            try {
                const response = await axios.get(`<?= base_url('/layanan/layanan') ?>/${id}`); // Ambil data layanan berdasarkan ID
                $('#layananModalLabel').text('Edit Layanan'); // Ubah judul modal menjadi 'Edit Layanan'
                $('#id_layanan').val(response.data.id_layanan);
                $('#nama_layanan').val(response.data.nama_layanan);
                $('#jenis_layanan').val(response.data.jenis_layanan);
                $('#tarif').val(response.data.tarif);
                $('#keterangan').val(response.data.keterangan);
                $('#layananModal').modal('show'); // Tampilkan modal dengan data layanan
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Tampilkan pesan kesalahan
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`); // Pulihkan tombol
            }
        });

        // Variabel untuk menyimpan ID dan nama layanan yang akan dihapus
        var layananId;
        var layananName;

        // Tampilkan modal konfirmasi hapus
        $(document).on('click', '.delete-btn', function() {
            layananId = $(this).data('id'); // Dapatkan ID layanan
            layananName = $(this).data('name'); // Dapatkan nama layanan
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Sembunyikan tooltip
            $('#deleteMessage').html(`Hapus "` + layananName + `"?`);
            $('#deleteModal').modal('show'); // Tampilkan modal konfirmasi
        });

        // Proses konfirmasi hapus layanan
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true); // Nonaktifkan tombol saat proses berlangsung
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/layanan/delete') ?>/${layananId}`); // Hapus layanan berdasarkan ID
                table.ajax.reload(null, false); // Reload tabel data
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error;
                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = 'Tidak dapat menghapus data ini karena sedang digunakan.'; // Pesan kesalahan khusus
                }
                showFailedToast(errorMessage); // Tampilkan pesan kesalahan
            } finally {
                $('#deleteModal').modal('hide'); // Sembunyikan modal konfirmasi
                $('#deleteModal button').prop('disabled', false); // Aktifkan kembali tombol
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        // Event submit form layanan (Tambah/Edit)
        $('#layananForm').submit(async function(e) {
            e.preventDefault(); // Cegah form dari submit default

            const url = $('#id_layanan').val() ? '<?= base_url('/layanan/update') ?>' : '<?= base_url('/layanan/create') ?>'; // Tentukan URL berdasarkan aksi
            const formData = new FormData(this); // Ambil data form
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            // Bersihkan status validasi sebelumnya
            $('#layananForm .is-invalid').removeClass('is-invalid');
            $('#layananForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses, silakan tunggu...</span>
            `); // Ubah tombol submit menjadi indikator loading

            $('#layananForm input, #layananForm select, #closeBtn').prop('disabled', true); // Nonaktifkan input dan tombol

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#layananModal').modal('hide'); // Tutup modal jika berhasil
                    table.ajax.reload(null, false); // Reload tabel data
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Tampilkan pesan validasi baru
                    for (const field in response.data.errors) {
                        const fieldElement = $('#' + field);
                        let feedbackElement = fieldElement.siblings('.invalid-feedback');

                        // Handle input-group cases
                        if (fieldElement.closest('.input-group').length) {
                            feedbackElement = fieldElement.closest('.input-group').find('.invalid-feedback');
                        }

                        if (fieldElement.length > 0 && feedbackElement.length > 0) {
                            fieldElement.addClass('is-invalid');
                            feedbackElement.text(response.data.errors[field]).show();

                            // Hapus pesan kesalahan saat input diubah
                            fieldElement.on('input change', function() {
                                $(this).removeClass('is-invalid');
                                $(this).siblings('.invalid-feedback').text('').hide();
                            });
                        }
                    }
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Tampilkan pesan kesalahan
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `); // Pulihkan tombol submit
                $('#layananForm input, #layananForm select, #closeBtn').prop('disabled', false); // Aktifkan kembali input dan tombol
            }
        });

        // Reset form saat modal ditutup
        $('#layananModal').on('hidden.bs.modal', function() {
            $('#layananForm')[0].reset(); // Reset form
            $('#id_layanan, #nama_layanan, #jenis_layanan, #tarif, #keterangan').val(''); // Kosongkan input
            $('#layananForm .is-invalid').removeClass('is-invalid'); // Bersihkan status validasi
            $('#layananForm .invalid-feedback').text('').hide(); // Sembunyikan pesan kesalahan
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>