<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><span id="total_rajal">0</span> pasien rawat jalan</div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
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
                        <div class="nav nav-underline nav-justified flex-nowrap overflow-auto" id="nav-tab" role="tablist">
                            <button class="nav-link py-1 active" id="tanggal-container-tab" data-bs-toggle="tab" data-bs-target="#tanggal-container" type="button" role="tab" aria-controls="tanggal-container" aria-selected="true">Tanggal</button>
                            <button class="nav-link py-1" id="no_rm-container-tab" data-bs-toggle="tab" data-bs-target="#no_rm-container" type="button" role="tab" aria-controls="no_rm-container" aria-selected="false">Nomor Rekam Medis</button>
                        </div>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur" id="tanggal_form">
                <div class="no-fluid-content">
                    <div class="input-group input-group-sm">
                        <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                        <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-success bg-gradient" type="button" id="refreshTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur" id="no-rm_form" style="display: none;">
                <form class="no-fluid-content" id="no-rm_form_content">
                    <div class="input-group input-group-sm">
                        <input type="text" id="no_rm" name="no_rm" class="form-control" placeholder="xx-xx-xx" autocomplete="off" dir="auto">
                        <button class="btn btn-danger bg-gradient" type="button" id="clearNoRMButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Pencarian"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="no_rm_submitBtn" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Kirim Pencarian"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <button class="btn btn-success bg-gradient" type="button" id="refreshNoRMButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </form>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane show active" id="tanggal-container" role="tabpanel" aria-labelledby="tanggal-container-tab" tabindex="0">
                    <div class="accordion mb-3" id="rawatjalan-tanggal">
                        <div class="accordion-item shadow-sm p-3 p-3">
                            <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Memuat data pasien rawat jalan...</h2>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="no_rm-container" role="tabpanel" aria-labelledby="no_rm-container-tab" tabindex="0">
                    <div class="accordion mb-3" id="rawatjalan-no_rm">
                        <div class="accordion-item shadow-sm p-3 p-3">
                            <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Memuat data pasien rawat jalan...</h2>
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
    // HTML untuk menunjukkan bahwa data pasien sedang dimuat
    const loading = `
        <div class="accordion-item shadow-sm p-3 p-3">
            <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Memuat data pasien rawat jalan...</h2>
        </div>
    `;

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

            // Cek apakah tanggal diinput
            if (!tanggal) {
                $('#rawatjalan-tanggal').empty(); // Kosongkan tabel rawat jalan
                $('#refreshTglButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Silakan masukkan tanggal</h2>
                    </div>
                `;
                $('#rawatjalan-tanggal').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlisttanggal') ?>/${tanggal}`);
            const data = response.data.data; // Mendapatkan data rawat jalan

            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel rawat jalan
            $('#refreshTglButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(data.length.toLocaleString('id-ID')); // Jumlah data

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Tidak ada pasien yang berobat pada ${tanggal}</h2>
                    </div>
                `;
                $('#rawatjalan-tanggal').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap rawatjalan ke tabel
            data.forEach(function(rawatjalan, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = rawatjalan.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                }
                let jenis_kelamin_string = rawatjalan.jenis_kelamin;
                if (jenis_kelamin_string === 'L') {
                    jenis_kelamin_string = `Laki-laki`;
                } else if (jenis_kelamin_string === 'P') {
                    jenis_kelamin_string = `Perempuan`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = rawatjalan.telpon ? rawatjalan.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                const transaksiBadge = rawatjalan.transaksi == '1' ?
                    `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                    `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;

                let status = rawatjalan.status;
                if (status === 'DAFTAR') {
                    status = `<span class="badge bg-success bg-gradient">Didaftarkan</span> ${transaksiBadge}`;
                } else if (status === 'BATAL') {
                    status = `<span class="badge bg-danger bg-gradient">Dibatalkan</span>`;
                }
                let pembatal = rawatjalan.status;
                if (pembatal === 'BATAL') {
                    pembatal = `
                            <div class="mb-0 row g-1">
                                <div class="col-5 fw-medium text-truncate">Dibatalkan oleh</div>
                                <div class="col date">
                                    ${rawatjalan.pembatal}
                                </div>
                            </div>
                            <div class="mb-0 row g-1">
                                <div class="col-5 fw-medium text-truncate">Alasan Pembatalan</div>
                                <div class="col date">
                                    ${rawatjalan.alasan_batal}
                                </div>
                            </div>
                        `;
                } else if (pembatal === 'DAFTAR') {
                    pembatal = ``;
                }
                let tombol_rme = rawatjalan.status;
                if (tombol_rme === 'DAFTAR') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Struk
                                                </button>
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
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                        `;
                } else if (tombol_rme === 'BATAL') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Struk
                                                </button>
                                            <?php endif; ?>
                                        </div>
                    `;
                }

                // Membuat elemen baris untuk setiap rawatjalan
                const rawatJalanElement = `
                <div class="accordion-item shadow-sm">
                    <div class="accordion-header">
                        <button class="accordion-button px-3 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index + 1}" aria-expanded="false" aria-controls="collapse-${index + 1}">
                            <div class="pe-3 text-truncate">
                                <h5 class="d-flex date justify-content-start mb-0 text-truncate">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${index + 1}</span>
                                    <span class="ms-1 align-self-center text-truncate">${rawatjalan.nama_pasien}</span>
                                </h5>
                                <h6 class="card-subtitle text-truncate">${rawatjalan.dokter}</h6>
                                <div class="card-text text-truncate"><small class="date">${rawatjalan.nomor_registrasi} ${jenis_kelamin}</small></div>
                                <div>
                                    <span class="badge bg-body text-body border date" style="font-weight: 900;">${rawatjalan.kode_antrian}${rawatjalan.no_antrian}</span> ${status}
                                </div>
                            </div>
                        </button>
                    </div>
                    <div id="collapse-${index + 1}" class="accordion-collapse collapse" data-bs-parent="#rawatjalan-tanggal">
                        <div class="accordion-body px-3 py-2">
                            <div class="row g-3">
                                <div class="col-lg-6 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Identitas Pasien</div>
                                        <div style="font-size: 0.75em;">
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nama</div>
                                                <div class="col">
                                                    ${rawatjalan.nama_pasien}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                                                <div class="col date">
                                                    ${rawatjalan.no_rm}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                                                <div class="col">
                                                    ${jenis_kelamin_string}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Tempat/Tanggal Lahir</div>
                                                <div class="col">
                                                    ${rawatjalan.tempat_lahir}, <span class="date text-nowrap">${rawatjalan.tanggal_lahir}</span>
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Usia</div>
                                                <div class="col date">
                                                    ${usia.usia} tahun ${usia.bulan} bulan
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Alamat</div>
                                                <div class="col">
                                                    ${rawatjalan.alamat}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                                                <div class="col date">
                                                    ${telpon}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('pasien/detailpasien') ?>/${rawatjalan.id_pasien}'">
                                                    <i class="fa-solid fa-user-injured"></i> Lihat Pasien
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Rawat Jalan</div>
                                        <div style="font-size: 0.75em;">
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                                                <div class="col date">
                                                    ${rawatjalan.nomor_registrasi}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                                <div class="col date">
                                                    ${rawatjalan.tanggal_registrasi}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jenis Kunjungan</div>
                                                <div class="col">
                                                    ${rawatjalan.jenis_kunjungan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Status Kunjungan</div>
                                                <div class="col">
                                                    ${rawatjalan.status_kunjungan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jaminan</div>
                                                <div class="col">
                                                    ${rawatjalan.jaminan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Ruangan</div>
                                                <div class="col">
                                                    ${rawatjalan.ruangan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Dokter</div>
                                                <div class="col">
                                                    ${rawatjalan.dokter}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Keluhan</div>
                                                <div class="col">
                                                    ${rawatjalan.keluhan}
                                                </div>
                                            </div>
                                            ${pembatal}
                                        </div>
                                    </div>
                                    <div>
                                        ${tombol_rme}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                $('#rawatjalan-tanggal').append(rawatJalanElement); // Menambahkan elemen pasien ke tabel
            });
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <div class="accordion-item shadow-sm p-3 p-3">
                    <h2 class="text-center text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </div>
            `;
            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-tanggal').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    async function fetchRajalNoRM() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai nomor rekam medis dari input
            const no_rm = $('#no_rm').val();

            // Cek apakah no_rm diinput
            if (!no_rm) {
                $('#rawatjalan-no_rm').empty(); // Kosongkan tabel rawat jalan
                $('#refreshNoRMButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Silakan masukkan nomor rekam medis</h2>
                    </div>
                `;
                $('#rawatjalan-no_rm').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan nomor rekam medis
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlistrm') ?>/${no_rm}`);
            const data = response.data.data; // Mendapatkan data rawat jalan

            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel rawat jalan
            $('#refreshNoRMButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(data.length.toLocaleString('id-ID')); // Jumlah data

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Pasien dengan nomor rekam medis ${no_rm} belum pernah berobat</h2>
                    </div>
                `;
                $('#rawatjalan-no_rm').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap rawatjalan ke tabel
            data.forEach(function(rawatjalan, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = rawatjalan.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                }
                let jenis_kelamin_string = rawatjalan.jenis_kelamin;
                if (jenis_kelamin_string === 'L') {
                    jenis_kelamin_string = `Laki-laki`;
                } else if (jenis_kelamin_string === 'P') {
                    jenis_kelamin_string = `Perempuan`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = rawatjalan.telpon ? rawatjalan.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(rawatjalan.tanggal_lahir, rawatjalan.tanggal_registrasi); // Menghitung usia pasien

                const transaksiBadge = rawatjalan.transaksi == '1' ?
                    `<span class="badge bg-success bg-gradient">Transaksi Diproses</span>` :
                    `<span class="badge bg-danger bg-gradient">Transaksi Belum Diproses</span>`;

                let status = rawatjalan.status;
                if (status === 'DAFTAR') {
                    status = `<span class="badge bg-success bg-gradient">Didaftarkan</span> ${transaksiBadge}`;
                } else if (status === 'BATAL') {
                    status = `<span class="badge bg-danger bg-gradient">Dibatalkan</span>`;
                }
                let pembatal = rawatjalan.status;
                if (pembatal === 'BATAL') {
                    pembatal = `
                            <div class="mb-0 row g-1">
                                <div class="col-5 fw-medium text-truncate">Dibatalkan oleh</div>
                                <div class="col date">
                                    ${rawatjalan.pembatal}
                                </div>
                            </div>
                            <div class="mb-0 row g-1">
                                <div class="col-5 fw-medium text-truncate">Alasan Pembatalan</div>
                                <div class="col date">
                                    ${rawatjalan.alasan_batal}
                                </div>
                            </div>
                        `;
                } else if (pembatal === 'DAFTAR') {
                    pembatal = ``;
                }
                let tombol_rme = rawatjalan.status;
                if (tombol_rme === 'DAFTAR') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Struk
                                                </button>
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
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                        `;
                } else if (tombol_rme === 'BATAL') {
                    tombol_rme = `
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                    <i class="fa-solid fa-print"></i> Struk
                                                </button>
                                            <?php endif; ?>
                                        </div>
                    `;
                }

                // Membuat elemen baris untuk setiap rawatjalan
                const rawatJalanElement = `
                <div class="accordion-item shadow-sm">
                    <div class="accordion-header">
                        <button class="accordion-button px-3 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index + 1}" aria-expanded="false" aria-controls="collapse-${index + 1}">
                            <div class="pe-3 text-truncate">
                                <h5 class="d-flex date justify-content-start mb-0 text-truncate">
                                    <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">${index + 1}</span>
                                    <span class="ms-1 align-self-center text-truncate">${rawatjalan.nama_pasien}</span>
                                </h5>
                                <h6 class="card-subtitle text-truncate">${rawatjalan.dokter}</h6>
                                <div class="card-text text-truncate"><small class="date">${rawatjalan.nomor_registrasi} ${jenis_kelamin}</small></div>
                                <div>
                                    <span class="badge bg-body text-body border date" style="font-weight: 900;">${rawatjalan.kode_antrian}${rawatjalan.no_antrian}</span> ${status}
                                </div>
                            </div>
                        </button>
                    </div>
                    <div id="collapse-${index + 1}" class="accordion-collapse collapse" data-bs-parent="#rawatjalan-no_rm">
                        <div class="accordion-body px-3 py-2">
                            <div class="row g-3">
                                <div class="col-lg-6 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Identitas Pasien</div>
                                        <div style="font-size: 0.75em;">
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nama</div>
                                                <div class="col">
                                                    ${rawatjalan.nama_pasien}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                                                <div class="col date">
                                                    ${rawatjalan.no_rm}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                                                <div class="col">
                                                    ${jenis_kelamin_string}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Tempat/Tanggal Lahir</div>
                                                <div class="col">
                                                    ${rawatjalan.tempat_lahir}, <span class="date text-nowrap">${rawatjalan.tanggal_lahir}</span>
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Usia</div>
                                                <div class="col date">
                                                    ${usia.usia} tahun ${usia.bulan} bulan
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Alamat</div>
                                                <div class="col">
                                                    ${rawatjalan.alamat}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                                                <div class="col date">
                                                    ${telpon}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-2">
                                            <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                                <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('pasien/detailpasien') ?>/${rawatjalan.id_pasien}'">
                                                    <i class="fa-solid fa-user-injured"></i> Lihat Pasien
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="fw-bold mb-2 border-bottom">Rawat Jalan</div>
                                        <div style="font-size: 0.75em;">
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                                                <div class="col date">
                                                    ${rawatjalan.nomor_registrasi}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                                                <div class="col date">
                                                    ${rawatjalan.tanggal_registrasi}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jenis Kunjungan</div>
                                                <div class="col">
                                                    ${rawatjalan.jenis_kunjungan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Status Kunjungan</div>
                                                <div class="col">
                                                    ${rawatjalan.status_kunjungan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Jaminan</div>
                                                <div class="col">
                                                    ${rawatjalan.jaminan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Ruangan</div>
                                                <div class="col">
                                                    ${rawatjalan.ruangan}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Dokter</div>
                                                <div class="col">
                                                    ${rawatjalan.dokter}
                                                </div>
                                            </div>
                                            <div class="mb-0 row g-1">
                                                <div class="col-5 fw-medium text-truncate">Keluhan</div>
                                                <div class="col">
                                                    ${rawatjalan.keluhan}
                                                </div>
                                            </div>
                                            ${pembatal}
                                        </div>
                                    </div>
                                    <div>
                                        ${tombol_rme}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                $('#rawatjalan-no_rm').append(rawatJalanElement); // Menambahkan elemen pasien ke tabel
            });
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <div class="accordion-item shadow-sm p-3 p-3">
                    <h2 class="text-center text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </div>
            `;
            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-no_rm').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    $('#tanggal-container-tab').on('click', function() {
        $('#tanggal_form').show();
        $('#no-rm_form').hide();
        fetchRajalTanggal();
    });

    $('#no_rm-container-tab').on('click', function() {
        $('#no-rm_form').show();
        $('#tanggal_form').hide();
        fetchRajalNoRM();
    });

    // Event listener ketika tanggal diubah
    $('#tanggal').on('change', function() {
        $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
        fetchRajalTanggal(); // Memanggil fungsi untuk mengambil data pasien
    });

    $('#no_rm').on('input', function() {
        if ($(this).val() === '') {
            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-no_rm').append(loading); // Menampilkan loading indicator
            fetchRajalNoRM();
        }
    });

    $('#no-rm_form_content').submit(function(ə) {
        ə.preventDefault();
        $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
        $('#rawatjalan-no_rm').append(loading); // Menampilkan loading indicator
        fetchRajalNoRM();
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol bersihkan
        $('#clearTglButton').on('click', function() {
            $('#tanggal').val(''); // Kosongkan tanggal
            $('#rawatjalan-tanggal').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-tanggal').append(loading); // Menampilkan loading indicator
            fetchRajalTanggal(); // Memanggil fungsi untuk mengambil data pasien
        });
        $('#clearNoRMButton').on('click', function() {
            $('#no_rm').val(''); // Kosongkan nomor rekam medis
            $('#rawatjalan-no_rm').empty(); // Kosongkan tabel pasien
            $('#rawatjalan-no_rm').append(loading); // Menampilkan loading indicator
            fetchRajalNoRM(); // Memanggil fungsi untuk mengambil data pasien
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchRajalTanggal();
                fetchRajalNoRM();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshTglButton').on('click', function() {
            fetchRajalTanggal(); // Panggil fungsi untuk mengambil data pasien
        });
        $('#refreshNoRMButton').on('click', function() {
            fetchRajalNoRM(); // Panggil fungsi untuk mengambil data pasien
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        fetchRajalTanggal();
        fetchRajalNoRM();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>