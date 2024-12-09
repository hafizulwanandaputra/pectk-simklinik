<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
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
                    <div class="input-group input-group-sm">
                        <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama lengkap dan nama pengguna">
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
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama Lengkap</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama Pengguna</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jenis Pengguna</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Aktif Sejak</th>
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
    <div class="modal modal-sheet p-4 py-md-5 fade" id="resetPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 id="resetPasswordMessage"></h5>
                    <h6 class="mb-0" id="resetPasswordSubmessage"></h6>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmResetPasswordBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="activateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="activateMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmActivateBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deactivateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deactivateMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeactivateBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="userForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="userModalLabel" style="font-weight: bold;">Tambah Pengguna</h6>
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="userId" name="id_user">
                    <input type="hidden" id="original_username" name="original_username">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="fullname" id="fullname" name="fullname">
                        <label for="fullname">Nama Lengkap*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="username" id="username" name="username">
                        <label for="username">Nama Pengguna*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select " id="role" name="role" aria-label="role">
                            <option value="" disabled selected>-- Pilih Jenis Pengguna --</option>
                            <option value="Admin">Admin</option>
                            <option value="Apoteker">Apoteker</option>
                            <option value="Dokter">Dokter</option>
                            <option value="Kasir">Kasir</option>
                        </select>
                        <label for="role">Jenis Pengguna</label>
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
                "sEmptyTable": 'Tidak ada pengguna. Klik "Tambah Pengguna" untuk menambahkan pengguna.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ pengguna",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 pengguna",
                "sInfoFiltered": "(di-filter dari _MAX_ pengguna)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ pengguna",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Pengguna yang Anda cari tidak ditemukan",
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
                "<'d-lg-flex justify-content-lg-between align-items-lg-top'<'text-md-center text-lg-start mt-2'l><'mt-2 mb-2'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'d-lg-flex justify-content-lg-between align-items-lg-center'<'text-md-center text-lg-start'><'d-md-flex justify-content-md-center d-lg-block'p>>",
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
                $(".page-item .page-link").addClass("bg-gradient");
            },
            'buttons': [{
                // Tombol Tambah Pengguna
                text: '<i class="fa-solid fa-plus"></i> Tambah Pengguna',
                className: 'btn-primary btn-sm bg-gradient ',
                attr: {
                    id: 'addUserBtn'
                },
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }],
            "search": {
                "caseInsensitive": true
            },
            "searching": false, // Disable the internal search bar
            'pageLength': 12,
            'lengthMenu': [
                [12, 24, 36, 48, 60],
                [12, 24, 36, 48, 60]
            ],
            "autoWidth": true,
            "processing": false,
            "serverSide": true,
            "ajax": {
                // URL endpoint untuk melakukan permintaan AJAX
                "url": "<?= base_url('/admin/adminlist') ?>",
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
                        let statusBtn = row.active == 1 ?
                            `<button class="btn btn-outline-danger text-nowrap bg-gradient  deactivate-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_user}" data-username="${row.username}"data-bs-toggle="tooltip" data-bs-title="Nonaktifkan"><i class="fa-solid fa-user-slash"></i></button>` :
                            `<button class="btn btn-outline-success text-nowrap bg-gradient  activate-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_user}" data-username="${row.username}"data-bs-toggle="tooltip" data-bs-title="Aktifkan"><i class="fa-solid fa-user-check"></i></i></button>`;

                        return `<div class="btn-group" role="group">
                                    ${statusBtn}
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  resetpwd-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_user}" data-username="${row.username}"data-bs-toggle="tooltip" data-bs-title="Atur ulang kata sandi"><i class="fa-solid fa-key"></i></button>
                                    <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_user}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${row.id_user}" data-username="${row.username}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'fullname',
                    render: function(data, type, row) {
                        let statusBadge = row.active == 1 ?
                            '<span class="badge bg-success bg-gradient">Aktif</span>' :
                            '<span class="badge bg-danger bg-gradient">Tidak Aktif</span>';

                        return `${data} ${statusBadge}`;
                    }
                },
                {
                    data: 'username'
                },
                {
                    data: 'role'
                },
                {
                    data: 'registered',
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
                "target": [0, 1],
                "width": "0%"
            }, {
                "target": [2, 3, 4],
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

        // Ketika tombol "Tambah Pengguna" diklik
        $('#addUserBtn').click(function() {
            $('#userModalLabel').text('Tambah Pengguna'); // Mengubah label modal
            $('#userForm')[0].reset(); // Mengatur ulang form
            $('#userId').val(''); // Mengosongkan ID pengguna
            $('#original_username').val(''); // Mengosongkan username asli
            $('#userModal').modal('show'); // Menampilkan modal
        });

        // Fokus pada field fullname saat modal ditampilkan
        $('#userModal').on('shown.bs.modal', function() {
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
                const response = await axios.get(`<?= base_url('/admin/admin') ?>/${id}`);

                // Memperbarui field modal dengan data pengguna yang diterima
                $('#userModalLabel').text('Edit Pengguna');
                $('#userId').val(response.data.id_user);
                $('#fullname').val(response.data.fullname);
                $('#username').val(response.data.username);
                $('#role').val(response.data.role);
                // Mengatur field hidden original_username
                $('#original_username').val(response.data.username);

                // Menampilkan modal
                $('#userModal').modal('show');
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                // Mengatur ulang status tombol
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`); // Mengembalikan tampilan tombol
            }
        });

        // Menyimpan ID pengguna yang akan dihapus
        var userId;
        var userName;

        // Menampilkan modal konfirmasi penghapusan
        $(document).on('click', '.delete-btn', function() {
            userId = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            userName = $(this).data('username'); // Mengambil nama pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#deleteMessage').html(`Hapus @` + userName + `?`); // Menampilkan pesan konfirmasi penghapusan
            $('#deleteModal').modal('show'); // Menampilkan modal konfirmasi
        });

        // Menampilkan modal untuk mengaktifkan pengguna
        $(document).on('click', '.activate-btn', function() {
            userId = $(this).data('id'); // Mengambil ID pengguna
            userName = $(this).data('username'); // Mengambil nama pengguna
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#activateMessage').html(`Aktifkan @` + userName + `?`); // Menampilkan pesan konfirmasi aktivasi
            $('#activateModal').modal('show'); // Menampilkan modal aktivasi
        });

        // Menampilkan modal untuk menonaktifkan pengguna
        $(document).on('click', '.deactivate-btn', function() {
            userId = $(this).data('id'); // Mengambil ID pengguna
            userName = $(this).data('username'); // Mengambil nama pengguna
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#deactivateMessage').html(`Nonaktifkan @` + userName + `?`); // Menampilkan pesan konfirmasi nonaktif
            $('#deactivateModal').modal('show'); // Menampilkan modal nonaktif
        });

        // Menampilkan modal untuk mengatur ulang kata sandi pengguna
        $(document).on('click', '.resetpwd-btn', function() {
            userId = $(this).data('id'); // Mengambil ID pengguna
            userName = $(this).data('username'); // Mengambil nama pengguna
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#resetPasswordMessage').html(`Atur ulang kata sandi @` + userName + `?`); // Menampilkan pesan reset kata sandi
            $('#resetPasswordSubmessage').html(`Kata sandi pengguna ini akan diatur sama dengan nama pengguna`); // Menjelaskan reset kata sandi
            $('#resetPasswordModal').modal('show'); // Menampilkan modal reset kata sandi
        });

        // Konfirmasi penghapusan pengguna
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#deleteMessage').html('Mengapus, silakan tunggu...'); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/admin/delete') ?>/${userId}`); // Menghapus pengguna
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

        // Konfirmasi aktivasi pengguna
        $('#confirmActivateBtn').click(async function() {
            $('#activateModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#activateMessage').html('Mengaktifkan, silakan tunggu...'); // Menampilkan pesan loading

            try {
                await axios.post(`<?= base_url('/admin/activate') ?>/${userId}`); // Mengaktifkan pengguna
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                $('#activateModal').modal('hide'); // Menyembunyikan modal aktivasi
                $('#activateModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Konfirmasi nonaktifkan pengguna
        $('#confirmDeactivateBtn').click(async function() {
            $('#deactivateModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#deactivateMessage').html('Menonaktifkan, silakan tunggu...'); // Menampilkan pesan loading

            try {
                await axios.post(`<?= base_url('/admin/deactivate') ?>/${userId}`); // Menonaktifkan pengguna
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                $('#deactivateModal').modal('hide'); // Menyembunyikan modal nonaktif
                $('#deactivateModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Konfirmasi reset kata sandi
        $('#confirmResetPasswordBtn').click(async function() {
            $('#resetPasswordModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#resetPasswordMessage').addClass('mb-0').html('Mengatur ulang kata sandi, silakan tunggu...'); // Menampilkan pesan loading
            $('#resetPasswordSubmessage').hide(); // Menyembunyikan subpesan

            try {
                await axios.post(`<?= base_url('/admin/resetpassword') ?>/${userId}`); // Mengatur ulang kata sandi pengguna
                showSuccessToast(`Kata sandi @${userName} berhasil diatur ulang.`); // Menampilkan pesan sukses
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                $('#resetPasswordModal').modal('hide'); // Menyembunyikan modal reset kata sandi
                $('#resetPasswordMessage').removeClass('mb-0'); // Menghapus kelas margin
                $('#resetPasswordSubmessage').show(); // Menampilkan subpesan
                $('#resetPasswordModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Mengirimkan form pengguna (Tambah/Edit)
        $('#userForm').submit(async function(e) {
            e.preventDefault(); // Mencegah pengiriman form default

            const url = $('#userId').val() ? '<?= base_url('/admin/update') ?>' : '<?= base_url('/admin/create') ?>'; // Menentukan URL berdasarkan apakah pengguna sedang diupdate atau ditambahkan
            const formData = new FormData(this); // Mengambil data form

            console.log("Form URL:", url); // Menampilkan URL di konsol
            console.log("Form Data:", $(this).serialize()); // Menampilkan data form di konsol

            // Menghapus status validasi sebelumnya
            $('#userForm .is-invalid').removeClass('is-invalid');
            $('#userForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses...</span>
            `); // Mengubah tampilan tombol submit menjadi loading

            // Menonaktifkan input form
            $('#userForm input, #userForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data' // Menentukan jenis konten untuk permintaan
                    }
                });

                if (response.data.success) {
                    $('#userModal').modal('hide'); // Menyembunyikan modal setelah sukses
                    table.ajax.reload(null, false); // Memperbarui tabel
                } else {
                    console.log("Validation Errors:", response.data.errors); // Menampilkan kesalahan validasi di konsol

                    // Menghapus status validasi sebelumnya
                    $('#userForm .is-invalid').removeClass('is-invalid');
                    $('#userForm .invalid-feedback').text('').hide();

                    // Menampilkan kesalahan validasi baru
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field); // Mengambil elemen field
                            const feedbackElement = fieldElement.siblings('.invalid-feedback'); // Mengambil elemen feedback

                            console.log("Target Field:", fieldElement); // Menampilkan elemen target di konsol
                            console.log("Target Feedback:", feedbackElement); // Menampilkan elemen feedback target di konsol

                            if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                fieldElement.addClass('is-invalid'); // Menandai field sebagai tidak valid
                                feedbackElement.text(response.data.errors[field]).show(); // Menampilkan pesan kesalahan

                                // Menghapus pesan kesalahan ketika pengguna memperbaiki input
                                fieldElement.on('input change', function() {
                                    $(this).removeClass('is-invalid'); // Menghapus tanda tidak valid
                                    $(this).siblings('.invalid-feedback').text('').hide(); // Menyembunyikan pesan kesalahan
                                });
                            } else {
                                console.warn("Elemen tidak ditemukan pada field:", field); // Peringatan jika elemen tidak ditemukan
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

                $('#userForm input, #userForm select, #closeBtn').prop('disabled', false); // Mengembalikan status input form
            }
        });

        // Mengatur ulang form dan status validasi saat modal ditutup
        $('#userModal').on('hidden.bs.modal', function() {
            $('#userForm')[0].reset(); // Mengatur ulang form
            $('.is-invalid').removeClass('is-invalid'); // Menghapus tanda tidak valid
            $('.invalid-feedback').text('').hide(); // Menyembunyikan semua pesan kesalahan
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>