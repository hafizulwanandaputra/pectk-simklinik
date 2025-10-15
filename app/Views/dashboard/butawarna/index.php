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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> surat</div>
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
    <div id="filterFields" class="sticky-top px-2 pt-2" style="z-index: 99; display: none;">
        <ul class="list-group no-fluid-content-list-group shadow-sm border border-bottom-0">
            <li class="list-group-item px-2 border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex flex-column flex-lg-row gap-2">
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
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="shadow-sm rounded">
                <?php if (session()->get('role') != 'Admisi') : ?>
                    <div class="d-grid gap-2">
                        <button id="collapseList" class="btn btn-primary btn-sm bg-gradient shadow-sm rounded-bottom-0" type="button" data-bs-toggle="collapse" data-bs-target="#ButaWarnaFormContainer" aria-expanded="false" aria-controls="ButaWarnaFormContainer">
                            <i class="fa-solid fa-plus"></i> Tambah Surat
                        </button>
                    </div>
                    <ul id="ButaWarnaFormContainer" class="list-group rounded-0 collapse">
                        <li class="list-group-item border-top-0 bg-body-tertiary">
                            <div class="row g-3">
                                <form id="ButaWarnaForm" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                    <div class="flex-fill">
                                        <select class="form-select form-select-sm" id="nomor_registrasi" name="nomor_registrasi" aria-label="nomor_registrasi">
                                            <option value="" disabled selected>-- Pilih Pasien Rawat Jalan --</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                        <button type="submit" id="submitButton" class="btn btn-primary bg-gradient btn-sm" disabled>
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                    </ul>
                <?php endif; ?>
                <ul id="ButaWarnaContainer" class="list-group <?= (session()->get('role') != 'Admisi') ? 'rounded-top-0' : ''; ?>">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3" style="cursor: wait;">
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
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                                <?php if (session()->get('role') != 'Admisi') : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
                                <?php if (session()->get('role') == 'Admisi') : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
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
                            <span class="placeholder w-100" style="max-width: 100px;"></span>
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
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                        <?php if (session()->get('role') != 'Admisi') : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
                                <?php if (session()->get('role') == 'Admisi') : ?>
                                    <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
                                <?php if (session()->get('role') != 'Admisi') : ?>
                                    <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                <?php endif; ?>
                </div>
            </li>
    `;
    <?php if (session()->get('role') != 'Admisi') : ?>
        async function fetchPasienOptions() {
            try {
                const response = await axios.get('<?= base_url('butawarna/pasienlist') ?>');

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
    <?php endif; ?>

    async function fetchSurat() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const tanggal = $('#tanggalFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('butawarna/butawarnalist') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#ButaWarnaContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#ButaWarnaContainer').append(
                    '<li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3">' +
                    '    <h1 class="display-4 text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.butawarna.forEach(function(butawarna) {
                    let jenis_kelamin = butawarna.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                    }
                    const keperluan = butawarna.keperluan ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${butawarna.keperluan}">` :
                        `<em>Belum ada</em>`;
                    const status_buta_warna = butawarna.status_buta_warna ?
                        `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${butawarna.status_buta_warna}">` :
                        `<em>Belum ada</em>`;
                    const tanggalRegistrasi = new Date(butawarna.tanggal_registrasi);
                    const today = new Date();
                    const sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(today.getDate() - 6); // Termasuk hari ini

                    // Normalisasi hanya ke tanggal (tanpa jam) agar perbandingan akurat
                    tanggalRegistrasi.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);
                    sevenDaysAgo.setHours(0, 0, 0, 0);

                    const delete_today = (tanggalRegistrasi < sevenDaysAgo || tanggalRegistrasi > today) ? 'disabled' : '';

                    const ButaWarnaElement = `
                    <li class="list-group-item <?= (session()->get('role') != 'Admisi') ? 'border-top-0' : ''; ?> pb-3 pt-3">
                        <div class="d-flex">
                            <div class="align-self-center w-100">
                                <h5 class="card-title d-flex date justify-content-start">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${butawarna.number}</span>
                                    <span class="ms-1 align-self-center w-100"><input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${butawarna.nama_pasien}"></span>
                                </h5>
                                    <h6 class="card-subtitle mb-2">
                                        <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${butawarna.nomor_registrasi} • ${butawarna.no_rm}">${jenis_kelamin}
                                    </h6>
                                    <div class="card-text">
                                        <div style="font-size: 0.75em;">
                                                    <div class="mb-0 row g-1 align-items-center">
                                                        <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                                        <div class="col date">
                                                            <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${butawarna.waktu_dibuat}">
                                                        </div>
                                                    </div>
                                                    <div class="mb-0 row g-1 align-items-center">
                                                        <div class="col-5 fw-medium text-truncate">Keperluan</div>
                                                        <div class="col date">
                                                            ${keperluan}
                                                        </div>
                                                    </div>
                                                    <div class="mb-0 row g-1 align-items-center">
                                                        <div class="col-5 fw-medium text-truncate">Status Buta Warna</div>
                                                        <div class="col date">
                                                            ${status_buta_warna}
                                                        </div>
                                                    </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                        <?php if (session()->get('role') != 'Admisi') : ?>
                            <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('butawarna/details') ?>/${butawarna.id_keterangan_buta_warna}';">
                                <i class="fa-solid fa-circle-info"></i> Detail
                            </button>
                        <?php endif; ?>
                        <?php if (session()->get('role') == 'Admisi') : ?>
                            <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.open('<?= base_url('butawarna/export') ?>/${butawarna.id_keterangan_buta_warna}');">
                                <i class="fa-solid fa-print"></i> Cetak
                            </button>
                        <?php endif; ?>
                        <?php if (session()->get('role') != 'Admisi') : ?>
                            <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${butawarna.id_keterangan_buta_warna}" data-name="${butawarna.nama_pasien}" data-date="${butawarna.nomor_registrasi}" ${delete_today}>
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        <?php endif; ?>
                        </div>
                    </li>
                `;
                    $('#ButaWarnaContainer').append(ButaWarnaElement);
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
            $('#ButaWarnaContainer').empty();
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
            <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
            fetchSurat();
        }
    });

    $('#tanggalFilter').on('change', function() {
        $('#ButaWarnaContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#ButaWarnaContainer').append(placeholder);
        }
        <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
        fetchSurat();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#ButaWarnaContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#ButaWarnaContainer').append(placeholder);
        }
        <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
        fetchSurat();
    });
    $('#setTodayTglButton').on('click', async function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#tanggalFilter').val(formattedDate);
        $('#ButaWarnaContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#ButaWarnaContainer').append(placeholder);
        }
        <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
        fetchSurat();
    });

    <?php if (session()->get('role') != 'Admisi') : ?>

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
    <?php endif; ?>

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update || data.delete) {
                console.log("Received update from WebSocket");
                <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>;
                fetchSurat();
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

        <?php if (session()->get('role') != 'Admisi') : ?>
            $('#nomor_registrasi').select2({
                dropdownParent: $('#ButaWarnaForm'),
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
            });
        <?php endif; ?>

        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchSurat();
        });

        <?php if (session()->get('role') != 'Admisi') : ?>
            // Store the ID of the user to be deleted
            var ButaWarnaId;
            var ButaWarnaName;
            var ButaWarnaDate;

            // Show delete confirmation modal
            $(document).on('click', '.delete-btn', function() {
                ButaWarnaId = $(this).data('id');
                ButaWarnaName = $(this).data('name');
                ButaWarnaDate = $(this).data('date');
                // Check if ButaWarnaName is null or undefined
                const nama_pasien = (ButaWarnaName === null || ButaWarnaName === undefined || ButaWarnaName === 'null') ?
                    'yang anonim ini' :
                    `dari "${ButaWarnaName}"`;
                $('[data-bs-toggle="tooltip"]').tooltip('hide');
                $('#deleteMessage').html(`Hapus Surat untuk ${nama_pasien}?`);
                $('#deleteSubmessage').html(`Nomor Registrasi: ` + ButaWarnaDate);
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(async function() {
                $('#deleteModal button').prop('disabled', true);
                $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

                try {
                    await axios.delete(`<?= base_url('/butawarna/delete') ?>/${ButaWarnaId}`);
                    fetchPasienOptions();
                    fetchSurat();
                } catch (error) {
                    if (error.response.request.status === 404 || error.response.request.status === 422) {
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
        <?php endif; ?>

        $('#ButaWarnaForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#ButaWarnaForm .is-invalid').removeClass('is-invalid');
            $('#ButaWarnaForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Tambah
            `);

            // Disable form inputs
            $('#ButaWarnaForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('butawarna/create') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#ButaWarnaForm')[0].reset();
                    $('#nomor_registrasi').val(null).trigger('change');
                    $('#ButaWarnaForm .is-invalid').removeClass('is-invalid');
                    $('#ButaWarnaForm .invalid-feedback').text('').hide();
                    $('#submitButton').prop('disabled', true);
                    <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
                    fetchSurat();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#ButaWarnaForm .is-invalid').removeClass('is-invalid');
                    $('#ButaWarnaForm .invalid-feedback').text('').hide();

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
                $('#ButaWarnaForm select').prop('disabled', false);
            }
        });

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>;
                fetchSurat(); // Refresh articles on button click
            }
        });
        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>;
            fetchSurat(); // Refresh articles on button click
        });
        <?= (session()->get('role') != 'Admisi') ? 'fetchPasienOptions();' : ''; ?>
        fetchSurat();
        toggleSubmitButton();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>