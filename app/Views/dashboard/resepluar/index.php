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
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <div class="d-flex flex-column flex-lg-row mb-1 gap-2 mb-3">
        <select id="statusFilter" class="form-select form-select-sm w-auto rounded-3">
            <option value="">Semua Status Proses</option>
            <option value="1">Diproses</option>
            <option value="0">Belum Diproses</option>
        </select>
        <select id="namesFilter" class="form-select form-select-sm w-auto rounded-3">
            <option value="">Semua Nama</option>
            <option value="1">Dengan Nama</option>
            <option value="0">Anonim</option>
        </select>
        <div class="input-group input-group-sm flex-fill">
            <input type="search" id="searchInput" class="form-control rounded-start-3" placeholder="Cari pasien dan tanggal resep...">
            <button class="btn btn-success btn-sm bg-gradient" type="button" id="refreshButton"><i class="fa-solid fa-sync"></i></button>
            <button class="btn btn-primary btn-sm bg-gradient rounded-end-3" type="button" id="addButton"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
    </div>
    <ul id="resepContainer" class="list-group shadow-sm rounded-3 mt-1">
        <?php for ($i = 0; $i < 12; $i++) : ?>

            <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center ps-2 w-100">
                        <h5 class="card-title placeholder-glow">
                            <span class="placeholder" style="width: 100%"></span>
                        </h5>
                        <p class="card-text placeholder-glow">
                            <small>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span>
                            </small>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                </div>
            </li>
        <?php endfor; ?>
    </ul>
    <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
        <ul class="pagination pagination-sm" style="--bs-pagination-border-radius: var(--bs-border-radius-lg);"></ul>
    </nav>
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
    <div class="modal fade" id="resepluarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="resepluarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="resepluarForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="resepluarModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_resep" name="id_resep">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_pasien" id="nama_pasien" name="nama_pasien">
                        <label for="nama_pasien">Nama Pasien</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mt-1 mb-1 row">
                        <label for="jenis_kelamin" class="col-3 col-form-label">Jenis Kelamin*</label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-start">
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
                        <input type="date" class="form-control" autocomplete="off" dir="auto" placeholder="tanggal_lahir" id="tanggal_lahir" name="tanggal_lahir">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="alamat" id="alamat" name="alamat">
                        <label for="alamat_pasien">Alamat</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient rounded-3">
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
            <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center ps-2 w-100">
                        <h5 class="card-title placeholder-glow">
                            <span class="placeholder" style="width: 100%"></span>
                        </h5>
                        <p class="card-text placeholder-glow">
                            <small>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span><br>
                                <span class="placeholder" style="width: 12.5%;"></span>
                            </small>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                </div>
            </li>
    `;
    async function fetchResep() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = $('#statusFilter').val();
        const names = $('#namesFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resepluar/listresep') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                    status: status,
                    names: names
                }
            });

            const data = response.data;
            $('#resepContainer').empty();
            $('#totalRecords').text(`(${data.total})`);

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#resepContainer').append(
                    '<li class="list-group-item bg-body-tertiary pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.resep.forEach(function(resep) {
                    const nama_pasien = resep.nama_pasien == null ?
                        `<em>Anonim</em>` :
                        resep.nama_pasien;
                    const jumlah_resep = parseInt(resep.jumlah_resep);
                    const total_biaya = parseInt(resep.total_biaya);
                    const statusBadge = resep.status == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    const statusButtons = resep.status == '1' ?
                        `disabled` :
                        ``;
                    const resepElement = `
            <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center ps-2 w-100">
                        <h5 class="card-title">
                            ${nama_pasien}
                        </h5>
                        <p class="card-text">
                            <small class="date">
                                ID Resep: ${resep.id_resep}<br>
                                Tanggal dan Waktu Resep: ${resep.tanggal_resep}<br>
                                Total Resep: ${jumlah_resep.toLocaleString('id-ID')}<br>
                                Total Harga: Rp${total_biaya.toLocaleString('id-ID')}<br>
                                ${statusBadge}
                            </small>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient rounded-3" onclick="window.location.href = '<?= base_url('resepluar/detailresep') ?>/${resep.id_resep}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-body btn-sm bg-gradient rounded-3 edit-btn" data-id="${resep.id_resep}">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Identitas
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient rounded-3 delete-btn" data-id="${resep.id_resep}" data-name="${resep.nama_pasien}" data-date="${resep.tanggal_resep}">
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
                        <a class="page-link bg-gradient date" href="#" data-page="1">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                }

                if (totalPages > 3) {
                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                    if (currentPage > 2) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">...</span></li>');
                    }

                    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }

                    if (currentPage < totalPages - 1) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">...</span></li>');
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
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">
                            <i class="fa-solid fa-angles-right"></i>
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

    $('#statusFilter, #namesFilter').on('change', function() {
        $('#resepContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#resepContainer').append(placeholder);
        }
        fetchResep();
    });

    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            currentPage = 1;
            fetchResep();
        });

        // Tampilkan modal tambah layanan
        $('#addButton').click(function() {
            $('#resepluarModalLabel').text('Tambah Resep Luar'); // Ubah judul modal menjadi 'Tambah Resep Luar'
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
                <span class="spinner-border" style="width: 15px; height: 15px;" aria-hidden="true"></span> Edit Identitas
            `); // Ubah tombol menjadi indikator loading

            try {
                const response = await axios.get(`<?= base_url('/resepluar/resep') ?>/${id}`); // Ambil data resep luar berdasarkan ID
                $('#resepluarModalLabel').text('Edit Identitas Pasien'); // Ubah judul modal menjadi 'Edit Identitas Pasien'
                $('#id_resep').val(response.data.id_resep);
                $('#nama_pasien').val(response.data.nama_pasien);
                $('#alamat').val(response.data.alamat);
                const selectedGender = response.data.jenis_kelamin;
                if (selectedGender) {
                    $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
                }
                $('#tanggal_lahir').val(response.data.tanggal_lahir);
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
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/resepluar/delete') ?>/${resepId}`);
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

        $('#resepluarForm').submit(async function(e) {
            e.preventDefault();

            const url = $('#id_resep').val() ? '<?= base_url('/resepluar/update') ?>' : '<?= base_url('/resepluar/create') ?>';
            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#resepluarForm .is-invalid').removeClass('is-invalid');
            $('#resepluarForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menambahkan
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

        $('#refreshButton').on('click', function() {
            $('#resepContainer').empty();
            for (let i = 0; i < limit; i++) {
                $('#resepContainer').append(placeholder);
            }
            fetchResep(); // Refresh articles on button click
        });

        // Reset form saat modal ditutup
        $('#resepluarModal').on('hidden.bs.modal', function() {
            $('#resepluarForm')[0].reset();
            $('#resepluarForm .is-invalid').removeClass('is-invalid');
            $('#resepluarForm .invalid-feedback').text('').hide();
        });

        fetchResep();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>