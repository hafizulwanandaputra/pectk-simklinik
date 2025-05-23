<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/settings'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
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
        <?= form_open_multipart('/settings/update', 'id="userInfoForm"'); ?>
        <?= csrf_field(); ?>
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Informasi Pengguna</div>
            <div class="form-floating mb-2">
                <input type="text" class="form-control  <?= (validation_show_error('username')) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?= (old('username')) ? old('username') : htmlspecialchars(session()->get('username')); ?>" autocomplete="off" dir="auto" placeholder="username">
                <label for="username">Nama Pengguna</label>
                <div class="invalid-feedback">
                    <?= validation_show_error('username'); ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
            <button class="btn btn-primary  bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-user-pen"></i> Ubah</button>
        </div>
        <?= form_close(); ?>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
        $('input.form-control').on('input', function() {
            // Remove the is-invalid class for the current input field
            $(this).removeClass('is-invalid');
            // Hide the invalid-feedback message for the current input field
            $(this).siblings('.invalid-feedback').hide();
        });
        $(document).on('click', '#submitBtn', function(e) {
            e.preventDefault();
            $('#userInfoForm').submit();
            $('input').prop('disabled', true);
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Mengubah, silakan tunggu...</span>
            `);
        });
    });
</script>
<?= $this->endSection(); ?>