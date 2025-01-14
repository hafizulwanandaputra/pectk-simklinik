<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/floating'); ?>
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
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/rawatjalan'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nomor_registrasi']; ?> • <?= $rawatjalan['no_rm'] ?> • <?= $rawatjalan['nama_pasien']; ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/asesmen/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/asesmen/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap active" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepkacamata/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/lptindakan/' . $rawatjalan['id_rawat_jalan']); ?>">Laporan Tindakan</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 text-nowrap <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active' : '' ?>" href="<?= base_url('rawatjalan/asesmen/' . $list['id_rawat_jalan']); ?>"><?= $list['nomor_registrasi']; ?></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <?= form_open_multipart('/rawatjalan/asesmen/update/' . $asesmen['id_asesmen'], 'id="asesmenForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Anamnesis (S)</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="keluhan_utama" name="keluhan_utama" value="" autocomplete="off" dir="auto" placeholder="keluhan_utama">
                        <label for="keluhan_utama">Keluhan Utama</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_penyakit_sekarang" name="riwayat_penyakit_sekarang" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_sekarang">
                            <label for="riwayat_penyakit_sekarang">Riwayat Penyakit Sekarang</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_penyakit_dahulu" name="riwayat_penyakit_dahulu" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_dahulu">
                            <label for="riwayat_penyakit_dahulu">Riwayat Penyakit Dahulu</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_penyakit_keluarga" name="riwayat_penyakit_keluarga" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_keluarga">
                            <label for="riwayat_penyakit_keluarga">Riwayat Penyakit Keluarga</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_pengobatan" name="riwayat_pengobatan" value="" autocomplete="off" dir="auto" placeholder="riwayat_pengobatan">
                            <label for="riwayat_pengobatan">Riwayat Pengobatan</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="riwayat_sosial_pekerjaan" name="riwayat_sosial_pekerjaan" value="" autocomplete="off" dir="auto" placeholder="riwayat_sosial_pekerjaan">
                        <label for="riwayat_sosial_pekerjaan">Riwayat Pekerjaan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemeriksaan Umum</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 align-items-start">
                    <div class="row row-cols-1 g-2 align-items-start">
                        <div class="col">
                            <div class="form-floating">
                                <select class="form-select" id="kesadaran" name="kesadaran" aria-label="kesadaran">
                                    <option value="" selected>-- Pilih Kesadaran --</option>
                                    <option value="Compos Mentis (GCS 14-15)">Compos Mentis (GCS 14-15)</option>
                                    <option value="Apatis (GCS 12-13)">Apatis (GCS 12-13)</option>
                                    <option value="Somnolen (GCS 10-11)">Somnolen (GCS 10-11)</option>
                                    <option value="Delirium (GCS 9-7)">Delirium (GCS 9-7)</option>
                                    <option value="Stupor (Suporos Comma) (GCS 4-6)">Stupor (Suporos Comma) (GCS 4-6)</option>
                                    <option value="Koma (GCS 3)">Koma (GCS 3)</option>
                                </select>
                                <label for="kesadaran">Kesadaran</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tekanan_darah" name="tekanan_darah" value="" autocomplete="off" dir="auto" placeholder="tekanan_darah">
                                <label for="tekanan_darah">Tekanan Darah (mmHg)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nadi" name="nadi" value="" autocomplete="off" dir="auto" placeholder="nadi">
                                <label for="nadi">Nadi (×/menit)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="suhu" name="suhu" value="" autocomplete="off" dir="auto" placeholder="suhu">
                                <label for="suhu">Suhu (°C)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="pernapasan" name="pernapasan" value="" autocomplete="off" dir="auto" placeholder="pernapasan">
                                <label for="pernapasan">Pernapasan (×/menit)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-cols-1 g-2 align-items-start">
                        <div class="col">
                            <div class="row g-1">
                                <label for="keadaan_umum" class="col col-form-label">Keadaan Umum</label>
                                <div class="col col-form-label">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keadaan_umum" id="keadaan_umum1" value="BAIK">
                                            <label class="form-check-label" for="keadaan_umum1">
                                                Baik
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keadaan_umum" id="keadaan_umum2" value="SEDANG">
                                            <label class="form-check-label" for="keadaan_umum2">
                                                Sedang
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keadaan_umum" id="keadaan_umum3" value="BURUK">
                                            <label class="form-check-label" for="keadaan_umum3">
                                                Buruk
                                            </label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback text-center"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="kesadaran_mental" name="kesadaran_mental" value="" autocomplete="off" dir="auto" placeholder="kesadaran_mental">
                                <label for="kesadaran_mental">Kesadaran / Mental</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row g-1">
                                <label for="alergi" class="col col-form-label">Alergi</label>
                                <div class="col col-form-label">
                                    <div class="d-flex align-items-center justify-content-evenly">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="alergi" id="alergi1" value="YA">
                                            <label class="form-check-label" for="alergi1">
                                                Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="alergi" id="alergi2" value="TIDAK">
                                            <label class="form-check-label" for="alergi2">
                                                Tidak
                                            </label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback text-center"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="alergi_keterangan" name="alergi_keterangan" value="" autocomplete="off" dir="auto" placeholder="alergi_keterangan">
                                <label for="alergi_keterangan">Keterangan Alergi</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sakit_lainnya" name="sakit_lainnya" value="" autocomplete="off" dir="auto" placeholder="sakit_lainnya">
                                <label for="sakit_lainnya">Sakit Lainnya</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemeriksaan Fisik (O)</div>
                <div class="card overflow-auto">
                    <div class="table-responsive">
                        <table class="table m-0 table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col" class="align-middle"></th>
                                    <th scope="col" class="text-center align-middle">UCVA</th>
                                    <th scope="col" class="text-center align-middle">BCVA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-center align-middle">OD</th>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="od_ucva" name="od_ucva" value="" autocomplete="off" dir="auto" placeholder="OD UCVA">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="od_bcva" name="od_bcva" value="" autocomplete="off" dir="auto" placeholder="OD BCVA">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center align-middle">OS</th>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="os_ucva" name="os_ucva" value="" autocomplete="off" dir="auto" placeholder="OS UCVA">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="os_bcva" name="os_bcva" value="" autocomplete="off" dir="auto" placeholder="OS BCVA">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Diagnosis Medis (A)</div>
                <div class="table-responsive mb-3">
                    <table class="table mb-0 table-borderless">
                        <tbody>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-0 pb-1" style="min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa_medis_1" name="diagnosa_medis_1" value="" autocomplete="off" dir="auto" placeholder="diagnosa_medis_1">
                                        <label for="diagnosa_medis_1">Diagnosis Medis 1</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-0 pb-1" style="min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="icdx_kode_1" name="icdx_kode_1" value="" autocomplete="off" dir="auto" placeholder="icdx_kode_1" list="icdx_kode_1_list">
                                        <label for="icdx_kode_1">ICD 10</label>
                                        <datalist id="icdx_kode_1_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa_medis_2" name="diagnosa_medis_2" value="" autocomplete="off" dir="auto" placeholder="diagnosa_medis_2">
                                        <label for="diagnosa_medis_2">Diagnosis Medis 2</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="icdx_kode_2" name="icdx_kode_2" value="" autocomplete="off" dir="auto" placeholder="icdx_kode_2" list="icdx_kode_2_list">
                                        <label for="icdx_kode_2">ICD 10</label>
                                        <datalist id="icdx_kode_2_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa_medis_3" name="diagnosa_medis_3" value="" autocomplete="off" dir="auto" placeholder="diagnosa_medis_3">
                                        <label for="diagnosa_medis_3">Diagnosis Medis 3</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="icdx_kode_3" name="icdx_kode_3" value="" autocomplete="off" dir="auto" placeholder="icdx_kode_3" list="icdx_kode_3_list">
                                        <label for="icdx_kode_3">ICD 10</label>
                                        <datalist id="icdx_kode_3_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa_medis_4" name="diagnosa_medis_4" value="" autocomplete="off" dir="auto" placeholder="diagnosa_medis_4">
                                        <label for="diagnosa_medis_4">Diagnosis Medis 4</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="icdx_kode_4" name="icdx_kode_4" value="" autocomplete="off" dir="auto" placeholder="icdx_kode_4" list="icdx_kode_4_list">
                                        <label for="icdx_kode_4">ICD 10</label>
                                        <datalist id="icdx_kode_4_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-0" style="min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa_medis_5" name="diagnosa_medis_5" value="" autocomplete="off" dir="auto" placeholder="diagnosa_medis_5">
                                        <label for="diagnosa_medis_5">Diagnosis Medis 5</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-0" style="min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="icdx_kode_5" name="icdx_kode_5" value="" autocomplete="off" dir="auto" placeholder="icdx_kode_5" list="icdx_kode_5_list">
                                        <label for="icdx_kode_5">ICD 10</label>
                                        <datalist id="icdx_kode_5_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div
                    </div>
                <div class="mb-3">
                    <div class="fw-bold mb-2 border-bottom">Terapi (P)</div>
                    <div class="table-responsive mb-3">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-0 pb-1" style="min-width: 200px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="terapi_1" name="terapi_1" value="" autocomplete="off" dir="auto" placeholder="terapi_1">
                                            <label for="terapi_1">Diagnosis Medis 1</label>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-0 pb-1" style="min-width: 100px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="icd9_kode_1" name="icd9_kode_1" value="" autocomplete="off" dir="auto" placeholder="icd9_kode_1" list="icd9_kode_1_list">
                                            <label for="icd9_kode_1">ICD 9</label>
                                            <datalist id="icd9_kode_1_list">
                                            </datalist>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="terapi_2" name="terapi_2" value="" autocomplete="off" dir="auto" placeholder="terapi_2">
                                            <label for="terapi_2">Diagnosis Medis 2</label>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="icd9_kode_2" name="icd9_kode_2" value="" autocomplete="off" dir="auto" placeholder="icd9_kode_2" list="icd9_kode_2_list">
                                            <label for="icd9_kode_2">ICD 9</label>
                                            <datalist id="icd9_kode_2_list">
                                            </datalist>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="terapi_3" name="terapi_3" value="" autocomplete="off" dir="auto" placeholder="terapi_3">
                                            <label for="terapi_3">Diagnosis Medis 3</label>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="icd9_kode_3" name="icd9_kode_3" value="" autocomplete="off" dir="auto" placeholder="icd9_kode_3" list="icd9_kode_3_list">
                                            <label for="icd9_kode_3">ICD 9</label>
                                            <datalist id="icd9_kode_3_list">
                                            </datalist>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="min-width: 200px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="terapi_4" name="terapi_4" value="" autocomplete="off" dir="auto" placeholder="terapi_4">
                                            <label for="terapi_4">Diagnosis Medis 4</label>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="min-width: 100px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="icd9_kode_4" name="icd9_kode_4" value="" autocomplete="off" dir="auto" placeholder="icd9_kode_4" list="icd9_kode_4_list">
                                            <label for="icd9_kode_4">ICD 9</label>
                                            <datalist id="icd9_kode_4_list">
                                            </datalist>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-0" style="min-width: 200px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="terapi_5" name="terapi_5" value="" autocomplete="off" dir="auto" placeholder="terapi_5">
                                            <label for="terapi_5">Diagnosis Medis 5</label>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-0" style="min-width: 100px;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="icd9_kode_5" name="icd9_kode_5" value="" autocomplete="off" dir="auto" placeholder="icd9_kode_5" list="icd9_kode_5_list">
                                            <label for="icd9_kode_5">ICD 9</label>
                                            <datalist id="icd9_kode_5_list">
                                            </datalist>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/asesmen/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
    async function fetchAsesmen() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/view/') . $asesmen['id_asesmen'] ?>');
            const data = response.data;

            // Anamnesis (S)
            $('#keluhan_utama').val(data.keluhan_utama);
            $('#riwayat_penyakit_sekarang').val(data.riwayat_penyakit_sekarang);
            $('#riwayat_penyakit_dahulu').val(data.riwayat_penyakit_dahulu);
            $('#riwayat_penyakit_keluarga').val(data.riwayat_penyakit_keluarga);
            $('#riwayat_pengobatan').val(data.riwayat_pengobatan);
            $('#riwayat_sosial_pekerjaan').val(data.riwayat_sosial_pekerjaan);

            // Pemeriksaan Umum
            $('#kesadaran').val(data.kesadaran);
            $('#tekanan_darah').val(data.tekanan_darah);
            $('#nadi').val(data.nadi);
            $('#suhu').val(data.suhu);
            $('#pernapasan').val(data.pernapasan);
            const keadaan_umum = data.keadaan_umum;
            if (keadaan_umum) {
                $("input[name='keadaan_umum'][value='" + keadaan_umum + "']").prop('checked', true);
            }
            $('#kesadaran_mental').val(data.kesadaran_mental);
            const alergi = data.alergi;
            if (alergi) {
                $("input[name='alergi'][value='" + alergi + "']").prop('checked', true);
            }
            $('#alergi_keterangan').val(data.alergi_keterangan);
            $('#sakit_lainnya').val(data.sakit_lainnya);

            // Pemeriksaan Fisik (O)
            $('#od_ucva').val(data.od_ucva);
            $('#od_bcva').val(data.od_bcva);
            $('#os_ucva').val(data.os_ucva);
            $('#os_bcva').val(data.os_bcva);

            // Diagnosis Medis (A)
            $('#diagnosa_medis_1').val(data.diagnosa_medis_1);
            $('#icdx_kode_1').val(data.icdx_kode_1);
            $('#diagnosa_medis_2').val(data.diagnosa_medis_2);
            $('#icdx_kode_2').val(data.icdx_kode_2);
            $('#diagnosa_medis_3').val(data.diagnosa_medis_3);
            $('#icdx_kode_3').val(data.icdx_kode_3);
            $('#diagnosa_medis_4').val(data.diagnosa_medis_4);
            $('#icdx_kode_4').val(data.icdx_kode_4);
            $('#diagnosa_medis_5').val(data.diagnosa_medis_5);
            $('#icdx_kode_5').val(data.icdx_kode_5);

            // Terapi (P)
            $('#terapi_1').val(data.terapi_1);
            $('#icd9_kode_1').val(data.icd9_kode_1);
            $('#terapi_2').val(data.terapi_2);
            $('#icd9_kode_2').val(data.icd9_kode_2);
            $('#terapi_3').val(data.terapi_3);
            $('#icd9_kode_3').val(data.icd9_kode_3);
            $('#terapi_4').val(data.terapi_4);
            $('#icd9_kode_4').val(data.icd9_kode_4);
            $('#terapi_5').val(data.terapi_5);
            $('#icd9_kode_5').val(data.icd9_kode_5);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function loadICDX1(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#icdx_kode_1_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICDX2(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#icdx_kode_2_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICDX3(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#icdx_kode_3_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICDX4(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#icdx_kode_4_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICDX5(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#icdx_kode_5_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    // Event listener untuk input
    $('#icdx_kode_1').on('input', function() {
        const query = $(this).val();
        loadICDX1(query);
    });

    // Event listener untuk input
    $('#icdx_kode_2').on('input', function() {
        const query = $(this).val();
        loadICDX2(query);
    });

    // Event listener untuk input
    $('#icdx_kode_3').on('input', function() {
        const query = $(this).val();
        loadICDX3(query);
    });

    // Event listener untuk input
    $('#icdx_kode_4').on('input', function() {
        const query = $(this).val();
        loadICDX4(query);
    });

    // Event listener untuk input
    $('#icdx_kode_5').on('input', function() {
        const query = $(this).val();
        loadICDX5(query);
    });

    async function loadICD91(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icd9') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icd9 = response.data.data;
            const dataList = $('#icd9_kode_1_list');
            dataList.empty(); // Kosongkan datalist
            icd9.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICD92(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icd9') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icd9 = response.data.data;
            const dataList = $('#icd9_kode_2_list');
            dataList.empty(); // Kosongkan datalist
            icd9.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICD93(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icd9') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icd9 = response.data.data;
            const dataList = $('#icd9_kode_3_list');
            dataList.empty(); // Kosongkan datalist
            icd9.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICD94(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icd9') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icd9 = response.data.data;
            const dataList = $('#icd9_kode_4_list');
            dataList.empty(); // Kosongkan datalist
            icd9.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    async function loadICD95(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/icd9') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icd9 = response.data.data;
            const dataList = $('#icd9_kode_5_list');
            dataList.empty(); // Kosongkan datalist
            icd9.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    // Event listener untuk input
    $('#icd9_kode_1').on('input', function() {
        const query = $(this).val();
        loadICD91(query);
    });

    // Event listener untuk input
    $('#icd9_kode_2').on('input', function() {
        const query = $(this).val();
        loadICD92(query);
    });

    // Event listener untuk input
    $('#icd9_kode_3').on('input', function() {
        const query = $(this).val();
        loadICD93(query);
    });

    // Event listener untuk input
    $('#icd9_kode_4').on('input', function() {
        const query = $(this).val();
        loadICD94(query);
    });

    // Event listener untuk input
    $('#icd9_kode_5').on('input', function() {
        const query = $(this).val();
        loadICD95(query);
    });

    $(document).ready(async function() {
        $('#asesmenForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#asesmenForm .is-invalid').removeClass('is-invalid');
            $('#asesmenForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#asesmenForm input, #asesmenForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/asesmen/update/' . $asesmen['id_asesmen']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    await fetchAsesmen();
                    await Promise.all([
                        loadICDX1(),
                        loadICDX2(),
                        loadICDX3(),
                        loadICDX4(),
                        loadICDX5(),
                        loadICD91(),
                        loadICD92(),
                        loadICD93(),
                        loadICD94(),
                        loadICD95()
                    ]);
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#asesmenForm .is-invalid').removeClass('is-invalid');
                    $('#asesmenForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'alergi' || field === 'keadaan_umum') {
                                const radioGroup = $("input[type='radio']");
                                const feedbackElement = radioGroup.closest('.col-form-label').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[type='radio']").removeClass('is-invalid');
                                        feedbackElement.removeAttr('style').hide();
                                    });
                                }
                            } else {
                                const feedbackElement = fieldElement.siblings('.invalid-feedback');

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
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#asesmenForm input, #asesmenForm select').prop('disabled', false);
            }
        });
        await fetchAsesmen();
        await Promise.all([
            loadICDX1(),
            loadICDX2(),
            loadICDX3(),
            loadICDX4(),
            loadICDX5(),
            loadICD91(),
            loadICD92(),
            loadICD93(),
            loadICD94(),
            loadICD95()
        ]);
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>