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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <?php endif; ?>
                        <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/optik/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/laporanrajal/' . $rawatjalan['id_rawat_jalan']); ?>">Tindakan Rajal</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/layanan/' . $rawatjalan['id_rawat_jalan']); ?>">Layanan</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink' : '' ?>" href="<?= base_url('rawatjalan/asesmen/' . $list['id_rawat_jalan']); ?>">
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
            <?= form_open_multipart('/rawatjalan/asesmen/update/' . $asesmen['id_asesmen'], 'id="asesmenForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Dokter Penanggung Jawab Pelayanan</div>
                <div><?= $rawatjalan['dokter'] ?></div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Anamnesis (S)</div>
                <div class="mb-2">
                    <label for="keluhan_utama">Keluhan Utama<span class="text-danger">*</span></label>
                    <textarea class="form-control" id="keluhan_utama" name="keluhan_utama" rows="2" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="riwayat_penyakit_sekarang">Riwayat Penyakit Sekarang</label>
                    <textarea class="form-control" id="riwayat_penyakit_sekarang" name="riwayat_penyakit_sekarang" rows="2" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="riwayat_penyakit_dahulu">Riwayat Penyakit Dahulu</label>
                    <textarea class="form-control" id="riwayat_penyakit_dahulu" name="riwayat_penyakit_dahulu" rows="2" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="riwayat_penyakit_keluarga">Riwayat Penyakit Keluarga</label>
                    <textarea class="form-control" id="riwayat_penyakit_keluarga" name="riwayat_penyakit_keluarga" rows="2" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <label for="riwayat_pengobatan">Riwayat Pengobatan</label>
                        <textarea class="form-control" id="riwayat_pengobatan" name="riwayat_pengobatan" rows="2" style="resize: none;"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col">
                        <label for="riwayat_sosial_pekerjaan">Riwayat Pekerjaan</label>
                        <textarea class="form-control" id="riwayat_sosial_pekerjaan" name="riwayat_sosial_pekerjaan" rows="2" style="resize: none;"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemeriksaan Umum</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <div class="mb-2">
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
                                <label for="kesadaran">Kesadaran<span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="tekanan_darah" name="tekanan_darah" value="" autocomplete="off" dir="auto" placeholder="tekanan_darah">
                                    <label for="tekanan_darah">Tekanan Darah</label>
                                </div>
                                <span class="input-group-text">mmHg</span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="nadi" name="nadi" value="" autocomplete="off" dir="auto" placeholder="nadi">
                                    <label for="nadi">Nadi</label>
                                </div>
                                <span class="input-group-text">×/menit</span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="suhu" name="suhu" value="" autocomplete="off" dir="auto" placeholder="suhu" step="0.1">
                                    <label for="suhu">Suhu</label>
                                </div>
                                <span class="input-group-text">°C</span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div>
                            <div class="input-group has-validation">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="pernapasan" name="pernapasan" value="" autocomplete="off" dir="auto" placeholder="pernapasan">
                                    <label for="pernapasan">Pernapasan</label>
                                </div>
                                <span class="input-group-text">×/menit</span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="mb-2">
                            <div class="row gx-1 radio-group">
                                <label for="keadaan_umum" class="col col-form-label">Keadaan Umum<span class="text-danger">*</span></label>
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
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="row gx-1 radio-group">
                                <label for="alergi" class="col col-form-label">Alergi<span class="text-danger">*</span></label>
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
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="alergi_keterangan" name="alergi_keterangan" value="" autocomplete="off" dir="auto" placeholder="alergi_keterangan">
                                <label for="alergi_keterangan">Keterangan Alergi (Jika Ya)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="sakit_lainnya" class="form-label">
                        Sakit Lainnya<br><small class="text-muted">Abaikan jika tidak ada</small>
                    </label>
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_asma" name="sakit_lainnya[]" value="ASMA">
                                <label class="form-check-label" for="sakit_lainnya_asma">ASMA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_asam_urat" name="sakit_lainnya[]" value="ASAM URAT">
                                <label class="form-check-label" for="sakit_lainnya_asam_urat">ASAM URAT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_stroke" name="sakit_lainnya[]" value="STROKE">
                                <label class="form-check-label" for="sakit_lainnya_stroke">STROKE</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_kolesterol" name="sakit_lainnya[]" value="KOLESTEROL">
                                <label class="form-check-label" for="sakit_lainnya_kolesterol">KOLESTEROL</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_tb_paru" name="sakit_lainnya[]" value="TB PARU">
                                <label class="form-check-label" for="sakit_lainnya_tb_paru">TB PARU</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_diabetes" name="sakit_lainnya[]" value="DIABETES">
                                <label class="form-check-label" for="sakit_lainnya_diabetes">DIABETES</label>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_hipertensi" name="sakit_lainnya[]" value="HIPERTENSI">
                                <label class="form-check-label" for="sakit_lainnya_hipertensi">HIPERTENSI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_jantung" name="sakit_lainnya[]" value="JANTUNG">
                                <label class="form-check-label" for="sakit_lainnya_jantung">JANTUNG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_magh" name="sakit_lainnya[]" value="MAGH">
                                <label class="form-check-label" for="sakit_lainnya_magh">MAGH</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_tiroid" name="sakit_lainnya[]" value="TIROID">
                                <label class="form-check-label" for="sakit_lainnya_tiroid">TIROID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_vertigo" name="sakit_lainnya[]" value="VERTIGO">
                                <label class="form-check-label" for="sakit_lainnya_vertigo">VERTIGO</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_ginjal" name="sakit_lainnya[]" value="GINJAL">
                                <label class="form-check-label" for="sakit_lainnya_ginjal">GINJAL</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemeriksaan Fisik (O)</div>
                <?php if (session()->get('role') != 'Perawat') : ?>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm bg-gradient mb-2" type="button" id="addMataButton">
                            <i class="fa-solid fa-plus"></i> Tambah Pemeriksaan Fisik
                        </button>
                    </div>
                <?php endif; ?>
                <div id="empty-placeholder" class="my-3 text-center" style="display: none;">
                    <img src="<?= base_url('assets/images/eye-svgrepo-com.svg') ?>" style="height: 7rem;" class="mb-2">
                    <h3>Pemeriksaan Fisik</h3>
                    <?php if (session()->get('role') != 'Perawat') : ?>
                        <div class="text-muted">Klik "Tambah Pemeriksaan Fisik" untuk menambahkan pemeriksaan fisik</div>
                    <?php else : ?>
                        <div class="text-muted">Tidak ada pemeriksaan fisik</div>
                    <?php endif; ?>
                </div>
                <div id="pemeriksaanFisikList" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <?php for ($i = 0; $i < 6; $i++) : ?>
                        <div class="col">
                            <div class="card shadow-sm h-100" style="cursor: wait;">
                                <div class="card-img-top" style="background-color: var(--bs-card-cap-bg); aspect-ratio: 4/3; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);"></div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="align-self-center w-100 placeholder-glow">
                                            <span class="placeholder" style="width: 100%;"></span><br>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="w-100 placeholder-glow">
                                        <small><span class="placeholder" style="width: 100%;"></span></small><br>
                                        <small><small><span class="placeholder" style="width: 100%;"></span></small></small>
                                    </div>
                                </div>
                                <?php if (session()->get('role') != 'Perawat') : ?>
                                    <div class="card-footer d-flex justify-content-end gap-1">
                                        <a class="btn btn-body btn-sm bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                        <a class="btn btn-danger bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="card overflow-auto mt-3 shadow-sm">
                    <div class="table-responsive m-1">
                        <table class="table m-0 table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-1 align-middle"></th>
                                    <th scope="col" class="text-center p-1 align-middle">OD</th>
                                    <th scope="col" class="text-center p-1 align-middle">OS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-center align-middle p-1 text-nowrap">Visus UCVA</th>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="od_ucva" name="od_ucva" value="" autocomplete="off" dir="auto" placeholder="" list="od_ucva_list">
                                        <datalist id="od_ucva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="os_ucva" name="os_ucva" value="" autocomplete="off" dir="auto" placeholder="" list="os_ucva_list">
                                        <datalist id="os_ucva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center align-middle p-1 text-nowrap">Visus BCVA</th>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="od_bcva" name="od_bcva" value="" autocomplete="off" dir="auto" placeholder="" list="od_bcva_list">
                                        <datalist id="od_bcva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="os_bcva" name="os_bcva" value="" autocomplete="off" dir="auto" placeholder="" list="os_bcva_list">
                                        <datalist id="os_bcva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center align-middle p-1 text-nowrap">Tono</th>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="tono_od" name="tono_od" value="" autocomplete="off" dir="auto" placeholder="">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" id="tono_os" name="tono_os" value="" autocomplete="off" dir="auto" placeholder="">
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
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_1" name="diagnosa_medis_1" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icdx_kode_1" name="icdx_kode_1" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_2" name="diagnosa_medis_2" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icdx_kode_2" name="icdx_kode_2" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_3" name="diagnosa_medis_3" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icdx_kode_3" name="icdx_kode_3" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_4" name="diagnosa_medis_4" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icdx_kode_4" name="icdx_kode_4" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_5" name="diagnosa_medis_5" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icdx_kode_5" name="icdx_kode_5" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Tindakan (P)</div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_1" name="terapi_1" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icd9_kode_1" name="icd9_kode_1" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_2" name="terapi_2" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icd9_kode_2" name="icd9_kode_2" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_3" name="terapi_3" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icd9_kode_3" name="icd9_kode_3" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_4" name="terapi_4" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icd9_kode_4" name="icd9_kode_4" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row g-2 align-items-start mb-2">
                    <div class="col-sm">
                        <input type="text" class="form-control my-auto" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_5" name="terapi_5" value="" autocomplete="off" dir="auto">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-6 col-lg-5">
                        <select class="form-select " id="icd9_kode_5" name="icd9_kode_5" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?>>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
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
    </div>
    <?php if (session()->get('role') != 'Perawat') : ?>
        <div class="modal fade" id="mataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="mataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
                <form id="mataForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                    <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                        <h6 class="pe-2 modal-title fs-6 text-truncate" id="mataModalLabel" style="font-weight: bold;"></h6>
                        <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-2">
                        <input type="hidden" id="id_asesmen_mata" name="id_asesmen_mata" value="">
                        <div class="mb-1 mt-1">
                            <label for="gambar" class="form-label mb-0">Unggah Gambar (maks 8 MB)</label>
                            <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div id="gambar_preview_div" style="display: none;" class="mb-1 mt-1">
                            <div class="d-flex justify-content-center">
                                <img id="gambar_preview" src="#" alt="Gambar" class="img-thumbnail" style="max-width: 100%">
                            </div>
                        </div>
                        <div class="form-floating mb-1 mt-1">
                            <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="keterangan" id="keterangan" name="keterangan">
                            <label for="keterangan">Keterangan</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                        <!-- Progress bar -->
                        <div class="mb-1 mt-1 w-100" id="uploadProgressDiv">
                            <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient" role="progressbar" style="width: 0%; transition: none;" id="uploadProgressBar"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between w-100">
                            <div>
                                <button type="button" id="cancelButton" class="btn btn-danger bg-gradient" style="display: none;" disabled>
                                    <i class="fa-solid fa-xmark"></i> Batalkan
                                </button>
                            </div>
                            <button type="submit" id="submitButton" class="btn btn-primary bg-gradient">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <div class="modal fade" id="mataPreviewModal" tabindex="-1" aria-labelledby="mataPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable">
            <form id="mataPreviewForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="mataPreviewModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div id="gambar_preview_2_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="gambar_preview_2" src="#" alt="Gambar" class="img-thumbnail" style="width: 100%">
                        </div>
                    </div>
                    <div id="keterangan_preview"></div>
                    <div><small id="waktu_dibuat_preview" class="text-muted"></small></div>
                </div>
            </form>
        </div>
    </div>
    <?php if (session()->get('role') != 'Perawat') : ?>
        <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                    <div class="modal-body p-4">
                        <h5 class="mb-0" id="deleteMessage"></h5>
                        <div class="row gx-2 pt-4">
                            <div class="col d-grid">
                                <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Tidak</button>
                            </div>
                            <div class="col d-grid">
                                <button type="button" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Ya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
            const sakit_lainnya = data.sakit_lainnya; // Data yang diterima dari server
            $('input[name="sakit_lainnya[]"]').each(function() {
                const value = $(this).val(); // Dapatkan nilai dari checkbox
                if (sakit_lainnya.includes(value)) {
                    $(this).prop('checked', true); // Tandai sebagai tercentang
                }
            });

            // Pemeriksaan Fisik (O)
            $('#tono_od').val(data.tono_od);
            $('#tono_os').val(data.tono_os);
            $('#od_ucva').val(data.od_ucva);
            $('#od_bcva').val(data.od_bcva);
            $('#os_ucva').val(data.os_ucva);
            $('#os_bcva').val(data.os_bcva);

            // Diagnosis Medis (A)
            $('#diagnosa_medis_1').val(data.diagnosa_medis_1);
            if (data.icdx_kode_1 !== null) {
                const icdx_kode_1 = new Option(data.icdx_kode_1, data.icdx_kode_1, true, true);
                $('#icdx_kode_1').append(icdx_kode_1).trigger('change');
            }

            $('#diagnosa_medis_2').val(data.diagnosa_medis_2);
            if (data.icdx_kode_2 !== null) {
                const icdx_kode_2 = new Option(data.icdx_kode_2, data.icdx_kode_2, true, true);
                $('#icdx_kode_2').append(icdx_kode_2).trigger('change');
            }

            $('#diagnosa_medis_3').val(data.diagnosa_medis_3);
            if (data.icdx_kode_3 !== null) {
                const icdx_kode_3 = new Option(data.icdx_kode_3, data.icdx_kode_3, true, true);
                $('#icdx_kode_3').append(icdx_kode_3).trigger('change');
            }

            $('#diagnosa_medis_4').val(data.diagnosa_medis_4);
            if (data.icdx_kode_4 !== null) {
                const icdx_kode_4 = new Option(data.icdx_kode_4, data.icdx_kode_4, true, true);
                $('#icdx_kode_4').append(icdx_kode_4).trigger('change');
            }

            $('#diagnosa_medis_5').val(data.diagnosa_medis_5);
            if (data.icdx_kode_5 !== null) {
                const icdx_kode_5 = new Option(data.icdx_kode_5, data.icdx_kode_5, true, true);
                $('#icdx_kode_5').append(icdx_kode_5).trigger('change');
            }

            // Tindakan (P)
            $('#terapi_1').val(data.terapi_1);
            if (data.icd9_kode_1 !== null) {
                const icd9_kode_1 = new Option(data.icd9_kode_1, data.icd9_kode_1, true, true);
                $('#icd9_kode_1').append(icd9_kode_1).trigger('change');
            }

            $('#terapi_2').val(data.terapi_2);
            if (data.icd9_kode_2 !== null) {
                const icd9_kode_2 = new Option(data.icd9_kode_2, data.icd9_kode_2, true, true);
                $('#icd9_kode_2').append(icd9_kode_2).trigger('change');
            }

            $('#terapi_3').val(data.terapi_3);
            if (data.icd9_kode_3 !== null) {
                const icd9_kode_3 = new Option(data.icd9_kode_3, data.icd9_kode_3, true, true);
                $('#icd9_kode_3').append(icd9_kode_3).trigger('change');
            }

            $('#terapi_4').val(data.terapi_4);
            if (data.icd9_kode_4 !== null) {
                const icd9_kode_4 = new Option(data.icd9_kode_4, data.icd9_kode_4, true, true);
                $('#icd9_kode_4').append(icd9_kode_4).trigger('change');
            }

            $('#terapi_5').val(data.terapi_5);
            if (data.icd9_kode_5 !== null) {
                const icd9_kode_5 = new Option(data.icd9_kode_5, data.icd9_kode_5, true, true);
                $('#icd9_kode_5').append(icd9_kode_5).trigger('change');
            }

            $('#icdx_kode_1, #icdx_kode_2, #icdx_kode_3, #icdx_kode_4, #icdx_kode_5').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: "ICD-10",
                disabled: <?= (session()->get('role') == 'Perawat') ? 'true' : 'false' ?>,
                allowClear: true,
                ajax: {
                    url: '<?= base_url('rawatjalan/asesmen/icdx') ?>',
                    dataType: 'json',
                    delay: 250, // Tambahkan debounce
                    data: function(params) {
                        return {
                            search: params.term, // Pencarian berdasarkan input
                            offset: (params.page || 0) * 50, // Pagination
                            limit: 50
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(item => ({
                                id: item.icdKode,
                                text: item.icdKode, // Teks untuk pencarian
                                nama: item.icdNamaInggris // Tambahan data untuk custom HTML
                            })),
                            pagination: {
                                more: data.data.length >= 50
                            }
                        };
                    }
                },
                minimumInputLength: 1,
                templateResult: function(data) {
                    // Format untuk tampilan hasil pencarian
                    if (!data.id) {
                        return data.text; // Untuk placeholder
                    }

                    const template = `
                        <div>
                            <strong>${data.text}</strong>
                        </div>
                        <div>
                            <small>${data.nama}</small>
                        </div>
                    `;
                    return $(template);
                },
                templateSelection: function(data) {
                    return data.text && data.text !== 'null' ? data.text : '';
                },
                escapeMarkup: function(markup) {
                    // Biarkan HTML tetap diproses
                    return markup;
                }
            });

            $('#icdx_kode_1').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada diagnosa_medis_1
                if (selectedData.nama) {
                    $('#diagnosa_medis_1').val(selectedData.nama);
                }
            });

            $('#icdx_kode_2').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada diagnosa_medis_1
                if (selectedData.nama) {
                    $('#diagnosa_medis_2').val(selectedData.nama);
                }
            });

            $('#icdx_kode_3').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada diagnosa_medis_1
                if (selectedData.nama) {
                    $('#diagnosa_medis_3').val(selectedData.nama);
                }
            });

            $('#icdx_kode_4').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada diagnosa_medis_1
                if (selectedData.nama) {
                    $('#diagnosa_medis_4').val(selectedData.nama);
                }
            });

            $('#icdx_kode_5').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada diagnosa_medis_1
                if (selectedData.nama) {
                    $('#diagnosa_medis_5').val(selectedData.nama);
                }
            });

            $('#icd9_kode_1, #icd9_kode_2, #icd9_kode_3, #icd9_kode_4, #icd9_kode_5').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: "ICD-9 CM",
                disabled: <?= (session()->get('role') == 'Perawat') ? 'true' : 'false' ?>,
                allowClear: true,
                ajax: {
                    url: '<?= base_url('rawatjalan/asesmen/icd9') ?>',
                    dataType: 'json',
                    delay: 250, // Tambahkan debounce
                    data: function(params) {
                        return {
                            search: params.term, // Pencarian berdasarkan input
                            offset: (params.page || 0) * 50, // Pagination
                            limit: 50
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(item => ({
                                id: item.icdKode,
                                text: item.icdKode, // Teks untuk pencarian
                                nama: item.icdNamaInggris // Tambahan data untuk custom HTML
                            })),
                            pagination: {
                                more: data.data.length >= 50
                            }
                        };
                    }
                },
                minimumInputLength: 1,
                templateResult: function(data) {
                    // Format untuk tampilan hasil pencarian
                    if (!data.id) {
                        return data.text; // Untuk placeholder
                    }

                    const template = `
                        <div>
                            <strong>${data.text}</strong>
                        </div>
                        <div>
                            <small>${data.nama}</small>
                        </div>
                    `;
                    return $(template);
                },
                templateSelection: function(data) {
                    return data.text && data.text !== 'null' ? data.text : '';
                },
                escapeMarkup: function(markup) {
                    // Biarkan HTML tetap diproses
                    return markup;
                }
            });

            $('#icd9_kode_1').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada terapi_1
                if (selectedData.nama) {
                    $('#terapi_1').val(selectedData.nama);
                }
            });

            $('#icd9_kode_2').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada terapi_1
                if (selectedData.nama) {
                    $('#terapi_2').val(selectedData.nama);
                }
            });

            $('#icd9_kode_3').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada terapi_1
                if (selectedData.nama) {
                    $('#terapi_3').val(selectedData.nama);
                }
            });

            $('#icd9_kode_4').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada terapi_1
                if (selectedData.nama) {
                    $('#terapi_4').val(selectedData.nama);
                }
            });

            $('#icd9_kode_5').on('select2:select', function(e) {
                // Dapatkan data item yang dipilih
                const selectedData = e.params.data;

                // Ubah nilai pada terapi_1
                if (selectedData.nama) {
                    $('#terapi_5').val(selectedData.nama);
                }
            });
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchAsesmenMata() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/mata/list/') . $rawatjalan['id_rawat_jalan'] ?>');

            const data = response.data;
            $('#pemeriksaanFisikList').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                $('#empty-placeholder').show();
                $('#pemeriksaanFisikList').hide();
            } else {
                $('#empty-placeholder').hide();
                data.forEach(function(asesmen_mata) {
                    const keterangan = asesmen_mata.keterangan ? asesmen_mata.keterangan : `<em>Tidak ada keterangan</em>`;
                    const penunjangScanElement = `
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-img-top gambar-scan" role="button" style="background-image: url('<?= base_url('uploads/asesmen_mata') ?>/${asesmen_mata.gambar}?t=${new Date().getTime()}'); background-color: var(--bs-card-cap-bg); aspect-ratio: 4/3; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);" data-id="${asesmen_mata.id_asesmen_mata}"></div>
                    <div class="card-body">
                        <div>
                            <small>${keterangan}</small><br>
                            <small class="text-body-secondary date"><small>${asesmen_mata.waktu_dibuat}</small></small>
                        </div>
                    </div>
                    <?php if (session()->get('role') != 'Perawat') : ?>
                    <div class="card-footer d-flex justify-content-end gap-1">
                        <button class="btn btn-body btn-sm bg-gradient edit-btn" data-id="${asesmen_mata.id_asesmen_mata}"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                        <button class="btn btn-danger btn-sm bg-gradient delete-btn" data-id="${asesmen_mata.id_asesmen_mata}"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
                `;
                    $('#pemeriksaanFisikList').show();
                    $('#pemeriksaanFisikList').append(penunjangScanElement);
                });
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#pemeriksaanFisikList').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function loadVisus() {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/asesmen/listvisus') ?>');
            const visus = response.data.data;

            // Array dari ID datalist yang ingin diisi
            const dataListIds = ['#od_ucva_list', '#os_ucva_list', '#od_bcva_list', '#os_bcva_list'];

            // Kosongkan dan isi setiap datalist
            dataListIds.forEach(id => {
                const dataList = $(id);
                dataList.empty(); // Kosongkan datalist sebelumnya
                visus.forEach(item => {
                    dataList.append(`<option value="${item.value}"></option>`);
                });
            });
        } catch (error) {
            console.error('Gagal memuat visus:', error);
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

        $(document).on('click', '.gambar-scan', async function(ə) {
            ə.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $('#loadingSpinner').show();

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/asesmen/mata/view') ?>/` + id);
                let data = response.data;
                console.log(data);

                $('#mataPreviewModalLabel').text('Pratinjau Gambar Pemeriksaan Fisik');
                if (data.gambar) {
                    $('#gambar_preview_2').attr('src', `<?= base_url('uploads/asesmen_mata') ?>/` + data.gambar);
                    $('#gambar_preview_2_div').show();
                } else {
                    $('#gambar_preview_2_div').hide();
                }
                const keterangan = data.keterangan ? data.keterangan : `<em>Tidak ada keterangan</em>`;
                $('#keterangan_preview').html(keterangan);
                $('#waktu_dibuat_preview').text(data.waktu_dibuat);
                $('#mataPreviewModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#loadingSpinner').hide();
            }
        });

        <?php if (session()->get('role') != 'Perawat') : ?>
            // Tampilkan modal tambah mata penunjang
            $('#addMataButton').click(function() {
                $('#mataModalLabel').text('Tambah Pemeriksaan Fisik'); // Ubah judul modal menjadi 'Tambah Pemeriksaan Fisik'
                $('#id_asesmen_mata').val('');
                $('#mataModal').modal('show'); // Tampilkan modal resep luar
            });

            $('#gambar').change(function() {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#gambar_preview').attr('src', e.target.result);
                    $('#gambar_preview_div').show();
                };
                reader.readAsDataURL(this.files[0]);
            });


            $(document).on('click', '.edit-btn', async function(ə) {
                ə.preventDefault();
                var $this = $(this);
                var id = $this.data('id');
                $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Edit`);

                try {
                    let response = await axios.get(`<?= base_url('/rawatjalan/asesmen/mata/view') ?>/` + id);
                    let data = response.data;
                    console.log(data);

                    $('#mataModalLabel').text('Edit Pemeriksaan Fisik');
                    $('#id_asesmen_mata').val(data.id_asesmen_mata);
                    if (data.gambar) {
                        $('#gambar_preview').attr('src', `<?= base_url('uploads/asesmen_mata') ?>/` + data.gambar);
                        $('#gambar_preview_div').show();
                    } else {
                        $('#gambar_preview_div').hide();
                    }
                    $('#keterangan').val(data.keterangan);
                    $('#mataModal').modal('show');
                } catch (error) {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                } finally {
                    $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i> Edit`);
                }
            });

            // Store the ID of the example to be deleted
            var id_asesmen_mata;

            // Show delete confirmation modal
            $(document).on('click', '.delete-btn', function(ə) {
                ə.preventDefault();
                id_asesmen_mata = $(this).data('id');
                $('#deleteMessage').html(`Hapus pemeriksaan fisik ini?`);
                $('#deleteModal').modal('show');
            });

            // Confirm deletion
            $('#confirmDeleteBtn').click(async function() {
                $('#deleteModal button').prop('disabled', true);
                $('#deleteMessage').html(`Menghapus, silakan tunggu...`);

                try {
                    // Perform the delete operation
                    let response = await axios.delete('<?= base_url('/rawatjalan/asesmen/mata/delete') ?>/' + id_asesmen_mata);

                    // Show success message
                    showSuccessToast(response.data.message);

                    // Reload the table
                    fetchAsesmenMata();
                } catch (error) {
                    // Handle any error responses
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                } finally {
                    // Re-enable the delete button and hide the modal
                    $('#deleteModal').modal('hide');
                    $('#deleteModal button').prop('disabled', false);
                }
            });

            $('#mataForm').submit(async function(ə) {
                ə.preventDefault();

                var url = $('#id_asesmen_mata').val() ? `<?= base_url('rawatjalan/asesmen/mata/update') ?>` : `<?= base_url('/rawatjalan/asesmen/mata/create/' . $rawatjalan['id_rawat_jalan']) ?>`;
                var formData = new FormData(this);
                console.log("Form URL:", url);
                console.log("Form Data:", formData);

                const CancelToken = axios.CancelToken;
                const source = CancelToken.source();

                // Clear previous validation states
                $('#mataForm .is-invalid').removeClass('is-invalid');
                $('#mataForm .invalid-feedback').text('').hide();

                // Show processing button and progress bar
                $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
                $('#cancelButton').prop('disabled', false).show();
                $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

                // Disable form inputs
                $('#mataForm input').prop('disabled', true);

                try {
                    // Perform the post request with progress handling
                    let response = await axios.post(url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function(progressEvent) {
                            if (progressEvent.lengthComputable) {
                                var percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                                $('#uploadProgressBar').css('width', percent + '%');
                                $('#uploadPercentage').html(percent + '%');
                            }
                        },
                        cancelToken: source.token // Attach the token here
                    });

                    // Handle successful response
                    if (response.data.success) {
                        showSuccessToast(response.data.message, 'success');
                        $('#mataModal').modal('hide');
                        $('#uploadProgressBar').css('width', '0%');
                        fetchAsesmenMata();
                    } else {
                        console.log("Validation Errors:", response.data.errors);

                        // Clear previous validation states
                        $('#mataForm .is-invalid').removeClass('is-invalid');
                        $('#mataForm .invalid-feedback').text('').hide();

                        // Display new validation errors
                        for (const field in response.data.errors) {
                            if (response.data.errors.hasOwnProperty(field)) {
                                const fieldElement = $('#' + field);
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
                        console.error('Perbaiki kesalahan pada formulir.');
                        $('#uploadProgressBar').addClass('bg-danger');
                    }
                } catch (error) {
                    if (axios.isCancel(error)) {
                        showFailedToast(error.message);
                        $('#uploadProgressBar').css('width', '0%');
                    } else {
                        showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        $('#uploadProgressBar').addClass('bg-danger');
                    }
                } finally {
                    // Reset the form and UI elements
                    $('#uploadPercentage').html('0%');
                    $('#cancelButton').prop('disabled', true).hide();
                    $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                    $('#mataForm input').prop('disabled', false);
                }

                // Attach the cancel functionality to the close button
                $('#closeBtn, #cancelButton').on('click', function() {
                    source.cancel('Penambahan pemeriksaan fisik telah dibatalkan.');
                });
            });

            $('#mataPreviewModal').on('hidden.bs.modal', function() {
                $('#gambar_preview_2').attr('src', '#');
                $('#gambar_preview_2_div').hide();
                $('#keterangan_preview').html('');
                $('#waktu_dibuat_preview').text('');
            });

            // Reset form saat modal ditutup
            $('#mataModal').on('hidden.bs.modal', function() {
                $('#mataForm')[0].reset();
                $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
                $('#gambar_preview').attr('src', '#');
                $('#gambar_preview_div').hide();
                $('#mataForm .is-invalid').removeClass('is-invalid');
                $('#mataForm .invalid-feedback').text('').hide();
            });
        <?php endif; ?>

        $('#asesmenForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#asesmenForm .is-invalid').removeClass('is-invalid');
            $('#asesmenForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#asesmenForm input, #asesmenForm textarea, #asesmenForm select, #asesmenForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/asesmen/update/' . $asesmen['id_asesmen']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    await fetchAsesmen();
                    loadVisus();
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
                            if (["alergi", "keadaan_umum"].includes(field)) {
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
                                        feedbackElement.text('').hide();
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
                $('#asesmenForm input, #asesmenForm textarea, #asesmenForm select, #asesmenForm button').prop('disabled', false);
            }
        });
        await fetchAsesmen();
        fetchAsesmenMata();
        loadVisus();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>