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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $rawatjalan['no_rm'] ?></div>
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
                        <?php endif; ?>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink' : '' ?>" href="<?= base_url('rawatjalan/asesmen/' . $list['id_rawat_jalan']); ?>">
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
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Anamnesis (S)</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="keluhan_utama" name="keluhan_utama" value="" autocomplete="off" dir="auto" placeholder="keluhan_utama">
                        <label for="keluhan_utama">Keluhan Utama</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="riwayat_penyakit_sekarang" name="riwayat_penyakit_sekarang" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_sekarang">
                        <label for="riwayat_penyakit_sekarang">Riwayat Penyakit Sekarang</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="riwayat_penyakit_dahulu" name="riwayat_penyakit_dahulu" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_dahulu">
                        <label for="riwayat_penyakit_dahulu">Riwayat Penyakit Dahulu</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="riwayat_penyakit_keluarga" name="riwayat_penyakit_keluarga" value="" autocomplete="off" dir="auto" placeholder="riwayat_penyakit_keluarga">
                        <label for="riwayat_penyakit_keluarga">Riwayat Penyakit Keluarga</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_pengobatan" name="riwayat_pengobatan" value="" autocomplete="off" dir="auto" placeholder="riwayat_pengobatan">
                            <label for="riwayat_pengobatan">Riwayat Pengobatan</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="riwayat_sosial_pekerjaan" name="riwayat_sosial_pekerjaan" value="" autocomplete="off" dir="auto" placeholder="riwayat_sosial_pekerjaan">
                            <label for="riwayat_sosial_pekerjaan">Riwayat Pekerjaan</label>
                            <div class="invalid-feedback"></div>
                        </div>
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
                                <label for="kesadaran">Kesadaran</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tekanan_darah" name="tekanan_darah" value="" autocomplete="off" dir="auto" placeholder="tekanan_darah">
                                <label for="tekanan_darah">Tekanan Darah (mmHg)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nadi" name="nadi" value="" autocomplete="off" dir="auto" placeholder="nadi">
                                <label for="nadi">Nadi (×/menit)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="suhu" name="suhu" value="" autocomplete="off" dir="auto" placeholder="suhu">
                                <label for="suhu">Suhu (°C)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="pernapasan" name="pernapasan" value="" autocomplete="off" dir="auto" placeholder="pernapasan">
                                <label for="pernapasan">Pernapasan (×/menit)</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="mb-2">
                            <div class="row gx-1 radio-group">
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
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="kesadaran_mental" name="kesadaran_mental" value="" autocomplete="off" dir="auto" placeholder="kesadaran_mental">
                                <label for="kesadaran_mental">Kesadaran / Mental</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="row gx-1 radio-group">
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
                        <div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="alergi_keterangan" name="alergi_keterangan" value="" autocomplete="off" dir="auto" placeholder="alergi_keterangan">
                                <label for="alergi_keterangan">Keterangan Alergi</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="sakit_lainnya" class="form-label">
                        Sakit Lainnya
                    </label>
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_tidak_ada" name="sakit_lainnya[]" value="TIDAK ADA">
                                <label class="form-check-label" for="sakit_lainnya_tidak_ada">TIDAK ADA</label>
                            </div>
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
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sakit_lainnya_diabetes" name="sakit_lainnya[]" value="DIABETES">
                                <label class="form-check-label" for="sakit_lainnya_diabetes">DIABETES</label>
                            </div>
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
                    <h1><i class="fa-solid fa-eye"></i></h1>
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
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="od_ucva" name="od_ucva" value="" autocomplete="off" dir="auto" placeholder="" list="od_ucva_list">
                                        <datalist id="od_ucva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="os_ucva" name="os_ucva" value="" autocomplete="off" dir="auto" placeholder="" list="os_ucva_list">
                                        <datalist id="os_ucva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center align-middle p-1 text-nowrap">Visus BCVA</th>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="od_bcva" name="od_bcva" value="" autocomplete="off" dir="auto" placeholder="" list="od_bcva_list">
                                        <datalist id="od_bcva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="os_bcva" name="os_bcva" value="" autocomplete="off" dir="auto" placeholder="" list="os_bcva_list">
                                        <datalist id="os_bcva_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center align-middle p-1 text-nowrap">Tono</th>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="tono_od" name="tono_od" value="" autocomplete="off" dir="auto" placeholder="">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle p-1">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="tono_os" name="tono_os" value="" autocomplete="off" dir="auto" placeholder="">
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
                                <td class="align-middle ps-0 pe-1 pt-0 pb-1" style="width: 100%; min-width: 200px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_1" name="diagnosa_medis_1" value="" autocomplete="off" dir="auto">
                                    <div class="invalid-feedback"></div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-0 pb-1" style="width: 0%; min-width: 100px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icdx_kode_1" name="icdx_kode_1" value="" autocomplete="off" dir="auto" placeholder="ICD 10" list="icdx_kode_1_list">
                                    <datalist id="icdx_kode_1_list">
                                    </datalist>
                                    <div class="invalid-feedback"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_2" name="diagnosa_medis_2" value="" autocomplete="off" dir="auto">
                                    <div class="invalid-feedback"></div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icdx_kode_2" name="icdx_kode_2" value="" autocomplete="off" dir="auto" placeholder="ICD 10" list="icdx_kode_2_list">
                                    <datalist id="icdx_kode_2_list">
                                    </datalist>
                                    <div class="invalid-feedback"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_3" name="diagnosa_medis_3" value="" autocomplete="off" dir="auto">
                                    <div class="invalid-feedback"></div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icdx_kode_3" name="icdx_kode_3" value="" autocomplete="off" dir="auto" placeholder="ICD 10" list="icdx_kode_3_list">
                                    <datalist id="icdx_kode_3_list">
                                    </datalist>
                                    <div class="invalid-feedback"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_4" name="diagnosa_medis_4" value="" autocomplete="off" dir="auto">
                                    <div class="invalid-feedback"></div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icdx_kode_4" name="icdx_kode_4" value="" autocomplete="off" dir="auto" placeholder="ICD 10" list="icdx_kode_4_list">
                                    <datalist id="icdx_kode_4_list">
                                    </datalist>
                                    <div class="invalid-feedback"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-1 pb-0" style="width: 100%; min-width: 200px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="diagnosa_medis_5" name="diagnosa_medis_5" value="" autocomplete="off" dir="auto">
                                    <div class="invalid-feedback"></div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-1 pb-0" style="width: 0%; min-width: 100px;">
                                    <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icdx_kode_5" name="icdx_kode_5" value="" autocomplete="off" dir="auto" placeholder="ICD 10" list="icdx_kode_5_list">
                                    <datalist id="icdx_kode_5_list">
                                    </datalist>
                                    <div class="invalid-feedback"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <div class="fw-bold mb-2 border-bottom">Tindakan (P)</div>
                    <div class="table-responsive mb-3">
                        <table class="table mb-0 table-borderless">
                            <tbody>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-0 pb-1" style="width: 100%; min-width: 200px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_1" name="terapi_1" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-0 pb-1" style="width: 0%; min-width: 100px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icd9_kode_1" name="icd9_kode_1" value="" autocomplete="off" dir="auto" placeholder="ICD 9" list="icd9_kode_1_list">
                                        <datalist id="icd9_kode_1_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_2" name="terapi_2" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icd9_kode_2" name="icd9_kode_2" value="" autocomplete="off" dir="auto" placeholder="ICD 9" list="icd9_kode_2_list">
                                        <datalist id="icd9_kode_2_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_3" name="terapi_3" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icd9_kode_3" name="icd9_kode_3" value="" autocomplete="off" dir="auto" placeholder="ICD 9" list="icd9_kode_3_list">
                                        <datalist id="icd9_kode_3_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-1" style="width: 100%; min-width: 200px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_4" name="terapi_4" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-1" style="width: 0%; min-width: 100px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icd9_kode_4" name="icd9_kode_4" value="" autocomplete="off" dir="auto" placeholder="ICD 9" list="icd9_kode_4_list">
                                        <datalist id="icd9_kode_4_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-0 pe-1 pt-1 pb-0" style="width: 100%; min-width: 200px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="terapi_5" name="terapi_5" value="" autocomplete="off" dir="auto">
                                        <div class="invalid-feedback"></div>
                                    </td>
                                    <td class="align-middle ps-1 pe-0 pt-1 pb-0" style="width: 0%; min-width: 100px;">
                                        <input type="text" class="form-control" <?= (session()->get('role') == 'Perawat') ? 'readonly' : ''; ?> id="icd9_kode_5" name="icd9_kode_5" value="" autocomplete="off" dir="auto" placeholder="ICD 9" list="icd9_kode_5_list">
                                        <datalist id="icd9_kode_5_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
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
    </div>
    <?php if (session()->get('role') != 'Perawat') : ?>
        <div class="modal fade" id="mataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="mataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
                <form id="mataForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                    <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                        <h6 class="pe-2 modal-title fs-6 text-truncate" id="mataModalLabel" style="font-weight: bold;"></h6>
                        <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
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
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
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
                    <div class="modal-body p-4 text-center">
                        <h5 id="deleteMessage"></h5>
                        <h6 class="mb-0" id="deleteSubmessage"></h6>
                    </div>
                    <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                        <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteBtn">Ya</button>
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
            $('#icdx_kode_1').val(data.icdx_kode_1);
            $('#diagnosa_medis_2').val(data.diagnosa_medis_2);
            $('#icdx_kode_2').val(data.icdx_kode_2);
            $('#diagnosa_medis_3').val(data.diagnosa_medis_3);
            $('#icdx_kode_3').val(data.icdx_kode_3);
            $('#diagnosa_medis_4').val(data.diagnosa_medis_4);
            $('#icdx_kode_4').val(data.icdx_kode_4);
            $('#diagnosa_medis_5').val(data.diagnosa_medis_5);
            $('#icdx_kode_5').val(data.icdx_kode_5);

            // Tindakan (P)
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

    <?php if (session()->get('role') != 'Perawat') : ?>
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
    <?php endif; ?>

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
                $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit`);

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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
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
                    <?php if (session()->get('role') != 'Perawat') : ?>
                        await fetchAsesmen();
                        Promise.all([
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
                        loadVisus();
                    <?php else : ?>
                        fetchAsesmen();
                    <?php endif; ?>
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
                            if (['alergi', 'keadaan_umum'].includes(field)) {
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
        <?php if (session()->get('role') != 'Perawat') : ?>
            await fetchAsesmen();
            Promise.all([
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
            fetchAsesmenMata();
            loadVisus();
        <?php else : ?>
            await fetchAsesmen();
            fetchAsesmenMata()
        <?php endif; ?>
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>