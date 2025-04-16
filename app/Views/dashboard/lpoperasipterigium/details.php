<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($lp_operasi_pterigium['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($lp_operasi_pterigium['tanggal_registrasi'])));

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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/lpoperasipterigium'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $lp_operasi_pterigium['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $lp_operasi_pterigium['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('lpoperasipterigium/details/' . $previous['id_lp_operasi_pterigium']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi pterigium sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('lpoperasipterigium/details/' . $next['id_lp_operasi_pterigium']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi pterigium berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_lp_operasi_pterigium']) ? 'active activeLink' : '' ?>" href="<?= base_url('lpoperasipterigium/details/' . $list['id_lp_operasi_pterigium']); ?>">
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
            <?= form_open_multipart('lpoperasipterigium/update/' . $lp_operasi_pterigium['id_lp_operasi_pterigium'], 'id="LaporanForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (date('Y-m-d', strtotime($lp_operasi_pterigium['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($lp_operasi_pterigium['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
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
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="antiseptic" class="col col-form-label">Antiseptik<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="antiseptic" id="antiseptic1" value="BETADINE">
                                    <label class="form-check-label" for="antiseptic1">
                                        Betadine
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="antiseptic" id="antiseptic2" value="LAINNYA">
                                    <label class="form-check-label" for="antiseptic2">
                                        Lainnya
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="antiseptic_lainnya" name="antiseptic_lainnya" value="" autocomplete="off" dir="auto" placeholder="antiseptic_lainnya">
                        <label for="antiseptic_lainnya">Antiseptik Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="spekulum" class="col col-form-label">Spekulum<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="spekulum" id="spekulum1" value="WIRE">
                                    <label class="form-check-label" for="spekulum1">
                                        <em>Wire</em>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="spekulum" id="spekulum2" value="LAINNYA">
                                    <label class="form-check-label" for="spekulum2">
                                        Lainnya
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="spekulum_lainnya" name="spekulum_lainnya" value="" autocomplete="off" dir="auto" placeholder="spekulum_lainnya">
                        <label for="spekulum_lainnya">Spekulum Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="kendala_rektus_superior" class="col col-form-label">Kendala Rektus Superior<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kendala_rektus_superior" id="kendala_rektus_superior1" value="YA">
                                    <label class="form-check-label" for="kendala_rektus_superior1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kendala_rektus_superior" id="kendala_rektus_superior2" value="TIDAK">
                                    <label class="form-check-label" for="kendala_rektus_superior2">
                                        Tidak
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kendala_rektus_superior" id="kendala_rektus_superior3" value="BENANG">
                                    <label class="form-check-label" for="kendala_rektus_superior3">
                                        Benang
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="cangkok_konjungtiva" class="col col-form-label">Cangkok Konjungtiva<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cangkok_konjungtiva" id="cangkok_konjungtiva1" value="YA">
                                    <label class="form-check-label" for="cangkok_konjungtiva1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cangkok_konjungtiva" id="cangkok_konjungtiva2" value="TIDAK">
                                    <label class="form-check-label" for="cangkok_konjungtiva2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ukuran_cangkok" name="ukuran_cangkok" value="" autocomplete="off" dir="auto" placeholder="ukuran_cangkok">
                        <label for="ukuran_cangkok">Ukuran Cangkok (jika ya)</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="cangkang_membrane_amnio" class="col col-form-label">Cangkang Membran Amnio<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cangkang_membrane_amnio" id="cangkang_membrane_amnio1" value="YA">
                                    <label class="form-check-label" for="cangkang_membrane_amnio1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cangkang_membrane_amnio" id="cangkang_membrane_amnio2" value="TIDAK">
                                    <label class="form-check-label" for="cangkang_membrane_amnio2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ukuran_cangkang" name="ukuran_cangkang" value="" autocomplete="off" dir="auto" placeholder="ukuran_cangkang">
                        <label for="ukuran_cangkang">Ukuran Cangkang (jika ya)</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="bare_sclera" class="col col-form-label"><em>Bare Sclera</em><span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="bare_sclera" id="bare_sclera1" value="YA">
                                    <label class="form-check-label" for="bare_sclera1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="bare_sclera" id="bare_sclera2" value="TIDAK">
                                    <label class="form-check-label" for="bare_sclera2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="mytomicyn_c" class="col col-form-label">Mitomisin C<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mytomicyn_c" id="mytomicyn_c1" value="YA">
                                    <label class="form-check-label" for="mytomicyn_c1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mytomicyn_c" id="mytomicyn_c2" value="TIDAK">
                                    <label class="form-check-label" for="mytomicyn_c2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="penjahitan" class="col col-form-label">Penjahitan<span class="text-danger">*</span></label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penjahitan" id="penjahitan1" value="VYCRIL 8.0">
                                    <label class="form-check-label" for="penjahitan1">
                                        Vycril 8.0
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penjahitan" id="penjahitan2" value="ETHYLON 10.0">
                                    <label class="form-check-label" for="penjahitan2">
                                        Ethylon 10.0
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="mb-2">
                        <label for="keterangan_tambahan">Keterangan Tambahan</label>
                        <textarea class="form-control" id="keterangan_tambahan" name="keterangan_tambahan" rows="8" style="resize: none;"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="mb-2">
                        <label for="terapi_pasca_bedah">Terapi Pasca Bedah</label>
                        <textarea class="form-control" id="terapi_pasca_bedah" name="terapi_pasca_bedah" rows="8" style="resize: none;"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('lpoperasipterigium/export/' . $lp_operasi_pterigium['id_lp_operasi_pterigium']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
            const response = await axios.get('<?= base_url('lpoperasipterigium/view/') . $lp_operasi_pterigium['id_lp_operasi_pterigium'] ?>');
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

            const antiseptic = data.antiseptic;
            if (antiseptic) {
                $("input[name='antiseptic'][value='" + antiseptic + "']").prop('checked', true);
            }
            $('#antiseptic_lainnya').val(data.antiseptic_lainnya);
            const spekulum = data.spekulum;
            if (spekulum) {
                $("input[name='spekulum'][value='" + spekulum + "']").prop('checked', true);
            }
            $('#spekulum_lainnya').val(data.spekulum_lainnya);
            const kendala_rektus_superior = data.kendala_rektus_superior;
            if (kendala_rektus_superior) {
                $("input[name='kendala_rektus_superior'][value='" + kendala_rektus_superior + "']").prop('checked', true);
            }
            const cangkok_konjungtiva = data.cangkok_konjungtiva;
            if (cangkok_konjungtiva) {
                $("input[name='cangkok_konjungtiva'][value='" + cangkok_konjungtiva + "']").prop('checked', true);
            }
            $('#ukuran_cangkok').val(data.ukuran_cangkok);
            const cangkang_membrane_amnio = data.cangkang_membrane_amnio;
            if (cangkang_membrane_amnio) {
                $("input[name='cangkang_membrane_amnio'][value='" + cangkang_membrane_amnio + "']").prop('checked', true);
            }
            $('#ukuran_cangkang').val(data.ukuran_cangkang);
            const bare_sclera = data.bare_sclera;
            if (bare_sclera) {
                $("input[name='bare_sclera'][value='" + bare_sclera + "']").prop('checked', true);
            }
            const mytomicyn_c = data.mytomicyn_c;
            if (mytomicyn_c) {
                $("input[name='mytomicyn_c'][value='" + mytomicyn_c + "']").prop('checked', true);
            }
            const penjahitan = data.penjahitan;
            if (penjahitan) {
                $("input[name='penjahitan'][value='" + penjahitan + "']").prop('checked', true);
            }

            $('#keterangan_tambahan').val(data.keterangan_tambahan);
            $('#terapi_pasca_bedah').val(data.terapi_pasca_bedah);
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
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#LaporanForm input, #LaporanForm select, #LaporanForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('lpoperasipterigium/update/' . $lp_operasi_pterigium['id_lp_operasi_pterigium']) ?>`, formData, {
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
                            if (['mata', 'jenis_anastesi', 'antiseptic', 'spekulum', 'kendala_rektus_superior', 'cangkok_konjungtiva', 'cangkang_membrane_amnio', 'bare_sclera', 'mytomicyn_c', 'penjahitan'].includes(field)) {
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