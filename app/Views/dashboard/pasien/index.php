<?= $this->extend('dashboard/templates/dashboard'); ?>
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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <div class="input-group input-group-sm">
                        <input type="search" id="searchInput" class="form-control" placeholder="Cari nomor rekam medis atau nama pasien">
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
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                        <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                                    </h5>
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
                                        <div class="mb-0 row g-1 placeholder-glow">
                                            <div class="col-5 fw-medium text-truncate">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                            <div class="col placeholder-glow">
                                                <span class="placeholder w-100"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                        <button type="button" class="btn btn-body btn-sm bg-gradient" style="width: 80px; height: 31px;" disabled></button>
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
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <?= form_open_multipart('/pasien/create', 'id="addForm"'); ?>
                <div class="modal-body p-4 text-center">
                    <h5 id="addMessage"></h5>
                    <h6 class="mb-0" id="addSubmessage"></h6>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmAddBtn">Ya</button>
                </div>
                <?= form_close(); ?>
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
        const offset = (currentPage - 1) * limit;

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pasien/pasienlist') ?>', {
                params: {
                    search: search,
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
                    const nama_pasien = pasien.nama_pasien ? pasien.nama_pasien : "<em>Belum Diisi</em>";
                    let jenis_kelamin = pasien.jenis_kelamin;
                    if (jenis_kelamin === 'L') {
                        jenis_kelamin = `Laki-Laki`;
                    } else if (jenis_kelamin === 'P') {
                        jenis_kelamin = `Perempuan`;
                    } else {
                        jenis_kelamin = `<em>Tidak ada</em>`;
                    }
                    const tempat_lahir = pasien.tempat_lahir ? pasien.tempat_lahir : "<em>Tidak ada</em>";
                    const tanggal_lahir = pasien.tanggal_lahir ? pasien.tanggal_lahir : "<em>Tidak ada</em>";
                    const alamat = pasien.alamat ? pasien.alamat : "<em>Tidak ada</em>";
                    const telpon = pasien.telpon ? pasien.telpon : "<em>Tidak ada</em>";
                    const pasienElement = `
            <span class="list-group-item border-top-0 pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-between">
                            <div class="d-flex justify-content-start text-truncate">
                                <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${pasien.number}</span>
                                <span class="mx-1 align-self-center text-truncate">${nama_pasien}</span>
                            </div>
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${pasien.no_rm}</span>
                        </h5>
                                    <div style="font-size: 0.75em;">
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Nama</div>
                                            <div class="col">
                                                ${nama_pasien}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                                            <div class="col">
                                                ${jenis_kelamin}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Tempat/Tanggal Lahir</div>
                                            <div class="col">
                                                ${tempat_lahir}, <span class="date text-nowrap">${tanggal_lahir}</span>
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Alamat</div>
                                            <div class="col">
                                                ${alamat}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                                            <div class="col date">
                                                ${telpon}
                                            </div>
                                        </div>
                                    </div>
                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('pasien/detailpasien') ?>/${pasien.id_pasien}'">
                                    <i class="fa-solid fa-circle-info"></i> Detail Pasien
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

        $('#exportButton').on('click', async function(ə) {
            ə.preventDefault();
            $(this).hide();
            $('#loadingSpinner').show(); // Menampilkan spinner

            // Membuat toast ekspor berjalan
            const toast = $(`
        <div id="exportToast" class="toast show transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <strong>Mengekspor</strong>
                    </div>
                    <div class="d-flex flex-row">
                        <span class="date" id="exportPercent">0%</span>
                        <div class="mb-1 ms-2">
                            <div class="vr"></div>
                            <button type="button" class="btn-close" aria-label="Close" id="cancelExport"></button>
                        </div>
                    </div>
                </div>
                <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                    <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient bg-primary" role="progressbar" style="width: 0%; transition: none"></div>
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

            try {
                // Mengambil file dari server dengan tracking progress
                const response = await axios.get(`<?= base_url('pasien/exportexcel') ?>`, {
                    responseType: 'blob', // Mendapatkan data sebagai blob
                    onDownloadProgress: function(progressEvent) {
                        if (progressEvent.lengthComputable) {
                            let percentComplete = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                            $('#exportPercent').text(percentComplete + '%');
                            $('#exportProgressBar').css('width', percentComplete + '%');
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
            }
        });

        $('#addButton').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#addMessage').html(`Tambah Pasien Baru?`);
            $('#addSubmessage').html(`Pastikan pasien tersebut benar-benar berobat dan membawa kartu identitas yang diperlukan. Ini akan menambahkan nomor rekam medis baru.`);
            $('#addModal').modal('show');
        });

        $(document).on('click', '#confirmAddBtn', function(ə) {
            ə.preventDefault();
            $('#addForm').submit();
            $('#addModal button').prop('disabled', true);
            $('#addMessage').addClass('mb-0').html('Menambahkan, silakan tunggu...');
            $('#addSubmessage').hide();
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