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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/pasien'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $pasien['no_rm'] ?> • <span id="nama_pasien_header"><?= $pasien['nama_pasien']; ?></span> • <span id="totalRecords">0</span> rawat jalan</div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $previous['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['no_rm'] ?> • <?= $previous['nama_pasien'] == NULL; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $next['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['no_rm'] ?> • <?= $next['nama_pasien'] == NULL; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                    <nav>
                        <div class="nav nav-underline nav-justified" id="nav-tab" role="tablist">
                            <button class="nav-link  active" id="pasien-container-tab" data-bs-toggle="tab" data-bs-target="#pasien-container" type="button" role="tab" aria-controls="pasien-container" aria-selected="true">Identitas Pasien</button>
                            <button class="nav-link " id="rawatjalan-container-tab" data-bs-toggle="tab" data-bs-target="#rawatjalan-container" type="button" role="tab" aria-controls="rawatjalan-container" aria-selected="false">Rawat Jalan</button>
                        </div>
                    </nav>
                    <div class="mt-2" id="tanggal_form" style="display: none;">
                        <div class="input-group input-group-sm">
                            <input type="date" id="tanggal" name="tanggal" class="form-control ">
                            <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                            <button class="btn btn-success bg-gradient " type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                        </div>
                        <div class="accordion mt-2" id="accordionFilter">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                        Pencarian Tambahan
                                    </button>
                                </h2>
                                <div id="collapseFilter" class="accordion-collapse collapse" data-bs-parent="#accordionFilter">
                                    <div class="accordion-body px-2 py-1 mt-1">
                                        <div class="row row-cols-1 row-cols-sm-2 g-1">
                                            <div class="col">
                                                <select id="kunjunganFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Jenis Kunjungan</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="jaminanFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Jaminan</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="ruanganFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Ruangan</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="dokterFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Dokter</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="pendaftarFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Pendaftar</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="statusFilter" class="form-select form-select-sm">
                                                    <option value="">Semua Status</option>
                                                </select>
                                            </div>
                                        </div>
                                        <select id="transaksiFilter" class="form-select form-select-sm my-1">
                                            <option value="">Semua Status Transaksi</option>
                                            <option value="1">Diproses</option>
                                            <option value="0">Belum Diproses</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane show active" id="pasien-container" role="tabpanel" aria-labelledby="pasien-container-tab" tabindex="0">
                    <?= form_open_multipart('/pasien/update/' . $pasien['id_pasien'], 'id="pasienForm"'); ?>
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <div class="mb-2 py-1 px-2 bg-body-tertiary border rounded">
                            <div class="mb-0 row g-1 d-flex align-items-end">
                                <div class="col fw-medium text-nowrap">Didaftarkan</div>
                                <div class="col text-end">
                                    <div class="date text-nowrap">
                                        <?= $pasien['tanggal_daftar'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="" autocomplete="off" dir="auto" placeholder="nama_pasien">
                                <label for="nama_pasien">Nama</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="nik" name="nik" value="" autocomplete="off" dir="auto" placeholder="nik">
                                    <label for="nik">Nomor Induk Kependudukan (Opsional)</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="no_bpjs" name="no_bpjs" value="" autocomplete="off" dir="auto" placeholder="no_bpjs">
                                    <label for="no_bpjs">Nomor BPJS (Opsional)</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="" autocomplete="off" dir="auto" placeholder="tempat_lahir">
                                    <label for="tempat_lahir">Tempat Lahir</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="" autocomplete="off" dir="auto" placeholder="tanggal_lahir">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="col col-form-label">
                                <div class="d-flex align-items-center justify-content-evenly">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin1" value="L">
                                        <label class="form-check-label" for="jenis_kelamin1">
                                            Laki-Laki
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin2" value="P">
                                        <label class="form-check-label" for="jenis_kelamin2">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback text-center"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="alamat" name="alamat" value="" autocomplete="off" dir="auto" placeholder="alamat">
                                <label for="alamat">Alamat</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="provinsi" name="provinsi" aria-label="provinsi">
                                        <option value="" selected>-- Pilih Provinsi --</option>
                                    </select>
                                    <label for="provinsi">Provinsi</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kabupaten" name="kabupaten" aria-label="kabupaten">
                                        <option value="" selected>-- Pilih Kabupaten/Kota --</option>
                                    </select>
                                    <label for="kabupaten">Kabupaten/Kota</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kecamatan" name="kecamatan" aria-label="kecamatan">
                                        <option value="" selected>-- Pilih Kecamatan --</option>
                                    </select>
                                    <label for="kecamatan">Kecamatan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kelurahan" name="kelurahan" aria-label="kelurahan">
                                        <option value="" selected>-- Pilih Desa/Kelurahan --</option>
                                    </select>
                                    <label for="kelurahan">Desa/Kelurahan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="rt" name="rt" value="" autocomplete="off" dir="auto" placeholder="rt">
                                    <label for="rt">RT (Opsional)</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="rw" name="rw" value="" autocomplete="off" dir="auto" placeholder="rw">
                                    <label for="rw">RW (Opsional)</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="telpon" name="telpon" value="" autocomplete="off" dir="auto" placeholder="telpon">
                                    <label for="telpon">Nomor HP (Opsional)</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kewarganegaraan" name="kewarganegaraan" aria-label="kewarganegaraan">
                                        <option value="" selected>-- Pilih Kewarganegaraan --</option>
                                        <option value="INDONESIA">INDONESIA</option>
                                        <option value="WARGA NEGARA ASING">WARGA NEGARA ASING</option>
                                    </select>
                                    <label for="kewarganegaraan">Kewarganegaraan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="agama" name="agama" aria-label="agama">
                                        <option value="" selected>-- Pilih Agama --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Kristen Protestan">Kristen Protestan</option>
                                        <option value="Kristen Katolik">Kristen Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                    <label for="agama">Agama</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="status_nikah" name="status_nikah" aria-label="status_nikah">
                                        <option value="" selected>-- Pilih Status Perkawinan --</option>
                                        <option value="BELUM MENIKAH">BELUM MENIKAH</option>
                                        <option value="MENIKAH">MENIKAH</option>
                                        <option value="JANDA">JANDA</option>
                                        <option value="DUDA">DUDA</option>
                                        <option value="CERAI">CERAI</option>
                                    </select>
                                    <label for="status_nikah">Status Perkawinan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <select class="form-select" id="pekerjaan" name="pekerjaan" aria-label="pekerjaan">
                                    <option value="" selected>-- Pilih Pekerjaan --</option>
                                    <option value="TIDAK BEKERJA">
                                        TIDAK BEKERJA</option>
                                    <option value="I R T">
                                        I R T</option>
                                    <option value="BURUH">
                                        BURUH</option>
                                    <option value="PELAJAR">
                                        PELAJAR</option>
                                    <option value="MAHASISWA">
                                        MAHASISWA</option>
                                    <option value="WIRASWASTA">
                                        WIRASWASTA</option>
                                    <option value="P N S">
                                        P N S</option>
                                    <option value="PEDAGANG">
                                        PEDAGANG</option>
                                    <option value="KARYAWAN/TI">
                                        KARYAWAN/TI</option>
                                    <option value="SWASTA">
                                        SWASTA</option>
                                    <option value="KARYAWAN RS">
                                        KARYAWAN RS</option>
                                    <option value="PETANI">
                                        PETANI</option>
                                    <option value="PERAWAT">
                                        PERAWAT</option>
                                    <option value="BIDAN">
                                        BIDAN</option>
                                    <option value="DOKTER">
                                        DOKTER</option>
                                    <option value="TUKANG">
                                        TUKANG</option>
                                    <option value="SOPIR">
                                        SOPIR</option>
                                    <option value="DOSEN">
                                        DOSEN</option>
                                    <option value="GURU">
                                        GURU</option>
                                    <option value="BUMN">
                                        BUMN</option>
                                    <option value="PENSIUNAN">
                                        PENSIUNAN</option>
                                    <option value="ABRI">
                                        ABRI</option>
                                    <option value="POLRI">
                                        POLRI</option>
                                </select>
                                <label for="pekerjaan">Pekerjaan</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <hr>
                        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                            <button class="btn btn-body  bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/pasien/kiup/' . $pasien['id_pasien']) ?>`)"><i class="fa-solid fa-print"></i> Cetak KIUP</button>
                            <button class="btn btn-body  bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/pasien/barcode/' . $pasien['id_pasien']) ?>`)"><i class="fa-solid fa-print"></i> Cetak <em>Barcode</em></button>
                            <button class="btn btn-primary  bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        </div>
                    </div>
                    <?= form_close(); ?>
                </div>
                <div class="tab-pane show active" id="rawatjalan-container" role="tabpanel" aria-labelledby="rawatjalan-container-tab" tabindex="0">
                    <div class="shadow-sm rounded">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm bg-gradient  rounded-bottom-0" type="button" id="addRajalButton">
                                <i class="fa-solid fa-plus"></i> Registrasi Rawat Jalan
                            </button>
                        </div>
                        <ul id="rajalContainer" class="list-group rounded-top-0 ">
                            <?php for ($i = 0; $i < 12; $i++) : ?>
                                <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                                    <div class="d-flex">
                                        <div class="align-self-center w-100">
                                            <h5 class="card-title d-flex justify-content-start placeholder-glow">
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><span class="spinner-border" style="width: 0.9em; height: 0.9em;" aria-hidden="true"></span></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><span class="spinner-border" style="width: 0.9em; height: 0.9em;" aria-hidden="true"></span></span>
                                            </h5>
                                            <h6 class="card-subtitle placeholder-glow">
                                                <span class="placeholder" style="width: 100%;"></span>
                                            </h6>
                                            <div class="card-text placeholder-glow">
                                                <div style="font-size: 0.75em;">
                                                    <div class="row gx-3">
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="placeholder w-100" style="max-width: 100px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-grid gap-2 d-flex justify-content-end">
                                        <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                        <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                        <a class="btn btn-danger bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                    </div>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                    <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteMessage"></h5>
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
    let limit = 12;
    let currentPage = 1;
    let rajalId = null;
    var placeholder = `
                                <li class="list-group-item border-top-0 pb-3 pt-3" style="cursor: wait;">
                                    <div class="d-flex">
                                        <div class="align-self-center w-100">
                                            <h5 class="card-title d-flex justify-content-start placeholder-glow">
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><span class="spinner-border" style="width: 0.9em; height: 0.9em;" aria-hidden="true"></span></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><span class="spinner-border" style="width: 0.9em; height: 0.9em;" aria-hidden="true"></span></span>
                                            </h5>
                                            <h6 class="card-subtitle placeholder-glow">
                                                <span class="placeholder" style="width: 100%;"></span>
                                            </h6>
                                            <div class="card-text placeholder-glow">
                                                <div style="font-size: 0.75em;">
                                                    <div class="row gx-3">
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="placeholder w-100" style="max-width: 100px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-grid gap-2 d-flex justify-content-end">
                                        <a class="btn btn-body bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                        <a class="btn btn-danger bg-gradient  disabled placeholder" aria-disabled="true" style="width: 75px; height: 31px;"></a>
                                    </div>
                                </li>
    `;

    async function fetchPasien() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pasien/pasien/') . $pasien['id_pasien'] ?>');

            const data = response.data;

            $('title').text(`Detail Pasien ${data.nama_pasien} (${data.no_rm}) - <?= $systemname ?>`);
            $('#nama_pasien_header').text(data.nama_pasien);
            $('#nama_pasien').val(data.nama_pasien);
            $('#nik').val(data.nik);
            $('#no_bpjs').val(data.no_bpjs);
            $('#tempat_lahir').val(data.tempat_lahir);
            $('#tanggal_lahir').val(data.tanggal_lahir);
            const selectedGender = response.data.jenis_kelamin;
            if (selectedGender) {
                $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
            }
            $('#alamat').val(data.alamat);
            $('#rt').val(data.rt);
            $('#rw').val(data.rw);
            $('#telpon').val(data.telpon);
            $('#kewarganegaraan').val(data.kewarganegaraan);
            $('#agama').val(data.agama);
            $('#status_nikah').val(data.status_nikah);
            $('#pekerjaan').val(data.pekerjaan);
            await loadProvinsi(data.provinsi); // Muat dan pilih provinsi
            if (data.kabupaten) {
                await loadKabupaten(data.provinsi, data.kabupaten); // Muat dan pilih kabupaten
                if (data.kecamatan) {
                    await loadKecamatan(data.kabupaten, data.kecamatan); // Muat dan pilih kecamatan
                    if (data.kelurahan) {
                        await loadKelurahan(data.kecamatan, data.kelurahan); // Muat dan pilih kelurahan
                    }
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    // Fungsi untuk memuat data provinsi
    async function loadProvinsi(selectedProvinsi = null) {
        try {
            const response = await axios.get('<?= base_url('pasien/provinsi') ?>'); // Ganti dengan URL API yang sesuai
            const provinsi = response.data.data; // Sesuaikan dengan struktur data API
            $('#provinsi').append(
                provinsi.map(p => `<option value="${p.id}">${p.name}</option>`)
            );
            if (selectedProvinsi) {
                $('#provinsi').val(selectedProvinsi).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data provinsi:', error);
        }
    }

    // Fungsi untuk memuat data kabupaten berdasarkan provinsi
    async function loadKabupaten(provinsiId, selectedKabupaten = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kabupaten') ?>/${provinsiId}`);
            const kabupaten = response.data.data;
            $('#kabupaten')
                .empty()
                .append('<option value="">-- Pilih Kabupaten/Kota --</option>')
                .append(
                    kabupaten.map(k => `<option value="${k.id}">${k.name}</option>`)
                );
            if (selectedKabupaten) {
                $('#kabupaten').val(selectedKabupaten).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data kabupaten:', error);
        }
    }

    // Fungsi untuk memuat data kecamatan berdasarkan kabupaten
    async function loadKecamatan(kabupatenId, selectedKecamatan = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kecamatan') ?>/${kabupatenId}`);
            const kecamatan = response.data.data;
            $('#kecamatan')
                .empty()
                .append('<option value="">-- Pilih Kecamatan --</option>')
                .append(
                    kecamatan.map(k => `<option value="${k.id}">${k.name}</option>`)
                );
            if (selectedKecamatan) {
                $('#kecamatan').val(selectedKecamatan).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data kecamatan:', error);
        }
    }

    // Fungsi untuk memuat data kelurahan berdasarkan kecamatan
    async function loadKelurahan(kecamatanId, selectedKelurahan = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kelurahan') ?>/${kecamatanId}`);
            const kelurahan = response.data.data;
            $('#kelurahan')
                .empty()
                .append('<option value="">-- Pilih Desa/Kelurahan --</option>')
                .append(
                    kelurahan.map(k => `<option value="${k.id}">${k.name}</option>`)
                );
            if (selectedKelurahan) {
                $('#kelurahan').val(selectedKelurahan);
            }
        } catch (error) {
            console.error('Gagal memuat data kelurahan:', error);
        }
    }

    // Event handler saat provinsi dipilih
    $('#provinsi').on('change.select2', function() {
        const provinsiId = $(this).val();
        if (provinsiId) {
            loadKabupaten(provinsiId);
            $('#kecamatan').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        } else {
            $('#kabupaten, #kecamatan, #kelurahan')
                .empty()
                .append('<option value="">-- Pilih --</option>');
        }
    });

    // Event handler saat kabupaten dipilih
    $('#kabupaten').on('change.select2', function() {
        const kabupatenId = $(this).val();
        if (kabupatenId) {
            loadKecamatan(kabupatenId);
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        } else {
            $('#kecamatan, #kelurahan')
                .empty()
                .append('<option value="">-- Pilih --</option>');
        }
    });

    // Event handler saat kecamatan dipilih
    $('#kecamatan').on('change.select2', function() {
        const kecamatanId = $(this).val();
        if (kecamatanId) {
            loadKelurahan(kecamatanId);
        } else {
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        }
    });

    $('#provinsi, #kabupaten, #kecamatan, #kelurahan').select2({
        dropdownParent: $('#pasienForm'),
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });

    $('#pasien-container-tab').on('click', function() {
        $('#tanggal_form').hide();
    });

    async function fetchJenisKunjunganOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/kunjunganoptions/' . $pasien['id_pasien']) ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#kunjunganFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan jenis kunjungan.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchJaminanOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/jaminanoptions/' . $pasien['id_pasien']) ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#jaminanFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan jaminan.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchRuanganOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#ruanganFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchDokterOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#dokterFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchPendaftarOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/pendaftaroptions/' . $pasien['id_pasien']) ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#pendaftarFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan pendaftar.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchStatusOptions() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/statusoptions/' . $pasien['id_pasien']) ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#statusFilter');

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan status.');
            }
        } catch (error) {
            showFailedToast(`${error.response.data.error}<br>${error.response.data.details.message}`);
        }
    }

    async function fetchRajal() {
        const offset = (currentPage - 1) * limit;
        const tanggal = $('#tanggal').val();
        const jenis_kunjungan = $('#kunjunganFilter').val();
        const jaminan = $('#jaminanFilter').val();
        const ruangan = $('#ruanganFilter').val();
        const dokter = $('#dokterFilter').val();
        const pendaftar = $('#pendaftarFilter').val();
        const status = $('#statusFilter').val();
        const transaksi = $('#transaksiFilter').val();

        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pasien/rawatjalanlist/' . $pasien['id_pasien']) ?>', {
                params: {
                    limit: limit,
                    offset: offset,
                    tanggal: tanggal,
                    jenis_kunjungan: jenis_kunjungan,
                    jaminan: jaminan,
                    ruangan: ruangan,
                    dokter: dokter,
                    pendaftar: pendaftar,
                    status: status,
                    transaksi: transaksi
                }
            });

            const data = response.data;
            $('#rajalContainer').empty();
            $('#totalRecords').text(data.total.toLocaleString('id-ID'));

            if (data.total === 0) {
                $('#paginationNav ul').empty();
                $('#rajalContainer').append(
                    '<li class="list-group-item border-top-0 pb-3 pt-3">' +
                    '    <h1 class="display-4 text-center text-muted" style="font-weight: 200;">Data Kosong</h1>' +
                    '</li>'
                );
            } else {
                data.rajal.forEach(function(rajal) {
                    const transaksiBadge = rajal.transaksi == '1' ?
                        `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                        `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;
                    let status = rajal.status;
                    if (status === 'DAFTAR') {
                        status = `<span class="badge bg-success bg-gradient">Didaftarkan</span> ${transaksiBadge}`;
                    } else if (status === 'BATAL') {
                        status = `<span class="badge bg-danger bg-gradient">Rawat Jalan Batal</span>`;
                    }
                    const rajalElement = `
            <li class="list-group-item border-top-0 pb-3 pt-3">
                <div class="d-flex">
                    <div class="align-self-center w-100">
                        <h5 class="card-title d-flex date justify-content-between">
                            <div>
                                <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rajal.number}</span>
                                <span class="align-self-center date">${rajal.nomor_registrasi}</span>
                            </div>
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rajal.no_antrian}</span>
                        </h5>
                        <h6 class="card-subtitle date">
                            ${rajal.pendaftar}
                        </h6>
                        <div class="card-text">
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                            <div class="col date">
                                                ${rajal.tanggal_registrasi}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Jenis Kunjungan</div>
                                            <div class="col date">
                                                ${rajal.jenis_kunjungan}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Ruangan</div>
                                            <div class="col">
                                                ${rajal.ruangan}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Dokter</div>
                                            <div class="col date">
                                                ${rajal.dokter}
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1">
                                            <div class="col-5 fw-medium text-truncate">Keluhan</div>
                                            <div class="col date">
                                                ${rajal.keluhan}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ${status}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-body btn-sm bg-gradient edit-btn" data-id="${rajal.id_rajal}">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Rajal
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient delete-btn" data-id="${rajal.id_rajal}" data-name="${rajal.nomor_registrasi}" data-date="${rajal.tanggal_rajal}">
                        <i class="fa-solid fa-xmark"></i> Batalkan
                    </button>
                </div>
            </li>
                `;

                    $('#rajalContainer').append(rajalElement);
                });

                // Pagination logic with ellipsis for more than 3 pages
                const totalPages = Math.ceil(data.total / limit);
                $('#paginationNav ul').empty();

                if (currentPage > 1) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
                }

                if (totalPages > 5) {
                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                    if (currentPage > 3) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }

                    if (currentPage < totalPages - 2) {
                        $('#paginationNav ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                    }

                    $('#paginationNav ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
                } else {
                    // Show all pages if total pages are 3 or fewer
                    for (let i = 1; i <= totalPages; i++) {
                        $('#paginationNav ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                    }
                }

                if (currentPage < totalPages) {
                    $('#paginationNav ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#rajalContainer').empty();
            $('#paginationNav ul').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).on('click', '#paginationNav a', function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            fetchRajal();
        }
    });

    $('#tanggal, #kunjunganFilter, #jaminanFilter, #ruanganFilter, #dokterFilter, #pendaftarFilter, #statusFilter, #transaksiFilter').on('change', function() {
        $('#rajalContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#rajalContainer').append(placeholder);
        }
        fetchRajal();
    });

    $('#clearTglButton').on('click', function() {
        $('#tanggal').val('');
        $('#rajalContainer').empty();
        for (let i = 0; i < limit; i++) {
            $('#rajalContainer').append(placeholder);
        }
        fetchRajal();
    });

    $('#refreshButton').on('click', async function(e) {
        e.preventDefault();
        await Promise.all([
            fetchJenisKunjunganOptions(),
            fetchJaminanOptions(),
            fetchRuanganOptions(),
            fetchDokterOptions(),
            fetchPendaftarOptions(),
            fetchStatusOptions()
        ]);
        fetchRajal();
    });

    $('#rawatjalan-container-tab').on('click', function() {
        $('#tanggal_form').show();
    });

    $(document).ready(async function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_obat').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        $('#pasienForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#pasienForm .is-invalid').removeClass('is-invalid');
            $('#pasienForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#pasienForm input, #pasienForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/pasien/update/' . $pasien['id_pasien']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchPasien();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#pasienForm .is-invalid').removeClass('is-invalid');
                    $('#pasienForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'jenis_kelamin') {
                                const radioGroup = $("input[name='jenis_kelamin']");
                                const feedbackElement = radioGroup.closest('.col-form-label').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[name='jenis_kelamin']").removeClass('is-invalid');
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
                $('#pasienForm input, #pasienForm select').prop('disabled', false);
            }
        });
        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                await Promise.all([
                    fetchJenisKunjunganOptions(),
                    fetchJaminanOptions(),
                    fetchRuanganOptions(),
                    fetchDokterOptions(),
                    fetchPendaftarOptions(),
                    fetchStatusOptions()
                ]);
                fetchRajal();
            }
        });
        await fetchPasien();
        await Promise.all([
            fetchJenisKunjunganOptions(),
            fetchJaminanOptions(),
            fetchRuanganOptions(),
            fetchDokterOptions(),
            fetchPendaftarOptions(),
            fetchStatusOptions()
        ]);
        fetchRajal();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>