<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
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
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">

    <fieldset class="border rounded-3 px-2 py-0 mb-3">
        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Informasi Transaksi</legend>
        <div class="row">
            <div class="col-lg-6" style="font-size: 9pt;">
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Nama Pasien</div>
                    <div class="col-lg">
                        <div>
                            <?= ($transaksi['nama_pasien'] == NULL) ? '<em>Anonim</em>' : $transaksi['nama_pasien']; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Nomor Rekam Medis</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= $transaksi['no_rm'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Alamat</div>
                    <div class="col-lg">
                        <div>
                            <?= $transaksi['alamat'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Nomor HP</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= $transaksi['telpon'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" style="font-size: 9pt;">
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Nomor Kwitansi</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= $transaksi['no_kwitansi'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Tanggal dan Waktu</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= $transaksi['tgl_transaksi'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Kasir</div>
                    <div class="col-lg">
                        <div>
                            <?= $transaksi['kasir'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-4 fw-medium">Dokter</div>
                    <div class="col-lg">
                        <div>
                            <?= $transaksi['dokter'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="row">
        <div class="col-lg-6 add-forms">
            <fieldset id="tambahLayananContainer" class="border rounded-3 px-2 py-0 h-100" style="display: none;">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Tambah Tindakan</legend>
                <form id="tambahLayanan" enctype="multipart/form-data">
                    <div class="mb-2">
                        <select class="form-select rounded-3" id="id_layanan" name="id_layanan" aria-label="id_layanan">
                            <option value="" disabled selected>-- Pilih Tindakan --</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row mb-2 gap-2">
                        <div class="flex-fill">
                            <input type="number" id="qty_transaksi" name="qty_transaksi" class="form-control rounded-3" placeholder="Qty" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="flex-fill">
                            <input type="number" id="diskon_layanan" name="diskon_layanan" class="form-control rounded-3" placeholder="Diskon (%)" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-grid d-lg-block w-auto">
                            <button type="submit" id="addLayananButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="col-lg-6 add-forms">
            <fieldset id="tambahObatAlkesContainer" class="border rounded-3 px-2 py-0 h-100" style="display: none;">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Tambah Obat dan Alkes</legend>
                <form id="tambahObatAlkes" enctype="multipart/form-data">
                    <div class="mb-2">
                        <select class="form-select rounded-3" id="id_resep" name="id_resep" aria-label="id_resep">
                            <option value="" disabled selected>-- Pilih Resep --</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row mb-2 gap-2">
                        <div class="flex-fill">
                            <input type="number" id="diskon_obatalkes" name="diskon_obatalkes" class="form-control rounded-3" placeholder="Diskon (%)" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-grid d-lg-block w-auto">
                            <button type="submit" id="addObatAlkesButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 100%;">Nama Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Qty</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Diskon</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pembayaran</th>
                </tr>
            </thead>
            <tbody class="align-top" id="list_layanan">
                <tr>
                    <td colspan="6" class="text-center">Memuat detail transaksi...</td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;" colspan="5">Sub Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="subtotal_layanan"></th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="my-3"></div>

    <div class="table-responsive">
        <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary min-width-column" style="border-bottom-width: 2px; width: 100%;">Nama Obat dan Alkes</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Diskon</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pembayaran</th>
                </tr>
            </thead>
            <tbody class="align-top" id="list_obat_alkes">
                <tr>
                    <td colspan="5" class="text-center">Memuat detail transaksi...</td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;" colspan="4">Sub Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="subtotal_obat_alkes"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;" colspan="4">Grand Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" id="total_pembayaran"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;" colspan="4">Terima Uang</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" id="terima_uang_table"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;" colspan="4">Uang Kembali</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" id="uang_kembali_table"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;" colspan="4">Metode Bayar</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" id="metode_pembayaran_table"></th>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="prosesTransaksi">
        <hr>
        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
            <button class="btn btn-body rounded-3 bg-gradient" type="button" id="printBtn" onclick="window.open(`<?= base_url('/transaksi/struk/' . $transaksi['id_transaksi']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Struk/Kwitansi</button>
            <button class="btn btn-danger rounded-3 bg-gradient" type="button" id="cancelBtn" data-id="<?= $transaksi['id_transaksi'] ?>" disabled><i class="fa-solid fa-xmark"></i> Batalkan Transaksi</button>
            <button class="btn btn-success rounded-3 bg-gradient" type="button" id="processBtn" data-id="<?= $transaksi['id_transaksi'] ?>" disabled><i class="fa-solid fa-money-bills"></i> Proses Transaksi</button>
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

    <div class="modal fade" id="transaksiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="transaksiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="transaksiForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="transaksiModalLabel" style="font-weight: bold;"></h6>
                    <button id="transaksiCloseBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <div id="mediaAlert" class="alert alert-info rounded-3 mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <p>Pastikan Anda telah memasukkan nominal transaksi sesuai dengan grand total yang diminta dan telah menyelesaikan proses pembayaran.</p>
                                <p>Grand Total:<br><span id="total_pembayaran_modal" class="date fs-4" style="font-weight: 900;"></span></p>
                                <p class="mb-0">Jika uang yang diterima melebihi grand total, maka akan ada uang kembali.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="terima_uang" id="terima_uang" name="terima_uang">
                        <label for="terima_uang">Terima Uang (Rp)*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="metode_pembayaran" name="metode_pembayaran" aria-label="metode_pembayaran">
                            <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS/Transfer Bank">QRIS/Transfer Bank</option>
                        </select>
                        <label for="metode_pembayaran">Metode Pembayaran*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mb-1 mt-1" id="bank_field" style="display: none;">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="bank" id="bank" name="bank" list="bank_list">
                        <label for="bank">Bank/E-wallet*</label>
                        <div class="invalid-feedback"></div>
                        <datalist id="bank_list">
                            <option value="BNI">
                            <option value="BRI">
                            <option value="BTN">
                            <option value="Mandiri">
                            <option value="BSI">
                            <option value="BCA">
                            <option value="CIMB Niaga">
                            <option value="Permata">
                            <option value="Danamon">
                            <option value="OCBC NISP">
                            <option value="Maybank Indonesia">
                            <option value="BRK Syariah">
                            <option value="OVO">
                            <option value="GoPay">
                            <option value="DANA">
                            <option value="LinkAja">
                        </datalist>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="submitButton" class="btn btn-primary bg-gradient rounded-3">
                        <i class="fa-solid fa-money-bill-transfer"></i> Proses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="batalTransaksiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="batalTransaksiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable rounded-3">
            <form id="batalTransaksiForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="batalTransaksiModalLabel" style="font-weight: bold;"></h6>
                    <button id="batalTransaksiCloseBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <div class="alert alert-warning rounded-3 mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <h4 style="font-weight: 900;">PERINGATAN!</h4>
                                <p>Pastikan Anda telah mendapatkan persetujuan dari pimpinan atau manajer klinik untuk membatalkan transaksi ini. Pembatalan transaksi <strong>HANYA DILAKUKAN</strong> apabila terjadi kesalahan dalam memasukkan item transaksi.</p>
                                <p class="mb-0">Segala penyalahgunaan dalam pembatalan transaksi ini akan diproses secara hukum.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="password" class="form-control" autocomplete="off" dir="auto" placeholder="password" id="password" name="password">
                        <label for="password">Masukkan Kata Sandi Transaksi*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="submit" id="cancelSubmitButton" class="btn btn-danger bg-gradient rounded-3">
                        <i class="fa-solid fa-xmark"></i> Batalkan
                    </button>
                </div>
            </form>
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
    async function fetchTindakanOptions() {
        try {
            const [rawatJalanList, pemeriksaanPenunjangList, OperasiList] = await Promise.all([
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Rawat%20Jalan' ?>'),
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Pemeriksaan%20Penunjang' ?>'),
                axios.get('<?= base_url('transaksi/layananlist/') . $transaksi['id_transaksi'] . '/Operasi' ?>')
            ]);

            // Ensure to access the correct property containing the array
            const rawatJalan = Array.isArray(rawatJalanList.data) ? rawatJalanList.data : [];
            const pemeriksaanPenunjang = Array.isArray(pemeriksaanPenunjangList.data) ? pemeriksaanPenunjangList.data : [];
            const Operasi = Array.isArray(OperasiList.data) ? OperasiList.data : [];

            const select = $('#id_layanan');

            // Clear existing options except the first one (the placeholder)
            select.empty().append('<option value="" disabled selected>-- Pilih Tindakan --</option>');

            // Create optgroup for Rawat Jalan
            const rawatJalanGroup = $('<optgroup label="Rawat Jalan"></optgroup>');
            rawatJalan.forEach(option => {
                if (option.value && option.text) {
                    rawatJalanGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(rawatJalanGroup);

            // Create optgroup for Pemeriksaan Penunjang
            const pemeriksaanGroup = $('<optgroup label="Pemeriksaan Penunjang"></optgroup>');
            pemeriksaanPenunjang.forEach(option => {
                if (option.value && option.text) {
                    pemeriksaanGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(pemeriksaanGroup);

            // Create optgroup for Operasi
            const operasiGroup = $('<optgroup label="Operasi"></optgroup>');
            Operasi.forEach(option => {
                if (option.value && option.text) {
                    operasiGroup.append(`<option value="${option.value}">${option.text}</option>`);
                }
            });
            select.append(operasiGroup);
        } catch (error) {
            showFailedToast('Gagal mendapatkan layanan.<br>' + error);
        }
    }

    async function fetchResepOptions() {
        try {
            <?php if ($transaksi['nomor_registrasi'] == NULL || $transaksi['no_rm'] == NULL || $transaksi['telpon'] == NULL || $transaksi['tanggal_lahir'] == NULL) : ?>
                const url = `<?= base_url('transaksi/reseplistexternal/') . $transaksi['id_transaksi'] . '/' . $transaksi['id_resep'] ?>`;
            <?php else : ?>
                const url = `<?= base_url('transaksi/reseplist/') . $transaksi['id_transaksi'] . '/' . $transaksi['nomor_registrasi'] ?>`;
            <?php endif; ?>
            const response = await axios.get(url);

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#id_resep');

                // Clear existing options except the first one
                select.find('option:not(:first)').remove();

                // Loop through the options and append them to the select element
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
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
            $('#metode_pembayaran_table').text(data.metode_pembayaran + bank);
            $('#total_pembayaran_modal').text(`Rp${total_pembayaran.toLocaleString('id-ID')}`);

            // Cek status `lunas`
            if (data.lunas === "1") {
                $('div.add-forms').removeClass('mb-3');
                $('#tambahLayananContainer').hide();
                $('#tambahObatAlkesContainer').hide();
                $('#printBtn').prop('disabled', false);
                $('#cancelBtn').prop('disabled', false);
            } else if (data.lunas === "0") {
                $('div.add-forms').addClass('mb-3');
                $('#tambahLayananContainer').show();
                $('#tambahObatAlkesContainer').show();
                $('#printBtn').prop('disabled', true);
                $('#cancelBtn').prop('disabled', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function transactionProcessBtn() {
        try {
            const [layananResponse, obatalkesResponse] = await Promise.all([
                axios.get('<?= base_url('transaksi/detaillayananlist/') . $transaksi['id_transaksi'] ?>'),
                axios.get('<?= base_url('transaksi/detailobatalkeslist/') . $transaksi['id_transaksi'] ?>')
            ]);
            const layanan = layananResponse.data;
            const obatalkes = obatalkesResponse.data;
            layanan.forEach(function(layanan) {
                const layananLunas = layanan.lunas;
                if (layananLunas === "1") {
                    $('#processBtn').prop('disabled', true);
                } else if (layananLunas === "0") {
                    $('#processBtn').prop('disabled', false);
                }
            });
            obatalkes.forEach(function(obatalkes) {
                const obatalkesLunas = obatalkes.lunas;
                if (obatalkesLunas === "1") {
                    $('#processBtn').prop('disabled', true);
                } else if (obatalkesLunas === "0") {
                    $('#processBtn').prop('disabled', false);
                }
            });
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
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
                                <button class="btn btn-outline-body text-nowrap bg-gradient rounded-start-3 edit-layanan-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${layanan.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${layanan.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
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
                    const dokter = obat_alkes.resep.dokter == null ? `Resep Luar` : obat_alkes.resep.dokter;
                    const tindakanElement = `
                        <tr>
                            <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient rounded-start-3 edit-obatalkes-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${obat_alkes.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${obat_alkes.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td>
                            <span>${dokter}</span>
                            <ol class="mb-0" id="obat-${obat_alkes.id_detail_transaksi}">
                            </ol>
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
                                <li>${detail_resep.nama_obat}<br><small>${detail_resep.kategori_obat} • ${detail_resep.bentuk_obat} • ${detail_resep.signa} • ${detail_resep.cara_pakai} • ${jumlah.toLocaleString('id-ID')} × Rp${harga_satuan.toLocaleString('id-ID')} × ${diskon}% = Rp${total_harga.toLocaleString('id-ID')}<br>${detail_resep.catatan}</small></li>
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
            $('#list_obat_alkes').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_layanan').select2({
            dropdownParent: $('#tambahLayanan'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        $('#id_resep').select2({
            dropdownParent: $('#tambahObatAlkes'),
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
                fetchLayanan();
                fetchObatAlkes();
                fetchTindakanOptions();
                fetchResepOptions();
                fetchStatusTransaksi();
                transactionProcessBtn();
            } catch (error) {
                if (error.response.request.status === 401) {
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
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editLayananTransaksi').remove();
            $('#editObatAlkesTransaksi').remove();
            try {
                const response = await axios.get(`<?= base_url('/transaksi/detailtransaksiitem') ?>/${id}`);
                const formHtml = `
                <tr id="editLayananTransaksi">
                    <td colspan="6">
                        <form id="editLayanan" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Tindakan</div>
                                <button id="editLayananCloseBtn" type="button" class="text-end btn-close ms-auto"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <input type="hidden" id="id_detail_transaksi" name="id_detail_transaksi" value="${response.data.id_detail_transaksi}">
                                <div class="flex-fill">
                                    <input type="number" id="qty_transaksi_edit" name="qty_transaksi_edit" class="form-control rounded-3" placeholder="Diskon (%)" value="${response.data.qty_transaksi}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="number" id="diskon_layanan_edit" name="diskon_layanan_edit" class="form-control rounded-3" placeholder="Diskon (%)" value="${response.data.diskon}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="editLayananButton" class="btn btn-primary bg-gradient rounded-3">
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
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Edit
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
                            fetchLayanan();
                            fetchObatAlkes();
                            fetchTindakanOptions();
                            fetchResepOptions();
                            fetchStatusTransaksi();
                            transactionProcessBtn();
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
                        if (error.response.request.status === 401) {
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
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
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
                                    <input type="number" id="diskon_obatalkes_edit" name="diskon_obatalkes_edit" class="form-control rounded-3" placeholder="Diskon (%)" value="${response.data.diskon}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="editObatAlkesButton" class="btn btn-primary bg-gradient rounded-3">
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
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Edit
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
                            fetchLayanan();
                            fetchObatAlkes();
                            fetchTindakanOptions();
                            fetchResepOptions();
                            fetchStatusTransaksi();
                            transactionProcessBtn();
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
                        if (error.response.request.status === 401) {
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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
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
                    fetchLayanan();
                    fetchObatAlkes();
                    fetchTindakanOptions();
                    fetchStatusTransaksi();
                    transactionProcessBtn();
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
                if (error.response.request.status === 401) {
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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
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
                    fetchLayanan();
                    fetchObatAlkes();
                    fetchResepOptions();
                    fetchStatusTransaksi();
                    transactionProcessBtn();
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
                if (error.response.request.status === 401) {
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

        $('#processBtn').click(function() {
            $('#transaksiModalLabel').text('Proses Transaksi');
            $('#transaksiModal').modal('show');
        });

        $('#cancelBtn').click(function() {
            $('#batalTransaksiModalLabel').text('Batalkan Transaksi');
            $('#batalTransaksiModal').modal('show');
        });

        $('#transaksiModal').on('shown.bs.modal', function() {
            $('#terima_uang').trigger('focus');
        });

        $('#batalTransaksiModal').on('shown.bs.modal', function() {
            $('#password').trigger('focus');
        });

        $('#transaksiForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#transaksiForm .is-invalid').removeClass('is-invalid');
            $('#transaksiForm .invalid-feedback').text('').hide();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses...</span>
            `);

            // Disable form inputs
            $('#transaksiForm input, #transaksiForm select, #closeBtn').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/transaksi/process/' . $transaksi['id_transaksi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#transaksiModal').modal('hide');
                    fetchLayanan();
                    fetchObatAlkes();
                    fetchStatusTransaksi();
                    transactionProcessBtn();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#transaksiForm .is-invalid').removeClass('is-invalid');
                    $('#transaksiForm .invalid-feedback').text('').hide();

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
                if (error.response.request.status === 422 || error.response.request.status === 402) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#transaksiForm input, #transaksiForm select, #closeBtn').prop('disabled', false);
            }
        });

        $('#batalTransaksiForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#batalTransaksiForm .is-invalid').removeClass('is-invalid');
            $('#batalTransaksiForm .invalid-feedback').text('').hide();
            $('#cancelSubmitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Membatalkan...</span>
            `);

            // Disable form inputs
            $('#batalTransaksiForm input, #batalTransaksiCloseBtn').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/transaksi/cancel/' . $transaksi['id_transaksi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#batalTransaksiModal').modal('hide');
                    fetchLayanan();
                    fetchObatAlkes();
                    fetchStatusTransaksi();
                    transactionProcessBtn();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#batalTransaksiForm .is-invalid').removeClass('is-invalid');
                    $('#batalTransaksiForm .invalid-feedback').text('').hide();

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
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#cancelSubmitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-xmark"></i> Batalkan
                `);
                $('#batalTransaksiForm input, #batalTransaksiCloseBtn').prop('disabled', false);
            }
        });

        // Fungsi untuk memunculkan/menghilangkan field Bank berdasarkan metode pembayaran
        function toggleBankField() {
            let metode = $('#metode_pembayaran').val();
            if (metode === 'Tunai') {
                $('#bank').val(''); // Kosongkan field bank
                $('#bank_field').hide(); // Hilangkan field bank
                // Hilangkan form validation
                $('#bank').removeClass('is-invalid');
                $('#bank').siblings('.invalid-feedback').text('').hide();
            } else if (metode === 'QRIS/Transfer Bank') {
                $('#bank_field').show(); // Munculkan field bank
            } else {
                $('#bank').val(''); // Kosongkan field bank
                $('#bank_field').hide(); // Hilangkan field bank
                // Hilangkan form validation
                $('#bank').removeClass('is-invalid');
                $('#bank').siblings('.invalid-feedback').text('').hide();
            }
        }

        // Event listener ketika dropdown metode pembayaran berubah
        $('#metode_pembayaran').on('change', function() {
            toggleBankField();
        });

        $('#transaksiModal').on('hidden.bs.modal', function() {
            $('#transaksiForm')[0].reset();
            $('#terima_uang').val('');
            $('#metode_pembayaran').val('').change(); // Trigger change agar toggleBankField dipanggil
            $('#bank').val(''); // Kosongkan field bank
            $('#bank_field').hide(); // Reset bank dan hilangkan
            $('#transaksiForm .is-invalid').removeClass('is-invalid');
            $('#transaksiForm .invalid-feedback').text('').hide();
        });

        $('#batalTransaksiModal').on('hidden.bs.modal', function() {
            $('#batalTransaksiForm')[0].reset();
            $('#batalTransaksiForm .is-invalid').removeClass('is-invalid');
            $('#batalTransaksiForm .invalid-feedback').text('').hide();
        });
        toggleBankField();
        fetchLayanan();
        fetchObatAlkes();
        fetchTindakanOptions();
        fetchResepOptions();
        fetchStatusTransaksi();
        transactionProcessBtn();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>