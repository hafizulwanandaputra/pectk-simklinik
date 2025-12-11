<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> loket</div>
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
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama loket">
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
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">No.</th>
                            <th scope="col" class="text-nowrap" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Nama Loket</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px;">Status</th>
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
    <div class="modal fade" id="loketModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="loketForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="loketModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_loket" name="id_loket">
                    <input type="hidden" id="nama_loket_old" name="nama_loket_old">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_loket" id="nama_loket" name="nama_loket">
                        <label for="nama_loket">Nama Loket<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1 radio-group">
                        <label for="status">
                            Status<span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center justify-content-evenly">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status1" value="1">
                                <label class="form-check-label" for="status1">
                                    Aktif
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status2" value="0">
                                <label class="form-check-label" for="status2">
                                    Tidak Aktif
                                </label>
                            </div>
                        </div>
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
<?= $this->section('toast'); ?>

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
                "sEmptyTable": 'Tidak ada loket. Klik "Tambah Loket" untuk menambahkan loket.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ loket",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 loket",
                "sInfoFiltered": "(di-filter dari _MAX_ loket)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ loket",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Loket yang Anda cari tidak ditemukan",
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
                text: '<i class="fa-solid fa-plus"></i> Tambah Loket',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addLoketBtn'
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
                "url": "<?= base_url('/loket/loketlist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_loket}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_loket}" data-name="${row.nama_loket}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'nama_loket'
                },
                {
                    data: 'fullname',
                    render: function(data, type, row) {
                        let statusBadge = row.status == 1 ?
                            '<span class="badge bg-success bg-gradient">Aktif</span>' :
                            '<span class="badge bg-danger bg-gradient">Tidak Aktif</span>';

                        return `${statusBadge}`;
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
                "target": [0, 1, 3],
                "width": "0%"
            }, {
                "target": [2],
                "width": "50%"
            }],
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

        // Ketika tombol "Tambah Ruangan" diklik
        $('#addLoketBtn').click(function() {
            $('#loketModalLabel').text('Tambah Loket'); // Mengubah label modal
            $('#loketForm')[0].reset(); // Mengatur ulang form
            $('#id_loket').val(''); // Mengosongkan ID poli
            $('#nama_loket_old').val('');
            $('#loketModal').modal('show'); // Menampilkan modal
        });

        // Fokus pada field fullname saat modal ditampilkan
        $('#loketModal').on('shown.bs.modal', function() {
            $('#fullname').trigger('focus'); // Memfokuskan field fullname
        });

        // Mengedit pengguna saat tombol edit diklik
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this); // Menyimpan referensi ke tombol yang diklik
            const id = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan spinner

            try {
                // Melakukan permintaan dengan Axios untuk mendapatkan data pengguna
                const response = await axios.get(`<?= base_url('/loket/loket') ?>/${id}`);

                // Memperbarui field modal dengan data pengguna yang diterima
                $('#loketModalLabel').text('Edit Loket');
                $('#id_loket').val(response.data.id_loket);
                $('#nama_loket_old').val(response.data.nama_loket);
                $('#nama_loket').val(response.data.nama_loket);
                const status = response.data.status;
                if (status) {
                    $("input[name='status'][value='" + status + "']").prop('checked', true);
                }

                // Menampilkan modal
                $('#loketModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                // Mengatur ulang status tombol
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`); // Mengembalikan tampilan tombol
            }
        });

        // Menyimpan ID pengguna yang akan dihapus
        var id_loket;
        var nama_loket;

        // Menampilkan modal konfirmasi penghapusan
        $(document).on('click', '.delete-btn', function() {
            id_loket = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            nama_loket = $(this).data('name'); // Mengambil nama pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#deleteMessage').html(`Hapus ` + nama_loket + `?`); // Menampilkan pesan konfirmasi penghapusan
            $('#deleteModal').modal('show'); // Menampilkan modal konfirmasi
        });

        // Konfirmasi penghapusan pengguna
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/loket/delete') ?>/${id_loket}`); // Menghapus pengguna
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                // Memeriksa jika error memiliki response dan mengambil pesan kesalahan
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error; // Pesan kesalahan umum
                showFailedToast(errorMessage); // Menampilkan pesan kesalahan
            } finally {
                $('#deleteModal').modal('hide'); // Menyembunyikan modal penghapusan
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        // Mengirimkan form pengguna (Tambah/Edit)
        $('#loketForm').submit(async function(e) {
            e.preventDefault(); // Mencegah pengiriman form default

            const url = $('#id_loket').val() ? '<?= base_url('/loket/update') ?>' : '<?= base_url('/loket/create') ?>'; // Menentukan URL berdasarkan apakah pengguna sedang diupdate atau ditambahkan
            const formData = new FormData(this); // Mengambil data form

            console.log("Form URL:", url); // Menampilkan URL di konsol
            console.log("Form Data:", $(this).serialize()); // Menampilkan data form di konsol

            // Menghapus status validasi sebelumnya
            $('#loketForm .is-invalid').removeClass('is-invalid');
            $('#loketForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses...</span>
            `); // Mengubah tampilan tombol submit menjadi loading

            // Menonaktifkan input form
            $('#loketForm input, #loketForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data' // Menentukan jenis konten untuk permintaan
                    }
                });

                if (response.data.success) {
                    $('#loketModal').modal('hide'); // Menyembunyikan modal setelah sukses
                    table.ajax.reload(null, false); // Memperbarui tabel
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#loketForm .is-invalid').removeClass('is-invalid');
                    $('#loketForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            if (['status'].includes(field)) {
                                const radioGroup = $(`input[name='${field}']`); // Ambil grup radio berdasarkan nama
                                const feedbackElement = radioGroup.closest('.radio-group').find('.invalid-feedback'); // Gunakan pembungkus dengan class tertentu

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        radioGroup.removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Radio group tidak ditemukan untuk field:", field);
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
                                    console.warn("Elemen tidak ditemukan pada field:", field);
                                }
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.'); // Menampilkan pesan kesalahan di konsol
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `); // Mengembalikan tampilan tombol submit

                $('#loketForm input, #loketForm select, #closeBtn').prop('disabled', false); // Mengembalikan status input form
            }
        });

        // Mengatur ulang form dan status validasi saat modal ditutup
        $('#loketModal').on('hidden.bs.modal', function() {
            $('#loketForm')[0].reset(); // Mengatur ulang form
            $('.is-invalid').removeClass('is-invalid'); // Menghapus tanda tidak valid
            $('.invalid-feedback').text('').hide(); // Menyembunyikan semua pesan kesalahan
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>