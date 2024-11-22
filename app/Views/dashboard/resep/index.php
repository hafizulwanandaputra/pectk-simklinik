<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?> <span id="totalRecords" class="date"></span></span></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0 mb-2">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="d-xxl-flex justify-content-center">
                    <div class="no-fluid-content">
                        <div class="d-flex flex-column flex-lg-row mb-1 gap-2 mb-2">
                            <div class="input-group input-group-sm">
                                <input type="date" id="tanggalFilter" class="form-control rounded-start-3">
                                <button class="btn btn-danger btn-sm bg-gradient rounded-end-3" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="input-group input-group-sm flex-fill">
                                <input type="search" id="searchInput" class="form-control rounded-start-3" placeholder="Cari pasien">
                                <button class="btn btn-success btn-sm bg-gradient rounded-end-3" type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></button>
                            </div>
                        </div>
                        <div class="accordion" id="accordionFilter" style="--bs-accordion-border-radius: var(--bs-border-radius-lg); --bs-accordion-inner-border-radius: calc(var(--bs-border-radius-lg) - (var(--bs-border-width)));">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                        Pencarian Tambahan
                                    </button>
                                </h2>
                                <div id="collapseFilter" class="accordion-collapse collapse" data-bs-parent="#accordionFilter">
                                    <div class="accordion-body px-2 py-1">
                                        <div class="d-flex flex-column flex-lg-row mb-1 gap-1 my-1">
                                            <select id="statusFilter" class="form-select form-select-sm w-auto rounded-3 flex-fill">
                                                <option value="">Semua Status Transaksi</option>
                                                <option value="1">Diproses</option>
                                                <option value="0">Belum Diproses</option>
                                            </select>
                                            <select id="confirmedFilter" class="form-select form-select-sm w-auto rounded-3 flex-fill">
                                                <option value="">Semua Status Konfirmasi</option>
                                                <option value="1">Dikonfirmasi</option>
                                                <option value="0">Belum Dikonfirmasi</option>
                                            </select>
                                            <select id="genderFilter" class="form-select form-select-sm w-auto rounded-3 flex-fill">
                                                <option value="">Semua Jenis Kelamin</option>
                                                <option value="L">Laki-Laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                        </div>
                                        <select id="dokterFilter" class="form-select form-select-sm rounded-3 my-1">
                                            <option value="">Semua Dokter</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3">
        <div class="d-xxl-flex justify-content-center">
            <div class="no-fluid-content">
                <div class="shadow-sm rounded-3">
                    <?php if (session()->get('role') != 'Apoteker') : ?>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm bg-gradient rounded-top-3 rounded-bottom-0" type="button" data-bs-toggle="collapse" data-bs-target="#tambahPasienForm" aria-expanded="false" aria-controls="tambahPasienForm">
                                <i class="fa-solid fa-plus"></i> Tambah Resep Dokter
                            </button>
                        </div>
                        <ul id="tambahPasienForm" class="list-group rounded-0 collapse">
                            <li class="list-group-item border-top-0 border-bottom-0 bg-body-tertiary">
                                <form id="resepForm" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                    <div class="flex-fill">
                                        <select class="form-select rounded-3" id="nomor_registrasi" name="nomor_registrasi" aria-label="nomor_registrasi">
                                            <option value="" disabled selected>-- Pilih Pasien --</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end" id="submitButtonContainer">
                                        <button type="submit" id="submitButton" class="btn btn-primary bg-gradient rounded-3" disabled>
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    <?php endif; ?>
                    <ul id="resepContainer" class="list-group <?= (session()->get('role') != 'Apoteker') ? 'rounded-top-0 rounded-bottom-3' : 'rounded-3'; ?>">
                        <?php for ($i = 0; $i < 12; $i++) : ?>
                            <li class="list-group-item <?= (session()->get('role') != 'Apoteker') ? 'border-top-0' : ''; ?> bg-body-tertiary pb-3 pt-3" style="cursor: wait;">
                                <div class="d-flex">
                                    <div class="align-self-center w-100">
                                        <h5 class="card-title placeholder-glow">
                                            <span class="placeholder" style="width: 100%"></span>
                                        </h5>
                                        <h6 class="card-subtitle mb-2 placeholder-glow">
                                            <span class="placeholder" style="width: 25%;"></span><br>
                                            <span class="placeholder" style="width: 12.5%;"></span>
                                        </h6>
                                        <p class="card-text placeholder-glow">
                                            <small>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                                <span class="placeholder" style="width: 9%;"></span><br>
                                                <span class="placeholder" style="width: 9%;"></span>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-grid gap-2 d-flex justify-content-end">
                                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                    <?php if (session()->get('role') != 'Apoteker'): ?>
                                        <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
                <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                    <ul class="pagination pagination-sm" style="--bs-pagination-border-radius: var(--bs-border-radius-lg);"></ul>
                </nav>
            </div>
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
    let pembelianObatId = null;
    var placeholder = `
            <li class="list-group-item <?= (session()->get('role') != 'Apoteker') ? 'border-top-0' : ''; ?> bg-body-tertiary pb-3 pt-3" style="cursor: wait;">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title placeholder-glow">
                            <span class="placeholder" style="width: 100%"></span>
                        </h5>
                        <h6 class="card-subtitle mb-2 placeholder-glow">
                            <span class="placeholder" style="width: 25%;"></span><br>
                            <span class="placeholder" style="width: 12.5%;"></span>
                        </h6>
                        <p class="card-text placeholder-glow">
                            <small>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 9%;"></span><br>
                                <span class="placeholder" style="width: 9%;"></span>
                            </small>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <?php if (session()->get('role') != 'Apoteker'): ?>
                        <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <?php endif; ?>
                </div>
            </li>
    `;
    <?php if (session()->get('role') != 'Apoteker') : ?>
        async function fetchPasienOptions() {
            try {
                // Panggil API dengan query string tanggal
                const response = await axios.get(`<?= base_url('resep/pasienlist') ?>`);

                if (response.data.success) {
                    const options = response.data.data;
                    const select = $('#nomor_registrasi');

                    // Hapus opsi yang ada, kecuali opsi pertama (default)
                    select.find('option:not(:first)').remove();

                    // Urutkan opsi berdasarkan 'value' secara ascending
                    options.sort((a, b) => b.value.localeCompare(a.value, 'en', {
                        numeric: true
                    }));

                    // Tambahkan opsi ke elemen select
                    options.forEach(option => {
                        select.append(`<option value="${option.value}">${option.text}</option>`);
                    });
                } else {
                    showFailedToast('Gagal mendapatkan pasien.');
                }
            } catch (error) {
                showFailedToast('Gagal mendapatkan pasien.<br>' + error);
            }
        }
    <?php endif; ?>
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
                    gender: gender,
                    dokter: dokter,
                    confirmed: confirmed,
                    tanggal: tanggal
                }
            });

            const data = response.data;
            $('#resepContainer').empty();
            $('#totalRecords').text(`(${data.total.toLocaleString('id-ID')})`);

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#resepContainer').append(
                    '<li class="list-group-item bg-body-tertiary pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted" style="font-weight: 200;">Data Kosong</h1>' +
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
                    const confirmedBadge = resep.confirmed == '1' ?
                        `<span class="badge bg-success bg-gradient">Dikonfirmasi</span>` :
                        `<span class="badge bg-danger bg-gradient">Belum Dikonfirmasi</span>`;
                    const statusBadge = resep.status == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const statusButtons = resep.status == '1' ?
                        `disabled` :
                        ``;
                    const deleteButton = `<button type="button" class="btn btn-danger btn-sm bg-gradient rounded-3 delete-btn" data-id="${resep.id_resep}" data-name="${resep.nama_pasien}" data-date="${resep.tanggal_resep}">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>`;
                    const resepElement = `
            <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title">
                            [<span class="date" style="font-weight: 900;">${resep.number}</span>] ${resep.nama_pasien}
                        </h5>
                        <h6 class="card-subtitle mb-2">
                            ${resep.dokter}<br>${jenis_kelamin}
                        </h6>
                        <p class="card-text">
                            <small class="date">
                                ID Resep: ${resep.id_resep}<br>
                                Tanggal dan Waktu Resep: ${resep.tanggal_resep}<br>
                                Nomor Rekam Medis: ${resep.no_rm}<br>
                                Nomor Registrasi: ${resep.nomor_registrasi}<br>
                                Total Resep: ${jumlah_resep.toLocaleString('id-ID')}<br>
                                Total Harga: Rp${total_biaya.toLocaleString('id-ID')}<br>
                                ${confirmedBadge}<br>${statusBadge}
                            </small>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient rounded-3" onclick="window.location.href = '<?= base_url('resep/detailresep') ?>/${resep.id_resep}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <?= (session()->get('role') == 'Apoteker') ? '' : '${deleteButton}' ?>
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
            <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
            fetchResep();
        }
    });

    $('#statusFilter, #genderFilter, #confirmedFilter, #dokterFilter, #tanggalFilter').on('change', function() {
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
        fetchResep();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggalFilter').val('');
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
        fetchResep();
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
        $('#nomor_registrasi').select2({
            dropdownParent: $('#resepForm'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
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
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/resep/delete') ?>/${resepId}`);
                // Simpan nilai pilihan dokter saat ini
                const selectedDokter = $('#dokterFilter').val();
                <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
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
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#resepForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#resepForm .is-invalid').removeClass('is-invalid');
            $('#resepForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#resepForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('resep/create') ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#nomor_registrasi').val(null).trigger('change');
                    $('#resepForm .is-invalid').removeClass('is-invalid');
                    $('#resepForm .invalid-feedback').text('').hide();
                    $('#submitButton').prop('disabled', true);
                    // Simpan nilai pilihan dokter saat ini
                    const selectedDokter = $('#dokterFilter').val();
                    <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
                    // Panggil fungsi untuk memperbarui opsi dokter
                    await fetchDokterOptions(selectedDokter);
                    fetchResep();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#resepForm .is-invalid').removeClass('is-invalid');
                    $('#resepForm .invalid-feedback').text('').hide();

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
                $('#submitButton').prop('disabled', false);
            } finally {
                $('#submitButton').html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#resepForm select').prop('disabled', false);
            }
        });
        $('#refreshButton').on('click', async function() {
            // Simpan nilai pilihan dokter saat ini
            const selectedDokter = $('#dokterFilter').val();
            <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
            // Panggil fungsi untuk memperbarui opsi dokter
            await fetchDokterOptions(selectedDokter);
            fetchResep();
        });

        <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
        await fetchDokterOptions();
        fetchResep();
        toggleSubmitButton();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>