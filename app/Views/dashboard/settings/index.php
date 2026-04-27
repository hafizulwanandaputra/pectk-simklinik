<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <?php if (session()->get('role') == 'Admin' || session()->get('role') == "Manajer") : ?>
            <h5>Rekam Medis yang Kosong</h5>
            <div class="row row-cols-2 row-cols-lg-4 g-2 mb-2">
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Asesmen</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_assesment">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Skrining</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_skrining">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Edukasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_edukasi">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pemeriksaan Penunjang</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_permintaan_penunjang">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Tindakan Rawat Jalan</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_lp_tindakan_rajal">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pra Operasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_operasi_pra">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Keselamatan Operasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_operasi_safety">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Kacamata</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_optik">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="list-group shadow-sm  mb-3">
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a id="refreshEmptyMRData" href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-arrows-rotate"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title">Segarkan Data Rekam Medis yang Kosong</h5>
                        </div>
                    </div>
                </li>
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a id="deleteEmptyMRData" href="#" class="link-danger stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-trash"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title text-danger">Hapus Data Rekam Medis yang Kosong</h5>
                        </div>
                    </div>
                </li>
            </ul>
            <h5>Transaksi</h5>
            <ul class="list-group shadow-sm  mb-3">
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a href="<?= base_url('/settings/pwdtransaksi'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-key"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title">Ubah Kata Sandi Transaksi</h5>
                        </div>
                        <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                        </div>
                    </div>
                </li>
            </ul>
        <?php endif; ?>
        <h5>Pengguna</h5>
        <ul class="list-group shadow-sm  mb-3">
            <?php if (session()->get('role') == 'Admin') : ?>
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a href="<?= base_url('/settings/sessions'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-users-gear"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title">Manajer Sesi</h5>
                        </div>
                        <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
            <li class="list-group-item p-1 list-group-item-action" id="ubah-foto-profil">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/changepassword'); ?>" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-file-image"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Unggah Foto Profil</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"></span>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/edit'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-user-pen"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Ubah Informasi Pengguna</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/changepassword'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-key"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Ubah Kata Sandi Pengguna</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        </ul>
        <h5>Sistem</h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/about'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-circle-info"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Tentang Sistem</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        </ul>
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
    <div class="modal fade" id="modalFotoProfil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalFotoProfilLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
            <form id="fotoProfilForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="modalFotoProfilLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="mb-1 mt-1">
                        <label for="profilephoto" class="form-label mb-0">Unggah Gambar (maks 8 MB)</label>
                        <input class="form-control" type="file" id="profilephoto" name="profilephoto" accept="image/jpg,image/jpeg,image/png">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="gambar_preview_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="gambar_preview" src="#" alt="Gambar" class="img-thumbnail" style="max-width: 100%">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <!-- Progress bar -->
                    <div class="mb-1 mt-1 w-100" id="uploadProgressDiv">
                        <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient" role="progressbar" style="width: 0%; transition: none;" id="uploadProgressBar"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" id="cancelButton" class="btn btn-danger bg-gradient" style="display: none;" disabled>
                                <i class="fa-solid fa-xmark"></i> Batalkan
                            </button>
                        </div>
                        <button type="submit" id="submitButton" class="btn btn-primary bg-gradient">
                            <i class="fa-solid fa-file-arrow-up"></i> Unggah
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    <?php if (session()->get('role') == 'Admin' || session()->get('role') == "Manajer") : ?>
        async function LoadEmptyRecords() {
            $('#loadingSpinner').show();
            const spinner = `<span class="placeholder w-100"></span>`;
            $('#medrec_assesment').html(spinner);
            $('#medrec_edukasi').html(spinner);
            $('#medrec_skrining').html(spinner);
            $('#medrec_permintaan_penunjang').html(spinner);
            $('#medrec_lp_tindakan_rajal').html(spinner);
            $('#medrec_operasi_pra').html(spinner);
            $('#medrec_operasi_safety').html(spinner);
            $('#medrec_optik').html(spinner);

            try {
                const response = await axios.get(`<?= base_url('/settings/emptyrecords') ?>`);
                $('#medrec_assesment').text(response.data.medrec_assesment);
                $('#medrec_edukasi').text(response.data.medrec_edukasi);
                $('#medrec_skrining').text(response.data.medrec_skrining);
                $('#medrec_permintaan_penunjang').text(response.data.medrec_permintaan_penunjang);
                $('#medrec_lp_tindakan_rajal').text(response.data.medrec_lp_tindakan_rajal);
                $('#medrec_operasi_pra').text(response.data.medrec_operasi_pra);
                $('#medrec_operasi_safety').text(response.data.medrec_operasi_safety);
                $('#medrec_optik').text(response.data.medrec_optik);
            } catch (error) {
                const falied = `
                <span class="text-danger"><i class="fa-solid fa-xmark"></i></span>
            `;
                $('#medrec_assesment').html(falied);
                $('#medrec_edukasi').html(falied);
                $('#medrec_skrining').html(falied);
                $('#medrec_permintaan_penunjang').html(falied);
                $('#medrec_lp_tindakan_rajal').html(falied);
                $('#medrec_operasi_pra').html(falied);
                $('#medrec_operasi_safety').html(falied);
                $('#medrec_optik').html(falied);
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#loadingSpinner').hide();
            }
        };
    <?php endif; ?>
    $(document).ready(function() {
        <?php if (session()->get('role') == 'Admin' || session()->get('role') == "Manajer") : ?>
            const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

            socket.onopen = () => {
                console.log("Connected to WebSocket server");
            };

            socket.onmessage = async function(event) {
                const data = JSON.parse(event.data);
                if (data.update || data.delete) {
                    console.log("Received update from WebSocket");
                    LoadEmptyRecords();
                }
            };

            socket.onclose = () => {
                console.log("Disconnected from WebSocket server");
            };

            $('#refreshEmptyMRData').click(function() {
                LoadEmptyRecords();
            });

            $('#deleteEmptyMRData').click(function() {
                $('[data-bs-toggle="tooltip"]').tooltip('hide');
                $('#deleteMessage').html(`Hapus data rekam medis yang kosong?`);
                $('#deleteSubmessage').html(`Tindakan ini tidak dapat dikembalikan. Pastikan tidak ada aktivitas rekam medis saat menghapus.`);
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(async function() {
                $('#deleteModal button').prop('disabled', true);
                $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

                try {
                    const response = await axios.delete(`<?= base_url('/settings/deleteempty') ?>`);
                    showSuccessToast(response.data.message);
                    LoadEmptyRecords();
                } catch (error) {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                } finally {
                    $('#deleteModal').modal('hide');
                    $('#deleteModal button').prop('disabled', false);
                    $(this).text(`Hapus`); // Mengembalikan teks tombol asal
                }
            });
            LoadEmptyRecords();
        <?php else : ?>
            $('#loadingSpinner').hide();
        <?php endif; ?>

        $('#profilephoto').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#gambar_preview').attr('src', e.target.result);
                $('#gambar_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Tampilkan modal ubah foto profil
        $('#ubah-foto-profil').click(function(ə) {
            ə.preventDefault();
            $('#modalFotoProfilLabel').text('Unggah Foto Profil'); // Ubah judul modal menjadi 'Unggah Foto Profil'
            $('#id_user').val('');
            $('#modalFotoProfil').modal('show'); // Tampilkan modal resep luar
        });

        $('#fotoProfilForm').submit(async function(ə) {
            ə.preventDefault();

            var url = `<?= base_url('settings/updateprofilephoto') ?>`;
            var formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", formData);

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Clear previous validation states
            $('#fotoProfilForm .is-invalid').removeClass('is-invalid');
            $('#fotoProfilForm .invalid-feedback').text('').hide();

            // Show processing button and progress bar
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#closeBtn').prop('disabled', true);
            $('#cancelButton').prop('disabled', false).show();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

            // Disable form inputs
            $('#fotoProfilForm input').prop('disabled', true);

            try {
                // Perform the post request with progress handling
                let response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        if (progressEvent.lengthComputable) {
                            var percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                            $('#uploadProgressBar').css('width', percent + '%');
                            $('#uploadPercentage').html(percent + '%');
                        }
                    },
                    cancelToken: source.token // Attach the token here
                });

                // Handle successful response
                if (response.data.success) {
                    $('#cancelButton').prop('disabled', true).hide();
                    $('#uploadProgressBar').addClass('bg-success');
                    $('#submitButton').prop('disabled', true).html(`
                        <?= $this->include('spinner/spinner'); ?>
                        <span>Unggah Berhasil! Memuat ulang...</span>
                    `);
                    window.location.reload();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#fotoProfilForm .is-invalid').removeClass('is-invalid');
                    $('#fotoProfilForm .invalid-feedback').text('').hide();

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
                    console.error('Perbaiki kesalahan pada formulir.');
                    $('#uploadProgressBar').addClass('bg-danger');
                    // Reset the form and UI elements
                    $('#uploadPercentage').html('0%');
                    $('#closeBtn').prop('disabled', false);
                    $('#cancelButton').prop('disabled', true).hide();
                    $('#submitButton').prop('disabled', false).html(`
                        <i class="fa-solid fa-file-arrow-up"></i> Unggah
                    `);
                    $('#fotoProfilForm input').prop('disabled', false);
                }
            } catch (error) {
                if (axios.isCancel(error)) {
                    showFailedToast(error.message);
                    $('#uploadProgressBar').css('width', '0%');
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                    $('#uploadProgressBar').addClass('bg-danger');
                }
                // Reset the form and UI elements
                $('#uploadPercentage').html('0%');
                $('#closeBtn').prop('disabled', false);
                $('#cancelButton').prop('disabled', true).hide();
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-file-arrow-up"></i> Unggah
                `);
                $('#fotoProfilForm input').prop('disabled', false);
            }

            // Attach the cancel functionality to the close button
            $('#closeBtn, #cancelButton').on('click', function() {
                source.cancel('Unggah foto profil dibatalkan.');
            });
        });

        // Reset form saat modal ditutup
        $('#modalFotoProfil').on('hidden.bs.modal', function() {
            // reset form
            $('#fotoProfilForm')[0].reset();

            // balikin status checkbox
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#gambar_preview').attr('src', '#');
            $('#gambar_preview_div').hide();
            $('#fotoProfilForm .is-invalid').removeClass('is-invalid');
            $('#fotoProfilForm .invalid-feedback').text('').hide();
        });
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>