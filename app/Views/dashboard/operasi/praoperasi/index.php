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
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/praoperasi/' . $previous['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_booking']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/praoperasi/' . $next['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_booking']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/spko/' . $operasi['id_sp_operasi']); ?>">SPKO</a>
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('operasi/praoperasi/' . $operasi['id_sp_operasi']); ?>">Pra Operasi</a>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/safety/' . $operasi['id_sp_operasi']); ?>">Keselamatan</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_sp_operasi']) ? 'active activeLink' : '' ?>" href="<?= base_url('operasi/praoperasi/' . $list['id_sp_operasi']); ?>">
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
            <?= form_open_multipart('/operasi/praoperasi/update/' . $operasi_pra['id_operasi_pra'], 'id="PraOperasiForm"'); ?>
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
                <div class="fw-bold mb-2 border-bottom">Dokter Penanggung Jawab Pelayanan</div>
                <div><?= $operasi['dokter_operator'] ?></div>
            </div>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="perawat_praoperasi" name="perawat_praoperasi" aria-label="perawat_praoperasi">
                            <option value="" selected>-- Pilih Perawat --</option>
                            <?php foreach ($perawat as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="perawat_praoperasi">Perawat Pra Operasi<span class="text-danger">*</span></label>
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
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">A</span> Catatan Keperawatan Pra Operasi<br><small class="text-muted fw-normal">Diisi oleh perawat ruangan maksimal 1 jam sebelum diantar ke kamar operasi</small></div>
                <div class="mb-2">
                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">1</span> Tanda-tanda Vital
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="ctt_vital_suhu" name="ctt_vital_suhu" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_suhu" step="0.1">
                            <label for="ctt_vital_suhu">Suhu</label>
                        </div>
                        <span class="input-group-text">°C</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="ctt_vital_nadi" name="ctt_vital_nadi" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_nadi">
                            <label for="ctt_vital_nadi">Nadi</label>
                        </div>
                        <span class="input-group-text">×/menit</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="ctt_vital_rr" name="ctt_vital_rr" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_rr">
                            <label for="ctt_vital_rr">Pernapasan</label>
                        </div>
                        <span class="input-group-text">×/menit</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="ctt_vital_td" name="ctt_vital_td" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_td">
                            <label for="ctt_vital_td">Tekanan Darah</label>
                        </div>
                        <span class="input-group-text">mmHg</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="ctt_vital_nyeri" name="ctt_vital_nyeri" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_nyeri">
                        <label for="ctt_vital_nyeri">Skor Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="ctt_vital_tb" name="ctt_vital_tb" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_tb">
                            <label for="ctt_vital_tb">Tinggi Badan</label>
                        </div>
                        <span class="input-group-text">cm</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="input-group has-validation">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="ctt_vital_bb" name="ctt_vital_bb" value="" autocomplete="off" dir="auto" placeholder="ctt_vital_bb">
                            <label for="ctt_vital_bb">Berat Badan</label>
                        </div>
                        <span class="input-group-text">kg</span>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">2</span> Status Mental
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="ctt_mental" name="ctt_mental" aria-label="ctt_mental">
                            <option value="" selected>-- Pilih Kesadaran --</option>
                            <option value="Compos Mentis (GCS 14-15)">Compos Mentis (GCS 14-15)</option>
                            <option value="Apatis (GCS 12-13)">Apatis (GCS 12-13)</option>
                            <option value="Somnolen (GCS 10-11)">Somnolen (GCS 10-11)</option>
                            <option value="Delirium (GCS 9-7)">Delirium (GCS 9-7)</option>
                            <option value="Stupor (Suporos Comma) (GCS 4-6)">Stupor (Suporos Comma) (GCS 4-6)</option>
                            <option value="Koma (GCS 3)">Koma (GCS 3)</option>
                        </select>
                        <label for="ctt_mental">Kesadaran<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="ctt_riwayat_sakit" class="form-label">
                        <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">3</span> Riwayat Penyakit<br><small class="text-muted">Abaikan jika tidak ada</small>
                    </label>
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_asma" name="ctt_riwayat_sakit[]" value="ASMA">
                                <label class="form-check-label" for="ctt_riwayat_sakit_asma">ASMA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_asam_urat" name="ctt_riwayat_sakit[]" value="ASAM URAT">
                                <label class="form-check-label" for="ctt_riwayat_sakit_asam_urat">ASAM URAT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_stroke" name="ctt_riwayat_sakit[]" value="STROKE">
                                <label class="form-check-label" for="ctt_riwayat_sakit_stroke">STROKE</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_kolesterol" name="ctt_riwayat_sakit[]" value="KOLESTEROL">
                                <label class="form-check-label" for="ctt_riwayat_sakit_kolesterol">KOLESTEROL</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_tb_paru" name="ctt_riwayat_sakit[]" value="TB PARU">
                                <label class="form-check-label" for="ctt_riwayat_sakit_tb_paru">TB PARU</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_diabetes" name="ctt_riwayat_sakit[]" value="DIABETES">
                                <label class="form-check-label" for="ctt_riwayat_sakit_diabetes">DIABETES</label>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_hipertensi" name="ctt_riwayat_sakit[]" value="HIPERTENSI">
                                <label class="form-check-label" for="ctt_riwayat_sakit_hipertensi">HIPERTENSI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_jantung" name="ctt_riwayat_sakit[]" value="JANTUNG">
                                <label class="form-check-label" for="ctt_riwayat_sakit_jantung">JANTUNG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_magh" name="ctt_riwayat_sakit[]" value="MAGH">
                                <label class="form-check-label" for="ctt_riwayat_sakit_magh">MAGH</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_tiroid" name="ctt_riwayat_sakit[]" value="TIROID">
                                <label class="form-check-label" for="ctt_riwayat_sakit_tiroid">TIROID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_riwayat_sakit_vertigo" name="ctt_riwayat_sakit[]" value="VERTIGO">
                                <label class="form-check-label" for="ctt_riwayat_sakit_vertigo">VERTIGO</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ctt_riwayat_sakit_lain" name="ctt_riwayat_sakit_lain" value="" autocomplete="off" dir="auto" placeholder="ctt_riwayat_sakit_lain">
                        <label for="ctt_riwayat_sakit_lain">Riwayat Penyakit Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="ctt_pengobatan_sekarang" class="mb-2"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">4</span> Pengobatan Saat Ini</label>
                    <input type="text" class="form-control" id="ctt_pengobatan_sekarang" name="ctt_pengobatan_sekarang" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="ctt_alat_bantu" class="mb-2"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">5</span> Alat Bantu yang Digunakan</label>
                    <input type="text" class="form-control" id="ctt_alat_bantu" name="ctt_alat_bantu" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">6</span> Operasi Sebelumnya
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ctt_operasi_jenis" name="ctt_operasi_jenis" value="" autocomplete="off" dir="auto" placeholder="ctt_operasi_jenis">
                        <label for="ctt_operasi_jenis">Jenis Operasi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="ctt_operasi_tanggal" name="ctt_operasi_tanggal" value="" autocomplete="off" dir="auto" placeholder="ctt_operasi_tanggal">
                        <label for="ctt_operasi_tanggal">Tanggal Operasi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ctt_operasi_lokasi" name="ctt_operasi_lokasi" value="" autocomplete="off" dir="auto" placeholder="ctt_operasi_lokasi">
                        <label for="ctt_operasi_lokasi">Lokasi Operasi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">7</span> Alergi
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="ctt_alergi" class="col col-form-label">
                            Apakah pasien memiliki riwayat alergi?<span class="text-danger">*</span>
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_alergi" id="ctt_alergi1" value="YA">
                                    <label class="form-check-label" for="ctt_alergi1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_alergi" id="ctt_alergi2" value="TIDAK">
                                    <label class="form-check-label" for="ctt_alergi2">
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
                        <input type="text" class="form-control" id="ctt_alergi_jelaskan" name="ctt_alergi_jelaskan" value="" autocomplete="off" dir="auto" placeholder="ctt_alergi_jelaskan">
                        <label for="ctt_alergi_jelaskan">Penjelasan Alergi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">8</span> Hasil Laboratorium
                </div>
                <div class="mb-2">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_lab_hb" name="ctt_lab_hb" value="1">
                                <label class="form-check-label" for="ctt_lab_hb">HB</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_lab_bt" name="ctt_lab_bt" value="1">
                                <label class="form-check-label" for="ctt_lab_bt">BT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_lab_ctaptt" name="ctt_lab_ctaptt" value="1">
                                <label class="form-check-label" for="ctt_lab_ctaptt">CT/APTT</label>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_lab_goldarah" name="ctt_lab_goldarah" value="1">
                                <label class="form-check-label" for="ctt_lab_goldarah">GOLONGAN DARAH</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ctt_lab_urin" name="ctt_lab_urin" value="1">
                                <label class="form-check-label" for="ctt_lab_urin">URIN</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ctt_lab_lainnya" name="ctt_lab_lainnya" value="" autocomplete="off" dir="auto" placeholder="ctt_lab_lainnya">
                        <label for="ctt_lab_lainnya">Hasil Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="ctt_haid" class="col col-form-label">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">9</span> Jika pasien perempuan, apakah sedang haid/menstruasi?<?= ($operasi['jenis_kelamin'] == 'L') ? '<br><small class="text-muted fw-normal">Dinonaktifkan untuk pasien laki-laki</small>' : '<span class="text-danger">*</span>'; ?>
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_haid" id="ctt_haid1" value="YA">
                                    <label class="form-check-label" for="ctt_haid1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_haid" id="ctt_haid2" value="TIDAK">
                                    <label class="form-check-label" for="ctt_haid2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="ctt_kepercayaan" class="col col-form-label">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">10</span> Perhatian khusus terkait budaya dan kepercayaan?<span class="text-danger">*</span>
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_kepercayaan" id="ctt_kepercayaan1" value="YA">
                                    <label class="form-check-label" for="ctt_kepercayaan1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ctt_kepercayaan" id="ctt_kepercayaan2" value="TIDAK">
                                    <label class="form-check-label" for="ctt_kepercayaan2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 table-responsive">
                <div class="fw-bold mb-2 border-bottom"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">B</span> <em>Checklist</em> Persiapan Pasien Pra Operasi</div>
                <table class="table table-borderless bg-body-hwpweb mb-0">
                    <tbody>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">1</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_biometri">
                                        <div>Hasil biometri</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_biometri" name="cek_biometri" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">2</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_retinometri">
                                        <div>Hasil retinometri</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_retinometri" name="cek_retinometri" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">3</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_labor">
                                        <div>Hasil laboratorium (labor lengkap/GDS)</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_labor" name="cek_labor" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">4</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_radiologi">
                                        <div>Hasil radiologi</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_radiologi" name="cek_radiologi" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">5</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_puasa">
                                        <div>Puasa</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_puasa" name="cek_puasa" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">6</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_instruksi">
                                        <div>Instruksi khusus dari dokter</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_instruksi" name="cek_instruksi" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">7</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_lensa">
                                        <div>Lensa</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_lensa" name="cek_lensa" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">8</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_rotgen">
                                        <div>Rontgen</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_rotgen" name="cek_rotgen" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_rotgen_usia">
                                        <div>ECG, usia > 40 tahun</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_rotgen_usia" name="cek_rotgen_usia" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_rotgen_konsul">
                                        <div>Hasil konsul dokter anak/<em>internist</em>/retina</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_rotgen_konsul" name="cek_rotgen_konsul" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">9</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_penyakit">
                                        <div>Cek file: Hepatitis, DM</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_penyakit" name="cek_penyakit" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_hepatitis_akhir">
                                        <div>Jika Hepatitis(+), jadwal paling akhir</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_hepatitis_akhir" name="cek_hepatitis_akhir" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_penyakit_lainnya">
                                        <div>Penyakit lainnya</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="cek_penyakit_lainnya" name="cek_penyakit_lainnya" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tekanan_darah">
                                        <div>Tekanan darah</div>
                                    </label>
                                    <div class="input-group input-group-sm has-validation">
                                        <input type="text" class="form-control form-control-sm" id="cek_tekanan_darah" name="cek_tekanan_darah" value="" autocomplete="off" dir="auto">
                                        <span class="input-group-text">mmHg</span>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_berat_badan">
                                        <div>Berat badan</div>
                                    </label>
                                    <div class="input-group input-group-sm has-validation">
                                        <input type="number" class="form-control form-control-sm" id="cek_berat_badan" name="cek_berat_badan" value="" autocomplete="off" dir="auto">
                                        <span class="input-group-text">kg</span>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">10</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_foto_fundus">
                                        <div>Hasil Foto Fundus</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_foto_fundus" name="cek_foto_fundus" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">11</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_usg">
                                        <div>Hasil USG</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_usg" name="cek_usg" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">12</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_perhiasan">
                                        <div>Melepas perhiasan</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_perhiasan" name="cek_perhiasan" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">13</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_ttd">
                                        <div>Tanda tangan <em>informed consent</em></div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_ttd" name="cek_ttd" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">14</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_cuci">
                                        <div>Cuci muka + ganti pakaian</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_cuci" name="cek_cuci" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">15</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_mark">
                                        <div><em>Sign mark</em> + gelang pasien</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_mark" name="cek_mark" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">16</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_pantocain">
                                        <div>Tetes Pantocain 2%</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_pantocain" name="cek_tetes_pantocain" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">17</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_efrisel1">
                                        <div>Tetes Efrisel I</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_efrisel1" name="cek_tetes_efrisel1" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">18</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_efrisel2">
                                        <div>Tetes Efrisel II</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_efrisel2" name="cek_tetes_efrisel2" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">19</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_midriatil1">
                                        <div>Tetes Midriatil I</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_midriatil1" name="cek_tetes_midriatil1" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">20</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_midriatil2">
                                        <div>Tetes Midriatil II</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_midriatil2" name="cek_tetes_midriatil2" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">21</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_tetes_midriatil3">
                                        <div>Tetes Midriatil III</div>
                                    </label>
                                    <div>
                                        <input type="time" class="form-control form-control-sm" id="cek_tetes_midriatil3" name="cek_tetes_midriatil3" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">22</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_makan">
                                        <div>Makan pagi/siang</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_makan" name="cek_makan" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                                <div class="badge d-grid bg-body text-body border px-2 me-2 date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">23</div>
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="w-100" for="cek_obat">
                                        <div>Obat-obatan sebelumnya</div>
                                    </label>
                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="cek_obat" name="cek_obat" value="1">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 0%;">
                            </td>
                            <td class="py-1 px-0 m-0 align-middle" style="width: 100%;">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
                                    <label class="w-100" for="cek_jenis_obat">
                                        <div>Jenis obat-obatan</div>
                                    </label>
                                    <div class="w-100">
                                        <input type="text" class="form-control form-control-sm" id="cek_jenis_obat" name="cek_jenis_obat" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/operasi/praoperasi/export/' . $operasi['id_sp_operasi']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
    async function fetchPraOperasi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/praoperasi/view/') . $operasi_pra['id_operasi_pra'] ?>');
            const data = response.data;

            $('#perawat_praoperasi').val(data.perawat_praoperasi);
            $('#jenis_operasi').val(data.jenis_operasi);

            // BAGIAN A
            $('#ctt_vital_suhu').val(data.ctt_vital_suhu);
            $('#ctt_vital_nadi').val(data.ctt_vital_nadi);
            $('#ctt_vital_rr').val(data.ctt_vital_rr);
            $('#ctt_vital_td').val(data.ctt_vital_td);
            $('#ctt_vital_nyeri').val(data.ctt_vital_nyeri);
            $('#ctt_vital_tb').val(data.ctt_vital_tb);
            $('#ctt_vital_bb').val(data.ctt_vital_bb);
            $('#ctt_mental').val(data.ctt_mental);
            const ctt_riwayat_sakit = data.ctt_riwayat_sakit;
            $('input[name="ctt_riwayat_sakit[]"]').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (ctt_riwayat_sakit.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('checked', true);
                }
            });
            $('#ctt_riwayat_sakit_lain').val(data.ctt_riwayat_sakit_lain);
            $('#ctt_pengobatan_sekarang').val(data.ctt_pengobatan_sekarang);
            $('#ctt_alat_bantu').val(data.ctt_alat_bantu);
            $('#ctt_operasi_jenis').val(data.ctt_operasi_jenis);
            $('#ctt_operasi_tanggal').val(data.ctt_operasi_tanggal);
            $('#ctt_operasi_lokasi').val(data.ctt_operasi_lokasi);
            const ctt_alergi = data.ctt_alergi;
            if (ctt_alergi) {
                $("input[name='ctt_alergi'][value='" + ctt_alergi + "']").prop('checked', true);
            }
            $('#ctt_alergi_jelaskan').val(data.ctt_alergi_jelaskan);
            const ctt_lab_hb = data.ctt_lab_hb;
            if (ctt_lab_hb) {
                $("input[name='ctt_lab_hb'][value='" + ctt_lab_hb + "']").prop('checked', true);
            }
            const ctt_lab_bt = data.ctt_lab_bt;
            if (ctt_lab_bt) {
                $("input[name='ctt_lab_bt'][value='" + ctt_lab_bt + "']").prop('checked', true);
            }
            const ctt_lab_ctaptt = data.ctt_lab_ctaptt;
            if (ctt_lab_ctaptt) {
                $("input[name='ctt_lab_ctaptt'][value='" + ctt_lab_ctaptt + "']").prop('checked', true);
            }
            const ctt_lab_goldarah = data.ctt_lab_goldarah;
            if (ctt_lab_goldarah) {
                $("input[name='ctt_lab_goldarah'][value='" + ctt_lab_goldarah + "']").prop('checked', true);
            }
            const ctt_lab_urin = data.ctt_lab_urin;
            if (ctt_lab_urin) {
                $("input[name='ctt_lab_urin'][value='" + ctt_lab_urin + "']").prop('checked', true);
            }
            $('#ctt_lab_lainnya').val(data.ctt_lab_lainnya);
            <?php if ($operasi['jenis_kelamin'] == 'L') : ?>
                $("input[name='ctt_haid']").prop('disabled', true);
            <?php elseif ($operasi['jenis_kelamin'] == 'P') : ?>
                const ctt_haid = data.ctt_haid;
                if (ctt_haid) {
                    $("input[name='ctt_haid'][value='" + ctt_haid + "']").prop('checked', true);
                }
            <?php endif; ?>
            const ctt_kepercayaan = data.ctt_kepercayaan;
            if (ctt_kepercayaan) {
                $("input[name='ctt_kepercayaan'][value='" + ctt_kepercayaan + "']").prop('checked', true);
            }

            // BAGIAN B
            const cek_biometri = data.cek_biometri;
            if (cek_biometri) {
                $("input[name='cek_biometri'][value='" + cek_biometri + "']").prop('checked', true);
            }
            const cek_retinometri = data.cek_retinometri;
            if (cek_retinometri) {
                $("input[name='cek_retinometri'][value='" + cek_retinometri + "']").prop('checked', true);
            }
            const cek_labor = data.cek_labor;
            if (cek_labor) {
                $("input[name='cek_labor'][value='" + cek_labor + "']").prop('checked', true);
            }
            const cek_radiologi = data.cek_radiologi;
            if (cek_radiologi) {
                $("input[name='cek_radiologi'][value='" + cek_radiologi + "']").prop('checked', true);
            }
            const cek_puasa = data.cek_puasa;
            if (cek_puasa) {
                $("input[name='cek_puasa'][value='" + cek_puasa + "']").prop('checked', true);
            }
            const cek_instruksi = data.cek_instruksi;
            if (cek_instruksi) {
                $("input[name='cek_instruksi'][value='" + cek_instruksi + "']").prop('checked', true);
            }
            const cek_lensa = data.cek_lensa;
            if (cek_lensa) {
                $("input[name='cek_lensa'][value='" + cek_lensa + "']").prop('checked', true);
            }
            const cek_rotgen = data.cek_rotgen;
            if (cek_rotgen) {
                $("input[name='cek_rotgen'][value='" + cek_rotgen + "']").prop('checked', true);
            }
            const cek_rotgen_usia = data.cek_rotgen_usia;
            if (cek_rotgen_usia) {
                $("input[name='cek_rotgen_usia'][value='" + cek_rotgen_usia + "']").prop('checked', true);
            }
            const cek_rotgen_konsul = data.cek_rotgen_konsul;
            if (cek_rotgen_konsul) {
                $("input[name='cek_rotgen_konsul'][value='" + cek_rotgen_konsul + "']").prop('checked', true);
            }
            const cek_penyakit = data.cek_penyakit;
            if (cek_penyakit) {
                $("input[name='cek_penyakit'][value='" + cek_penyakit + "']").prop('checked', true);
            }
            const cek_hepatitis_akhir = data.cek_hepatitis_akhir;
            if (cek_hepatitis_akhir) {
                $("input[name='cek_hepatitis_akhir'][value='" + cek_hepatitis_akhir + "']").prop('checked', true);
            }
            $('#cek_penyakit_lainnya').val(data.cek_penyakit_lainnya);
            $('#cek_tekanan_darah').val(data.cek_tekanan_darah);
            $('#cek_berat_badan').val(data.cek_berat_badan);
            const cek_foto_fundus = data.cek_foto_fundus;
            if (cek_foto_fundus) {
                $("input[name='cek_foto_fundus'][value='" + cek_foto_fundus + "']").prop('checked', true);
            }
            const cek_usg = data.cek_usg;
            if (cek_usg) {
                $("input[name='cek_usg'][value='" + cek_usg + "']").prop('checked', true);
            }
            const cek_perhiasan = data.cek_perhiasan;
            if (cek_perhiasan) {
                $("input[name='cek_perhiasan'][value='" + cek_perhiasan + "']").prop('checked', true);
            }
            const cek_ttd = data.cek_ttd;
            if (cek_ttd) {
                $("input[name='cek_ttd'][value='" + cek_ttd + "']").prop('checked', true);
            }
            const cek_cuci = data.cek_cuci;
            if (cek_cuci) {
                $("input[name='cek_cuci'][value='" + cek_cuci + "']").prop('checked', true);
            }
            const cek_mark = data.cek_mark;
            if (cek_mark) {
                $("input[name='cek_mark'][value='" + cek_mark + "']").prop('checked', true);
            }
            $('#cek_tetes_pantocain').val(data.cek_tetes_pantocain);
            $('#cek_tetes_efrisel1').val(data.cek_tetes_efrisel1);
            $('#cek_tetes_efrisel2').val(data.cek_tetes_efrisel2);
            $('#cek_tetes_midriatil1').val(data.cek_tetes_midriatil1);
            $('#cek_tetes_midriatil2').val(data.cek_tetes_midriatil2);
            $('#cek_tetes_midriatil3').val(data.cek_tetes_midriatil3);
            const cek_makan = data.cek_makan;
            if (cek_makan) {
                $("input[name='cek_makan'][value='" + cek_makan + "']").prop('checked', true);
            }
            const cek_obat = data.cek_obat;
            if (cek_obat) {
                $("input[name='cek_obat'][value='" + cek_obat + "']").prop('checked', true);
            }
            $('#cek_jenis_obat').val(data.cek_jenis_obat);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        // Fungsi untuk mengunggah gambar dari kanvas
        $('#PraOperasiForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#PraOperasiForm .is-invalid').removeClass('is-invalid');
            $('#PraOperasiForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#PraOperasiForm input, #PraOperasiForm select, #PraOperasiForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/operasi/praoperasi/update/' . $operasi_pra['id_operasi_pra']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchPraOperasi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#PraOperasiForm .is-invalid').removeClass('is-invalid');
                    $('#PraOperasiForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['ctt_alergi', 'ctt_haid', 'ctt_kepercayaan'].includes(field)) {
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
                $('#PraOperasiForm input, #PraOperasiForm select, #PraOperasiForm button').prop('disabled', false);
                <?php if ($operasi['jenis_kelamin'] == 'L') : ?>
                    $("input[name='ctt_haid']").prop('disabled', true);
                <?php endif; ?>
            }
        });
        // $('#loadingSpinner').hide();
        fetchPraOperasi();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>