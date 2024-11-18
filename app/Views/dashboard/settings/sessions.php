<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/settings'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4">
    <div class="d-xxl-flex justify-content-center">
        <div class="no-fluid-content">
            <div class="sticky-top" style="z-index: 99;">
                <ul class="list-group shadow-sm rounded-top-0 rounded-bottom-3 mb-2">
                    <li class="list-group-item border-top-0 bg-body-tertiary">
                        <div class="input-group input-group-sm">
                            <input type="search" class="form-control form-control-sm rounded-start-3" id="externalSearch" placeholder="Cari nama pengguna, alamat IP, dan user agent">
                            <button class="btn btn-success btn-sm bg-gradient rounded-end-3" type="button" id="refreshButton"><i class="fa-solid fa-sync"></i></button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="mb-2">
                <table id="tabel" class="table table-sm table-hover" style="width:100%; font-size: 9pt;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No.</th>
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Pengguna</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Alamat IP</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">User Agent</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Waktu Masuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Kedaluwarsa</th>
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
    <div class="modal modal-sheet p-4 py-md-5 fade" id="flushModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="flushModal" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="flushMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" data-bs-dismiss="modal" style="border-right: 1px solid var(--bs-border-color-translucent);">Tidak</button>
                    <button type="submit" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmFlushBtn">Ya</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteExpiredModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteExpiredModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteExpiredMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteExpiredBtn">Ya</button>
                </div>
            </div>
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
                "sEmptyTable": 'Tidak ada sesi. Sesi akan muncul ketika ada yang masuk ke sistem ini di perangkat lain.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ sesi",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 sesi",
                "sInfoFiltered": "(di-filter dari _MAX_ sesi)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ sesi",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Sesi yang Anda cari tidak ditemukan",
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
                $('select[name="tabel_length"]').addClass("rounded-3");
            },
            'buttons': [{
                // Tombol Bersihkan Sesi
                text: '<i class="fa-solid fa-broom"></i> Bersihkan',
                className: 'btn-danger btn-sm bg-gradient rounded-start-3',
                attr: {
                    id: 'flushBtn'
                },
                init: function(api, node, config) {
                    $(node).removeClass('btn-secondary')
                },
            }, {
                // Tombol Hapus Sesi Kedaluwarsa
                text: '<i class="fa-solid fa-trash"></i> Kedaluwarsa',
                className: 'btn-danger btn-sm bg-gradient rounded-end-3',
                attr: {
                    id: 'deleteExpiredBtn'
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
                "url": "<?= base_url('/settings/sessionslist') ?>",
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
                        return `
                            <div class="d-grid">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id}" data-username="${row.username}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'username',
                    render: function(data, type, row) {
                        // Get current date and time in the same format as 'expires_at'
                        const currentDate = new Date();
                        const expiresAt = new Date(row.expires_at); // Assuming 'expires_at' is in a standard date-time string format

                        // Check if 'expires_at' has passed the current date and time
                        const isExpired = expiresAt < currentDate;
                        const statusBadge = isExpired ?
                            '<span class="badge bg-danger bg-gradient">Kedaluwarsa</span>' :
                            '<span class="badge bg-success bg-gradient">Aktif</span>';
                        return `<strong>${row.fullname}</strong><br>@${data} ${statusBadge}`;
                    }
                },
                {
                    data: 'ip_address',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'user_agent'
                },
                {
                    data: 'created_at',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
                {
                    data: 'expires_at',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap">${data}</span>`;
                    }
                },
            ],
            "order": [
                [5, 'desc']
            ],
            "columnDefs": [{
                "target": [1],
                "orderable": false
            }, {
                "target": [0, 1, 3, 5, 6],
                "width": "0%"
            }, {
                "target": [2],
                "width": "25%"
            }, {
                "target": [4],
                "width": "75%"
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

        $('#refreshButton').on('click', async function() {
            table.ajax.reload(null, false);
        });

        // Menyimpan ID pengguna yang akan dihapus
        var userId;
        var userName;

        // Menampilkan modal konfirmasi penghapusan
        $(document).on('click', '.delete-btn', function() {
            userId = $(this).data('id'); // Mengambil ID pengguna dari atribut data
            userName = $(this).data('username'); // Mengambil nama pengguna dari atribut data
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $('#deleteMessage').html(`Hapus sesi dari @` + userName + `?`); // Menampilkan pesan konfirmasi penghapusan
            $('#deleteModal').modal('show'); // Menampilkan modal konfirmasi
        });

        // Menampilkan modal untuk mengaktifkan pengguna
        $(document).on('click', '#flushBtn', function() {
            $('#flushMessage').html(`Melakukan pembersihan sesi akan membuat semua pengguna kecuali Anda di perangkat ini keluar dan diminta untuk masuk kembali. Apakah Anda ingin melanjutkan?`); // Menampilkan pesan konfirmasi pembersihan sesi
            $('#flushModal').modal('show'); // Menampilkan modal pembersihan sesi
        });

        // Menampilkan modal untuk menonaktifkan pengguna
        $(document).on('click', '#deleteExpiredBtn', function() {
            $('#deleteExpiredMessage').html(`Hapus seluruh sesi yang kedaluwarsa?`); // Menampilkan pesan konfirmasi nonaktif
            $('#deleteExpiredModal').modal('show'); // Menampilkan modal nonaktif
        });

        // Konfirmasi penghapusan pengguna
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#deleteMessage').html('Mengapus, silakan tunggu...'); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/settings/deletesession') ?>/${userId}`); // Menghapus pengguna
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                if (error.response.request.status === 404) {
                    showFailedToast(error.response.data.error);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteModal').modal('hide'); // Menyembunyikan modal penghapusan
                $('#deleteModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Konfirmasi aktivasi pengguna
        $('#confirmFlushBtn').click(async function() {
            $('#flushModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#flushMessage').html('Membersihkan, silakan tunggu...'); // Menampilkan pesan loading

            try {
                const response = await axios.delete(`<?= base_url('/settings/flush') ?>`); // Mengaktifkan pengguna
                showSuccessToast(response.data.message);
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                if (error.response.request.status === 404) {
                    showFailedToast(error.response.data.error);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#flushModal').modal('hide'); // Menyembunyikan modal aktivasi
                $('#flushModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        // Konfirmasi nonaktifkan pengguna
        $('#confirmDeleteExpiredBtn').click(async function() {
            $('#deleteExpiredModal button').prop('disabled', true); // Menonaktifkan tombol konfirmasi
            $('#deleteExpiredMessage').html('Menghapus, silakan tunggu...'); // Menampilkan pesan loading

            try {
                const response = await axios.delete(`<?= base_url('/settings/deleteexpired') ?>`); // Menghapus sesi yang kadaluwarsa
                showSuccessToast(response.data.message);
                table.ajax.reload(null, false); // Memperbarui tabel
            } catch (error) {
                if (error.response.request.status === 404) {
                    showFailedToast(error.response.data.error);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteExpiredModal').modal('hide'); // Menyembunyikan modal nonaktif
                $('#deleteExpiredModal button').prop('disabled', false); // Mengembalikan status tombol
            }
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>