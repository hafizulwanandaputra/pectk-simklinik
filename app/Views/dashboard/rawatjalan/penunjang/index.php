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
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/penunjang/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/penunjang/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top px-3 pt-2" style="z-index: 99;">
        <ul class="list-group no-fluid-content shadow-sm border border-bottom-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-pills nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                            <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
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
                    <nav class="nav nav-pills flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink ' . ((date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'bg-danger text-white' : '') : '' ?>" href="<?= base_url('rawatjalan/penunjang/' . $list['id_rawat_jalan']); ?>">
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
            <?= form_open_multipart('/rawatjalan/penunjang/update/' . $penunjang['id_penunjang'], 'id="penunjangForm"'); ?>
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
                <div class="fw-bold mb-2 border-bottom">Pemeriksaan Penunjang</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="dokter_pengirim" name="dokter_pengirim" aria-label="dokter_pengirim">
                            <option value="" disabled selected>-- Pilih Dokter --</option>
                        </select>
                        <label for="dokter_pengirim">Dokter Pengirim</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="rujukan_dari" name="rujukan_dari" aria-label="rujukan_dari">
                            <option value="" disabled selected>-- Pilih Asal Rujukan --</option>
                        </select>
                        <label for="rujukan_dari">Rujukan dari</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 checkbox-group">
                    <label for="pemeriksaan" class="form-label">
                        Pemeriksaan<br><small class="text-muted">Abaikan jika pemeriksaan lainnya diisi</small>
                    </label>
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_autoref" name="pemeriksaan[]" value="AUTOREF">
                                <label class="form-check-label" for="pemeriksaan_autoref">AUTOREF</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_tono" name="pemeriksaan[]" value="TONO">
                                <label class="form-check-label" for="pemeriksaan_tono">TONO</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_oct" name="pemeriksaan[]" value="OCT">
                                <label class="form-check-label" for="pemeriksaan_oct">OCT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_foto_fundus" name="pemeriksaan[]" value="FOTO FUNDUS">
                                <label class="form-check-label" for="pemeriksaan_foto_fundus">FOTO FUNDUS</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_usg" name="pemeriksaan[]" value="USG">
                                <label class="form-check-label" for="pemeriksaan_usg">USG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_yag_laser" name="pemeriksaan[]" value="YAG LASER">
                                <label class="form-check-label" for="pemeriksaan_yag_laser">YAG LASER</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_perimetri" name="pemeriksaan[]" value="PERIMETRI">
                                <label class="form-check-label" for="pemeriksaan_perimetri">PERIMETRI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_biometri" name="pemeriksaan[]" value="BIOMETRI">
                                <label class="form-check-label" for="pemeriksaan_biometri">BIOMETRI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_labor" name="pemeriksaan[]" value="LABOR">
                                <label class="form-check-label" for="pemeriksaan_labor">LABOR</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_keratometri" name="pemeriksaan[]" value="KERATOMETRI">
                                <label class="form-check-label" for="pemeriksaan_keratometri">KERATOMETRI</label>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-sm">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_ekg" name="pemeriksaan[]" value="EKG">
                                <label class="form-check-label" for="pemeriksaan_ekg">EKG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_ct_scan" name="pemeriksaan[]" value="CT SCAN">
                                <label class="form-check-label" for="pemeriksaan_ct_scan">CT SCAN</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_ffa" name="pemeriksaan[]" value="FFA">
                                <label class="form-check-label" for="pemeriksaan_ffa">FFA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_anterior_mata" name="pemeriksaan[]" value="ANTERIOR MATA">
                                <label class="form-check-label" for="pemeriksaan_anterior_mata">ANTERIOR MATA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_palpebra" name="pemeriksaan[]" value="PALPEBRA">
                                <label class="form-check-label" for="pemeriksaan_palpebra">PALPEBRA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_konjungtiva" name="pemeriksaan[]" value="KONJUNGTIVA">
                                <label class="form-check-label" for="pemeriksaan_konjungtiva">KONJUNGTIVA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_keterangan" name="pemeriksaan[]" value="KETERANGAN">
                                <label class="form-check-label" for="pemeriksaan_keterangan">KETERANGAN</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_surat_keterangan" name="pemeriksaan[]" value="SURAT KETERANGAN">
                                <label class="form-check-label" for="pemeriksaan_surat_keterangan">SURAT KETERANGAN</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_surat_rujukan" name="pemeriksaan[]" value="SURAT RUJUKAN">
                                <label class="form-check-label" for="pemeriksaan_surat_rujukan">SURAT RUJUKAN</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="pemeriksaan_ukuran_kacamata" name="pemeriksaan[]" value="UKURAN KACAMATA">
                                <label class="form-check-label" for="pemeriksaan_ukuran_kacamata">UKURAN KACAMATA</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="pemeriksaan_lainnya" name="pemeriksaan_lainnya" value="" autocomplete="off" dir="auto" placeholder="pemeriksaan_lainnya">
                        <label for="pemeriksaan_lainnya">Pemeriksaan Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="lokasi_pemeriksaan" name="lokasi_pemeriksaan" aria-label="lokasi_pemeriksaan">
                            <option value="" disabled selected>-- Pilih Lokasi Pemeriksaan --</option>
                            <option value="OD">OD</option>
                            <option value="OS">OS</option>
                            <option value="ODS">ODS</option>
                        </select>
                        <label for="lokasi_pemeriksaan">Lokasi Pemeriksaan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/penunjang/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemindaian Pemeriksaan Penunjang</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm bg-gradient mb-2" type="button" id="addScanButton" disabled>
                        <i class="fa-solid fa-plus"></i> Tambah Pemindaian
                    </button>
                </div>
                <div id="empty-placeholder" class="my-3" style="display: none;">
                    <img src="<?= base_url('assets/images/stethoscope-svgrepo-com.svg') ?>" style="height: 7rem;" class="mb-2">
                    <h3>Pemindaian Pemeriksaan Penunjang</h3>
                    <div class="text-muted">Klik "Tambah Pemindaian" untuk menambahkan pemindaian pemeriksaan penunjang</div>
                </div>
                <div id="scanPenunjangList" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <?php for ($i = 0; $i < 6; $i++) : ?>
                        <div class="col">
                            <div class="card shadow-sm h-100" style="cursor: wait;">
                                <div class="card-img-top" style="background-color: var(--bs-card-cap-bg); aspect-ratio: 16/9; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);"></div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="align-self-center w-100 placeholder-glow">
                                            <span class="placeholder" style="width: 100%;"></span><br>
                                        </div>
                                    </div>
                                    <div class="w-100 placeholder-glow">
                                        <small><span class="placeholder" style="width: 100%;"></span></small><br>
                                        <small><small><span class="placeholder" style="width: 100%;"></span></small></small>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end gap-1">
                                    <a class="btn btn-body btn-sm bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                    <a class="btn btn-danger bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
            <form id="scanForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="scanModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_penunjang_scan" name="id_penunjang_scan" value="">
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select" id="pemeriksaan_scan" name="pemeriksaan_scan" aria-label="pemeriksaan_scan">
                            <option value="" disabled selected>-- Pilih Pemeriksaan --</option>
                        </select>
                        <label for="status_fungsional">Pemeriksaan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1">
                        <label for="gambar" class="form-label mb-0">Unggah Gambar (maks 8 MB)</label>
                        <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="gambar_preview_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="gambar_preview" src="#" alt="Gambar" class="img-thumbnail" style="width: 100%">
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="keterangan" id="keterangan" name="keterangan">
                        <label for="keterangan">Keterangan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="w-100" for="keepModalOpen">
                            Biarkan modal ini tetap terbuka setelah menyimpan
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="" id="keepModalOpen" switch>
                        </div>
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
    <div class="modal fade" id="scanPreviewModal" tabindex="-1" aria-labelledby="scanPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable">
            <form id="scanPreviewForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="scanPreviewModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div id="gambar_preview_2_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="gambar_preview_2" src="#" alt="Gambar" class="img-thumbnail" style="width: 100%">
                        </div>
                    </div>
                    <div class="fw-bold" id="pemeriksaan_scan_preview"></div>
                    <div id="keterangan_preview"></div>
                    <div><small id="waktu_dibuat_preview" class="text-muted"></small></div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchPenunjang() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/penunjang/view/') . $penunjang['id_penunjang'] ?>');
            const data = response.data;

            $('#dokter_pengirim').val(data.dokter_pengirim);
            $('#rujukan_dari').val(data.rujukan_dari);
            const pemeriksaan = data.pemeriksaan;
            $('input[name="pemeriksaan[]"]').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (pemeriksaan.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('checked', true);
                }
            });
            $('#pemeriksaan_lainnya').val(data.pemeriksaan_lainnya);
            $('#lokasi_pemeriksaan').val(data.lokasi_pemeriksaan);

            // Cek validitas data
            const isValidPemeriksaan = Array.isArray(pemeriksaan) && pemeriksaan.some(item => item && item.trim() !== "");
            const isValidPemeriksaanLainnya = data.pemeriksaan_lainnya !== null && data.pemeriksaan_lainnya.trim() !== "";
            const isValidLokasiPemeriksaan = data.lokasi_pemeriksaan !== null && data.lokasi_pemeriksaan.trim() !== "";

            // Jika salah satu memiliki nilai, tampilkan #scanPenunjangContainer
            if (isValidPemeriksaan || isValidPemeriksaanLainnya || isValidLokasiPemeriksaan) {
                $('#addScanButton').prop('disabled', false);
            } else {
                $('#addScanButton').prop('disabled', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDokterOptions() {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/penunjang/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#dokter_pengirim').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#dokter_pengirim').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchRuanganOptions() {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/penunjang/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#rujukan_dari').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#rujukan_dari').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchPemeriksaan() {
        $('#loadingSpinner').show();
        try {
            const response = await axios.get('<?= base_url('rawatjalan/penunjang/view/') . $penunjang['id_penunjang'] ?>');
            const data = response.data;

            const pemeriksaan = data.pemeriksaan; // Array string dari JSON
            const pemeriksaanLainnya = data.pemeriksaan_lainnya; // String dari JSON
            const $select = $('#pemeriksaan_scan');

            // Hapus opsi yang ada, kecuali opsi pertama (default)
            $select.find('option:not(:first)').remove();

            // Tambahkan opsi baru dari array pemeriksaan
            pemeriksaan.forEach(item => {
                if (item.trim() !== "") { // Pastikan tidak menambahkan string kosong
                    $select.append(`<option value="${item}">${item}</option>`);
                }
            });

            // Tambahkan pemeriksaan_lainnya jika tidak null atau kosong
            if (pemeriksaanLainnya && pemeriksaanLainnya.trim() !== "") {
                $select.append(`<option value="${pemeriksaanLainnya}">Lainnya: ${pemeriksaanLainnya}</option>`);
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`Terjadi kesalahan: ${error}`);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchScanPenunjang() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/penunjang/scan/list/') . $rawatjalan['id_rawat_jalan'] ?>');

            const data = response.data;
            $('#scanPenunjangList').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                $('#empty-placeholder').show();
                $('#scanPenunjangList').hide();
            } else {
                $('#empty-placeholder').hide();
                data.forEach(function(penunjang_scan) {
                    const keterangan = penunjang_scan.keterangan ? penunjang_scan.keterangan : `<em>Tidak ada keterangan</em>`;
                    const penunjangScanElement = `
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-img-top gambar-scan" role="button" style="background-image: url('<?= base_url('uploads/scan_penunjang') ?>/${penunjang_scan.gambar}?t=${new Date().getTime()}'); background-color: var(--bs-card-cap-bg); aspect-ratio: 16/9; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);" data-id="${penunjang_scan.id_penunjang_scan}"></div>
                        <div class="card-body">
                            <div class="d-inline-flex">
                                <div class="align-self-center">
                                    <span class="card-text fw-bold">${penunjang_scan.pemeriksaan_scan}</span>
                                </div>
                            </div>
                            <div>
                                <small>${keterangan}</small><br>
                                <small class="text-muted date"><small>${penunjang_scan.waktu_dibuat}</small></small>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-1">
                            <button class="btn btn-body btn-sm bg-gradient edit-btn" data-id="${penunjang_scan.id_penunjang_scan}"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                            <button class="btn btn-danger btn-sm bg-gradient delete-btn" data-id="${penunjang_scan.id_penunjang_scan}"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>                               
                    </div>
                </div>
                    `;
                    $('#scanPenunjangList').show();
                    $('#scanPenunjangList').append(penunjangScanElement);
                });
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#scanPenunjangList').empty();
        } finally {
            // Hide the spinner when done
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

        // Saat halaman dimuat, set checkbox sesuai localStorage
        if (localStorage.getItem("keepModalOpen") === "true") {
            $("#keepModalOpen").prop("checked", true);
        }

        // Toggle simpan ke localStorage
        $("#keepModalOpen").on("change", function() {
            if ($(this).is(":checked")) {
                localStorage.setItem("keepModalOpen", "true");
            } else {
                localStorage.setItem("keepModalOpen", "false");
            }
        });

        // Tampilkan modal tambah scan penunjang
        $('#addScanButton').click(async function() {
            $('#scanModalLabel').text('Tambah Pemindaian Pemeriksaan Penunjang'); // Ubah judul modal menjadi 'Tambah Pemindaian Pemeriksaan Penunjang'
            $('#id_penunjang_scan').val('');
            await fetchPemeriksaan();
            $('#scanModal').modal('show'); // Tampilkan modal resep luar
        });

        $('#gambar').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#gambar_preview').attr('src', e.target.result);
                $('#gambar_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $(document).on('click', '.gambar-scan', async function(ə) {
            ə.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $('#loadingSpinner').show();

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/penunjang/scan/view') ?>/` + id);
                let data = response.data;
                console.log(data);

                $('#scanPreviewModalLabel').text('Pratinjau Gambar Pemeriksaan Penunjang');
                if (data.gambar) {
                    $('#gambar_preview_2').attr('src', `<?= base_url('uploads/scan_penunjang') ?>/` + data.gambar);
                    $('#gambar_preview_2_div').show();
                } else {
                    $('#gambar_preview_2_div').hide();
                }
                const keterangan = data.keterangan ? data.keterangan : `<em>Tidak ada keterangan</em>`;
                $('#pemeriksaan_scan_preview').text(data.pemeriksaan_scan);
                $('#keterangan_preview').html(keterangan);
                $('#waktu_dibuat_preview').text(data.waktu_dibuat);
                $('#scanPreviewModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#loadingSpinner').hide();
            }
        });

        $(document).on('click', '.edit-btn', async function() {
            var $this = $(this);
            var id = $this.data('id');
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Edit`);

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/penunjang/scan/view') ?>/` + id);
                let data = response.data;
                console.log(data);
                await fetchPemeriksaan();

                $('#scanModalLabel').text('Edit Pemeriksaan Penunjang');
                $('#id_penunjang_scan').val(data.id_penunjang_scan);
                $('#pemeriksaan_scan').val(data.pemeriksaan_scan);
                if (data.gambar) {
                    $('#gambar_preview').attr('src', `<?= base_url('uploads/scan_penunjang') ?>/` + data.gambar);
                    $('#gambar_preview_div').show();
                } else {
                    $('#gambar_preview_div').hide();
                }
                $('#keterangan').val(data.keterangan);
                $('#scanModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i> Edit`);
            }
        });

        // Store the ID of the example to be deleted
        var id_penunjang_scan;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            id_penunjang_scan = $(this).data('id');
            $('#deleteMessage').html(`Hapus pemindaian pemeriksaan penunjang ini?`);
            $('#deleteModal').modal('show');
        });

        // Confirm deletion
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                // Perform the delete operation
                let response = await axios.delete('<?= base_url('/rawatjalan/penunjang/scan/delete') ?>/' + id_penunjang_scan);

                // Show success message
                showSuccessToast(response.data.message);

                // Reload the table
                fetchScanPenunjang();
            } catch (error) {
                // Handle any error responses
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                // Re-enable the delete button and hide the modal
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        $('#scanForm').submit(async function(ə) {
            ə.preventDefault();

            var url = $('#id_penunjang_scan').val() ? `<?= base_url('rawatjalan/penunjang/scan/update') ?>` : `<?= base_url('/rawatjalan/penunjang/scan/create/' . $rawatjalan['id_rawat_jalan']) ?>`;
            var formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", formData);

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Clear previous validation states
            $('#scanForm .is-invalid').removeClass('is-invalid');
            $('#scanForm .invalid-feedback').text('').hide();

            // Show processing button and progress bar
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#cancelButton').prop('disabled', false).show();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

            // Disable form inputs
            $('#scanForm input').prop('disabled', true);

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
                    if ($('#keepModalOpen').is(':checked')) {
                        // simpan status checkbox sebelum reset
                        let keepChecked = $('#keepModalOpen').is(':checked');

                        // reset form
                        $('#scanForm')[0].reset();

                        // balikin status checkbox
                        $('#keepModalOpen').prop('checked', keepChecked);
                        $('#gambar_preview').attr('src', '#');
                        $('#gambar_preview_div').hide();
                    } else {
                        $('#scanModal').modal('hide');
                    }
                    $('#uploadProgressBar').css('width', '0%');
                    fetchScanPenunjang();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#scanForm .is-invalid').removeClass('is-invalid');
                    $('#scanForm .invalid-feedback').text('').hide();

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
                $('#scanForm input').prop('disabled', false);
            }

            // Attach the cancel functionality to the close button
            $('#closeBtn, #cancelButton').on('click', function() {
                source.cancel('Penambahan pemindaian pemeriksaan penunjang telah dibatalkan.');
            });
        });

        $('#scanPreviewModal').on('hidden.bs.modal', function() {
            $('#gambar_preview_2').attr('src', '#');
            $('#gambar_preview_2_div').hide();
            $('#pemeriksaan_scan_preview').text('');
            $('#keterangan_preview').html('');
            $('#waktu_dibuat_preview').text('');
        });

        // Reset form saat modal ditutup
        $('#scanModal').on('hidden.bs.modal', function() {
            // simpan status checkbox sebelum reset
            let keepChecked = $('#keepModalOpen').is(':checked');

            // reset form
            $('#scanForm')[0].reset();

            // balikin status checkbox
            $('#keepModalOpen').prop('checked', keepChecked);
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#gambar_preview').attr('src', '#');
            $('#gambar_preview_div').hide();
            $('#scanForm .is-invalid').removeClass('is-invalid');
            $('#scanForm .invalid-feedback').text('').hide();
        });

        $('#penunjangForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#penunjangForm .is-invalid').removeClass('is-invalid');
            $('#penunjangForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#penunjangForm input, #penunjangForm select, #penunjangForm button').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/penunjang/update/' . $penunjang['id_penunjang']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchPenunjang();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#penunjangForm .is-invalid').removeClass('is-invalid');
                    $('#penunjangForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            if (['pemeriksaan'].includes(field)) {
                                // Handle checkbox group
                                const checkboxGroup = $(`input[name='${field}[]']`); // Ambil grup checkbox berdasarkan nama
                                const feedbackElement = checkboxGroup.closest('.checkbox-group').find('.invalid-feedback'); // Gunakan pembungkus dengan class tertentu

                                if (checkboxGroup.length > 0 && feedbackElement.length > 0) {
                                    checkboxGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user checks any checkbox in the group
                                    checkboxGroup.on('change', function() {
                                        checkboxGroup.removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Checkbox group tidak ditemukan untuk field:", field);
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
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#penunjangForm input, #penunjangForm select, #penunjangForm button').prop('disabled', false);
            }
        });
        await Promise.all([fetchDokterOptions(), fetchRuanganOptions()]);
        await fetchPenunjang();
        fetchScanPenunjang();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>