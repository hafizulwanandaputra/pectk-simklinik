<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/transaksi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
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
                    <select id="jaminanFilter" class="form-select form-select-sm mb-2 flex-grow-1">
                        <option value="" disabled selected>-- Pilih Jaminan --</option>
                    </select>
                    <div class="d-flex flex-column flex-lg-row gap-1">
                        <div class="input-group input-group-sm w-auto flex-fill" id="form-tanggal-awal">
                            <input type="date" id="tanggal-awal" name="tanggal-awal" class="form-control">
                            <button class="btn btn-danger bg-gradient" type="button" id="clearTglAwalButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="input-group input-group-sm w-auto flex-fill" id="form-tanggal-akhir">
                            <input type="date" id="tanggal-akhir" name="tanggal-akhir" class="form-control">
                            <button class="btn btn-danger bg-gradient" type="button" id="clearTglAkhirButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Tanggal"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <button class="btn btn-success bg-gradient btn-sm" type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="mb-3">
                <div class="card shadow-sm  overflow-auto">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                            <thead>
                                <tr class="align-middle">
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">No</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Tanggal dan Waktu</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Nomor Kuitansi</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 12.5%;">Kasir</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Nomor Rekam Medis</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 12.5%;">Nama Pasien</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Metode Pembayaran</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 12.5%;">Dokter</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 12.5%;">Tindakan</th>
                                    <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Kas</th>
                                </tr>
                            </thead>
                            <tbody class="align-top" id="datatransaksi">
                                <tr>
                                    <td colspan="9" class="text-center" style="cursor: wait;">Memuat data transaksi...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row overflow-hidden d-flex align-items-end">
                            <div class="col fw-medium text-nowrap">Total Pemasukan</div>
                            <div class="col text-end">
                                <div class="date text-truncate placeholder-glow fw-bold" id="total_all">
                                    <span class="placeholder w-100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="reportBtns" style="display: none;">
                    <hr>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                        <button class="btn btn-success  bg-gradient" type="button" id="reportBtn" onclick="downloadReport()"><i class="fa-solid fa-file-excel"></i> Buat Laporan (Excel)</button>
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
        $('#reportBtn').prop('disabled', true);
        $('#form-tanggal-awal input, #form-tanggal-awal button').prop('disabled', true);
        $('#loadingSpinner').show(); // Menampilkan spinner

        // Membuat toast ekspor berjalan
        const toast = $(`
        <div id="exportToast" class="toast show bg-body-tertiary transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="text-truncate me-1">
                        <strong id="statusHeader">Menunggu respons peladen...</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="date" id="exportPercent">0%</span>
                        <button type="button" class="btn-close p-0 ms-1" aria-label="Close" id="cancelExport" style="height: 1rem; width: 1rem;"></button>
                    </div>
                </div>
                <div class="progress mb-1" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                    <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient bg-primary" role="progressbar" style="width: 0%; transition: none"></div>
                </div>
                <div style="font-size: 0.75em;">
                    <span class="date" id="loadedKB">0 B</span> dari <span class="date" id="totalKB">0 B</span> diunduh<br>
                    <span class="date" id="eta">Menunggu respons peladen...</span>
                </div>
            </div>
        </div>
    `);

        $('#toastContainer').append(toast);

        const CancelToken = axios.CancelToken;
        const source = CancelToken.source();

        // Menangani pembatalan ekspor
        $(document).on('click', '#cancelExport', function() {
            source.cancel('Ekspor dibatalkan');
        });

        // Event listener untuk menangani sebelum halaman di-unload
        $(window).on('beforeunload', function() {
            source.cancel('Ekspor dibatalkan');
            // Memberi jeda sebelum menyembunyikan loading spinner
            setTimeout(function() {
                $('#loadingSpinner').show();
            }, 300); // jeda 300 milidetik (0.3 detik)
        });

        try {
            // Ambil nilai tanggal dari input
            const jaminan = $('#jaminanFilter').val();
            const jaminanNama = $('#jaminanFilter option:selected').text();
            const tanggal_awal = $('#tanggal-awal').val();
            const tanggal_akhir = $('#tanggal-akhir').val();
            // Fungsi untuk mengubah byte ke satuan otomatis
            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                const value = bytes / Math.pow(k, i);
                return `${value.toLocaleString('id-ID', { maximumFractionDigits: 2 })} ${sizes[i]}`;
            }

            // Fungsi untuk memformat ETA dengan jam/menit/detik
            function formatETA(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = Math.floor(seconds % 60);

                let parts = [];
                if (hours > 0) parts.push(`${hours} jam`);
                if (minutes > 0) parts.push(`${minutes} menit`);
                if (secs > 0 || parts.length === 0) parts.push(`${secs} detik`);

                return parts.join(' ');
            }

            let startTime = null;
            let loadedBytes = 0;
            let totalBytes = 0;
            let speedBps = 0;
            let etaTimer = null;

            // Fungsi untuk memperbarui ETA setiap detik
            function updateETA() {
                if (speedBps > 0) {
                    const remainingBytes = totalBytes - loadedBytes;
                    const estimatedTimeInSeconds = remainingBytes / speedBps;

                    const etaFormatted = formatETA(estimatedTimeInSeconds);
                    $('#eta').text(`Selesai dalam ${etaFormatted}`);
                }
            }

            startTime = Date.now(); // Waktu mulai unduhan

            // Mulai interval ETA
            etaTimer = setInterval(updateETA, 1000);
            // Mulai unduhan file
            const response = await axios.get(`<?= base_url('transaksi/reportjaminanexcel') ?>`, {
                params: {
                    jaminan: jaminan,
                    jaminanNama: jaminanNama,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                },
                responseType: 'blob', // Mendapatkan data sebagai blob
                onDownloadProgress: function(progressEvent) {
                    if (progressEvent.lengthComputable) {
                        loadedBytes = progressEvent.loaded;
                        totalBytes = progressEvent.total;

                        const percentComplete = Math.round((loadedBytes / totalBytes) * 100);
                        const elapsedTimeInSeconds = (Date.now() - startTime) / 1000;
                        speedBps = elapsedTimeInSeconds > 0 ? (loadedBytes / elapsedTimeInSeconds) : 0;

                        // Update tampilan progress
                        $('#exportPercent').text(`${percentComplete}%`);
                        $('#exportProgressBar').css('width', `${percentComplete}%`);
                        $('#statusHeader').text(`Mengunduh...`);
                        $('#loadedKB').text(formatBytes(loadedBytes));
                        $('#totalKB').text(formatBytes(totalBytes));

                        // Jika selesai
                        if (loadedBytes >= totalBytes) {
                            clearInterval(etaTimer); // Hentikan ETA timer
                            $('#eta').text('Selesai');
                            $('#statusHeader').text('Unduhan selesai');
                        }
                    }
                },
                cancelToken: source.token
            });

            // Memastikan progress 100% setelah selesai
            $('#exportPercent').text('100%');
            $('#exportProgressBar').css('width', '100%');

            // Mendapatkan nama file dari header Content-Disposition
            const disposition = response.headers['content-disposition'];
            const filename = disposition ? disposition.split('filename=')[1].split(';')[0].replace(/"/g, '') : 'export.xlsx';

            // Membuat URL unduhan
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();

            window.URL.revokeObjectURL(url); // Membebaskan URL yang dibuat

            // Hapus #exportToast dan ganti dengan sukses
            $('#exportToast').fadeOut(300, function() {
                $('#exportToast').remove();
                showSuccessToast('Berhasil diekspor');
            });
        } catch (error) {
            // Hapus #exportToast dan ganti dengan gagal
            $('#exportToast').fadeOut(300, function() {
                $(this).remove();
                if (axios.isCancel(error)) {
                    showFailedToast(error.message); // Pesan pembatalan ekspor
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            });
        } finally {
            $('#loadingSpinner').hide(); // Menyembunyikan spinner setelah unduhan selesai
            $('#reportBtn').prop('disabled', false);
            $('#form-tanggal-awal input, #form-tanggal-awal button').prop('disabled', false);
        }
    }
    // HTML untuk menunjukkan bahwa data transaksi sedang dimuat
    const loading = `
        <tr>
            <td colspan="9" class="text-center" style="cursor: wait;">Memuat data transaksi...</td>
        </tr>
    `;

    async function fetchJaminanOptions(selectedJaminan = null) {
        // Show the spinner
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('transaksi/jaminanlist') ?>`);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#jaminanFilter');

                // Simpan nilai yang saat ini dipilih
                const currentSelection = selectedJaminan || select.val();

                // Hapus semua opsi kecuali opsi pertama (default)
                select.find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Mengatur ulang pilihan sebelumnya
                if (currentSelection) {
                    select.val(currentSelection);
                }
            } else {
                showFailedToast('Gagal mendapatkan kasir.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan kasir.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    // Fungsi untuk mengambil data transaksi dari tabel transaksi
    async function fetchTransaksi() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tanggal dari input
            const jaminan = $('#jaminanFilter').val();
            const jaminanNama = $('#jaminanFilter option:selected').text();
            const tanggal_awal = $('#tanggal-awal').val();
            const tanggal_akhir = $('#tanggal-akhir').val();

            // Cek apakah jaminan
            if (!jaminan) {
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                $('#datatransaksi').empty(); // Kosongkan tabel transaksi
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center">Silakan pilih jaminan</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_all').text('Rp0');
                return; // Keluar dari fungsi
            }

            // Cek apakah tanggal diinput
            if (!tanggal_awal || !tanggal_akhir) {
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                $('#datatransaksi').empty(); // Kosongkan tabel transaksi
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                const emptyRow = `
                    <tr>
                        <td colspan="9" class="text-center">Silakan masukkan rentang tanggal</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_all').text('Rp0');
                return; // Keluar dari fungsi
            }

            // Mengambil data transaksi dari API berdasarkan tanggal
            const response = await axios.get(`<?= base_url('transaksi/reportjaminanlist') ?>`, {
                params: {
                    jaminan: jaminan,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                }
            });
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
                        <td colspan="9" class="text-center">Tidak ada transaksi yang berlangsung pada ${tanggal_awal} sampai dengan ${tanggal_akhir} dengan jaminan ${jaminanNama}</td>
                    </tr>
                `;
                $('#datatransaksi').append(emptyRow); // Menambahkan baris pesan ke tabel
                $('#total_all').text('Rp0');
            }

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
                        <td rowspan="${rowspan}" class="date text-nowrap">${transaksi.tgl_transaksi}</td>
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
                    <td colspan="9" class="text-center text-danger">${error}</td>
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
    $('#jaminanFilter, #tanggal-awal, #tanggal-akhir').on('change', function() {
        $('#datatransaksi').empty(); // Kosongkan tabel transaksi
        $('#datatransaksi').append(loading); // Menampilkan loading indicator
        fetchTransaksi(); // Memanggil fungsi untuk mengambil data transaksi
    });

    $(document).ready(function() {
        // Menangani event klik pada tombol bersihkan
        $('#clearTglAwalButton').on('click', function() {
            $('#tanggal_awal').val(''); // Kosongkan tanggal
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#datatransaksi').append(loading); // Tampilkan loading indicator
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });
        $('#clearTglAkhirButton').on('click', function() {
            $('#tanggal_akhir').val(''); // Kosongkan tanggal
            $('#datatransaksi').empty(); // Kosongkan tabel transaksi
            $('#datatransaksi').append(loading); // Tampilkan loading indicator
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchTransaksi();
            }
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            fetchTransaksi(); // Panggil fungsi untuk mengambil data transaksi
        });

        // Panggil fungsi untuk mengambil data transaksi saat dokumen siap
        fetchJaminanOptions();
        fetchTransaksi();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>