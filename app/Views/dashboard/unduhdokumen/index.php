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
        <h5>Rawat Jalan</h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action">
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('/unduhdokumen/optik'); ?>" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;" target="_blank">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-glasses"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Resep Kacamata Kosong</h5>
                            <span>Dokumen resep kacamata kosong yang dapat digunakan untuk membuat resep kacamata secara manual.</span>
                        </div>
                    </div>
                    <div class="align-self-center lh-sm" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <span class="text-body-tertiary"><i class="fa-solid fa-file-arrow-down"></i><br><span style="font-size: 0.75em;">PDF</span></span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>