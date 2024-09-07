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
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4">
    <div class="d-flex justify-content-start align-items-start pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 me-3"><i class="fa-regular fa-face-smile-beam"></i></h1>
        <h1 class="h2"><?= $txtgreeting . ', ' . session()->get('fullname') . '!'; ?></h1>
    </div>
    <div class="row row-cols-1 <?= (session()->get('role') == "Master Admin") ? 'row-cols-sm-2' : 'row-cols-sm-3'; ?> g-3 mb-3">
        <div class="col">
            <div class="card text-bg-secondary mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Menu Makanan</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-bg-primary mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Permintaan</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-bg-success mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Petugas Gizi</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"></h5>
                </div>
            </div>
        </div>
        <?php if (session()->get('role') == "Admin") : ?>
            <div class="col">
                <div class="card text-bg-danger mb-3 w-100 rounded-3">
                    <div class="card-header w-100 text-truncate bg-gradient">Admin</div>
                    <div class="card-body">
                        <h5 class="display-5 fw-medium date"></h5>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row row-cols-1 g-3 mb-3">
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Permintaan Pasien per Bulan</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="permintaanperbulangraph"></canvas>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Jumlah Permintaan berdasarkan Menu Makanan</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="permintaangraph"></canvas>
                </div>
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Jumlah Menu Makanan berdasarkan Petugas Gizi</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="petugasgraph"></canvas>
                </div>
            </fieldset>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    });
</script>
<?= $this->endSection(); ?>
<?= $this->section('chartjs'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js" integrity="sha512-SIMGYRUjwY8+gKg7nn9EItdD8LCADSDfJNutF9TPrvEo86sQmFMh6MyralfIyhADlajSxqc7G0gs7+MwWF/ogQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?= $this->endSection(); ?>