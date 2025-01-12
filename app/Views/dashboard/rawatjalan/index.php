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
                    <div class="input-group input-group-sm">
                        <input type="date" id="tanggal" name="tanggal" class="form-control ">
                        <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-success bg-gradient" type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="accordion mb-3" id="rawatjalan">
                <div class="accordion-item shadow-sm p-3 p-3">
                    <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Memuat data pasien rawat jalan...</h2>
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
    async function fetchRajal() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();

            // Cek apakah tanggal diinput
            if (!tanggal) {
                $('#rawatjalan').empty(); // Kosongkan tabel rawat jalan
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Silakan masukkan tanggal</h2>
                    </div>
                `;
                $('#rawatjalan').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text('0'); // Kosongkan total
                return; // Keluar dari fungsi
            }

            // Mengambil data rawat jalan berdasarkan tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/rawatjalanlist') ?>/${tanggal}`);
            const data = response.data.data; // Mendapatkan data rawat jalan

            $('#rawatjalan').empty(); // Kosongkan tabel rawat jalan
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(data.length.toLocaleString('id-ID')); // Jumlah data

            // Cek apakah data rawat jalan kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Tidak ada pasien yang berobat pada ${tanggal}</h2>
                    </div>
                `;
                $('#rawatjalan').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Mengurutkan data rawatjalan berdasarkan nomor registrasi
            data.sort((a, b) => a.nomor_registrasi.localeCompare(b.nomor_registrasi, 'en', {
                numeric: true
            }));

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
                    <div id="collapse-${index + 1}" class="accordion-collapse collapse" data-bs-parent="#rawatjalan">
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
                                    <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                        <div class="d-grid gap-2 d-flex justify-content-end mt-2">
                                            <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.location.href = '<?= base_url('pasien/detailpasien') ?>/${rawatjalan.id_pasien}'">
                                                <i class="fa-solid fa-user-injured"></i> Lihat Pasien
                                            </button>
                                        </div>
                                    <?php endif; ?>
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
                                    <?php if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') : ?>
                                        <div class="d-grid gap-2 d-flex justify-content-end mt-2">
                                            <button type="button" class="btn btn-body btn-sm bg-gradient" onclick="window.open('<?= base_url('rawatjalan/struk') ?>/${rawatjalan.id_rawat_jalan}');">
                                                <i class="fa-solid fa-print"></i> Struk
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                $('#rawatjalan').append(rawatJalanElement); // Menambahkan elemen pasien ke tabel
            });
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <div class="accordion-item shadow-sm p-3 p-3">
                    <h2 class="text-center text-danger mb-0" style="font-weight: 300;">${error}</h2>
                </div>
            `;
            $('#rawatjalan').empty(); // Kosongkan tabel pasien
            $('#rawatjalan').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    // Event listener ketika tanggal diubah
    $('#tanggal').on('change', function() {
        $('#rawatjalan').empty(); // Kosongkan tabel pasien
        $('#rawatjalan').append(loading); // Menampilkan loading indicator
        fetchRajal(); // Memanggil fungsi untuk mengambil data pasien
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol bersihkan
        $('#clearTglButton').on('click', function() {
            $('#tanggal').val(''); // Kosongkan tanggal
            $('#rawatjalan').empty(); // Kosongkan tabel pasien
            $('#rawatjalan').append(loading); // Menampilkan loading indicator
            fetchRajal(); // Memanggil fungsi untuk mengambil data pasien
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchRajal();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            fetchRajal(); // Panggil fungsi untuk mengambil data pasien
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        fetchRajal();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>