<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);
?>
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

    .custom-counter {
        list-style-type: none;
    }

    .custom-counter li {
        counter-increment: step-counter;
    }

    .custom-counter li::before {
        content: counter(step-counter) ".";
        font-variant-numeric: tabular-nums;
        font-weight: bold;
        padding-right: 0.5rem;
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/rawatjalan'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $rawatjalan['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/transaksi/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/transaksi/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline nav-fill flex-nowrap overflow-auto">
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <?php endif; ?>
                        <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/optik/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/laporanrajal/' . $rawatjalan['id_rawat_jalan']); ?>">Tindakan Rajal</a>
                            <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('rawatjalan/transaksi/' . $rawatjalan['id_rawat_jalan']); ?>">Transaksi</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink' : '' ?>" href="<?= base_url('rawatjalan/transaksi/' . $list['id_rawat_jalan']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm date" style="font-size: 0.75em;"><?= $list['tanggal_registrasi'] ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <?php if (session()->get('role') == 'Dokter') : ?>
                <?php if ($rawatjalan['dokter'] != session()->get('fullname')) : ?>
                    <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                Saat ini Anda melihat transaksi yang dimasukkan oleh <?= $rawatjalan['dokter'] ?>. Pastikan Anda mengisi transaksi sesuai dengan DPJP yang masuk pada sistem ini.
                            </div>
                            <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="row g-3 mb-2">
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm  overflow-auto">
                        <div class="card-header bg-body-tertiary" id="tambahLayananContainer" style="display: none;">
                            <form id="tambahLayanan" enctype="multipart/form-data">
                                <div class="mb-2">
                                    <select class="form-select form-select-sm form-tindakan" id="id_layanan" name="id_layanan" aria-label="id_layanan">
                                        <option value="" disabled selected>-- Pilih Layanan --</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-flex flex-column flex-lg-row gap-2">
                                    <div class="flex-fill">
                                        <input type="number" id="qty_transaksi" name="qty_transaksi" class="form-control form-control-sm form-tindakan" placeholder="Qty" autocomplete="off">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="flex-fill">
                                        <input type="number" id="diskon_layanan" name="diskon_layanan" class="form-control form-control-sm form-tindakan" placeholder="Diskon (%)" autocomplete="off">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="d-grid d-lg-block w-auto">
                                        <button type="submit" id="addLayananButton" class="btn btn-primary bg-gradient btn-sm text-nowrap">
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body p-0 m-0 table-responsive">
                            <table class="table table-sm mb-0" style="width:100%; font-size: 0.75em;">
                                <thead>
                                    <tr class="align-middle">
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nama Layanan</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Qty</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Diskon</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="align-top" id="list_layanan">
                                    <tr>
                                        <td colspan="6" class="text-center">Memuat detail transaksi...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-body-tertiary">
                            <div class="row overflow-hidden d-flex align-items-end">
                                <div class="col fw-medium text-nowrap">Sub Total</div>
                                <div class="col text-end">
                                    <div class="date text-truncate placeholder-glow fw-bold" id="subtotal_layanan">
                                        <span class="placeholder w-100"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm  overflow-auto">
                        <div class="card-header bg-body-tertiary" id="tambahObatAlkesContainer" style="display: none;">
                            <form id="tambahObatAlkes" enctype="multipart/form-data">
                                <div class="mb-2">
                                    <select class="form-select form-select-sm" id="id_resep" name="id_resep" aria-label="id_resep">
                                        <option value="" disabled selected>-- Pilih Resep --</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-flex flex-column flex-lg-row gap-2">
                                    <div class="flex-fill">
                                        <input type="number" id="diskon_obatalkes" name="diskon_obatalkes" class="form-control form-control-sm" placeholder="Diskon (%)" autocomplete="off">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="d-grid d-lg-block w-auto">
                                        <button type="submit" id="addObatAlkesButton" class="btn btn-primary bg-gradient btn-sm text-nowrap">
                                            <i class="fa-solid fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body p-0 m-0 table-responsive">
                            <table class="table table-sm mb-0" style="width:100%; font-size: 0.75em;">
                                <thead>
                                    <tr class="align-middle">
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                                        <th scope="col" class="bg-body-secondary border-secondary col-resize" style="border-bottom-width: 2px; width: 100%;">Nama Obat dan Alkes</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Diskon</th>
                                        <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="align-top" id="list_obat_alkes">
                                    <tr>
                                        <td colspan="5" class="text-center">Memuat detail transaksi...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-body-tertiary">
                            <div class="row overflow-hidden d-flex align-items-end">
                                <div class="col fw-medium text-nowrap">Sub Total</div>
                                <div class="col text-end">
                                    <div class="date text-truncate placeholder-glow fw-bold" id="subtotal_obat_alkes">
                                        <span class="placeholder w-100"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-0 row g-1 overflow-hidden d-flex align-items-end">
                <div class="col fw-medium text-nowrap">Grand Total</div>
                <div class="col text-end">
                    <div class="fs-4 date text-truncate placeholder-glow" style="font-weight: 900;" id="total_pembayaran">
                        <span class="placeholder w-100"></span>
                    </div>
                </div>
            </div>
            <div class="mb-0 row g-1 overflow-hidden d-flex align-items-end">
                <div class="col fw-medium text-nowrap">Terima Uang</div>
                <div class="col text-end">
                    <div class="date text-truncate placeholder-glow" id="terima_uang_table">
                        <span class="placeholder w-100"></span>
                    </div>
                </div>
            </div>
            <div class="mb-0 row g-1 overflow-hidden d-flex align-items-end">
                <div class="col fw-medium text-nowrap">Uang Kembali</div>
                <div class="col text-end">
                    <div class="date text-truncate placeholder-glow" id="uang_kembali_table">
                        <span class="placeholder w-100"></span>
                    </div>
                </div>
            </div>
            <div class="mb-0 row g-1 overflow-hidden d-flex align-items-end">
                <div class="col fw-medium text-nowrap">Metode Bayar</div>
                <div class="col text-end">
                    <div class="date text-truncate placeholder-glow" id="metode_pembayaran_table">
                        <span class="placeholder w-100"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchLayananOptions(selectedLayanan = null) {
        try {
            const [rawatJalanList, pemeriksaanPenunjangList, OperasiList] = await Promise.all([
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Rawat%20Jalan' ?>'),
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Pemeriksaan%20Penunjang' ?>'),
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Operasi' ?>')
            ]);

            const rawatJalan = Array.isArray(rawatJalanList.data) ? rawatJalanList.data : [];
            const pemeriksaanPenunjang = Array.isArray(pemeriksaanPenunjangList.data) ? pemeriksaanPenunjangList.data : [];
            const Operasi = Array.isArray(OperasiList.data) ? OperasiList.data : [];

            const select = $('#id_layanan');

            // Kosongkan pilihan terlebih dahulu sebelum memuat ulang
            select.val('').trigger('change');

            // Hapus semua opsi kecuali placeholder
            select.empty().append('<option value="" disabled selected>-- Pilih Layanan --</option>');

            // Tambahkan optgroup dan opsi
            const rawatJalanGroup = $('<optgroup label="Rawat Jalan"></optgroup>');
            rawatJalan.forEach(option => {
                if (option.value && option.text) {
                    rawatJalanGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(rawatJalanGroup);

            const pemeriksaanGroup = $('<optgroup label="Pemeriksaan Penunjang"></optgroup>');
            pemeriksaanPenunjang.forEach(option => {
                if (option.value && option.text) {
                    pemeriksaanGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(pemeriksaanGroup);

            const operasiGroup = $('<optgroup label="Operasi"></optgroup>');
            Operasi.forEach(option => {
                if (option.value && option.text) {
                    operasiGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(operasiGroup);

            // Kembalikan pilihan sebelumnya jika ada
            if (selectedLayanan) {
                select.val(selectedLayanan).trigger('change');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan layanan.<br>' + error);
        }
    }

    async function fetchResepOptions(selectedResep = null) {
        try {
            const url = `<?= base_url('transaksi/reseplist/') . $transaksi['id_transaksi'] . '/' . $transaksi['nomor_registrasi'] ?>`;
            const response = await axios.get(url);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#id_resep');

                // Kosongkan pilihan terlebih dahulu sebelum memuat ulang
                select.val('').trigger('change');

                // Hapus semua opsi kecuali yang pertama
                select.find('option:not(:first)').remove();

                // Tambahkan opsi baru dari data yang diterima
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });

                // Kembalikan pilihan sebelumnya jika ada
                if (selectedResep) {
                    select.val(selectedResep).trigger('change');
                }
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan resep.<br>' + error);
        }
    }

    async function fetchStatusTransaksi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('transaksi/transaksi/') . $transaksi['id_transaksi'] ?>');

            const data = response.data;

            const total_pembayaran = parseInt(data.total_pembayaran);
            const terima_uang = parseInt(data.terima_uang);
            const uang_kembali = parseInt(data.uang_kembali);
            const bank = data.bank ? ` (${data.bank})` : ``;

            $('#total_pembayaran').text(`Rp${total_pembayaran.toLocaleString('id-ID')}`);
            $('#terima_uang_table').text(`Rp${terima_uang.toLocaleString('id-ID')}`);
            $('#uang_kembali_table').text(`Rp${uang_kembali.toLocaleString('id-ID')}`);
            $('#metode_pembayaran_table').html(data.metode_pembayaran + bank);
            $('#total_pembayaran_modal').text(`Rp${total_pembayaran.toLocaleString('id-ID')}`);

            if (data.dokter === "Resep Luar") {
                $('.form-tindakan').prop('disabled', true);
                $('#addLayananButton').prop('disabled', true);
            }

            // Cek status `lunas`
            if (data.lunas === "1") {
                $('#tambahLayananContainer').hide();
                $('#tambahObatAlkesContainer').hide();
            } else if (data.lunas === "0") {
                $('#tambahLayananContainer').show();
                $('#tambahObatAlkesContainer').show();
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchLayanan() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('transaksi/detaillayananlist/') . $transaksi['id_transaksi'] ?>');

            const data = response.data;
            $('#list_layanan').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada tindakan yang ditransaksikan</td>
                    </tr>
                `;
                $('#list_layanan').append(emptyRow);
                $('#processBtn').prop('disabled', true);
            } else {
                data.forEach(function(layanan) {
                    const diskon = parseInt(layanan.diskon); // Konversi jumlah ke integer
                    const qty_transaksi = parseInt(layanan.qty_transaksi);
                    const harga_transaksi = parseInt(layanan.harga_transaksi); // Konversi harga satuan ke integer
                    const total_pembayaran = (harga_transaksi * qty_transaksi) * (1 - (diskon / 100)); // Hitung total harga
                    totalPembayaran += total_pembayaran;
                    const tindakanElement = `
                        <tr>
                            <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient  edit-layanan-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${layanan.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${layanan.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td>
                            <span>${layanan.nama_layanan}</span>
                        </td>
                        <td class="date text-end">${qty_transaksi.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_transaksi.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${diskon.toLocaleString('id-ID')}%</td>
                        <td class="date text-end">Rp${total_pembayaran.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    $('#list_layanan').append(tindakanElement);
                    if (layanan.dokter === "Resep Luar") {
                        $('.edit-layanan-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    } else {
                        $('.edit-layanan-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                    }
                    // Cek status `lunas`
                    if (layanan.lunas === "1") {
                        $('.edit-layanan-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    } else if (layanan.lunas === "0") {
                        $('.edit-layanan-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                    }
                });
            }
            const totalPembayaranElement = `Rp${totalPembayaran.toLocaleString('id-ID')}`;
            $('#subtotal_layanan').text(totalPembayaranElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#list_layanan').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchObatAlkes() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('transaksi/detailobatalkeslist/') . $transaksi['id_transaksi'] ?>');

            const data = response.data;
            $('#list_obat_alkes').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada resep yang akan ditransaksikan</td>
                    </tr>
                `;
                $('#list_obat_alkes').append(emptyRow);
                $('#processBtn').prop('disabled', true);
            } else {
                data.forEach(function(obat_alkes) {
                    const diskon = parseInt(obat_alkes.diskon); // Konversi jumlah ke integer
                    const qty_transaksi = parseInt(obat_alkes.qty_transaksi);
                    const harga_transaksi = parseInt(obat_alkes.harga_transaksi); // Konversi harga satuan ke integer
                    const total_pembayaran = Math.round((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))); // Hitung total harga
                    totalPembayaran += total_pembayaran;
                    const dokter = obat_alkes.resep.dokter == `Resep Luar` ? `` : ` <span><small><strong>Dokter</strong>: ${obat_alkes.resep.dokter}</small></span>`;
                    const tindakanElement = `
                        <tr>
                            <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient  edit-obatalkes-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${obat_alkes.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient  delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${obat_alkes.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td>
                            <ol class="ps-0 mb-0 custom-counter" id="obat-${obat_alkes.id_detail_transaksi}">
                            </ol>
                            ${dokter}
                        </td>
                        <td class="date text-end">Rp${harga_transaksi.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${diskon.toLocaleString('id-ID')}%</td>
                        <td class="date text-end">Rp${total_pembayaran.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    $('#list_obat_alkes').append(tindakanElement);
                    // Iterasi untuk setiap resep
                    obat_alkes.resep.detail_resep.forEach(function(detail_resep) {
                        const jumlah = parseInt(detail_resep.jumlah); // Konversi jumlah ke integer
                        const harga_satuan = parseInt(detail_resep.harga_satuan); // Konversi harga satuan ke integer
                        const total_harga = Math.round((jumlah * harga_satuan) * (1 - (diskon / 100))); // Hitung total harga

                        const obat_alkesElement = `
                                <li>${detail_resep.nama_obat}<br><small>${jumlah.toLocaleString('id-ID')} × Rp${harga_satuan.toLocaleString('id-ID')} × ${diskon}% = Rp${total_harga.toLocaleString('id-ID')}</small></li>
                            `;

                        $(`#obat-${obat_alkes.id_detail_transaksi}`).append(obat_alkesElement);
                    });
                    // Cek status `lunas`
                    if (obat_alkes.lunas === "1") {
                        $('.edit-obatalkes-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    } else if (obat_alkes.lunas === "0") {
                        $('.edit-obatalkes-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                    }
                });
            }
            const totalPembayaranElement = `Rp${totalPembayaran.toLocaleString('id-ID')}`;
            $('#subtotal_obat_alkes').text(totalPembayaranElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('.col-resize').css('min-width', '0');
            $('#list_obat_alkes').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);
            if (data.update) {
                console.log("Received update from WebSocket");
                const selectedLayanan = $('#id_layanan').val();
                await fetchLayananOptions(selectedLayanan);
                await fetchLayanan();
                await fetchResepOptions();
                await fetchObatAlkes();
                fetchStatusTransaksi();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_layanan').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        $('#id_resep').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        var detailTransaksiId;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            detailTransaksiId = $(this).data('id');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus item ini?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Menghapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/transaksi/hapusdetailtransaksi') ?>/${detailTransaksiId}`);
                const selectedLayanan = $('#id_layanan').val();
                await fetchLayananOptions(selectedLayanan);
                await fetchLayanan();
                await fetchResepOptions();
                await fetchObatAlkes();
                fetchStatusTransaksi();

            } catch (error) {
                if (error.response.request.status === 400) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $(document).on('click', '.edit-layanan-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span>`);
            $('#editLayananTransaksi').remove();
            $('#editObatAlkesTransaksi').remove();
            try {
                const response = await axios.get(`<?= base_url('/transaksi/detailtransaksiitem') ?>/${id}`);
                const formHtml = `
                <tr id="editLayananTransaksi">
                    <td colspan="6">
                        <form id="editLayanan" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Layanan</div>
                                <button id="editLayananCloseBtn" type="button" class="text-end btn-close ms-auto"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <input type="hidden" id="id_detail_transaksi" name="id_detail_transaksi" value="${response.data.id_detail_transaksi}">
                                <div class="flex-fill">
                                    <input type="number" id="qty_transaksi_edit" name="qty_transaksi_edit" class="form-control form-control-sm" placeholder="Diskon (%)" value="${response.data.qty_transaksi}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="number" id="diskon_layanan_edit" name="diskon_layanan_edit" class="form-control form-control-sm" placeholder="Diskon (%)" value="${response.data.diskon}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="editLayananButton" class="btn btn-primary bg-gradient btn-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                `;
                // Append the new row with the form directly after the current data row
                $row.after(formHtml);

                // Handle form submission
                $('#editLayanan').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#editLayanan .is-invalid').removeClass('is-invalid');
                    $('#editLayanan .invalid-feedback').text('').hide();
                    $('#editLayananButton').prop('disabled', true).html(`
                        <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editLayanan input, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/transaksi/perbaruilayanan/' . $transaksi['id_transaksi']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editLayanan')[0].reset();
                            $('#editLayanan .is-invalid').removeClass('is-invalid');
                            $('#editLayanan .invalid-feedback').text('').hide();
                            $('#editLayananTransaksi').remove();
                            const selectedLayanan = $('#id_layanan').val();
                            await fetchLayananOptions(selectedLayanan);
                            await fetchLayanan();
                            fetchStatusTransaksi();

                        } else {
                            console.log("Validation Errors:", response.data.errors);

                            // Clear previous validation states
                            $('#editLayanan .is-invalid').removeClass('is-invalid');
                            $('#editLayanan .invalid-feedback').text('').hide();

                            // Display new validation errors
                            for (const field in response.data.errors) {
                                if (response.data.errors.hasOwnProperty(field)) {
                                    const fieldElement = $('#' + field);
                                    const feedbackElement = fieldElement.siblings('.invalid-feedback');

                                    console.log("Target Field:", fieldElement);
                                    console.log("Target Feedback:", feedbackElement);

                                    if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                        fieldElement.addClass('is-invalid');
                                        feedbackElement.text(response.data.errors[field]).show();

                                        // Remove error message when the user corrects the input
                                        fieldElement.on('input change', function() {
                                            $(this).removeClass('is-invalid');
                                            $(this).siblings('.invalid-feedback').text('').hide();
                                        });
                                    } else {
                                        console.warn("Elemen tidak ditemukan pada field:", field);
                                    }
                                }
                            }
                            console.error('Perbaiki kesalahan pada formulir.');
                        }
                    } catch (error) {
                        if (error.response.request.status === 400 || error.response.request.status === 422) {
                            showFailedToast(error.response.data.message);
                        } else {
                            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        }
                    } finally {
                        $('#editLayananButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        `);
                        $('#editLayanan input, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('#editLayananCloseBtn').on('click', function() {
                    $('#editLayananTransaksi').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $(document).on('click', '.edit-obatalkes-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span>`);
            $('#editLayananTransaksi').remove();
            $('#editObatAlkesTransaksi').remove();
            try {
                const response = await axios.get(`<?= base_url('/transaksi/detailtransaksiitem') ?>/${id}`);
                const formHtml = `
                <tr id="editObatAlkesTransaksi">
                    <td colspan="5">
                        <form id="editObatAlkes" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Diskon (%)</div>
                                <button id="editObatAlkesCloseBtn" type="button" class="text-end btn-close ms-auto"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <input type="hidden" id="id_detail_transaksi" name="id_detail_transaksi" value="${response.data.id_detail_transaksi}">
                                <div class="flex-fill">
                                    <input type="number" id="diskon_obatalkes_edit" name="diskon_obatalkes_edit" class="form-control form-control-sm" placeholder="Diskon (%)" value="${response.data.diskon}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="editObatAlkesButton" class="btn btn-primary bg-gradient btn-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                `;
                // Append the new row with the form directly after the current data row
                $row.after(formHtml);

                // Handle form submission
                $('#editObatAlkes').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#editObatAlkes .is-invalid').removeClass('is-invalid');
                    $('#editObatAlkes .invalid-feedback').text('').hide();
                    $('#editObatAlkesButton').prop('disabled', true).html(`
                        <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editObatAlkes input, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/transaksi/perbaruiobatalkes/' . $transaksi['id_transaksi']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editObatAlkes')[0].reset();
                            $('#editObatAlkes .is-invalid').removeClass('is-invalid');
                            $('#editObatAlkes .invalid-feedback').text('').hide();
                            $('#editObatAlkesTransaksi').remove();
                            await fetchResepOptions();
                            await fetchObatAlkes();
                            fetchStatusTransaksi();

                        } else {
                            console.log("Validation Errors:", response.data.errors);

                            // Clear previous validation states
                            $('#editObatAlkes .is-invalid').removeClass('is-invalid');
                            $('#editObatAlkes .invalid-feedback').text('').hide();

                            // Display new validation errors
                            for (const field in response.data.errors) {
                                if (response.data.errors.hasOwnProperty(field)) {
                                    const fieldElement = $('#' + field);
                                    const feedbackElement = fieldElement.siblings('.invalid-feedback');

                                    console.log("Target Field:", fieldElement);
                                    console.log("Target Feedback:", feedbackElement);

                                    if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                        fieldElement.addClass('is-invalid');
                                        feedbackElement.text(response.data.errors[field]).show();

                                        // Remove error message when the user corrects the input
                                        fieldElement.on('input change', function() {
                                            $(this).removeClass('is-invalid');
                                            $(this).siblings('.invalid-feedback').text('').hide();
                                        });
                                    } else {
                                        console.warn("Elemen tidak ditemukan pada field:", field);
                                    }
                                }
                            }
                            console.error('Perbaiki kesalahan pada formulir.');
                        }
                    } catch (error) {
                        if (error.response.request.status === 400) {
                            showFailedToast(error.response.data.message);
                        } else {
                            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        }
                    } finally {
                        $('#editObatAlkesButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        `);
                        $('#editObatAlkes input, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('#editObatAlkesCloseBtn').on('click', function() {
                    $('#editObatAlkesTransaksi').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $('#tambahLayanan').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#tambahLayanan .is-invalid').removeClass('is-invalid');
            $('#tambahLayanan .invalid-feedback').text('').hide();
            $('#addLayananButton').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#tambahLayanan input, #tambahLayanan select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/transaksi/tambahlayanan/' . $transaksi['id_transaksi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#tambahLayanan')[0].reset();
                    $('#id_layanan').val(null).trigger('change');
                    $('#qty_transaksi').val('');
                    $('#diskon_layanan').val('');
                    $('#tambahLayanan .is-invalid').removeClass('is-invalid');
                    $('#tambahLayanan .invalid-feedback').text('').hide();
                    const selectedLayanan = $('#id_layanan').val();
                    await fetchLayananOptions(selectedLayanan);
                    await fetchLayanan();
                    fetchStatusTransaksi();

                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#tambahLayanan .is-invalid').removeClass('is-invalid');
                    $('#tambahLayanan .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
                            const feedbackElement = fieldElement.siblings('.invalid-feedback');

                            console.log("Target Field:", fieldElement);
                            console.log("Target Feedback:", feedbackElement);

                            if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                fieldElement.addClass('is-invalid');
                                feedbackElement.text(response.data.errors[field]).show();

                                // Remove error message when the user corrects the input
                                fieldElement.on('input change', function() {
                                    $(this).removeClass('is-invalid');
                                    $(this).siblings('.invalid-feedback').text('').hide();
                                });
                            } else {
                                console.warn("Elemen tidak ditemukan pada field:", field);
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                if (error.response.request.status === 400 || error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#addLayananButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#tambahLayanan input, #tambahLayanan select').prop('disabled', false);
            }
        });

        $('#tambahObatAlkes').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#tambahObatAlkes .is-invalid').removeClass('is-invalid');
            $('#tambahObatAlkes .invalid-feedback').text('').hide();
            $('#addObatAlkesButton').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#tambahObatAlkes input, #tambahObatAlkes select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/transaksi/tambahobatalkes/' . $transaksi['id_transaksi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#tambahObatAlkes')[0].reset();
                    $('#id_resep').val(null).trigger('change');
                    $('#diskon_obatalkes').val('');
                    $('#tambahObatAlkes .is-invalid').removeClass('is-invalid');
                    $('#tambahObatAlkes .invalid-feedback').text('').hide();
                    await fetchResepOptions();
                    await fetchObatAlkes();
                    fetchStatusTransaksi();

                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#tambahObatAlkes .is-invalid').removeClass('is-invalid');
                    $('#tambahObatAlkes .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
                            const feedbackElement = fieldElement.siblings('.invalid-feedback');

                            console.log("Target Field:", fieldElement);
                            console.log("Target Feedback:", feedbackElement);

                            if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                fieldElement.addClass('is-invalid');
                                feedbackElement.text(response.data.errors[field]).show();

                                // Remove error message when the user corrects the input
                                fieldElement.on('input change', function() {
                                    $(this).removeClass('is-invalid');
                                    $(this).siblings('.invalid-feedback').text('').hide();
                                });
                            } else {
                                console.warn("Elemen tidak ditemukan pada field:", field);
                            }
                        }
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                if (error.response.request.status === 400) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#addObatAlkesButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#tambahObatAlkes input, #tambahObatAlkes select').prop('disabled', false);
            }
        });

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                const selectedLayanan = $('#id_layanan').val();
                await fetchLayananOptions(selectedLayanan);
                await fetchLayanan();
                await fetchResepOptions();
                await fetchObatAlkes();
                fetchStatusTransaksi();

            }
        });

        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            const selectedLayanan = $('#id_layanan').val();
            await fetchLayananOptions(selectedLayanan);
            await fetchLayanan();
            await fetchResepOptions();
            await fetchObatAlkes();
            fetchStatusTransaksi();

        });
        const selectedLayanan = $('#id_layanan').val();
        await fetchLayananOptions(selectedLayanan);
        await fetchLayanan();
        await fetchResepOptions();
        await fetchObatAlkes();
        fetchStatusTransaksi();

    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>