<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    @media (min-width: 992px) {
        .max-width-flex {
            width: 600px;
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
                                <div class="accordion-body px-2 py-1 mt-1">
                                    <div class="row row-cols-1 row-cols-sm-2 g-1">
                                        <div class="col">
                                            <select id="statusFilter" class="form-select form-select-sm">
                                                <option value="">Semua Status Transaksi</option>
                                                <option value="1">Diproses</option>
                                                <option value="0">Belum Diproses</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select id="jenisFilter" class="form-select form-select-sm">
                                                <option value="">Semua Jenis</option>
                                                <option value="Rawat Jalan">Rawat Jalan</option>
                                                <option value="Rawat Inap">Rawat Inap</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select id="confirmedFilter" class="form-select form-select-sm">
                                                <option value="">Semua Status Konfirmasi</option>
                                                <option value="1">Dikonfirmasi</option>
                                                <option value="0">Belum Dikonfirmasi</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select id="genderFilter" class="form-select form-select-sm">
                                                <option value="">Semua Jenis Kelamin</option>
                                                <option value="L">Laki-Laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <select id="dokterFilter" class="form-select form-select-sm  my-1">
                                        <option value="">Semua Dokter</option>
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
                <ul id="resepContainer" class="list-group">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title d-flex justify-content-start placeholder-glow">
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
                                        <span class="placeholder w-100" style="max-width: 100px;"></span> <span class="placeholder w-100" style="max-width: 100px;"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
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
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Haous</button>
                        </div>
                    </div>
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
    let pembelianObatId = null;
    var placeholder = `
            <li class="list-group-item pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex justify-content-start placeholder-glow">
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
                            <span class="placeholder w-100" style="max-width: 100px;"></span> <span class="placeholder w-100" style="max-width: 100px;"></span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                </div>
            </li>
    `;
    async function fetchDokterOptions(selectedDokter = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('resep/dokterlist') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#dokterFilter');

                // Simpan nilai yang saat ini dipilih
                const currentSelection = selectedDokter || select.val();

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

    async function fetchResep() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = $('#statusFilter').val();
        const jenis = $('#jenisFilter').val();
        const gender = $('#genderFilter').val();
        const confirmed = $('#confirmedFilter').val();
        const dokter = $('#dokterFilter').val();
        const tanggal = $('#tanggalFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resep/listresep') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    status: status,
                    jenis: jenis,
                    gender: gender,
                    confirmed: confirmed,
                    dokter: dokter,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#resepContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#resepContainer').append(
                    '<li class="list-group-item pb-3 pt-3">' +
                    '    <h1 class="display-4 text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.resep.forEach(function(resep) {
                    let jenis_kelamin = resep.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    const jumlah_resep = parseInt(resep.jumlah_resep);
                    const total_biaya = parseInt(resep.total_biaya);
                    const statusBadge = resep.status == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const statusButtons = resep.status == '1' ? `disabled` : ``;
                    const status = resep.status == '1' ? `disabled` : ``;
                    let nomor_registrasi = resep.nomor_registrasi || "";
                    if (nomor_registrasi.includes("RJ")) {
                        nomor_registrasi = `<span class="badge bg-success bg-gradient text-nowrap"><i class="fa-solid fa-hospital-user"></i> RAWAT JALAN</span>`;
                    } else if (nomor_registrasi.includes("RI")) {
                        nomor_registrasi = `<span class="badge bg-success bg-gradient text-nowrap"><i class="fa-solid fa-bed-pulse"></i> RAWAT INAP</span>`;
                    }
                    const resepElement = `
            <li class="list-group-item pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-between">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${resep.number}</span>
                            <span class="ms-1 align-self-center w-100"><input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${resep.nama_pasien}"></span>
                        </h5>
                        <h6 class="card-subtitle mb-2">
                            <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${resep.dokter}">${jenis_kelamin} ${nomor_registrasi}
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
                                            <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${resep.no_rm}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${resep.nomor_registrasi}">
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
                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('resep/detailresep') ?>/${resep.id_resep}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${resep.id_resep}" data-name="${resep.nama_pasien}" data-date="${resep.tanggal_resep}" ${status}>
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </li>
                `;

                    $('#resepContainer').append(resepElement);
                });

                $('[data-bs-toggle="tooltip"]').tooltip();

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

    $('#statusFilter, #jenisFilter, #genderFilter, #confirmedFilter, #dokterFilter, #tanggalFilter').on('change', function() {
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
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                await fetchDokterOptions(selectedDokter);
                fetchResep();
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

        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchResep();
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
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus resep untuk "` + resepName + `"?`);
            $('#deleteSubmessage').html(`Tanggal Resep: ` + resepDate);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/resep/delete') ?>/${resepId}`);
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                // Panggil fungsi untuk memperbarui opsi dokter
                await fetchDokterOptions(selectedDokter);
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

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                await fetchDokterOptions(selectedDokter);
                fetchResep();
            }
        });
        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan dokter saat ini
            const selectedDokter = $('#dokterFilter').val();
            await fetchDokterOptions(selectedDokter);
            fetchResep();
        });

        await fetchDokterOptions();
        fetchResep();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>