<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <?php if (session()->get('role') == "Admin") : ?>
        <h5>Transaksi</h5>
        <ul class="list-group shadow-sm rounded-3 mb-3">
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/settings/pwdtransaksi'); ?>" class="stretched-link" style="min-width: 48px; max-width: 48px; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-key"></i></p>
                    </a>
                    <div class="align-self-center flex-fill ps-1 text-wrap overflow-hidden" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Ubah Kata Sandi Transaksi</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 48px; max-width: 48px; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        </ul>
    <?php endif; ?>
    <h5>Pengguna</h5>
    <ul class="list-group shadow-sm rounded-3 mb-3">
        <?php if (session()->get('role') == "Admin") : ?>
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 48px; max-width: 48px; text-align: center;" data-bs-toggle="modal" data-bs-target="#flushModal">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-broom"></i></p>
                    </a>
                    <div class="align-self-center flex-fill ps-1 text-wrap overflow-hidden" style="text-overflow: ellipsis;">
                        <h5 class="card-title">Bersihkan Sesi Pengguna</h5>
                    </div>
                    <div class="align-self-center" style="min-width: 48px; max-width: 48px; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                    </div>
                </div>
            </li>
        <?php endif; ?>
        <li class="list-group-item p-1 list-group-item-action">
            <div class="d-flex align-items-start">
                <a href="<?= base_url('/settings/edit'); ?>" class="stretched-link" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-user-pen"></i></p>
                </a>
                <div class="align-self-center flex-fill ps-1 text-wrap overflow-hidden" style="text-overflow: ellipsis;">
                    <h5 class="card-title">Ubah Informasi Pengguna</h5>
                </div>
                <div class="align-self-center" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                </div>
            </div>
        </li>
        <li class="list-group-item p-1 list-group-item-action">
            <div class="d-flex align-items-start">
                <a href="<?= base_url('/settings/changepassword'); ?>" class="stretched-link" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-key"></i></p>
                </a>
                <div class="align-self-center flex-fill ps-1 text-wrap overflow-hidden" style="text-overflow: ellipsis;">
                    <h5 class="card-title">Ubah Kata Sandi Pengguna</h5>
                </div>
                <div class="align-self-center" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                </div>
            </div>
        </li>
    </ul>
    <h5>Sistem</h5>
    <ul class="list-group shadow-sm rounded-3 mb-3">
        <li class="list-group-item p-1 list-group-item-action">
            <div class="d-flex align-items-start">
                <a href="<?= base_url('/settings/about'); ?>" class="stretched-link" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-circle-info"></i></p>
                </a>
                <div class="align-self-center flex-fill ps-1 text-wrap overflow-hidden" style="text-overflow: ellipsis;">
                    <h5 class="card-title">Tentang Sistem</h5>
                </div>
                <div class="align-self-center" style="min-width: 48px; max-width: 48px; text-align: center;">
                    <span class="text-body-tertiary"><i class="fa-solid fa-angle-right"></i></span>
                </div>
            </div>
        </li>
    </ul>
    <?php if (session()->get('role') == "Admin") : ?>
        <div class="modal modal-sheet p-4 py-md-5 fade" id="flushModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="flushModal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                    <div class="modal-body p-4 text-center">
                        <h5 class="mb-0" id="flushMessage">Melakukan pembersihan sesi akan membuat semua pengguna kecuali Anda di perangkat ini keluar dan diminta untuk masuk kembali. Apakah Anda ingin melanjutkan?</h5>
                    </div>
                    <form action="<?= base_url('/settings/flush') ?>" method="post" id="flushForm" class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" data-bs-dismiss="modal" style="border-right: 1px solid var(--bs-border-color-translucent);">Tidak</button>
                        <button type="submit" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmFlush">Ya</a>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
    });
</script>
<?= $this->endSection(); ?>