<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-6 me-3 text-success-emphasis" href="<?= base_url('/settings'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
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
        <div class="alert alert-info " role="alert">
            <div class="d-flex align-items-start">
                <div style="width: 12px; text-align: center;">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="w-100 ms-3">
                    Kata sandi harus minimal 3 karakter. Sesi yang menggunakan akun Anda selain dari perangkat ini akan dihapus setelah mengganti kata sandi. Disarankan untuk menggunakan kata sandi kuat demi keamanan.
                </div>
            </div>
        </div>
        <div class="alert alert-warning " id="capsLockStatus" role="alert" style="display: none;">
            <div class="d-flex align-items-start">
                <div style="width: 12px; text-align: center;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="w-100 ms-3">
                    <strong><em>CAPS LOCK</em> AKTIF!</strong> Harap periksa status <em>Caps Lock</em> pada papan tombol (<em>keyboard</em>) Anda.
                </div>
            </div>
        </div>
        <?= form_open_multipart('/settings/changepassword/update', 'id="changePasswordForm"'); ?>
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Kata Sandi Pengguna</div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control  <?= (validation_show_error('current_password')) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password" placeholder="current_password" data-bs-toggle="popover"
                    data-bs-placement="top"
                    data-bs-trigger="manual"
                    data-bs-title="<em>CAPS LOCK</em> AKTIF"
                    data-bs-content="Harap periksa status <span class='badge text-bg-dark bg-gradient kbd'>Caps Lock</span> pada papan tombol (<em>keyboard</em>) Anda.">
                <label for="current_password">Kata Sandi Lama</label>
                <div class="invalid-feedback">
                    <?= validation_show_error('current_password'); ?>
                </div>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control  <?= (validation_show_error('new_password1')) ? 'is-invalid' : ''; ?>" id="new_password1" name="new_password1" placeholder="new_password1" data-bs-toggle="popover"
                    data-bs-placement="top"
                    data-bs-trigger="manual"
                    data-bs-title="<em>CAPS LOCK</em> AKTIF"
                    data-bs-content="Harap periksa status <span class='badge text-bg-dark bg-gradient kbd'>Caps Lock</span> pada papan tombol (<em>keyboard</em>) Anda.">
                <label for="new_password1">Kata Sandi Baru</label>
                <div class="invalid-feedback">
                    <?= validation_show_error('new_password1'); ?>
                </div>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control  <?= (validation_show_error('new_password2')) ? 'is-invalid' : ''; ?>" id="new_password2" name="new_password2" placeholder="new_password2" data-bs-toggle="popover"
                    data-bs-placement="top"
                    data-bs-trigger="manual"
                    data-bs-title="<em>CAPS LOCK</em> AKTIF"
                    data-bs-content="Harap periksa status <span class='badge text-bg-dark bg-gradient kbd'>Caps Lock</span> pada papan tombol (<em>keyboard</em>) Anda.">
                <label for="new_password2">Konfirmsi Kata Sandi Baru</label>
                <div class="invalid-feedback">
                    <?= validation_show_error('new_password2'); ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
            <button class="btn btn-primary  bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-pen-to-square"></i> Ubah</button>
        </div>
        <?= form_close(); ?>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide(); // Menyembunyikan spinner loading saat halaman siap

        // Menangani semua input password dengan jQuery
        $('input[type="password"]').each(function() {
            const passwordInput = $(this); // Menggunakan jQuery untuk elemen input
            const popover = new bootstrap.Popover(passwordInput[0], {
                html: true,
                template: '<div class="popover shadow-lg" role="tooltip">' +
                    '<div class="popover-arrow"></div>' +
                    '<h3 class="popover-header"></h3>' +
                    '<div class="popover-body">Caps Lock aktif!</div>' +
                    '</div>'
            });

            let capsLockActive = false; // Status Caps Lock sebelumnya

            // Menambahkan event listener untuk 'focus' pada setiap input password
            passwordInput.on('focus', function() {
                passwordInput[0].addEventListener('keyup', function(event) {
                    const currentCapsLock = event.getModifierState('CapsLock'); // Memeriksa status Caps Lock

                    // Jika status Caps Lock berubah
                    if (currentCapsLock !== capsLockActive) {
                        capsLockActive = currentCapsLock; // Perbarui status
                        if (capsLockActive) {
                            popover.show(); // Tampilkan popover jika Caps Lock aktif
                        } else {
                            popover.hide(); // Sembunyikan popover jika Caps Lock tidak aktif
                        }
                    }
                });
            });

            // Menambahkan event listener untuk 'blur' pada setiap input password
            passwordInput.on('blur', function() {
                popover.hide(); // Sembunyikan popover saat kehilangan fokus
                passwordInput[0].removeEventListener('keyup', function() {}); // Hapus listener keyup saat blur
                capsLockActive = false; // Reset status Caps Lock
            });
        });

        // Menangani event klik pada tombol dengan ID 'submitBtn'
        $(document).on('click', '#submitBtn', function(e) {
            e.preventDefault(); // Mencegah perilaku default dari tombol
            $('#changePasswordForm').submit(); // Mengirimkan form untuk mengubah kata sandi
            $('input').prop('disabled', true); // Menonaktifkan semua field input
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses, silakan tunggu...</span>
            `); // Mengubah tampilan tombol submit menjadi loading
        });
    });
</script>
<?= $this->endSection(); ?>