<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <?php if (session()->get('role') == "Admin") : ?>
            <h5>Rekam Medis</h5>
            <ul class="list-group shadow-sm  mb-3">
                <li class="list-group-item p-1 list-group-item-action">
                    <div class="d-flex align-items-start">
                        <a id="deleteEmptyMRData" href="#" class="link-danger stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-trash"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <h5 class="card-title text-danger">Hapus Data Rekam Medis yang Kosong</h5>
                        </div>
                        <div class="align-self-center" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
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
    // Event listener untuk menangani klik pada tombol konfirmasi flush
    $(document).on('click', '#confirmFlush', function(e) {
        e.preventDefault(); // Mencegah aksi default tombol
        $('#flushForm').submit(); // Mengirimkan form flush
        $('#flushModal button').prop('disabled', true); // Nonaktifkan tombol flush
        $('#flushMessage').html(`Silakan tunggu...`); // Tampilkan pesan menunggu saat flush
    });
    $(document).ready(function() {
        // Menyembunyikan spinner loading saat dokumen sudah siap
        $('#loadingSpinner').hide(); // Menyembunyikan elemen spinner loading
        // Show delete confirmation modal
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
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteMessage').removeClass('mb-0');
                $('#deleteSubmessage').show();
                $('#deleteModal button').prop('disabled', false);
            }
        });
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>