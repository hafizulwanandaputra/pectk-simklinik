<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_datatables">0</span> ruangan</div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-row gap-2">
                        <select class="form-select form-select-sm w-auto" id="length-menu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama lengkap dan nama pengguna">
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
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No.</th>
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama Ruangan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Status</th>
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
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
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
    <div class="modal fade" id="poliModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="poliModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="poliForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="poliModalLabel" style="font-weight: bold;">Tambah Pengguna</h6>
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_poli" name="id_poli">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_poli" id="nama_poli" name="nama_poli">
                        <label for="nama_poli">Nama Ruangan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1 radio-group">
                        <label for="status">
                            Status
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
                "sEmptyTable": 'Tidak ada ruangan. Klik "Tambah Pengguna" untuk menambahkan ruangan.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ ruangan",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 ruangan",
                "sInfoFiltered": "(di-filter dari _MAX_ ruangan)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ ruangan",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Ruangan yang Anda cari tidak ditemukan",
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
                // Tombol Tambah Pengguna
                text: '<i class="fa-solid fa-plus"></i> Tambah Ruangan',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addPoliBtn'
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
                "url": "<?= base_url('/poliklinik/polikliniklist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_poli}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_poli}" data-name="${row.nama_poli}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'nama_poli'
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

        const toggleFilter = $('#toggleFilter');
        const filterFields = $('#filterFields');
        const toggleStateKey = 'filterFieldsToggleState';

        // Fungsi untuk menyimpan status toggle di local storage
        function saveToggleState(state) {
            localStorage.setItem(toggleStateKey, state ? 'visible' : 'hidden');
        }

        // Fungsi untuk memuat status toggle dari local storage
        function loadToggleState() {
            return localStorage.getItem(toggleStateKey);
        }

        // Atur status awal berdasarkan local storage
        const initialState = loadToggleState();
        if (initialState === 'visible') {
            filterFields.show();
        } else {
            filterFields.hide(); // Sembunyikan jika 'hidden' atau belum ada data
        }

        // Event klik untuk toggle
        toggleFilter.on('click', function(e) {
            e.preventDefault();
            const isVisible = filterFields.is(':visible');
            filterFields.toggle(!isVisible);
            saveToggleState(!isVisible);
        });

        // Ketika tombol "Tambah Ruangan" diklik
        $('#addPoliBtn').click(function() {
            $('#poliModalLabel').text('Tambah Ruangan'); // Mengubah label modal
            $('#poliForm')[0].reset(); // Mengatur ulang form
            $('#id_poli').val(''); // Mengosongkan ID poli
            $('#poliModal').modal('show'); // Menampilkan modal
        });

        // Fokus pada field fullname saat modal ditampilkan
        $('#poliModal').on('shown.bs.modal', function() {
            $('#fullname').trigger('focus'); // Memfokuskan field fullname
        });

        // Mengedit pengguna saat tombol edit diklik
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this); // Menyimpan referensi ke tombol yang diklik
            const id = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span>`); // Menampilkan spinner

            try {
                // Melakukan permintaan dengan Axios untuk mendapatkan data pengguna
                const response = await axios.get(`<?= base_url('/poliklinik/poliklinik') ?>/${id}`);

                // Memperbarui field modal dengan data pengguna yang diterima
                $('#poliModalLabel').text('Edit Ruangan');
                $('#id_poli').val(response.data.id_poli);
                $('#nama_poli').val(response.data.nama_poli);
                const status = response.data.status;
                if (status) {
                    $("input[name='status'][value='" + status + "']").prop('checked', true);
                }

                // Menampilkan modal
                $('#poliModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                // Mengatur ulang status tombol
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`); // Mengembalikan tampilan tombol
            }
        });

        // Menyimpan ID pengguna yang akan dihapus
        var id_poli;
        var nama_poli;

        // Menampilkan modal konfirmasi penghapusan
        $(document).on('click', '.delete-btn', function() {
            id_poli = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            nama_poli = $(this).data('name'); // Mengambil nama pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#deleteMessage').html(`Hapus ` + nama_poli + `?`); // Menampilkan pesan konfirmasi penghapusan
            $('#deleteModal').modal('show'); // Menampilkan modal konfirmasi
        });

        // Konfirmasi penghapusan pengguna
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#deleteMessage').html('Mengapus, silakan tunggu...'); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/poliklinik/delete') ?>/${id_poli}`); // Menghapus pengguna
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                // Memeriksa jika error memiliki response dan mengambil pesan kesalahan
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error; // Pesan kesalahan umum
                showFailedToast(errorMessage); // Menampilkan pesan kesalahan
            } finally {
                $('#deleteModal').modal('hide'); // Menyembunyikan modal penghapusan
                $('#deleteModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Mengirimkan form pengguna (Tambah/Edit)
        $('#poliForm').submit(async function(e) {
            e.preventDefault(); // Mencegah pengiriman form default

            const url = $('#id_poli').val() ? '<?= base_url('/poliklinik/update') ?>' : '<?= base_url('/poliklinik/create') ?>'; // Menentukan URL berdasarkan apakah pengguna sedang diupdate atau ditambahkan
            const formData = new FormData(this); // Mengambil data form

            console.log("Form URL:", url); // Menampilkan URL di konsol
            console.log("Form Data:", $(this).serialize()); // Menampilkan data form di konsol

            // Menghapus status validasi sebelumnya
            $('#poliForm .is-invalid').removeClass('is-invalid');
            $('#poliForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses...</span>
            `); // Mengubah tampilan tombol submit menjadi loading

            // Menonaktifkan input form
            $('#poliForm input, #poliForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data' // Menentukan jenis konten untuk permintaan
                    }
                });

                if (response.data.success) {
                    $('#poliModal').modal('hide'); // Menyembunyikan modal setelah sukses
                    table.ajax.reload(null, false); // Memperbarui tabel
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#poliForm .is-invalid').removeClass('is-invalid');
                    $('#poliForm .invalid-feedback').text('').hide();

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

                $('#poliForm input, #poliForm select, #closeBtn').prop('disabled', false); // Mengembalikan status input form
            }
        });

        // Mengatur ulang form dan status validasi saat modal ditutup
        $('#poliModal').on('hidden.bs.modal', function() {
            $('#poliForm')[0].reset(); // Mengatur ulang form
            $('.is-invalid').removeClass('is-invalid'); // Menghapus tanda tidak valid
            $('.invalid-feedback').text('').hide(); // Menyembunyikan semua pesan kesalahan
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>