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
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> transaksi</div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if (session()->get('role') != 'Admisi') : ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= base_url('transaksi/report') ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Laporan Transaksi Harian"><i class="fa-solid fa-file-invoice-dollar"></i></a>
    <?php endif; ?>
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
                    <div class="d-flex flex-column flex-lg-row gap-2 mb-2">
                        <div class="input-group input-group-sm w-auto">
                            <input type="date" id="tanggalFilter" class="form-control" <?= (session()->get('auto_date') == 1) ? 'value="' . date('Y-m-d') . '"' : ''; ?>>
                            <?php if (session()->get('auto_date') == 1) : ?>
                                <button class="btn btn-primary btn-sm bg-gradient" type="button" id="setTodayTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Kembali ke Hari Ini"><i class="fa-solid fa-calendar-day"></i></button>
                            <?php else : ?>
                                <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                            <?php endif; ?>
                        </div>
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="search" id="searchInput" class="form-control " placeholder="Cari pasien">
                        </div>
                    </div>
                    <div class="accordion accordion-bg-body" id="accordionFilter">
                        <div class="accordion-item">
                            <div class="accordion-header lh-1">
                                <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                    Pencarian Tambahan
                                </button>
                            </div>
                            <div id="collapseFilter" class="accordion-collapse collapse" data-bs-parent="#accordionFilter">
                                <div class="accordion-body px-2 py-1">
                                    <div class="d-flex flex-column flex-lg-row mb-1 gap-1 my-1">
                                        <?php if (session()->get('role') != 'Admisi') : ?>
                                            <select id="statusFilter" class="form-select form-select-sm w-auto  flex-fill">
                                                <option value="">Semua Transaksi</option>
                                                <option value="1">Diproses</option>
                                                <option value="0">Belum Diproses</option>
                                            </select>
                                        <?php endif; ?>
                                        <select id="jenisFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Jenis</option>
                                            <option value="Rawat Jalan">Rawat Jalan</option>
                                            <option value="Rawat Inap">Rawat Inap</option>
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
                <?php if (session()->get('role') != 'Admisi') : ?>
                    <div class="d-grid gap-2">
                        <button id="collapseList" class="btn btn-primary btn-sm bg-gradient shadow-sm rounded-bottom-0" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiFormContainer" aria-expanded="false" aria-controls="transaksiFormContainer">
                            <i class="fa-solid fa-plus"></i> Tambah Transaksi
                        </button>
                    </div>
                    <ul id="transaksiFormContainer" class="list-group rounded-0 collapse">
                        <li class="list-group-item border-top-0 bg-body-tertiary">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Tambah Pasien Rawat Jalan</div>
                                        <form id="transaksiForm1" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                            <div class="flex-fill">
                                                <select class="form-select form-select-sm" id="nomor_registrasi" name="nomor_registrasi" aria-label="nomor_registrasi">
                                                    <option value="" disabled selected>-- Pilih Pasien Rawat Jalan --</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                                <button type="submit" id="submitButton1" class="btn btn-primary bg-gradient btn-sm" disabled>
                                                    <i class="fa-solid fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Tambah Pasien dari Resep Luar</div>
                                        <form id="transaksiForm2" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                            <div class="flex-fill">
                                                <select class="form-select form-select-sm" id="id_resep" name="id_resep" aria-label="id_resep">
                                                    <option value="" disabled selected>-- Pilih Pasien dari Resep Luar --</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                                <button type="submit" id="submitButton2" class="btn btn-primary bg-gradient btn-sm" disabled>
                                                    <i class="fa-solid fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                <?php endif; ?>
                <ul id="transaksiContainer" class="list-group <?= (session()->get('role') != 'Admisi') ? 'rounded-top-0' : ''; ?>">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title d-flex placeholder-glow">
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 placeholder-glow">
                                        <span class="placeholder" style="width: 100%;"></span><br>
                                        <span class="placeholder w-100" style="max-width: 100px;"></span> <span class="placeholder w-100" style="max-width: 100px;"></span>
                                    </h6>
                                    <div class="card-text placeholder-glow">
                                        <div style="font-size: 0.75em;">
                                            <div class="row gx-3">
                                                <div class="col-lg-6">
                                                    <div class="mb-0 row g-1 placeholder-glow">
                                                        <div class="col-5 fw-medium text-truncate">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-0 row g-1 placeholder-glow">
                                                        <div class="col-5 fw-medium text-truncate">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-0 row g-1 placeholder-glow">
                                                        <div class="col-5 fw-medium text-truncate">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-0 row g-1 placeholder-glow">
                                                        <div class="col-5 fw-medium text-truncate">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                        <div class="col placeholder-glow">
                                                            <span class="placeholder w-100"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (session()->get('role') != 'Admisi') : ?>
                                            <span class="placeholder w-100" style="max-width: 100px;"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                                <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php if (session()->get('role') != 'Admisi') : ?>
                                    <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
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
    <?php if (session()->get('role') != 'Admisi') : ?>
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
    <?php endif; ?>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let limit = 12;
    let currentPage = 1;
    let transaksiId = null;
    var placeholder = `
            <li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex placeholder-glow">
                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                        </h5>
                        <h6 class="card-subtitle mb-2 placeholder-glow">
                            <span class="placeholder" style="width: 100%;"></span><br>
                            <span class="placeholder w-100" style="max-width: 100px;"></span> <span class="placeholder w-100" style="max-width: 100px;"></span>
                        </h6>
                        <div class="card-text placeholder-glow">
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 placeholder-glow">
                                            <div class="col-5 fw-medium text-truncate">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 placeholder-glow">
                                            <div class="col-5 fw-medium text-truncate">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 placeholder-glow">
                                            <div class="col-5 fw-medium text-truncate">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 placeholder-glow">
                                            <div class="col-5 fw-medium text-truncate">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (session()->get('role') != 'Admisi') : ?>
                                <span class="placeholder w-100" style="max-width: 100px;"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php if (session()->get('role') != 'Admisi') : ?>
                                    <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
                </div>
            </li>
    `;
    <?php if (session()->get('role') != 'Admisi') : ?>
        async function fetchPasienOptions1() {
            try {
                const response = await axios.get('<?= base_url('transaksi/pasienlist') ?>');

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#nomor_registrasi');

                    // Clear existing options except the first one
                    select.find('option:not(:first)').remove();

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

                    // Loop through the options and append them to the select element
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                }
            } catch (error) {
                showFailedToast('Gagal mendapatkan pasien.<br>' + error);
            }
        }
    <?php endif; ?>

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

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelection) {
                    select.val(currentSelection);
                }
            } else {
                showFailedToast('Gagal mendapatkan kasir.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan kasir.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchTransaksi() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = <?= (session()->get('role') == 'Admisi') ? "'1'" : "$('#statusFilter').val()"; ?>;
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
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#transaksiContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.transaksi.forEach(function(transaksi) {
                    const nama_pasien = transaksi.nama_pasien ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${transaksi.nama_pasien}">` :
                        `<em>Anonim</em>`;
                    let jenis_kelamin = transaksi.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    const kasir = transaksi.kasir == 'Ditambahkan Dokter' ? `<em>${transaksi.kasir}</em>` : `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${transaksi.kasir}">`;
                    const total_pembayaran = parseInt(transaksi.total_pembayaran);
                    const statusBadge = transaksi.lunas == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const bank = transaksi.bank ? ` (${transaksi.bank})` : ``;
                    const metode_pembayaran = transaksi.metode_pembayaran ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${transaksi.metode_pembayaran} ${bank}">` :
                        `<em>Belum ada</em>`;
                    let nomor_registrasi = transaksi.nomor_registrasi || "";
                    if (nomor_registrasi.includes("RJ")) {
                        nomor_registrasi = `<span class="badge bg-success bg-gradient text-nowrap"><i class="fa-solid fa-hospital-user"></i> RAWAT JALAN</span>`;
                    } else if (nomor_registrasi.includes("RI")) {
                        nomor_registrasi = `<span class="badge bg-success bg-gradient text-nowrap"><i class="fa-solid fa-bed-pulse"></i> RAWAT INAP</span>`;
                    }
                    const jenisResep = transaksi.id_resep ? `<span class="badge bg-secondary bg-gradient text-nowrap"><i class="fa-solid fa-prescription-bottle-medical"></i> RESEP LUAR</span>` : nomor_registrasi;
                    const statusButtons = transaksi.lunas == '1' ? `disabled` : ``;
                    const transaksiElement = `
                    <li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3">
                        <div class="d-flex">
                            <div class="align-self-center w-100">
                                <h5 class="card-title d-flex date justify-content-start">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${transaksi.number}</span>
                                    <span class="ms-1 align-self-center w-100">${nama_pasien}</span>
                                </h5>
                                <h6 class="card-subtitle mb-2">
                                    ${kasir}${jenis_kelamin} ${jenisResep}
                                </h6>
                                <div class="card-text">
                                    <div style="font-size: 0.75em;">
                                        <div class="row gx-3">
                                            <div class="col-lg-6">
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Nomor Kuitansi</div>
                                                    <div class="col date">
                                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${transaksi.no_kwitansi}">
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                                    <div class="col date">
                                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${transaksi.tgl_transaksi}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Grand Total</div>
                                                    <div class="col date">
                                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="Rp${total_pembayaran.toLocaleString('id-ID')}">
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Metode</div>
                                                    <div class="col">
                                                        ${metode_pembayaran}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (session()->get('role') != 'Admisi') : ?>
                                        ${statusBadge}
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                            <?php if (session()->get('role') == 'Admisi') : ?>
                                <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.open('<?= base_url('transaksi/struk') ?>/${transaksi.id_transaksi}');">
                                    <i class="fa-solid fa-print"></i> Kuitansi
                                </button>
                            <?php else : ?>
                                <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('transaksi/detailtransaksi') ?>/${transaksi.id_transaksi}';">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${transaksi.id_transaksi}" data-name="${transaksi.nama_pasien}" data-date="${transaksi.tgl_transaksi}" ${statusButtons}>
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            <?php endif; ?>
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
            <?php if (session()->get('role') != 'Admisi') : ?>
                fetchPasienOptions1()
                fetchPasienOptions2()
            <?php endif; ?>
            fetchTransaksi();
        }
    });

    $('#statusFilter, #jenisFilter, #namesFilter, #kasirFilter, #tanggalFilter').on('change', function() {
        $('#transaksiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#transaksiContainer').append(placeholder);
        }
        <?php if (session()->get('role') != 'Admisi') : ?>
            fetchPasienOptions1()
            fetchPasienOptions2()
        <?php endif; ?>
        fetchTransaksi();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#transaksiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#transaksiContainer').append(placeholder);
        }
        <?php if (session()->get('role') != 'Admisi') : ?>
            fetchPasienOptions1()
            fetchPasienOptions2()
        <?php endif; ?>
        fetchTransaksi();
    });
    $('#setTodayTglButton').on('click', async function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#tanggalFilter').val(formattedDate);
        $('#transaksiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#transaksiContainer').append(placeholder);
        }
        <?php if (session()->get('role') != 'Admisi') : ?>
            fetchPasienOptions1()
            fetchPasienOptions2()
        <?php endif; ?>
        fetchTransaksi();
    });

    <?php if (session()->get('role') != 'Admisi') : ?>

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
    <?php endif; ?>

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update || data.update_transaksi || data.delete) {
                console.log("Received update from WebSocket");
                // Simpan nilai pilihan kasir saat ini
                const selectedKasir = $('#kasirFilter').val();
                // Panggil fungsi untuk memperbarui opsi kasir
                await fetchKasirOptions(selectedKasir);
                <?php if (session()->get('role') != 'Admisi') : ?>
                    fetchPasienOptions1()
                    fetchPasienOptions2()
                <?php endif; ?>
                fetchTransaksi();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('[data-bs-toggle="popover"]').popover({
            html: true,
            template: '<div class="popover shadow-lg" role="tooltip">' +
                '<div class="popover-arrow"></div>' +
                '<h3 class="popover-header"></h3>' +
                '<div class="popover-body"></div>' +
                '</div>'
        });

        // Hilangkan popover ketika tombol #collapseList diklik
        $('#collapseList').on('click', function() {
            $('#APIInfoPopover').popover('hide');
        });

        <?php if (session()->get('role') != 'Admisi') : ?>
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
        <?php endif; ?>
        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchTransaksi();
        });

        <?php if (session()->get('role') != 'Admisi') : ?>
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
                $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

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
                    $('#deleteModal button').prop('disabled', false);
                    $(this).text(`Hapus`); // Mengembalikan teks tombol asal
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
                <?= $this->include('spinner/spinner'); ?> Tambah
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
                <?= $this->include('spinner/spinner'); ?> Tambah
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
        <?php endif; ?>
        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                // Simpan nilai pilihan kasir saat ini
                const selectedKasir = $('#kasirFilter').val();
                // Panggil fungsi untuk memperbarui opsi kasir
                await fetchKasirOptions(selectedKasir);
                <?php if (session()->get('role') != 'Admisi') : ?>
                    fetchPasienOptions1()
                    fetchPasienOptions2()
                <?php endif; ?>
                fetchTransaksi(); // Refresh articles on button click
            }
        });
        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan kasir saat ini
            const selectedKasir = $('#kasirFilter').val();
            // Panggil fungsi untuk memperbarui opsi kasir
            await fetchKasirOptions(selectedKasir);
            <?php if (session()->get('role') != 'Admisi') : ?>
                fetchPasienOptions1()
                fetchPasienOptions2()
            <?php endif; ?>
            fetchTransaksi(); // Refresh articles on button click
        });
        await fetchKasirOptions();
        fetchTransaksi();
        <?php if (session()->get('role') != 'Admisi') : ?>
            fetchPasienOptions1()
            fetchPasienOptions2()
            toggleSubmitButton1();
            toggleSubmitButton2();
        <?php endif; ?>
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>