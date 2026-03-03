<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="totalRecords">0</span> BMHP</div>
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
                            <select id="apotekerFilter" class="form-select">
                                <option value="">Semua Apoteker</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm flex-grow-1">
                            <select id="konfirmasiKasirFilter" class="form-select">
                                <option value="">Semua Status Konfirmasi</option>
                                <option value="1">Dikonfirmasi</option>
                                <option value="0">Tidak Dikonfirmasi</option>
                            </select>
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
                    <button class="btn btn-primary btn-sm bg-gradient shadow-sm rounded-bottom-0" type="button" id="addButton">
                        <i class="fa-solid fa-plus"></i> Tambah BMHP
                    </button>
                </div>
                <ul id="bmhpContainer" class="list-group rounded-top-0 ">
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
                                                <div class="col">
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
    <div class="modal modal-sheet p-4 py-md-5 fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <?= form_open_multipart('/bmhp/create', 'id="addForm"'); ?>
                <div class="modal-body p-4">
                    <h5 id="addMessage"></h5>
                    <h6 class="mb-0 fw-normal" id="addSubmessage"></h6>
                    <div class="row gy-2 pt-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmAddBtn">Tambah BMHP</button>
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
                                    <div class="col">
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

    async function fetchApotekerOptions(selectedApoteker = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('bmhp/apotekerlist') ?>`);

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

    async function fetchBMHP() {
        const search = $('#tanggalFilter').val();
        const offset = (currentPage - 1) * limit;
        const apoteker = $('#apotekerFilter').val();
        const konfirmasiKasir = $('#konfirmasiKasirFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('bmhp/listbmhp') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    apoteker: apoteker,
                    konfirmasiKasir: konfirmasiKasir,
                }
            });

            const data = response.data;
            $('#bmhpContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#bmhpContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-muted mb-0" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.bmhp.forEach(function(bmhp) {
                    const jumlah_bmhp = parseInt(bmhp.jumlah_bmhp);
                    const total_biaya = parseInt(bmhp.total_biaya);
                    const statusBadge1 = bmhp.status == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const statusBadge2 = bmhp.konfirmasi_kasir == '1' ?
                        `<span class="badge bg-success bg-gradient">Dikonfirmasi</span> ${statusBadge1}` :
                        `<span class="badge bg-secondary bg-gradient">Tidak Dikonfirmasi</span>`;
                    const statusButtons = bmhp.konfirmasi_kasir == '1' ? `disabled` : ``;
                    const bmhpElement = `
            <li class="list-group-item border-top-0 pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-start">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${bmhp.number}</span>
                            <span class="ms-1 align-self-center w-100">${bmhp.tanggal_bmhp}</span>
                        </h5>
                        <h6 class="card-subtitle mb-2">
                            <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 fw-medium" value="${bmhp.apoteker}">
                        </h6>
                        <div class="card-text">
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 fw-medium pe-none" value="Tanggal dan Waktu">
                                            </div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 date" value="${bmhp.tanggal_bmhp}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 fw-medium pe-none" value="Total Barang">
                                            </div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 date" value="${jumlah_bmhp.toLocaleString('id-ID')}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 fw-medium pe-none" value="Total Harga">
                                            </div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 date" value="Rp${total_biaya.toLocaleString('id-ID')}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ${statusBadge2}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex flex-wrap justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient text-nowrap details-btn" onclick="window.location.href = '<?= base_url('bmhp/detailbmhp') ?>/${bmhp.id_bmhp}'">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient text-nowrap delete-btn" data-id="${bmhp.id_bmhp}" data-name="${bmhp.tanggal_bmhp}" ${statusButtons}>
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </li>
                `;

                    $('#bmhpContainer').append(bmhpElement);
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
            // if (error.response.request.status === 400) {
            //     showFailedToast(error.response.data.error);
            // } else {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            // }
            $('#bmhpContainer').empty();
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
            fetchBMHP();
        }
    });

    $('#apotekerFilter, #konfirmasiKasirFilter, #tanggalFilter').on('change', function() {
        $('#bmhpContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#bmhpContainer').append(placeholder);
        }
        fetchBMHP();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#bmhpContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#bmhpContainer').append(placeholder);
        }
        fetchBMHP();
    });
    $('#setTodayTglButton').on('click', async function() {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        $('#tanggalFilter').val(formattedDate);
        $('#bmhpContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#bmhpContainer').append(placeholder);
        }
        fetchBMHP();
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
                fetchBMHP();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchBMHP();
        });

        // Tampilkan modal tambah layanan
        $('#addButton').click(function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#addMessage').html(`Tambah Barang Medis Habis Pakai Baru?`);
            $('#addSubmessage').html(`Ini akan menambahkan barang medis habis pakai baru dan diarahkan ke halaman detail barang medis habis pakai baru ini.`);
            $('#addModal').modal('show');
        });

        $(document).on('click', '#confirmAddBtn', async function(ə) {
            ə.preventDefault();
            $('#addModal button').prop('disabled', true);
            $('#addForm').submit();
            $('#confirmAddBtn').html(`<?= $this->include('spinner/spinner'); ?> Menambahkan BMHP...`);
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
                await axios.delete(`<?= base_url('/bmhp/delete') ?>/${resepId}`);
                // Simpan nilai pilihan apoteker saat ini
                const selectedApoteker = $('#apotekerFilter').val();
                // Panggil fungsi untuk memperbarui opsi apoteker
                await fetchApotekerOptions(selectedApoteker);
                fetchBMHP();
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
                const selectedApoteker = $('#apotekerFilter').val();
                // Panggil fungsi untuk memperbarui opsi apoteker
                await fetchApotekerOptions(selectedApoteker);
                fetchBMHP(); // Refresh articles on button click
            }
        });

        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            // Simpan nilai pilihan apoteker saat ini
            const selectedApoteker = $('#apotekerFilter').val();
            // Panggil fungsi untuk memperbarui opsi apoteker
            await fetchApotekerOptions(selectedApoteker);
            fetchBMHP(); // Refresh articles on button click
        });

        await fetchApotekerOptions();
        fetchBMHP();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>