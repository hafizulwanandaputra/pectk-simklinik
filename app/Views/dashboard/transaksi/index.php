<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    @media (min-width: 992px) {
        .max-width-flex {
            width: 238px;
        }
    }
</style>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?> <span id="totalRecords" class="date"></span></span></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm me-3" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('transaksi/report') ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Laporan Transaksi Harian"><i class="fa-solid fa-file-export"></i></a>
    <a id="toggleFilter" class="fs-5 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Pencarian"><i class="fa-solid fa-magnifying-glass"></i></a>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div id="filterFields" class="sticky-top" style="z-index: 99; display: none;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-column flex-lg-row mb-1 gap-2 mb-2">
                        <div class="input-group input-group-sm">
                            <input type="date" id="tanggalFilter" class="form-control ">
                            <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="search" id="searchInput" class="form-control " placeholder="Cari pasien">
                            <button class="btn btn-success btn-sm bg-gradient " type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></button>
                        </div>
                    </div>
                    <div class="accordion" id="accordionFilter">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                    Pencarian Tambahan
                                </button>
                            </h2>
                            <div id="collapseFilter" class="accordion-collapse collapse" data-bs-parent="#accordionFilter">
                                <div class="accordion-body px-2 py-1">
                                    <div class="d-flex flex-column flex-lg-row mb-1 gap-1 my-1">
                                        <select id="statusFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Transaksi</option>
                                            <option value="1">Diproses</option>
                                            <option value="0">Belum Diproses</option>
                                        </select>
                                        <select id="jenisFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Resep</option>
                                            <option value="Resep Dokter">Resep Dokter</option>
                                            <option value="Resep Luar">Resep Luar</option>
                                        </select>
                                        <select id="namesFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Nama</option>
                                            <option value="1">Dengan Nama</option>
                                            <option value="0">Anonim</option>
                                        </select>
                                    </div>
                                    <select id="kasirFilter" class="form-select form-select-sm  my-1">
                                        <option value="">Semua Petugas Kasir</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="shadow-sm rounded">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm bg-gradient  rounded-bottom-0" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiFormContainer" aria-expanded="false" aria-controls="transaksiFormContainer">
                        <i class="fa-solid fa-plus"></i> Tambah Transaksi
                    </button>
                </div>
                <ul id="transaksiFormContainer" class="list-group rounded-0 collapse">
                    <li class="list-group-item border-top-0 bg-body-tertiary">
                        <div class="row gy-3">
                            <div class="col-lg-6">
                                <div>
                                    <div class="fw-bold mb-2 border-bottom">Tambah Pasien Rawat Jalan</div>
                                    <form id="transaksiForm1" enctype="multipart/form-data" class="d-flex flex-column mb-2 gap-2">
                                        <div class="flex-fill">
                                            <select class="form-select " id="nomor_registrasi" name="nomor_registrasi" aria-label="nomor_registrasi">
                                                <option value="" disabled selected>-- Pilih Pasien Rawat Jalan --</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                            <div class="btn-group">
                                                <button class="btn btn-body bg-gradient dropdown-toggle dropdown-toggle-split no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-circle-question"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end bg-body-tertiary shadow-sm transparent-blur">
                                                    <h6 class="dropdown-header text-wrap max-width-flex">Data-data pasien rawat jalan ini diperoleh dari <em>Application Programming Interface</em> (API) Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan berdasarkan tanggal hari ini</h6>
                                                    <li>
                                                        <a class="dropdown-item px-2 py-1" href="https://pectk.padangeyecenter.com/klinik" target="_blank">
                                                            <div class="d-flex align-items-start">
                                                                <span style="min-width: 32px; max-width: 32px; text-align: center;"><i class="fa-solid fa-up-right-from-square"></i></span>
                                                                <span>Buka SIM Klinik</span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <button type="submit" id="submitButton1" class="btn btn-primary bg-gradient " disabled>
                                                    <i class="fa-solid fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <div class="fw-bold mb-2 border-bottom">Tambah Pasien dari Resep Luar</div>
                                    <form id="transaksiForm2" enctype="multipart/form-data" class="d-flex flex-column mb-2 gap-2">
                                        <div class="flex-fill">
                                            <select class="form-select " id="id_resep" name="id_resep" aria-label="id_resep">
                                                <option value="" disabled selected>-- Pilih Pasien dari Resep Luar --</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                            <button type="submit" id="submitButton2" class="btn btn-primary bg-gradient " disabled>
                                                <i class="fa-solid fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul id="transaksiContainer" class="list-group rounded-top-0 ">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item border-top-0 bg-body-tertiary pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title placeholder-glow">
                                        <span class="placeholder" style="width: 100%"></span>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 placeholder-glow">
                                        <span class="placeholder" style="width: 25%;"></span><br>
                                        <span class="placeholder" style="width: 12.5%;"></span>
                                    </h6>
                                    <div class="card-text placeholder-glow">
                                        <div style="font-size: 0.75em;">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-1 row placeholder-glow">
                                                        <div class="col-5 col-lg-4 fw-medium">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-1 row placeholder-glow">
                                                        <div class="col-5 col-lg-4 fw-medium">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-1 row placeholder-glow">
                                                        <div class="col-5 col-lg-4 fw-medium">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-1 row placeholder-glow">
                                                        <div class="col-5 col-lg-4 fw-medium">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-1 row placeholder-glow">
                                                        <div class="col-5 col-lg-4 fw-medium">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="placeholder w-100" style="max-width: 100px;"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex justify-content-end">
                                <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                <a class="btn btn-danger bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
            <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                <ul class="pagination pagination-sm"></ul>
            </nav>
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
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let limit = 12;
    let currentPage = 1;
    let transaksiId = null;
    var placeholder = `
            <li class="list-group-item border-top-0 bg-body-tertiary pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title placeholder-glow">
                            <span class="placeholder" style="width: 100%"></span>
                        </h5>
                        <h6 class="card-subtitle mb-2 placeholder-glow">
                            <span class="placeholder" style="width: 25%;"></span><br>
                            <span class="placeholder" style="width: 12.5%;"></span>
                        </h6>
                        <div class="card-text placeholder-glow">
                            <div style="font-size: 0.75em;">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-1 row placeholder-glow">
                                            <div class="col-5 col-lg-4 fw-medium">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                        <div class="mb-1 row placeholder-glow">
                                            <div class="col-5 col-lg-4 fw-medium">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                        <div class="mb-1 row placeholder-glow">
                                            <div class="col-5 col-lg-4 fw-medium">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-1 row placeholder-glow">
                                            <div class="col-5 col-lg-4 fw-medium">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                        <div class="mb-1 row placeholder-glow">
                                            <div class="col-5 col-lg-4 fw-medium">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="placeholder w-100" style="max-width: 100px;"></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-danger bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                </div>
            </li>
    `;
    async function fetchPasienOptions1() {
        try {
            const response = await axios.get('<?= base_url('transaksi/pasienlist') ?>');

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#nomor_registrasi');

                // Clear existing options except the first one
                select.find('option:not(:first)').remove();

                // Sort the options by 'value' in ascending order
                options.sort((a, b) => b.value.localeCompare(a.value, 'en', {
                    numeric: true
                }));

                // Loop through the options and append them to the select element
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }
    async function fetchPasienOptions2() {
        try {
            const response = await axios.get('<?= base_url('transaksi/pasienlistexternal') ?>');

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#id_resep');

                // Clear existing options except the first one
                select.find('option:not(:first)').remove();

                // Sort the options by 'value' in ascending order
                options.sort((a, b) => b.value.localeCompare(a.value, 'en', {
                    numeric: true
                }));

                // Loop through the options and append them to the select element
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan pasien.<br>' + error);
        }
    }

    async function fetchKasirOptions(selectedKasir = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('transaksi/kasirlist') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#kasirFilter');

                // Simpan nilai yang saat ini dipilih
                const currentSelection = selectedKasir || select.val();

                // Hapus semua opsi kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Urutkan opsi berdasarkan 'value' secara ascending
                options.sort((a, b) => b.value.localeCompare(a.value, 'en', {
                    numeric: true
                }));

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelection) {
                    select.val(currentSelection);
                }
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan dokter.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchTransaksi() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = $('#statusFilter').val();
        const jenis = $('#jenisFilter').val();
        const names = $('#namesFilter').val();
        const kasir = $('#kasirFilter').val();
        const tanggal = $('#tanggalFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('transaksi/listtransaksi') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    status: status,
                    jenis: jenis,
                    names: names,
                    kasir: kasir,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#transaksiContainer').empty();
            $('#totalRecords').text(`(${data.total.toLocaleString('id-ID')})`);

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#transaksiContainer').append(
                    '<li class="list-group-item border-top-0 bg-body-tertiary pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.transaksi.forEach(function(transaksi) {
                    const nama_pasien = transaksi.nama_pasien == null ?
                        `<em>Anonim</em>` :
                        transaksi.nama_pasien;
                    let jenis_kelamin = transaksi.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    const total_pembayaran = parseInt(transaksi.total_pembayaran);
                    const statusBadge = transaksi.lunas == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const bank = transaksi.bank ? ` (${transaksi.bank})` : ``;
                    const metode_pembayaran = transaksi.metode_pembayaran == '' ?
                        `<em>Belum ada</em>` :
                        transaksi.metode_pembayaran + bank;
                    const jenisResep = transaksi.id_resep ? `<span class="badge bg-secondary bg-gradient text-nowrap">RESEP LUAR</span>` : `<span class="badge bg-success bg-gradient text-nowrap">RESEP DOKTER</span>`;
                    const transaksiElement = `
                    <li class="list-group-item border-top-0 bg-body-tertiary pb-3 pt-3">
                        <div class="d-flex">
                            <div class="align-self-center w-100">
                                <h5 class="card-title">
                                    [<span class="date" style="font-weight: 900;">${transaksi.number}</span>] ${nama_pasien}
                                </h5>
                                <h6 class="card-subtitle mb-2">
                                    ${transaksi.kasir}<br>${jenis_kelamin} ${jenisResep}
                                </h6>
                                <div class="card-text">
                                    <div style="font-size: 0.75em;">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-1 row">
                                                    <div class="col-5 col-lg-4 fw-medium">ID</div>
                                                    <div class="col date">
                                                        ${transaksi.id_transaksi}
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-5 col-lg-4 fw-medium">Nomor Kuitansi</div>
                                                    <div class="col date">
                                                        ${transaksi.no_kwitansi}
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-5 col-lg-4 fw-medium">Tanggal dan Waktu</div>
                                                    <div class="col date">
                                                        ${transaksi.tgl_transaksi}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-1 row">
                                                    <div class="col-5 col-lg-4 fw-medium">Grand Total</div>
                                                    <div class="col date">
                                                        Rp${total_pembayaran.toLocaleString('id-ID')}
                                                    </div>
                                                </div>
                                                <div class="mb-1 row">
                                                    <div class="col-5 col-lg-4 fw-medium">Metode</div>
                                                    <div class="col">
                                                        ${metode_pembayaran}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    ${statusBadge}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('transaksi/detailtransaksi') ?>/${transaksi.id_transaksi}';">
                                <i class="fa-solid fa-circle-info"></i> Detail
                            </button>
                            <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${transaksi.id_transaksi}" data-name="${transaksi.nama_pasien}" data-date="${transaksi.tgl_transaksi}">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>
                    </li>
                `;
                    $('#transaksiContainer').append(transaksiElement);
                });

                // Pagination logic with ellipsis for more than 3 pages
                const totalPages = Math.ceil(data.total / limit);
                $('#paginationNav ul').empty();

                if (currentPage > 1) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                }

                if (totalPages > 5) {
                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                    if (currentPage > 3) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }

                    if (currentPage < totalPages - 2) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                } else {
                    // Show all pages if total pages are 3 or fewer
                    for (let i = 1; i <= totalPages; i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }
                }

                if (currentPage < totalPages) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#transaksiContainer').empty();
            $('#paginationNav ul').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }


    $(document).on('click', '#paginationNav a', function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            fetchPasienOptions1()
            fetchPasienOptions2()
            fetchTransaksi();
        }
    });

    $('#statusFilter, #jenisFilter, #namesFilter, #kasirFilter, #tanggalFilter').on('change', function() {
        $('#transaksiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#transaksiContainer').append(placeholder);
        }
        fetchPasienOptions1()
        fetchPasienOptions2()
        fetchTransaksi();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#transaksiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#transaksiContainer').append(placeholder);
        }
        fetchPasienOptions1();
        fetchPasienOptions2();
        fetchTransaksi();
    });

    function toggleSubmitButton1() {
        var selectedValue = $('#nomor_registrasi').val();
        if (selectedValue === null || selectedValue === "") {
            $('#submitButton1').prop('disabled', true);
        } else {
            $('#submitButton1').prop('disabled', false);
        }
    }
    $('#nomor_registrasi').on('change.select2', function() {
        toggleSubmitButton1();
    });

    function toggleSubmitButton2() {
        var selectedValue = $('#id_resep').val();
        if (selectedValue === null || selectedValue === "") {
            $('#submitButton2').prop('disabled', true);
        } else {
            $('#submitButton2').prop('disabled', false);
        }
    }
    $('#id_resep').on('change.select2', function() {
        toggleSubmitButton2();
    });

    $(document).ready(async function() {
        $('#nomor_registrasi').select2({
            dropdownParent: $('#transaksiForm1'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        $('#id_resep').select2({
            dropdownParent: $('#transaksiForm2'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchTransaksi();
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

        // Store the ID of the user to be deleted
        var transaksiId;
        var transaksiName;
        var transaksiDate;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            transaksiId = $(this).data('id');
            transaksiName = $(this).data('name');
            transaksiDate = $(this).data('date');
            // Check if transaksiName is null or undefined
            const nama_pasien = (transaksiName === null || transaksiName === undefined || transaksiName === 'null') ?
                'yang anonim ini' :
                `dari "${transaksiName}"`;
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus transaksi ${nama_pasien}?`);
            $('#deleteSubmessage').html(`Tanggal Transaksi: ` + transaksiDate);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/transaksi/delete') ?>/${transaksiId}`);
                // Simpan nilai pilihan kasir saat ini
                const selectedKasir = $('#kasirFilter').val();
                // Panggil fungsi untuk memperbarui opsi kasir
                await fetchKasirOptions(selectedKasir);
                fetchPasienOptions1();
                fetchPasienOptions2();
                fetchTransaksi();
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
            }
        }); // Simpan nilai pilihan apoteker saat ini
        const selectedApoteker = $('apotekerFilter').val();

        $('#transaksiForm1').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#transaksiForm1 .is-invalid').removeClass('is-invalid');
            $('#transaksiForm1 .invalid-feedback').text('').hide();
            $('#submitButton1').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#transaksiForm1 select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('transaksi/create') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#transaksiForm1')[0].reset();
                    $('#nomor_registrasi').val(null).trigger('change');
                    $('#transaksiForm1 .is-invalid').removeClass('is-invalid');
                    $('#transaksiForm1 .invalid-feedback').text('').hide();
                    $('#submitButton1').prop('disabled', true);
                    // Simpan nilai pilihan kasir saat ini
                    const selectedKasir = $('#kasirFilter').val();
                    // Panggil fungsi untuk memperbarui opsi kasir
                    await fetchKasirOptions(selectedKasir);
                    fetchPasienOptions1();
                    fetchTransaksi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#transaksiForm1 .is-invalid').removeClass('is-invalid');
                    $('#transaksiForm1 .invalid-feedback').text('').hide();

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
                if (error.response.request.status === 404) {
                    showFailedToast(error.response.data.message);
                } else if (error.response.request.status === 422) {
                    showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#submitButton1').prop('disabled', false);
            } finally {
                $('#submitButton1').html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#transaksiForm1 select').prop('disabled', false);
            }
        });

        $('#transaksiForm2').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#transaksiForm2 .is-invalid').removeClass('is-invalid');
            $('#transaksiForm2 .invalid-feedback').text('').hide();
            $('#submitButton2').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#transaksiForm2 select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('transaksi/createexternal') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#transaksiForm2')[0].reset();
                    $('#id_resep').val(null).trigger('change');
                    $('#transaksiForm2 .is-invalid').removeClass('is-invalid');
                    $('#transaksiForm2 .invalid-feedback').text('').hide();
                    $('#submitButton2').prop('disabled', true);
                    // Simpan nilai pilihan kasir saat ini
                    const selectedKasir = $('#kasirFilter').val();
                    // Panggil fungsi untuk memperbarui opsi kasir
                    await fetchKasirOptions(selectedKasir);
                    fetchPasienOptions2();
                    fetchTransaksi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#transaksiForm2 .is-invalid').removeClass('is-invalid');
                    $('#transaksiForm2 .invalid-feedback').text('').hide();

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
                if (error.response.request.status === 404) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#submitButton2').prop('disabled', false);
            } finally {
                $('#submitButton2').html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#transaksiForm2 select').prop('disabled', false);
            }
        });
        $('#refreshButton').on('click', async function() {
            // Simpan nilai pilihan kasir saat ini
            const selectedKasir = $('#kasirFilter').val();
            // Panggil fungsi untuk memperbarui opsi kasir
            await fetchKasirOptions(selectedKasir);
            fetchPasienOptions1();
            fetchPasienOptions2();
            fetchTransaksi(); // Refresh articles on button click
        });
        await fetchKasirOptions();
        fetchTransaksi();
        fetchPasienOptions1();
        fetchPasienOptions2();
        toggleSubmitButton1();
        toggleSubmitButton2();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>