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
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/edukasi/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/edukasi/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap active" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <!-- <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepkacamata/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/lptindakan/' . $rawatjalan['id_rawat_jalan']); ?>">Laporan Tindakan</a> -->
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline justify-content-center flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 text-nowrap <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active' : '' ?>" href="<?= base_url('rawatjalan/edukasi/' . $list['id_rawat_jalan']); ?>"><?= $list['nomor_registrasi']; ?></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <?= form_open_multipart('/rawatjalan/edukasi/update/' . $edukasi['id_edukasi'], 'id="edukasiForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pengkajian Kebutuhan Informasi dan Edukasi</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="bahasa" name="bahasa" aria-label="bahasa">
                            <option value="" disabled selected>-- Pilih Bahasa --</option>
                            <option value="INDONESIA">INDONESIA</option>
                            <option value="INGGRIS">INGGRIS</option>
                            <option value="DAERAH">DAERAH</option>
                            <option value="LAINNYA">LAINNYA</option>
                        </select>
                        <label for="bahasa">Bahasa</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="bahasa_lainnya" name="bahasa_lainnya" value="" autocomplete="off" dir="auto" placeholder="bahasa_lainnya">
                        <label for="bahasa_lainnya">Bahasa Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="penterjemah" class="col col-form-label">
                            Kebutuhan Penerjemah
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penterjemah" id="penterjemah1" value="YA">
                                    <label class="form-check-label" for="penterjemah1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penterjemah" id="penterjemah2" value="TIDAK">
                                    <label class="form-check-label" for="penterjemah2">
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
                        <select class="form-select" id="pendidikan" name="pendidikan" aria-label="pendidikan">
                            <option value="" disabled selected>-- Pilih Pendidikan --</option>
                            <?php foreach ($pendidikan as $list) : ?>
                                <option value="<?= $list['pendidikan'] ?>"><?= $list['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="pendidikan">Pendidikan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="baca_tulis" class="col col-form-label">
                            Baca dan Tulis
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="baca_tulis" id="baca_tulis1" value="BAIK">
                                    <label class="form-check-label" for="baca_tulis1">
                                        Baik
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="baca_tulis" id="baca_tulis2" value="KURANG">
                                    <label class="form-check-label" for="baca_tulis2">
                                        Kurang
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="cara_belajar" class="col col-form-label">
                            Pilihan Cara Belajar
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cara_belajar" id="cara_belajar1" value="VERBAL">
                                    <label class="form-check-label" for="cara_belajar1">
                                        Verbal
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cara_belajar" id="cara_belajar2" value="TULISAN">
                                    <label class="form-check-label" for="cara_belajar2">
                                        Tulisan
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="budaya" name="budaya" value="" autocomplete="off" dir="auto" placeholder="budaya">
                        <label for="budaya">Budaya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="hambatan" class="form-label">
                        Hambatan<br><small class="text-muted">Tekan dan tahan <kbd>Ctrl</kbd> atau <kbd>⌘ Command</kbd> jika tidak bisa memilih lebih dari satu</small>
                    </label>
                    <select class="form-select" id="hambatan" name="hambatan[]" aria-label="hambatan" size="9" multiple>
                        <option value="TIDAK ADA">TIDAK ADA</option>
                        <option value="BAHASA">BAHASA</option>
                        <option value="EMOSIONAL">EMOSIONAL</option>
                        <option value="FISIK LEMAH">FISIK LEMAH</option>
                        <option value="GANGGUAN BICARA">GANGGUAN BICARA</option>
                        <option value="KOGNITIF TERBATAS">KOGNITIF TERBATAS</option>
                        <option value="MOTIVASI KURANG">MOTIVASI KURANG</option>
                        <option value="PENDENGARAN TERGANGGU">PENDENGARAN TERGANGGU</option>
                        <option value="PENGLIHATAN TERGANGGU">PENGLIHATAN TERGANGGU</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="keyakinan" name="keyakinan" aria-label="keyakinan">
                            <option value="" disabled selected>-- Pilih Keyakinan --</option>
                            <option value="SPIRITUAL">SPIRITUAL</option>
                            <option value="ISLAM">ISLAM</option>
                            <option value="KRISTEN">KRISTEN</option>
                            <option value="HINDU">HINDU</option>
                            <option value="BUDDHA">BUDDHA</option>
                            <option value="KONGHUCU">KONGHUCU</option>
                            <option value="KHUSUS">KHUSUS</option>
                        </select>
                        <label for="status_fungsional">Keyakinan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="keyakinan_khusus" name="keyakinan_khusus" value="" autocomplete="off" dir="auto" placeholder="keyakinan_khusus">
                        <label for="keyakinan_khusus">Keyakinan Khusus</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="topik_pembelajaran" name="topik_pembelajaran" aria-label="topik_pembelajaran">
                            <option value="" disabled selected>-- Pilih Keyakinan --</option>
                            <option value="Proses penyakit">Proses penyakit</option>
                            <option value="Rencana tindakan/terapi">Rencana tindakan/terapi</option>
                            <option value="Pengobatan dan prosedur yang diberikan atau diperlukan">Pengobatan dan prosedur yang diberikan atau diperlukan</option>
                            <option value="Hasil pelayanan termasuk terjadinya kejadian yang diharapkan dan tidak diharapkan">Hasil pelayanan termasuk terjadinya kejadian yang diharapkan dan tidak diharapkan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <label for="status_fungsional">Topik Pembelajaran</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="topik_lainnya" name="topik_lainnya" value="" autocomplete="off" dir="auto" placeholder="topik_lainnya">
                        <label for="topik_lainnya">Topik Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="kesediaan_pasien" class="col col-form-label">
                            Kesediaan pasien dan keluarga untuk menerima informasi dan edukasi
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kesediaan_pasien" id="kesediaan_pasien1" value="YA">
                                    <label class="form-check-label" for="kesediaan_pasien1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kesediaan_pasien" id="kesediaan_pasien2" value="TIDAK">
                                    <label class="form-check-label" for="kesediaan_pasien2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/edukasi/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Evaluasi Edukasi</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm bg-gradient mb-2" type="button" id="addEvaluasiButton">
                        <i class="fa-solid fa-plus"></i> Tambah Evaluasi
                    </button>
                </div>
                <center id="empty-placeholder" class="my-3" style="display: none;">
                    <h1><i class="fa-solid fa-user-graduate"></i></h1>
                    <h3>Evaluasi Edukasi Pasien</h3>
                    <div class="text-muted">Klik "Tambah Evaluasi" untuk menambahkan evaluasi edukasi</div>
                </center>
                <ul class="list-group shadow-sm" id="evaluasiEdukasiList">
                    <?php for ($i = 0; $i < 4; $i++) : ?>
                        <li class="list-group-item bg-body-tertiary" style="cursor: wait;">
                            <div class="fw-bold fs-5 placeholder-glow">
                                <span class="placeholder w-100"></span>
                            </div>
                            <div class="date text-nowrap placeholder-glow">
                                <span class="placeholder w-100"></span>
                            </div>
                            <div class="date text-nowrap placeholder-glow">
                                <span class="placeholder w-100"></span>
                            </div>
                            <div class="date text-nowrap text-muted placeholder-glow"><small>
                                    <span class="placeholder w-100"></span>
                                </small></div>
                            <div class="date text-nowrap text-muted placeholder-glow"><small>
                                    <span class="placeholder w-100"></span>
                                </small></div>
                            <div class="placeholder-glow placeholder-glow">
                                <span class="placeholder w-100" style="max-width: 128px;"></span>
                            </div>
                            <div class="btn-group float-end" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em; width: 80px; height: 30.781px;" disabled></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em; width: 80px; height: 30.781px;" disabled></button>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="modal fade" id="evaluasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="evaluasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form id="evaluasiForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="evaluasiModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_edukasi_evaluasi" name="id_edukasi_evaluasi" value="">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" dir="auto" placeholder="unit" id="unit" name="unit">
                        <label for="unit">Unit</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select" id="informasi_edukasi" name="informasi_edukasi" aria-label="informasi_edukasi">
                            <option value="" disabled selected>-- Pilih Informasi Edukasi --</option>
                            <option value="Penyakit yang di derita pasien">Penyakit yang di derita pasien</option>
                            <option value="Rencana tindakan/terapi">Rencana tindakan/terapi</option>
                            <option value="Pengobatan dan prosedur yang diberikan/diperlukan">Pengobatan dan prosedur yang diberikan/diperlukan</option>
                            <option value="Hasil pelayanan, termasuk terjadinya kejadian yang tidak diharapkan">Hasil pelayanan, termasuk terjadinya kejadian yang tidak diharapkan</option>
                        </select>
                        <label for="status_fungsional">Keyakinan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_edukator" id="nama_edukator" name="nama_edukator">
                        <label for="nama_edukator">Nama Edukator</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="profesi_edukator" id="profesi_edukator" name="profesi_edukator">
                        <label for="profesi_edukator">Profesi Edukator</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1">
                        <label for="tanda_tangan_edukator" class="form-label mb-0">Tanda Tangan Edukator (maks 8 MB)</label>
                        <input class="form-control" type="file" id="tanda_tangan_edukator" name="tanda_tangan_edukator" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="tanda_tangan_edukator_preview_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="tanda_tangan_edukator_preview" src="#" alt="Tanda Tangan Edukator" class="img-thumbnail" style="max-width: 100%">
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="nama_pasien_keluarga" id="nama_pasien_keluarga" name="nama_pasien_keluarga">
                        <label for="nama_pasien_keluarga">Nama Pasien/Keluarga/Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1">
                        <label for="tanda_tangan_pasien" class="form-label mb-0">Tanda Tangan Pasien (maks 8 MB)</label>
                        <input class="form-control" type="file" id="tanda_tangan_pasien" name="tanda_tangan_pasien" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="tanda_tangan_pasien_preview_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="tanda_tangan_pasien_preview" src="#" alt="Tanda Tangan Pasien" class="img-thumbnail" style="max-width: 100%">
                        </div>
                    </div>
                    <div class="mb-1 mt-1 radio-group">
                        <label for="evaluasi">
                            Evaluasi
                        </label>
                        <div class="d-flex align-items-center justify-content-evenly">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="evaluasi" id="evaluasi1" value="MENGERTI">
                                <label class="form-check-label" for="evaluasi1">
                                    Mengerti
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="evaluasi" id="evaluasi2" value="RE-EDUKASI">
                                <label class="form-check-label" for="evaluasi2">
                                    Re-edukasi
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="evaluasi" id="evaluasi3" value="RE-DEMONSTRASI">
                                <label class="form-check-label" for="evaluasi3">
                                    Re-demonstrasi
                                </label>
                            </div>
                        </div>
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
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchEdukasi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/edukasi/view/') . $edukasi['id_edukasi'] ?>');
            const data = response.data;

            $('#bahasa').val(data.bahasa);
            $('#bahasa_lainnya').val(data.bahasa_lainnya);
            const penterjemah = data.penterjemah;
            if (penterjemah) {
                $("input[name='penterjemah'][value='" + penterjemah + "']").prop('checked', true);
            }
            $('#pendidikan').val(data.pendidikan);
            const baca_tulis = data.baca_tulis;
            if (baca_tulis) {
                $("input[name='baca_tulis'][value='" + baca_tulis + "']").prop('checked', true);
            }
            const cara_belajar = data.cara_belajar;
            if (cara_belajar) {
                $("input[name='cara_belajar'][value='" + cara_belajar + "']").prop('checked', true);
            }
            $('#budaya').val(data.budaya);
            const hambatan = data.hambatan;
            $('#hambatan option').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (hambatan.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('selected', true);
                }
            });
            $('#keyakinan').val(data.keyakinan);
            $('#keyakinan_khusus').val(data.keyakinan_khusus);
            $('#topik_pembelajaran').val(data.topik_pembelajaran);
            $('#topik_lainnya').val(data.topik_lainnya);
            const kesediaan_pasien = data.kesediaan_pasien;
            if (kesediaan_pasien) {
                $("input[name='kesediaan_pasien'][value='" + kesediaan_pasien + "']").prop('checked', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchEvaluasiEdukasi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/edukasi/evaluasi/list/') . $rawatjalan['id_rawat_jalan'] ?>');

            const data = response.data;
            $('#evaluasiEdukasiList').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                $('#empty-placeholder').show();
                $('#evaluasiEdukasiList').hide();
            } else {
                $('#empty-placeholder').hide();
                data.forEach(function(evaluasi_edukasi) {
                    let evaluasi = evaluasi_edukasi.evaluasi;
                    if (evaluasi === 'MENGERTI') {
                        evaluasi = `<span class="badge text-bg-success bg-gradient">Mengerti</span>`;
                    } else if (evaluasi === 'RE-EDUKASI') {
                        evaluasi = `<span class="badge text-bg-warning bg-gradient">Re-edukasi</span>`;
                    } else if (evaluasi === 'RE-DEMONSTRASI') {
                        evaluasi = `<span class="badge text-bg-warning bg-gradient">Re-demonstrasi</span>`;
                    }
                    const evaluasiEdukasiElement = `
                    <li class="list-group-item bg-body-tertiary">
                        <div class="fw-bold fs-5">${evaluasi_edukasi.waktu_dibuat}</div>
                        <div class="date text-nowrap">${evaluasi_edukasi.unit}</div>
                        <div class="date text-nowrap">${evaluasi_edukasi.informasi_edukasi}</div>
                        <div class="date text-nowrap text-muted"><small>Edukator: ${evaluasi_edukasi.nama_edukator} (${evaluasi_edukasi.profesi_edukator})</small></div>
                        <div class="date text-nowrap text-muted"><small>Pasien/Keluarga/Lainnya: ${evaluasi_edukasi.nama_pasien_keluarga}</small></div>
                        ${evaluasi}
                        <div class="btn-group float-end" role="group">
                            <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${evaluasi_edukasi.id_edukasi_evaluasi}"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                            <button class="btn btn-outline-danger text-nowrap bg-gradient delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${evaluasi_edukasi.id_edukasi_evaluasi}"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>
                    </li>
                    `;
                    $('#evaluasiEdukasiList').show();
                    $('#evaluasiEdukasiList').append(evaluasiEdukasiElement);
                });
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#evaluasiEdukasiList').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }


    $(document).ready(async function() {
        // Tampilkan modal tambah evaluasi edukasi
        $('#addEvaluasiButton').click(function() {
            $('#evaluasiModalLabel').text('Tambah Evaluasi Edukasi'); // Ubah judul modal menjadi 'Tambah Evaluasi Edukasi'
            $('#id_edukasi_evaluasi').val('');
            $('#evaluasiModal').modal('show'); // Tampilkan modal resep luar
        });

        $('#tanda_tangan_edukator').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#tanda_tangan_edukator_preview').attr('src', e.target.result);
                $('#tanda_tangan_edukator_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $('#tanda_tangan_pasien').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#tanda_tangan_pasien_preview').attr('src', e.target.result);
                $('#tanda_tangan_pasien_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $(document).on('click', '.edit-btn', async function() {
            var $this = $(this);
            var id = $this.data('id');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit`);

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/edukasi/evaluasi/view') ?>/` + id);
                let data = response.data;
                console.log(data);

                $('#evaluasiModalLabel').text('Edit Evaluasi Edukasi');
                $('#id_edukasi_evaluasi').val(data.id_edukasi_evaluasi);
                $('#unit').val(data.unit);
                $('#informasi_edukasi').val(data.informasi_edukasi);
                $('#nama_edukator').val(data.nama_edukator);
                $('#profesi_edukator').val(data.profesi_edukator);
                if (data.tanda_tangan_edukator) {
                    $('#tanda_tangan_edukator_preview').attr('src', `<?= base_url('uploads/ttd_edukator_evaluasi') ?>/` + data.tanda_tangan_edukator);
                    $('#tanda_tangan_edukator_preview_div').show();
                } else {
                    $('#tanda_tangan_edukator_preview_div').hide();
                }
                $('#nama_pasien_keluarga').val(data.nama_pasien_keluarga);
                if (data.tanda_tangan_pasien) {
                    $('#tanda_tangan_pasien_preview').attr('src', `<?= base_url('uploads/ttd_pasien_evaluasi') ?>/` + data.tanda_tangan_pasien);
                    $('#tanda_tangan_pasien_preview_div').show();
                } else {
                    $('#tanda_tangan_pasien_preview_div').hide();
                }
                const evaluasi = data.evaluasi;
                if (evaluasi) {
                    $("input[name='evaluasi'][value='" + evaluasi + "']").prop('checked', true);
                }
                $('#evaluasiModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i> Edit`);
            }
        });

        // Store the ID of the example to be deleted
        var id_edukasi_evaluasi;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            id_edukasi_evaluasi = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus evaluasi edukasi ini?`);
            $('#deleteModal').modal('show');
        });

        // Confirm deletion
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html(`Menghapus, silakan tunggu...`);

            try {
                // Perform the delete operation
                let response = await axios.delete('<?= base_url('/rawatjalan/edukasi/evaluasi/delete') ?>/' + id_edukasi_evaluasi);

                // Show success message
                showSuccessToast(response.data.message);

                // Reload the table
                fetchEvaluasiEdukasi();
            } catch (error) {
                // Handle any error responses
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                // Re-enable the delete button and hide the modal
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#evaluasiForm').submit(async function(ə) {
            ə.preventDefault();

            var url = $('#id_edukasi_evaluasi').val() ? `<?= base_url('rawatjalan/edukasi/evaluasi/update') ?>` : `<?= base_url('/rawatjalan/edukasi/evaluasi/create/' . $rawatjalan['id_rawat_jalan']) ?>`;
            var formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", formData);

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Clear previous validation states
            $('#evaluasiForm .is-invalid').removeClass('is-invalid');
            $('#evaluasiForm .invalid-feedback').text('').hide();

            // Show processing button and progress bar
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#cancelButton').prop('disabled', false).show();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

            // Disable form inputs
            $('#evaluasiForm input').prop('disabled', true);

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
                    $('#evaluasiModal').modal('hide');
                    $('#uploadProgressBar').css('width', '0%');
                    fetchEvaluasiEdukasi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#evaluasiForm .is-invalid').removeClass('is-invalid');
                    $('#evaluasiForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            if (['evaluasi'].includes(field)) {
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
                $('#evaluasiForm input').prop('disabled', false);
            }

            // Attach the cancel functionality to the close button
            $('#closeBtn, #cancelButton').on('click', function() {
                source.cancel('Penambahan evaluasi edukasi telah dibatalkan.');
            });
        });

        // Reset form saat modal ditutup
        $('#evaluasiModal').on('hidden.bs.modal', function() {
            $('#evaluasiForm')[0].reset();
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#tanda_tangan_edukator_preview').attr('src', '#');
            $('#tanda_tangan_edukator_preview_div').hide();
            $('#tanda_tangan_pasien_preview').attr('src', '#');
            $('#tanda_tangan_pasien_preview_div').hide();
            $('#evaluasiForm .is-invalid').removeClass('is-invalid');
            $('#evaluasiForm .invalid-feedback').text('').hide();
        });

        $('#edukasiForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#edukasiForm .is-invalid').removeClass('is-invalid');
            $('#edukasiForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#edukasiForm input, #edukasiForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/edukasi/update/' . $edukasi['id_edukasi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchEdukasi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#edukasiForm .is-invalid').removeClass('is-invalid');
                    $('#edukasiForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['penterjemah', 'baca_tulis', 'cara_belajar', 'kesediaan_pasien'].includes(field)) {
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
                $('#edukasiForm input, #edukasiForm select').prop('disabled', false);
            }
        });
        await fetchEdukasi();
        fetchEvaluasiEdukasi();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>