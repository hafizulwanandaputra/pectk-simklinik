<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/transaksi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
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
            <div class="mb-2">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Kuitansi</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 12.5%;">Kasir</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Nomor Rekam Medis</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 12.5%;">Nama Pasien</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Metode Pembayaran</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 12.5%;">Dokter</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 12.5%;">Tindakan</th>
                                <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Kas</th>
                            </tr>
                        </thead>
                        <tbody class="align-top" id="datatransaksi">
                            <tr>
                                <td colspan="9" class="text-center" style="cursor: wait;">Memuat data transaksi...</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;" colspan="8">Total Pemasukan</th>
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
            const response = await axios.get(`<?= base_url('transaksi/reportexcel') ?>/${tanggal}`, {
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
            <td colspan="9" class="text-center" style="cursor: wait;">Memuat data transaksi...</td>
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
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                $('#datatransaksi').empty(); // Kosongkan tabel transaksi
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center">Silakan masukkan tanggal</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris kosong ke tabel
                return; // Keluar dari fungsi
            }

            // Mengambil data transaksi dari API berdasarkan tanggal
            const response = await axios.get(`<?= base_url('transaksi/report') ?>/${tanggal}`);
            const data = response.data.data; // Mendapatkan data transaksi
            const total_all = response.data.total_all; // Mendapatkan total keseluruhan

            $('#reportBtns').show(); // Tampilkan tombol buat laporan
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh

            // Cek apakah data transaksi kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada transaksi yang berlangsung pada ${tanggal}</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Mengurutkan data transaksi berdasarkan no_kwitansi
            data.sort((a, b) => a.no_kwitansi.localeCompare(b.no_kwitansi, 'en', {
                numeric: true
            }));

            // Menambahkan setiap transaksi ke tabel
            data.forEach(function(transaksi, index) {
                const nama_pasien = transaksi.nama_pasien == null ?
                    `<em>Anonim</em>` :
                    transaksi.nama_pasien;
                const rowspan = (transaksi.detail.layanan.length) + 4;
                const layanan = transaksi.detail.layanan
                const obatalkes = transaksi.detail.obatalkes
                const bank = transaksi.bank ? transaksi.bank : `-`;
                const no_rm = transaksi.no_rm === null ? `-` : transaksi.no_rm;
                // Menjadikan angka-angka yang diperoleh sebagai integer
                const total_pembayaran = parseInt(transaksi.total_pembayaran);

                // Baris pertama untuk informasi utama transaksi
                const transaksiElement = `
                    <tr>
                        <td rowspan="${rowspan}" class="date text-nowrap text-center">${index + 1}</td>
                        <td rowspan="${rowspan}" class="date text-nowrap">${transaksi.no_kwitansi}</td>
                        <td rowspan="${rowspan}">${transaksi.kasir}</td>
                        <td rowspan="${rowspan}" class="date text-nowrap">${no_rm}</td>
                        <td rowspan="${rowspan}">${nama_pasien}</td>
                        <td rowspan="${rowspan}">${transaksi.metode_pembayaran}</td>
                        <td rowspan="${rowspan}">${transaksi.dokter}</td>
                    </tr>
                `;

                // Menambahkan elemen transaksi utama ke tabel
                $('#datatransaksi').append(transaksiElement);

                // Menambahkan baris untuk setiap layanan
                layanan.forEach(function(layananItem) {
                    const harga_transaksi = parseInt(layananItem.harga_transaksi);
                    const layananRow = `
                        <tr>
                            <td>${layananItem.nama_layanan}</td>
                            <td class="text-end date">Rp${harga_transaksi.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    $('#datatransaksi').append(layananRow);
                });

                // Menambahkan baris untuk setiap obat/alkes
                const obatRow = `
                        <tr>
                            <td>Obat</td>
                            <td class="text-end date">Rp${obatalkes.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                $('#datatransaksi').append(obatRow);
                // Menambahkan baris terakhir untuk total pembayaran
                const totalPembayaranRow = `
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="fw-bold date text-end">Rp${total_pembayaran.toLocaleString('id-ID')}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Bank/E-wallet</td>
                        <td class="fw-bold text-end">${bank}</td>
                    </tr>
                `;
                $('#datatransaksi').append(totalPembayaranRow);
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
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <tr>
                    <td colspan="9" class="text-center">${error}</td>
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
        fetchTransaksi(); // Memanggil fungsi untuk mengambil data transaksi
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol bersihkan
        $('#clearTglButton').on('click', function() {
            $('#tanggal').val(''); // Kosongkan tanggal
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#datatransaksi').append(loading); // Tampilkan loading indicator
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });

        // Panggil fungsi untuk mengambil data transaksi saat dokumen siap
        fetchTransaksi();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>