<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
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

    <fieldset class="border rounded-3 px-2 py-0 mb-3">
        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Informasi Transaksi</legend>
        <div style="font-size: 9pt;">
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Tanggal dan Waktu</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $transaksi['tgl_transaksi'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Nama Pasien</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $transaksi['nama_pasien'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Nomor MR</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $transaksi['no_mr'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Nomor Registrasi</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $transaksi['no_registrasi'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Kasir</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $transaksi['fullname'] ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset id="tambahDetailContainer" class="border rounded-3 px-2 py-0 mb-3" style="display: none;">
        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Tambah Detail Transaksi</legend>
        <form id="tambahDetail" enctype="multipart/form-data">
            <div class="mb-2">
                <select class="form-select rounded-3" id="id_resep" name="id_resep" aria-label="id_resep">
                    <option value="" disabled selected>-- Pilih Resep --</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="d-flex flex-column flex-lg-row mb-2 gap-2">
                <div class="flex-fill">
                    <input type="number" id="diskon" name="diskon" class="form-control rounded-3" placeholder="Diskon (%)">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="d-grid d-lg-block w-auto">
                    <button type="submit" id="addButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
        </form>
    </fieldset>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Resep</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Diskon</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Pembayaran</th>
                </tr>
            </thead>
            <tbody class="align-top" id="detail_transaksi">
                <tr>
                    <td colspan="5" class="text-center">Memuat detail transaksi...</td>
                </tr>
            </tbody>
            <tbody class="align-top">
            </tbody>
            <thead>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 2px;" colspan="1"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;">Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" colspan="3" id="total_pembayaran"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 0px;" colspan="1"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;">Terima Uang</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" colspan="3" id="terima_uang_table"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 0px;" colspan="1"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;">Uang Kembali</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" colspan="3" id="uang_kembali_table"></th>
                </tr>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 0px;" colspan="1"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 0px;">Metode Bayar</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 0px;" colspan="3" id="metode_pembayaran_table"></th>
                </tr>
            </thead>
        </table>
    </div>

    <div id="prosesTransaksi">
        <hr>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
            <button class="btn btn-primary rounded-3 bg-gradient" type="button" id="printBtn" onclick="window.open(`<?= base_url('/transaksi/struk/' . $transaksi['id_transaksi']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Struk</button>
            <button class="btn btn-success rounded-3 bg-gradient" type="button" id="processBtn" data-id="<?= $transaksi['id_transaksi'] ?>" disabled><i class="fa-solid fa-money-bills"></i> Proses Transaksi</button>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body rounded-4 shadow-lg transparent-blur">
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
            <form id="transaksiForm" enctype="multipart/form-data" class="modal-content bg-body shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="transaksiModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger btn-sm bg-gradient ps-0 pe-0 pt-0 pb-0 rounded-3" data-bs-dismiss="modal" aria-label="Close"><span data-feather="x" class="mb-0" style="width: 30px; height: 30px;"></span></button>
                </div>
                <div class="modal-body py-2">
                    <div class="form-floating mb-1 mt-1">
                        <input type="number" class="form-control" autocomplete="off" dir="auto" placeholder="terima_uang" id="terima_uang" name="terima_uang">
                        <label for="terima_uang">Terima Uang (Rp)*</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-floating mt-1 mb-1">
                        <select class="form-select rounded-3" id="metode_pembayaran" name="metode_pembayaran" aria-label="metode_pembayaran">
                            <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                        <label for="metode_pembayaran">Metode Pembayaran*</label>
                        <div class="invalid-feedback"></div>
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
    async function fetchResepOptions() {
        try {
            const response = await axios.get('<?= base_url('transaksi/reseplist/' . $transaksi['id_transaksi'] . '/' . $transaksi['id_pasien']) ?>) ?>');

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

            const terima_uang = parseInt(data.terima_uang);
            const uang_kembali = parseInt(data.uang_kembali);

            $('#terima_uang_table').text(`Rp${terima_uang.toLocaleString('id-ID')}`);
            $('#uang_kembali_table').text(`Rp${uang_kembali.toLocaleString('id-ID')}`);
            $('#metode_pembayaran_table').text(data.metode_pembayaran);

            // Cek status `lunas`
            if (data.lunas === "1") {
                $('#tambahDetailContainer').hide();
                $('#printBtn').prop('disabled', false);
            } else if (data.lunas === "0") {
                $('#tambahDetailContainer').show();
                $('#printBtn').prop('disabled', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDetailTransaksi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('transaksi/detailtransaksilist/') . $transaksi['id_transaksi'] ?>');

            const data = response.data;
            $('#detail_transaksi').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada resep yang akan ditransaksikan</td>
                    </tr>
                `;
                $('#detail_transaksi').append(emptyRow);
                $('#processBtn').prop('disabled', true);
            } else {
                data.forEach(function(detail_transaksi) {
                    const diskon = parseInt(detail_transaksi.diskon); // Konversi jumlah ke integer
                    const harga_resep = parseInt(detail_transaksi.harga_resep); // Konversi harga satuan ke integer
                    const total_pembayaran = harga_resep * (1 - (diskon / 100)); // Hitung total harga
                    totalPembayaran += total_pembayaran;
                    const keterangan = detail_transaksi.resep.keterangan == '' ?
                        `<em>Tidak ada keterangan</em>` :
                        `${detail_transaksi.resep.keterangan}`;
                    const tindakanElement = `
                        <tr>
                            <td class="tindakan" rowspan="1">
                            <div class="btn-group" role="group">
                                <button class="btn btn-secondary text-nowrap bg-gradient rounded-start-3 edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_transaksi.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_transaksi.id_detail_transaksi}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td>
                            <span>${detail_transaksi.resep.user.fullname}</span>
                            <ul class="mb-0" id="obat-${detail_transaksi.id_detail_transaksi}">
                            </ul>
                            <span>${keterangan}</span>
                        </td>
                        <td class="date text-end">Rp${harga_resep.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${diskon.toLocaleString('id-ID')}%</td>
                        <td class="date text-end">Rp${total_pembayaran.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    $('#detail_transaksi').append(tindakanElement);
                    // Iterasi untuk setiap resep
                    detail_transaksi.resep.detail_resep.forEach(function(detail_resep) {
                        // Iterasi untuk setiap obat di detail resep
                        detail_resep.obat.forEach(function(obat) {
                            const jumlah = parseInt(detail_resep.jumlah); // Konversi jumlah ke integer
                            const harga_satuan = parseInt(detail_resep.harga_satuan); // Konversi harga satuan ke integer
                            const total_harga = jumlah * harga_satuan; // Hitung total harga

                            const detail_transaksiElement = `
                                <li>${obat.nama_obat}<br><small>${obat.kategori_obat} • ${obat.bentuk_obat} • ${obat.dosis_kali} × ${obat.dosis_hari} hari • ${obat.cara_pakai} • ${jumlah.toLocaleString('id-ID')} × Rp${harga_satuan.toLocaleString('id-ID')} = Rp${total_harga.toLocaleString('id-ID')}</small></li>
                            `;

                            $(`#obat-${detail_transaksi.id_detail_transaksi}`).append(detail_transaksiElement);
                        });
                    });
                    // Cek status `lunas`
                    if (detail_transaksi.lunas === "1") {
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                        $('#processBtn').prop('disabled', true);
                    } else if (detail_transaksi.lunas === "0") {
                        $('.edit-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                        $('#processBtn').prop('disabled', false);
                    }
                });
            }
            const totalPembayaranElement = `Rp${totalPembayaran.toLocaleString('id-ID')}`;
            $('#total_pembayaran').text(totalPembayaranElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#detail_transaksi').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_resep').select2({
            dropdownParent: $('#tambahDetail'),
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
                fetchDetailTransaksi();
                fetchResepOptions();
                fetchStatusTransaksi();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editDetailTransaksi').remove();
            try {
                const response = await axios.get(`<?= base_url('/transaksi/detailtransaksiitem') ?>/${id}`);
                const formHtml = `
                <tr id="editDetailTransaksi">
                    <td colspan="5">
                        <form id="editDetail" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Diskon (%)</div>
                                <button type="button" class="text-end btn-close ms-auto cancel-edit"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <input type="hidden" id="id_detail_transaksi" name="id_detail_transaksi" value="${response.data.id_detail_transaksi}">
                                <div class="flex-fill">
                                    <input type="number" id="diskon_edit" name="diskon_edit" class="form-control rounded-3" placeholder="Diskon (%)" value="${response.data.diskon}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="editButton" class="btn btn-primary bg-gradient rounded-3">
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
                $('#editDetail').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#editDetail .is-invalid').removeClass('is-invalid');
                    $('#editDetail .invalid-feedback').text('').hide();
                    $('#editButton').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editDetail input, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/transaksi/perbaruidetailtransaksi/' . $transaksi['id_transaksi']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editDetail')[0].reset();
                            $('#editDetail .is-invalid').removeClass('is-invalid');
                            $('#editDetail .invalid-feedback').text('').hide();
                            $('#editDetailTransaksi').remove();
                            fetchDetailTransaksi();
                            fetchResepOptions();
                            fetchStatusTransaksi();
                        } else {
                            console.log("Validation Errors:", response.data.errors);

                            // Clear previous validation states
                            $('#editDetail .is-invalid').removeClass('is-invalid');
                            $('#editDetail .invalid-feedback').text('').hide();

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
                        showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                    } finally {
                        $('#editButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        `);
                        $('#editDetail input, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('.cancel-edit').on('click', function() {
                    $('#editDetailTransaksi').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $('#tambahDetail').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#tambahDetail .is-invalid').removeClass('is-invalid');
            $('#tambahDetail .invalid-feedback').text('').hide();
            $('#addButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#tambahDetail input, #tambahDetail select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/transaksi/tambahdetailtransaksi/' . $transaksi['id_transaksi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#tambahDetail')[0].reset();
                    $('#id_resep').val('');
                    $('#diskon').val('');
                    $('#tambahDetail .is-invalid').removeClass('is-invalid');
                    $('#tambahDetail .invalid-feedback').text('').hide();
                    fetchDetailTransaksi();
                    fetchResepOptions();
                    fetchStatusTransaksi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#tambahDetail .is-invalid').removeClass('is-invalid');
                    $('#tambahDetail .invalid-feedback').text('').hide();

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
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#addButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#tambahDetail input, #tambahDetail select').prop('disabled', false);
            }
        });

        $('#processBtn').click(function() {
            $('#transaksiModalLabel').text('Proses Transaksi');
            $('#transaksiModal').modal('show');
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
                const response = await axios.post(`<?= base_url('/transaksi/process/' . $transaksi['id_transaksi'] . '/' . $transaksi['id_pasien']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#transaksiModal').modal('hide');
                    fetchDetailTransaksi();
                    fetchStatusTransaksi();
                } else {
                    if (response.data.errors == null) {
                        showFailedToast(response.data.message);
                    } else if (response.data.message == null) {
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
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#transaksiForm input, #transaksiForm select, #closeBtn').prop('disabled', false);
            }
        });

        $('#transaksiModal').on('hidden.bs.modal', function() {
            $('#transaksiForm')[0].reset();
            $('#terima_uang').val('');
            $('#metode_pembayaran').val('');
            $('#transaksiForm .is-invalid').removeClass('is-invalid');
            $('#transaksiForm .invalid-feedback').text('').hide();
        });

        fetchDetailTransaksi();
        fetchResepOptions();
        fetchStatusTransaksi();
    });
    // Show toast notification
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