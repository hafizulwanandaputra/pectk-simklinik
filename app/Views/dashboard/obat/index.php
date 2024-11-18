<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/floating'); ?>
<?= $this->endSection(); ?>
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
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4">
    <div class="d-xxl-flex justify-content-center">
        <div class="no-fluid-content">
            <div class="sticky-top" style="z-index: 99;">
                <ul class="list-group shadow-sm rounded-top-0 rounded-bottom-3 mb-2">
                    <li class="list-group-item border-top-0 bg-body-tertiary">
                        <div class="input-group input-group-sm">
                            <input type="search" class="form-control form-control-sm rounded-start-3" id="externalSearch" placeholder="Cari merek, nama obat, dan isi obat">
                            <button class="btn btn-success btn-sm bg-gradient rounded-end-3" type="button" id="refreshButton"><i class="fa-solid fa-sync"></i></button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="mb-2">
                <table id="tabel" class="table table-sm table-hover" style="width:100%; font-size: 9pt;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">No</th>
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Merek</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Nama</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Isi</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Kategori</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Bentuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Harga Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">PPN</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Mark Up</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Pembulatan Harga</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Penyesuaian Harga</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Harga Jual</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jumlah Masuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Jumlah Keluar</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Sisa Stok</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px;">Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody class="align-top">
                    </tbody>
                </table>
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
            <div class="modal fade" id="obatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="obatModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable rounded-3">
                    <form id="obatForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                        <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                            <h6 class="pe-2 modal-title fs-6 text-truncate" id="obatModalLabel" style="font-weight: bold;"></h6>
                            <button id="closeBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                        </div>
                        <div class="modal-body py-2">
                            <input type="hidden" id="id_obat" name="id_obat">
                            <div class="form-floating mt-1 mb-1">
                                <select class="form-select rounded-3" id="id_supplier" name="id_supplier" aria-label="id_supplier">
                                    <option value="" disabled selected>-- Pilih Merek dan Supplier --</option>
                                </select>
                                <label for="id_dokter">Merek dan Supplier*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="text" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="nama_obat" id="nama_obat" name="nama_obat">
                                <label for="nama_obat">Nama*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="text" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="isi_obat" id="isi_obat" name="isi_obat">
                                <label for="isi_obat">Isi</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mt-1 mb-1">
                                <input type="text" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="kategori_obat" id="kategori_obat" name="kategori_obat" list="list_kategori_obat">
                                <label for="kategori_obat">Kategori Obat</label>
                                <div class="invalid-feedback"></div>
                                <datalist id="list_kategori_obat">
                                    <option value="Antibiotik">
                                    <option value="Antiinflamasi">
                                    <option value="Antihistamin">
                                    <option value="Dekongestan">
                                    <option value="Pelumas">
                                    <option value="Antiglaukoma">
                                    <option value="Antivirus">
                                    <option value="Antijamur">
                                    <option value="Suplemen">
                                </datalist>
                            </div>
                            <div class="form-floating mt-1 mb-1">
                                <select class="form-select rounded-3" id="bentuk_obat" name="bentuk_obat" aria-label="bentuk_obat">
                                    <option value="" disabled selected>-- Pilih Bentuk --</option>
                                    <optgroup label="Obat Luar">
                                        <option value="Tetes">Tetes</option>
                                        <option value="Salep">Salep</option>
                                    </optgroup>
                                    <optgroup label="Obat Dalam">
                                        <option value="Tablet/Kapsul">Tablet/Kapsul</option>
                                        <option value="Sirup">Sirup</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="Alat Kesehatan">Alat Kesehatan</option>
                                    </optgroup>
                                </select>
                                <label for="bentuk_obat">Bentuk*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="number" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="harga_obat" id="harga_obat" name="harga_obat">
                                <label for="harga_obat">Harga Obat (Rp)*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="number" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="ppn" id="ppn" name="ppn">
                                <label for="ppn">PPN (%)*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="number" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="mark_up" id="mark_up" name="mark_up">
                                <label for="mark_up">Mark Up (%)*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="number" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="penyesuaian_harga" id="penyesuaian_harga" name="penyesuaian_harga">
                                <label for="penyesuaian_harga">Penyesuaian Harga (Rp)*</label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-floating mb-1 mt-1">
                                <input type="number" class="form-control rounded-3" autocomplete="off" dir="auto" placeholder="jumlah_masuk" id="jumlah_masuk" name="jumlah_masuk">
                                <label for="jumlah_masuk" id="stok_label"></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                            <div>
                                Harga Jual: <span class="fw-bold date" id="hasil_harga_jual">Rp0</span>
                            </div>
                            <button type="submit" id="submitButton" class="btn btn-primary bg-gradient rounded-3">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan
                            </button>
                        </div>
                    </form>
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
                "sEmptyTable": 'Tidak ada obat. Klik "Tambah Obat" untuk menambahkan obat.',
                "sInfo": "Menampilkan _START_ hingga _END_ dari _TOTAL_ obat",
                "sInfoEmpty": "Menampilkan 0 hingga 0 dari 0 obat",
                "sInfoFiltered": "(di-filter dari _MAX_ obat)",
                "sInfoPostFix": "",
                "sThousands": ".",
                "sLengthMenu": "Tampilkan _MENU_ obat",
                "sLoadingRecords": "Memuat...",
                "sProcessing": "",
                "sSearch": "Cari:",
                "sZeroRecords": "Obat yang Anda cari tidak ditemukan",
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
                // Tombol Tambah Obat
                text: '<i class="fa-solid fa-plus"></i> Tambah Obat',
                className: 'btn-primary btn-sm bg-gradient rounded-3',
                attr: {
                    id: 'addObatBtn'
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
                "url": "<?= base_url('/obat/obatlist') ?>",
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
                                    <button class="btn btn-outline-body text-nowrap bg-gradient rounded-start-3 edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${row.id_obat}" data-name="${row.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'merek',
                    render: function(data, type, row) {
                        const merek = data ? data : '<em>Tanpa merek</em>';
                        return `<span>${merek}<br><small>${row.nama_supplier}</small></span>`;
                    }
                },
                {
                    data: 'nama_obat'
                },
                {
                    data: 'isi_obat'
                },
                {
                    data: 'kategori_obat'
                },
                {
                    data: 'bentuk_obat'
                },
                {
                    data: 'harga_obat',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'ppn',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${data}%</span>`;
                    }
                },
                {
                    data: 'mark_up',
                    render: function(data, type, row) {
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${data}%</span>`;
                    }
                },
                {
                    data: 'selisih_harga',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'penyesuaian_harga',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'harga_jual',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedHarga = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);

                        return `<span class="date text-nowrap" style="display: block; text-align: right;">Rp${formattedHarga}</span>`;
                    }
                },
                {
                    data: 'jumlah_masuk',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'jumlah_keluar',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'sisa_stok',
                    render: function(data, type, row) {
                        // Format harga_obat using number_format equivalent in JavaScript
                        let formattedData = new Intl.NumberFormat('id-ID', {
                            style: 'decimal',
                            minimumFractionDigits: 0
                        }).format(data);
                        return `<span class="date text-nowrap" style="display: block; text-align: right;">${formattedData}</span>`;
                    }
                },
                {
                    data: 'updated_at',
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
                "target": [0, 1, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
                "width": "0%"
            }, {
                "target": [2, 3],
                "width": "50%"
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

        // Fungsi untuk mengambil opsi supplier dari server
        async function fetchSupplierOptions() {
            try {
                // Mengirim permintaan GET untuk mendapatkan daftar supplier
                const response = await axios.get('<?= base_url('obat/supplierlist') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#id_supplier');

                    // Hapus opsi yang sudah ada, kecuali yang pertama (placeholder)
                    select.find('option:not(:first)').remove();

                    // Tambahkan opsi supplier ke dalam elemen select
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                // Tampilkan pesan error jika terjadi kegagalan
                showFailedToast('Gagal mendapatkan dokter.<br>' + error);
            }
        }

        // Panggil fungsi untuk mengisi opsi supplier saat halaman dimuat
        fetchSupplierOptions();

        // Fungsi untuk menghitung harga jual
        function hitungHargaJual() {
            let hargaObat = parseFloat($('#harga_obat').val()) || 0;
            let ppn = parseFloat($('#ppn').val()) || 0;
            let markUp = parseFloat($('#mark_up').val()) || 0;
            let penyesuaianHarga = parseFloat($('#penyesuaian_harga').val()) || 0;

            // 1. Hitung PPN
            let jumlahPpn = (hargaObat * ppn) / 100;
            let totalHargaPpn = hargaObat + jumlahPpn;

            // 2. Hitung Mark Up
            let jumlahMarkUp = (totalHargaPpn * markUp) / 100;
            let totalHarga = totalHargaPpn + jumlahMarkUp;

            // 3. Bulatkan ke ratusan terdekat ke atas
            let hargaBulat = Math.ceil(totalHarga / 100) * 100;

            // 4. Tambahkan penyesuaian harga
            let hargaJual = hargaBulat + penyesuaianHarga;

            // 5. Tampilkan hasil dengan format Rupiah
            $('#hasil_harga_jual').text('Rp' + new Intl.NumberFormat('id-ID').format(hargaJual));
        }

        // Event listener untuk setiap input
        $('#harga_obat, #ppn, #mark_up, #penyesuaian_harga').on('input', function() {
            hitungHargaJual(); // Panggil fungsi saat ada perubahan nilai
        });

        // Event handler untuk menampilkan modal tambah obat
        $('#addObatBtn').click(function() {
            $('#obatModalLabel').text('Tambah Obat');
            $('#stok_label').text('Jumlah Stok Awal*');
            $('#obatModal').modal('show');
        });

        // Inisialisasi select2 untuk elemen #id_supplier
        $('#id_supplier').select2({});

        // Konfigurasi tambahan select2 dengan parent dropdown dari modal
        $('#obatModal').on('shown.bs.modal', function() {
            $('#id_supplier').select2({
                dropdownParent: $('#obatModal'),
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
            });
        });

        // Event handler untuk menampilkan modal edit obat
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);

            try {
                const response = await axios.get(`<?= base_url('/obat/obat') ?>/${id}`);
                $('#obatModalLabel').text('Edit Obat');
                $('#id_obat').val(response.data.id_obat);
                $('#id_supplier').val(response.data.id_supplier);
                $('#nama_obat').val(response.data.nama_obat);
                $('#isi_obat').val(response.data.isi_obat);
                $('#kategori_obat').val(response.data.kategori_obat);
                $('#bentuk_obat').val(response.data.bentuk_obat);
                $('#harga_obat').val(response.data.harga_obat);
                $('#ppn').val(response.data.ppn);
                $('#mark_up').val(response.data.mark_up);
                $('#penyesuaian_harga').val(response.data.penyesuaian_harga);
                $('#jumlah_masuk').val('0');
                $('#stok_label').text(`Tambah/Kurangi Stok* (masuk: ${response.data.jumlah_masuk}; keluar: ${response.data.jumlah_keluar}; stok: ${response.data.jumlah_masuk - response.data.jumlah_keluar})`);
                hitungHargaJual();
                $('#obatModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        // Variabel untuk menyimpan ID dan nama obat yang akan dihapus
        var obatId;
        var obatName;

        // Event handler untuk menampilkan modal konfirmasi hapus
        $(document).on('click', '.delete-btn', function() {
            obatId = $(this).data('id');
            obatName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus "` + obatName + `"?`);
            $('#deleteModal').modal('show');
        });

        // Event handler untuk konfirmasi penghapusan obat
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Mengapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/obat/delete') ?>/${obatId}`);
                table.ajax.reload(null, false);
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.<br>' + error;
                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = 'Tidak dapat menghapus data ini karena sedang digunakan.'
                }
                showFailedToast(errorMessage);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        // Event handler untuk submit form obat
        $('#obatForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_obat').val() ? '<?= base_url('/obat/update') ?>' : '<?= base_url('/obat/create') ?>';
            const formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", $(this).serialize());

            $('#obatForm .is-invalid').removeClass('is-invalid');
            $('#obatForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses...</span>
            `);
            $('#obatForm input, #obatForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#obatModal').modal('hide');
                    table.ajax.reload(null, false);
                } else {
                    console.log("Validation Errors:", response.data.errors);
                    for (const field in response.data.errors) {
                        const fieldElement = $('#' + field);
                        const feedbackElement = fieldElement.siblings('.invalid-feedback');

                        if (fieldElement.length && feedbackElement.length) {
                            fieldElement.addClass('is-invalid');
                            feedbackElement.text(response.data.errors[field]).show();

                            fieldElement.on('input change', function() {
                                $(this).removeClass('is-invalid');
                                $(this).siblings('.invalid-feedback').text('').hide();
                            });
                        }
                    }
                }
            } catch (error) {
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#obatForm input, #obatForm select, #closeBtn').prop('disabled', false);
            }
        });

        // Reset form dan status validasi saat modal obat ditutup
        $('#obatModal').on('hidden.bs.modal', function() {
            $('#obatForm')[0].reset();
            $('#id_obat').val('');
            $('#id_supplier').val(null).trigger('change');
            $('#hasil_harga_jual').text('Rp0');
            $('#obatForm .is-invalid').removeClass('is-invalid');
            $('#obatForm .invalid-feedback').text('').hide();
        });

        <?= $this->include('toast/index') ?>
    });
</script>
<?= $this->endSection(); ?>