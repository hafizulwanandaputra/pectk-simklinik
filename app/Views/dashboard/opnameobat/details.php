<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/opnameobat'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $opname_obat['tanggal'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('opnameobat/detailopnameobat/' . $previous['id_opname_obat']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['tanggal'] ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('opnameobat/detailopnameobat/' . $next['id_opname_obat']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['tanggal'] ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Informasi Laporan Stok Obat</div>
            <div style="font-size: 0.75rem;">
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                    <div class="col">
                        <div class="date">
                            <?= $opname_obat['tanggal'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Apoteker</div>
                    <div class="col">
                        <div class="date">
                            <?= $opname_obat['apoteker'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm  overflow-auto">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="width:100%; font-size: 0.75rem;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">No</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nama Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="detail_opname_obat">
                        <tr>
                            <td colspan="3" class="text-center">Memuat detail obat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-body-tertiary">
                <div class="row overflow-hidden d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total</div>
                    <div class="col text-end">
                        <div class="date text-truncate placeholder-glow fw-bold" id="total_stok">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="exportOpnameObatBtn">
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-success  bg-gradient" type="button" id="exportBtn" onclick="startDownload()" disabled><i class="fa-solid fa-file-excel"></i> Ekspor Laporan Stok Obat (Excel)</button>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function startDownload() {
        $('#exportBtn').prop('disabled', true);
        $('#loadingSpinner').show(); // Menampilkan spinner

        // Membuat toast ekspor berjalan
        const toast = $(`
        <div id="exportToast" class="toast show transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong>Mengekspor</strong>
                    <div>
                        <span class="date" id="exportPercent">0%</span>
                        <div class="vr"></div>
                        <button type="button" class="btn-close" aria-label="Close" id="cancelExport"></button>
                    </div>
                </div>
                <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                    <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient bg-primary" role="progressbar" style="width: 0%; transition: none"></div>
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

        try {
            // Mengambil file dari server
            const response = await axios.get('<?= base_url('opnameobat/exportopnameobat/' . $opname_obat['id_opname_obat']); ?>', {
                responseType: 'blob', // Mendapatkan data sebagai blob
                onDownloadProgress: function(progressEvent) {
                    if (progressEvent.lengthComputable) {
                        let percentComplete = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                        $('#exportPercent').text(percentComplete + '%');
                        $('#exportProgressBar').css('width', percentComplete + '%');
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
            $('#exportBtn').prop('disabled', false);
        }
    }
    async function fetchDetailOpnameObat() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('opnameobat/obatlist/') . $opname_obat['id_opname_obat'] ?>');

            const data = response.data;
            $('#detail_opname_obat').empty();

            let jumlahStok = 0;

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada obat</td>
                    </tr>
                `;
                $('#detail_opname_obat').append(emptyRow);
                $('#exportBtn').prop('disabled', true);
            } else {
                data.forEach(function(detail_opname_obat, index) {
                    const sisa_stok = parseInt(detail_opname_obat.sisa_stok); // Konversi sisa stok ke integer
                    jumlahStok += sisa_stok;

                    const detail_opname_obatElement = `
                    <tr>
                        <td class="date text-nowrap text-center">${index + 1}</td>
                        <td>${detail_opname_obat.nama_obat}</td>
                        <td class="date text-end">${sisa_stok.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_opname_obat').append(detail_opname_obatElement);
                    $('#exportBtn').prop('disabled', false);

                });
            }
            const jumlahStokElement = `${jumlahStok.toLocaleString('id-ID')}`;
            $('#total_stok').text(jumlahStokElement);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#detail_opname_obat').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchDetailOpnameObat();
            }
        });
        fetchDetailOpnameObat();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>