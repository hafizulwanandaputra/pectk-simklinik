<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> laporan</div>
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
                    <div class="input-group input-group-sm mb-2">
                        <input type="date" id="tanggalFilter" class="form-control" <?= (session()->get('auto_date') == 1) ? 'value="' . date('Y-m-d') . '"' : ''; ?>>
                        <?php if (session()->get('auto_date') == 1) : ?>
                            <button class="btn btn-primary btn-sm bg-gradient" type="button" id="setTodayTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Kembali ke Hari Ini"><i class="fa-solid fa-calendar-day"></i></button>
                        <?php else : ?>
                            <button class="btn btn-danger btn-sm bg-gradient " type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        <?php endif; ?>
                    </div>
                    <div class="input-group input-group-sm">
                        <select id="apotekerFilter" class="form-select form-select-sm ">
                            <option value="">Semua Apoteker</option>
                        </select>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="shadow-sm rounded">
                <form id="opnameObatForm" enctype="multipart/form-data">
                    <div class="d-grid gap-2">
                        <button type="submit" id="addButton" class="btn btn-primary btn-sm bg-gradient  rounded-bottom-0">
                            <i class="fa-solid fa-plus"></i> Buat Laporan Baru
                        </button>
                    </div>
                </form>
                <ul id="opnameObatContainer" class="list-group rounded-top-0 ">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                            <div class="d-flex">
                                <div class="align-self-center w-100">
                                    <h5 class="card-title d-flex justify-content-start placeholder-glow">
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                                    </h5>
                                    <h6 class="card-subtitle placeholder-glow">
                                        <span class="placeholder" style="width: 100%;"></span>
                                    </h6>
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
    var placeholder = `
            <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex justify-content-start placeholder-glow">
                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                        </h5>
                        <h6 class="card-subtitle placeholder-glow">
                            <span class="placeholder" style="width: 100%;"></span>
                        </h6>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
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
            const response = await axios.get(`<?= base_url('opnameobat/apotekerlist') ?>`);

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
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan dokter.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchOpnameObat() {
        const tanggal = $('#tanggalFilter').val();
        const apoteker = $('#apotekerFilter').val();
        const offset = (currentPage - 1) * limit;

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('opnameobat/opnameobatlist') ?>', {
                params: {
                    tanggal: tanggal,
                    apoteker: apoteker,
                    limit: limit,
                    offset: offset,
                }
            });

            const data = response.data;
            $('#opnameObatContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#opnameObatContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.opname_obat.forEach(function(opname_obat) {
                    const sisa_stok = parseInt(opname_obat.sisa_stok);
                    const opnameObatElement = `
            <li class="list-group-item border-top-0 pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-between">
                            <div class="d-flex justify-content-start">
                                <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${opname_obat.number}</span>
                                <span class="mx-1 align-self-center">${opname_obat.tanggal}</span>
                            </div>
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${sisa_stok.toLocaleString('id-ID')}</span>
                        </h5>
                        <h6 class="card-subtitle">
                            ${opname_obat.apoteker}
                        </h6>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient " onclick="window.location.href = '<?= base_url('opnameobat/detailopnameobat') ?>/${opname_obat.id_opname_obat}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient  delete-btn" data-id="${opname_obat.id_opname_obat}" data-name="${opname_obat.apoteker}" data-date="${opname_obat.tanggal}">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </li>
                `;

                    $('#opnameObatContainer').append(opnameObatElement);
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
            $('#opnameObatContainer').empty();
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
            fetchOpnameObat();
        }
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
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('apotekerFilter').val();
                await fetchApotekerOptions(selectedApoteker);
                fetchOpnameObat();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('#tanggalFilter, #apotekerFilter').on('change', function() {
            // Simpan nilai pilihan apoteker saat ini
            const selectedApoteker = $('apotekerFilter').val();
            $('#opnameObatContainer').empty();
            for (let i = 0; i < limit; i++) {
                $('#opnameObatContainer').append(placeholder);
            }
            fetchApotekerOptions(selectedApoteker);
            fetchOpnameObat();
        });

        $('#clearTglButton').on('click', function() {
            $('#tanggalFilter').val('');
            $('#opnameObatContainer').empty();
            for (let i = 0; i < limit; i++) {
                $('#opnameObatContainer').append(placeholder);
            }
            fetchOpnameObat();
        });
        $('#setTodayTglButton').on('click', async function() {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            $('#tanggalFilter').val(formattedDate);
            $('#opnameObatContainer').empty();
            for (let i = 0; i < limit; i++) {
                $('#opnameObatContainer').append(placeholder);
            }
            fetchOpnameObat();
        });
        // Store the ID of the user to be deleted
        var OpnameObatId;
        var OpnameObatName;
        var OpnameObatDate;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            opnameObatId = $(this).data('id');
            opnameObatName = $(this).data('name');
            opnameObatDate = $(this).data('date');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus laporan obat ini ?`);
            $('#deleteSubmessage').html(`Tanggal: ` + opnameObatDate);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('apotekerFilter').val();
                await axios.delete(`<?= base_url('/opnameobat/delete') ?>/${opnameObatId}`);
                await fetchApotekerOptions(selectedApoteker);
                fetchOpnameObat();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        $('#opnameObatForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#opnameObatForm .is-invalid').removeClass('is-invalid');
            $('#opnameObatForm .invalid-feedback').text('').hide();
            $('#addButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Membuat Laporan Obat...
            `);

            // Disable form inputs
            $('#opnameObatForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('opnameobat/create') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    // Simpan nilai pilihan apoteker saat ini
                    const selectedApoteker = $('apotekerFilter').val();
                    $('#opnameObatForm .is-invalid').removeClass('is-invalid');
                    $('#opnameObatForm .invalid-feedback').text('').hide();
                    $('#addButton').prop('disabled', true);
                    await fetchApotekerOptions(selectedApoteker);
                    fetchOpnameObat();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#opnameObatForm .is-invalid').removeClass('is-invalid');
                    $('#opnameObatForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
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
            } catch (error) {
                if (error.response.request.status === 500 || error.response.request.status === 404) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#addButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Buat Laporan Baru
                `);
                $('#opnameObatForm select').prop('disabled', false);
            }
        });
        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('apotekerFilter').val();
                await fetchApotekerOptions(selectedApoteker);
                fetchOpnameObat(); // Refresh articles on button click
            }
        });
        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan apoteker saat ini
            const selectedApoteker = $('apotekerFilter').val();
            await fetchApotekerOptions(selectedApoteker);
            fetchOpnameObat(); // Refresh articles on button click
        });

        await fetchApotekerOptions();
        fetchOpnameObat();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>