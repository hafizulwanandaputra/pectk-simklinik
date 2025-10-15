<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<style>
    /* Ensures the dropdown is visible outside the parent with overflow auto */
    .select2-container {
        z-index: 1050;
        /* Make sure it's above other elements, like modals */
    }

    .select2-dropdown {
        position: absolute !important;
        /* Ensures placement isn't affected by overflow */
        z-index: 1050;
    }

    .garis {
        width: 50%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        justify-content: center;
        transition: transform 1s ease-in-out, opacity 1s ease-in-out;
    }

    .isi-garis {
        height: 2px;
        /* Bisa diubah tanpa mengganggu posisi */
        width: 70%;
        background-color: black;
    }

    .garis.start-0 {
        transform: translateY(-50%) rotate(0deg);
        opacity: 0;
    }

    .garis.end-0 {
        transform: translateY(-50%) rotate(0deg);
        opacity: 0;
    }

    [data-bs-theme=dark] .isi-garis {
        background-color: white;
    }

    [data-bs-theme=dark] #kacamata {
        filter: invert(1);
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/rawatjalan'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $rawatjalan['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/optik/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/optik/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top px-2 pt-2" style="z-index: 99;">
        <ul class="list-group no-fluid-content-list-group shadow-sm border border-bottom-0">
            <li class="list-group-item px-2 border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-pills nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <?php endif; ?>
                        <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('rawatjalan/optik/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/laporanrajal/' . $rawatjalan['id_rawat_jalan']); ?>">Tindakan Rajal</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/layanan/' . $rawatjalan['id_rawat_jalan']); ?>">Layanan</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </li>
            <li class="list-group-item px-2 border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-pills flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink ' . ((date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'bg-danger text-white' : '') : '' ?>" href="<?= base_url('rawatjalan/optik/' . $list['id_rawat_jalan']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm date" style="font-size: 0.75em;"><?= $list['tanggal_registrasi'] ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <?= form_open_multipart('/rawatjalan/optik/update/' . $optik['id_optik'], 'id="optikForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (session()->get('role') == 'Dokter') : ?>
                <?php if ($rawatjalan['dokter'] != session()->get('fullname')) : ?>
                    <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                Saat ini Anda melihat resep kacamata yang diberikan oleh <?= $rawatjalan['dokter'] ?>. Pengisian resep kacamata hanya dapat dilakukan apabila sesuai dengan DPJP yang masuk pada sistem ini.
                            </div>
                            <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="d-flex flex-column flex-lg-row justify-content-lg-center mb-2">
                <div class="d-flex justify-content-center">
                    <figure class="figure mb-0 mx-3 position-relative">
                        <img src="<?= base_url('assets/images/kacamata.png') ?>" width="512px" id="kacamata" class="figure-img img-fluid mb-0 pb-0" alt="Kacamata">
                        <!-- Garisan Axis -->
                        <div class="position-absolute top-50 start-0 translate-middle-y w-100">
                            <!-- OD -->
                            <div class="garis position-absolute start-0">
                                <div class="isi-garis"></div>
                            </div>
                            <!-- OS -->
                            <div class="garis position-absolute end-0">
                                <div class="isi-garis"></div>
                            </div>
                        </div>
                    </figure>
                </div>
                <div class="row g-1 radio-group">
                    <div class="col col-form-label">
                        <div class="d-flex flex-row flex-lg-column justify-content-between">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" disabled type="radio" name="tipe_lensa" id="tipe_lensa1" value="TRIFOCUS">
                                <label class="form-check-label text-nowrap" for="tipe_lensa1">
                                    Trifocus
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" disabled type="radio" name="tipe_lensa" id="tipe_lensa2" value="BIFOCUS">
                                <label class="form-check-label text-nowrap" for="tipe_lensa2">
                                    Bifocus
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" disabled type="radio" name="tipe_lensa" id="tipe_lensa3" value="SINGLE FOCUS">
                                <label class="form-check-label text-nowrap" for="tipe_lensa3">
                                    Single Focus
                                </label>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="card overflow-auto mt-3 shadow-sm">
                <div class="table-responsive">
                    <table class="table-sm" style="font-size: 0.75em;">
                        <thead>
                            <tr>
                                <th class="border-end"></th>
                                <th colspan="5" class="text-center border-end">
                                    <h2 class="mb-0 pt-0">O.D</h2>
                                </th>
                                <th colspan="5" class="text-center border-start border-end">
                                    <h2 class="mb-0 pt-0">O.S</h2>
                                </th>
                                <th colspan="2" class="text-center border-start">
                                </th>
                            </tr>
                            <tr>
                                <th class="border-bottom border-end"></th>
                                <th style="min-width: 60px;" class="text-center border-bottom border-start">Vitrum Spher</th>
                                <th style="min-width: 60px;" class="text-center border-bottom">Vitrum Cyldr</th>
                                <th style="min-width: 60px;" class="text-center border-bottom"><span data-bs-toggle="tooltip" data-bs-title="Jika nilai ini tidak ada, garis axis tidak digambarkan di lembar resep kacamata.">Axis</span></th>
                                <th style="min-width: 60px;" class="text-center border-bottom">Prisma</th>
                                <th style="min-width: 60px;" class="text-center border-bottom border-end">Basis</th>
                                <th style="min-width: 60px;" class="text-center border-bottom border-start">Vitrum Spher</th>
                                <th style="min-width: 60px;" class="text-center border-bottom">Vitrum Cyldr</th>
                                <th style="min-width: 60px;" class="text-center border-bottom"><span data-bs-toggle="tooltip" data-bs-title="Jika nilai ini tidak ada, garis axis tidak digambarkan di lembar resep kacamata.">Axis</span></th>
                                <th style="min-width: 60px;" class="text-center border-bottom">Prisma</th>
                                <th style="min-width: 60px;" class="text-center border-bottom border-end">Basis</th>
                                <th style="min-width: 60px;" class="text-center border-bottom border-start">Golor Vitror</th>
                                <th style="min-width: 60px;" class="text-center border-bottom">Distant Pupil</th>
                            </tr>
                            <tr>
                                <th class="text-center border-end">Pro Login Quitat</th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_login_spher" name="od_login_spher" list="od_login_spher_list">
                                    <datalist id="od_login_spher_list">
                                        <option value="+3.00"></option>
                                        <option value="+2.75"></option>
                                        <option value="+2.50"></option>
                                        <option value="+2.25"></option>
                                        <option value="+2.00"></option>
                                        <option value="+1.75"></option>
                                        <option value="+1.50"></option>
                                        <option value="+1.25"></option>
                                        <option value="+1.00"></option>
                                        <option value="+0.75"></option>
                                        <option value="+0.50"></option>
                                        <option value="+0.25"></option>
                                        <option value="Plano"></option>
                                        <option value="-0.25"></option>
                                        <option value="-0.50"></option>
                                        <option value="-0.75"></option>
                                        <option value="-1.00"></option>
                                        <option value="-1.25"></option>
                                        <option value="-1.50"></option>
                                        <option value="-1.75"></option>
                                        <option value="-2.00"></option>
                                        <option value="-2.25"></option>
                                        <option value="-2.50"></option>
                                        <option value="-2.75"></option>
                                        <option value="-3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_login_cyldr" name="od_login_cyldr" list="od_login_cyldr_list">
                                    <datalist id="od_login_cyldr_list">
                                        <option value="+3.00"></option>
                                        <option value="+2.75"></option>
                                        <option value="+2.50"></option>
                                        <option value="+2.25"></option>
                                        <option value="+2.00"></option>
                                        <option value="+1.75"></option>
                                        <option value="+1.50"></option>
                                        <option value="+1.25"></option>
                                        <option value="+1.00"></option>
                                        <option value="+0.75"></option>
                                        <option value="+0.50"></option>
                                        <option value="+0.25"></option>
                                        <option value="Plano"></option>
                                        <option value="-0.25"></option>
                                        <option value="-0.50"></option>
                                        <option value="-0.75"></option>
                                        <option value="-1.00"></option>
                                        <option value="-1.25"></option>
                                        <option value="-1.50"></option>
                                        <option value="-1.75"></option>
                                        <option value="-2.00"></option>
                                        <option value="-2.25"></option>
                                        <option value="-2.50"></option>
                                        <option value="-2.75"></option>
                                        <option value="-3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_login_axis" name="od_login_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_login_prisma" name="od_login_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_login_basis" name="od_login_basis">
                                </th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_spher" name="os_login_spher" list="os_login_spher_list">
                                    <datalist id="os_login_spher_list">
                                        <option value="+3.00"></option>
                                        <option value="+2.75"></option>
                                        <option value="+2.50"></option>
                                        <option value="+2.25"></option>
                                        <option value="+2.00"></option>
                                        <option value="+1.75"></option>
                                        <option value="+1.50"></option>
                                        <option value="+1.25"></option>
                                        <option value="+1.00"></option>
                                        <option value="+0.75"></option>
                                        <option value="+0.50"></option>
                                        <option value="+0.25"></option>
                                        <option value="Plano"></option>
                                        <option value="-0.25"></option>
                                        <option value="-0.50"></option>
                                        <option value="-0.75"></option>
                                        <option value="-1.00"></option>
                                        <option value="-1.25"></option>
                                        <option value="-1.50"></option>
                                        <option value="-1.75"></option>
                                        <option value="-2.00"></option>
                                        <option value="-2.25"></option>
                                        <option value="-2.50"></option>
                                        <option value="-2.75"></option>
                                        <option value="-3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_cyldr" name="os_login_cyldr" list="os_login_cyldr_list">
                                    <datalist id="os_login_cyldr_list">
                                        <option value="+3.00"></option>
                                        <option value="+2.75"></option>
                                        <option value="+2.50"></option>
                                        <option value="+2.25"></option>
                                        <option value="+2.00"></option>
                                        <option value="+1.75"></option>
                                        <option value="+1.50"></option>
                                        <option value="+1.25"></option>
                                        <option value="+1.00"></option>
                                        <option value="+0.75"></option>
                                        <option value="+0.50"></option>
                                        <option value="+0.25"></option>
                                        <option value="Plano"></option>
                                        <option value="-0.25"></option>
                                        <option value="-0.50"></option>
                                        <option value="-0.75"></option>
                                        <option value="-1.00"></option>
                                        <option value="-1.25"></option>
                                        <option value="-1.50"></option>
                                        <option value="-1.75"></option>
                                        <option value="-2.00"></option>
                                        <option value="-2.25"></option>
                                        <option value="-2.50"></option>
                                        <option value="-2.75"></option>
                                        <option value="-3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_axis" name="os_login_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_prisma" name="os_login_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_basis" name="os_login_basis">
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_vitror" name="os_login_vitror">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_login_pupil" name="os_login_pupil">
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center border-end">Pro Domo</th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_domo_spher" name="od_domo_spher">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_domo_cyldr" name="od_domo_cyldr">
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_domo_axis" name="od_domo_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_domo_prisma" name="od_domo_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_domo_basis" name="od_domo_basis">
                                </th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_spher" name="os_domo_spher">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_cyldr" name="os_domo_cyldr">
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_axis" name="os_domo_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_prisma" name="os_domo_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_basis" name="os_domo_basis">
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_vitror" name="os_domo_vitror">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_domo_pupil" name="os_domo_pupil">
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center border-end">Propin Quitat</th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_quitat_spher" name="od_quitat_spher" list="od_quitat_spher_list">
                                    <datalist id="od_quitat_spher_list">
                                        <option value="Add +1.00"></option>
                                        <option value="Add +1.25"></option>
                                        <option value="Add +1.50"></option>
                                        <option value="Add +1.75"></option>
                                        <option value="Add +2.00"></option>
                                        <option value="Add +2.25"></option>
                                        <option value="Add +2.50"></option>
                                        <option value="Add +2.75"></option>
                                        <option value="Add +3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_quitat_cyldr" name="od_quitat_cyldr">
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_quitat_axis" name="od_quitat_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_quitat_prisma" name="od_quitat_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="od_quitat_basis" name="od_quitat_basis">
                                </th>
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_spher" name="os_quitat_spher" list="os_quitat_spher_list">
                                    <datalist id="os_quitat_spher_list">
                                        <option value="Add +1.00"></option>
                                        <option value="Add +1.25"></option>
                                        <option value="Add +1.50"></option>
                                        <option value="Add +1.75"></option>
                                        <option value="Add +2.00"></option>
                                        <option value="Add +2.25"></option>
                                        <option value="Add +2.50"></option>
                                        <option value="Add +2.75"></option>
                                        <option value="Add +3.00"></option>
                                    </datalist>
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_cyldr" name="os_quitat_cyldr">
                                </th>
                                <th>
                                    <input type="number" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_axis" name="os_quitat_axis">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_prisma" name="os_quitat_prisma">
                                </th>
                                <th class="border-end">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_basis" name="os_quitat_basis">
                                <th class="border-start">
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_vitror" name="os_quitat_vitror">
                                </th>
                                <th>
                                    <input type="text" readonly class="form-control eyeglass-inputs form-control-sm m-0" style="height: 60px;" id="os_quitat_pupil" name="os_quitat_pupil">
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="mb-3">
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/optik/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Resep</button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn" disabled><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchOptik() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/optik/view/') . $optik['id_optik'] ?>');
            const data = response.data;
            const dokter = data.dokter;
            const tipe_lensa = data.tipe_lensa;

            <?php if (session()->get('role') == 'Dokter') : ?>
                <?php if ($rawatjalan['dokter'] != session()->get('fullname')) : ?>
                    $("input[name='tipe_lensa']").prop('disabled', true);
                    $(".eyeglass-inputs").prop('readonly', true);
                    $("#submitBtn").prop('disabled', true);
                <?php else : ?>
                    $("input[name='tipe_lensa']").prop('disabled', false);
                    $(".eyeglass-inputs").prop('readonly', false);
                    $("#submitBtn").prop('disabled', false);
                <?php endif; ?>
            <?php else : ?>
                $("input[name='tipe_lensa']").prop('disabled', false);
                $(".eyeglass-inputs").prop('readonly', false);
                $("#submitBtn").prop('disabled', false);
            <?php endif; ?>

            if (tipe_lensa) {
                $("input[name='tipe_lensa'][value='" + tipe_lensa + "']").prop('checked', true);
            }

            $('#od_login_spher').val(data.od_login_spher);
            $('#od_login_cyldr').val(data.od_login_cyldr);
            $('#od_login_axis').val(data.od_login_axis);
            $('#od_login_prisma').val(data.od_login_prisma);
            $('#od_login_basis').val(data.od_login_basis);

            $('#od_domo_spher').val(data.od_domo_spher);
            $('#od_domo_cyldr').val(data.od_domo_cyldr);
            $('#od_domo_axis').val(data.od_domo_axis);
            $('#od_domo_prisma').val(data.od_domo_prisma);
            $('#od_domo_basis').val(data.od_domo_basis);

            $('#od_quitat_spher').val(data.od_quitat_spher);
            $('#od_quitat_cyldr').val(data.od_quitat_cyldr);
            $('#od_quitat_axis').val(data.od_quitat_axis);
            $('#od_quitat_prisma').val(data.od_quitat_prisma);
            $('#od_quitat_basis').val(data.od_quitat_basis);

            $('#os_login_spher').val(data.os_login_spher);
            $('#os_login_cyldr').val(data.os_login_cyldr);
            $('#os_login_axis').val(data.os_login_axis);
            $('#os_login_prisma').val(data.os_login_prisma);
            $('#os_login_basis').val(data.os_login_basis);
            $('#os_login_vitror').val(data.os_login_vitror);
            $('#os_login_pupil').val(data.os_login_pupil);

            $('#os_domo_spher').val(data.os_domo_spher);
            $('#os_domo_cyldr').val(data.os_domo_cyldr);
            $('#os_domo_axis').val(data.os_domo_axis);
            $('#os_domo_prisma').val(data.os_domo_prisma);
            $('#os_domo_basis').val(data.os_domo_basis);
            $('#os_domo_vitror').val(data.os_domo_vitror);
            $('#os_domo_pupil').val(data.os_domo_pupil);

            $('#os_quitat_spher').val(data.os_quitat_spher);
            $('#os_quitat_cyldr').val(data.os_quitat_cyldr);
            $('#os_quitat_axis').val(data.os_quitat_axis);
            $('#os_quitat_prisma').val(data.os_quitat_prisma);
            $('#os_quitat_basis').val(data.os_quitat_basis);
            $('#os_quitat_vitror').val(data.os_quitat_vitror);
            $('#os_quitat_pupil').val(data.os_quitat_pupil);

            const od_login_axis = data.od_login_axis;
            if (od_login_axis) {
                $(".garis.start-0").css("transform", `translateY(-50%) rotate(-${od_login_axis}deg)`).css("opacity", "1");
            }
            const os_login_axis = data.os_login_axis;
            if (os_login_axis) {
                $(".garis.end-0").css("transform", `translateY(-50%) rotate(-${os_login_axis}deg)`).css("opacity", "1");
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }
    $(document).ready(function() {
        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });
        $('#od_login_axis').on('input', function() {
            let value = $(this).val();
            if (value === '') {
                $(".garis.start-0").css("transform", `translateY(-50%) rotate(-0deg)`).css("opacity", "0");
            } else {
                $(".garis.start-0").css("transform", `translateY(-50%) rotate(-${value}deg)`).css("opacity", "1");
            }
        });
        $('#os_login_axis').on('input', function() {
            let value = $(this).val();
            if (value === '') {
                $(".garis.end-0").css("transform", `translateY(-50%) rotate(-0deg)`).css("opacity", "0");
            } else {
                $(".garis.end-0").css("transform", `translateY(-50%) rotate(-${value}deg)`).css("opacity", "1");
            }
        });
        $('#optikForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#optikForm .is-invalid').removeClass('is-invalid');
            $('#optikForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#optikForm input, #optikForm select, #optikForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/optik/update/' . $optik['id_optik']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchOptik();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#optikForm .is-invalid').removeClass('is-invalid');
                    $('#optikForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['tipe_lensa'].includes(field)) {
                                const radioGroup = $(`input[name='${field}']`); // Ambil grup radio berdasarkan nama
                                const feedbackElement = radioGroup.closest('.radio-group').find('.invalid-feedback'); // Gunakan pembungkus dengan class tertentu

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        radioGroup.removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Radio group tidak ditemukan untuk field:", field);
                                }
                            } else {
                                let feedbackElement = fieldElement.siblings('.invalid-feedback');

                                // Handle input-group cases
                                if (fieldElement.closest('.input-group').length) {
                                    feedbackElement = fieldElement.closest('.input-group').find('.invalid-feedback');
                                }

                                if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                    fieldElement.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user corrects the input
                                    fieldElement.on('input change', function() {
                                        $(this).removeClass('is-invalid');
                                        $(this).siblings('.invalid-feedback').text('').hide();
                                    });
                                } else {
                                    console.warn("Elemen tidak ditemukan pada field:", field);
                                }
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                if (error.response.request.status === 422 || error.response.request.status === 400) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#optikForm input, #optikForm select, #optikForm button').prop('disabled', false);
            }
        });
        fetchOptik();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>