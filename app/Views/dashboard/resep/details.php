<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/resep'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4 pt-3">
    <div class="d-xxl-flex justify-content-center">
        <div class="no-fluid-content">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Informasi Pasien Rawat Jalan</legend>
                <div class="row">
                    <div class="col-lg-6" style="font-size: 9pt;">
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Tanggal dan Waktu</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['tanggal_resep'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Dokter</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['dokter'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Nama Pasien</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['nama_pasien'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Nomor Rekam Medis</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['no_rm'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Nomor Registrasi</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['nomor_registrasi'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="font-size: 9pt;">
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Jenis Kelamin</div>
                            <div class="col-lg">
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
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Tanggal Lahir</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['tanggal_lahir'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Alamat</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['alamat'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Nomor Telepon</div>
                            <div class="col-lg">
                                <div class="date">
                                    <?= $resep['telpon'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-lg-4 fw-medium">Status Konfirmasi</div>
                            <div class="col-lg">
                                <div class="date" id="confirmedStatus">
                                    Memuat status...
                                </div>
                                <?php if (session()->get('role') != 'Dokter') : ?>
                                    <button id="refreshConfirmed" type="button" class="btn btn-link" style="--bs-btn-padding-y: 0; --bs-btn-padding-x: 0; --bs-btn-font-size: 9pt;">Perbarui Status</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <?php if (session()->get('role') == 'Dokter' || session()->get('role') == 'Admin') : ?>
                <fieldset id="tambahDetailContainer" class="border rounded-3 px-2 py-0 mb-3" style="display: none;">
                    <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Tambah Detail Resep</legend>
                    <form id="tambahDetail" enctype="multipart/form-data">
                        <div class="row g-2">
                            <div class="col-12">
                                <select class="form-select form-select-sm rounded-3" id="id_obat" name="id_obat" aria-label="id_obat" autocomplete="off">
                                    <option value="" disabled selected>-- Pilih Obat --</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">
                                <input type="text" id="signa" name="signa" class="form-control form-control-sm rounded-3" placeholder="Dosis" autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">
                                <input type="text" id="catatan" name="catatan" class="form-control form-control-sm rounded-3" placeholder="Catatan" list="list_catatan" autocomplete="off">
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
                                <select class="form-select form-select-sm  rounded-3" id="cara_pakai" name="cara_pakai" aria-label="cara_pakai">
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
                                <input type="number" id="jumlah" name="jumlah" class="form-control form-control-sm rounded-3" placeholder="Jumlah">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                                <button type="submit" id="addButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-sm mb-0" style="width:100%; font-size: 9pt;">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap tindakan" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Obat</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jumlah</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                            <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="detail_resep">
                        <tr>
                            <td colspan="5" class="text-center">Memuat detail resep...</td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 2px;" colspan="1"></th>
                            <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;">Total</th>
                            <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="jumlah_resep"></th>
                            <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;"></th>
                            <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="total_harga"></th>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="mb-3">
                <hr>
                <?php if (session()->get('role') != 'Dokter') : ?>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                        <button class="btn btn-body rounded-3 bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/resep/etiket-dalam/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Etiket Obat Dalam</button>
                        <button class="btn btn-body rounded-3 bg-gradient" type="button" id="printBtn2" onclick="window.open(`<?= base_url('/resep/etiket-luar/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Etiket Obat Luar</button>
                        <button class="btn btn-body rounded-3 bg-gradient" type="button" id="printBtn3" onclick="window.open(`<?= base_url('/resep/print/' . $resep['id_resep']) ?>`)" disabled><i class="fa-solid fa-print"></i> Cetak Resep</button>
                    </div>
                <?php endif; ?>
                <?php if (session()->get('role') != 'Apoteker') : ?>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                        <button class="btn btn-danger rounded-3 bg-gradient" type="button" id="cancelConfirmBtn" disabled><i class="fa-solid fa-xmark"></i> Batalkan Konfirmasi</button>
                        <button class="btn btn-success rounded-3 bg-gradient" type="button" id="confirmBtn" disabled><i class="fa-solid fa-check-double"></i> Konfirmasi</button>
                    </div>
                <?php endif; ?>
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

    <div class="modal modal-sheet p-4 py-md-5 fade" id="confirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="confirmMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmConfirmBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="cancelConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="cancelConfirmMessage"></h5>
                </div>
                <div class="modal-footer flex-nowrap p-0" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end" style="border-right: 1px solid var(--bs-border-color-translucent)!important;" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" id="confirmCancelConfirmBtn">Ya</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    <?php if (session()->get('role') != 'Apoteker') : ?>
        async function fetchObatOptions() {
            try {
                const response = await axios.get('<?= base_url('resep/obatlist/' . $resep['id_resep']) ?>');

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
                const response = await axios.get('<?= base_url('resep/resep/') . $resep['id_resep'] ?>');

                const data = response.data;

                // Cek status `status`
                if (data.status === "1" || data.confirmed === "1"
                    <?= (session()->get('role') != 'Admin') ? ' || data.dokter != `' . session()->get("fullname") . '`' : ''; ?>) {
                    $('#tambahDetailContainer').hide();
                } else if (data.status === "0" || data.confirmed === "0") {
                    $('#tambahDetailContainer').show();
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                // Hide the spinner when done
                $('#loadingSpinner').hide();
            }
        }
    <?php endif; ?>

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

                    const detail_resepElement = `
                    <tr>
                        <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient rounded-start-3 edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_resep.id_detail_resep}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient rounded-end-3 delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 9pt;" data-id="${detail_resep.id_detail_resep}" data-name="${detail_resep.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td class="text-nowrap"><i class="fa-solid fa-prescription"></i> ${detail_resep.nama_obat}<br><small>${detail_resep.kategori_obat} • ${detail_resep.bentuk_obat} • ${detail_resep.signa} • ${detail_resep.cara_pakai}<br>${detail_resep.catatan}</small></td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_resep').append(detail_resepElement);
                    <?php if (session()->get('role') == 'Apoteker') : ?>
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    <?php else : ?>
                        if (detail_resep.status === "1"
                            <?= (session()->get('role') != 'Admin') ? ' || detail_resep.dokter != `' . session()->get("fullname") . '`' : ''; ?>) {
                            $('.edit-btn').prop('disabled', true);
                            $('.delete-btn').prop('disabled', true);
                        } else if (detail_resep.status === "0") {
                            if (detail_resep.confirmed === "1") {
                                $('.edit-btn').prop('disabled', true);
                                $('.delete-btn').prop('disabled', true);
                                $('#cancelConfirmBtn').prop('disabled', false);
                                $('#confirmBtn').prop('disabled', true);
                            } else if (detail_resep.confirmed === "0") {
                                $('.edit-btn').prop('disabled', false);
                                $('.delete-btn').prop('disabled', false);
                                $('#cancelConfirmBtn').prop('disabled', true);
                                $('#confirmBtn').prop('disabled', false);
                            }
                        }
                    <?php endif; ?>

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
            $('#detail_resep').empty();
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
                await axios.delete(`<?= base_url('/resep/hapusdetailresep') ?>/${detailResepId}`);
                fetchDetailResep();
                <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
                <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
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

        $('#confirmBtn').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#confirmMessage').html(`Resep ini akan diproses oleh apoteker. Konfirmasi resep ini?`);
            $('#confirmModal').modal('show');
        });

        $('#confirmConfirmBtn').click(async function() {
            $('#confirmModal button').prop('disabled', true);
            $('#confirmMessage').html('Mengonfirmasi, silakan tunggu...');

            try {
                await axios.post(`<?= base_url('/resep/confirm/' . $resep['id_resep']) ?>`);
                fetchStatusKonfirmasi();
                fetchDetailResep();
                <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
                <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#confirmModal').modal('hide');
                $('#confirmModal button').prop('disabled', false);
            }
        });

        $('#cancelConfirmBtn').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#cancelConfirmMessage').html(`Resep ini akan dibatalkan dan tidak bisa diproses oleh apoteker. Batalkan konfirmasi resep ini?`);
            $('#cancelConfirmModal').modal('show');
        });

        $('#confirmCancelConfirmBtn').click(async function() {
            $('#cancelConfirmModal button').prop('disabled', true);
            $('#cancelConfirmMessage').html('Membatalkan, silakan tunggu...');

            try {
                await axios.post(`<?= base_url('/resep/cancel/' . $resep['id_resep']) ?>`);
                fetchStatusKonfirmasi();
                fetchDetailResep();
                <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
                <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#cancelConfirmModal').modal('hide');
                $('#cancelConfirmModal button').prop('disabled', false);
            }
        });

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 11px; height: 11px;" aria-hidden="true"></span>`);
            $('#editDetailResep').remove();
            try {
                const response = await axios.get(`<?= base_url('/resep/detailresepitem') ?>/${id}`);
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
                                <input type="text" id="signa_edit" name="signa_edit" class="form-control form-control-sm rounded-3" placeholder="Dosis" value="${response.data.signa}" autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">
                                <input type="text" id="catatan_edit" name="catatan_edit" class="form-control form-control-sm rounded-3" placeholder="Catatan" value="${response.data.catatan}" list="list_catatan_edit" autocomplete="off">
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
                                <select class="form-select form-select-sm  rounded-3" id="cara_pakai_edit" name="cara_pakai_edit" aria-label="cara_pakai">
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
                                <input type="text" id="jumlah_edit" name="jumlah_edit" class="form-control form-control-sm rounded-3" placeholder="Jumlah" value="${response.data.jumlah}" autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                                <button type="submit" id="addButton" class="btn btn-primary bg-gradient rounded-3 text-nowrap">
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
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Edit
                    `);

                    // Disable form inputs
                    $('#editDetail input, #editDetail select, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/resep/perbaruidetailresep/' . $resep['id_resep']) ?>`, formData, {
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
                            <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
                            <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
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
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#tambahDetail input, #tambahDetail select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/resep/tambahdetailresep/' . $resep['id_resep']) ?>`, formData, {
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
                    <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
                    <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
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

        $('#refreshConfirmed').on('click', function() {
            fetchStatusKonfirmasi();
            fetchDetailResep();
            <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
            <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
        });

        fetchStatusKonfirmasi();
        fetchDetailResep();
        <?= (session()->get('role') != 'Apoteker') ? 'fetchObatOptions();' : '' ?>
        <?= (session()->get('role') != 'Apoteker') ? 'fetchStatusResep();' : '' ?>
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>