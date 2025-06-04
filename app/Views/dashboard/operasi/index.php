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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> pasien operasi</div>
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
    <div id="filterFields" class="sticky-top" style="z-index: 99; display: none;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
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
                            <input type="search" id="searchInput" class="form-control " placeholder="Cari nomor rekam medis atau nama pasien">
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
                                <div class="accordion-body px-2 py-2">
                                    <div class="row g-1">
                                        <div class="col-lg">
                                            <select id="dokterFilter" class="form-select form-select-sm">
                                                <option value="">Semua Dokter</option>
                                            </select>
                                        </div>
                                        <div class="col-lg">
                                            <select id="statusFilter" class="form-select form-select-sm">
                                                <option value="">Semua Status</option>
                                                <option value="DIJADWAL">Dijadwal</option>
                                                <option value="OPERASI">Operasi</option>
                                                <option value="TERLAKSANA">Terlaksana</option>
                                                <option value="BATAL">Batal</option>
                                            </select>
                                        </div>
                                    </div>
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
                    <button id="collapseList" class="btn btn-primary btn-sm bg-gradient  rounded-bottom-0" type="button" data-bs-toggle="collapse" data-bs-target="#spOperasiFormContainer" aria-expanded="false" aria-controls="spOperasiFormContainer">
                        <i class="fa-solid fa-plus"></i> Tambah Pasien Operasi
                    </button>
                </div>
                <ul id="spOperasiFormContainer" class="list-group rounded-0 collapse">
                    <li class="list-group-item border-top-0 bg-body-tertiary">
                        <form id="spOperasiForm" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                            <div class="flex-fill">
                                <select class="form-select form-select-sm" id="nomor_registrasi" name="nomor_registrasi" aria-label="nomor_registrasi">
                                    <option value="" disabled selected>-- Pilih Pasien Operasi --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                <button type="submit" id="submitButton" class="btn btn-primary bg-gradient btn-sm" disabled>
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </div>
                        </form>
                    </li>
                </ul>
                <ul id="spOperasiContainer" class="list-group rounded-top-0 ">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
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
                                        <span class="placeholder w-100" style="max-width: 100px;"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex justify-content-end">
                                <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                                <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                                <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                                <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
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
    <div class="modal fade" id="statusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
            <form id="statusForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="statusModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_sp_operasi" name="id_sp_operasi" value="">
                    <div class="mb-1 mt-1 radio-group">
                        <div class="d-flex flex-wrap justify-content-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_operasi" id="status_operasi1" value="DIJADWAL">
                                <label class="form-check-label" for="status_operasi1">
                                    Dijadwal
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_operasi" id="status_operasi2" value="OPERASI">
                                <label class="form-check-label" for="status_operasi2">
                                    Operasi
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_operasi" id="status_operasi3" value="TERLAKSANA">
                                <label class="form-check-label" for="status_operasi3">
                                    Terlaksana
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_operasi" id="status_operasi4" value="BATAL">
                                <label class="form-check-label" for="status_operasi4">
                                    Batal
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status_operasi" id="status_operasi5" value="HAPUS">
                                <label class="form-check-label" for="status_operasi5">
                                    Hapus
                                </label>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="alert alert-danger mb-1 mt-1" id="deleteAlert" role="alert" style="display: none;">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                Pilihan ini akan menghapus pasien operasi.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <div class="d-flex justify-content-end">
                        <button type="submit" id="statusSubmitButton" class="btn btn-primary bg-gradient">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
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
            <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
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
                            <span class="placeholder w-100" style="max-width: 100px;"></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                    <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                    <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                    <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 50px; height: 31px;"></a>
                </div>
            </li>
    `;
    async function fetchPasienOptions() {
        try {
            const response = await axios.get('<?= base_url('operasi/rawatjalanlist') ?>');

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

    async function fetchDokterOptions(selectedDokter = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('operasi/dokterlist') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#dokterFilter');

                // Simpan nilai yang saat ini dipilih
                const currentSelection = selectedDokter || select.val();

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
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan dokter.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchSPOperasi() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const dokter = $('#dokterFilter').val();
        const status = $('#statusFilter').val();
        const tanggal = $('#tanggalFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/operasilist') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    dokter: dokter,
                    status: status,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#spOperasiContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#spOperasiContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.sp_operasi.forEach(function(sp_operasi) {
                    const tanggal_operasi = sp_operasi.tanggal_operasi ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${sp_operasi.tanggal_operasi} ${sp_operasi.jam_operasi}">` :
                        `<em>Belum ada</em>`;
                    const dokter_operator = sp_operasi.dokter_operator == 'Belum Ada' ?
                        `<em>Belum ada</em>` :
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${sp_operasi.dokter_operator}">`;
                    let jenis_kelamin = sp_operasi.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    let statusBadge = sp_operasi.status_operasi;
                    if (statusBadge === 'DIJADWAL') {
                        statusBadge = `<span class="badge bg-primary bg-gradient">Dijadwal</span>`;
                    } else if (statusBadge === 'OPERASI') {
                        statusBadge = `<span class="badge bg-secondary bg-gradient">Operasi</span>`;
                    } else if (statusBadge === 'TERLAKSANA') {
                        statusBadge = `<span class="badge bg-success bg-gradient">Terlaksana</span>`;
                    } else if (statusBadge === 'BATAL') {
                        statusBadge = `<span class="badge bg-danger bg-gradient">Batal</span>`;
                    }
                    const sp_operasiElement = `
                    <li class="list-group-item border-top-0 pb-3 pt-3">
                        <div class="d-flex">
                            <div class="align-self-center w-100">
                                <h5 class="card-title d-flex date justify-content-start">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${sp_operasi.number}</span>
                                    <span class="ms-1 align-self-center w-100"><input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${sp_operasi.nama_pasien}"></span>
                                </h5>
                                <h6 class="card-subtitle mb-2">
                                    <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${sp_operasi.nomor_booking}">${jenis_kelamin}
                                </h6>
                                <div class="card-text">
                                    <div style="font-size: 0.75em;">
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                                                    <div class="col date">
                                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${sp_operasi.no_rm}">
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                                                    <div class="col date">
                                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${sp_operasi.nomor_registrasi}">
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                                    <div class="col date">
                                                        ${tanggal_operasi}
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center">
                                                    <div class="col-5 fw-medium text-truncate">Dokter</div>
                                                    <div class="col">
                                                        ${dokter_operator}
                                                    </div>
                                                </div>
                                    </div>
                                    ${statusBadge}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-body btn-sm bg-gradient status-btn" data-id="${sp_operasi.id_sp_operasi}">
                                    <i class="fa-solid fa-gear"></i> Atur Status
                                </button>
                                <?php if (session()->get('role') == 'Admisi') : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.open('<?= base_url('operasi/spko/export') ?>/${sp_operasi.id_sp_operasi}');">
                                        <i class="fa-solid fa-print"></i> SPKO
                                    </button>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.open('<?= base_url('operasi/praoperasi/export') ?>/${sp_operasi.id_sp_operasi}');">
                                        <i class="fa-solid fa-print"></i> Pra Operasi
                                    </button>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.open('<?= base_url('operasi/safety/export') ?>/${sp_operasi.id_sp_operasi}');">
                                        <i class="fa-solid fa-print"></i> Keselamatan
                                    </button>
                                <?php else : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('operasi/spko') ?>/${sp_operasi.id_sp_operasi}';">
                                        <i class="fa-solid fa-circle-info"></i> SPKO
                                    </button>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('operasi/praoperasi') ?>/${sp_operasi.id_sp_operasi}';">
                                        <i class="fa-solid fa-user-check"></i> Pra Operasi
                                    </button>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('operasi/safety') ?>/${sp_operasi.id_sp_operasi}';">
                                        <i class="fa-solid fa-user-shield"></i> Keselamatan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                `;
                    $('#spOperasiContainer').append(sp_operasiElement);
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
            $('#spOperasiContainer').empty();
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
            fetchPasienOptions()

            fetchSPOperasi();
        }
    });

    $('#tanggalFilter, #dokterFilter, #statusFilter').on('change', function() {
        $('#spOperasiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#spOperasiContainer').append(placeholder);
        }
        fetchPasienOptions()

        fetchSPOperasi();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#spOperasiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#spOperasiContainer').append(placeholder);
        }
        fetchPasienOptions();
        fetchSPOperasi();
    });
    $('#setTodayTglButton').on('click', async function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#tanggalFilter').val(formattedDate);
        $('#spOperasiContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#spOperasiContainer').append(placeholder);
        }
        fetchPasienOptions();
        fetchSPOperasi();
    });

    function toggleSubmitButton() {
        var selectedValue = $('#nomor_registrasi').val();
        if (selectedValue === null || selectedValue === "") {
            $('#submitButton').prop('disabled', true);
        } else {
            $('#submitButton').prop('disabled', false);
        }
    }
    $('#nomor_registrasi').on('change.select2', function() {
        toggleSubmitButton();
    });

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update || data.delete) {
                console.log("Received update from WebSocket");
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                // Panggil fungsi untuk memperbarui opsi dokter
                await fetchDokterOptions(selectedDokter);
                fetchPasienOptions();
                fetchSPOperasi();
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

        $('#nomor_registrasi').select2({
            dropdownParent: $('#spOperasiForm'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchSPOperasi();
        });

        $(document).on('click', '.status-btn', async function() {
            var $this = $(this);
            var id = $this.data('id');
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Atur Status`);

            try {
                let response = await axios.get(`<?= base_url('/operasi/spko/view') ?>/` + id);
                let data = response.data;
                console.log(data);
                const tanggalRegistrasi = new Date(data.tanggal_registrasi);
                const hariIni = new Date();

                // Hitung selisih waktu dalam milidetik
                const selisihWaktu = hariIni - tanggalRegistrasi;

                // Ubah ke hari (1 hari = 86.400.000 ms)
                const selisihHari = selisihWaktu / (1000 * 60 * 60 * 24);

                // Cek apakah lebih dari 14 hari
                if (selisihHari > 14) {
                    $('#status_operasi5').prop('disabled', true);
                }

                $('#statusModalLabel').text('Atur Status Pasien Operasi');
                $('#id_sp_operasi').val(data.id_sp_operasi);;
                const status_operasi = data.status_operasi;
                if (status_operasi) {
                    $("input[name='status_operasi'][value='" + status_operasi + "']").prop('checked', true);
                }
                $('#statusModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-gear"></i> Atur Status`);
            }
        });

        $('input[name="status_operasi"]').on('change', function() {
            if ($('#status_operasi5').is(':checked')) {
                // Ubah teks, ikon, dan kelas tombol saat status_operasi5 (Hapus) dipilih
                $('#statusSubmitButton')
                    .html('<i class="fa-solid fa-trash"></i> Hapus')
                    .removeClass('btn-primary')
                    .addClass('btn-danger');
                $('#deleteAlert').show();
            } else {
                // Kembalikan ke semula jika selain status_operasi5 yang dipilih
                $('#statusSubmitButton')
                    .html('<i class="fa-solid fa-floppy-disk"></i> Simpan')
                    .removeClass('btn-danger')
                    .addClass('btn-primary');
                $('#deleteAlert').hide();
            }
        });

        $('#statusForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#statusForm .is-invalid').removeClass('is-invalid');
            $('#statusForm .invalid-feedback').text('').hide();
            $('#statusSubmitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Memproses...
            `);

            // Disable form inputs
            $('#statusForm input').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('operasi/setstatus') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    $('#statusModal').modal('hide');
                    $('#nomor_registrasi').val(null).trigger('change');
                    await fetchDokterOptions();
                    fetchSPOperasi();
                    fetchPasienOptions();
                    toggleSubmitButton();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#statusForm .is-invalid').removeClass('is-invalid');
                    $('#statusForm .invalid-feedback').text('').hide();

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
                if (error.response.request.status === 404 || error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#statusSubmitButton').prop('disabled', false);
            } finally {
                if ($('#status_operasi5').is(':checked')) {
                    // Ubah teks, ikon, dan kelas tombol saat status_operasi5 (Hapus) dipilih
                    $('#statusSubmitButton')
                        .prop('disabled', false)
                        .html('<i class="fa-solid fa-trash"></i> Hapus')
                        .removeClass('btn-primary')
                        .addClass('btn-danger');
                } else {
                    // Kembalikan ke semula jika selain status_operasi5 yang dipilih
                    $('#statusSubmitButton')
                        .prop('disabled', false)
                        .html('<i class="fa-solid fa-floppy-disk"></i> Simpan')
                        .removeClass('btn-danger')
                        .addClass('btn-primary');
                }
                $('#statusForm input').prop('disabled', false);
            }
        });

        // Reset form saat modal ditutup
        $('#statusModal').on('hidden.bs.modal', function() {
            $('#statusForm')[0].reset();
            $('#statusSubmitButton')
                .prop('disabled', false)
                .html('<i class="fa-solid fa-floppy-disk"></i> Simpan')
                .removeClass('btn-danger')
                .addClass('btn-primary');
            $('#deleteAlert').hide();
            $('#statusForm .is-invalid').removeClass('is-invalid');
            $('#statusForm .invalid-feedback').text('').hide();
        });

        const selectedDokter = $('dokterFilter').val();

        $('#spOperasiForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#spOperasiForm .is-invalid').removeClass('is-invalid');
            $('#spOperasiForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#spOperasiForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('operasi/create') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#nomor_registrasi').val(null).trigger('change');
                    await fetchDokterOptions();
                    fetchSPOperasi();
                    fetchPasienOptions();
                    toggleSubmitButton();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#spOperasiForm .is-invalid').removeClass('is-invalid');
                    $('#spOperasiForm .invalid-feedback').text('').hide();

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
                $('#submitButton').prop('disabled', false);
            } finally {
                $('#submitButton').html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#spOperasiForm select').prop('disabled', false);
            }
        });

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                // Panggil fungsi untuk memperbarui opsi dokter
                await fetchDokterOptions(selectedDokter);
                fetchPasienOptions();
                fetchSPOperasi(); // Refresh articles on button click
            }
        });
        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan dokter saat ini
            const selectedDokter = $('#dokterFilter').val();
            // Panggil fungsi untuk memperbarui opsi dokter
            await fetchDokterOptions(selectedDokter);
            fetchPasienOptions();
            fetchSPOperasi(); // Refresh articles on button click
        });
        await fetchDokterOptions();
        fetchSPOperasi();
        fetchPasienOptions();
        toggleSubmitButton();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>