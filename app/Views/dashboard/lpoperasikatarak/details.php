<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($lp_operasi_katarak['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($lp_operasi_katarak['tanggal_registrasi'])));

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

    #site_marking_canvas {
        cursor: crosshair;
    }

    .canvas-container::-webkit-scrollbar {
        width: 16px;
        height: 16px;
    }

    .canvas-container::-webkit-scrollbar-track {
        background-color: var(--bs-secondary-bg);
    }

    .canvas-container::-webkit-scrollbar-thumb {
        background-color: var(--bs-secondary);
    }

    .canvas-container::-webkit-scrollbar-thumb:hover {
        background-color: var(--bs-body-color);
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/lpoperasikatarak'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $lp_operasi_katarak['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $lp_operasi_katarak['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('lpoperasikatarak/details/' . $previous['id_lp_operasi_katarak']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi katarak sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('lpoperasikatarak/details/' . $next['id_lp_operasi_katarak']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi katarak berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 <?= ($activeSegment === $list['id_lp_operasi_katarak']) ? 'active activeLink' : '' ?>" href="<?= base_url('lpoperasikatarak/details/' . $list['id_lp_operasi_katarak']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm" style="font-size: 0.75em;"><?= $list['tanggal_registrasi'] ?></div>
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
            <?= form_open_multipart('lpoperasikatarak/update/' . $lp_operasi_katarak['id_lp_operasi_katarak'], 'id="LaporanForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="mata" class="col col-form-label">Mata<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mata" id="mata1" value="OD">
                                    <label class="form-check-label" for="mata1">
                                        OD
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mata" id="mata2" value="OS">
                                    <label class="form-check-label" for="mata2">
                                        OS
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="operator" name="operator" aria-label="operator">
                            <option value="" disabled selected>-- Pilih Dokter Operator --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="operator">Dokter Operator<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-3 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="tanggal_operasi" name="tanggal_operasi" value="" autocomplete="off" dir="auto" placeholder="tanggal_operasi">
                            <label for="tanggal_operasi">Tanggal Operasi<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="time" class="form-control" id="jam_operasi" name="jam_operasi" value="" autocomplete="off" dir="auto" placeholder="jam_operasi">
                            <label for="jam_operasi">Jam Operasi<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="lama_operasi" name="lama_operasi" value="" autocomplete="off" dir="auto" placeholder="lama_operasi">
                            <label for="lama_operasi">Lama Operasi (menit)<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" value="" autocomplete="off" dir="auto" placeholder="diagnosis">
                        <label for="diagnosis">Diagnosis<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="asisten" name="asisten" aria-label="asisten">
                            <option value="" disabled selected>-- Pilih Asisten --</option>
                            <?php foreach ($asisten as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="asisten">Asisten<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="jenis_operasi" name="jenis_operasi" value="" autocomplete="off" dir="auto" placeholder="jenis_operasi">
                        <label for="jenis_operasi">Jenis Operasi<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="jenis_anastesi" class="col col-form-label">Jenis Anestesi<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_anastesi" id="jenis_anastesi1" value="UMUM">
                                    <label class="form-check-label" for="jenis_anastesi1">
                                        Umum
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_anastesi" id="jenis_anastesi2" value="SPINAL">
                                    <label class="form-check-label" for="jenis_anastesi2">
                                        Spinal
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_anastesi" id="jenis_anastesi3" value="EPIDURAL">
                                    <label class="form-check-label" for="jenis_anastesi3">
                                        Epidural
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_anastesi" id="jenis_anastesi4" value="LOKAL">
                                    <label class="form-check-label" for="jenis_anastesi4">
                                        Lokal
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="dokter_anastesi" name="dokter_anastesi" aria-label="dokter_anastesi">
                            <option value="" disabled selected>-- Pilih Anestesiologis --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="dokter_anastesi">Anestesiologis<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Anestesi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_retrobulbar" class="w-100">
                                        <div>Retrobulbar</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_retrobulbar" name="anastesi_retrobulbar" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_peribulber" class="w-100">
                                        <div>Peribulbar</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_peribulber" name="anastesi_peribulber" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_topikal" class="w-100">
                                        <div>Topikal</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_topikal" name="anastesi_topikal" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_subtenom" class="w-100">
                                        <div>Subtenom</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_subtenom" name="anastesi_subtenom" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_lidocain_2" class="w-100">
                                        <div>Lidocain 2%</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_lidocain_2" name="anastesi_lidocain_2" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="anastesi_marcain_05" class="w-100">
                                        <div>Marcain 0,5%</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anastesi_marcain_05" name="anastesi_marcain_05" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label for="anastesi_lainnya" class="w-100">
                                        <div>Lainnya</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="anastesi_lainnya" name="anastesi_lainnya" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Peritomi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="peritomi_basis_forniks" class="w-100">
                                        <div>Basis Forniks</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="peritomi_basis_forniks" name="peritomi_basis_forniks" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="peritomi_basis_limbus" class="w-100">
                                        <div>Basis Limbus</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="peritomi_basis_limbus" name="peritomi_basis_limbus" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Lokasi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_superonasal" class="w-100">
                                        <div>Superonasal</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_superonasal" name="lokasi_superonasal" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_superior" class="w-100">
                                        <div>Superior</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_superior" name="lokasi_superior" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_supertemporal" class="w-100">
                                        <div>Supertemporal</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_supertemporal" name="lokasi_supertemporal" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label for="lokasi_lainnya" class="w-100">
                                        <div>Lainnya</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="lokasi_lainnya" name="lokasi_lainnya" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Lokasi Insisi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_insisi_kornea" class="w-100">
                                        <div>Kornea</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_insisi_kornea" name="lokasi_insisi_kornea" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_insisi_limbus" class="w-100">
                                        <div>Limbus</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_insisi_limbus" name="lokasi_insisi_limbus" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_insisi_skelera" class="w-100">
                                        <div>Skelera</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_insisi_skelera" name="lokasi_insisi_skelera" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_insisi_skeleratunnel" class="w-100">
                                        <div><em>Skelera Tunnel</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_insisi_skeleratunnel" name="lokasi_insisi_skeleratunnel" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="lokasi_insisi_sideport" class="w-100">
                                        <div><em>Side Port</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lokasi_insisi_sideport" name="lokasi_insisi_sideport" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ukuran_inisiasi" name="ukuran_inisiasi" value="" autocomplete="off" dir="auto" placeholder="ukuran_inisiasi">
                        <label for="ukuran_inisiasi">Ukuran Insisi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Alat Insisi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="alat_insisi_jarum" class="w-100">
                                        <div>Jarum</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="alat_insisi_jarum" name="alat_insisi_jarum" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="alat_insisi_crescent" class="w-100">
                                        <div><em>Crescent</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="alat_insisi_crescent" name="alat_insisi_crescent" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="alat_insisi_diamond" class="w-100">
                                        <div><em>Diamond</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="alat_insisi_diamond" name="alat_insisi_diamond" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Kapsulotomi Anterior</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="capsulectomy_canopener" class="w-100">
                                        <div><em>Can Opener</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="capsulectomy_canopener" name="capsulectomy_canopener" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="capsulectomy_envelope" class="w-100">
                                        <div><em>Envelope</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="capsulectomy_envelope" name="capsulectomy_envelope" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="capsulectomy_ccc" class="w-100">
                                        <div>CCC</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="capsulectomy_ccc" name="capsulectomy_ccc" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Ekstraksi Lensa</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_icce" class="w-100">
                                        <div>ICCE</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_icce" name="ekstraksi_lenca_icce" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_ecce" class="w-100">
                                        <div>ECCE</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_ecce" name="ekstraksi_lenca_ecce" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_sucea" class="w-100">
                                        <div>SUCEA</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_sucea" name="ekstraksi_lenca_sucea" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_phaco" class="w-100">
                                        <div>Phaco</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_phaco" name="ekstraksi_lenca_phaco" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_cle" class="w-100">
                                        <div>CLE</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_cle" name="ekstraksi_lenca_cle" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="ekstraksi_lenca_ai" class="w-100">
                                        <div>AI</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ekstraksi_lenca_ai" name="ekstraksi_lenca_ai" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Tindakan Tambahan</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="tindakan_sphincter" class="w-100">
                                        <div><em>Sphincter Otomy</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="tindakan_sphincter" name="tindakan_sphincter" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="tindakan_jahitan_iris" class="w-100">
                                        <div>Jahitan Iris</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="tindakan_jahitan_iris" name="tindakan_jahitan_iris" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label for="tindakan_virektomi" class="w-100">
                                        <div>Virektomi</div>
                                    </label>
                                    <div class="input-group input-group-sm has-validation">
                                        <input type="number" class="form-control form-control-sm" id="tindakan_virektomi" name="tindakan_virektomi" value="" autocomplete="off" dir="auto">
                                        <span class="input-group-text">cm</span>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="tindakan_kapsulotomi_post" class="w-100">
                                        <div>Kapsulotomi Post</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="tindakan_kapsulotomi_post" name="tindakan_kapsulotomi_post" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="tindakan_sinechiolyssis" class="w-100">
                                        <div><em>Synechiolysis</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="tindakan_sinechiolyssis" name="tindakan_sinechiolyssis" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Cairan Irigasi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_irigasi_ri" class="w-100">
                                        <div>RI</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_irigasi_ri" name="cairan_irigasi_ri" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_irigasi_bss" class="w-100">
                                        <div>BSS</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_irigasi_bss" name="cairan_irigasi_bss" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label for="cairan_irigasi_lainnya" class="w-100">
                                        <div>Lainnya</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="cairan_irigasi_lainnya" name="cairan_irigasi_lainnya" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Fiksasi LIO</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="fiksasi_bmb" class="w-100">
                                        <div>BMB</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fiksasi_bmb" name="fiksasi_bmb" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="fiksasi_bmd" class="w-100">
                                        <div>BMD</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fiksasi_bmd" name="fiksasi_bmd" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="fiksasi_sulkus_siliaris" class="w-100">
                                        <div>Sulkus Siliaris</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fiksasi_sulkus_siliaris" name="fiksasi_sulkus_siliaris" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="fiksasi_sklera" class="w-100">
                                        <div>Fiksasi Sklera</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="fiksasi_sklera" name="fiksasi_sklera" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Penanaman</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="penanaman_diputar" class="w-100">
                                        <div>Diputar</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="penanaman_diputar" name="penanaman_diputar" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="penanaman_tidak_diputar" class="w-100">
                                        <div>Tidak Diputar</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="penanaman_tidak_diputar" name="penanaman_tidak_diputar" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Jenis LIO</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="jenis_dilipat" class="w-100">
                                        <div>Dilipat</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="jenis_dilipat" name="jenis_dilipat" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="jenis_tidak_dilipat" class="w-100">
                                        <div>Tidak Dilipat</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="jenis_tidak_dilipat" name="jenis_tidak_dilipat" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Posisi LIO</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="posisi_vertikal" class="w-100">
                                        <div>Vertikal</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="posisi_vertikal" name="posisi_vertikal" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="posisi_horizontal" class="w-100">
                                        <div>Horizontal</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="posisi_horizontal" name="posisi_horizontal" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="posisi_miring" class="w-100">
                                        <div>Miring</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="posisi_miring" name="posisi_miring" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Cairan Viskoelastis</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_viscoelastik_healon" class="w-100">
                                        <div>Healon</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_viscoelastik_healon" name="cairan_viscoelastik_healon" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_viscoelastik_viscoat" class="w-100">
                                        <div>Viscoat</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_viscoelastik_viscoat" name="cairan_viscoelastik_viscoat" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_viscoelastik_amvisca" class="w-100">
                                        <div>Amvisca</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_viscoelastik_amvisca" name="cairan_viscoelastik_amvisca" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_viscoelastik_healon_5" class="w-100">
                                        <div>Healon 5</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_viscoelastik_healon_5" name="cairan_viscoelastik_healon_5" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="cairan_viscoelastik_rohtovisc" class="w-100">
                                        <div>Rohtovisc</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cairan_viscoelastik_rohtovisc" name="cairan_viscoelastik_rohtovisc" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Benang</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="benang_vicryl_8_0" class="w-100">
                                        <div>Vicryl 8-0</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="benang_vicryl_8_0" name="benang_vicryl_8_0" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="benang_ethylon_10_0" class="w-100">
                                        <div>Rthylon 10-0</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="benang_ethylon_10_0" name="benang_ethylon_10_0" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="jumlah_jahitan" name="jumlah_jahitan" value="" autocomplete="off" dir="auto" placeholder="jumlah_jahitan">
                        <label for="jumlah_jahitan">Jumlah Jahitan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="input-group has-validation">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="prabedah_od" name="prabedah_od" value="" autocomplete="off" dir="auto" placeholder="prabedah_od">
                                <label for="prabedah_od">Tio Pra Bedah OD</label>
                            </div>
                            <span class="input-group-text">mmHg</span>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group has-validation">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="prabedah_os" name="prabedah_os" value="" autocomplete="off" dir="auto" placeholder="prabedah_os">
                                <label for="prabedah_os">Tio Pra Bedah OS</label>
                            </div>
                            <span class="input-group-text">mmHg</span>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom">Komplikasi</div>
                <table class="table table-borderless table-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="komplikasi_tidak_ada" class="w-100">
                                        <div>Tidak Ada</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="komplikasi_tidak_ada" name="komplikasi_tidak_ada" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="komplikasi_ada" class="w-100">
                                        <div>Ada</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="komplikasi_ada" name="komplikasi_ada" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="komplikasi_prolaps" class="w-100">
                                        <div>Prolaps Vitreus</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="komplikasi_prolaps" name="komplikasi_prolaps" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="komplikasi_pendarahan" class="w-100">
                                        <div>Pendarahan</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="komplikasi_pendarahan" name="komplikasi_pendarahan" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label for="komplikasi_lainnya" class="w-100">
                                        <div>Lainnya</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="komplikasi_lainnya" name="komplikasi_lainnya" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3">
                <div class="mb-2">
                    <label for="laporan_operasi">Laporan Operasi</label>
                    <textarea class="form-control" id="laporan_operasi" name="laporan_operasi" rows="8" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="mb-2">
                    <label for="terapi_pascabedah">Terapi Pasca Bedah</label>
                    <textarea class="form-control" id="terapi_pascabedah" name="terapi_pascabedah" rows="8" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('lpoperasikatarak/export/' . $lp_operasi_katarak['id_lp_operasi_katarak']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchLaporan() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('lpoperasikatarak/view/') . $lp_operasi_katarak['id_lp_operasi_katarak'] ?>');
            const data = response.data;

            const mata = data.mata;
            if (mata) {
                $("input[name='mata'][value='" + mata + "']").prop('checked', true);
            }
            $('#operator').val(data.operator);
            $('#tanggal_operasi').val(data.tanggal_operasi);
            $('#jam_operasi').val(data.jam_operasi);
            $('#lama_operasi').val(data.lama_operasi);
            $('#diagnosis').val(data.diagnosis);
            $('#asisten').val(data.asisten);
            $('#jenis_operasi').val(data.jenis_operasi);
            const jenis_anastesi = data.jenis_anastesi;
            if (jenis_anastesi) {
                $("input[name='jenis_anastesi'][value='" + jenis_anastesi + "']").prop('checked', true);
            }
            $('#dokter_anastesi').val(data.dokter_anastesi);

            const anastesi_retrobulbar = data.anastesi_retrobulbar;
            if (anastesi_retrobulbar) {
                $("input[name='anastesi_retrobulbar'][value='" + anastesi_retrobulbar + "']").prop('checked', true);
            }
            const anastesi_peribulber = data.anastesi_peribulber;
            if (anastesi_peribulber) {
                $("input[name='anastesi_peribulber'][value='" + anastesi_peribulber + "']").prop('checked', true);
            }
            const anastesi_topikal = data.anastesi_topikal;
            if (anastesi_topikal) {
                $("input[name='anastesi_topikal'][value='" + anastesi_topikal + "']").prop('checked', true);
            }
            const anastesi_subtenom = data.anastesi_subtenom;
            if (anastesi_subtenom) {
                $("input[name='anastesi_subtenom'][value='" + anastesi_subtenom + "']").prop('checked', true);
            }
            const anastesi_lidocain_2 = data.anastesi_lidocain_2;
            if (anastesi_lidocain_2) {
                $("input[name='anastesi_lidocain_2'][value='" + anastesi_lidocain_2 + "']").prop('checked', true);
            }
            const anastesi_marcain_05 = data.anastesi_marcain_05;
            if (anastesi_marcain_05) {
                $("input[name='anastesi_marcain_05'][value='" + anastesi_marcain_05 + "']").prop('checked', true);
            }
            $('#anastesi_lainnya').val(data.anastesi_lainnya);

            const peritomi_basis_forniks = data.peritomi_basis_forniks;
            if (peritomi_basis_forniks) {
                $("input[name='peritomi_basis_forniks'][value='" + peritomi_basis_forniks + "']").prop('checked', true);
            }
            const peritomi_basis_limbus = data.peritomi_basis_limbus;
            if (peritomi_basis_limbus) {
                $("input[name='peritomi_basis_limbus'][value='" + peritomi_basis_limbus + "']").prop('checked', true);
            }

            const lokasi_superonasal = data.lokasi_superonasal;
            if (lokasi_superonasal) {
                $("input[name='lokasi_superonasal'][value='" + lokasi_superonasal + "']").prop('checked', true);
            }
            const lokasi_superior = data.lokasi_superior;
            if (lokasi_superior) {
                $("input[name='lokasi_superior'][value='" + lokasi_superior + "']").prop('checked', true);
            }
            const lokasi_supertemporal = data.lokasi_supertemporal;
            if (lokasi_supertemporal) {
                $("input[name='lokasi_supertemporal'][value='" + lokasi_supertemporal + "']").prop('checked', true);
            }
            $('#lokasi_lainnya').val(data.lokasi_lainnya);

            const lokasi_insisi_kornea = data.lokasi_insisi_kornea;
            if (lokasi_insisi_kornea) {
                $("input[name='lokasi_insisi_kornea'][value='" + lokasi_insisi_kornea + "']").prop('checked', true);
            }
            const lokasi_insisi_limbus = data.lokasi_insisi_limbus;
            if (lokasi_insisi_limbus) {
                $("input[name='lokasi_insisi_limbus'][value='" + lokasi_insisi_limbus + "']").prop('checked', true);
            }
            const lokasi_insisi_skelera = data.lokasi_insisi_skelera;
            if (lokasi_insisi_skelera) {
                $("input[name='lokasi_insisi_skelera'][value='" + lokasi_insisi_skelera + "']").prop('checked', true);
            }
            const lokasi_insisi_skeleratunnel = data.lokasi_insisi_skeleratunnel;
            if (lokasi_insisi_skeleratunnel) {
                $("input[name='lokasi_insisi_skeleratunnel'][value='" + lokasi_insisi_skeleratunnel + "']").prop('checked', true);
            }
            const lokasi_insisi_sideport = data.lokasi_insisi_sideport;
            if (lokasi_insisi_sideport) {
                $("input[name='lokasi_insisi_sideport'][value='" + lokasi_insisi_sideport + "']").prop('checked', true);
            }

            $('#ukuran_inisiasi').val(data.ukuran_inisiasi);

            const alat_insisi_jarum = data.alat_insisi_jarum;
            if (alat_insisi_jarum) {
                $("input[name='alat_insisi_jarum'][value='" + alat_insisi_jarum + "']").prop('checked', true);
            }
            const alat_insisi_crescent = data.alat_insisi_crescent;
            if (alat_insisi_crescent) {
                $("input[name='alat_insisi_crescent'][value='" + alat_insisi_crescent + "']").prop('checked', true);
            }
            const alat_insisi_diamond = data.alat_insisi_diamond;
            if (alat_insisi_diamond) {
                $("input[name='alat_insisi_diamond'][value='" + alat_insisi_diamond + "']").prop('checked', true);
            }

            const capsulectomy_canopener = data.capsulectomy_canopener;
            if (capsulectomy_canopener) {
                $("input[name='capsulectomy_canopener'][value='" + capsulectomy_canopener + "']").prop('checked', true);
            }
            const capsulectomy_envelope = data.capsulectomy_envelope;
            if (capsulectomy_envelope) {
                $("input[name='capsulectomy_envelope'][value='" + capsulectomy_envelope + "']").prop('checked', true);
            }
            const capsulectomy_ccc = data.capsulectomy_ccc;
            if (capsulectomy_ccc) {
                $("input[name='capsulectomy_ccc'][value='" + capsulectomy_ccc + "']").prop('checked', true);
            }

            const ekstraksi_lenca_icce = data.ekstraksi_lenca_icce;
            if (ekstraksi_lenca_icce) {
                $("input[name='ekstraksi_lenca_icce'][value='" + ekstraksi_lenca_icce + "']").prop('checked', true);
            }
            const ekstraksi_lenca_ecce = data.ekstraksi_lenca_ecce;
            if (ekstraksi_lenca_ecce) {
                $("input[name='ekstraksi_lenca_ecce'][value='" + ekstraksi_lenca_ecce + "']").prop('checked', true);
            }
            const ekstraksi_lenca_sucea = data.ekstraksi_lenca_sucea;
            if (ekstraksi_lenca_sucea) {
                $("input[name='ekstraksi_lenca_sucea'][value='" + ekstraksi_lenca_sucea + "']").prop('checked', true);
            }
            const ekstraksi_lenca_phaco = data.ekstraksi_lenca_phaco;
            if (ekstraksi_lenca_phaco) {
                $("input[name='ekstraksi_lenca_phaco'][value='" + ekstraksi_lenca_phaco + "']").prop('checked', true);
            }
            const ekstraksi_lenca_cle = data.ekstraksi_lenca_cle;
            if (ekstraksi_lenca_cle) {
                $("input[name='ekstraksi_lenca_cle'][value='" + ekstraksi_lenca_cle + "']").prop('checked', true);
            }
            const ekstraksi_lenca_ai = data.ekstraksi_lenca_ai;
            if (ekstraksi_lenca_ai) {
                $("input[name='ekstraksi_lenca_ai'][value='" + ekstraksi_lenca_ai + "']").prop('checked', true);
            }

            const tindakan_sphincter = data.tindakan_sphincter;
            if (tindakan_sphincter) {
                $("input[name='tindakan_sphincter'][value='" + tindakan_sphincter + "']").prop('checked', true);
            }
            const tindakan_jahitan_iris = data.tindakan_jahitan_iris;
            if (tindakan_jahitan_iris) {
                $("input[name='tindakan_jahitan_iris'][value='" + tindakan_jahitan_iris + "']").prop('checked', true);
            }
            $('#tindakan_virektomi').val(data.tindakan_virektomi);
            const tindakan_kapsulotomi_post = data.tindakan_kapsulotomi_post;
            if (tindakan_kapsulotomi_post) {
                $("input[name='tindakan_kapsulotomi_post'][value='" + tindakan_kapsulotomi_post + "']").prop('checked', true);
            }
            const tindakan_sinechiolyssis = data.tindakan_sinechiolyssis;
            if (tindakan_sinechiolyssis) {
                $("input[name='tindakan_sinechiolyssis'][value='" + tindakan_sinechiolyssis + "']").prop('checked', true);
            }

            const cairan_irigasi_ri = data.cairan_irigasi_ri;
            if (cairan_irigasi_ri) {
                $("input[name='cairan_irigasi_ri'][value='" + cairan_irigasi_ri + "']").prop('checked', true);
            }
            const cairan_irigasi_bss = data.cairan_irigasi_bss;
            if (cairan_irigasi_bss) {
                $("input[name='cairan_irigasi_bss'][value='" + cairan_irigasi_bss + "']").prop('checked', true);
            }
            $('#cairan_irigasi_lainnya').val(data.cairan_irigasi_lainnya);

            const fiksasi_bmb = data.fiksasi_bmb;
            if (fiksasi_bmb) {
                $("input[name='fiksasi_bmb'][value='" + fiksasi_bmb + "']").prop('checked', true);
            }
            const fiksasi_bmd = data.fiksasi_bmd;
            if (fiksasi_bmd) {
                $("input[name='fiksasi_bmd'][value='" + fiksasi_bmd + "']").prop('checked', true);
            }
            const fiksasi_sulkus_siliaris = data.fiksasi_sulkus_siliaris;
            if (fiksasi_sulkus_siliaris) {
                $("input[name='fiksasi_sulkus_siliaris'][value='" + fiksasi_sulkus_siliaris + "']").prop('checked', true);
            }
            const fiksasi_sklera = data.fiksasi_sklera;
            if (fiksasi_sklera) {
                $("input[name='fiksasi_sklera'][value='" + fiksasi_sklera + "']").prop('checked', true);
            }

            const penanaman_diputar = data.penanaman_diputar;
            if (penanaman_diputar) {
                $("input[name='penanaman_diputar'][value='" + penanaman_diputar + "']").prop('checked', true);
            }
            const penanaman_tidak_diputar = data.penanaman_tidak_diputar;
            if (penanaman_tidak_diputar) {
                $("input[name='penanaman_tidak_diputar'][value='" + penanaman_tidak_diputar + "']").prop('checked', true);
            }

            const jenis_dilipat = data.jenis_dilipat;
            if (jenis_dilipat) {
                $("input[name='jenis_dilipat'][value='" + jenis_dilipat + "']").prop('checked', true);
            }
            const jenis_tidak_dilipat = data.jenis_tidak_dilipat;
            if (jenis_tidak_dilipat) {
                $("input[name='jenis_tidak_dilipat'][value='" + jenis_tidak_dilipat + "']").prop('checked', true);
            }

            const posisi_vertikal = data.posisi_vertikal;
            if (posisi_vertikal) {
                $("input[name='posisi_vertikal'][value='" + posisi_vertikal + "']").prop('checked', true);
            }
            const posisi_horizontal = data.posisi_horizontal;
            if (posisi_horizontal) {
                $("input[name='posisi_horizontal'][value='" + posisi_horizontal + "']").prop('checked', true);
            }
            const posisi_miring = data.posisi_miring;
            if (posisi_miring) {
                $("input[name='posisi_miring'][value='" + posisi_miring + "']").prop('checked', true);
            }

            const cairan_viscoelastik_healon = data.cairan_viscoelastik_healon;
            if (cairan_viscoelastik_healon) {
                $("input[name='cairan_viscoelastik_healon'][value='" + cairan_viscoelastik_healon + "']").prop('checked', true);
            }
            const cairan_viscoelastik_viscoat = data.cairan_viscoelastik_viscoat;
            if (cairan_viscoelastik_viscoat) {
                $("input[name='cairan_viscoelastik_viscoat'][value='" + cairan_viscoelastik_viscoat + "']").prop('checked', true);
            }
            const cairan_viscoelastik_amvisca = data.cairan_viscoelastik_amvisca;
            if (cairan_viscoelastik_amvisca) {
                $("input[name='cairan_viscoelastik_amvisca'][value='" + cairan_viscoelastik_amvisca + "']").prop('checked', true);
            }
            const cairan_viscoelastik_healon_5 = data.cairan_viscoelastik_healon_5;
            if (cairan_viscoelastik_healon_5) {
                $("input[name='cairan_viscoelastik_healon_5'][value='" + cairan_viscoelastik_healon_5 + "']").prop('checked', true);
            }
            const cairan_viscoelastik_rohtovisc = data.cairan_viscoelastik_rohtovisc;
            if (cairan_viscoelastik_rohtovisc) {
                $("input[name='cairan_viscoelastik_rohtovisc'][value='" + cairan_viscoelastik_rohtovisc + "']").prop('checked', true);
            }

            const benang_vicryl_8_0 = data.benang_vicryl_8_0;
            if (benang_vicryl_8_0) {
                $("input[name='benang_vicryl_8_0'][value='" + benang_vicryl_8_0 + "']").prop('checked', true);
            }
            const benang_ethylon_10_0 = data.benang_ethylon_10_0;
            if (benang_ethylon_10_0) {
                $("input[name='benang_ethylon_10_0'][value='" + benang_ethylon_10_0 + "']").prop('checked', true);
            }

            $('#jumlah_jahitan').val(data.jumlah_jahitan);

            $('#prabedah_od').val(data.prabedah_od);
            $('#prabedah_os').val(data.prabedah_os);

            const komplikasi_tidak_ada = data.komplikasi_tidak_ada;
            if (komplikasi_tidak_ada) {
                $("input[name='komplikasi_tidak_ada'][value='" + komplikasi_tidak_ada + "']").prop('checked', true);
            }
            const komplikasi_ada = data.komplikasi_ada;
            if (komplikasi_ada) {
                $("input[name='komplikasi_ada'][value='" + komplikasi_ada + "']").prop('checked', true);
            }
            const komplikasi_prolaps = data.komplikasi_prolaps;
            if (komplikasi_prolaps) {
                $("input[name='komplikasi_prolaps'][value='" + komplikasi_prolaps + "']").prop('checked', true);
            }
            const komplikasi_pendarahan = data.komplikasi_pendarahan;
            if (komplikasi_pendarahan) {
                $("input[name='komplikasi_pendarahan'][value='" + komplikasi_pendarahan + "']").prop('checked', true);
            }
            $('#komplikasi_lainnya').val(data.komplikasi_lainnya);

            $('#laporan_operasi').val(data.laporan_operasi);
            $('#terapi_pascabedah').val(data.terapi_pascabedah);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        // Fungsi untuk mengunggah gambar dari kanvas
        $('#LaporanForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#LaporanForm .is-invalid').removeClass('is-invalid');
            $('#LaporanForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#LaporanForm input, #LaporanForm select, #LaporanForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('lpoperasikatarak/update/' . $lp_operasi_katarak['id_lp_operasi_katarak']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchLaporan();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#LaporanForm .is-invalid').removeClass('is-invalid');
                    $('#LaporanForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['mata', 'jenis_anastesi'].includes(field)) {
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
                    $('.main-content').animate({
                        scrollTop: 0
                    }, 'slow');
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            `);
                $('#LaporanForm input, #LaporanForm select, #LaporanForm button').prop('disabled', false);
            }
        });
        // $('#loadingSpinner').hide();
        fetchLaporan();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>