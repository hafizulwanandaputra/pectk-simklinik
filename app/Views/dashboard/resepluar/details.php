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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/resepluar'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $resep['id_resep'] ?> • <?= ($resep['nama_pasien'] == NULL) ? '<em>Anonim</em>' : $resep['nama_pasien']; ?> • <?= $resep['tanggal_resep'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('resepluar/detailresep/' . $previous['id_resep']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['id_resep'] ?> • <?= ($previous['nama_pasien'] == NULL) ? 'Anonim' : $previous['nama_pasien']; ?> • <?= $previous['tanggal_resep'] ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada resep sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('resepluar/detailresep/' . $next['id_resep']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['id_resep'] ?> • <?= ($next['nama_pasien'] == NULL) ? 'Anonim' : $next['nama_pasien']; ?> • <?= $next['tanggal_resep'] ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
            <div class="fw-bold mb-2 border-bottom">Informasi Pasien Resep Luar</div>
            <div style="font-size: 0.75em;">
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                    <div class="col">
                        <div class="date">
                            <?= $resep['tanggal_resep'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Nama Pasien</div>
                    <div class="col">
                        <div class="date">
                            <?= ($resep['nama_pasien'] == NULL) ? '<em>Anonim</em>' : $resep['nama_pasien']; ?>
                        </div>
                    </div>
                </div>
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
                            <?= ($resep['alamat'] == NULL) ? '<em>Tidak ada</em>' : $resep['alamat']; ?>
                        </div>
                    </div>
                </div>
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Apoteker</div>
                    <div class="col">
                        <div class="date">
                            <?= $resep['apoteker'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm  overflow-auto">
            <div class="card-header bg-body-tertiary" id="tambahDetailContainer" style="display: none;">
                <form id="tambahDetail" enctype="multipart/form-data">
                    <div class="row g-2">
                        <div class="col-12">
                            <select class="form-select form-select-sm" id="id_obat" name="id_obat" aria-label="id_obat" autocomplete="off">
                                <option value="" disabled selected>-- Pilih Obat --</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6">
                            <input type="text" id="signa" name="signa" class="form-control form-control-sm " placeholder="Dosis" list="list_signa" autocomplete="off">
                            <div class="invalid-feedback"></div>
                            <datalist id="list_signa">
                                <option value="1×½">
                                <option value="1×1">
                                <option value="2×½">
                                <option value="2×1">
                                <option value="3×½">
                                <option value="3×1">
                                <option value="4×½">
                                <option value="4×1">
                                <option value="5×½">
                                <option value="5×1">
                                <option value="6×½">
                                <option value="6×1">
                            </datalist>
                        </div>
                        <div class="col-6">
                            <input type="text" id="catatan" name="catatan" class="form-control form-control-sm" placeholder="Catatan" list="list_catatan" autocomplete="off">
                            <div class="invalid-feedback"></div>
                            <datalist id="list_catatan">
                                <option value="1 Tetes">
                                <option value="1 Tablet">
                                <option value="1 Salep">
                                <option value="Sendok Teh">
                                <option value="Sendok Makan">
                            </datalist>
                        </div>
                        <div class="col-6">
                            <select class="form-select form-select-sm" id="cara_pakai" name="cara_pakai" aria-label="cara_pakai">
                                <option value="" disabled selected>-- Pilih Cara Pakai --</option>
                                <option value="Mata Kanan">Mata Kanan</option>
                                <option value="Mata Kiri">Mata Kiri</option>
                                <option value="Kedua Mata">Kedua Mata</option>
                                <option value="Sebelum Makan">Sebelum Makan</option>
                                <option value="Sesudah Makan">Sesudah Makan</option>
                                <option value="Sesudah Makan Dihabiskan">Sesudah Makan Dihabiskan</option>
                                <option value="Sesudah Makan Bila Sakit">Sesudah Makan Bila Sakit</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6">
                            <input type="number" id="jumlah" name="jumlah" class="form-control form-control-sm" placeholder="Qty">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                            <button type="submit" id="addButton" class="btn btn-primary bg-gradient btn-sm text-nowrap">
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
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
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
                <div class="row d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Resep</div>
                    <div class="col text-end">
                        <div class="date text-nowrap placeholder-glow" id="jumlah_resep">
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

        <div id="cetakEtiketBtn">
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/resepluar/etiket-dalam/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak E-Tiket Obat Dalam</button>
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn2" onclick="window.open(`<?= base_url('/resepluar/etiket-luar/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak E-Tiket Obat Luar</button>
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
    async function fetchObatOptions() {
        try {
            const response = await axios.get('<?= base_url('resepluar/obatlist/' . $resep['id_resep']) ?>');

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

    async function fetchStatusResep() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resepluar/resep/') . $resep['id_resep'] ?>');

            const data = response.data;

            // Cek status `status`
            if (data.status === "1") {
                $('#tambahDetailContainer').hide();
            } else if (data.status === "0") {
                $('#tambahDetailContainer').show();
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDetailResep() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('resepluar/detailreseplist/') . $resep['id_resep'] ?>');

            const data = response.data;
            $('#detail_resep').empty();

            let jumlahResep = 0;
            let totalHarga = 0;

            let hasInternalMedicine = false; // Track if there is internal medicine (kapsul/tablet)
            let hasExternalMedicine = false; // Track if there is external medicine (tetes/salep)

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada obat yang akan dijadikan resep</td>
                    </tr>
                `;
                $('#detail_resep').append(emptyRow);
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

                    const kategori_obat = detail_resep.kategori_obat ? `${detail_resep.kategori_obat}, ` : ``;

                    const detail_resepElement = `
                    <tr>
                        <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${detail_resep.id_detail_resep}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${detail_resep.id_detail_resep}" data-name="${detail_resep.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td><i class="fa-solid fa-prescription"></i> ${detail_resep.nama_obat}
                        <small>
                            <ul class="ps-3 mb-0">
                                <li>${kategori_obat}${detail_resep.bentuk_obat}</li>
                                <li>${detail_resep.signa}, ${detail_resep.cara_pakai}, ${detail_resep.catatan}</li>
                            </ul>
                        </small></td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_resep').append(detail_resepElement);
                    if (detail_resep.status === "1") {
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    } else if (detail_resep.status === "0") {
                        $('.edit-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                    }

                });
            }
            // Handle enabling/disabling print buttons based on medicine type
            if (hasInternalMedicine) {
                $('#printBtn1').prop('disabled', false); // Disable if no internal medicine
            }
            if (hasExternalMedicine) {
                $('#printBtn2').prop('disabled', false); // Disable if no external medicine
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
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_obat').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        var detailResepId;
        var detailResepName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            detailResepId = $(this).data('id');
            detailResepName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus item "` + detailResepName + `?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html('Menghapus, silakan tunggu...');

            try {
                await axios.delete(`<?= base_url('/resepluar/hapusdetailresep') ?>/${detailResepId}`);
                fetchDetailResep();
                fetchObatOptions();
                fetchStatusResep();
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
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span>`);
            $('#editDetailResep').remove();
            try {
                const response = await axios.get(`<?= base_url('/resepluar/detailresepitem') ?>/${id}`);
                const formHtml = `
                <tr id="editDetailResep">
                    <td colspan="5">
                        <form id="editDetail" enctype="multipart/form-data">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-bold">Edit Resep</div>
                            <button type="button" class="text-end btn-close ms-auto cancel-edit"></button>
                        </div>
                        <input type="hidden" id="id_detail_resep" name="id_detail_resep" value="${response.data.id_detail_resep}">
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" id="signa_edit" name="signa_edit" class="form-control form-control-sm" placeholder="Dosis" value="${response.data.signa}" list="list_signa_edit" autocomplete="off">
                                <div class="invalid-feedback"></div>
                                <datalist id="list_signa_edit">
                                    <option value="1×½">
                                    <option value="1×1">
                                    <option value="2×½">
                                    <option value="2×1">
                                    <option value="3×½">
                                    <option value="3×1">
                                    <option value="4×½">
                                    <option value="4×1">
                                    <option value="5×½">
                                    <option value="5×1">
                                    <option value="6×½">
                                    <option value="6×1">
                                </datalist>
                            </div>
                            <div class="col-6">
                                <input type="text" id="catatan_edit" name="catatan_edit" class="form-control form-control-sm" placeholder="Catatan" value="${response.data.catatan}" list="list_catatan_edit" autocomplete="off">
                                <div class="invalid-feedback"></div>
                                <datalist id="list_catatan_edit">
                                    <option value="1 Tetes">
                                    <option value="1 Tablet">
                                    <option value="1 Salep">
                                    <option value="Sendok Teh">
                                    <option value="Sendok Makan">
                                </datalist>
                            </div>
                            <div class="col-6">
                                <select class="form-select form-select-sm" id="cara_pakai_edit" name="cara_pakai_edit" aria-label="cara_pakai">
                                    <option value="" disabled selected>-- Pilih Cara Pakai --</option>
                                    <option value="Mata Kanan">Mata Kanan</option>
                                    <option value="Mata Kiri">Mata Kiri</option>
                                    <option value="Kedua Mata">Kedua Mata</option>
                                    <option value="Sebelum Makan">Sebelum Makan</option>
                                    <option value="Sesudah Makan">Sesudah Makan</option>
                                    <option value="Sesudah Makan Dihabiskan">Sesudah Makan Dihabiskan</option>
                                    <option value="Sesudah Makan Bila Sakit">Sesudah Makan Bila Sakit</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">
                                <input type="text" id="jumlah_edit" name="jumlah_edit" class="form-control form-control-sm" placeholder="Qty" value="${response.data.jumlah}" autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                                <button type="submit" id="editButton" class="btn btn-primary bg-gradient btn-sm text-nowrap">
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
                $('#cara_pakai_edit').val(response.data.cara_pakai);

                // Handle form submission
                $('#editDetail').on('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    console.log("Form Data:", $(this).serialize());

                    // Clear previous validation states
                    $('#editDetail .is-invalid').removeClass('is-invalid');
                    $('#editDetail .invalid-feedback').text('').hide();
                    $('#editButton').prop('disabled', true).html(`
                        <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editDetail input, #editDetail select, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/resepluar/perbaruidetailresep/' . $resep['id_resep']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editDetail')[0].reset();
                            $('#id_obat').val(null).trigger('change');
                            $('#editDetail .is-invalid').removeClass('is-invalid');
                            $('#editDetail .invalid-feedback').text('').hide();
                            $('#editDetailResep').remove();
                            fetchDetailResep();
                            fetchObatOptions();
                            fetchStatusResep();
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
                        if (error.response.request.status === 422 || error.response.request.status === 401 || error.response.request.status === 404) {
                            showFailedToast(error.response.data.message);
                        } else {
                            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                        }
                    } finally {
                        $('#editButton').prop('disabled', false).html(`
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        `);
                        $('#editDetail input, #editDetail select, .btn-close').prop('disabled', false);
                    }
                });

                // Handle cancel button
                $('.cancel-edit').on('click', function() {
                    $('#editDetailResep').remove();
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
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#tambahDetail input, #tambahDetail select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/resepluar/tambahdetailresep/' . $resep['id_resep']) ?>`, formData, {
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
                    fetchDetailResep();
                    fetchObatOptions();
                    fetchStatusResep();
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
                if (error.response.request.status === 422 || error.response.request.status === 401) {
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

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchDetailResep();
                fetchObatOptions();
                fetchStatusResep();
            }
        });

        $('#refreshButton').on('click', function(e) {
            e.preventDefault();
            fetchDetailResep();
            fetchObatOptions();
            fetchStatusResep();
        });

        fetchDetailResep();
        fetchObatOptions();
        fetchStatusResep();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>