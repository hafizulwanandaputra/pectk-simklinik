<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($operasi['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($operasi['tanggal_registrasi'])));

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
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/operasi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $operasi['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $operasi['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/safety/' . $previous['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_booking']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/safety/' . $next['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_booking']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/spko/' . $operasi['id_sp_operasi']); ?>">SPKO</a>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/praoperasi/' . $operasi['id_sp_operasi']); ?>">Pra Operasi</a>
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('operasi/safety/' . $operasi['id_sp_operasi']); ?>">Keselamatan</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_sp_operasi']) ? 'active activeLink' : '' ?>" href="<?= base_url('operasi/safety/' . $list['id_sp_operasi']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm" style="font-size: 0.75em;"><?= $list['nomor_booking'] ?></div>
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
            <?= form_open_multipart('/operasi/signin/update/' . $operasi_safety_signin['id_signin'], 'id="SafetySignInForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (date('Y-m-d', strtotime($operasi['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($operasi['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><em>Sign In</em><br><small class="text-muted fw-normal">Sebelum tindakan anestesi</small></div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Pasien (penanggung jawab) telah mengonfirmasi identitas pasien, prosedur dan lokasi tindakan (termasuk dalam tindakan anestesi)</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="ns_konfirmasi_identitas" name="ns_konfirmasi_identitas" value="1">
                            <label class="form-check-label" for="ns_konfirmasi_identitas">
                                Perawat
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dr_konfirmasi_identitas" name="dr_konfirmasi_identitas" value="1">
                            <label class="form-check-label" for="dr_konfirmasi_identitas">
                                Dokter
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div><em>Marker</em> pada daerah operasi</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Perawat</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_marker_operasi" id="ns_marker_operasi1" value="YA">
                                    <label class="form-check-label" for="ns_marker_operasi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_marker_operasi" id="ns_marker_operasi2" value="TIDAK">
                                    <label class="form-check-label" for="ns_marker_operasi2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Dokter</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_marker_operasi" id="dr_marker_operasi1" value="YA">
                                    <label class="form-check-label" for="dr_marker_operasi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_marker_operasi" id="dr_marker_operasi2" value="TIDAK">
                                    <label class="form-check-label" for="dr_marker_operasi2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Formulir <em>informed consent</em> ditandatangani dan sesuai dengan identitas pada gelang pasien</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="ns_inform_consent_sesuai" name="ns_inform_consent_sesuai" value="1">
                            <label class="form-check-label" for="ns_inform_consent_sesuai">
                                Perawat
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="dr_inform_consent_sesuai" name="dr_inform_consent_sesuai" value="1">
                            <label class="form-check-label" for="dr_inform_consent_sesuai">
                                Dokter
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Jenis alergi pada pasien telah diidentifikasi (termasuk lateks)</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Perawat</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_identifikasi_alergi" id="ns_identifikasi_alergi1" value="YA">
                                    <label class="form-check-label" for="ns_identifikasi_alergi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_identifikasi_alergi" id="ns_identifikasi_alergi2" value="TIDAK">
                                    <label class="form-check-label" for="ns_identifikasi_alergi2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Dokter</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_identifikasi_alergi" id="dr_identifikasi_alergi1" value="YA">
                                    <label class="form-check-label" for="dr_identifikasi_alergi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_identifikasi_alergi" id="dr_identifikasi_alergi2" value="TIDAK">
                                    <label class="form-check-label" for="dr_identifikasi_alergi2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Puasa</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Perawat</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_puasa" id="ns_puasa1" value="YA">
                                    <label class="form-check-label" for="ns_puasa1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ns_puasa" id="ns_puasa2" value="TIDAK">
                                    <label class="form-check-label" for="ns_puasa2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="radio-group">
                            <div class="text-start text-sm-center text-lg-start">Dokter</div>
                            <div class="d-flex flex-column flex-sm-row flex-lg-column">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_puasa" id="dr_puasa1" value="YA">
                                    <label class="form-check-label" for="dr_puasa1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dr_puasa" id="dr_puasa2" value="TIDAK">
                                    <label class="form-check-label" for="dr_puasa2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Lensa intrakuer jenis dan ukuran telah tercatat dalam rekam medis, Jika ya, perawat mengkonfimasi ketersediaan lensa tersebut</div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div>
                            <div class="text-center text-lg-start">Perawat</div>
                            <div class="radio-group">
                                <div class="d-flex flex-row">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ns_cek_lensa_intrakuler" id="ns_cek_lensa_intrakuler1" value="YA">
                                        <label class="form-check-label" for="ns_cek_lensa_intrakuler1">
                                            Ya
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ns_cek_lensa_intrakuler" id="ns_cek_lensa_intrakuler2" value="TIDAK">
                                        <label class="form-check-label" for="ns_cek_lensa_intrakuler2">
                                            Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ns_konfirmasi_lensa" name="ns_konfirmasi_lensa" value="1">
                                <label class="form-check-label" for="ns_konfirmasi_lensa">
                                    Konfirmasi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Perhatikan anestesi khusus termasuk <em>veorus trombo emolism</em></div>
                    <div class="d-flex align-items-center justify-content-evenly">
                        <div>
                            <div class="text-center text-lg-start">Dokter</div>
                            <div class="radio-group">
                                <div class="d-flex flex-row">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dr_cek_anestesi_khusus" id="dr_cek_anestesi_khusus1" value="YA">
                                        <label class="form-check-label" for="dr_cek_anestesi_khusus1">
                                            Ya
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dr_cek_anestesi_khusus" id="dr_cek_anestesi_khusus2" value="TIDAK">
                                        <label class="form-check-label" for="dr_cek_anestesi_khusus2">
                                            Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="dr_konfirmasi_anastersi" name="dr_konfirmasi_anastersi" value="1">
                                <label class="form-check-label" for="dr_konfirmasi_anastersi">
                                    Konfirmasi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="nama_dokter_anastesi" name="nama_dokter_anastesi" aria-label="nama_dokter_anastesi">
                            <option value="" disabled selected>-- Pilih Dokter Anestesi --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="nama_dokter_anastesi">Dokter Anestesi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitSignInBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan <em>Sign In</em></button>
                </div>
            </div>
            <?= form_close(); ?>
            <?= form_open_multipart('/operasi/timeout/update/' . $operasi_safety_timeout['id_timeout'], 'id="SafetyTimeOutForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><em>Time Out</em><br><small class="text-muted fw-normal">Sebelum tindakan bedah</small></div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Apakah setiap anggota tim telah memperkenalkan diri baik nama maupun posisinya?</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="perkenalan_diri" name="perkenalan_diri" value="1">
                            <label class="form-check-label" for="perkenalan_diri">
                                Telah dilakukan
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Dokter operator (dokter spesialis mata), dokter anestesi dan perawat melakukan cek identitas pasien dan rencana tindakan (<em>informed consent</em>) yang dilakukan secara verbal</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="cek_nama_mr" name="cek_nama_mr" value="1">
                            <label class="form-check-label" for="cek_nama_mr">
                                Nama, nomor RM
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="cek_rencana_tindakan" name="cek_rencana_tindakan" value="1">
                            <label class="form-check-label" for="cek_rencana_tindakan">
                                Rencana tindakan
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="cek_marker" name="cek_marker" value="1">
                            <label class="form-check-label" for="cek_marker">
                                Penanda (<em>marker</em>) operasi
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Alergi</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="radio-group">
                            <div class="d-flex flex-row">
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
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="lateks" name="lateks" value="1">
                            <label class="form-check-label" for="lateks">
                                Lateks
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Maka yang tidak dilakukan tindakan diberikan proteksi/perlindungan</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="radio-group">
                            <div class="d-flex flex-row">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="proteksi" id="proteksi1" value="YA">
                                    <label class="form-check-label" for="proteksi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="proteksi" id="proteksi2" value="TIDAK">
                                    <label class="form-check-label" for="proteksi2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Jika ya,</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="proteksi_kasa" name="proteksi_kasa" value="1">
                            <label class="form-check-label" for="proteksi_kasa">
                                Kasa dengan plester
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="proteksi_shield" name="proteksi_shield" value="1">
                            <label class="form-check-label" for="proteksi_shield">
                                <em>Shield drop</em>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Dokter operator (dokter spesialis mata) menginformasikan</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="info_instrumen_ok" name="info_instrumen_ok" value="1">
                            <label class="form-check-label" for="info_instrumen_ok">
                                Instrumen spesifik yang dibutuhkan
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="info_teknik_ok" name="info_teknik_ok" value="1">
                            <label class="form-check-label" for="info_teknik_ok">
                                Langkah/teknik tidak rutin dilakukan yang harus diketahui tim operasi
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Perawat menginformasikan</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="info_steril_instrumen" name="info_steril_instrumen" value="1">
                            <label class="form-check-label" for="info_steril_instrumen">
                                Sterilisasi dan instrumen operasi
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="info_kelengkapan_instrumen" name="info_kelengkapan_instrumen" value="1">
                            <label class="form-check-label" for="info_kelengkapan_instrumen">
                                Kelengkapan instrumen operasi
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Apakah hal tersebut di bawah ini diperlukan untuk mengurangi resiko infeksi operasi?
                        <br><small class="fw-normal">Antibiotik profilaksis</small>
                        <br><small class="fw-normal">Kontrol gula darah</small>
                    </div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="radio-group">
                            <div class="d-flex flex-row">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perlu_antibiotik_dan_guladarah" id="perlu_antibiotik_dan_guladarah1" value="YA">
                                    <label class="form-check-label" for="perlu_antibiotik_dan_guladarah1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perlu_antibiotik_dan_guladarah" id="perlu_antibiotik_dan_guladarah2" value="TIDAK">
                                    <label class="form-check-label" for="perlu_antibiotik_dan_guladarah2">
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
                        <select class="form-select" id="nama_perawat" name="nama_perawat" aria-label="nama_perawat">
                            <option value="" disabled selected>-- Pilih Perawat Sirkuler --</option>
                            <?php foreach ($perawat as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="nama_perawat">Perawat Sirkuler</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitTimeOutBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan <em>Time Out</em></button>
                </div>
            </div>
            <?= form_close(); ?>
            <?= form_open_multipart('/operasi/signout/update/' . $operasi_safety_signout['id_signout'], 'id="SafetySignOutForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><em>Sign Out</em><br><small class="text-muted fw-normal">Prosedur akhir</small></div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Dokter operator dokter spesialis mata/perawat melakukan konfirmasi dengan tim</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="kelengkapan_instrumen" name="kelengkapan_instrumen" value="1">
                            <label class="form-check-label" for="kelengkapan_instrumen">
                                Perhitungan jumlah instrumen sudah lengkap
                            </label>
                        </div>
                        <div class="radio-group">
                            <div class="d-flex flex-row">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="spesimen_kultur" id="spesimen_kultur1" value="SPESIMEN">
                                    <label class="form-check-label" for="spesimen_kultur1">
                                        Spesimen
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="spesimen_kultur" id="spesimen_kultur2" value="KULTUR">
                                    <label class="form-check-label" for="spesimen_kultur2">
                                        Kultur
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="label_pasien" name="label_pasien" value="1">
                            <label class="form-check-label" for="label_pasien">
                                Label pasien
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Adakah masalah pada instrumen?</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="radio-group">
                            <div class="d-flex flex-row">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="masalah_instrumen" id="masalah_instrumen1" value="YA">
                                    <label class="form-check-label" for="masalah_instrumen1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="masalah_instrumen" id="masalah_instrumen2" value="TIDAK">
                                    <label class="form-check-label" for="masalah_instrumen2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div>
                            <input type="text" class="form-control form-control-sm" id="keterangan_masalah" name="keterangan_masalah" value="" autocomplete="off" dir="auto" placeholder="Keterangan jika ya">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2 border-top d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-start">
                    <div>Instruksi khusus untuk menunjang pemulihan pasca operasi</div>
                    <div class="d-flex flex-row flex-lg-column justify-content-center align-items-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="instruksi_khusus" name="instruksi_khusus" value="1">
                            <label class="form-check-label" for="instruksi_khusus">
                                Ada
                            </label>
                        </div>
                        <div>
                            <input type="text" class="form-control form-control-sm" id="keterangan_instruksi" name="keterangan_instruksi" value="" autocomplete="off" dir="auto" placeholder="Keterangan jika ada">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="nama_dokter_operator" name="nama_dokter_operator" aria-label="nama_dokter_operator">
                            <option value="" disabled selected>-- Pilih Dokter Operator --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="nama_dokter_operator">Dokter Operator (Spesialis Mata)</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitSignOutBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan <em>Sign Out</em></button>
                </div>
            </div>
            <?= form_close(); ?>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/operasi/safety/export/' . $operasi['id_sp_operasi']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    function checkRole() {
        <?php if (session()->get('role') == 'Dokter') : ?>
            // SIGN IN
            $("input[name='ns_konfirmasi_identitas']").prop('disabled', true);
            $("input[name='ns_marker_operasi']").prop('disabled', true);
            $("input[name='ns_inform_consent_sesuai']").prop('disabled', true);
            $("input[name='ns_identifikasi_alergi']").prop('disabled', true);
            $("input[name='ns_puasa']").prop('disabled', true);
            $("input[name='ns_cek_lensa_intrakuler']").prop('disabled', true);
            $("input[name='ns_konfirmasi_lensa']").prop('disabled', true);
            // TIME OUT
            $('#SafetyTimeOutForm input, #SafetyTimeOutForm select, #SafetyTimeOutForm button').prop('disabled', true);
        <?php elseif (session()->get('role') == 'Perawat') : ?>
            // SIGN IN
            $("input[name='dr_konfirmasi_identitas']").prop('disabled', true);
            $("input[name='dr_marker_operasi']").prop('disabled', true);
            $("input[name='dr_inform_consent_sesuai']").prop('disabled', true);
            $("input[name='dr_identifikasi_alergi']").prop('disabled', true);
            $("input[name='dr_puasa']").prop('disabled', true);
            $("input[name='dr_cek_anestesi_khusus']").prop('disabled', true);
            $("input[name='dr_konfirmasi_anastersi']").prop('disabled', true);
            // SIGN OUT
            $('#SafetySignOutForm input, #SafetySignOutForm select, #SafetySignOutForm button').prop('disabled', true);
        <?php endif; ?>
    }

    async function fetchSignIn() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/signin/view/') . $operasi_safety_signin['id_signin'] ?>');
            const data = response.data;

            const ns_konfirmasi_identitas = data.ns_konfirmasi_identitas;
            if (ns_konfirmasi_identitas) {
                $("input[name='ns_konfirmasi_identitas'][value='" + ns_konfirmasi_identitas + "']").prop('checked', true);
            }
            const dr_konfirmasi_identitas = data.dr_konfirmasi_identitas;
            if (dr_konfirmasi_identitas) {
                $("input[name='dr_konfirmasi_identitas'][value='" + dr_konfirmasi_identitas + "']").prop('checked', true);
            }
            const ns_marker_operasi = data.ns_marker_operasi;
            if (ns_marker_operasi) {
                $("input[name='ns_marker_operasi'][value='" + ns_marker_operasi + "']").prop('checked', true);
            }
            const dr_marker_operasi = data.dr_marker_operasi;
            if (dr_marker_operasi) {
                $("input[name='dr_marker_operasi'][value='" + dr_marker_operasi + "']").prop('checked', true);
            }
            const ns_inform_consent_sesuai = data.ns_inform_consent_sesuai;
            if (ns_inform_consent_sesuai) {
                $("input[name='ns_inform_consent_sesuai'][value='" + ns_inform_consent_sesuai + "']").prop('checked', true);
            }
            const dr_inform_consent_sesuai = data.dr_inform_consent_sesuai;
            if (dr_inform_consent_sesuai) {
                $("input[name='dr_inform_consent_sesuai'][value='" + dr_inform_consent_sesuai + "']").prop('checked', true);
            }
            const ns_identifikasi_alergi = data.ns_identifikasi_alergi;
            if (ns_identifikasi_alergi) {
                $("input[name='ns_identifikasi_alergi'][value='" + ns_identifikasi_alergi + "']").prop('checked', true);
            }
            const dr_identifikasi_alergi = data.dr_identifikasi_alergi;
            if (dr_identifikasi_alergi) {
                $("input[name='dr_identifikasi_alergi'][value='" + dr_identifikasi_alergi + "']").prop('checked', true);
            }
            const ns_puasa = data.ns_puasa;
            if (ns_puasa) {
                $("input[name='ns_puasa'][value='" + ns_puasa + "']").prop('checked', true);
            }
            const dr_puasa = data.dr_puasa;
            if (dr_puasa) {
                $("input[name='dr_puasa'][value='" + dr_puasa + "']").prop('checked', true);
            }
            const ns_cek_lensa_intrakuler = data.ns_cek_lensa_intrakuler;
            if (ns_cek_lensa_intrakuler) {
                $("input[name='ns_cek_lensa_intrakuler'][value='" + ns_cek_lensa_intrakuler + "']").prop('checked', true);
            }
            const ns_konfirmasi_lensa = data.ns_konfirmasi_lensa;
            if (ns_konfirmasi_lensa) {
                $("input[name='ns_konfirmasi_lensa'][value='" + ns_konfirmasi_lensa + "']").prop('checked', true);
            }
            const dr_cek_anestesi_khusus = data.dr_cek_anestesi_khusus;
            if (dr_cek_anestesi_khusus) {
                $("input[name='dr_cek_anestesi_khusus'][value='" + dr_cek_anestesi_khusus + "']").prop('checked', true);
            }
            const dr_konfirmasi_anastersi = data.dr_konfirmasi_anastersi;
            if (dr_konfirmasi_anastersi) {
                $("input[name='dr_konfirmasi_anastersi'][value='" + dr_konfirmasi_anastersi + "']").prop('checked', true);
            }
            $('#nama_dokter_anastesi').val(data.nama_dokter_anastesi);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchTimeOut() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/timeout/view/') . $operasi_safety_timeout['id_timeout'] ?>');
            const data = response.data;

            const perkenalan_diri = data.perkenalan_diri;
            if (perkenalan_diri) {
                $("input[name='perkenalan_diri'][value='" + perkenalan_diri + "']").prop('checked', true);
            }
            const cek_nama_mr = data.cek_nama_mr;
            if (cek_nama_mr) {
                $("input[name='cek_nama_mr'][value='" + cek_nama_mr + "']").prop('checked', true);
            }
            const cek_rencana_tindakan = data.cek_rencana_tindakan;
            if (cek_rencana_tindakan) {
                $("input[name='cek_rencana_tindakan'][value='" + cek_rencana_tindakan + "']").prop('checked', true);
            }
            const cek_marker = data.cek_marker;
            if (cek_marker) {
                $("input[name='cek_marker'][value='" + cek_marker + "']").prop('checked', true);
            }
            const alergi = data.alergi;
            if (alergi) {
                $("input[name='alergi'][value='" + alergi + "']").prop('checked', true);
            }
            const lateks = data.lateks;
            if (lateks) {
                $("input[name='lateks'][value='" + lateks + "']").prop('checked', true);
            }
            const proteksi = data.proteksi;
            if (proteksi) {
                $("input[name='proteksi'][value='" + proteksi + "']").prop('checked', true);
            }
            const proteksi_kasa = data.proteksi_kasa;
            if (proteksi_kasa) {
                $("input[name='proteksi_kasa'][value='" + proteksi_kasa + "']").prop('checked', true);
            }
            const proteksi_shield = data.proteksi_shield;
            if (proteksi_shield) {
                $("input[name='proteksi_shield'][value='" + proteksi_shield + "']").prop('checked', true);
            }
            const info_instrumen_ok = data.info_instrumen_ok;
            if (info_instrumen_ok) {
                $("input[name='info_instrumen_ok'][value='" + info_instrumen_ok + "']").prop('checked', true);
            }
            const info_teknik_ok = data.info_teknik_ok;
            if (info_teknik_ok) {
                $("input[name='info_teknik_ok'][value='" + info_teknik_ok + "']").prop('checked', true);
            }
            const info_steril_instrumen = data.info_steril_instrumen;
            if (info_steril_instrumen) {
                $("input[name='info_steril_instrumen'][value='" + info_steril_instrumen + "']").prop('checked', true);
            }
            const info_kelengkapan_instrumen = data.info_kelengkapan_instrumen;
            if (info_kelengkapan_instrumen) {
                $("input[name='info_kelengkapan_instrumen'][value='" + info_kelengkapan_instrumen + "']").prop('checked', true);
            }
            const perlu_antibiotik_dan_guladarah = data.perlu_antibiotik_dan_guladarah;
            if (perlu_antibiotik_dan_guladarah) {
                $("input[name='perlu_antibiotik_dan_guladarah'][value='" + perlu_antibiotik_dan_guladarah + "']").prop('checked', true);
            }
            $('#nama_perawat').val(data.nama_perawat);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchSignOut() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/signout/view/') . $operasi_safety_signout['id_signout'] ?>');
            const data = response.data;

            const kelengkapan_instrumen = data.kelengkapan_instrumen;
            if (kelengkapan_instrumen) {
                $("input[name='kelengkapan_instrumen'][value='" + kelengkapan_instrumen + "']").prop('checked', true);
            }
            const spesimen_kultur = data.spesimen_kultur;
            if (spesimen_kultur) {
                $("input[name='spesimen_kultur'][value='" + spesimen_kultur + "']").prop('checked', true);
            }
            const label_pasien = data.label_pasien;
            if (label_pasien) {
                $("input[name='label_pasien'][value='" + label_pasien + "']").prop('checked', true);
            }
            const masalah_instrumen = data.masalah_instrumen;
            if (masalah_instrumen) {
                $("input[name='masalah_instrumen'][value='" + masalah_instrumen + "']").prop('checked', true);
            }
            $('#keterangan_masalah').val(data.keterangan_masalah);
            const instruksi_khusus = data.instruksi_khusus;
            if (instruksi_khusus) {
                $("input[name='instruksi_khusus'][value='" + instruksi_khusus + "']").prop('checked', true);
            }
            $('#keterangan_instruksi').val(data.keterangan_instruksi);
            $('#nama_dokter_operator').val(data.nama_dokter_operator);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);

            if (data.delete) {
                console.log("Received delete from WebSocket, going back...");
                location.href = `<?= base_url('/operasi'); ?>`;
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        $('#SafetySignInForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#SafetySignInForm .is-invalid').removeClass('is-invalid');
            $('#SafetySignInForm .invalid-feedback').text('').hide();
            $('#submitSignInBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan <em>Sign In</em>
            `);

            // Disable form inputs
            $('#SafetySignInForm input, #SafetySignInForm select, #SafetySignInForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/operasi/signin/update/' . $operasi_safety_signin['id_signin']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSignIn();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SafetySignInForm .is-invalid').removeClass('is-invalid');
                    $('#SafetySignInForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if ([
                                    'ns_marker_operasi',
                                    'dr_marker_operasi',
                                    'ns_identifikasi_alergi',
                                    'dr_identifikasi_alergi',
                                    'ns_puasa',
                                    'dr_puasa',
                                    'ns_cek_lensa_intrakuler',
                                    'ns_konfirmasi_lensa',
                                    'dr_cek_anestesi_khusus',
                                    'dr_konfirmasi_anastersi'
                                ].includes(field)) {
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
                $('#submitSignInBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan <em>Sign In</em>
            `);
                $('#SafetySignInForm input, #SafetySignInForm select, #SafetySignInForm button').prop('disabled', false);
                checkRole();
            }
        });

        $('#SafetyTimeOutForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#SafetyTimeOutForm .is-invalid').removeClass('is-invalid');
            $('#SafetyTimeOutForm .invalid-feedback').text('').hide();
            $('#submitTimeOutBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan <em>Time Out</em>
            `);

            // Disable form inputs
            $('#SafetyTimeOutForm input, #SafetyTimeOutForm select, #SafetyTimeOutForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/operasi/timeout/update/' . $operasi_safety_timeout['id_timeout']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchTimeOut();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SafetyTimeOutForm .is-invalid').removeClass('is-invalid');
                    $('#SafetyTimeOutForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if ([
                                    'alergi',
                                    'proteksi',
                                    'perlu_antibiotik_dan_guladarah'
                                ].includes(field)) {
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
                $('#submitTimeOutBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan <em>Time Out</em>
            `);
                $('#SafetyTimeOutForm input, #SafetyTimeOutForm select, #SafetyTimeOutForm button').prop('disabled', false);
                checkRole();
            }
        });

        $('#SafetySignOutForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#SafetySignOutForm .is-invalid').removeClass('is-invalid');
            $('#SafetySignOutForm .invalid-feedback').text('').hide();
            $('#submitSignOutBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan <em>Sign Out</em>
            `);

            // Disable form inputs
            $('#SafetySignOutForm input, #SafetySignOutForm select, #SafetySignOutForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/operasi/signout/update/' . $operasi_safety_signout['id_signout']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSignOut();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SafetySignOutForm .is-invalid').removeClass('is-invalid');
                    $('#SafetySignOutForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if ([
                                    'spesimen_kultur',
                                    'masalah_instrumen'
                                ].includes(field)) {
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
                $('#submitSignOutBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan <em>Sign Out</em>
            `);
                $('#SafetySignOutForm input, #SafetySignOutForm select, #SafetySignOutForm button').prop('disabled', false);
                checkRole();
            }
        });
        // $('#loadingSpinner').hide();
        await checkRole();
        fetchSignIn();
        fetchTimeOut();
        fetchSignOut();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>