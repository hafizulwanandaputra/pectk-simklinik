<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4">
    <div class="d-xxl-flex justify-content-center">
        <div class="no-fluid-content">
            <div class="sticky-top" style="z-index: 99;">
                <ul class="list-group shadow-sm rounded-top-0 rounded-bottom-3 mb-2">
                    <li class="list-group-item border-top-0 bg-body-tertiary">
                        <div class="input-group input-group-sm">
                            <input type="date" id="tanggal" name="tanggal" class="form-control rounded-start-3">
                            <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                            <button class="btn btn-success bg-gradient rounded-end-3" type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="alert alert-info rounded-3" role="alert">
                <div class="d-flex align-items-start">
                    <div style="width: 12px; text-align: center;">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="w-100 ms-3">
                        Data-data pasien rawat jalan ini diperoleh dari <a href="https://pectk.padangeyecenter.com/klinik" class="alert-link" target="_blank">Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan</a> melalui <em>Application Programming Interface</em> (API)
                    </div>
                </div>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%;">Nama</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jenis Kelamin</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Rekam Medis</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Registrasi</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%;">Tempat dan Tanggal Lahir</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Telepon</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%;">Alamat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 15%;">Dokter</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="datapasien">
                        <tr>
                            <td colspan="9" class="text-center" style="cursor: wait;">Memuat data pasien rawat jalan...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    // HTML untuk menunjukkan bahwa data pasien sedang dimuat
    const loading = `
        <tr>
            <td colspan="9" class="text-center" style="cursor: wait;">Memuat data pasien rawat jalan...</td>
        </tr>
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
                    <tr>
                        <td colspan="9" class="text-center">Silakan masukkan tanggal</td>
                    </tr>
                `;
                $('#datapasien').append(emptyRow); // Menambahkan baris kosong ke tabel
                return; // Keluar dari fungsi
            }

            // Mengambil data pasien dari API berdasarkan tanggal
            const response = await axios.get(`<?= base_url('pasien/pasienapi') ?>?tanggal=${tanggal}`);
            const data = response.data.data; // Mendapatkan data pasien

            $('#datapasien').empty(); // Kosongkan tabel pasien
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh

            // Cek apakah data pasien kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada pasien yang berobat pada ${tanggal}</td>
                    </tr>
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
                const jenis_kelamin = pasien.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan";
                // Gunakan pesan jika tidak ada nomor telepon
                const telpon = pasien.telpon ? pasien.telpon : "<em>Tidak ada</em>";
                const usia = hitungUsia(pasien.tanggal_lahir, pasien.tanggal_registrasi); // Menghitung usia pasien

                // Membuat elemen baris untuk setiap pasien
                const pasienElement = `
                    <tr>
                        <td class="date text-nowrap text-center">${index + 1}</td>
                        <td>${pasien.nama_pasien}</td>
                        <td class="text-nowrap">${jenis_kelamin}</td>
                        <td class="date text-nowrap">${pasien.no_rm}</td>
                        <td class="date text-nowrap">${pasien.nomor_registrasi}</td>
                        <td>${pasien.tempat_lahir}<br><small class="date text-nowrap">${pasien.tanggal_lahir} • ${usia.usia} tahun ${usia.bulan} bulan</small></td>
                        <td class="date text-nowrap">${telpon}</td>
                        <td>${pasien.alamat}</td>
                        <td>${pasien.dokter}</td>
                    </tr>
                `;
                $('#datapasien').append(pasienElement); // Menambahkan elemen pasien ke tabel
            });
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error.response.data.error); // Menampilkan error di konsol
            const errorRow = `
                <tr>
                    <td colspan="9" class="text-center">${error.response.data.error}</td>
                </tr>
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