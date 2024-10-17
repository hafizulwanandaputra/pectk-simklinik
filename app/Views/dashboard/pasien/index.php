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
    <div class="mb-2">
        <fieldset class="border rounded-3 px-2 py-0 mb-3" id="tambahPasienForm">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Masukkan Tanggal</legend>
            <div class="mb-2 input-group">
                <input type="date" id="tanggal" name="tanggal" class="form-control rounded-start-3">
                <button class="btn btn-success bg-gradient rounded-end-3" type="button" id="refreshButton" disabled><i class="fa-solid fa-sync"></i></button>
            </div>
        </fieldset>
        <div class="row row-cols-1 row-cols-sm-2 g-2 mb-2">
            <div class="col">
                <div class="card bg-body-tertiary w-100 rounded-3">
                    <div class="card-header w-100 text-truncate bg-gradient">Tanggal </div>
                    <div class="card-body">
                        <h5 class="display-6 fw-medium date mb-0" id="tanggal2"><i class="fa-solid fa-spinner fa-spin-pulse"></i></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-body-tertiary w-100 rounded-3">
                    <div class="card-header w-100 text-truncate bg-gradient">Jumlah Pasien yang Berobat</div>
                    <div class="card-body">
                        <h5 class="display-6 fw-medium date mb-0" id="lengthpasien"><i class="fa-solid fa-spinner fa-spin-pulse"></i></h5>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table class="table table-sm table-hover" style="width:100%; font-size: 9pt;">
                <thead>
                    <tr class="align-middle">
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%; min-width: 128px;">Nama</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jenis Kelamin</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Rekam Medis</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Registrasi</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%; min-width: 128px;">Tempat dan Tanggal Lahir</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Telepon</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 25%; min-width: 128px;">Alamat</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 15%; min-width: 128px;">Dokter</th>
                    </tr>
                </thead>
                <tbody class="align-top" id="datapasien">
                    <tr>
                        <td colspan="9" class="text-center">Memuat data pasien...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('datatable'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script>
    const loading = `
        <tr>
            <td colspan="9" class="text-center">Memuat data pasien...</td>
        </tr>
    `;

    function hitungUsia(tanggalLahir) {
        const lahir = new Date(tanggalLahir);
        const sekarang = new Date();
        let usia = sekarang.getFullYear() - lahir.getFullYear();
        const bulan = sekarang.getMonth() - lahir.getMonth();
        const hari = sekarang.getDate() - lahir.getDate();

        // Periksa apakah bulan/hari ulang tahun belum terlewati di tahun ini
        if (bulan < 0 || (bulan === 0 && hari < 0)) {
            usia--;
        }
        return usia;
    }

    async function fetchPasien() {
        $('#loadingSpinner').show();

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();

            if (!tanggal) {
                $('#datapasien').empty();
                $('#refreshButton').prop('disabled', true);
                $('#tanggal2').text(`-`);
                $('#lengthpasien').text(`-`);
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center"><strong>Silakan masukkan tanggal</strong><br>Data-data pasien ini diperoleh dari Sistem Informasi Manajemen Klinik Utama Mata Padang Eye Center Teluk Kuantan melalui <em>Application Programming Interface</em> (API)</td>
                    </tr>
                `;
                $('#datapasien').append(emptyRow);
                return;
            }
            const response = await axios.get(`<?= base_url('pasien/pasienapi') ?>?tanggal=${tanggal}`);
            const data = response.data.data;
            $('#datapasien').empty();
            $('#refreshButton').prop('disabled', false);
            $('#tanggal2').text(tanggal);
            $('#lengthpasien').text(data.length);
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                <tr>
                    <td colspan="9" class="text-center">Tidak ada pasien yang berobat pada ${tanggal}</td>
                </tr>
            `;
                $('#datapasien').append(emptyRow);
            }
            data.sort((a, b) => a.nomor_registrasi.localeCompare(b.nomor_registrasi, 'en', {
                numeric: true
            }));
            data.forEach(function(pasien, index) {
                // Kondisikan jenis kelamin
                const jenis_kelamin = pasien.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan";
                const usia = hitungUsia(pasien.tanggal_lahir);

                const pasienElement = `
                <tr>
                    <td class="date text-nowrap text-center">${index + 1}</td>
                    <td>${pasien.nama_pasien}</td>
                    <td class="text-nowrap">${jenis_kelamin}</td>
                    <td class="date text-nowrap">${pasien.no_rm}</td>
                    <td class="date text-nowrap">${pasien.nomor_registrasi}</td>
                    <td>${pasien.tempat_lahir}<br><small class="date text-nowrap">${pasien.tanggal_lahir} • ${usia} tahun</small></td>
                    <td class="date text-nowrap text-end">${pasien.telpon}</td>
                    <td>${pasien.alamat}</td>
                    <td>${pasien.dokter}</td>
                </tr>
                `;
                $('#datapasien').append(pasienElement);
            });
        } catch (error) {
            console.error(error.response.data.error);
            $('#tanggal2').html(`<i class="fa-solid fa-xmark"></i> Error`);
            $('#lengthpasien').html(`<i class="fa-solid fa-xmark"></i> Error`);
            const errorRow = `
                <tr>
                    <td colspan="9" class="text-center">${error.response.data.error}</td>
                </tr>
            `;
            $('#datapasien').empty();
            $('#datapasien').append(errorRow);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }
    $('#tanggal').on('change', function() {
        $('#datapasien').empty();
        $('#datapasien').append(loading);
        $('#tanggal2').html(`<i class="fa-solid fa-spinner fa-spin-pulse"></i>`);
        $('#lengthpasien').html(`<i class="fa-solid fa-spinner fa-spin-pulse"></i>`);
        fetchPasien(); // Refresh articles on button click
    });

    $(document).ready(function() {
        $('#refreshButton').on('click', function() {
            $('#datapasien').empty();
            $('#datapasien').append(loading);
            $('#tanggal2').html(`<i class="fa-solid fa-spinner fa-spin-pulse"></i>`);
            $('#lengthpasien').html(`<i class="fa-solid fa-spinner fa-spin-pulse"></i>`);
            fetchPasien(); // Refresh articles on button click
        });
        fetchPasien();
    });

    function showSuccessToast(message) {
        var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-success border border-success rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start">
                    <div style="width: 24px; text-align: center;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="w-100 mx-2 text-start" id="toast-message">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        var toastElement = $(toastHTML);
        $('#toastContainer').append(toastElement); // Make sure there's a container with id `toastContainer`
        var toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    function showFailedToast(message) {
        var toastHTML = `<div id="toast" class="toast fade align-items-center text-bg-danger border border-danger rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start">
                    <div style="width: 24px; text-align: center;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div class="w-100 mx-2 text-start" id="toast-message">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        var toastElement = $(toastHTML);
        $('#toastContainer').append(toastElement); // Make sure there's a container with id `toastContainer`
        var toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
</script>
<?= $this->endSection(); ?>