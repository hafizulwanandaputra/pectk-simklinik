<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/opnameobat'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Informasi Laporan Stok Obat</div>
            <div style="font-size: 0.75rem;">
                <div class="mb-1 row">
                    <div class="col-5 col-lg-3 fw-medium">Tanggal dan Waktu</div>
                    <div class="col">
                        <div class="date">
                            <?= $opname_obat['tanggal'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-1 row">
                    <div class="col-5 col-lg-3 fw-medium">Apoteker</div>
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
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow fw-bold" id="total_stok">
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
        $('#loadingSpinner').show(); // Menampilkan spinner

        try {
            // Mengambil file dari server
            const response = await axios.get('<?= base_url('opnameobat/exportopnameobat/' . $opname_obat['id_opname_obat']); ?>', {
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
        fetchDetailOpnameObat();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>