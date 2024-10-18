<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 link-body-emphasis" href="<?= base_url('/transaksi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
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
        <div id="infoCard" class="row row-cols-1 row-cols-sm-2 g-2 mb-2" style="display: none;">
            <div class="col">
                <div class="card bg-body-tertiary w-100 rounded-3">
                    <div class="card-header w-100 text-truncate">Tanggal </div>
                    <div class="card-body placeholder-glow">
                        <h5 class="display-6 fw-medium date mb-0" id="tanggal2"><span class="placeholder w-100"></span></h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card bg-body-tertiary w-100 rounded-3">
                    <div class="card-header w-100 text-truncate">Jumlah Transaksi yang Diproses</div>
                    <div class="card-body placeholder-glow">
                        <h5 class="display-6 fw-medium date mb-0" id="lengthtransaksi"><span class="placeholder w-100"></span></h5>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
                <thead>
                    <tr class="align-middle">
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nomor Kwitansi</th>
                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%; white-space: nowrap;">Grand Total</th>
                    </tr>
                </thead>
                <tbody class="align-top" id="datatransaksi">
                    <tr>
                        <td colspan="9" class="text-center">Memuat data transaksi...</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;" colspan="2">Total Pemasukan</th>
                        <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="total_all"></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="reportBtns" style="display: none;">
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-success rounded-3 bg-gradient" type="button" id="reportBtn" onclick="downloadReport()"><i class="fa-solid fa-file-excel"></i> Buat Laporan (Excel)</button>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function downloadReport() {
        $('#loadingSpinner').show(); // Menampilkan spinner

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();
            // Mengambil file dari server
            const response = await axios.get(`<?= base_url('transaksi/dailyreportexcel') ?>/${tanggal}`, {
                responseType: 'blob' // Mendapatkan data sebagai blob
            });

            // Mendapatkan nama file dari header Content-Disposition
            const disposition = response.headers['content-disposition'];
            const filename = disposition ? disposition.split('filename=')[1].split(';')[0].replace(/"/g, '') : '.xlsx';

            // Membuat URL unduhan
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const a = document.createElement('a');
            a.href = url;
            a.download = filename; // Menggunakan nama file dari header
            document.body.appendChild(a);
            a.click();
            a.remove();

            window.URL.revokeObjectURL(url); // Membebaskan URL yang dibuat
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide(); // Menyembunyikan spinner setelah unduhan selesai
        }
    }
    // HTML untuk menunjukkan bahwa data transaksi sedang dimuat
    const loading = `
    <tr>
        <td colspan="3" class="text-center">Memuat data transaksi...</td>
    </tr>
`;

    // Fungsi untuk mengambil data transaksi dari tabel transaksi
    async function fetchTransaksi() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tanggal dari input
            const tanggal = $('#tanggal').val();

            // Cek apakah tanggal diinput
            if (!tanggal) {
                $('#infoCard').hide(); // Sembunyikan infoCard
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                $('#datatransaksi').empty(); // Kosongkan tabel transaksi
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                $('#tanggal2').text(``); // Kosongkan tanggal tanggal
                $('#lengthtransaksi').text(``); // Kosongkan panjang transaksi
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="3" class="text-center">Silakan masukkan tanggal</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris kosong ke tabel
                return; // Keluar dari fungsi
            }

            $('#infoCard').show();

            // Mengambil data transaksi dari API berdasarkan tanggal
            const response = await axios.get(`<?= base_url('transaksi/dailyreport') ?>/${tanggal}`);
            const data = response.data.data; // Mendapatkan data transaksi
            const total_all = response.data.total_all; // Mendapatkan total keseluruhan

            $('#reportBtns').show(); // Tampilkan tombol buat laporan
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh
            $('#tanggal2').text(tanggal); // Set text tanggal
            $('#lengthtransaksi').text(data.length); // Set panjang transaksi

            // Cek apakah data transaksi kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                const emptyRow = `
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada transaksi yang berlangsung pada ${tanggal}</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Mengurutkan data transaksi berdasarkan nomor registrasi
            data.sort((a, b) => a.nomor_registrasi.localeCompare(b.nomor_registrasi, 'en', {
                numeric: true
            }));

            // Menambahkan setiap transaksi ke tabel
            data.forEach(function(transaksi, index) {
                // Menjadikan angka-angka yang diperoleh sebagai integer
                const total_pembayaran = parseInt(transaksi.total_pembayaran);

                // Membuat elemen baris untuk setiap transaksi
                const transaksiElement = `
                    <tr>
                        <td class="date text-nowrap text-center">${index + 1}</td>
                        <td class="date text-nowrap">${transaksi.no_kwitansi}</td>
                        <td class="date text-end">Rp${total_pembayaran.toLocaleString('id-ID')}</td>
                    </tr>
                `;
                $('#datatransaksi').append(transaksiElement); // Menambahkan elemen transaksi ke tabel
            });

            // Menambahkan total pemasukan
            const total_pemasukan = parseInt(total_all);
            if (isNaN(total_pemasukan) || total_pemasukan === 0) {
                $('#total_all').text('Rp0');
            } else {
                $('#total_all').text(`Rp${total_pemasukan.toLocaleString('id-ID')}`);
            }
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error.response.data.error); // Menampilkan error di konsol
            $('#tanggal2').html(`<i class="fa-solid fa-xmark"></i> Error`); // Menampilkan error pada text tanggal
            $('#lengthtransaksi').html(`<i class="fa-solid fa-xmark"></i> Error`); // Menampilkan error pada panjang transaksi
            const errorRow = `
                <tr>
                    <td colspan="3" class="text-center">${error.response.data.error}</td>
                </tr>
            `;
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#datatransaksi').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    // Event listener ketika tanggal diubah
    $('#tanggal').on('change', function() {
        $('#datatransaksi').empty(); // Kosongkan tabel transaksi
        $('#datatransaksi').append(loading); // Menampilkan loading indicator
        $('#tanggal2').html(`<span class="placeholder w-100"></span>`); // Menampilkan placeholder pada text tanggal
        $('#lengthtransaksi').html(`<span class="placeholder w-100"></span>`); // Menampilkan placeholder pada panjang transaksi
        fetchTransaksi(); // Memanggil fungsi untuk mengambil data transaksi
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#datatransaksi').append(loading); // Tampilkan loading indicator
            $('#tanggal2').html(`<span class="placeholder w-100"></span>`); // Tampilkan placeholder pada text tanggal
            $('#lengthtransaksi').html(`<span class="placeholder w-100"></span>`); // Tampilkan placeholder pada panjang transaksi
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });

        // Panggil fungsi untuk mengambil data transaksi saat dokumen siap
        fetchTransaksi();
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