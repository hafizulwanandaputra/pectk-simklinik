<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 link-body-emphasis" href="<?= base_url('/pembelianobat'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
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
        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Informasi Pembelian Obat</legend>
        <div style="font-size: 9pt;">
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Tanggal dan Waktu</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['tgl_pembelian'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Nama Supplier</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['nama_supplier'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Alamat Supplier</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['alamat_supplier'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Nomor Telepon Supplier</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['kontak_supplier'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Apoteker</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['fullname'] ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset id="tambahDetailContainer" class="border rounded-3 px-2 py-0 mb-3" style="display: none;">
        <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Tambah Detail Pembelian</legend>
        <form id="tambahDetail" enctype="multipart/form-data">
            <div class="mb-2">
                <select class="form-select rounded-3" id="id_obat" name="id_obat" aria-label="id_obat">
                    <option value="" disabled selected>-- Pilih Obat --</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="d-flex flex-column flex-lg-row mb-2 gap-2">
                <div class="flex-fill">
                    <input type="number" id="jumlah" name="jumlah" class="form-control rounded-3" placeholder="Jumlah">
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
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nama Obat</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">No Batch</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Kadaluwarsa</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jumlah</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Harga</th>
                </tr>
            </thead>
            <tbody class="align-top" id="detail_pembelian_obat">
                <tr>
                    <td colspan="7" class="text-center">Memuat detail pembelian...</td>
                </tr>
            </tbody>
            <tbody class="align-top">
            </tbody>
            <thead>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 2px;" colspan="3"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;">Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="total_qty"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="total_harga"></th>
                </tr>
            </thead>
        </table>
    </div>

    <div id="terimaObat">
        <hr>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
            <button class="btn btn-primary rounded-3 bg-gradient" type="button" id="printBtn" onclick="startDownload()" disabled><i class="fa-solid fa-print"></i> Cetak Faktur</button>
            <button class="btn btn-success rounded-3 bg-gradient" type="button" id="completeBtn" data-id="<?= $pembelianobat['id_pembelian_obat'] ?>" disabled><i class="fa-solid fa-check-double"></i> Terima Obat</button>
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

    <div class="modal modal-sheet p-4 py-md-5 fade" id="completeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 id="completeMessage"></h5>
                    <h6 class="mb-0" id="completeSubmessage"></h6>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmCompleteBtn">Ya</button>
                </div>
            </div>
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
    async function startDownload() {
        $('#loadingSpinner').show(); // Menampilkan spinner

        try {
            // Mengambil file dari server
            const response = await axios.get('<?= base_url('pembelianobat/fakturpembelianobat/' . $pembelianobat['id_pembelian_obat']); ?>', {
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
    async function fetchObatOptions() {
        try {
            const response = await axios.get('<?= base_url('pembelianobat/obatlist/' . $pembelianobat['id_supplier'] . '/' . $pembelianobat['id_pembelian_obat']) ?>');

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#id_obat');

                // Clear existing options except the first one
                select.find('option:not(:first)').remove();

                // Loop through the options and append them to the select element
                options.forEach(option => {
                    select.append(`<option value="${option.value}">${option.text}</option>`);
                });
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan obat.<br>' + error);
        }
    }

    async function fetchStatusPembelian() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pembelianobat/pembelianobat/') . $pembelianobat['id_pembelian_obat'] ?>');

            const data = response.data;

            // Cek status `diterima`
            if (data.diterima === "1") {
                $('#tambahDetailContainer').hide();
            } else if (data.diterima === "0") {
                $('#tambahDetailContainer').show();
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDetailPembelianObat() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pembelianobat/detailpembelianobatlist/') . $pembelianobat['id_pembelian_obat'] ?>');

            const data = response.data;
            $('#detail_pembelian_obat').empty();

            let totalQty = 0;
            let totalHarga = 0;

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada obat yang akan dibeli</td>
                    </tr>
                `;
                $('#detail_pembelian_obat').append(emptyRow);
                $('#completeBtn').prop('disabled', true);
                $('#printBtn').prop('disabled', true);
            } else {
                data.forEach(function(detail_pembelian_obat) {
                    const jumlah = parseInt(detail_pembelian_obat.jumlah); // Konversi jumlah ke integer
                    const harga_satuan = parseInt(detail_pembelian_obat.harga_satuan); // Konversi harga obat ke integer
                    const total_harga = jumlah * harga_satuan; // Hitung total harga
                    totalHarga += total_harga;
                    totalQty += jumlah;
                    const expired = (detail_pembelian_obat.expired == null) ? '' : detail_pembelian_obat.expired;
                    const detail_pembelian_obatElement = `
                    <tr>
                        <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-secondary text-nowrap bg-gradient rounded-start-3 input-batch-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-bs-toggle="tooltip" data-bs-title="Input Batch dan Kadaluwarsa"><i class="fa-solid fa-list-check"></i></button>
                                <button class="btn btn-secondary text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-name="${detail_pembelian_obat.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td class="text-nowrap">${detail_pembelian_obat.nama_obat}<br><small>${detail_pembelian_obat.kategori_obat} • ${detail_pembelian_obat.bentuk_obat}</small></td>
                        <td class="date">${detail_pembelian_obat.no_batch}</td>
                        <td class="date">${expired}</td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_pembelian_obat').append(detail_pembelian_obatElement);
                    if (detail_pembelian_obat.diterima === "1") {
                        $('.input-batch-btn').prop('disabled', true);
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                        $('#printBtn').prop('disabled', true);
                        $('#completeBtn').prop('disabled', true);
                    } else if (detail_pembelian_obat.diterima === "0") {
                        $('.input-batch-btn').prop('disabled', false);
                        $('.edit-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                        $('#printBtn').prop('disabled', false);
                        $('#completeBtn').prop('disabled', false);
                    }
                });
            }
            const totalHargaElement = `Rp${totalHarga.toLocaleString('id-ID')}`;
            const totalQtyElement = `${totalQty.toLocaleString('id-ID')}`;
            $('#total_harga').text(totalHargaElement);
            $('#total_qty').text(totalQtyElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#detail_pembelian_obat').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_obat').select2({
            dropdownParent: $('#tambahDetail'),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });
        var detailPembelianObatId;
        var detailPembelianObatName;
        var pembelianObatId;

        $(document).on('click', '#completeBtn', function() {
            pembelianObatId = $(this).data('id');
            $('#completeMessage').html(`Apakah Anda telah menerima obat-obat ini?`);
            $('#completeSubmessage').html(`Silakan periksa informasi pembelian ini! Detail pembelian tidak dapat diubah setelah menerima obat-obat ini!`);
            $('#completeModal').modal('show');
        });

        $('#confirmCompleteBtn').click(async function() {
            $('#completeModal button').prop('disabled', true);
            $('#completeMessage').addClass('mb-0').html('Memproses data, silakan tunggu...');
            $('#completeSubmessage').hide();
            try {
                const response = await axios.post(`<?= base_url('pembelianobat/complete') ?>/${pembelianObatId}`);
                if (response.data.success == true) {
                    showSuccessToast(response.data.message);
                    fetchDetailPembelianObat();
                    fetchObatOptions();
                    fetchStatusPembelian();
                } else if (response.data.success == false) {
                    showFailedToast(response.data.message);
                }

            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#completeModal').modal('hide');
                $('#completeMessage').removeClass('mb-0');
                $('#completeSubmessage').show();
                $('#completeModal button').prop('disabled', false);
            }
        });

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            detailPembelianObatId = $(this).data('id');
            detailPembelianObatName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus item "` + detailPembelianObatName + `?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Menghapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/pembelianobat/hapusdetailpembelianobat') ?>/${detailPembelianObatId}`);
                fetchDetailPembelianObat();
                fetchObatOptions();
                fetchStatusPembelian();
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
            $('#editDetailPembelian').remove();
            $('#inputBatchPembelian').remove();
            try {
                const response = await axios.get(`<?= base_url('/pembelianobat/detailpembelianobatitem') ?>/${id}`);
                const formHtml = `
                <tr id="editDetailPembelian">
                    <td colspan="7">
                        <form id="editDetail" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Jumlah</div>
                                <button type="button" class="text-end btn-close ms-auto cancel-edit"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <input type="hidden" id="id_detail_pembelian_obat" name="id_detail_pembelian_obat" value="${response.data.id_detail_pembelian_obat}">
                                <input type="hidden" id="id_obat_edit" name="id_obat_edit" value="${response.data.id_obat}">
                                <div class="flex-fill">
                                    <input type="number" id="jumlah_edit" name="jumlah_edit" class="form-control rounded-3" placeholder="Jumlah" value="${response.data.jumlah}">
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
                        const response = await axios.post(`<?= base_url('/pembelianobat/perbaruidetailpembelianobat/' . $pembelianobat['id_pembelian_obat']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editDetail')[0].reset();
                            $('#id_obat').val(null).trigger('change');
                            $('#editDetail .is-invalid').removeClass('is-invalid');
                            $('#editDetail .invalid-feedback').text('').hide();
                            $('#editDetailPembelian').remove();
                            fetchDetailPembelianObat();
                            fetchObatOptions();
                            fetchStatusPembelian();
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
                    $('#editDetailPembelian').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $(document).on('click', '.input-batch-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editDetailPembelian').remove();
            $('#inputBatchPembelian').remove();
            try {
                const response = await axios.get(`<?= base_url('/pembelianobat/detailpembelianobatitem') ?>/${id}`);
                const formHtml = `
                <tr id="inputBatchPembelian">
                    <td colspan="7">
                        <form id="inputBatch" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Input No Batch dan Tanggal Kadaluwarsa</div>
                                <button type="button" class="text-end btn-close ms-auto cancel-input-batch"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <div class="flex-fill">
                                    <input type="text" id="no_batch" name="no_batch" class="form-control rounded-3" placeholder="Nomor Batch" value="${response.data.no_batch}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="date" id="expired" name="expired" class="form-control rounded-3" placeholder="Jumlah" value="${response.data.expired}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="inputBatchButton" class="btn btn-primary bg-gradient rounded-3">
                                        <i class="fa-solid fa-floppy-disk"></i> Simpan
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
                $('#inputBatch').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#inputBatch .is-invalid').removeClass('is-invalid');
                    $('#inputBatch .invalid-feedback').text('').hide();
                    $('#inputBatchButton').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Simpan
                    `);

                    // Disable form inputs
                    $('#inputBatch input, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/pembelianobat/inputbatchexpired') ?>/${id}`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#inputBatch')[0].reset();
                            $('#id_obat').val(null).trigger('change');
                            $('#inputBatch .is-invalid').removeClass('is-invalid');
                            $('#inputBatch .invalid-feedback').text('').hide();
                            $('#inputBatchPembelian').remove();
                            fetchDetailPembelianObat();
                            fetchObatOptions();
                            fetchStatusPembelian();
                        } else {
                            console.log("Validation Errors:", response.data.errors);

                            // Clear previous validation states
                            $('#inputBatch .is-invalid').removeClass('is-invalid');
                            $('#inputBatch .invalid-feedback').text('').hide();

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
                        $('#inputBatchButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        `);
                        $('#inputBatch input, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('.cancel-input-batch').on('click', function() {
                    $('#inputBatchPembelian').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-list-check"></i>`);
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
                const response = await axios.post(`<?= base_url('/pembelianobat/tambahdetailpembelianobat/' . $pembelianobat['id_pembelian_obat']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#tambahDetail')[0].reset();
                    $('#id_obat').val('');
                    $('#jumlah').val('');
                    $('#tambahDetail .is-invalid').removeClass('is-invalid');
                    $('#tambahDetail .invalid-feedback').text('').hide();
                    fetchDetailPembelianObat();
                    fetchObatOptions();
                    fetchStatusPembelian();
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

        fetchDetailPembelianObat();
        fetchObatOptions();
        fetchStatusPembelian();
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