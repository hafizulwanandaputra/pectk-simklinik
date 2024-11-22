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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/pembelianobat'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 pt-3">
    <div class="no-fluid-content">
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Informasi Obat Masuk</legend>
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
                            <?= ($pembelianobat['kontak_supplier'] == '') ? '<em>Tidak ada</em>' : $pembelianobat['kontak_supplier']; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-3 fw-medium">Merek</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= ($pembelianobat['merek'] == '') ? '<em>Tanpa Merek</em>' : $pembelianobat['merek']; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-lg-3 fw-medium">Apoteker</div>
                    <div class="col-lg">
                        <div class="date">
                            <?= $pembelianobat['apoteker'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="card shadow-sm rounded-3 overflow-auto">
            <div class="card-header bg-body-tertiary" id="tambahDetailContainer" style="display: none;">
                <form id="tambahDetail" enctype="multipart/form-data">
                    <div class="mb-2">
                        <select class="form-select rounded-3" id="id_obat" name="id_obat" aria-label="id_obat">
                            <option value="" disabled selected>-- Pilih Obat --</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row gap-2">
                        <div class="flex-fill">
                            <input type="number" id="jumlah" name="jumlah" class="form-control rounded-3" placeholder="Jumlah" autocomplete="off">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-grid d-lg-block w-auto">
                            <button type="submit" id="addButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-0 m-0 table-responsive">
                <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nama Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jumlah</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Obat Masuk</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Obat Belum Diterima</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="detail_pembelian_obat">
                        <tr>
                            <td colspan="7" class="text-center">Memuat detail pembelian...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-body-tertiary">
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Qty</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow" id="total_qty">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Masuk</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow" id="total_masuk">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Belum Diterima</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow" id="total_blm_diterima">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Harga</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow fw-bold" id="total_harga">
                            <span class="placeholder w-100"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="terimaObat">
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-outline-success rounded-3 bg-gradient" type="button" id="printBtn" onclick="startDownload()" disabled><i class="fa-solid fa-file-excel"></i> Buat Faktur (Excel)</button>
                <button class="btn btn-primary rounded-3 bg-gradient" type="button" id="completeBtn" data-id="<?= $pembelianobat['id_pembelian_obat'] ?>" disabled><i class="fa-solid fa-check-double"></i> Terima Obat</button>
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

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteItemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteItemMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmDeleteItemBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="completeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
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
<?= $this->section('javascript'); ?>
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
            let totalMasuk = 0;
            let totalHarga = 0;
            let totalBlmDiterima = 0;

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
                    const obat_masuk = parseInt(detail_pembelian_obat.obat_masuk_baru); // Konversi obat_masuk ke integer
                    const harga_satuan = parseInt(detail_pembelian_obat.harga_satuan); // Konversi harga obat ke integer
                    const total_harga = jumlah * harga_satuan; // Hitung total harga
                    const blm_diterima = jumlah - obat_masuk;
                    totalHarga += total_harga;
                    totalQty += jumlah;
                    totalMasuk += obat_masuk;
                    totalBlmDiterima += blm_diterima;
                    const expired = (detail_pembelian_obat.expired == null) ? '' : detail_pembelian_obat.expired;
                    const detail_pembelian_obatElement = `
                    <tr>
                        <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary text-nowrap bg-gradient rounded-start-3 add-batch-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-bs-toggle="tooltip" data-bs-title="Tambah Item Obat"><i class="fa-solid fa-plus"></i></button>
                                <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_pembelian_obat.id_detail_pembelian_obat}" data-name="${detail_pembelian_obat.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            ${detail_pembelian_obat.nama_obat}<br><small>${detail_pembelian_obat.kategori_obat} • ${detail_pembelian_obat.bentuk_obat}</small>
                            <ul class="list-group shadow-sm rounded-3" id="item-${detail_pembelian_obat.id_detail_pembelian_obat}">
                            </ul>
                        </td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${obat_masuk.toLocaleString('id-ID')}</td>
                        <td class="date text-end">${blm_diterima.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_pembelian_obat').append(detail_pembelian_obatElement);
                    detail_pembelian_obat.item.forEach(function(item) {
                        const jumlah_item = parseInt(item.jumlah_item); // Konversi jumlah ke integer
                        const itemElement = `
                                <li class="list-group-item bg-body-tertiary">
                                    <div class="fw-bold">${item.no_batch}</div>
                                    <div class="date">Kadaluwarsa: ${item.expired}</div>
                                    <div class="date">Jumlah: ${jumlah_item}</div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-body text-nowrap bg-gradient rounded-start-3 edit-batch-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${item.id_item_obat}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-batch-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${item.id_item_obat}" data-name="${item.no_batch}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </li>
                            `;
                        $(`#item-${item.id_detail_pembelian_obat}`).append(itemElement);
                    });
                    $('#printBtn').prop('disabled', false);
                    if (detail_pembelian_obat.diterima === "1") {
                        $('.add-batch-btn').prop('disabled', true);
                        $('.edit-batch-btn').prop('disabled', true);
                        $('.delete-batch-btn').prop('disabled', true);
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                        $('#completeBtn').prop('disabled', true);
                    } else if (detail_pembelian_obat.diterima === "0") {
                        $('.add-batch-btn').prop('disabled', false);
                        $('.edit-batch-btn').prop('disabled', false);
                        $('.delete-batch-btn').prop('disabled', false);
                        $('.edit-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                        $('#completeBtn').prop('disabled', false);
                    }
                });
            }
            const totalHargaElement = `Rp${totalHarga.toLocaleString('id-ID')}`;
            const totalQtyElement = `${totalQty.toLocaleString('id-ID')}`;
            const totalMasukElement = `${totalMasuk.toLocaleString('id-ID')}`;
            const totalBlmDiterimaElement = `${totalBlmDiterima.toLocaleString('id-ID')}`;
            $('#total_harga').text(totalHargaElement);
            $('#total_qty').text(totalQtyElement);
            $('#total_masuk').text(totalMasukElement);
            $('#total_blm_diterima').text(totalBlmDiterimaElement);
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
            dropdownParent: $(document.body),
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
            $('#completeSubmessage').html(`Silakan periksa informasi pembelian ini. Detail pembelian tidak dapat diubah setelah menerima semua obat-obat ini. Jika jumlah obat masuk kurang dari jumlah yang Anda minta, Anda masih bisa mengubah detail pembelian ini.`);
            $('#completeModal').modal('show');
        });

        $('#confirmCompleteBtn').click(async function() {
            $('#completeModal button').prop('disabled', true);
            $('#completeMessage').addClass('mb-0').html('Memproses data, silakan tunggu...');
            $('#completeSubmessage').hide();
            try {
                const response = await axios.post(`<?= base_url('pembelianobat/complete') ?>/${pembelianObatId}`);
                showSuccessToast(response.data.message);
                fetchDetailPembelianObat();
                fetchObatOptions();
                fetchStatusPembelian();
            } catch (error) {
                if (error.response.request.status === 422) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#completeModal').modal('hide');
                $('#completeMessage').removeClass('mb-0');
                $('#completeSubmessage').show();
                $('#completeModal button').prop('disabled', false);
            }
        });

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

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editDetailPembelian').remove();
            $('#addBatchPembelian').remove();
            $('#editBatchPembelian').remove();
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
                                    <input type="number" id="jumlah_edit" name="jumlah_edit" class="form-control rounded-3" placeholder="Jumlah" value="${response.data.jumlah}" autocomplete="off">
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
                        if (error.response.request.status === 422 || error.response.request.status === 401) {
                            showFailedToast(error.response.data.message);
                        } else {
                            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        }
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
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#addButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-plus"></i> Tambah
                `);
                $('#tambahDetail input, #tambahDetail select').prop('disabled', false);
            }
        });

        $(document).on('click', '.add-batch-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#editDetailPembelian').remove();
            $('#addBatchPembelian').remove();
            $('#editBatchPembelian').remove();
            const formHtml = `
                <tr id="addBatchPembelian">
                    <td colspan="8">
                        <form id="addBatch" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Tambah Item</div>
                                <button type="button" class="text-end btn-close ms-auto cancel-add-batch"></button>
                            </div>
                            <div class="d-flex flex-column flex-lg-row gap-1">
                                <div class="flex-fill">
                                    <input type="text" id="no_batch" name="no_batch" class="form-control rounded-3" placeholder="Nomor Batch" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="date" id="expired" name="expired" class="form-control rounded-3" placeholder="Kedaluwarsa">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="number" id="jumlah_item" name="jumlah_item" class="form-control rounded-3" placeholder="Jumlah Diterima" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-lg-block w-auto">
                                    <button type="submit" id="addBatchButton" class="btn btn-primary bg-gradient rounded-3">
                                        <i class="fa-solid fa-plus"></i> Tambah
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
            $('#addBatch').on('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                console.log("Form Data:", $(this).serialize());

                // Clear previous validation states
                $('#addBatch .is-invalid').removeClass('is-invalid');
                $('#addBatch .invalid-feedback').text('').hide();
                $('#addBatchButton').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
                    `);

                // Disable form inputs
                $('#addBatch input, .btn-close').prop('disabled', true);

                try {
                    const response = await axios.post(`<?= base_url('/pembelianobat/tambahitemobat') ?>/${id}/<?= $pembelianobat['id_pembelian_obat'] ?>`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        $('#addBatch')[0].reset();
                        $('#id_obat').val(null).trigger('change');
                        $('#addBatch .is-invalid').removeClass('is-invalid');
                        $('#addBatch .invalid-feedback').text('').hide();
                        $('#addBatchPembelian').remove();
                        fetchDetailPembelianObat();
                        fetchObatOptions();
                        fetchStatusPembelian();
                    } else {
                        console.log("Validation Errors:", response.data.errors);

                        // Clear previous validation states
                        $('#addBatch .is-invalid').removeClass('is-invalid');
                        $('#addBatch .invalid-feedback').text('').hide();

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
                    if (error.response.request.status === 422 || error.response.request.status === 401) {
                        showFailedToast(error.response.data.message);
                    } else {
                        showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                    }
                } finally {
                    $('#addBatchButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-plus"></i> Tambah
                        `);
                    $('#addBatch input, .btn-close').prop('disabled', false);
                }
            });

            // Handle cancel button
            $('.cancel-add-batch').on('click', function() {
                $('#addBatchPembelian').remove();
            });
        });

        $(document).on('click', '.edit-batch-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('li');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editDetailPembelian').remove();
            $('#addBatchPembelian').remove();
            $('#editBatchPembelian').remove();
            try {
                const response = await axios.get(`<?= base_url('/pembelianobat/itemobat') ?>/${id}`);
                const formHtml = `
                    <li class="list-group-item" id="editBatchPembelian">
                        <form id="editBatch" enctype="multipart/form-data">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold">Edit Item</div>
                                <button type="button" class="text-end btn-close ms-auto cancel-edit-batch"></button>
                            </div>
                            <div class="d-flex flex-column flex-xl-row gap-1">
                                <input type="hidden" id="id_detail_pembelian_obat" name="id_detail_pembelian_obat" value="${response.data.id_detail_pembelian_obat}">
                                <div class="flex-fill">
                                    <input type="text" id="no_batch_edit" name="no_batch_edit" class="form-control rounded-3" placeholder="Nomor Batch" value="${response.data.no_batch}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="date" id="expired_edit" name="expired_edit" class="form-control rounded-3" placeholder="Kedaluwarsa" value="${response.data.expired}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="number" id="jumlah_item_edit" name="jumlah_item_edit" class="form-control rounded-3" placeholder="Jumlah Diterima" value="${response.data.jumlah_item}" autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="d-grid d-xl-block w-auto">
                                    <button type="submit" id="editBatchButton" class="btn btn-primary bg-gradient rounded-3">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </li>
                `;
                // Append the new row with the form directly after the current data row
                $row.after(formHtml);

                // Handle form submission
                $('#editBatch').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#editBatch .is-invalid').removeClass('is-invalid');
                    $('#editBatch .invalid-feedback').text('').hide();
                    $('#editBatchButton').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editBatch input, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/pembelianobat/perbaruiitemobat') ?>/${id}/<?= $pembelianobat['id_pembelian_obat'] ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editBatch')[0].reset();
                            $('#id_obat').val(null).trigger('change');
                            $('#editBatch .is-invalid').removeClass('is-invalid');
                            $('#editBatch .invalid-feedback').text('').hide();
                            $('#editBatchPembelian').remove();
                            fetchDetailPembelianObat();
                            fetchObatOptions();
                            fetchStatusPembelian();
                        } else {
                            console.log("Validation Errors:", response.data.errors);

                            // Clear previous validation states
                            $('#editBatch .is-invalid').removeClass('is-invalid');
                            $('#editBatch .invalid-feedback').text('').hide();

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
                        if (error.response.request.status === 422 || error.response.request.status === 401) {
                            showFailedToast(error.response.data.message);
                        } else {
                            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        }
                    } finally {
                        $('#editBatchButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        `);
                        $('#editBatch input, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('.cancel-edit-batch').on('click', function() {
                    $('#editBatchPembelian').remove();
                });
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                console.error(error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i>`);
            }
        });

        $(document).on('click', '.delete-batch-btn', function() {
            itemObatId = $(this).data('id');
            itemObatName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteItemMessage').html(`Hapus item "` + itemObatName + `?`);
            $('#deleteItemModal').modal('show');
        });

        $('#confirmDeleteItemBtn').click(async function() {
            $('#deleteItemModal button').prop('disabled', true);
            $('#deleteItemMessage').html('Menghapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/pembelianobat/hapusitemobat') ?>/${itemObatId}/<?= $pembelianobat['id_pembelian_obat'] ?>`);
                fetchDetailPembelianObat();
                fetchObatOptions();
                fetchStatusPembelian();
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteItemModal').modal('hide');
                $('#deleteItemModal button').prop('disabled', false);
            }
        });

        fetchDetailPembelianObat();
        fetchObatOptions();
        fetchStatusPembelian();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>