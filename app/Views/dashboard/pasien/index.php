<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<style>
    .second-row-form {
        min-width: 15em;
    }

    @media (max-width: 767.98px) {
        .second-row-form {
            min-width: 0;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> pasien</div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <a id="exportButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Ekspor Excel"><i class="fa-solid fa-file-excel"></i></a>
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
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="input-group input-group-sm flex-grow-1">
                            <input type="search" id="searchInput" class="form-control" placeholder="Cari nomor rekam medis, nama pasien, NIK, atau nomor BPJS">
                        </div>
                        <div class="input-group input-group-sm w-auto second-row-form">
                            <input type="date" id="tanggalFilter" class="form-control rounded-start" placeholder="Tanggal lahir (dd-mm-yyyy)">
                            <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
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
                        <i class="fa-solid fa-plus"></i> Tambah Pasien
                    </button>
                </div>
                <div id="pasienContainer" class="list-group rounded-top-0 ">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <span class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title d-flex justify-content-start placeholder-glow">
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                                    </h5>
                                    <div style="font-size: 0.75em;">
                                        <div class="row gx-3">
                                            <div class="col-lg-6">
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                                <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                    <div class="col-5 fw-medium text-truncate">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                    <div class="col placeholder-glow">
                                                        <span class="placeholder w-100"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="placeholder-glow">
                                            <span class="placeholder w-100"></span>
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                                        <button type="button" class="btn btn-body btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                        <button type="button" class="btn btn-danger btn-sm bg-gradient placeholder" style="width: 4em;" disabled aria-disabled="true"></button>
                                    </div>
                                </div>
                            </div>
                        </span>
                    <?php endfor; ?>
                </div>
            </div>
            <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                <ul class="pagination pagination-sm"></ul>
            </nav>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <?= form_open_multipart('/pasien/create', 'id="addForm"'); ?>
                <div class="modal-body p-4">
                    <h5 id="addMessage"></h5>
                    <h6 class="mb-0 fw-normal" id="addSubmessage"></h6>
                    <div class="row gy-2 pt-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmAddBtn">Tambah Pasien</button>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="deleteMessage"></h5>
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
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let limit = 12;
    let currentPage = 1;
    let pembelianObatId = null;

    async function fetchPasien() {
        const search = $('#searchInput').val();
        const tanggal_lahir = $('#tanggalFilter').val();
        const offset = (currentPage - 1) * limit;

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            $('#tanggalFilter').flatpickr({
                altInput: true,
                allowInput: true,
                altFormat: "d-m-Y",
                disableMobile: "true"
            });
            const response = await axios.get('<?= base_url('pasien/pasienlist') ?>', {
                params: {
                    search: search,
                    tanggal_lahir: tanggal_lahir,
                    limit: limit,
                    offset: offset
                }
            });

            const data = response.data;
            $('#pasienContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#pasienContainer').append(
                    '<li class="list-group-item border-top-0 bg-body-tertiary pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.pasien.forEach(function(pasien) {
                    const nama_pasien_header = pasien.nama_pasien ? pasien.nama_pasien : "<em>Belum Diisi</em>";
                    const nama_pasien = pasien.nama_pasien ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${pasien.nama_pasien}">` : `<em>Belum diisi</em>`;
                    let jenis_kelamin = pasien.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="Laki-Laki">`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="Perempuan">`;
                    } else {
                        jenis_kelamin = `<em>Tidak ada</em>`;
                    }
                    const nik = pasien.nik ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${pasien.nik}">` : `<em>Tidak ada</em>`;
                    const no_bpjs = pasien.no_bpjs ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${pasien.no_bpjs}">` : `<em>Tidak ada</em>`;
                    const tempat_lahir = pasien.tempat_lahir ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${pasien.tempat_lahir}">` : `<em>Tidak ada</em>`;
                    const tanggal_lahir = pasien.tanggal_lahir ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${pasien.tanggal_lahir}">` : `<em>Tidak ada</em>`;
                    const alamat = pasien.alamat ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${pasien.alamat}">` : `<em>Tidak ada</em>`;
                    const telpon = pasien.telpon ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${pasien.telpon}">` : `<em>Tidak ada</em>`;
                    let jumlah_rawat_jalan = pasien.jumlah_rawat_jalan;
                    if (jumlah_rawat_jalan === 0) {
                        jumlah_rawat_jalan = `Tidak ada rawat jalan`;
                    } else {
                        jumlah_rawat_jalan = `${jumlah_rawat_jalan.toLocaleString('id-ID')} rawat jalan`;
                    }
                    const delete_status = pasien.jumlah_rawat_jalan_daftar > 0 ? `disabled` : ``;
                    const pasienElement = `
                <span class="list-group-item border-top-0 pb-3 pt-3">
                    <div class="d-flex">
                        <div class="align-self-center w-100">
                            <h5 class="card-title d-flex date justify-content-between">
                                <div class="d-flex justify-content-start text-truncate">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${pasien.number}</span>
                                    <span class="mx-1 align-self-center text-truncate">${nama_pasien_header}</span>
                                </div>
                                <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${pasien.no_rm}</span>
                            </h5>
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Nama</div>
                                            <div class="col">
                                                ${nama_pasien}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Nomor Induk Kependudukan</div>
                                            <div class="col date">
                                                ${nik}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Nomor BPJS</div>
                                            <div class="col date">
                                                ${no_bpjs}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                                            <div class="col">
                                                ${jenis_kelamin}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Tempat Lahir</div>
                                            <div class="col">
                                                ${tempat_lahir}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Tanggal Lahir</div>
                                            <div class="col">
                                                ${tanggal_lahir}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Alamat</div>
                                            <div class="col">
                                                ${alamat}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                                            <div class="col date">
                                                ${telpon}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fw-bold">
                                    <span class="me-1"><i class="fa-solid fa-check"></i></span><span class="date">${pasien.jumlah_rawat_jalan_daftar.toLocaleString('id-ID')}</span><span class="mx-1"></span><span class="me-1"><i class="fa-solid fa-xmark"></i></span><span class="date">${pasien.jumlah_rawat_jalan_batal.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="fw-bold">
                                    <span class="date">${jumlah_rawat_jalan}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                                <button type="button" class="btn btn-body btn-sm bg-gradient text-nowrap details-btn" onclick="window.location.href = '<?= base_url('pasien/detailpasien') ?>/${pasien.id_pasien}'">
                                    <i class="fa-solid fa-circle-info"></i> Detail
                                </button>
                                <button type="button" class="btn btn-danger btn-sm bg-gradient text-nowrap delete-btn" data-id="${pasien.id_pasien}" data-name="${pasien.no_rm}" ${delete_status}>
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </div>        
                        </div>
                    </div>
                </span>
                `;

                    $('#pasienContainer').append(pasienElement);
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
            $('#pasienContainer').empty();
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
            fetchPasien();
        }
    });

    $(document).ready(function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update) {
                console.log("Received update from WebSocket");
                fetchPasien();
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
            fetchPasien();
        });

        $('#tanggalFilter').on('change', function() {
            currentPage = 1;
            fetchPasien();
        });

        $('#clearTglButton').on('click', function() {
            currentPage = 1;
            if ($('#tanggalFilter')[0]._flatpickr) {
                $('#tanggalFilter')[0]._flatpickr.clear();
            }
            fetchPasien();
        });

        $('#exportButton').on('click', async function(ə) {
            ə.preventDefault();
            $(this).hide();
            $('#searchInput, #addButton, #tanggalFilter, #clearTglButton').prop('disabled', true);
            const fp = $('#tanggalFilter')[0]._flatpickr;

            if (fp) {
                // Nonaktifkan altInput (untuk desktop)
                if (fp.altInput) {
                    fp.altInput.disabled = true;
                    fp.altInput.setAttribute('readonly', true);
                    fp.altInput.style.pointerEvents = 'none';
                    fp.altInput.style.touchAction = 'none';
                }

                // Nonaktifkan mobile input (untuk perangkat mobile)
                const mobileInput = fp._input.parentElement.querySelector('.flatpickr-mobile');
                if (mobileInput) {
                    mobileInput.disabled = true;
                    mobileInput.setAttribute('readonly', true);
                    mobileInput.style.pointerEvents = 'none';
                    mobileInput.style.touchAction = 'none';
                }

                // Mencegah datepicker muncul saat diklik
                fp.set('clickOpens', false);
            }
            $('#loadingSpinner').show(); // Menampilkan spinner

            // Membuat toast ekspor berjalan
            const toast = $(`
        <div id="exportToast" class="toast show bg-body-tertiary transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-truncate me-1">
                        <strong id="statusHeader">Menunggu respons peladen...</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="date" id="exportPercent">0%</span>
                        <button type="button" class="btn-close p-0 ms-1" aria-label="Close" id="cancelExport" style="height: 1rem; width: 1rem;"></button>
                    </div>
                </div>
                <div class="progress mb-1" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                    <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient bg-primary" role="progressbar" style="width: 0%; transition: none"></div>
                </div>
                <div style="font-size: 0.75em;">
                    <span class="date" id="loadedKB">0 B</span> dari <span class="date" id="totalKB">0 B</span> diunduh<br>
                    <span class="date" id="eta">Menunggu respons peladen...</span>
                </div>
            </div>
        </div>
    `);

            $('#toastContainer').append(toast);

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Menangani pembatalan ekspor
            $(document).on('click', '#cancelExport', function() {
                source.cancel('Ekspor dibatalkan');
            });

            // Event listener untuk menangani sebelum halaman di-unload
            $(window).on('beforeunload', function() {
                source.cancel('Ekspor dibatalkan');
                // Memberi jeda sebelum menyembunyikan loading spinner
                setTimeout(function() {
                    $('#loadingSpinner').show();
                }, 300); // jeda 300 milidetik (0.3 detik)
            });

            try {
                // Fungsi untuk mengubah byte ke satuan otomatis
                function formatBytes(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    const value = bytes / Math.pow(k, i);
                    return `${value.toLocaleString('id-ID', { maximumFractionDigits: 2 })} ${sizes[i]}`;
                }

                // Fungsi untuk memformat ETA dengan jam/menit/detik
                function formatETA(seconds) {
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const secs = Math.floor(seconds % 60);

                    let parts = [];
                    if (hours > 0) parts.push(`${hours} jam`);
                    if (minutes > 0) parts.push(`${minutes} menit`);
                    if (secs > 0 || parts.length === 0) parts.push(`${secs} detik`);

                    return parts.join(' ');
                }

                let startTime = null;
                let loadedBytes = 0;
                let totalBytes = 0;
                let speedBps = 0;
                let etaTimer = null;

                // Fungsi untuk memperbarui ETA setiap detik
                function updateETA() {
                    if (speedBps > 0) {
                        const remainingBytes = totalBytes - loadedBytes;
                        const estimatedTimeInSeconds = remainingBytes / speedBps;

                        const etaFormatted = formatETA(estimatedTimeInSeconds);
                        $('#eta').text(`Selesai dalam ${etaFormatted}`);
                    }
                }

                startTime = Date.now(); // Waktu mulai unduhan

                // Mulai interval ETA
                etaTimer = setInterval(updateETA, 1000);

                // Mulai unduhan file
                const response = await axios.get(`<?= base_url('pasien/exportexcel') ?>`, {
                    responseType: 'blob', // Mendapatkan data sebagai blob
                    onDownloadProgress: function(progressEvent) {
                        if (progressEvent.lengthComputable) {
                            loadedBytes = progressEvent.loaded;
                            totalBytes = progressEvent.total;

                            const percentComplete = Math.round((loadedBytes / totalBytes) * 100);
                            const elapsedTimeInSeconds = (Date.now() - startTime) / 1000;
                            speedBps = elapsedTimeInSeconds > 0 ? (loadedBytes / elapsedTimeInSeconds) : 0;

                            // Update tampilan progress
                            $('#exportPercent').text(`${percentComplete}%`);
                            $('#exportProgressBar').css('width', `${percentComplete}%`);
                            $('#statusHeader').text(`Mengunduh...`);
                            $('#loadedKB').text(formatBytes(loadedBytes));
                            $('#totalKB').text(formatBytes(totalBytes));

                            // Jika selesai
                            if (loadedBytes >= totalBytes) {
                                clearInterval(etaTimer); // Hentikan ETA timer
                                $('#eta').text('Selesai');
                                $('#statusHeader').text('Unduhan selesai');
                            }
                        }
                    },
                    cancelToken: source.token
                });

                // Memastikan progress 100% setelah selesai
                $('#exportPercent').text('100%');
                $('#exportProgressBar').css('width', '100%');

                // Mendapatkan nama file dari header Content-Disposition
                const disposition = response.headers['content-disposition'];
                const filename = disposition ? disposition.split('filename=')[1].split(';')[0].replace(/"/g, '') : 'export.xlsx';

                // Membuat URL unduhan
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();

                window.URL.revokeObjectURL(url); // Membebaskan URL yang dibuat

                // Hapus #exportToast dan ganti dengan sukses
                $('#exportToast').fadeOut(300, function() {
                    $('#exportToast').remove();
                    showSuccessToast('Berhasil diekspor');
                });
            } catch (error) {
                // Hapus #exportToast dan ganti dengan gagal
                $('#exportToast').fadeOut(300, function() {
                    $(this).remove();
                    if (axios.isCancel(error)) {
                        showFailedToast(error.message); // Pesan pembatalan ekspor
                    } else {
                        showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                    }
                });
            } finally {
                $('#loadingSpinner').hide(); // Menyembunyikan spinner setelah unduhan selesai
                $(this).show();
                $('#searchInput, #addButton, #tanggalFilter, #clearTglButton').prop('disabled', false);
                if (fp) {
                    // Aktifkan kembali altInput (untuk desktop)
                    if (fp.altInput) {
                        fp.altInput.disabled = false;
                        fp.altInput.removeAttribute('readonly');
                        fp.altInput.style.pointerEvents = '';
                        fp.altInput.style.touchAction = '';
                    }

                    // Aktifkan kembali mobile input (untuk perangkat mobile)
                    const mobileInput = fp._input.parentElement.querySelector('.flatpickr-mobile');
                    if (mobileInput) {
                        mobileInput.disabled = false;
                        mobileInput.removeAttribute('readonly');
                        mobileInput.style.pointerEvents = '';
                        mobileInput.style.touchAction = '';
                    }

                    // Izinkan datepicker dibuka kembali
                    fp.set('clickOpens', true);
                }
            }
        });

        $('#addButton').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#addMessage').html(`Tambah Pasien Baru?`);
            $('#addSubmessage').html(`Pastikan pasien tersebut benar-benar berobat dan membawa kartu identitas yang diperlukan. Ini akan menambahkan nomor rekam medis baru.`);
            $('#addModal').modal('show');
        });

        $(document).on('click', '#confirmAddBtn', async function(ə) {
            ə.preventDefault();
            $('#addModal button').prop('disabled', true);
            $('#confirmAddBtn').html(`<?= $this->include('spinner/spinner'); ?> Memeriksa Data Pasien Kosong...`);
            try {
                const response = await axios.post('<?= base_url('pasien/cekkososng') ?>');
                if (response.data.cekkosong === false) {
                    $('#addForm').submit();
                    $('#confirmAddBtn').html(`<?= $this->include('spinner/spinner'); ?> Menambahkan Pasien...`);
                } else if (response.data.cekkosong === true) {
                    // ambil array no_rm
                    const daftarNoRM = response.data.no_rm;

                    // buat <ul> dari array
                    let ulHtml = '<ul class="mb-0">';
                    daftarNoRM.forEach(noRM => {
                        ulHtml += `<li>${noRM}</li>`;
                    });
                    ulHtml += '</ul>';

                    // tampilkan di toast atau modal
                    showFailedToast(`${response.data.message}${ulHtml}`);
                    $('#addModal button').prop('disabled', false);
                    $('#confirmAddBtn').html('Tambah Pasien');
                    $('#addModal').modal('hide');
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                $('#addModal button').prop('disabled', false);
                $('#confirmAddBtn').html('Tambah Pasien');
                $('#addModal').modal('hide');
            }
        });

        var id_pasien;
        var no_rm;

        $(document).on('click', '.delete-btn', function() {
            id_pasien = $(this).data('id');
            no_rm = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus pasien ${no_rm}?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                const response = await axios.delete(`<?= base_url('/pasien/delete') ?>/${id_pasien}`);
                if (response.data.success === true) {
                    showSuccessToast(response.data.message);
                    fetchPasien();
                } else {
                    showFailedToast(response.data.message);
                }
            } catch (error) {
                if (error.response.request.status === 404) {
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

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchPasien();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function(ə) {
            ə.preventDefault();
            fetchPasien(); // Panggil fungsi untuk mengambil data pasien
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        fetchPasien();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>