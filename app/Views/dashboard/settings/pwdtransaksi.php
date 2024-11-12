<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 link-body-emphasis" href="<?= base_url('/settings'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <div class="d-lg-flex justify-content-center">
        <div class="no-fluid-content">
            <div class="alert alert-info rounded-3" role="alert">
                <div class="d-flex align-items-start">
                    <div style="width: 12px; text-align: center;">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="w-100 ms-3">
                        Kata sandi harus minimal 5 karakter. Disarankan untuk membuat kata sandi yang kuat demi keamanan sistem transaksi untuk mencegah pembatalan dan penghapusan transaksi tanpa persetujuan admin.
                    </div>
                </div>
            </div>
            <?= form_open_multipart('/settings/updatepwdtransaksi', 'id="changePasswordForm"'); ?>
            <fieldset class="border rounded-3 px-2 py-0">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Kata Sandi Transaksi</legend>
                <div class="form-floating mb-2">
                    <input type="password" class="form-control rounded-3 <?= (validation_show_error('new_password1')) ? 'is-invalid' : ''; ?>" id="new_password1" name="new_password1" placeholder="new_password1">
                    <label for="new_password1">Kata Sandi Baru</label>
                    <div class="invalid-feedback">
                        <?= validation_show_error('new_password1'); ?>
                    </div>
                </div>
                <div class="form-floating mb-2">
                    <input type="password" class="form-control rounded-3 <?= (validation_show_error('new_password2')) ? 'is-invalid' : ''; ?>" id="new_password2" name="new_password2" placeholder="new_password2">
                    <label for="new_password2">Konfirmsi Kata Sandi Baru</label>
                    <div class="invalid-feedback">
                        <?= validation_show_error('new_password2'); ?>
                    </div>
                </div>
            </fieldset>
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-primary rounded-3 bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-pen-to-square"></i> Ubah</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide(); // Menyembunyikan spinner loading saat halaman siap

        // Menangani event input pada field dengan kelas 'form-control'
        $('input.form-control').on('input', function() {
            // Menghapus kelas 'is-invalid' untuk field input saat ini
            $(this).removeClass('is-invalid');
            // Menyembunyikan pesan 'invalid-feedback' untuk field input saat ini
            $(this).siblings('.invalid-feedback').hide();
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