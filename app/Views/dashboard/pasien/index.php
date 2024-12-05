<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?> <span id="total_rajal" class="date"></span></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm me-3" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <a class="fs-5 text-success-emphasis dropdown-toggle no-caret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-circle-question"></i></a>
    <ul class="dropdown-menu dropdown-menu-end bg-body-tertiary shadow-sm transparent-blur">
        <li>
            <h6 class="dropdown-header text-wrap" style="max-width: 600px;">Data-data pasien rawat jalan ini diperoleh dari <em>Application Programming Interface</em> (API) Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan</h6>
        </li>
        <li>
            <a class="dropdown-item px-2 py-1" href="https://pectk.padangeyecenter.com/klinik" target="_blank">
                <div class="d-flex align-items-start">
                    <span style="min-width: 32px; max-width: 32px; text-align: center;"><i class="fa-solid fa-up-right-from-square"></i></span>
                    <span>Buka SIM Klinik</span>
                </div>
            </a>
        </li>
    </ul>
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
            <div class="accordion mb-3" id="datapasien">
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

    // Fungsi untuk mengambil data pasien dari API
    async function fetchPasien() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();

            // Cek apakah tanggal diinput
            if (!tanggal) {
                $('#datapasien').empty(); // Kosongkan tabel pasien
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Silakan masukkan tanggal</h2>
                    </div>
                `;
                $('#datapasien').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_rajal').text(''); // Kosongkan total
                return; // Keluar dari fungsi
            }

            // Mengambil data pasien dari API berdasarkan tanggal
            const response = await axios.get(`<?= base_url('pasien/pasienapi') ?>?tanggal=${tanggal}`);
            const data = response.data.data; // Mendapatkan data pasien

            $('#datapasien').empty(); // Kosongkan tabel pasien
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#total_rajal').text(`(${data.length})`); // Jumlah data

            // Cek apakah data pasien kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <div class="accordion-item shadow-sm p-3 p-3">
                        <h2 class="text-center text-muted mb-0" style="font-weight: 300;">Tidak ada pasien yang berobat pada ${tanggal}</h2>
                    </div>
                `;
                $('#datapasien').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Mengurutkan data pasien berdasarkan nomor registrasi
            data.sort((a, b) => a.nomor_registrasi.localeCompare(b.nomor_registrasi, 'en', {
                numeric: true
            }));

            // Menambahkan setiap pasien ke tabel
            data.forEach(function(pasien, index) {
                // Mengkondisikan jenis kelamin
                let jenis_kelamin = pasien.jenis_kelamin;
                if (jenis_kelamin === 'L') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: SkyBlue"><i class="fa-solid fa-mars"></i> LAKI-LAKI</span>`;
                } else if (jenis_kelamin === 'P') {
                    jenis_kelamin = `<span class="badge text-black bg-gradient text-nowrap" style="background-color: Pink"><i class="fa-solid fa-venus"></i> PEREMPUAN</span>`;
                }
                let jenis_kelamin_string = pasien.jenis_kelamin;
                if (jenis_kelamin_string === 'L') {
                    jenis_kelamin_string = `Laki-laki`;
                } else if (jenis_kelamin_string === 'P') {
                    jenis_kelamin_string = `Perempuan`;
                }
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = pasien.telpon ? pasien.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(pasien.tanggal_lahir, pasien.tanggal_registrasi); // Menghitung usia pasien

                // Membuat elemen baris untuk setiap pasien
                const pasienElement = `
                <div class="accordion-item shadow-sm">
                    <div class="accordion-header">
                        <button class="accordion-button px-3 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index + 1}" aria-expanded="false" aria-controls="collapse-${index + 1}">
                            <div class="pe-3">
                                <h5 class="card-title">[<span class="date" style="font-weight: 900;">${index + 1}</span>] ${pasien.nama_pasien}</h5>
                                <h6 class="card-subtitle">${pasien.dokter}</h6>
                                <p class="card-text"><small class="date">${pasien.nomor_registrasi} ${jenis_kelamin}</small></p>
                            </div>
                        </button>
                    </div>
                    <div id="collapse-${index + 1}" class="accordion-collapse collapse" data-bs-parent="#datapasien">
                        <div class="accordion-body px-3 py-2">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="fw-bold mb-2 border-bottom">Identitas Pasien</div>
                                    <div style="font-size: 0.75em;">
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Nama</div>
                                            <div class="col">
                                                ${pasien.nama_pasien}
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Nomor RM</div>
                                            <div class="col date">
                                                ${pasien.no_rm}
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Jenis Kelamin</div>
                                            <div class="col">
                                                ${jenis_kelamin_string}
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Tempat/Tanggal Lahir</div>
                                            <div class="col">
                                                ${pasien.tempat_lahir}, <span class="date text-nowrap">${pasien.tanggal_lahir}</span>
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Usia</div>
                                            <div class="col date">
                                                ${usia.usia} tahun ${usia.bulan} bulan
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Alamat</div>
                                            <div class="col">
                                                ${pasien.alamat}
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Nomor Telepon</div>
                                            <div class="col date">
                                                ${telpon}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="fw-bold mb-2 border-bottom">Rawat Jalan</div>
                                    <div style="font-size: 0.75em;">
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Nomor Registrasi</div>
                                            <div class="col date">
                                                ${pasien.nomor_registrasi}
                                            </div>
                                        </div>
                                        <div class="mb-1 row">
                                            <div class="col-5 col-lg-4 fw-medium">Dokter</div>
                                            <div class="col">
                                                ${pasien.dokter}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                $('#datapasien').append(pasienElement); // Menambahkan elemen pasien ke tabel
            });
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error.response.data.details); // Menampilkan error di konsol
            const errorRow = `
                <div class="accordion-item shadow-sm p-3 p-3">
                    <h2 class="text-center text-danger mb-0" style="font-weight: 300;">${error.response.data.error}<br>${error.response.data.details.message}</h2>
                </div>
            `;
            $('#datapasien').empty(); // Kosongkan tabel pasien
            $('#datapasien').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    // Event listener ketika tanggal diubah
    $('#tanggal').on('change', function() {
        $('#datapasien').empty(); // Kosongkan tabel pasien
        $('#datapasien').append(loading); // Menampilkan loading indicator
        fetchPasien(); // Memanggil fungsi untuk mengambil data pasien
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol bersihkan
        $('#clearTglButton').on('click', function() {
            $('#tanggal').val(''); // Kosongkan tanggal
            $('#datapasien').empty(); // Kosongkan tabel pasien
            $('#datapasien').append(loading); // Menampilkan loading indicator
            fetchPasien(); // Memanggil fungsi untuk mengambil data pasien
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            fetchPasien(); // Panggil fungsi untuk mengambil data pasien
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        fetchPasien();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>