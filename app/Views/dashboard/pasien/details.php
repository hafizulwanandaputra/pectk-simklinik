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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $pasien['no_rm'] ?> • <span id="nama_pasien_header"><?= $pasien['nama_pasien']; ?></span></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $previous['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $next['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav>
                        <div class="nav nav-underline nav-justified flex-nowrap overflow-auto" id="nav-tab" role="tablist">
                            <button class="nav-link py-1 active" id="pasien-container-tab" data-bs-toggle="tab" data-bs-target="#pasien-container" type="button" role="tab" aria-controls="pasien-container" aria-selected="true">Identitas Pasien</button>
                            <button class="nav-link py-1" id="rawatjalan-container-tab" data-bs-toggle="tab" data-bs-target="#rawatjalan-container" type="button" role="tab" aria-controls="rawatjalan-container" aria-selected="false">Rawat Jalan <span id="totalRecords" class="badge bg-body border text-body">0</span></button>
                        </div>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur" id="tanggal_form" style="display: none;">
                <div class="no-fluid-content">
                    <div class="input-group input-group-sm">
                        <input type="date" id="tanggal" name="tanggal" class="form-control ">
                        <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-success bg-gradient " type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></button>
                    </div>
                    <div class="accordion accordion-bg-body mt-2" id="accordionFilter">
                        <div class="accordion-item">
                            <div class="accordion-header lh-1">
                                <button class="accordion-button p-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
                                    Pencarian Tambahan
                                </button>
                            </div>
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
                        <div class="mb-2">
                            <div class="mb-0 row g-1 align-items-center overflow-hidden d-flex align-items-end">
                                <div class="col fw-medium text-nowrap">Didaftarkan</div>
                                <div class="col text-end">
                                    <div class="date text-truncate">
                                        <?= $pasien['tanggal_daftar'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-0 row g-1 align-items-center overflow-hidden d-flex  align-items-end">
                                <div class="col fw-medium text-nowrap">Nomor Rekam Medis</div>
                                <div class="col text-end">
                                    <div class="date text-truncate">
                                        <span id="no_rekam_medis"><?= $pasien['no_rm'] ?></span> <span role="button" id="copy_no_rekam_medis" class="link-primary"><i class="fa-solid fa-copy"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="" autocomplete="off" dir="auto" placeholder="nama_pasien">
                                <label for="nama_pasien">Nama<span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="nik" name="nik" value="" autocomplete="off" dir="auto" placeholder="nik">
                                    <label for="nik">Nomor Induk Kependudukan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="no_bpjs" name="no_bpjs" value="" autocomplete="off" dir="auto" placeholder="no_bpjs">
                                    <label for="no_bpjs">Nomor BPJS</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="" autocomplete="off" dir="auto" placeholder="tempat_lahir">
                                    <label for="tempat_lahir">Tempat Lahir<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="" autocomplete="off" dir="auto" placeholder="tanggal_lahir">
                                    <label for="tanggal_lahir">Tanggal Lahir (DD-MM-YYYY)<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="row gx-1 radio-group">
                                <label for="jenis_kelamin" class="col col-form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                                <div class="col-lg col-form-label">
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
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="alamat" name="alamat" value="" autocomplete="off" dir="auto" placeholder="alamat">
                                <label for="alamat">Alamat<span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="provinsi" name="provinsi" aria-label="provinsi">
                                        <option value="" selected>-- Pilih Provinsi --</option>
                                    </select>
                                    <label for="provinsi">Provinsi<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kabupaten" name="kabupaten" aria-label="kabupaten">
                                        <option value="" selected>-- Pilih Kabupaten/Kota --</option>
                                    </select>
                                    <label for="kabupaten">Kabupaten/Kota<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kecamatan" name="kecamatan" aria-label="kecamatan">
                                        <option value="" selected>-- Pilih Kecamatan --</option>
                                    </select>
                                    <label for="kecamatan">Kecamatan<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kelurahan" name="kelurahan" aria-label="kelurahan">
                                        <option value="" selected>-- Pilih Desa/Kelurahan --</option>
                                    </select>
                                    <label for="kelurahan">Desa/Kelurahan<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="rt" name="rt" value="" autocomplete="off" dir="auto" placeholder="rt">
                                    <label for="rt">RT</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="rw" name="rw" value="" autocomplete="off" dir="auto" placeholder="rw">
                                    <label for="rw">RW</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="telpon" name="telpon" value="" autocomplete="off" dir="auto" placeholder="telpon">
                                    <label for="telpon">Nomor Telepon</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="kewarganegaraan" name="kewarganegaraan" aria-label="kewarganegaraan">
                                        <option value="" selected disabled>-- Pilih Kewarganegaraan --</option>
                                        <option value="WNI">INDONESIA</option>
                                        <option value="WNA">WARGA NEGARA ASING</option>
                                    </select>
                                    <label for="kewarganegaraan">Kewarganegaraan<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="agama" name="agama" aria-label="agama">
                                        <option value="" selected disabled>-- Pilih Agama --</option>
                                        <?php foreach ($agama as $agama_list) : ?>
                                            <option value="<?= $agama_list['agamaId'] ?>"><?= $agama_list['agamaNama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="agama">Agama<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <select class="form-select" id="status_nikah" name="status_nikah" aria-label="status_nikah">
                                        <option value="" selected disabled>-- Pilih Status Perkawinan --</option>
                                        <?php foreach ($status_pernikahan as $status_pernikahan_list) : ?>
                                            <option value="<?= $status_pernikahan_list['pernikahanId'] ?>"><?= $status_pernikahan_list['pernikahanStatus'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="status_nikah">Status Perkawinan<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <select class="form-select" id="pekerjaan" name="pekerjaan" aria-label="pekerjaan">
                                    <option value="" selected disabled>-- Pilih Pekerjaan --</option>
                                    <option value="1">BELUM/TIDAK BEKERJA</option>
                                    <?php foreach ($pekerjaan as $pekerjaan_list) : ?>
                                        <option value="<?= $pekerjaan_list['pekerjaanId'] ?>"><?= $pekerjaan_list['pekerjaanNama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="pekerjaan">Pekerjaan<span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <hr>
                        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                            <button class="btn btn-body bg-gradient" type="button" data-id="<?= $pasien['id_pasien'] ?>" id="printBtn1"><i class="fa-solid fa-print"></i> Cetak Identitas</button>
                            <button class="btn btn-body bg-gradient" type="button" data-id="<?= $pasien['id_pasien'] ?>" id="printBtn2"><i class="fa-solid fa-barcode"></i> Cetak <em>Barcode</em></button>
                            <button class="btn btn-primary  bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        </div>
                    </div>
                    <iframe id="print_frame_1" style="display: none;"></iframe>
                    <iframe id="print_frame_2" style="display: none;"></iframe>
                    <?= form_close(); ?>
                </div>
                <div class="tab-pane" id="rawatjalan-container" role="tabpanel" aria-labelledby="rawatjalan-container-tab" tabindex="0">
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
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                                            </h5>
                                            <h6 class="card-subtitle placeholder-glow">
                                                <span class="placeholder" style="width: 100%;"></span>
                                            </h6>
                                            <div class="card-text placeholder-glow">
                                                <div style="font-size: 0.75em;">
                                                    <div class="row gx-3">
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
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
                            <?php endfor; ?>
                        </ul>
                    </div>
                    <nav id="paginationNav" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                    <iframe id="print_frame_3" style="display: none;"></iframe>
                    <iframe id="print_frame_4" style="display: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="batalRajalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="batalRajalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="batalRajalForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="batalRajalModalLabel" style="font-weight: bold;"></h6>
                    <button id="batalRajalCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="alert alert-warning mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <h4 style="font-weight: 900;">PERINGATAN!</h4>
                                <p>Pastikan pasien Anda benar-benar batal melakukan rawat jalan dengan alasan yang jelas dan masuk akal. Rawat jalan yang batal tidak dapat digunakan untuk proses poliklinik dan pemberian resep. Pembatalan tidak bisa dilakukan apabila transaksi rawat jalan ini sudah diproses oleh kasir.</p>
                                <p class="mb-0">Jika registrasi rawat jalan ini ada kesalahan dalam memasukkan data, silakan pilih "Kesalahan dalam Memasukkan Data". Ini akan menghapus rawat jalan yang dimasukkan dengan data yang salah.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select" id="alasan_batal" name="alasan_batal" aria-label="alasan_batal">
                            <option value="" disabled selected>-- Pilih Alasan Pembatalan --</option>
                            <option value="HAPUS">Kesalahan Input atau Pasien Salah Tempat Berobat (Hapus Registrasi)</option>
                            <option value="Pasien Tidak Jadi Berobat">Pasien Tidak Jadi Berobat (Atur ke Batal)</option>
                        </select>
                        <label for="alasan_batal">Alasan Pembatalan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="cancelSubmitButton" class="btn btn-danger bg-gradient ">
                        <i class="fa-solid fa-xmark"></i> Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="rajalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="rajalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="rajalForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="rajalModalLabel" style="font-weight: bold;"></h6>
                    <button id="rajalCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div id="mediaAlert" class="alert alert-info  mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <p>Jika pasien ini belum pernah berobat, status kunjungan rawat jalan diatur ke BARU.</p>
                                <p class="mb-0">Jika pasien ini sudah pernah berobat, status kunjungan rawat jalan diatur ke LAMA.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select" id="jaminan" name="jaminan" aria-label="jaminan">
                            <option value="" disabled selected>-- Pilih Jaminan --</option>
                        </select>
                        <label for="jaminan">Jaminan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select" id="ruangan" name="ruangan" aria-label="ruangan">
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                        </select>
                        <label for="ruangan">Ruangan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select" id="dokter" name="dokter" aria-label="dokter">
                            <option value="" disabled selected>-- Pilih Dokter --</option>
                        </select>
                        <label for="dokter">Dokter<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="keluhan" id="keluhan" name="keluhan">
                        <label for="keluhan">Keluhan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient ">
                        <i class="fa-solid fa-plus"></i> Registrasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="editRajalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editRajalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="editRajalForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="editRajalModalLabel" style="font-weight: bold;"></h6>
                    <button id="editRajalCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="alert alert-warning mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <h4 style="font-weight: 900;">PERINGATAN!</h4>
                                <p class="mb-0">Mengganti dokter melalui formulir ini tidak akan mengubah nomor antrian. Jika ingin mengubah nomor antrian, hapus rawat jalan ini dan registrasikan dengan rawat jalan baru.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select" id="edit_ruangan" name="edit_ruangan" aria-label="edit_ruangan">
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                        </select>
                        <label for="edit_ruangan">Ruangan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select" id="edit_dokter" name="edit_dokter" aria-label="dokter">
                            <option value="" disabled selected>-- Pilih Dokter --</option>
                        </select>
                        <label for="edit_dokter">Dokter<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="keluhan" id="edit_keluhan" name="edit_keluhan">
                        <label for="edit_keluhan">Keluhan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient ">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="isianOperasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="isianOperasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable ">
            <form id="isianOperasiForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="isianOperasiModalLabel" style="font-weight: bold;"></h6>
                    <button id="isianOperasiCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control " autocomplete="off" dir="auto" placeholder="tindakan_operasi_rajal" id="tindakan_operasi_rajal" name="tindakan_operasi_rajal">
                        <label for="tindakan_operasi_rajal">Tindakan yang akan dilakukan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mt-1 mb-1 row row-cols-1 row-cols-sm-2 g-2">
                        <div class="col form-floating ">
                            <input type="date" class="form-control " autocomplete="off" dir="auto" placeholder="tanggal_operasi_rajal" id="tanggal_operasi_rajal" name="tanggal_operasi_rajal">
                            <label for="tanggal_operasi_rajal">Tanggal Tindakan<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col form-floating ">
                            <input type="time" class="form-control " autocomplete="off" dir="auto" placeholder="jam_operasi_rajal" id="jam_operasi_rajal" name="jam_operasi_rajal">
                            <label for="jam_operasi_rajal">Jam</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitIsianOKButton" class="btn btn-primary bg-gradient ">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </form>
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
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder mx-1" style="width: 100%"></span>
                                                <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span>
                                            </h5>
                                            <h6 class="card-subtitle placeholder-glow">
                                                <span class="placeholder" style="width: 100%;"></span>
                                            </h6>
                                            <div class="card-text placeholder-glow">
                                                <div style="font-size: 0.75em;">
                                                    <div class="row gx-3">
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
                                                                <div class="col-5 fw-medium text-truncate">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                                <div class="col placeholder-glow">
                                                                    <span class="placeholder w-100"></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0 row g-1 align-items-center placeholder-glow">
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

    // HALAMAN IDENTITAS PASIEN
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
            $('#tanggal_lahir').flatpickr({
                altInput: true,
                allowInput: true,
                altFormat: "d-m-Y",
                defaultDate: data.tanggal_lahir,
                disableMobile: "true"
            });
            const selectedGender = response.data.jenis_kelamin;
            if (selectedGender) {
                $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
            }
            $('#alamat').val(data.alamat);
            $('#rt').val(data.rt);
            $('#rw').val(data.rw);
            $('#telpon').val(data.telpon);
            $('#kewarganegaraan').val(data.kewarganegaraan);
            if (data.agama > 0) {
                $('#agama').val(data.agama);
            }
            if (data.status_nikah > 0) {
                $('#status_nikah').val(data.status_nikah);
            }
            if (data.pekerjaan > 0) {
                $('#pekerjaan').val(data.pekerjaan).trigger('change');
            }
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
                provinsi.map(p => `<option value="${p.provinsiId}">${p.provinsiNama}</option>`)
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
                    kabupaten.map(k => `<option value="${k.kabupatenId}">${k.kabupatenNama}</option>`)
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
                    kecamatan.map(k => `<option value="${k.kecamatanId}">${k.kecamatanNama}</option>`)
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
                    kelurahan.map(k => `<option value="${k.kelurahanId}">${k.kelurahanNama}</option>`)
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

    $('#provinsi, #kabupaten, #kecamatan, #kelurahan, #pekerjaan').select2({
        dropdownParent: $('#pasienForm'),
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });

    $('#printBtn1').on('click', function() {
        const id = $(this).data('id');

        // Tampilkan loading di tombol cetak
        const $btn = $(this);
        $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Cetak Identitas`);

        // Muat PDF ke iframe
        var iframe = $('#print_frame_1');
        iframe.attr('src', `<?= base_url("pasien/identitas") ?>/${id}`);

        // Saat iframe selesai memuat, jalankan print
        iframe.off('load').on('load', function() {
            try {
                this.contentWindow.focus();
                this.contentWindow.print();
            } catch (e) {
                showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url("pasien/identitas") ?>/${id}`);
            } finally {
                $btn.prop('disabled', false).html(`<i class="fa-solid fa-print"></i> Cetak Identitas`);
            }
        });
    });

    $('#printBtn2').on('click', function() {
        const id = $(this).data('id');

        // Tampilkan loading di tombol cetak
        const $btn = $(this);
        $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Cetak <em>Barcode</em>`);

        // Muat PDF ke iframe
        var iframe = $('#print_frame_2');
        iframe.attr('src', `<?= base_url("pasien/barcode") ?>/${id}`);

        // Saat iframe selesai memuat, jalankan print
        iframe.off('load').on('load', function() {
            try {
                this.contentWindow.focus();
                this.contentWindow.print();
            } catch (e) {
                showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url("pasien/barcode") ?>/${id}`);
            } finally {
                $btn.prop('disabled', false).html(`<i class="fa-solid fa-print"></i> Cetak <em>Barcode</em>`);
            }
        });
    });

    $('#pasien-container-tab').on('click', function() {
        $('#tanggal_form').hide();
    });

    // HALAMAN RAWAT JALAN
    async function fetchJenisKunjunganOptions(selectedJenisKunjungan = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/kunjunganoptions/' . $pasien['no_rm']) ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedJenisKunjungan || $('#kunjunganFilter').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#kunjunganFilter').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#kunjunganFilter').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#kunjunganFilter').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan jenis kunjungan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchJaminanOptions(selectedJaminan = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/jaminanoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedJaminan || $('#jaminanFilter').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#jaminanFilter').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#jaminanFilter').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#jaminanFilter').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan jaminan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchJaminanOptionsModal() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/jaminanoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#jaminan').prop('disabled', false).find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#jaminan').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan jaminan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchRuanganOptions(selectedRuangan = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedRuangan || $('#ruanganFilter').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#ruanganFilter').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#ruanganFilter').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#ruanganFilter').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchRuanganOptionsModal() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#ruangan').prop('disabled', false).find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#ruangan').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchRuanganOptionsModalEdit() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#edit_ruangan').prop('disabled', false).find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#edit_ruangan').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchDokterOptions(selectedDokter = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedDokter || $('#dokter').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#dokterFilter').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#dokterFilter').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#dokterFilter').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchDokterOptionsModal() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#dokter').prop('disabled', false).find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#dokter').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchDokterOptionsModalEdit() {
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#edit_dokter').prop('disabled', false).find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#edit_dokter').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchPendaftarOptions(selectedPendaftar = null) {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/pendaftaroptions/' . $pasien['no_rm']) ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Simpan nilai yang saat ini dipilih untuk masing-masing elemen
                const currentSelectionFilter = selectedPendaftar || $('#pendaftarFilter').val();

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#pendaftarFilter').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#pendaftarFilter').append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelectionFilter) {
                    $('#pendaftarFilter').val(currentSelectionFilter);
                }
            } else {
                showFailedToast('Gagal mendapatkan pendaftar.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchStatusOptions() {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('pasien/statusoptions/' . $pasien['no_rm']) ?>`);

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
            console.error(error);
            showFailedToast(`${error}`);
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
            const response = await axios.get('<?= base_url('pasien/rawatjalanlist/' . $pasien['no_rm']) ?>', {
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
                        status = `<span class="badge bg-danger bg-gradient">Dibatalkan</span>`;
                    }
                    let pembatal = rajal.status;
                    if (pembatal === 'BATAL') {
                        pembatal = `
                            <div class="mb-0 row g-1 align-items-center">
                                <div class="col-5 fw-medium text-truncate">Dibatalkan oleh</div>
                                <div class="col date">
                                    ${rajal.pembatal}
                                </div>
                            </div>
                            <div class="mb-0 row g-1 align-items-center">
                                <div class="col-5 fw-medium text-truncate">Alasan Pembatalan</div>
                                <div class="col date">
                                    ${rajal.alasan_batal}
                                </div>
                            </div>
                        `;
                    } else if (pembatal === 'DAFTAR') {
                        pembatal = ``;
                    }
                    let tblbatal = rajal.status;
                    if (tblbatal === 'BATAL') {
                        tblbatal = `disabled`;
                    } else if (tblbatal === 'DAFTAR') {
                        tblbatal = ``;
                    }
                    const transaksi = rajal.transaksi == '1' ?
                        `disabled` :
                        ``;
                    const digunakan = (rajal.resep_obat_digunakan > 0 || rajal.resep_kacamata_digunakan > 0 || rajal.transaksi_digunakan > 0) ? 'disabled' : '';
                    let tombol_isian_ok = rajal.ruangan;
                    if (tombol_isian_ok === 'Kamar Operasi') {
                        tombol_isian_ok = `
                        <button type="button" class="btn btn-body btn-sm bg-gradient print-lio-btn" data-id="${rajal.id_rawat_jalan}">
                            <i class="fa-solid fa-receipt"></i> Cetak Lembar Isian Operasi
                        </button>
                        <button type="button" class="btn btn-body btn-sm bg-gradient edit-isian-ok-btn" data-id="${rajal.id_rawat_jalan}" ${transaksi} ${tblbatal}>
                            <i class="fa-solid fa-file-pen"></i> Edit Lembar Isian Operasi
                        </button>
                        `;
                    } else {
                        tombol_isian_ok = ``;
                    }
                    let isian_ok = rajal.ruangan;
                    if (isian_ok === 'Kamar Operasi') {
                        const tindakan_operasi_rajal = rajal.tindakan_operasi_rajal ?
                            `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.tindakan_operasi_rajal}">` :
                            `<em>Belum diisi</em>`;
                        let waktu_operasi_rajal = `<em>Belum diisi</em>`;
                        if (rajal.tanggal_operasi_rajal) {
                            waktu_operasi_rajal = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rajal.tanggal_operasi_rajal}`;
                            if (rajal.jam_operasi_rajal) {
                                waktu_operasi_rajal += ` ${rajal.jam_operasi_rajal}`;
                            }
                            waktu_operasi_rajal += `">`;
                        }
                        isian_ok = `
                            <div class="mb-0 row g-1 align-items-center">
                                <div class="col-5 fw-medium text-truncate">Tindakan yang Akan Dilakukan</div>
                                <div class="col date">
                                    ${tindakan_operasi_rajal}
                                </div>
                            </div>
                            <div class="mb-0 row g-1 align-items-center">
                                <div class="col-5 fw-medium text-truncate">Waktu Tindakan</div>
                                <div class="col date">
                                    ${waktu_operasi_rajal}
                                </div>
                            </div>
                        `;
                    } else {
                        isian_ok = ``;
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
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rajal.kode_antrian}${rajal.no_antrian}</span>
                        </h5>
                        <h6 class="card-subtitle date">
                            ${rajal.pendaftar}
                        </h6>
                        <div class="card-text">
                            <div style="font-size: 0.75em;">
                                <div class="row gx-3">
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                            <div class="col">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rajal.tanggal_registrasi}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Status Kunjungan</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.status_kunjungan}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Jaminan</div>
                                            <div class="col">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.jaminan}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Ruangan</div>
                                            <div class="col">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.ruangan}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Dokter</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.dokter}">
                                            </div>
                                        </div>
                                        <div class="mb-0 row g-1 align-items-center">
                                            <div class="col-5 fw-medium text-truncate">Keluhan</div>
                                            <div class="col date">
                                                <input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rajal.keluhan}">
                                            </div>
                                        </div>
                                        ${isian_ok}
                                        ${pembatal}
                                    </div>
                                </div>
                            </div>
                            ${status}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-body btn-sm bg-gradient print-struk-btn" data-id="${rajal.id_rawat_jalan}">
                        <i class="fa-solid fa-receipt"></i> Struk
                    </button>
                    ${tombol_isian_ok}
                    <button type="button" class="btn btn-body btn-sm bg-gradient edit-rajal-btn" data-id="${rajal.id_rawat_jalan}" ${transaksi} ${tblbatal} ${digunakan}>
                        <i class="fa-solid fa-file-pen"></i> Edit Rawat Jalan
                    </button>
                    <button type="button" class="btn btn-danger btn-sm bg-gradient cancel-btn" data-id="${rajal.id_rawat_jalan}" ${transaksi} ${tblbatal}>
                        <i class="fa-solid fa-xmark"></i> Batal
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

    $(document).on('click', '.print-struk-btn', function() {
        const id = $(this).data('id');

        // Tampilkan loading di tombol cetak
        const $btn = $(this);
        $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Struk`);

        // Muat PDF ke iframe
        var iframe = $('#print_frame_3');
        iframe.attr('src', `<?= base_url("rawatjalan/struk") ?>/${id}`);

        // Saat iframe selesai memuat, jalankan print
        iframe.off('load').on('load', function() {
            try {
                this.contentWindow.focus();
                this.contentWindow.print();
            } catch (e) {
                showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url("rawatjalan/struk") ?>/${id}`);
            } finally {
                $btn.prop('disabled', false).html(`<i class="fa-solid fa-receipt"></i> Struk`);
            }
        });
    });

    $(document).on('click', '.print-lio-btn', function() {
        const id = $(this).data('id');

        // Tampilkan loading di tombol cetak
        const $btn = $(this);
        $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Cetak Lembar Isian Operasi`);

        // Muat PDF ke iframe
        var iframe = $('#print_frame_4');
        iframe.attr('src', `<?= base_url('rawatjalan/lembarisianoperasi') ?>/${id}`);

        // Saat iframe selesai memuat, jalankan print
        iframe.off('load').on('load', function() {
            try {
                this.contentWindow.focus();
                this.contentWindow.print();
            } catch (e) {
                showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url('rawatjalan/lembarisianoperasi') ?>/${id}`);
            } finally {
                $btn.prop('disabled', false).html(`<i class="fa-solid fa-receipt"></i> Cetak Lembar Isian Operasi`);
            }
        });
    });

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
        const selectedJenisKunjungan = $('#kunjunganFilter').val();
        const selectedJaminan = $('#jaminanFilter').val();
        const selectedRuangan = $('#ruanganFilter').val();
        const selectedDokter = $('#dokterFilter').val();
        const selectedPendaftar = $('#pendaftarFilter').val();
        await Promise.all([
            fetchJenisKunjunganOptions(selectedJenisKunjungan),
            fetchJaminanOptions(selectedJaminan),
            fetchRuanganOptions(selectedRuangan),
            fetchDokterOptions(selectedDokter),
            fetchPendaftarOptions(selectedPendaftar)
        ]);
        fetchRajal();
    });

    $('#rawatjalan-container-tab').on('click', async function() {
        $('#tanggal_form').show();
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

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update || data.delete) {
                console.log("Received update from WebSocket");
                const selectedJenisKunjungan = $('#kunjunganFilter').val();
                const selectedJaminan = $('#jaminanFilter').val();
                const selectedRuangan = $('#ruanganFilter').val();
                const selectedDokter = $('#dokterFilter').val();
                const selectedPendaftar = $('#pendaftarFilter').val();
                await Promise.all([
                    fetchJenisKunjunganOptions(selectedJenisKunjungan),
                    fetchJaminanOptions(selectedJaminan),
                    fetchRuanganOptions(selectedRuangan),
                    fetchDokterOptions(selectedDokter),
                    fetchPendaftarOptions(selectedPendaftar)
                ]);
                fetchRajal();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('#copy_no_rekam_medis').on('click', function() {
            var textToCopy = $('#no_rekam_medis').text().trim();

            if (navigator.clipboard) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    $('#copy_no_rekam_medis').removeClass('link-primary').addClass('link-success').html(`<i class="fa-solid fa-check"></i>`);

                    setTimeout(function() {
                        $('#copy_no_rekam_medis').addClass('link-primary').removeClass('link-success').html(`<i class="fa-solid fa-copy"></i>`);
                    }, 1000);
                }).catch(function(err) {
                    $('#copy_no_rekam_medis').removeClass('link-primary').addClass('link-danger').html(`<i class="fa-solid fa-xmark"></i>`);

                    setTimeout(function() {
                        $('#copy_no_rekam_medis').addClass('link-primary').removeClass('link-danger').html(`<i class="fa-solid fa-copy"></i>`);
                    }, 1000);
                    console.error('Gagal menyalin teks:', err);
                });
            } else {
                showFailedToast('Clipboard API tidak didukung di peramban ini.');
            }
        });

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
                <?= $this->include('spinner/spinner'); ?> Simpan
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
                            if (['jenis_kelamin'].includes(field)) {
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
                $('#pasienForm input, #pasienForm select').prop('disabled', false);
            }
        });

        var rajalId;

        // Tampilkan modal registrasi rawat jalan
        $('#addRajalButton').click(async function() {
            $(this).prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Memuat
            `);
            try {
                await Promise.all([
                    fetchJaminanOptionsModal(),
                    fetchRuanganOptionsModal(),
                    fetchDokterOptionsModal()
                ]);
                $('#rajalModalLabel').text('Registrasi Rawat Jalan'); // Ubah judul modal menjadi 'Registrasi Rawat Jalan'
                $('#rajalModal').modal('show'); // Tampilkan modal
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $(this).prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Registrasi Rawat Jalan
                `);
            }
        });


        // Tampilkan modal batalkan rawat halan
        $(document).on('click', '.cancel-btn', function() {
            rajalId = $(this).data('id');
            $('#batalRajalModalLabel').text('Batalkan Rawat Jalan'); // Ubah judul modal menjadi 'Batalkan Rawat Jalan'
            $('#batalRajalModal').modal('show'); // Tampilkan modal resep luar
        });

        $(document).on('click', '.edit-rajal-btn', async function() {
            const $this = $(this); // Menyimpan referensi ke tombol yang diklik
            rajalId = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Edit Rawat Jalan`); // Menampilkan spinner

            try {
                // Melakukan permintaan dengan Axios untuk mendapatkan data pengguna
                const response = await axios.get(`<?= base_url('/rawatjalan/rawatjalan') ?>/${rajalId}`);
                await Promise.all([
                    fetchRuanganOptionsModalEdit(),
                    fetchDokterOptionsModalEdit()
                ]);
                // Memperbarui field modal dengan data pengguna yang diterima
                $('#editRajalModalLabel').text('Edit Rawat Jalan');
                $('#edit_ruangan').val(response.data.ruangan);
                $('#edit_dokter').val(response.data.dokter);
                $('#edit_keluhan').val(response.data.keluhan);
                // Menampilkan modal
                $('#editRajalModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                // Mengatur ulang status tombol
                $this.prop('disabled', false).html(`<i class="fa-solid fa-file-pen"></i> Edit Rawat Jalan`); // Mengembalikan tampilan tombol
            }
        });

        // Mengedit pengguna saat tombol edit diklik
        $(document).on('click', '.edit-isian-ok-btn', async function() {
            const $this = $(this); // Menyimpan referensi ke tombol yang diklik
            rajalId = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide'); // Menyembunyikan tooltip
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Edit Lembar Isian Operasi`); // Menampilkan spinner

            try {
                // Melakukan permintaan dengan Axios untuk mendapatkan data pengguna
                const response = await axios.get(`<?= base_url('/rawatjalan/rawatjalan') ?>/${rajalId}`);

                // Memperbarui field modal dengan data pengguna yang diterima
                $('#isianOperasiModalLabel').text('Lembar Isian Operasi');
                $('#tindakan_operasi_rajal').val(response.data.tindakan_operasi_rajal);
                $('#tanggal_operasi_rajal').val(response.data.tanggal_operasi_rajal);
                $('#jam_operasi_rajal').val(response.data.jam_operasi_rajal);
                // Menampilkan modal
                $('#isianOperasiModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error); // Menampilkan pesan kesalahan
            } finally {
                // Mengatur ulang status tombol
                $this.prop('disabled', false).html(`<i class="fa-solid fa-file-pen"></i> Edit Lembar Isian Operasi`); // Mengembalikan tampilan tombol
            }
        });

        $('#rajalForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#rajalForm .is-invalid').removeClass('is-invalid');
            $('#rajalForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Registrasi
            `);

            // Disable form inputs
            $('#rajalForm input, #rajalForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/create/' . $pasien['no_rm']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#rajalModal').modal('hide');
                    const selectedJenisKunjungan = $('#kunjunganFilter').val();
                    const selectedJaminan = $('#jaminanFilter, #jaminan').val();
                    const selectedRuangan = $('#ruanganFilter, #ruangan').val();
                    const selectedDokter = $('#dokterFilter, #dokter').val();
                    const selectedPendaftar = $('#pendaftarFilter').val();
                    await Promise.all([
                        fetchJenisKunjunganOptions(selectedJenisKunjungan),
                        fetchJaminanOptions(selectedJaminan),
                        fetchRuanganOptions(selectedRuangan),
                        fetchDokterOptions(selectedDokter),
                        fetchPendaftarOptions(selectedPendaftar)
                    ]);
                    fetchRajal();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#rajalForm .is-invalid').removeClass('is-invalid');
                    $('#rajalForm .invalid-feedback').text('').hide();

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
                }
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Registrasi
                `);
                $('#rajalForm input, #rajalForm select').prop('disabled', false);
            }
        });

        $('#editRajalForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#editRajalForm .is-invalid').removeClass('is-invalid');
            $('#editRajalForm .invalid-feedback').text('').hide();
            $('#submitIsianOKButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#editRajalForm input, #editRajalForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/edit') ?>/${rajalId}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#editRajalModal').modal('hide');
                    const selectedJenisKunjungan = $('#kunjunganFilter').val();
                    const selectedJaminan = $('#jaminanFilter, #jaminan').val();
                    const selectedRuangan = $('#ruanganFilter, #ruangan').val();
                    const selectedDokter = $('#dokterFilter, #dokter').val();
                    const selectedPendaftar = $('#pendaftarFilter').val();
                    await Promise.all([
                        fetchJenisKunjunganOptions(selectedJenisKunjungan),
                        fetchJaminanOptions(selectedJaminan),
                        fetchRuanganOptions(selectedRuangan),
                        fetchDokterOptions(selectedDokter),
                        fetchPendaftarOptions(selectedPendaftar)
                    ]);
                    fetchRajal();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#editRajalForm .is-invalid').removeClass('is-invalid');
                    $('#editRajalForm .invalid-feedback').text('').hide();

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
                }
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitIsianOKButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#editRajalForm input, #editRajalForm select').prop('disabled', false);
            }
        });

        $('#batalRajalForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#batalRajalForm .is-invalid').removeClass('is-invalid');
            $('#batalRajalForm .invalid-feedback').text('').hide();
            $('#cancelSubmitButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#batalRajalForm input, #batalrajalForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/cancel') ?>/${rajalId}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    $('#batalRajalModal').modal('hide');
                    const selectedJenisKunjungan = $('#kunjunganFilter, #jenis_kunjungan').val();
                    const selectedJaminan = $('#jaminanFilter, #jaminan').val();
                    const selectedRuangan = $('#ruanganFilter, #ruangan').val();
                    const selectedDokter = $('#dokterFilter, #dokter').val();
                    const selectedPendaftar = $('#pendaftarFilter').val();
                    await Promise.all([
                        fetchJenisKunjunganOptions(selectedJenisKunjungan),
                        fetchJaminanOptions(selectedJaminan),
                        fetchRuanganOptions(selectedRuangan),
                        fetchDokterOptions(selectedDokter),
                        fetchPendaftarOptions(selectedPendaftar)
                    ]);
                    fetchRajal();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#batalRajalForm .is-invalid').removeClass('is-invalid');
                    $('#batalRajalForm .invalid-feedback').text('').hide();

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
                }
            } catch (error) {
                if (error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#cancelSubmitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#batalRajalForm input, #batalrajalForm select').prop('disabled', false);
            }
        });

        $('#isianOperasiForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#isianOperasiForm .is-invalid').removeClass('is-invalid');
            $('#isianOperasiForm .invalid-feedback').text('').hide();
            $('#submitIsianOKButton').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#isianOperasiForm input, #isianOperasiForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/editlembarisianoperasi') ?>/${rajalId}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#isianOperasiModal').modal('hide');
                    const selectedJenisKunjungan = $('#kunjunganFilter').val();
                    const selectedJaminan = $('#jaminanFilter, #jaminan').val();
                    const selectedRuangan = $('#ruanganFilter, #ruangan').val();
                    const selectedDokter = $('#dokterFilter, #dokter').val();
                    const selectedPendaftar = $('#pendaftarFilter').val();
                    await Promise.all([
                        fetchJenisKunjunganOptions(selectedJenisKunjungan),
                        fetchJaminanOptions(selectedJaminan),
                        fetchRuanganOptions(selectedRuangan),
                        fetchDokterOptions(selectedDokter),
                        fetchPendaftarOptions(selectedPendaftar)
                    ]);
                    fetchRajal();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#isianOperasiForm .is-invalid').removeClass('is-invalid');
                    $('#isianOperasiForm .invalid-feedback').text('').hide();

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
                }
            } catch (error) {
                if (error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitIsianOKButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#isianOperasiForm input, #isianOperasiForm select').prop('disabled', false);
            }
        });

        $('#rajalModal').on('hidden.bs.modal', function() {
            $('#rajalForm')[0].reset();
            $('#jaminan').find('option:not(:first)').remove();
            $('#ruangan').find('option:not(:first)').remove();
            $('#dokter').find('option:not(:first)').remove();
            $('#rajalForm .is-invalid').removeClass('is-invalid');
            $('#rajalForm .invalid-feedback').text('').hide();
        });
        $('#batalRajalModal').on('hidden.bs.modal', function() {
            $('#batalRajalForm')[0].reset();
            $('#batalRajalForm .is-invalid').removeClass('is-invalid');
            $('#batalRajalForm .invalid-feedback').text('').hide();
        });
        $('#editRajalModal').on('hidden.bs.modal', function() {
            $('#editRajalForm')[0].reset();
            $('#editRajalForm .is-invalid').removeClass('is-invalid');
            $('#editRajalForm .invalid-feedback').text('').hide();
        });
        $('#isianOperasiModal').on('hidden.bs.modal', function() {
            $('#isianOperasiForm')[0].reset();
            $('#isianOperasiForm .is-invalid').removeClass('is-invalid');
            $('#isianOperasiForm .invalid-feedback').text('').hide();
        });

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                const selectedJenisKunjungan = $('#kunjunganFilter').val();
                const selectedJaminan = $('#jaminanFilter').val();
                const selectedRuangan = $('#ruanganFilter').val();
                const selectedDokter = $('#dokterFilter').val();
                const selectedPendaftar = $('#pendaftarFilter').val();
                await Promise.all([
                    fetchJenisKunjunganOptions(selectedJenisKunjungan),
                    fetchJaminanOptions(selectedJaminan),
                    fetchRuanganOptions(selectedRuangan),
                    fetchDokterOptions(selectedDokter),
                    fetchPendaftarOptions(selectedPendaftar)
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