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
<main class="col-md-9 ms-sm-auto col-lg-10">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0 mb-2">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <div class="input-group input-group-sm">
                        <input type="search" class="form-control form-control-sm " id="externalSearch" placeholder="Cari nama tindakan">
                        <button class="btn btn-success btn-sm bg-gradient " type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3">
        <div class="no-fluid-content">
            <div class="mb-3">
                <table id="tabel" class="table table-sm table-hover m-0 p-0" style="width:100%; font-size: 9pt;">
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
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
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
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="layananForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="layananModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_layanan" name="id_layanan">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_layanan" id="nama_layanan" name="nama_layanan">
                        <label for="nama_layanan">Nama*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select  " id="jenis_layanan" name="jenis_layanan" aria-label="jenis_layanan">
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="Rawat Jalan">Rawat Jalan</option>
                            <option value="Pemeriksaan Penunjang">Pemeriksaan Penunjang</option>
                            <option value="Operasi">Operasi</option>
                        </select>
                        <label for="jenis_layanan">Jenis*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control " autocomplete="off" dir="auto" placeholder="tarif" id="tarif" name="tarif">
                        <label for="tarif">Tarif (Rp)*</label>
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
                $('.pagination-sm').css({
                    '--bs-pagination-border-radius': 'var(--bs-border-radius-lg)'
                });
                $(".page-item .page-link").addClass("bg-gradient");
            },
            'buttons': [{
                // Tombol Tambah Tindakan
                text: '<i class="fa-solid fa-plus"></i> Tambah Tindakan',
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient  edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_layanan}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_layanan}" data-name="${row.nama_layanan}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
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

        // Tampilkan modal tambah layanan
        $('#addLayananBtn').click(function() {
            $('#layananModalLabel').text('Tambah Tindakan'); // Ubah judul modal menjadi 'Tambah Tindakan'
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
                <span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>
            `); // Ubah tombol menjadi indikator loading

            try {
                const response = await axios.get(`<?= base_url('/layanan/layanan') ?>/${id}`); // Ambil data layanan berdasarkan ID
                $('#layananModalLabel').text('Edit Tindakan'); // Ubah judul modal menjadi 'Edit Tindakan'
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
            $('#deleteMessage').html(`Hapus "` + layananName + `"?`); // Pesan konfirmasi
            $('#deleteSubmessage').html(`Layanan tidak dapat dihapus jika ada transaksi yang menggunakan layanan ini!`);
            $('#deleteModal').modal('show'); // Tampilkan modal konfirmasi
        });

        // Proses konfirmasi hapus layanan
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true); // Nonaktifkan tombol saat proses berlangsung
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...'); // Ubah pesan menjadi indikator proses
            $('#deleteSubmessage').hide(); // Sembunyikan pesan tambahan

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
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show(); // Tampilkan kembali pesan tambahan
                $('#deleteModal button').prop('disabled', false); // Aktifkan kembali tombol
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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
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
                        const feedbackElement = fieldElement.siblings('.invalid-feedback');

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