<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> resep</div>
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
    <div id="filterFields" class="sticky-top px-3 pt-2" style="z-index: 99; display: none;">
        <ul class="list-group no-fluid-content shadow-sm border border-bottom-0">
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
                                        <select id="statusFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Status Transaksi</option>
                                            <option value="1">Diproses</option>
                                            <option value="0">Belum Diproses</option>
                                        </select>
                                        <select id="namesFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Nama</option>
                                            <option value="1">Dengan Nama</option>
                                            <option value="0">Anonim</option>
                                        </select>
                                        <select id="genderFilter" class="form-select form-select-sm w-auto  flex-fill">
                                            <option value="">Semua Jenis Kelamin</option>
                                            <option value="L">Laki-Laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <select id="apotekerFilter" class="form-select form-select-sm  my-1">
                                        <option value="">Semua Apoteker</option>
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
                    <button class="btn btn-primary btn-sm bg-gradient  rounded-bottom-0" type="button" id="addButton">
                        <i class="fa-solid fa-plus"></i> Tambah Resep Luar
                    </button>
                </div>
                <ul id="resepContainer" class="list-group rounded-top-0 ">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title d-flex placeholder-glow">
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 placeholder-glow">
                                        <span class="placeholder" style="width: 100%;"></span><br>
                                        <span class="placeholder w-100" style="max-width: 100px;"></span>
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
                                        <span class="placeholder w-100" style="max-width: 100px;"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                                <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
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
    <div class="modal fade" id="resepluarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="resepluarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="resepluarForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="resepluarModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_resep" name="id_resep">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="nama_pasien" id="nama_pasien" name="nama_pasien">
                        <label for="nama_pasien">Nama Pasien</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mt-1 mb-0 row g-1">
                        <label for="jenis_kelamin" class="col-3 col-form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin1" value="L">
                                    <label class="form-check-label" for="jenis_kelamin1">
                                        Laki-Laki
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin2" value="P">
                                    <label class="form-check-label" for="jenis_kelamin2">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="date" class="form-control " autocomplete="off" dir="auto" placeholder="tanggal_lahir" id="tanggal_lahir" name="tanggal_lahir">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="alamat" id="alamat" name="alamat">
                        <label for="alamat_pasien">Alamat</label>
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
<?= $this->section('javascript'); ?>
<script>
    let limit = 12;
    let currentPage = 1;
    let pembelianObatId = null;
    var placeholder = `
            <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex placeholder-glow">
                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                        </h5>
                        <h6 class="card-subtitle mb-2 placeholder-glow">
                            <span class="placeholder" style="width: 100%;"></span><br>
                            <span class="placeholder w-100" style="max-width: 100px;"></span>
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
                            <span class="placeholder w-100" style="max-width: 100px;"></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                </div>
            </li>
    `;

    async function fetchApotekerOptions(selectedApoteker = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('resepluar/apotekerlist') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#apotekerFilter');

                // Simpan nilai yang saat ini dipilih
                const currentSelection = selectedApoteker || select.val();

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
                showFailedToast('Gagal mendapatkan apoteker.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan apoteker.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchResep() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = $('#statusFilter').val();
        const gender = $('#genderFilter').val();
        const names = $('#namesFilter').val();
        const apoteker = $('#apotekerFilter').val();
        const tanggal = $('#tanggalFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resepluar/listresep') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    status: status,
                    gender: gender,
                    names: names,
                    apoteker: apoteker,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#resepContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#resepContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.resep.forEach(function(resep) {
                    const nama_pasien = resep.nama_pasien ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${resep.nama_pasien}">` :
                        `<em>Anonim</em>`;
                    let jenis_kelamin = resep.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    const alamat = resep.alamat ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${resep.alamat}">` :
                        `<em>Tidak ada</em>`;
                    const jumlah_resep = parseInt(resep.jumlah_resep);
                    const total_biaya = parseInt(resep.total_biaya);
                    const statusBadge = resep.status == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const statusButtons = resep.status == '1' ? `disabled` : ``;
                    const tanggal_lahir = !resep.tanggal_lahir || resep.tanggal_lahir === '0000-00-00' ?
                        '<em>Tidak ada</em>' :
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${resep.tanggal_lahir}">`;
                    const resepElement = `
            <li class="list-group-item border-top-0 pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-start">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${resep.number}</span>
                            <span class="ms-1 align-self-center w-100">${nama_pasien}</span>
                        </h5>
                        <h6 class="card-subtitle mb-2">
                            <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${resep.apoteker}">${jenis_kelamin}
                        </h6>
                        <div class="card-text">
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${resep.tanggal_resep}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Tanggal Lahir</div>
                                            <div class="col date">
                                                ${tanggal_lahir}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Alamat</div>
                                            <div class="col">
                                                ${alamat}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Total Resep</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${jumlah_resep.toLocaleString('id-ID')}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Total Harga</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="Rp${total_biaya.toLocaleString('id-ID')}">
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
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('resepluar/detailresep') ?>/${resep.id_resep}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-body btn-sm bg-gradient  edit-btn" data-id="${resep.id_resep}" ${statusButtons}>
                        <i class="fa-solid fa-pen-to-square"></i> Edit Identitas
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${resep.id_resep}" data-name="${resep.nama_pasien}" data-date="${resep.tanggal_resep}" ${statusButtons}>
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </li>
                `;

                    $('#resepContainer').append(resepElement);
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
            $('#resepContainer').empty();
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
            fetchResep();
        }
    });

    $('#statusFilter, #genderFilter, #namesFilter, #apotekerFilter, #tanggalFilter').on('change', function() {
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        fetchResep();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        fetchResep();
    });
    $('#setTodayTglButton').on('click', async function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#tanggalFilter').val(formattedDate);
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        fetchResep();
    });

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update || data.update_resep || data.delete) {
                console.log("Received update from WebSocket");
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('#apotekerFilter').val();
                // Panggil fungsi untuk memperbarui opsi apoteker
                await fetchApotekerOptions(selectedApoteker);
                fetchResep();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchResep();
        });

        // Tampilkan modal tambah layanan
        $('#addButton').click(function() {
            $('#resepluarModalLabel').text('Tambah Resep Luar'); // Ubah judul modal menjadi 'Tambah Resep Luar'
            $('#tanggal_lahir').flatpickr({
                altInput: true,
                allowInput: true,
                altFormat: "d-m-Y",
                defaultDate: ''
            });
            $('#resepluarModal').modal('show'); // Tampilkan modal resep luar
        });

        // Fokuskan kursor ke field 'nama_pasien' saat modal ditampilkan
        $('#resepluarModal').on('shown.bs.modal', function() {
            $('#nama_pasien').trigger('focus');
        });

        // Event klik untuk tombol edit
        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id'); // Dapatkan ID layanan
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Sembunyikan tooltip
            $this.prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Edit Identitas
            `); // Ubah tombol menjadi indikator loading
            try {
                const response = await axios.get(`<?= base_url('/resepluar/resep') ?>/${id}`); // Ambil data resep luar berdasarkan ID
                const tanggal_lahir = response.data.tanggal_lahir === '0000-00-00' ? '' : response.data.tanggal_lahir;
                $('#resepluarModalLabel').text('Edit Identitas Pasien'); // Ubah judul modal menjadi 'Edit Identitas Pasien'
                $('#id_resep').val(response.data.id_resep);
                $('#nama_pasien').val(response.data.nama_pasien);
                $('#alamat').val(response.data.alamat);
                const selectedGender = response.data.jenis_kelamin;
                if (selectedGender) {
                    $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
                }
                $('#tanggal_lahir').flatpickr({
                    altInput: true,
                    allowInput: true,
                    altFormat: "d-m-Y",
                    defaultDate: tanggal_lahir
                });
                $('#resepluarModal').modal('show'); // Tampilkan modal dengan data resep luar
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Tampilkan pesan kesalahan
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i> Edit Identitas`); // Pulihkan tombol
            }
        });

        // Store the ID of the user to be deleted
        var resepId;
        var resepName;
        var resepDate;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            resepId = $(this).data('id');
            resepName = $(this).data('name');
            resepDate = $(this).data('date');
            // Check if transaksiName is null or undefined
            const nama_pasien = (resepName === null || resepName === undefined || resepName === 'null') ?
                'yang anonim ini' :
                `dari "${resepName}"`;
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus resep ${nama_pasien}?`);
            $('#deleteSubmessage').html(`Tanggal Resep: ` + resepDate);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/resepluar/delete') ?>/${resepId}`);
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('#apotekerFilter').val();
                // Panggil fungsi untuk memperbarui opsi apoteker
                await fetchApotekerOptions(selectedApoteker);
                fetchResep();
            } catch (error) {
                if (error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        $('#resepluarForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_resep').val() ? '<?= base_url('/resepluar/update') ?>' : '<?= base_url('/resepluar/create') ?>';
            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#resepluarForm .is-invalid').removeClass('is-invalid');
            $('#resepluarForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Menambahkan
            `);

            // Disable form inputs
            $('#resepluarForm input, #resepluarForm select').prop('disabled', true);

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#resepluarModal').modal('hide');
                    // Simpan nilai pilihan apoteker saat ini
                    const selectedApoteker = $('#apotekerFilter').val();
                    // Panggil fungsi untuk memperbarui opsi apoteker
                    await fetchApotekerOptions(selectedApoteker);
                    fetchResep();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#resepluarForm .is-invalid').removeClass('is-invalid');
                    $('#resepluarForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'jenis_kelamin') {
                                const radioGroup = $("input[name='jenis_kelamin']");
                                const feedbackElement = radioGroup.closest('.col-form-label').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[name='jenis_kelamin']").removeClass('is-invalid');
                                        feedbackElement.removeAttr('style').hide();
                                    });
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
                }
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#resepluarForm input, #resepluarForm select').prop('disabled', false);
            }
        });

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                const selectedApoteker = $('#apotekerFilter').val();
                // Panggil fungsi untuk memperbarui opsi apoteker
                await fetchApotekerOptions(selectedApoteker);
                fetchResep(); // Refresh articles on button click
            }
        });

        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan apoteker saat ini
            const selectedApoteker = $('#apotekerFilter').val();
            // Panggil fungsi untuk memperbarui opsi apoteker
            await fetchApotekerOptions(selectedApoteker);
            fetchResep(); // Refresh articles on button click
        });

        // Reset form saat modal ditutup
        $('#resepluarModal').on('hidden.bs.modal', function() {
            $('#resepluarForm')[0].reset();
            $('#id_resep').val('');
            $('#resepluarForm .is-invalid').removeClass('is-invalid');
            // Hapus nilai input secara langsung
            $('#tanggal_lahir').val('');

            // Hapus instance flatpickr lama jika ada
            const flatpickrInstance = $('#tanggal_lahir').data('flatpickr');
            if (flatpickrInstance) {
                flatpickrInstance.destroy();
            }

            // Inisialisasi ulang flatpickr
            $('#tanggal_lahir').flatpickr({
                altInput: true,
                allowInput: true,
                altFormat: "d-m-Y",
                defaultDate: '' // Default kosong
            });
            $('#resepluarForm .invalid-feedback').text('').hide();
        });
        await fetchApotekerOptions();
        fetchResep();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>