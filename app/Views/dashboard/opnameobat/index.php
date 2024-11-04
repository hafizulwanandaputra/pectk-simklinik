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
        <div class="input-group input-group-sm flex-fill">
            <input type="search" id="searchInput" class="form-control rounded-start-3" placeholder="Cari tanggal dan apoteker...">
            <button class="btn btn-success btn-sm bg-gradient rounded-end-3" type="button" id="refreshButton"><i class="fa-solid fa-sync"></i></button>
        </div>
    </div>
    <div class="shadow-sm rounded-3">
        <form id="opnameObatForm" enctype="multipart/form-data">
            <div class="d-grid gap-2">
                <button type="submit" id="addButton" class="btn btn-primary bg-gradient rounded-top-3 rounded-bottom-0">
                    <i class="fa-solid fa-plus"></i> Buat Laporan Baru
                </button>
            </div>
        </form>
        <ul id="opnameObatContainer" class="list-group rounded-top-0 rounded-bottom-3">
            <?php for ($i = 0; $i < 12; $i++) : ?>
                <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                    <div class="d-flex">
                        <div class="align-self-center ps-2 w-100">
                            <h5 class="card-title placeholder-glow">
                                <span class="placeholder" style="width: 100%"></span>
                            </h5>
                            <h6 class="card-subtitle mb-2 placeholder-glow">
                                <span class="placeholder" style="width: 25%;"></span>
                            </h6>
                        </div>
                    </div>
                    <hr>
                    <div class="d-grid gap-2 d-flex justify-content-end">
                        <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                        <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    </div>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
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
</main>
<?= $this->endSection(); ?>
<?= $this->section('datatable'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
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
                        <h6 class="card-subtitle mb-2 placeholder-glow">
                            <span class="placeholder" style="width: 25%;"></span>
                        </h6>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <a class="btn btn-body bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                    <a class="btn btn-danger bg-gradient rounded-3 disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                </div>
            </li>
    `;

    async function fetchOpnameObat() {
        const search = $('#searchInput').val();
        const offset = (currentPage - 1) * limit;
        const status = $('#statusFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('opnameobat/opnameobatlist') ?>', {
                params: {
                    search: search,
                    limit: limit,
                    offset: offset,
                }
            });

            const data = response.data;
            $('#opnameObatContainer').empty();
            $('#totalRecords').text(`(${data.total})`);

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#opnameObatContainer').append(
                    '<li class="list-group-item bg-body-tertiary pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.opname_obat.forEach(function(opname_obat) {
                    const opnameObatElement = `
            <li class="list-group-item bg-body-tertiary pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center ps-2 w-100">
                        <h5 class="card-title date">
                            ${opname_obat.tanggal}
                        </h5>
                        <h6 class="card-subtitle mb-2">
                            ${opname_obat.apoteker}
                        </h6>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient rounded-3" onclick="window.location.href = '<?= base_url('opnameobat/detailopnameobat') ?>/${opname_obat.id_opname_obat}';">
                        <i class="fa-solid fa-circle-info"></i> Detail
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient rounded-3 delete-btn" data-id="${opname_obat.id_opname_obat}" data-name="${opname_obat.apoteker}" data-date="${opname_obat.tanggal}">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </div>
            </li>
                `;

                    $('#opnameObatContainer').append(opnameObatElement);
                });

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

                for (let i = 1; i <= totalPages; i++) {
                    $('#paginationNav ul').append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
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
            <?= (session()->get('role') != 'Apoteker') ? 'fetchPasienOptions();' : '' ?>
            fetchOpnameObat();
        }
    });

    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            currentPage = 1;
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
            $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
            $('#deleteSubmessage').hide();

            try {
                await axios.delete(`<?= base_url('/opnameobat/delete') ?>/${opnameObatId}`);
                fetchOpnameObat();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Membuat Laporan Obat...
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
                    $('#opnameObatForm .is-invalid').removeClass('is-invalid');
                    $('#opnameObatForm .invalid-feedback').text('').hide();
                    $('#addButton').prop('disabled', true);
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
        $('#refreshButton').on('click', function() {
            $('#opnameObatContainer').empty();
            for (let i = 0; i < limit; i++) {
                $('#opnameObatContainer').append(placeholder);
            }
            fetchOpnameObat(); // Refresh articles on button click
        });

        fetchOpnameObat();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>