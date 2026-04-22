<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
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
                    <div class="input-group input-group-sm" id="form-resep-tahunan">
                        <input type="number" id="tahun" name="tahun" class="form-control rounded-start" <?= (session()->get('auto_date') == 1) ? 'value="' . date('Y') . '"' : ''; ?>>
                        <button class="btn btn-danger bg-gradient" type="button" id="clearThnButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Bersihkan Bulan"><i class="fa-solid fa-xmark"></i></button>
                        <button class="btn btn-success bg-gradient " type="button" id="refreshButton" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan" disabled><i class="fa-solid fa-sync"></i></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <div class="mb-3">
                <div class="tab-content" id="nav-tabContent">
                    <div id="reseptahunan-container">
                        <div class="card shadow-sm  overflow-auto">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                                    <thead>
                                        <tr class="align-middle">
                                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">No</th>
                                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 50%;">Nama Obat</th>
                                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Obat Keluar</th>
                                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-top" id="reseptahunan">
                                        <tr>
                                            <td colspan="5" class="text-center" style="cursor: wait;">Memuat data resep...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Obat Keluar</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow" id="total_keluar_tahunan">
                                            <span class="placeholder w-100"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row overflow-hidden d-flex align-items-end">
                                    <div class="col fw-medium text-nowrap">Total Harga</div>
                                    <div class="col text-end">
                                        <div class="date text-truncate placeholder-glow fw-bold" id="total_harga_tahunan">
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
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    // LAPORAN TAHUNAN
    async function downloadReport() {
        $('#reportBtn').prop('disabled', true);
        $('#form-resep-tahunan input, #form-resep-tahunan button, .dokter-checkbox-2').prop('disabled', true);
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
                        <button type="button" class="btn-close p-0 ms-1" aria-label="Close" id="cancelExport2" style="height: 1rem; width: 1rem;"></button>
                    </div>
                </div>
                <div class="progress mb-1" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                    <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient bg-primary" role="progressbar" style="width: 0%; transition: none"></div>
                </div>
                <div style="font-size: 0.75em;">
                    <span><strong>Laporan Resep Bulanan</strong></span><br>
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
        $(document).on('click', '#cancelExport2', function() {
            source.cancel('Ekspor laporan resep tahunan dibatalkan');
        });

        // Event listener untuk menangani sebelum halaman di-unload
        $(window).on('beforeunload', function() {
            source.cancel('Ekspor laporan resep harian dibatalkan');
            // Memberi jeda sebelum menyembunyikan loading spinner
            setTimeout(function() {
                $('#loadingSpinner').show();
            }, 300); // jeda 300 milidetik (0.3 detik)
        });

        try {
            // Ambil nilai tahun dari input
            const tahun = $('#tahun').val();
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
            const response = await axios.get(`<?= base_url('laporanreseptahunan/exportexcel') ?>/${tahun}`, {
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
                showSuccessToast('Laporan resep tahunan berhasil diekspor');
            });
        } catch (error) {
            // Hapus #exportToast dan ganti dengan gagal
            $('#exportToast').fadeOut(300, function() {
                $(this).remove();
                if (axios.isCancel(error)) {
                    showFailedToast(error.message); // Pesan pembatalan ekspor
                } else if (error.response.request.status === 404) {
                    showFailedToast('Data resep kosong');
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            });
        } finally {
            $('#loadingSpinner').hide(); // Menyembunyikan spinner setelah unduhan selesai
            $('#reportBtn').prop('disabled', false);
            $('#form-resep-tahunan input, #form-resep-tahunan button, .dokter-checkbox-2').prop('disabled', false);
        }
    }
    // HTML untuk menunjukkan bahwa data transaksi sedang dimuat
    const loading = `
        <tr>
            <td colspan="5" class="text-center" style="cursor: wait;">Memuat data resep...</td>
        </tr>
    `;

    // Fungsi untuk mengambil data resep harian dari tabel resep
    async function fetchResep() {
        $('#loadingSpinner').show(); // Menampilkan spinner loading

        try {
            // Ambil nilai tahun dari input
            const tahun = $('#tahun').val();

            // Cek apakah tahun diinput
            if (!tahun) {
                $('#dokter-tahunan').hide(); // Sembunyikan kotak centang dokter
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                $('#reseptahunan').empty(); // Kosongkan tabel resep
                $('#refreshButton').prop('disabled', true); // Nonaktifkan tombol refresh
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Silakan masukkan tahun</td>
                    </tr>
                `;
                $('#reseptahunan').append(emptyRow); // Menambahkan baris kosong ke tabel
                $('#total_keluar_tahunan').text('0');
                $('#total_harga_tahunan').text('Rp0');
                return; // Keluar dari fungsi
            }

            // Mengambil data resep dari API berdasarkan tahun
            const response = await axios.get(`<?= base_url('laporanreseptahunan/export') ?>/${tahun}?`);
            const data = response.data.laporanresep; // Mendapatkan data resep
            // Mendapatkan total keseluruhan
            const total_keluar_keseluruhan = response.data.total_keluar_keseluruhan;
            const total_harga_keseluruhan = response.data.total_harga_keseluruhan;

            $('#dokter-tahunan').show(); // Tampilkan kotak centang dokter
            $('#reportBtns').show(); // Tampilkan tombol buat laporan
            $('#reseptahunan').empty(); // Kosongkan tabel resep
            $('#refreshButton').prop('disabled', false); // Aktifkan tombol refresh

            // Cek apakah data resep kosong
            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                $('#reportBtns').hide(); // Sembunyikan tombol buat laporan
                const message = response.data.message == null ? `Tidak ada resep yang digunakan pada tahun ${tahun}` : response.data.message;
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">${message}</td>
                    </tr>
                `;
                $('#reseptahunan').append(emptyRow); // Menambahkan baris pesan ke tabel
            }

            // Menambahkan setiap resep ke tabel
            data.forEach(function(resep, index) {
                // Menjadikan angka-angka yang diperoleh sebagai integer
                const total_keluar = parseInt(resep.total_keluar);
                const harga_satuan = parseInt(resep.harga_satuan);
                const total_harga = parseInt(resep.total_harga);

                // Baris pertama untuk informasi utama resep
                const resepElement = `
                    <tr>
                        <td class="date text-nowrap text-center">${index + 1}</td>
                        <td>${resep.nama_obat}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${total_keluar.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                // Menambahkan elemen resep ke tabel
                $('#reseptahunan').append(resepElement);
            });

            // Menambahkan total keluar keseluruhan
            if (isNaN(total_keluar_keseluruhan) || total_keluar_keseluruhan === 0) {
                $('#total_keluar_tahunan').text('0');
            } else {
                $('#total_keluar_tahunan').text(`${total_keluar_keseluruhan.toLocaleString('id-ID')}`);
            }

            // Menambahkan total harga keseluruhan
            if (isNaN(total_harga_keseluruhan) || total_harga_keseluruhan === 0) {
                $('#total_harga_tahunan').text('Rp0');
            } else {
                $('#total_harga_tahunan').text(`Rp${total_harga_keseluruhan.toLocaleString('id-ID')}`);
            }
        } catch (error) {
            // Menangani error jika permintaan gagal
            console.error(error); // Menampilkan error di konsol
            const errorRow = `
                <tr>
                    <td colspan="5" class="text-center text-danger">${error}</td>
                </tr>
            `;
            $('#reseptahunan').empty(); // Kosongkan tabel resep
            $('#reseptahunan').append(errorRow); // Menambahkan baris error ke tabel
        } finally {
            // Sembunyikan spinner loading setelah selesai
            $('#loadingSpinner').hide();
        }
    }

    // Event listener ketika tahun diubah
    $('#tahun').on('input', function() {
        $('#reseptahunan').empty(); // Kosongkan tabel resep
        $('#reseptahunan').append(loading); // Menampilkan loading indicator
        fetchResep(); // Memanggil fungsi untuk mengambil data resep
    });

    $(document).ready(function() {
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchResep();
            }
        });
        // Menangani event klik pada tombol bersihkan tahun
        $('#clearThnButton').on('click', function() {
            $('#tahun').val(''); // Bersihkan input tahun
            $('#reseptahunan').empty(); // Kosongkan tabel resep
            $('#reseptahunan').append(loading); // Tampilkan loading indicator
            fetchResep(); // Panggil fungsi untuk mengambil data resep
        });
        // Menangani event klik pada tombol refresh
        $('#refreshButton').on('click', function() {
            fetchResep(); // Panggil fungsi untuk mengambil data resep
        });

        // Panggil fungsi untuk mengambil data transaksi saat dokumen siap
        fetchResep();
    });

    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>