<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .ratio-onecol {
        --bs-aspect-ratio: 33%;
    }

    @media (max-width: 991.98px) {
        .ratio-onecol {
            --bs-aspect-ratio: 75%;
        }
    }
</style>
<?= $this->endSection(); ?>
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
<main class="main-content-inside px-3">
    <div class="no-fluid-content">
        <div class="d-flex justify-content-start align-items-start pt-3">
            <h1 class="h2 mb-0 me-3"><i class="fa-regular fa-face-smile-beam"></i></h1>
            <h1 class="h2 mb-0"><?= $txtgreeting . ', ' . session()->get('fullname') . '!'; ?></h1>
        </div>
        <hr>
        <?php if (session()->get('role') == "Admin") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Admin</div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pengguna Keseluruhan</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Pengguna Nonaktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_inactive, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Pengguna Aktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Sesi Keseluruhan Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Sesi Kedaluwarsa Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_expired, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Sesi Aktif Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Statistik Antrean</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Antrean Menurut Jaminan</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="antreanpiegraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Antrean Per Bulan Menurut Jaminan</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="antreankodegraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card w-100 h-100 shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Antrean Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="antreangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pasien dan Rawat Jalan</div>
                <div class="mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Total Pasien</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_pasien, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Total Rawat Jalan yang Dibatalkan</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rajal_all_batal, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Total Rawat Jalan yang Didaftarkan</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rajal_all, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Pasien yang Batal Berobat Hari Ini</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rawatjalan_batal, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Pasien yang Berobat Hari Ini</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rawatjalan, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Agama Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="agamagraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Jenis Kelamin Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="jeniskelamingraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Provinsi Pasien</div>
                            <div class="card-body py-2 px-3">
                                <input type="search" id="ProvinsiFilter" class="form-control form-control-sm mb-2" placeholder="Masukkan provinsi">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Provinsi</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pasien</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_provinsi">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat provinsi...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_provinsi" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_provinsi">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kabupaten/Kota Pasien</div>
                            <div class="card-body py-2 px-3">
                                <input type="search" id="KabupatenFilter" class="form-control form-control-sm mb-2" placeholder="Masukkan kabuapaten/kota">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Kabupaten/Kota</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pasien</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_kabkota">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat kabupaten/kota...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_kabkota" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_kabkota">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kecamatan Pasien</div>
                            <div class="card-body py-2 px-3">
                                <input type="search" id="KecamatanFilter" class="form-control form-control-sm mb-2" placeholder="Masukkan kecamatan">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Kecamatan</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pasien</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_kecamatan">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat kecamatan...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_kecamatan" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_kecamatan">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kelurahan Pasien</div>
                            <div class="card-body py-2 px-3">
                                <input type="search" id="KelurahanFilter" class="form-control form-control-sm mb-2" placeholder="Masukkan kelurahan">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Kelurahan</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pasien</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_kelurahan">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat kelurahan...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_kelurahan" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_kelurahan">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Rawat Jalan Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="rawatjalanpiegraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Rawat Jalan Per Bulan Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="rawatjalandoktergraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card w-100 h-100 shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Rawat Jalan Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="rawatjalangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">ICD-10 (Diagnosis)</div>
                            <div class="card-body py-2 px-3">
                                <input type="month" id="ICD10bulanFilter" class="form-control form-control-sm mb-2" value="<?= date('Y-m'); ?>">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">ICD-10</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Kasus</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_icd_x">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat ICD-10...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_icd_x" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_icd_x">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">ICD-9 CM (Tindakan)</div>
                            <div class="card-body py-2 px-3">
                                <input type="month" id="ICD9bulanFilter" class="form-control form-control-sm mb-2" value="<?= date('Y-m'); ?>">
                                <div class="card ratio ratio-4x3 w-100 overflow-auto">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                            <thead>
                                                <tr class="align-middle">
                                                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">ICD-9 CM</th>
                                                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="align-top" id="view_icd_9">
                                                <tr>
                                                    <td colspan="3" class="text-center">Memuat ICD-9 CM...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <nav id="paginationNav_icd_9" class="d-flex justify-content-end mt-2 overflow-auto w-100">
                                    <ul class="pagination pagination-sm mb-0"></ul>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Keseluruhan</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_icd_9">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Farmasi</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <?php if (session()->get('role') != "Dokter") : ?>
                        <div class="col">
                            <div class="card w-100 h-100 shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pemasok</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_supplier, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card w-100 h-100 shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Obat</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_obat, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Resep yang Belum Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_resep_blm_status, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Resep yang Sudah Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_resep_sdh_status, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="resepbydoktergraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Per Bulan Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="resepgraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card w-100 h-100 shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="resepallgraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Kasir</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Transaksi yang Belum Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_transaksi_blm_lunas, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Transaksi yang Sudah Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_transaksi_sdh_lunas, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Menurut Petugas Kasir</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="transaksibykasirgraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card w-100 h-100 shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Per Bulan Menurut Petugas Kasir</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="transaksiperbulangraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card w-100 h-100 shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="transaksiperbulanallgraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-primary-subtle border-primary-subtle text-primary-emphasis w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-primary-subtle w-100 text-truncate">Total Pemasukan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0"><?= 'Rp' . number_format($total_pemasukan, 0, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card w-100 h-100 shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pemasukan Per Bulan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="pemasukanperbulangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let limit = 20;
    let currentPage = 1;

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        $('#ICD10bulanFilter, #ICD9bulanFilter').flatpickr({
            plugins: [
                new monthSelectPlugin({
                    altFormat: "F Y",
                    dateFormat: "Y-m",
                })
            ],
            altInput: true,
            disableMobile: "true"
        });

        // Fetch Provinsi
        async function fetchProvinsi() {
            const kueri = $('#ProvinsiFilter').val();
            const offset = (currentPage - 1) * limit;

            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/persebaran_provinsi') ?>', {
                    params: {
                        kueri: kueri,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_provinsi').empty();
                $('#total_provinsi').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_provinsi ul').empty();
                    $('#view_provinsi').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada provinsi</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_pasien = parseInt(data.total_pasien);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = "";
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const ProvinsiElement = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.provinsiNama}</td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_pasien.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_provinsi').append(ProvinsiElement);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_provinsi ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_provinsi ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_provinsi ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_provinsi ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_provinsi ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_provinsi ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_provinsi ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_provinsi ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_provinsi ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_provinsi').empty();
                if (error.response.request.status === 400) {
                    $('#view_provinsi').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_provinsi').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_provinsi ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_provinsi a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchProvinsi();
            }
        });

        // Fetch Kabupaten/Kota
        async function fetchKabupaten() {
            const kueri = $('#KabupatenFilter').val();
            const offset = (currentPage - 1) * limit;

            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/persebaran_kabkota') ?>', {
                    params: {
                        kueri: kueri,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_kabkota').empty();
                $('#total_kabkota').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_kabkota ul').empty();
                    $('#view_kabkota').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada provinsi</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_pasien = parseInt(data.total_pasien);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = "";
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const KabupatenElement = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.kabupatenNama}<br><small>${data.provinsiNama}</small></td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_pasien.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_kabkota').append(KabupatenElement);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_kabkota ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_kabkota ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_kabkota ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_kabkota ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_kabkota ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_kabkota ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_kabkota ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_kabkota ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_kabkota ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_kabkota').empty();
                if (error.response.request.status === 400) {
                    $('#view_kabkota').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_kabkota').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_kabkota ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_kabkota a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchKabupaten();
            }
        });

        // Fetch Kecamatan
        async function fetchKecamatan() {
            const kueri = $('#KecamatanFilter').val();
            const offset = (currentPage - 1) * limit;

            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/persebaran_kecamatan') ?>', {
                    params: {
                        kueri: kueri,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_kecamatan').empty();
                $('#total_kecamatan').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_kecamatan ul').empty();
                    $('#view_kecamatan').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada provinsi</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_pasien = parseInt(data.total_pasien);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = "";
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const KecamatanElement = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.kecamatanNama}<br><small>${data.kabupatenNama}, ${data.provinsiNama}</small></td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_pasien.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_kecamatan').append(KecamatanElement);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_kecamatan ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_kecamatan ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_kecamatan ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_kecamatan ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_kecamatan ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_kecamatan ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_kecamatan ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_kecamatan ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_kecamatan ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_kecamatan').empty();
                if (error.response.request.status === 400) {
                    $('#view_kecamatan').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_kecamatan').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_kecamatan ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_kecamatan a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchKecamatan();
            }
        });

        // Fetch Kelurahan
        async function fetchKelurahan() {
            const kueri = $('#KelurahanFilter').val();
            const offset = (currentPage - 1) * limit;

            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/persebaran_kelurahan') ?>', {
                    params: {
                        kueri: kueri,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_kelurahan').empty();
                $('#total_kelurahan').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_kelurahan ul').empty();
                    $('#view_kelurahan').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada provinsi</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_pasien = parseInt(data.total_pasien);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = "";
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const KelurahanElement = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.kelurahanNama}<br><small>${data.kecamatanNama}, ${data.kabupatenNama}, ${data.provinsiNama}</small></td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_pasien.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_kelurahan').append(KelurahanElement);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_kelurahan ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_kelurahan ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_kelurahan ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_kelurahan ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_kelurahan ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_kelurahan ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_kelurahan ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_kelurahan ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_kelurahan ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_kelurahan').empty();
                if (error.response.request.status === 400) {
                    $('#view_kelurahan').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_kelurahan').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_kelurahan ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_kelurahan a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchKelurahan();
            }
        });

        async function fetchICD10() {
            const bulan = $('#ICD10bulanFilter').val();
            const offset = (currentPage - 1) * limit;


            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/icd_x') ?>', {
                    params: {
                        bulan: bulan,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_icd_x').empty();
                $('#total_icd_x').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_icd_x ul').empty();
                    $('#view_icd_x').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada ICD-10</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_icdx = parseInt(data.total_icdx);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = "";
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const ICD10Element = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.icdx_kode}<br><small>${data.icdx_nama}</small></td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_icdx.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_icd_x').append(ICD10Element);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_icd_x ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_icd_x ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_icd_x ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_icd_x ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_icd_x ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_icd_x ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_icd_x ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_icd_x ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_icd_x ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_icd_x').empty();
                if (error.response.request.status === 400) {
                    $('#view_icd_x').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_icd_x').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_icd_x ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_icd_x a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchICD10();
            }
        });

        async function fetchICD9() {
            const bulan = $('#ICD9bulanFilter').val();
            const offset = (currentPage - 1) * limit;

            // Show the spinner
            $('#loadingSpinner').show();

            try {
                const response = await axios.get('<?= base_url('home/icd_9') ?>', {
                    params: {
                        bulan: bulan,
                        limit: limit,
                        offset: offset,
                    }
                });

                const data = response.data;
                $('#view_icd_9').empty();
                $('#total_icd_9').text(data.total.toLocaleString('id-ID'));

                if (data.total === "0") {
                    $('#paginationNav_icd_9 ul').empty();
                    $('#view_icd_9').append(`
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada ICD-9 CM</td>
                    </tr>
                `);
                } else {
                    data.data.forEach(function(data) {
                        const total_icd9 = parseInt(data.total_icd9);
                        // Tentukan kelas berdasarkan data.number
                        let rowClass = "";
                        if (data.number == 1) {
                            rowClass = "bg-gold";
                        } else if (data.number == 2) {
                            rowClass = "bg-silver";
                        } else if (data.number == 3) {
                            rowClass = "bg-bronze";
                        } else {
                            rowClass = ""; // Default class jika bukan 1, 2, atau 3
                        }
                        let rowBold = "";
                        if (data.number == 1 || data.number == 2 || data.number == 3) {
                            rowBold = "fw-bold";
                        } else {
                            rowBold = "";
                        }
                        const ICD9Element = `
                    <tr>
                        <td class="date text-nowrap text-center ${rowClass} ${rowBold}">${data.number}</td>
                        <td class="${rowClass} ${rowBold}">${data.icd9_kode}<br><small>${data.icd9_nama}</small></td>
                        <td class="date text-end ${rowClass} ${rowBold}">${total_icd9.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                        $('#view_icd_9').append(ICD9Element);
                    });

                    // Pagination logic with ellipsis for more than 3 pages
                    const totalPages = Math.ceil(data.total / limit);
                    $('#paginationNav_icd_9 ul').empty();

                    if (currentPage > 1) {
                        $('#paginationNav_icd_9 ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                    }

                    if (totalPages > 5) {
                        $('#paginationNav_icd_9 ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                        if (currentPage > 3) {
                            $('#paginationNav_icd_9 ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                            $('#paginationNav_icd_9 ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }

                        if (currentPage < totalPages - 2) {
                            $('#paginationNav_icd_9 ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                        }

                        $('#paginationNav_icd_9 ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                    } else {
                        // Show all pages if total pages are 3 or fewer
                        for (let i = 1; i <= totalPages; i++) {
                            $('#paginationNav_icd_9 ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                        }
                    }

                    if (currentPage < totalPages) {
                        $('#paginationNav_icd_9 ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                    }
                }
            } catch (error) {
                $('#view_icd_9').empty();
                if (error.response.request.status === 400) {
                    $('#view_icd_9').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error.response.data.error}</td>
                    </tr>
                `);
                    showFailedToast(error.response.data.error);
                } else {
                    $('#view_icd_9').append(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">${error}</td>
                    </tr>
                `);
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
                $('#paginationNav_icd_9 ul').empty();
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }

        $(document).on('click', '#paginationNav_icd_9 a', function(event) {
            event.preventDefault(); // Prevents default behavior (scrolling)
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                fetchICD9();
            }
        });
    <?php endif; ?>

    $(document).ready(function() {
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
            $('#ProvinsiFilter').on('input', function() {
                fetchProvinsi();
            });
            $('#KabupatenFilter').on('input', function() {
                fetchKabupaten();
            });
            $('#KecamatanFilter').on('input', function() {
                fetchKecamatan();
            });
            $('#KelurahanFilter').on('input', function() {
                fetchKelurahan();
            });
            $('#ICD10bulanFilter').on('change', function() {
                fetchICD10();
            });
            $('#ICD9bulanFilter').on('change', function() {
                fetchICD9();
            });
            fetchProvinsi();
            fetchKabupaten();
            fetchKecamatan();
            fetchKelurahan();
            fetchICD10();
            fetchICD9();
        <?php else: ?>
            $('#loadingSpinner').hide();
        <?php endif; ?>
    });
</script>
<?= $this->endSection(); ?>
<?= $this->section('chartjs'); ?>
<script>
    // Array to keep track of chart instances
    const chartInstances = [];

    // Function to initialize a chart and add it to the instances array
    function createChart(ctx, config) {
        const chart = new Chart(ctx, config);
        chartInstances.push(chart);
        return chart;
    }

    // Function to update chart configurations based on the color scheme
    function updateChartOptions() {
        // Cek apakah data-bs-theme ada dan bernilai "dark"
        const themeAttribute = document.documentElement.getAttribute("data-bs-theme");
        const isDarkMode = themeAttribute === "dark";

        const colorSettings = {
            color: isDarkMode ? "#FFFFFF" : "#000000",
            borderColor: isDarkMode ? "rgba(255,255,255,0.1)" : "rgba(0,0,0,0.1)",
            backgroundColor: isDarkMode ? "rgba(255,255,0,0.1)" : "rgba(0,255,0,0.1)",
            lineBorderColor: isDarkMode ? "rgba(255,255,0,0.4)" : "rgba(0,255,0,0.4)",
            gridColor: isDarkMode ? "rgba(255,255,255,0.2)" : "rgba(0,0,0,0.2)"
        };

        chartInstances.forEach(chart => {
            if (chart.options.scales) {
                // Update X-axis
                if (chart.options.scales.x) {
                    if (chart.options.scales.x.ticks) {
                        chart.options.scales.x.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.title) {
                        chart.options.scales.x.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.grid) {
                        chart.options.scales.x.grid.color = colorSettings.gridColor;
                    }
                }

                // Update Y-axis
                if (chart.options.scales.y) {
                    if (chart.options.scales.y.ticks) {
                        chart.options.scales.y.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.title) {
                        chart.options.scales.y.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.grid) {
                        chart.options.scales.y.grid.color = colorSettings.gridColor;
                    }
                }
            }

            // Update line chart specific settings
            if (chart.options.elements && chart.options.elements.line) {
                chart.options.elements.line.borderColor = colorSettings.lineBorderColor;
            }

            // Update doughnut and pie chart legend
            if ((chart.config.type === 'doughnut' || chart.config.type === 'pie') && chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels.color = colorSettings.color;
            }

            // Redraw the chart with updated settings
            chart.update();
        });
    }
    Chart.defaults.font.family = '"Helvetica Neue", Helvetica, Arial, Arimo, "Liberation Sans", sans-serif';

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        const data_antreanpiegraph = [];
        const label_antreanpiegraph = [];
        const data_antreankodegraph = [];
        const label_antreankodegraph = [];
        const data_antreangraph = [];
        const label_antreangraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        const data_agamagraph = [];
        const label_agamagraph = [];
        const data_jeniskelamingraph = [];
        const label_jeniskelamingraph = [];
        const data_rawatjalanpiegraph = [];
        const label_rawatjalanpiegraph = [];
        const data_rawatjalandoktergraph = [];
        const label_rawatjalandoktergraph = [];
        const data_rawatjalangraph = [];
        const label_rawatjalangraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        const data_resepbydoktergraph = [];
        const label_resepbydoktergraph = [];
        const data_resepallgraph = [];
        const label_resepallgraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        const data_transaksibykasirgraph = [];
        const label_transaksibykasirgraph = [];
        const data_transaksiperbulanallgraph = [];
        const label_transaksiperbulanallgraph = [];
        const data_pemasukanperbulangraph = [];
        const label_pemasukanperbulangraph = [];
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        <?php foreach ($antreanpiegraph->getResult() as $key => $antreanpiegraph) : ?>
            data_antreanpiegraph.push(<?= $antreanpiegraph->total_antrean; ?>);
            label_antreanpiegraph.push('<?= $antreanpiegraph->nama_jaminan; ?>');
        <?php endforeach; ?>
        <?php foreach ($antreangraph->getResult() as $key => $antreangraph) : ?>
            data_antreangraph.push(<?= $antreangraph->total_antrean; ?>);
            label_antreangraph.push('<?= $antreangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        <?php foreach ($agamagraph->getResult() as $key => $agamagraph) :
            // Query untuk mencocokkan ID agama dengan nama agama
            $agamaId = $agamagraph->agama;
            $query = $db->table('master_agama')
                ->select('agamaNama')
                ->where('agamaId', $agamaId)
                ->get();

            // Ambil nama agama
            $agamaNama = $query->getRow();

            if ($agamaNama) {
                // Ambil nama kelurahan jika ditemukan
                $agamaNama = $agamaNama->agamaNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $agamaNama = 'Tidak Ada';
            } ?>
            data_agamagraph.push(<?= $agamagraph->total_agama; ?>);
            label_agamagraph.push('<?= htmlspecialchars($agamaNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($jeniskelamingraph->getResult() as $key => $jeniskelamingraph) : ?>
            data_jeniskelamingraph.push(<?= $jeniskelamingraph->total_jeniskelamin; ?>);
            <?php
            $jenisKelamin = $jeniskelamingraph->jenis_kelamin;
            if ($jenisKelamin == 'L') {
                $jenisKelamin = 'Laki-Laki';
            } elseif ($jenisKelamin == 'P') {
                $jenisKelamin = 'Perempuan';
            } elseif ($jenisKelamin === NULL) {
                $jenisKelamin = 'Tidak Ada';
            }
            ?>
            label_jeniskelamingraph.push('<?= $jenisKelamin; ?>');
        <?php endforeach; ?>
        <?php foreach ($rawatjalanpiegraph->getResult() as $key => $rawatjalanpiegraph) : ?>
            data_rawatjalanpiegraph.push(<?= $rawatjalanpiegraph->total_rajal; ?>);
            label_rawatjalanpiegraph.push('<?= $rawatjalanpiegraph->dokter; ?>');
        <?php endforeach; ?>
        <?php foreach ($rawatjalangraph->getResult() as $key => $rawatjalangraph) : ?>
            data_rawatjalangraph.push(<?= $rawatjalangraph->total_rajal; ?>);
            label_rawatjalangraph.push('<?= $rawatjalangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        <?php foreach ($resepbydoktergraph->getResult() as $key => $resepbydoktergraph) : ?>
            data_resepbydoktergraph.push(<?= $resepbydoktergraph->jumlah; ?>);
            label_resepbydoktergraph.push('<?= $resepbydoktergraph->dokter; ?>');
        <?php endforeach; ?>
        <?php foreach ($resepallgraph->getResult() as $key => $resepallgraph) : ?>
            data_resepallgraph.push(<?= $resepallgraph->total_resep; ?>);
            label_resepallgraph.push('<?= $resepallgraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        <?php foreach ($transaksibykasirgraph->getResult() as $key => $transaksibykasirgraph) : ?>
            data_transaksibykasirgraph.push(<?= $transaksibykasirgraph->jumlah; ?>);
            label_transaksibykasirgraph.push('<?= $transaksibykasirgraph->kasir; ?>');
        <?php endforeach; ?>
        <?php foreach ($transaksiperbulanallgraph->getResult() as $key => $transaksiperbulanallgraph) : ?>
            data_transaksiperbulanallgraph.push(<?= $transaksiperbulanallgraph->total_transaksi; ?>);
            label_transaksiperbulanallgraph.push('<?= $transaksiperbulanallgraph->bulan; ?>');
        <?php endforeach; ?>
        <?php foreach ($pemasukanperbulangraph->getResult() as $key => $pemasukanperbulangraph) : ?>
            data_pemasukanperbulangraph.push(<?= $pemasukanperbulangraph->total_pemasukan; ?>);
            label_pemasukanperbulangraph.push('<?= $pemasukanperbulangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        var data_content_antreanpiegraph = {
            labels: label_antreanpiegraph,
            datasets: [{
                label: 'Total Antrean',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_antreanpiegraph
            }]
        }
        var data_content_antreankodegraph = {
            labels: <?= $labels_antreankode ?>,
            datasets: <?= $datasets_antreankode ?>
        }
        var data_content_antreangraph = {
            labels: label_antreangraph,
            datasets: [{
                label: 'Total Antrean',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_antreangraph
            }]
        }
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        var data_content_agamagraph = {
            labels: label_agamagraph,
            datasets: [{
                label: 'Total Pasien',
                borderWidth: 2,
                borderRadius: 10,
                fill: true,
                data: data_agamagraph
            }]
        }
        var data_content_jeniskelamingraph = {
            labels: label_jeniskelamingraph,
            datasets: [{
                label: 'Total Pasien',
                borderWidth: 2,
                borderRadius: 10,
                fill: true,
                data: data_jeniskelamingraph
            }]
        }
        var data_content_rawatjalanpiegraph = {
            labels: label_rawatjalanpiegraph,
            datasets: [{
                label: 'Total Rawat Jalan',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_rawatjalanpiegraph
            }]
        }
        var data_content_rawatjalandoktergraph = {
            labels: <?= $labels_rawatjalandokter ?>,
            datasets: <?= $datasets_rawatjalandokter ?>
        }
        var data_content_rawatjalangraph = {
            labels: label_rawatjalangraph,
            datasets: [{
                label: 'Total Rawat Jalan',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_rawatjalangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        var data_content_resepbydoktergraph = {
            labels: label_resepbydoktergraph,
            datasets: [{
                label: 'Total Resep',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_resepbydoktergraph
            }]
        }
        var data_content_resepgraph = {
            labels: <?= $labels_resep ?>,
            datasets: <?= $datasets_resep ?>
        }
        var data_content_resepallgraph = {
            labels: label_resepallgraph,
            datasets: [{
                label: 'Total Resep',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_resepallgraph
            }]
        }
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        var data_content_transaksibykasirgraph = {
            labels: label_transaksibykasirgraph,
            datasets: [{
                label: 'Total Transaksi',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_transaksibykasirgraph
            }]
        }
        var data_content_transaksiperbulanallgraph = {
            labels: label_transaksiperbulanallgraph,
            datasets: [{
                label: 'Total Transaksi',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_transaksiperbulanallgraph
            }]
        }
        var data_content_transaksiperbulangraph = {
            labels: <?= $labels_transaksi ?>,
            datasets: <?= $datasets_transaksi ?>
        }
        var data_content_pemasukanperbulangraph = {
            labels: label_pemasukanperbulangraph,
            datasets: [{
                label: 'Total Pemasukan (Rp)',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_pemasukanperbulangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
        var chart_antreanpiegraph = createChart(document.getElementById('antreanpiegraph').getContext('2d'), {
            type: 'pie',
            data: data_content_antreanpiegraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_antreankodegraph = createChart(document.getElementById('antreankodegraph').getContext('2d'), {
            type: 'line',
            data: data_content_antreankodegraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Antrean'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_antreangraph = createChart(document.getElementById('antreangraph').getContext('2d'), {
            type: 'line',
            data: data_content_antreangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Antrean'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        var chart_agamagraph = createChart(document.getElementById('agamagraph').getContext('2d'), {
            type: 'bar',
            data: data_content_agamagraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Agama'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pasien'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_jeniskelamingraph = createChart(document.getElementById('jeniskelamingraph').getContext('2d'), {
            type: 'bar',
            data: data_content_jeniskelamingraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Jenis Kelamin'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pasien'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_rawatjalanpiegraph = createChart(document.getElementById('rawatjalanpiegraph').getContext('2d'), {
            type: 'pie',
            data: data_content_rawatjalanpiegraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_rawatjalandoktergraph = createChart(document.getElementById('rawatjalandoktergraph').getContext('2d'), {
            type: 'line',
            data: data_content_rawatjalandoktergraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Rawat Jalan'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_rawatjalangraph = createChart(document.getElementById('rawatjalangraph').getContext('2d'), {
            type: 'line',
            data: data_content_rawatjalangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Rawat Jalan'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        var chart_resepbydoktergraph = createChart(document.getElementById('resepbydoktergraph').getContext('2d'), {
            type: 'pie',
            data: data_content_resepbydoktergraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_resepgraph = createChart(document.getElementById('resepgraph').getContext('2d'), {
            type: 'line',
            data: data_content_resepgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Resep yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_resepallgraph = createChart(document.getElementById('resepallgraph').getContext('2d'), {
            type: 'line',
            data: data_content_resepallgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Resep yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        var chart_transaksibykasirgraph = createChart(document.getElementById('transaksibykasirgraph').getContext('2d'), {
            type: 'pie',
            data: data_content_transaksibykasirgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_transaksiperbulangraph = createChart(document.getElementById('transaksiperbulangraph').getContext('2d'), {
            type: 'line',
            data: data_content_transaksiperbulangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Transaksi yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_transaksiperbulanallgraph = createChart(document.getElementById('transaksiperbulanallgraph').getContext('2d'), {
            type: 'line',
            data: data_content_transaksiperbulanallgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Transaksi yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_pemasukanperbulangraph = createChart(document.getElementById('pemasukanperbulangraph').getContext('2d'), {
            type: 'line',
            data: data_content_pemasukanperbulangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Pemasukan (Rp)'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>

    // Initial setup
    updateChartOptions();

    // Watch for changes in color scheme preference
    const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQueryList.addEventListener("change", () => {
        updateChartOptions();
    });
</script>
<?= $this->endSection(); ?>