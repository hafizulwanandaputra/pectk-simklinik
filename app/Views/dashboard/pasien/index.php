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
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <div class="d-lg-flex justify-content-center">
        <div class="no-fluid-content">
            <div class="mb-2">
                <fieldset class="border rounded-3 px-2 py-0 mb-3" id="tambahPasienForm">
                    <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Masukkan Tanggal</legend>
                    <div class="mb-2 input-group">
                        <input type="date" id="tanggal" name="tanggal" class="form-control rounded-start-3">
                        <button class="btn btn-danger bg-gradient" type="button" id="clearTglButton"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-success bg-gradient rounded-end-3" type="button" id="refreshButton" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </fieldset>
                <div class="table-responsive">
                    <table class="table table-sm" style="width:100%; font-size: 9pt;">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No</th>
                                <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 25%;">Nama</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jenis Kelamin</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Rekam Medis</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Registrasi</th>
                                <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 25%;">Tempat dan Tanggal Lahir</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Telepon</th>
                                <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 25%;">Alamat</th>
                                <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 15%;">Dokter</th>
                            </tr>
                        </thead>
                        <tbody class="align-top" id="datapasien">
                            <tr>
                                <td colspan="9" class="text-center">Memuat data pasien rawat jalan...</td>
                            </tr>
                        </tbody>
                    </table>
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
        <tr>
            <td colspan="9" class="text-center">Memuat data pasien...</td>
        </tr>
    `;

    // Fungsi untuk menghitung usia berdasarkan tanggal lahir
    function hitungUsia(tanggalLahir, tanggalRegistrasi) {
        const lahir = new Date(tanggalLahir); // Mengubah tanggal lahir menjadi objek Date
        const sekarang = new Date(tanggalRegistrasi); // Mendapatkan tanggal registrasi
        let usia = sekarang.getFullYear() - lahir.getFullYear(); // Menghitung usia berdasarkan tahun

        // Menghitung selisih bulan dan hari
        const bulan = sekarang.getMonth() - lahir.getMonth();
        const hari = sekarang.getDate() - lahir.getDate();

        // Periksa apakah bulan/hari ulang tahun belum terlewati di tahun ini
        if (bulan < 0 || (bulan === 0 && hari < 0)) {
            usia--; // Kurangi usia jika ulang tahun belum terlewati
        }
        return usia; // Mengembalikan usia
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
                        <td colspan="9" class="text-center"><strong>Silakan masukkan tanggal</strong><br>Data-data pasien rawat jalan ini diperoleh dari Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan melalui  <em>Application Programming Interface</em> (API)</td>
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
                        <td>${pasien.tempat_lahir}<br><small class="date text-nowrap">${pasien.tanggal_lahir} • ${usia} tahun</small></td>
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
            $('#datapasien').empty(); // Kosongkan tabel pasien
            $('#datapasien').append(loading); // Tampilkan loading indicator
            fetchPasien(); // Panggil fungsi untuk mengambil data pasien
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        fetchPasien();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>