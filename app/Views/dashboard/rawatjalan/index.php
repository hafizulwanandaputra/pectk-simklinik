<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_rajal">0</span> rawat jalan (<span id="total_didaftarkan">0</span> didaftarkan • <span id="total_dibatalkan">0</span> dibatalkan)</div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top px-2 pt-2" style="z-index: 99;">
        <ul class="list-group no-fluid-content-list-group shadow-sm border border-bottom-0">
            <li class="list-group-item px-2 border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav>
                        <div class="nav nav-pills nav-justified flex-nowrap overflow-auto" id="nav-tab" role="tablist">
                            <button class="nav-link py-1 text-nowrap active" id="tanggal-container-tab" data-bs-toggle="tab" data-bs-target="#tanggal-container" type="button" role="tab" aria-controls="tanggal-container" aria-selected="true">Tanggal</button>
                            <button class="nav-link py-1 text-nowrap" id="no_rm-container-tab" data-bs-toggle="tab" data-bs-target="#no_rm-container" type="button" role="tab" aria-controls="no_rm-container" aria-selected="false">Nomor Rekam Medis</button>
                            <button class="nav-link py-1 text-nowrap" id="nama-container-tab" data-bs-toggle="tab" data-bs-target="#nama-container" type="button" role="tab" aria-controls="nama-container" aria-selected="false">Nama</button>
                        </div>
                    </nav>
                    <div id="tanggal_form" class="pt-2">
                        <div class="no-fluid-content">
                            <div class="input-group input-group-sm">
                                <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                                <button class="btn btn-primary bg-gradient" type="button" id="setTodayTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Kembali ke Hari Ini"><i class="fa-solid fa-calendar-day"></i></button>
                                <button class="btn btn-success bg-gradient" type="button" id="refreshTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="no-rm_form" class="pt-2" style="display: none;">
                        <form class="no-fluid-content" id="no-rm_form_content">
                            <div class="input-group input-group-sm">
                                <input type="search" id="no_rm" name="no_rm" class="form-control" placeholder="xx-xx-xx" autocomplete="off" dir="auto">
                                <button class="btn btn-primary bg-gradient" type="submit" id="no_rm_submitBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
                                <button class="btn btn-success bg-gradient" type="button" id="refreshNoRMButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                            </div>
                        </form>
                    </div>
                    <div id="nama_form" class="pt-2" style="display: none;">
                        <form class="no-fluid-content" id="nama_form_content">
                            <div class="input-group input-group-sm">
                                <input type="search" id="nama" name="nama" class="form-control" placeholder="Nama pasien" autocomplete="off" dir="auto">
                                <button class="btn btn-primary bg-gradient" type="submit" id="nama_submitBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
                                <button class="btn btn-success bg-gradient" type="button" id="refreshNamaButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane show active" id="tanggal-container" role="tabpanel" aria-labelledby="tanggal-container-tab" tabindex="0">
                    <div id="rawatjalan-tanggal" class="list-group shadow-sm">
                        <?php for ($i = 0; $i < 24; $i++) : ?>
                            <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" disabled style="cursor: wait;">
                                <div class="px-3 py-2">
                                    <div class="text-truncate">
                                        <h5 class="d-flex date justify-content-start mb-0 text-truncate placeholder-glow">
                                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                                        </h5>
                                        <h6 class="mb-1 text-truncate placeholder-glow">
                                            <span class="placeholder w-100" style="max-width: 15.625em;"></span> <!-- 250px -->
                                        </h6>
                                        <div class="d-flex align-items-stretch date mb-1 placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 h-100 d-flex align-items-center me-1" style="max-width: 9.375em;"></span> <!-- 150px -->
                                            <span class="placeholder w-100 h-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                        <div class="d-flex align-items-stretch date placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 1.5625em;"></span> <!-- 25px -->
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 3.125em;"></span> <!-- 50px -->
                                            <span class="placeholder w-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                    </div>
                                </div>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <nav id="paginationNav-tanggal" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                </div>
                <div class="tab-pane" id="no_rm-container" role="tabpanel" aria-labelledby="no_rm-container-tab" tabindex="0">
                    <div id="rawatjalan-no_rm" class="list-group shadow-sm">
                        <?php for ($i = 0; $i < 24; $i++) : ?>
                            <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" disabled style="cursor: wait;">
                                <div class="px-3 py-2">
                                    <div class="text-truncate">
                                        <h5 class="d-flex date justify-content-start mb-0 text-truncate placeholder-glow">
                                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                                        </h5>
                                        <h6 class="mb-1 text-truncate placeholder-glow">
                                            <span class="placeholder w-100" style="max-width: 15.625em;"></span> <!-- 250px -->
                                        </h6>
                                        <div class="d-flex align-items-stretch date mb-1 placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 h-100 d-flex align-items-center me-1" style="max-width: 9.375em;"></span> <!-- 150px -->
                                            <span class="placeholder w-100 h-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                        <div class="d-flex align-items-stretch date placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 1.5625em;"></span> <!-- 25px -->
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 3.125em;"></span> <!-- 50px -->
                                            <span class="placeholder w-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                    </div>
                                </div>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <nav id="paginationNav-no_rm" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                </div>
                <div class="tab-pane" id="nama-container" role="tabpanel" aria-labelledby="nama-container-tab" tabindex="0">
                    <div id="rawatjalan-nama" class="list-group shadow-sm">
                        <?php for ($i = 0; $i < 24; $i++) : ?>
                            <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" disabled style="cursor: wait;">
                                <div class="px-3 py-2">
                                    <div class="text-truncate">
                                        <h5 class="d-flex date justify-content-start mb-0 text-truncate placeholder-glow">
                                            <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;"><?= $this->include('spinner/spinner'); ?></span> <span class="placeholder ms-1" style="width: 100%"></span>
                                        </h5>
                                        <h6 class="mb-1 text-truncate placeholder-glow">
                                            <span class="placeholder w-100" style="max-width: 15.625em;"></span> <!-- 250px -->
                                        </h6>
                                        <div class="d-flex align-items-stretch date mb-1 placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 h-100 d-flex align-items-center me-1" style="max-width: 9.375em;"></span> <!-- 150px -->
                                            <span class="placeholder w-100 h-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                        <div class="d-flex align-items-stretch date placeholder-glow" style="height: 1.25em;">
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 1.5625em;"></span> <!-- 25px -->
                                            <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 3.125em;"></span> <!-- 50px -->
                                            <span class="placeholder w-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                                        </div>
                                    </div>
                                </div>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <nav id="paginationNav-nama" class="d-flex justify-content-center justify-content-lg-end mt-3 overflow-auto w-100">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                    <iframe id="print_frame_1" style="display: none;"></iframe>
                    <iframe id="print_frame_2" style="display: none;"></iframe>
                    <iframe id="print_frame_3" style="display: none;"></iframe>
                    <iframe id="print_frame_4" style="display: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rajalModal" tabindex="-1" aria-labelledby="rajalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable">
            <div id="rajalModalContent" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="rajalModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="row g-3">
                        <div class="d-flex flex-column justify-content-between">
                            <div>
                                <div class="fw-bold mb-2 d-flex justify-content-between align-items-start" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                                    <div>Identitas Pasien</div>
                                    <div class="text-nowrap">
                                        <span role="button" id="copy_identitas_pasien" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Salin nama pasien, nomor rekam medis, dan tanggal lahir"><i class="fa-solid fa-copy"></i></span>
                                    </div>
                                </div>
                                <div style="font-size: 0.75em;">
                                    <span id="copy_identitas_pasien_value" class="d-none"></span>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Nama</div>
                                        <div class="col">
                                            <span id="nama_pasien"></span>
                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                                        <div class="col date">
                                            <span id="no_rekam_medis"></span>
                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                                        <div class="col" id="jenis_kelamin"></div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Tempat Lahir</div>
                                        <div class="col">
                                            <span id="tempat_lahir"></span>
                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Tanggal Lahir</div>
                                        <div class="col">
                                            <span id="tanggal_lahir"></span>
                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Usia</div>
                                        <div class="col date" id="usia"></div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Alamat</div>
                                        <div class="col" id="alamat"></div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Kewarganegaraan</div>
                                        <div class="col" id="kewarganegaraan"></div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                                        <div class="col date" id="telpon"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                    <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                        <button id="identitas_btn" type="button" class="btn btn-body btn-sm bg-gradient print-identitas" data-id="">
                                            <i class="fa-solid fa-print"></i> Identitas
                                        </button>
                                        <button id="barcode_btn" type="button" class="btn btn-body btn-sm bg-gradient print-barcode" data-id="">
                                            <i class="fa-solid fa-barcode"></i> <em>Barcode</em>
                                        </button>
                                        <button id="detail_pasien_btn" type="button" class="btn btn-body btn-sm bg-gradient" onclick="">
                                            <i class="fa-solid fa-circle-info"></i> Detail Pasien
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-between">
                            <div>
                                <div class="fw-bold mb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">Rawat Jalan</div>
                                <div style="font-size: 0.75em;">
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Operator</div>
                                        <div class="col date" id="pendaftar">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                                        <div class="col date" id="nomor_registrasi">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                        <div class="col date" id="tanggal_registrasi">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Status Kunjungan</div>
                                        <div class="col" id="status_kunjungan">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Jaminan</div>
                                        <div class="col" id="jaminan">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Ruangan</div>
                                        <div class="col" id="ruangan">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Dokter</div>
                                        <div class="col" id="dokter">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Keluhan</div>
                                        <div class="col" id="keluhan">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center pasien_operasi">
                                        <div class="col-5 fw-medium text-truncate">Tindakan yang Akan Dilakukan</div>
                                        <div class="col date" id="tindakan_operasi_rajal">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center pasien_operasi">
                                        <div class="col-5 fw-medium text-truncate">Waktu Tindakan</div>
                                        <div class="col date" id="waktu_operasi_rajal">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center">
                                        <div class="col-5 fw-medium text-truncate">Status Layanan</div>
                                        <div class="col" id="status">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center rajal_batal">
                                        <div class="col-5 fw-medium text-truncate">Dibatalkan oleh</div>
                                        <div class="col date" id="pembatal">

                                        </div>
                                    </div>
                                    <div class="mb-0 row g-1 align-items-center rajal_batal">
                                        <div class="col-5 fw-medium text-truncate">Alasan Pembatalan</div>
                                        <div class="col date" id="alasan_batal">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tombol_rme">
                            </div>
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
    let limit = 24;
    let currentPage = 1;
    let loading = '';
    for (let i = 0; i < limit; i++) {
        loading += `
        <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" disabled style="cursor: wait;">
            <div class="px-3 py-2">
                <div class="text-truncate">
                    <h5 class="d-flex date justify-content-start mb-0 text-truncate placeholder-glow">
                        <span class="badge bg-body text-body border py-1 px-2 date placeholder number-placeholder" style="font-weight: 900; font-size: 0.85em; padding-top: .1rem !important; padding-bottom: .1rem !important;">
                            <?= $this->include('spinner/spinner'); ?>
                        </span> 
                        <span class="placeholder ms-1" style="width: 100%"></span>
                    </h5>
                    <h6 class="mb-1 text-truncate placeholder-glow">
                        <span class="placeholder w-100" style="max-width: 15.625em;"></span> <!-- 250px -->
                    </h6>
                    <div class="d-flex align-items-stretch date mb-1 placeholder-glow" style="height: 1.25em;">
                        <span class="placeholder w-100 h-100 d-flex align-items-center me-1" style="max-width: 9.375em;"></span> <!-- 150px -->
                        <span class="placeholder w-100 h-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                    </div>
                    <div class="d-flex align-items-stretch date placeholder-glow" style="height: 1.25em;">
                        <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 1.5625em;"></span> <!-- 25px -->
                        <span class="placeholder w-100 d-flex align-items-center me-1" style="max-width: 3.125em;"></span> <!-- 50px -->
                        <span class="placeholder w-100 d-flex align-items-center" style="max-width: 6.25em;"></span> <!-- 100px -->
                    </div>
                </div>
            </div>
        </button>
    `;
    }

    // Fungsi untuk menghitung usia dan sisa bulan berdasarkan tanggal lahir
    function hitungUsia(tanggalLahir, tanggalRegistrasi) {
        const lahir = new Date(tanggalLahir); // Mengubah tanggal lahir menjadi objek Date
        const sekarang = new Date(tanggalRegistrasi); // Mengubah tanggal registrasi menjadi objek Date

        // Menghitung usia dalam tahun
        let usia = sekarang.getFullYear() - lahir.getFullYear();

        // Menghitung selisih bulan
        let bulan = sekarang.getMonth() - lahir.getMonth();

        // Menghitung selisih hari untuk memastikan bulan tidak negatif
        const hari = sekarang.getDate() - lahir.getDate();

        // Periksa apakah bulan/hari ulang tahun belum terlewati di tahun ini
        if (bulan < 0 || (bulan === 0 && hari < 0)) {
            usia--; // Kurangi usia jika ulang tahun belum terlewati
            bulan += 12; // Tambahkan 12 bulan jika bulan menjadi negatif
        }

        // Jika hari di bulan ini belum cukup, kurangi bulan
        if (hari < 0) {
            bulan--;
        }

        // Pastikan bulan berada dalam rentang 0-11
        if (bulan < 0) {
            bulan += 12;
        }

        return {
            usia,
            bulan
        }; // Mengembalikan usia dan sisa bulan
    }

    // Fungsi untuk mengambil data rawat jalan
    async function fetchRajalTanggal() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();
            const offset = (currentPage - 1) * limit;

            // Cek apakah tanggal diinput
            if (!tanggal) {
                $('#paginationNav-tanggal ul').empty();
                $('#rawatjalan-tanggal').empty(); // Kosongkan tabel rawat jalan
                $('#refreshTglButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Silakan masukkan tanggal</h2>
                    </span>
                `;
                $('#rawatjalan-tanggal').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                $('#total_didaftarkan').text('0');
                $('#total_dibatalkan').text('0');
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlisttanggal') ?>`, {
                params: {
                    tanggal: tanggal,
                    limit: limit,
                    offset: offset
                }
            });
            const data = response.data.data; // Mendapatkan data rawat jalan
            const total = response.data.total; // Mendapatkan total rawat jalan
            const didaftarkan = response.data.didaftarkan; // Mendapatkan total rawat jalan didaftarkan
            const dibatalkan = response.data.dibatalkan; // Mendapatkan total rawat jalan dibatalkan
            const promises = [];

            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel rawat jalan
            $('#refreshTglButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(total.toLocaleString('id-ID')); // Jumlah data
            $('#total_didaftarkan').text(didaftarkan.toLocaleString('id-ID')); // Jumlah didaftarkan
            $('#total_dibatalkan').text(dibatalkan.toLocaleString('id-ID')); // Jumlah dibatalkan

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                $('#paginationNav-tanggal ul').empty();
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Tidak ada pasien yang berobat pada ${tanggal}</h2>
                    </span>
                `;
                $('#rawatjalan-tanggal').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap rawatjalan ke tabel
            data.forEach(function(rawatjalan, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = rawatjalan.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i><span class="ms-1">LAKI-LAKI</span></span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: Pink"><i class="fa-solid fa-venus"></i><span class="ms-1">PEREMPUAN</span></span>`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = rawatjalan.telpon ? rawatjalan.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                let status = rawatjalan.status;
                let transaksi = rawatjalan.transaksi;
                if (status === 'DAFTAR' && transaksi === '0') {
                    status = `<span class="badge text-bg-primary bg-gradient h-100 d-flex align-items-center me-1">Didaftarkan</span>`;
                } else if (status === 'DAFTAR' && transaksi === '1') {
                    status = `<span class="badge text-bg-success bg-gradient h-100 d-flex align-items-center me-1">Sudah Dilayani</span>`;
                } else if (status === 'BATAL') {
                    status = `<span class="badge text-bg-danger bg-gradient h-100 d-flex align-items-center">Dibatalkan</span>`;
                }

                // Membuat elemen baris untuk setiap rawatjalan
                const rawatJalanElement = `
                <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" data-id="${rawatjalan.id_rawat_jalan}">
                    <div class="px-3 py-2">
                        <div class="text-truncate">
                            <h5 class="d-flex date justify-content-start mb-0 text-truncate">
                                <span class="badge bg-body text-body border px-2 align-self-start" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rawatjalan.number}</span>
                                <span class="ms-1 align-self-center text-truncate">${rawatjalan.nama_pasien}</span>
                            </h5>
                            <h6 class="mb-1 text-truncate">${rawatjalan.dokter}</h6>
                            <div class="d-flex align-items-stretch date mb-1" style="height: 1.25em;">
                                <span class="badge bg-body text-body border text-nowrap h-100 d-flex align-items-center me-1">${rawatjalan.nomor_registrasi}</span> ${jenis_kelamin}
                            </div>
                            <div class="d-flex align-items-stretch date" style="height: 1.25em;">
                                <span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.kode_antrian}${rawatjalan.no_antrian}</span><span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.status_kunjungan}</span>
                                ${status}
                            </div>
                        </div>
                    </div>
                </button>
                `;
                const promise = new Promise((resolve) => {
                    $('#rawatjalan-tanggal').append(rawatJalanElement);
                    resolve(); // Menandakan bahwa penambahan elemen telah selesai
                });
                promises.push(promise); // Menambahkan promise ke dalam array promises
            });
            // Menunggu semua promise selesai
            await Promise.all(promises);

            // Pagination logic with ellipsis for more than 3 pages
            const totalPages = Math.ceil(total / limit);
            $('#paginationNav-tanggal ul').empty();

            if (currentPage > 1) {
                $('#paginationNav-tanggal ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
            }

            if (totalPages > 5) {
                $('#paginationNav-tanggal ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                if (currentPage > 3) {
                    $('#paginationNav-tanggal ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                    $('#paginationNav-tanggal ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (currentPage < totalPages - 2) {
                    $('#paginationNav-tanggal ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                $('#paginationNav-tanggal ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
            } else {
                // Show all pages if total pages are 3 or fewer
                for (let i = 1; i <= totalPages; i++) {
                    $('#paginationNav-tanggal ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
            }

            if (currentPage < totalPages) {
                $('#paginationNav-tanggal ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
            }
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <span class="list-group-item pb-3 pt-3">
                    <h2 class="text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </span>
            `;
            $('#paginationNav-tanggal ul').empty();
            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-tanggal').append(errorRow); // Menambahkan baris error ke tabel
        }
    }

    async function fetchRajalNoRM() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai nomor rekam medis dari input
            const no_rm = $('#no_rm').val();
            const offset = (currentPage - 1) * limit;
            // Cek apakah formatnya sesuai dengan xx-xx-xx
            const regex = /^\d{2}-\d{2}-\d{2}$/;

            // Cek apakah no_rm diinput
            if (!no_rm) {
                $('#paginationNav-no_rm ul').empty();
                $('#rawatjalan-no_rm').empty(); // Kosongkan tabel rawat jalan
                $('#refreshNoRMButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Silakan masukkan nomor rekam medis</h2>
                    </span>
                `;
                $('#rawatjalan-no_rm').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                $('#total_didaftarkan').text('0');
                $('#total_dibatalkan').text('0');
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan nomor rekam medis
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlistrm') ?>`, {
                params: {
                    no_rm: no_rm,
                    limit: limit,
                    offset: offset
                }
            });
            const data = response.data.data; // Mendapatkan data rawat jalan
            const total = response.data.total; // Mendapatkan total rawat jalan
            const didaftarkan = response.data.didaftarkan; // Mendapatkan total rawat jalan didaftarkan
            const dibatalkan = response.data.dibatalkan; // Mendapatkan total rawat jalan dibatalkan
            const promises = [];

            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel rawat jalan
            $('#refreshNoRMButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(total.toLocaleString('id-ID')); // Jumlah data
            $('#total_didaftarkan').text(didaftarkan.toLocaleString('id-ID')); // Jumlah didaftarkan
            $('#total_dibatalkan').text(dibatalkan.toLocaleString('id-ID')); // Jumlah dibatalkan

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                $('#paginationNav-no_rm ul').empty();
                // Tampilkan pesan jika tidak ada data
                const emptyRow = !regex.test(no_rm) ?
                    `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Silakan masukkan nomor rekam medis dengan benar</h2>
                    </span>
                ` :
                    `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Pasien dengan nomor rekam medis ${no_rm} belum pernah berobat</h2>
                    </span>
                `;
                $('#rawatjalan-no_rm').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap rawatjalan ke tabel
            data.forEach(function(rawatjalan, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = rawatjalan.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i><span class="ms-1">LAKI-LAKI</span></span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: Pink"><i class="fa-solid fa-venus"></i><span class="ms-1">PEREMPUAN</span></span>`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = rawatjalan.telpon ? rawatjalan.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                let status = rawatjalan.status;
                let transaksi = rawatjalan.transaksi;
                if (status === 'DAFTAR' && transaksi === '0') {
                    status = `<span class="badge text-bg-primary bg-gradient h-100 d-flex align-items-center me-1">Didaftarkan</span>`;
                } else if (status === 'DAFTAR' && transaksi === '1') {
                    status = `<span class="badge text-bg-success bg-gradient h-100 d-flex align-items-center me-1">Sudah Dilayani</span>`;
                } else if (status === 'BATAL') {
                    status = `<span class="badge text-bg-danger bg-gradient h-100 d-flex align-items-center">Dibatalkan</span>`;
                }

                // Membuat elemen baris untuk setiap rawatjalan
                const rawatJalanElement = `
                <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" data-id="${rawatjalan.id_rawat_jalan}">
                    <div class="px-3 py-2">
                        <div class="text-truncate">
                            <h5 class="d-flex date justify-content-start mb-0 text-truncate">
                                <span class="badge bg-body text-body border px-2 align-self-start" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rawatjalan.number}</span>
                                <span class="ms-1 align-self-center text-truncate">${rawatjalan.nama_pasien}</span>
                            </h5>
                            <h6 class="mb-1 text-truncate">${rawatjalan.dokter}</h6>
                            <div class="d-flex align-items-stretch date mb-1" style="height: 1.25em;">
                                <span class="badge bg-body text-body border text-nowrap h-100 d-flex align-items-center me-1">${rawatjalan.nomor_registrasi}</span> ${jenis_kelamin}
                            </div>
                            <div class="d-flex align-items-stretch date" style="height: 1.25em;">
                                <span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.kode_antrian}${rawatjalan.no_antrian}</span><span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.status_kunjungan}</span>
                                ${status}
                            </div>
                        </div>
                    </div>
                </button>
                `;
                const promise = new Promise((resolve) => {
                    $('#rawatjalan-no_rm').append(rawatJalanElement);
                    resolve(); // Menandakan bahwa penambahan elemen telah selesai
                });
                promises.push(promise); // Menambahkan promise ke dalam array promises
            });
            // Menunggu semua promise selesai
            await Promise.all(promises);

            // Pagination logic with ellipsis for more than 3 pages
            const totalPages = Math.ceil(total / limit);
            $('#paginationNav-no_rm ul').empty();

            if (currentPage > 1) {
                $('#paginationNav-no_rm ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
            }

            if (totalPages > 5) {
                $('#paginationNav-no_rm ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                if (currentPage > 3) {
                    $('#paginationNav-no_rm ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                    $('#paginationNav-no_rm ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (currentPage < totalPages - 2) {
                    $('#paginationNav-no_rm ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                $('#paginationNav-no_rm ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
            } else {
                // Show all pages if total pages are 3 or fewer
                for (let i = 1; i <= totalPages; i++) {
                    $('#paginationNav-no_rm ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
            }

            if (currentPage < totalPages) {
                $('#paginationNav-no_rm ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
            }
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <span class="list-group-item pb-3 pt-3">
                    <h2 class="text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </span>
            `;
            $('#paginationNav-no_rm ul').empty();
            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-no_rm').append(errorRow); // Menambahkan baris error ke tabel
        }
    }

    async function fetchRajalNama() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai nama dari input
            const nama = $('#nama').val();
            const offset = (currentPage - 1) * limit;

            // Cek apakah nama diinput
            if (!nama) {
                $('#paginationNav-nama ul').empty();
                $('#rawatjalan-nama').empty(); // Kosongkan tabel rawat jalan
                $('#refreshNamaButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Silakan masukkan nama pasien</h2>
                    </span>
                `;
                $('#rawatjalan-nama').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                $('#total_didaftarkan').text('0');
                $('#total_dibatalkan').text('0');
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan nama
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlistnama') ?>`, {
                params: {
                    nama: nama,
                    limit: limit,
                    offset: offset
                }
            });
            const data = response.data.data; // Mendapatkan data rawat jalan
            const total = response.data.total; // Mendapatkan total rawat jalan
            const didaftarkan = response.data.didaftarkan; // Mendapatkan total rawat jalan didaftarkan
            const dibatalkan = response.data.dibatalkan; // Mendapatkan total rawat jalan dibatalkan
            const promises = [];

            $('#rawatjalan-nama').empty(); // Kosongkan tabel rawat jalan
            $('#refreshNamaButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(total.toLocaleString('id-ID')); // Jumlah data
            $('#total_didaftarkan').text(didaftarkan.toLocaleString('id-ID')); // Jumlah didaftarkan
            $('#total_dibatalkan').text(dibatalkan.toLocaleString('id-ID')); // Jumlah dibatalkan

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                $('#paginationNav-nama ul').empty();
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <span class="list-group-item pb-3 pt-3">
                        <h2 class="text-muted mb-0" style="font-weight: 300;">Tidak ada pasien dengan nama "${nama}"</h2>
                    </span>
                `;
                $('#rawatjalan-nama').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap rawatjalan ke tabel
            data.forEach(function(rawatjalan, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = rawatjalan.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i><span class="ms-1">LAKI-LAKI</span></span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap h-100 d-flex align-items-center" style="background-color: Pink"><i class="fa-solid fa-venus"></i><span class="ms-1">PEREMPUAN</span></span>`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = rawatjalan.telpon ? rawatjalan.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                let status = rawatjalan.status;
                let transaksi = rawatjalan.transaksi;
                if (status === 'DAFTAR' && transaksi === '0') {
                    status = `<span class="badge text-bg-primary bg-gradient h-100 d-flex align-items-center me-1">Didaftarkan</span>`;
                } else if (status === 'DAFTAR' && transaksi === '1') {
                    status = `<span class="badge text-bg-success bg-gradient h-100 d-flex align-items-center me-1">Sudah Dilayani</span>`;
                } else if (status === 'BATAL') {
                    status = `<span class="badge text-bg-danger bg-gradient h-100 d-flex align-items-center">Dibatalkan</span>`;
                }

                // Membuat elemen baris untuk setiap rawatjalan
                const rawatJalanElement = `
                <button type="button" class="list-group-item p-1 list-group-item-action detail-rajal" data-id="${rawatjalan.id_rawat_jalan}">
                    <div class="px-3 py-2">
                        <div class="text-truncate">
                            <h5 class="d-flex date justify-content-start mb-0 text-truncate">
                                <span class="badge bg-body text-body border px-2 align-self-start" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${rawatjalan.number}</span>
                                <span class="ms-1 align-self-center text-truncate">${rawatjalan.nama_pasien}</span>
                            </h5>
                            <h6 class="mb-1 text-truncate">${rawatjalan.dokter}</h6>
                            <div class="d-flex align-items-stretch date mb-1" style="height: 1.25em;">
                                <span class="badge bg-body text-body border text-nowrap h-100 d-flex align-items-center me-1">${rawatjalan.nomor_registrasi}</span> ${jenis_kelamin}
                            </div>
                            <div class="d-flex align-items-stretch date" style="height: 1.25em;">
                                <span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.kode_antrian}${rawatjalan.no_antrian}</span><span class="badge bg-body text-body border date h-100 d-flex align-items-center me-1">${rawatjalan.status_kunjungan}</span>
                                ${status}
                            </div>
                        </div>
                    </div>
                </button>
                `;
                const promise = new Promise((resolve) => {
                    $('#rawatjalan-nama').append(rawatJalanElement);
                    resolve(); // Menandakan bahwa penambahan elemen telah selesai
                });
                promises.push(promise); // Menambahkan promise ke dalam array promises
            });
            // Menunggu semua promise selesai
            await Promise.all(promises);

            // Pagination logic with ellipsis for more than 3 pages
            const totalPages = Math.ceil(total / limit);
            $('#paginationNav-nama ul').empty();

            if (currentPage > 1) {
                $('#paginationNav-nama ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage - 1}">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                `);
            }

            if (totalPages > 5) {
                $('#paginationNav-nama ul').append(`
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="1">1</a>
                    </li>
                `);

                if (currentPage > 3) {
                    $('#paginationNav-nama ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                    $('#paginationNav-nama ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (currentPage < totalPages - 2) {
                    $('#paginationNav-nama ul').append('<li class="page-item disabled"><span class="page-link bg-gradient">…</span></li>');
                }

                $('#paginationNav-nama ul').append(`
                    <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link bg-gradient date" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
            } else {
                // Show all pages if total pages are 3 or fewer
                for (let i = 1; i <= totalPages; i++) {
                    $('#paginationNav-nama ul').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link bg-gradient date" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
            }

            if (currentPage < totalPages) {
                $('#paginationNav-nama ul').append(`
                    <li class="page-item">
                        <a class="page-link bg-gradient date" href="#" data-page="${currentPage + 1}">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                `);
            }
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <span class="list-group-item pb-3 pt-3">
                    <h2 class="text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </span>
            `;
            $('#paginationNav-nama ul').empty();
            $('#rawatjalan-nama').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-nama').append(errorRow); // Menambahkan baris error ke tabel
        }
    }

    $('#tanggal-container-tab').on('click', async function() {
        $('#tanggal_form').show();
        $('#no-rm_form').hide();
        $('#nama_form').hide();
        $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
        await fetchRajalTanggal();
        $('#loadingSpinner').hide();
    });

    $('#no_rm-container-tab').on('click', async function() {
        $('#tanggal_form').hide();
        $('#no-rm_form').show();
        $('#nama_form').hide();
        $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
        await fetchRajalNoRM();
        $('#loadingSpinner').hide();
    });

    $('#nama-container-tab').on('click', async function() {
        $('#tanggal_form').hide();
        $('#no-rm_form').hide();
        $('#nama_form').show();
        $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
        await fetchRajalNama();
        $('#loadingSpinner').hide();
    });

    $(document).on('click', '#paginationNav-tanggal a', async function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            await fetchRajalTanggal();
            $('#loadingSpinner').hide();
        }
    });

    $(document).on('click', '#paginationNav-no_rm a', async function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            await fetchRajalNoRM();
            $('#loadingSpinner').hide();
        }
    });

    $(document).on('click', '#paginationNav-nama a', async function(event) {
        event.preventDefault(); // Prevents default behavior (scrolling)
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            await fetchRajalNama();
            $('#loadingSpinner').hide();
        }
    });

    // Event listener ketika tanggal diubah
    $('#tanggal').on('change', async function() {
        $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
        await fetchRajalTanggal(); // Memanggil fungsi untuk mengambil data pasien
        $('#loadingSpinner').hide();
    });

    $('#no_rm').on('input', async function() {
        if ($(this).val() === '') {
            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-no_rm').append(loading); // Menampilkan loading indicator
            await fetchRajalNoRM();
            $('#loadingSpinner').hide();
        }
    });

    $('#no-rm_form_content').submit(async function(ə) {
        ə.preventDefault();
        $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-no_rm').append(loading); // Menampilkan loading indicator
        await fetchRajalNoRM();
        $('#loadingSpinner').hide();
    });

    $('#nama').on('input', async function() {
        if ($(this).val() === '') {
            $('#rawatjalan-nama').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-nama').append(loading); // Menampilkan loading indicator
            await fetchRajalNama(); // Memanggil fungsi untuk mengambil data pasien
            $('#loadingSpinner').hide();
        }
    });

    $('#nama_form_content').submit(async function(ə) {
        ə.preventDefault();
        $('#rawatjalan-nama').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-nama').append(loading); // Menampilkan loading indicator
        await fetchRajalNama();
        $('#loadingSpinner').hide();
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
                await Promise.all([
                    fetchRajalTanggal(),
                    fetchRajalNoRM(),
                    fetchRajalNama()
                ]);
                $('#loadingSpinner').hide();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('#copy_identitas_pasien').on('click', function() {
            var textToCopy = $('#copy_identitas_pasien_value').text().trim();;

            if (navigator.clipboard) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    $('#copy_identitas_pasien')
                        .removeClass('link-primary')
                        .addClass('link-success')
                        .html(`<i class="fa-solid fa-check"></i>`);

                    setTimeout(function() {
                        $('#copy_identitas_pasien')
                            .addClass('link-primary')
                            .removeClass('link-success')
                            .html(`<i class="fa-solid fa-copy"></i>`);
                    }, 1000);
                }).catch(function(err) {
                    $('#copy_identitas_pasien')
                        .removeClass('link-primary')
                        .addClass('link-danger')
                        .html(`<i class="fa-solid fa-xmark"></i>`);

                    setTimeout(function() {
                        $('#copy_identitas_pasien')
                            .addClass('link-primary')
                            .removeClass('link-danger')
                            .html(`<i class="fa-solid fa-copy"></i>`);
                    }, 1000);

                    console.error('Gagal menyalin teks:', err);
                });
            } else {
                // Toast fallback jika tidak didukung (jika kamu punya fungsi seperti ini)
                showFailedToast('Clipboard API tidak didukung di peramban ini.');
            }
        });

        $(document).on('click', '.detail-rajal', async function(ə) {
            ə.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $('#loadingSpinner').show();
            $this.prop('disabled', true);

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/rawatjalan') ?>/` + id);
                const rawatjalan = response.data;

                $('#rajalModalLabel').text('Informasi Rawat Jalan');

                // Mengkondisikan jenis kelamin
                let jenis_kelamin_string = rawatjalan.jenis_kelamin;
                if (jenis_kelamin_string === 'L') {
                    jenis_kelamin_string = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="Laki-Laki">`;
                } else if (jenis_kelamin_string === 'P') {
                    jenis_kelamin_string = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="Perempuan">`;
                }

                const tempat_tanggal_lahir = (rawatjalan.tempat_lahir && rawatjalan.tanggal_lahir) ?
                    `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.tempat_lahir}, ${rawatjalan.tanggal_lahir}">` :
                    `<em>Tidak ada</em>`;

                // Gunakan pesan jika tidak ada nomor telepon
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                const telpon = rawatjalan.telpon ? `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.telpon}">` : `<em>Tidak ada</em>`;

                let pembatal = rawatjalan.status;
                if (pembatal === 'BATAL') {
                    $('.rajal_batal').show();
                } else if (pembatal === 'DAFTAR') {
                    $('.rajal_batal').hide();
                }
                let tombol_isian_ok = rawatjalan.ruangan;
                if (tombol_isian_ok === 'Kamar Operasi') {
                    tombol_isian_ok = `
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient print-lio" data-id="${rawatjalan.id_rawat_jalan}">
                                                    <i class="fa-solid fa-receipt"></i> Lembar Isian Operasi
                                                </button>
                                            <?php endif; ?>
                    `;
                } else {
                    tombol_isian_ok = ``;
                }
                let isian_ok = rawatjalan.ruangan;
                const tindakan_operasi_rajal = rawatjalan.tindakan_operasi_rajal ?
                    `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.tindakan_operasi_rajal}">` :
                    `<em>Belum diisi</em>`;
                let waktu_operasi_rajal = `<em>Belum diisi</em>`;
                if (rawatjalan.tanggal_operasi_rajal) {
                    waktu_operasi_rajal = `<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.tanggal_operasi_rajal}`;
                    if (rawatjalan.jam_operasi_rajal) {
                        waktu_operasi_rajal += ` ${rawatjalan.jam_operasi_rajal}`;
                    }
                    waktu_operasi_rajal += `">`;
                }
                if (isian_ok === 'Kamar Operasi') {
                    $('.pasien_operasi').show();
                } else {
                    $('.pasien_operasi').hide();
                }
                let tombol_rme = rawatjalan.status;
                if (tombol_rme === 'DAFTAR') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient print-struk" data-id="${rawatjalan.id_rawat_jalan}">
                                                    <i class="fa-solid fa-receipt"></i> Struk
                                                </button>
                                                ${tombol_isian_ok}
                                                <?php if (session()->get('role') != 'Admin') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/asesmen/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Asesmen
                                                </button>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/skrining/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Skrining
                                                </button>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/edukasi/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Edukasi
                                                </button>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/penunjang/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Penunjang
                                                </button>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/optik/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Resep Kacamata
                                                </button>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/laporanrajal/export') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Tindakan Rajal
                                                </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/asesmen') ?>/${rawatjalan.id_rawat_jalan}';">
                                                    <i class="fa-solid fa-user-check"></i> Asesmen
                                                </button>
                                                <?php if (session()->get('role') != 'Dokter') : ?>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/skrining') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-user-check"></i> Skrining
                                                    </button>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/edukasi') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-user-graduate"></i> Edukasi
                                                    </button>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/penunjang') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-stethoscope"></i> Penunjang
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (session()->get('role') != 'Perawat') : ?>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/resepobat') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-prescription"></i> Resep Obat
                                                    </button>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/optik') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-glasses"></i> Resep Kacamata
                                                    </button>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/laporanrajal') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-file-medical"></i> Tindakan Rajal
                                                    </button>
                                                    <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('rawatjalan/layanan') ?>/${rawatjalan.id_rawat_jalan}';">
                                                        <i class="fa-solid fa-user-nurse"></i> Layanan
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                        `;
                } else if (tombol_rme === 'BATAL') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-receipt"></i> Struk
                                                </button>
                                            <?php endif; ?>
                                        </div>
                    `;
                }

                let status = rawatjalan.status;
                let transaksi = rawatjalan.transaksi;
                if (status === 'DAFTAR' && transaksi === '0') {
                    status = `Didaftarkan`;
                } else if (status === 'DAFTAR' && transaksi === '1') {
                    status = `Sudah Dilayani`;
                } else if (status === 'BATAL') {
                    status = `Dibatalkan`;
                }

                $('#copy_identitas_pasien_value').text(`${rawatjalan.nama_pasien} ${rawatjalan.no_rm} ${rawatjalan.tanggal_lahir}`);
                $('#nama_pasien').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.nama_pasien}">`);
                $('#no_rekam_medis').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.no_rm}">`);
                $('#jenis_kelamin').html(jenis_kelamin_string);
                $('#tempat_lahir').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.tempat_lahir}">`);
                $('#tanggal_lahir').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.tanggal_lahir}">`);
                $('#usia').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${usia.usia} tahun ${usia.bulan} bulan">`);
                $('#alamat').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.alamat}">`);
                $('#kewarganegaraan').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.kewarganegaraan}">`);
                $('#telpon').html(telpon);
                $('#identitas_btn').attr('data-id', rawatjalan.id_pasien);
                $('#barcode_btn').attr('data-id', rawatjalan.id_pasien);
                $('#detail_pasien_btn').attr('onclick', "window.location.href = '<?= base_url('pasien/detailpasien') ?>/" + rawatjalan.id_pasien + "'");
                $('#pendaftar').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.pendaftar}">`);
                $('#nomor_registrasi').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.nomor_registrasi}">`);
                $('#tanggal_registrasi').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1 date" value="${rawatjalan.tanggal_registrasi}">`);
                $('#status_kunjungan').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.status_kunjungan}">`);
                $('#jaminan').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.jaminan}">`);
                $('#ruangan').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.ruangan}">`);
                $('#dokter').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.dokter}">`);
                $('#keluhan').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.keluhan}">`);
                $('#tindakan_operasi_rajal').html(tindakan_operasi_rajal);
                $('#waktu_operasi_rajal').html(waktu_operasi_rajal);
                $('#status').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${status}">`);
                $('#pembatal').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.pembatal}">`);
                $('#alasan_batal').html(`<input type="text" readonly class="form-control-plaintext p-0 border border-0 lh-1" value="${rawatjalan.alasan_batal}">`);
                $('#tombol_rme').html(tombol_rme);
                $('#rajalModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#loadingSpinner').hide();
                $this.prop('disabled', false);
            }
        });
        $(document).on('click', '.print-identitas', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Identitas`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame_1');
            iframe.attr('src', `<?= base_url('pasien/identitas') ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url('pasien/identitas') ?>/${id}`);
                } finally {
                    $btn.prop('disabled', false).html(`<i class="fa-solid fa-print"></i> Identitas`);
                }
            });
        });
        $(document).on('click', '.print-barcode', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> <em>Barcode</em>`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame_2');
            iframe.attr('src', `<?= base_url('pasien/barcode') ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url('pasien/barcode') ?>/${id}`);
                } finally {
                    $btn.prop('disabled', false).html(`<i class="fa-solid fa-barcode"></i> <em>Barcode</em>`);
                }
            });
        });
        $(document).on('click', '.print-struk', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Struk`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame_2');
            iframe.attr('src', `<?= base_url('rawatjalan/struk') ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`), `<?= base_url('rawatjalan/struk') ?>/${id}`;
                } finally {
                    $btn.prop('disabled', false).html(`<i class="fa-solid fa-receipt"></i> Struk`);
                }
            });
        });
        $(document).on('click', '.print-lio', function() {
            const id = $(this).data('id');

            // Tampilkan loading di tombol cetak
            const $btn = $(this);
            $btn.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?> Lembar Isian Operasi`);

            // Muat PDF ke iframe
            var iframe = $('#print_frame_2');
            iframe.attr('src', `<?= base_url('rawatjalan/lembarisianoperasi') ?>/${id}`);

            // Saat iframe selesai memuat, jalankan print
            iframe.off('load').on('load', function() {
                try {
                    this.contentWindow.focus();
                    this.contentWindow.print();
                } catch (e) {
                    showFailedPrintToast(`<p>Pencetakan otomatis tidak dapat dilakukan</p><p class="mb-0">${e}</p>`, `<?= base_url('rawatjalan/lembarisianoperasi') ?>/${id}`);
                } finally {
                    $btn.prop('disabled', false).html(`<i class="fa-solid fa-receipt"></i> Lembar Isian Operasi`);
                }
            });
        });
        $('#rajalModal').on('hidden.bs.modal', function() {
            $('#nama_pasien').html('');
            $('#no_rekam_medis').html('');
            $('#jenis_kelamin').html('');
            $('#kelahiran').html('');
            $('#usia').html('');
            $('#alamat').html('');
            $('#telpon').html('');
            $('#identitas_btn').attr('data-id', ``);
            $('#barcode_btn').attr('data-id', ``);
            $('#detail_pasien_btn').attr('onclick', ``);
            $('#pendaftar').html('');
            $('#nomor_registrasi').html('');
            $('#tanggal_registrasi').html('');
            $('#status_kunjungan').html('');
            $('#jaminan').html('');
            $('#ruangan').html('');
            $('#dokter').html('');
            $('#keluhan').html('');
            $('#status_kunjungan').html('');
            $('#tindakan_operasi_rajal').html('');
            $('#waktu_operasi_rajal').html('');
            $('#status').html('');
            $('#pembatal').html('');
            $('#alasan_batal').html('');
            $('#tombol_rme').html('');
        });
        // Menangani event klik pada tombol bersihkan
        $('#setTodayTglButton').on('click', async function() {
            // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            $('#tanggal').val(formattedDate); // Setel tanggal ke hari ini
            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
            await fetchRajalTanggal(); // Memanggil fungsi untuk mengambil data pasien
            $('#loadingSpinner').hide();
        });
        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                await Promise.all([
                    fetchRajalTanggal(),
                    fetchRajalNoRM(),
                    fetchRajalNama()
                ]);
                $('#loadingSpinner').hide();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshTglButton').on('click', async function() {
            await fetchRajalTanggal(); // Panggil fungsi untuk mengambil data pasien
            $('#loadingSpinner').hide();
        });
        $('#refreshNoRMButton').on('click', async function() {
            await fetchRajalNoRM(); // Panggil fungsi untuk mengambil data pasien
            $('#loadingSpinner').hide();
        });
        $('#refreshNamaButton').on('click', async function() {
            await fetchRajalNama(); // Panggil fungsi untuk mengambil data pasien
            $('#loadingSpinner').hide();
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        await Promise.all([
            fetchRajalTanggal(),
            fetchRajalNoRM(),
            fetchRajalNama()
        ]);
        $('#loadingSpinner').hide();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>