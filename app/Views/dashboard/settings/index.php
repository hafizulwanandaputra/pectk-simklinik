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
        <?php if (session()->get('role') == "Admin") : ?>
            <h5>Rekam Medis yang Kosong</h5>
            <div class="row row-cols-2 row-cols-lg-4 g-2 mb-2">
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Asesmen</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_assesment">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Skrining</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_skrining">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Edukasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_edukasi">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pemeriksaan Penunjang</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_permintaan_penunjang">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Tindakan Rawat Jalan</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_lp_tindakan_rajal">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pra Operasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_operasi_pra">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Keselamatan Operasi</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0 placeholder-glow" id="medrec_operasi_safety">
                                <span class="placeholder w-100"></span>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
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
            <?php if (session()->get('role') == "Admin") : ?>
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
    <?php if (session()->get('role') == "Admin") : ?>
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
        <?php if (session()->get('role') == "Admin") : ?>
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
                $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
                $('#deleteSubmessage').hide();

                try {
                    const response = await axios.delete(`<?= base_url('/settings/deleteempty') ?>`);
                    showSuccessToast(response.data.message);
                    LoadEmptyRecords();
                } catch (error) {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                } finally {
                    $('#deleteModal').modal('hide');
                    $('#deleteMessage').removeClass('mb-0');
                    $('#deleteSubmessage').show();
                    $('#deleteModal button').prop('disabled', false);
                }
            });
            LoadEmptyRecords();
        <?php else : ?>
            $('#loadingSpinner').hide();
        <?php endif; ?>
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>