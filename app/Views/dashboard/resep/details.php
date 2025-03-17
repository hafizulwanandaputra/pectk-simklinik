<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<style>
    /* Ensures the dropdown is visible outside the parent with overflow auto */
    .select2-container {
        z-index: 1050;
        /* Make sure it's above other elements, like modals */
    }

    .select2-dropdown {
        position: absolute !important;
        /* Ensures placement isn't affected by overflow */
        z-index: 1050;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/resep'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $resep['id_resep'] ?> • <?= $resep['nama_pasien'] ?> • <?= $resep['tanggal_resep'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('resep/detailresep/' . $previous['id_resep']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['id_resep'] ?> • <?= $previous['nama_pasien'] ?> • <?= $previous['tanggal_resep'] ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada resep sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('resep/detailresep/' . $next['id_resep']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['id_resep'] ?> • <?= $next['nama_pasien'] ?> • <?= $next['tanggal_resep'] ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada resep berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Informasi Pasien Rawat Jalan</div>
            <div class="row gx-3">
                <div class="col-lg-6" style="font-size: 0.75em;">
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['tanggal_resep'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Dokter</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['dokter'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Nama Pasien</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['nama_pasien'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Nomor Rekam Medis</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['no_rm'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Nomor Registrasi</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['nomor_registrasi'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="font-size: 0.75em;">
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                        <div class="col">
                            <div class="date">
                                <?php
                                if ($resep['jenis_kelamin'] == 'L') {
                                    echo 'Laki-Laki';
                                } else if ($resep['jenis_kelamin'] == 'P') {
                                    echo 'Perempuan';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Tanggal Lahir</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['tanggal_lahir'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Alamat</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['alamat'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Nomor Telepon</div>
                        <div class="col">
                            <div class="date">
                                <?= $resep['telpon'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Status Konfirmasi</div>
                        <div class="col">
                            <div class="date" id="confirmedStatus">
                                Memuat status...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm  overflow-auto">
            <div class="card-body p-0 m-0 table-responsive">
                <table class="table table-sm mb-0" style="width:100%; font-size: 0.75em;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary col-resize" style="border-bottom-width: 2px; width: 100%;">Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Qty</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="detail_resep">
                        <tr>
                            <td colspan="5" class="text-center">Memuat detail resep...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-body-tertiary">
                <div class="row overflow-hidden d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Resep</div>
                    <div class="col text-end">
                        <div class="date text-truncate placeholder-glow" id="jumlah_resep">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
                <div class="row overflow-hidden d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Harga</div>
                    <div class="col text-end">
                        <div class="date text-truncate placeholder-glow fw-bold" id="total_harga">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <hr>
            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/resep/etiket-dalam/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak E-Tiket Obat Dalam</button>
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn2" onclick="window.open(`<?= base_url('/resep/etiket-luar/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak E-Tiket Obat Luar</button>
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn3" onclick="window.open(`<?= base_url('/resep/print/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Resep</button>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchStatusKonfirmasi() {
        $('#confirmedStatus').text('Memuat status...');

        try {
            const response = await axios.get('<?= base_url('resep/resep/') . $resep['id_resep'] ?>');

            const data = response.data;

            // Cek status konfirmasi
            if (data.confirmed === "1") {
                $('#confirmedStatus').text('Dikonfirmasi');
            } else if (data.confirmed === "0") {
                $('#confirmedStatus').text('Belum Dikonfirmasi');
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#confirmedStatus').text('Silakan coba lagi! • ' + error);
        }
    }

    async function fetchDetailResep() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resep/detailreseplist/') . $resep['id_resep'] ?>');

            const data = response.data;
            $('#detail_resep').empty();

            let jumlahResep = 0;
            let totalHarga = 0;

            let hasInternalMedicine = false; // Track if there is internal medicine (kapsul/tablet/sirup)
            let hasExternalMedicine = false; // Track if there is external medicine (tetes/salep)
            let allConfirmed = true; // Track overall confirmed status

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada obat yang akan dijadikan resep</td>
                    </tr>
                `;
                $('#detail_resep').append(emptyRow);
                $('#cancelConfirmBtn').prop('disabled', true);
                $('#confirmBtn').prop('disabled', true);
            } else {
                data.forEach(function(detail_resep) {
                    const jumlah = parseInt(detail_resep.jumlah); // Konversi jumlah ke integer
                    const harga_satuan = parseInt(detail_resep.harga_satuan); // Konversi harga obat ke integer
                    const total_harga = jumlah * harga_satuan; // Hitung total harga
                    totalHarga += total_harga;
                    jumlahResep += jumlah;

                    // Check if the medicine is internal (kapsul/tablet) or external (tetes/salep)
                    if (['Tablet/Kapsul', 'Sirup'].includes(detail_resep.bentuk_obat)) {
                        hasInternalMedicine = true;
                    } else if (['Tetes', 'Salep'].includes(detail_resep.bentuk_obat)) {
                        hasExternalMedicine = true;
                    }

                    if (detail_resep.confirmed !== "1") {
                        allConfirmed = false;
                    }

                    const kategori_obat = detail_resep.kategori_obat ? `${detail_resep.kategori_obat}, ` : ``;
                    const signa = detail_resep.signa ? `${detail_resep.signa}` : `<em>Tidak ada dosis</em>`;
                    const catatan = detail_resep.catatan ? `${detail_resep.catatan}` : `<em>Tidak ada catatan</em>`;
                    const nama_batch = detail_resep.nama_batch ? `${detail_resep.nama_batch}` : `<em>Tidak ada batch</em>`;

                    const detail_resepElement = `
                    <tr>
                        <td><i class="fa-solid fa-prescription"></i> ${detail_resep.nama_obat}
                        <small>
                            <ul class="ps-3 mb-0">
                                <li>${kategori_obat}${detail_resep.bentuk_obat}</li>
                                <li>${nama_batch}</li>
                                <li>${signa}, ${detail_resep.cara_pakai}, ${catatan}</li>
                            </ul>
                        </small></td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_resep').append(detail_resepElement);
                });
            }

            // Enable/disable print buttons based on overall confirmed status
            if (allConfirmed) {
                if (hasInternalMedicine) {
                    $('#printBtn1').prop('disabled', false);
                }
                if (hasExternalMedicine) {
                    $('#printBtn2').prop('disabled', false);
                }
                $('#printBtn3').prop('disabled', false);
            } else {
                $('#printBtn1').prop('disabled', true);
                $('#printBtn2').prop('disabled', true);
                $('#printBtn3').prop('disabled', true);
            }
            const totalHargaElement = `Rp${totalHarga.toLocaleString('id-ID')}`;
            const jumlahResepElement = `${jumlahResep.toLocaleString('id-ID')}`;
            $('#total_harga').text(totalHargaElement);
            $('#jumlah_resep').text(jumlahResepElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('.col-resize').css('min-width', '0');
            $('#detail_resep').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update) {
                console.log("Received update from WebSocket");
                fetchStatusKonfirmasi();
                fetchDetailResep();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('[data-bs-toggle="tooltip"]').tooltip();

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchStatusKonfirmasi();
                fetchDetailResep();
            }
        });

        $('#refreshButton').on('click', function(e) {
            e.preventDefault();
            fetchStatusKonfirmasi();
            fetchDetailResep();
        });
        fetchStatusKonfirmasi();
        fetchDetailResep();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>