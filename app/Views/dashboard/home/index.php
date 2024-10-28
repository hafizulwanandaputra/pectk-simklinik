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
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Farmasi</legend>
            <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                <?php if (session()->get('role') != "Dokter") : ?>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Supplier</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_supplier ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_obat ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat Masuk yang Belum Diterima</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_pembelian_obat_blm_diterima ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat Masuk yang Sudah Diterima</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_pembelian_obat_sdh_diterima ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col">
                    <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Resep yang Belum Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_resep_blm_status ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Resep Obat yang Sudah Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_resep_sdh_status ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Kasir</legend>
            <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                <div class="col">
                    <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Transaksi yang Belum Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_transaksi_blm_lunas ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Transaksi yang Sudah Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_transaksi_sdh_lunas ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Admin</legend>
            <div class="row row-cols-1 g-2 mb-2">
                <div class="col">
                    <div class="card bg-body-tertiary w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Pengguna</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_user ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        // Menyembunyikan spinner loading saat dokumen sudah siap
        $('#loadingSpinner').hide(); // Menyembunyikan elemen spinner loading
    });
</script>
<?= $this->endSection(); ?>