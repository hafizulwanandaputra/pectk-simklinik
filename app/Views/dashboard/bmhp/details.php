<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/normal'); ?>
<style>
    /* Ensures the dropdown is visible outside the parent with overflow auto */
    .select2-container {
        z-index: 1050;
        /* Make sure it's above other elements, like modals */
        flex: 1 1 auto;
        min-width: 0;
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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/bmhp'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $bmhp['tanggal_bmhp']; ?> • <?= $bmhp['apoteker'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <a id="refreshButton" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Segarkan"><i class="fa-solid fa-sync"></i></a>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('bmhp/detailbmhp/' . $previous['id_bmhp']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['id_bmhp'] ?> • <?= $previous['tanggal_bmhp']; ?> • <?= $previous['apoteker'] ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada BMHP sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('bmhp/detailbmhp/' . $next['id_bmhp']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['id_bmhp'] ?> • <?= $next['tanggal_bmhp']; ?> • <?= $next['apoteker'] ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada BMHP berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Informasi Barang Medis Habis Pakai</div>
            <div style="font-size: 0.75em;">
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Tanggal dan Waktu</div>
                    <div class="col">
                        <div class="date">
                            <?= $bmhp['tanggal_bmhp'] ?>
                        </div>
                    </div>
                </div>
                <div class="mb-0 row g-1">
                    <div class="col-5 fw-medium text-truncate">Apoteker</div>
                    <div class="col">
                        <div class="date">
                            <?= $bmhp['apoteker']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm  overflow-auto">
            <div class="card-header" id="tambahDetailContainer" style="display: none;">
                <form id="tambahDetail" enctype="multipart/form-data">
                    <div class="row g-2">
                        <div class="col-12 has-validation">
                            <div class="input-group flex-nowrap">
                                <select class="form-select form-select-sm" id="id_batch_obat" name="id_batch_obat" aria-label="id_batch_obat" autocomplete="off">
                                    <option value="" disabled selected>-- Pilih Barang --</option>
                                </select>
                                <button id="expired_med_btn" class="btn btn-warning bg-gradient btn-sm" type="button" data-bs-toggle="tooltip" data-bs-title="Peringatan Barang Kedaluwarsa"><i class="fa-solid fa-triangle-exclamation"></i></button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col">
                            <input type="number" id="jumlah" name="jumlah" class="form-control form-control-sm" placeholder="Kuantitas">
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
                            <th scope="col" class="text-nowrap" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Tindakan</th>
                            <th scope="col" class="col-resize" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 100%;">Barang</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Kuantitas</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                            <th scope="col" style="background-color: var(--bs-card-cap-bg); border-bottom-width: 2px; width: 0%;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="align-top" id="detail_bmhp">
                        <tr>
                            <td colspan="5" class="text-center">Memuat detail BMHP...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row overflow-hidden d-flex align-items-end">
                    <div class="col fw-medium text-nowrap">Total Barang</div>
                    <div class="col text-end">
                        <div class="date text-truncate placeholder-glow" id="jumlah_bmhp">
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

        <div>
            <hr>
            <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-2">
                <button class="btn btn-danger  bg-gradient" type="button" id="cancelConfirmBtn" disabled><i class="fa-solid fa-xmark"></i> Batalkan Konfirmasi</button>
                <button class="btn btn-success  bg-gradient" type="button" id="confirmBtn" disabled><i class="fa-solid fa-check-double"></i> Konfirmasi</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="expiredModal" tabindex="-1" aria-labelledby="expiredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered modal-dialog-scrollable ">
            <div class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="expiredModalLabel" style="font-weight: bold;"></h6>
                    <button id="expiredCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div id="mediaAlert" class="alert alert-warning  mb-1 mt-1" role="alert">
                        <div class="d-flex align-items-start">
                            <div style="width: 12px; text-align: center;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div class="w-100 ms-3">
                                <p><strong id="total_exp_med"></strong> barang berikut akan segera kedaluwarsa. Pastikan Anda memilih barang yang masa kedaluwarsanya masih panjang.</p>
                                <small class="text-muted" id="expired_none_msg"><em>Tidak ada barang yang mendekati masa kedaluwarsa dalam 6 bulan ke depan.</em></small>
                                <ol id="expired_med_list">
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="confirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="confirmMessage"></h5>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Jangan Konfirmasi</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="confirmConfirmBtn">Konfirmasi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="cancelConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="cancelConfirmMessage"></h5>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Jangan Batalkan</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmCancelConfirmBtn">Batalkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 class="mb-0" id="deleteMessage"></h5>
                    <div class="row gx-2 pt-4">
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmDeleteBtn">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchObatOptions(selectedObat = null) {
        try {
            const response = await axios.get('<?= base_url('bmhp/obatlist/' . $bmhp['id_bmhp']) ?>');

            if (response.data.success) {
                const options = response.data.data;
                const select = $('#id_batch_obat');

                // Reset pilihan terlebih dahulu sebelum memuat ulang
                select.val('').trigger('change'); // Kosongkan pilihan

                // Hapus semua opsi kecuali yang pertama
                select.find('option:not(:first)').remove();
                select.find('optgroup').remove();

                // Buat satu optgroup untuk semua opsi
                const optgroup = $('<optgroup>', {
                    label: 'Diurutkan menurut tanggal kedaluwarsa terbaru'
                });

                options.forEach(option => {
                    optgroup.append(
                        $('<option>', {
                            value: option.value,
                            text: option.text
                        })
                    );
                });

                // Tambahkan optgroup ke select
                select.append(optgroup);

                // Pilih kembali jika ada selectedObat
                if (selectedObat) {
                    select.val(selectedObat).trigger('change');
                }
            } else {
                showFailedToast('Gagal mendapatkan barang.');
            }
        } catch (error) {
            showFailedToast('Gagal mendapatkan barang.<br>' + error);
        }
    }

    async function fetchStatusBMHP() {
        $('#loadingSpinner').show();

        try {
            const response1 = await axios.get('<?= base_url('bmhp/detailbmhplist/') . $bmhp['id_bmhp'] ?>');
            const response2 = await axios.get('<?= base_url('bmhp/bmhp/') . $bmhp['id_bmhp'] ?>');

            const data1 = response1.data;
            const data2 = response2.data;

            // Cek status `konfirmasi_kasir`
            if (data2.konfirmasi_kasir === "1") {
                $('#tambahDetailContainer').hide();
                $('#cancelConfirmBtn').prop('disabled', false);
                $('#confirmBtn').prop('disabled', true);
                if (data2.status === "1") {
                    $('#cancelConfirmBtn').prop('disabled', true);
                    $('#confirmBtn').prop('disabled', true);
                }
            } else if (data2.konfirmasi_kasir === "0") {
                $('#tambahDetailContainer').show();
                $('#cancelConfirmBtn').prop('disabled', true);
                $('#confirmBtn').prop('disabled', false);
                if (data1.length === 0) {
                    $('#confirmBtn').prop('disabled', true);
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDetailBMHP() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('bmhp/detailbmhplist/') . $bmhp['id_bmhp'] ?>');

            const data = response.data;
            $('#detail_bmhp').empty();

            let jumlahBMHP = 0;
            let totalHarga = 0;

            let allConfirmed = true; // Track overall confirmed status

            if (data.length === 0) {
                // Tampilkan pesan jika tidak ada data
                const emptyRow = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada barang medis habis pakai</td>
                    </tr>
                `;
                $('#detail_bmhp').append(emptyRow);
            } else {
                data.forEach(function(detail_bmhp) {
                    const jumlah = parseInt(detail_bmhp.jumlah); // Konversi jumlah ke integer
                    const harga_satuan = parseInt(detail_bmhp.harga_satuan); // Konversi harga obat ke integer
                    const total_harga = jumlah * harga_satuan; // Hitung total harga
                    totalHarga += total_harga;
                    jumlahBMHP += jumlah;

                    if (detail_bmhp.konfirmasi_kasir !== "1") {
                        allConfirmed = false;
                    }

                    const kategori_obat = detail_bmhp.kategori_obat ? `${detail_bmhp.kategori_obat}, ` : ``;
                    const signa = detail_bmhp.signa ? `${detail_bmhp.signa}` : `<em>Tidak ada dosis</em>`;
                    const catatan = detail_bmhp.catatan ? `${detail_bmhp.catatan}` : `<em>Tidak ada catatan</em>`;
                    const nama_batch = detail_bmhp.nama_batch ? `${detail_bmhp.nama_batch}` : `<em>Tidak ada batch</em>`;

                    const detail_bmhpElement = `
                    <tr>
                        <td class="tindakan">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-body text-nowrap bg-gradient edit-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${detail_bmhp.id_detail_bmhp}" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-danger text-nowrap bg-gradient delete-btn" style="--bs-btn-padding-y: 0.15rem; --bs-btn-padding-x: 0.5rem; --bs-btn-font-size: 1em;" data-id="${detail_bmhp.id_detail_bmhp}" data-name="${detail_bmhp.nama_obat}" data-bs-toggle="tooltip" data-bs-title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                        <td><i class="fa-solid fa-prescription"></i> ${detail_bmhp.nama_obat}
                        <small>
                            <ul class="ps-3 mb-0">
                                <li>${kategori_obat}${detail_bmhp.bentuk_obat}</li>
                                <li>${nama_batch}</li>
                            </ul>
                        </small></td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                    $('#detail_bmhp').append(detail_bmhpElement);
                    if (detail_bmhp.status === "1") {
                        $('.edit-btn').prop('disabled', true);
                        $('.delete-btn').prop('disabled', true);
                    } else if (detail_bmhp.status === "0") {
                        $('.edit-btn').prop('disabled', false);
                        $('.delete-btn').prop('disabled', false);
                    }
                });
            }
            const totalHargaElement = `Rp${totalHarga.toLocaleString('id-ID')}`;
            const jumlahBMHPElement = `${jumlahBMHP.toLocaleString('id-ID')}`;
            $('#total_harga').text(totalHargaElement);
            $('#jumlah_bmhp').text(jumlahBMHPElement);
            $('[data-bs-toggle="tooltip"]').tooltip();
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('.col-resize').css('min-width', '0');
            $('#detail_bmhp').empty();
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
            if (data.update_resep) {
                console.log("Received update from WebSocket");
                const selectedObat = $('#id_batch_obat').val();
                await fetchObatOptions(selectedObat);
                await fetchStatusBMHP();
                fetchDetailBMHP();
            } else if (data.update) {
                console.log("Received update from WebSocket");
                await fetchStatusBMHP();
                fetchDetailBMHP();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_batch_obat').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        $('#expired_med_btn').on('click', async function(ə) {
            ə.preventDefault();
            var $this = $(this);
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);

            try {
                let response = await axios.get(`<?= base_url('/bmhp/obatkedaluwarsa') ?>`);
                const data = response.data;
                const safeText = v => (v && v.trim() !== '') ? v : null;

                $('#expiredModalLabel').text('Peringatan Barang Kedaluwarsa');

                // Kosongkan daftar sebelum menambahkan elemen baru
                $('#expired_med_list').empty();

                $('#total_exp_med').text(data.jumlah);

                if (data.jumlah > 0) {
                    $('#expired_none_msg').hide();
                    data.data.forEach(item => {
                        const isiObat = safeText(item.isi_obat);
                        const kategori = safeText(item.kategori_obat);
                        const bentuk = safeText(item.bentuk_obat);

                        const detail = [isiObat, kategori, bentuk]
                            .filter(Boolean)
                            .join(' • ');

                        const batch = safeText(item.nama_batch) ?? '<em>Tidak ada</em>';

                        $('#expired_med_list').append(`
                            <li>
                                <strong>${item.nama_obat}</strong>
                                <br>
                                <small>
                                    ${detail || '<em>Tidak ada detail barang</em>'}
                                    <br>
                                    <em>Batch</em>: ${batch}
                                    <br>
                                    Stok: ${item.stok_tersisa.toLocaleString('id-ID')}
                                    <br>
                                    Harga: Rp${item.harga}
                                    <br>
                                    <span class="text-danger">EXP: <strong>${item.tgl_kedaluwarsa}</strong></span>
                                </small>
                            </li>
                        `);
                    });
                } else {
                    $('#expired_med_list').hide();
                    $('#expired_none_msg').show();
                }

                $('#expiredModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-triangle-exclamation"></i>`);
            }
        });

        $('#expiredModal').on('hidden.bs.modal', function() {
            $('#expired_med_list').empty();
            $('#expired_none_msg').show();
        });

        var detailBMHPId;
        var detailBMHPName;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            detailBMHPId = $(this).data('id');
            detailBMHPName = $(this).data('name');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#deleteMessage').html(`Hapus item "` + detailBMHPName + `?`);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.delete(`<?= base_url('/bmhp/hapusdetailbmhp') ?>/${detailBMHPId}`);
                const selectedObat = $('#id_batch_obat').val();
                await fetchObatOptions(selectedObat);
                await fetchStatusBMHP();
                fetchDetailBMHP();
            } catch (error) {
                if (error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
                $(this).text(`Hapus`); // Mengembalikan teks tombol asal
            }
        });

        $('#confirmBtn').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#confirmMessage').html(`BMHP ini akan diproses transaksinya oleh kasir. Konfirmasi BMHP ini?`);
            $('#confirmModal').modal('show');
        });

        $('#confirmConfirmBtn').click(async function() {
            $('#confirmModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.post(`<?= base_url('/bmhp/konfirmasi/' . $bmhp['id_bmhp']) ?>`);
                await fetchObatOptions();
                await fetchStatusResep();
                fetchDetailResep();
            } catch (error) {
                if (error.response.request.status === 400) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#confirmModal').modal('hide');
                $('#confirmModal button').prop('disabled', false);
                $(this).text(`Konfirmasi`); // Mengembalikan teks tombol asal
            }
        });

        $('#cancelConfirmBtn').on('click', function() {
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $('#cancelConfirmMessage').html(`BMHP ini akan dibatalkan dan tidak bisa diproses oleh apoteker. Batalkan konfirmasi BMHP ini?`);
            $('#cancelConfirmModal').modal('show');
        });

        $('#confirmCancelConfirmBtn').click(async function() {
            $('#cancelConfirmModal button').prop('disabled', true);
            $(this).html(`<?= $this->include('spinner/spinner'); ?>`); // Menampilkan pesan loading

            try {
                await axios.post(`<?= base_url('/bmhp/batalkonfirmasi/' . $bmhp['id_bmhp']) ?>`);
                await fetchObatOptions();
                await fetchStatusResep();
                fetchDetailResep();
            } catch (error) {
                if (error.response.request.status === 400) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#cancelConfirmModal').modal('hide');
                $('#cancelConfirmModal button').prop('disabled', false);
                $(this).text(`Batalkan`); // Mengembalikan teks tombol asal
            }
        });

        $(document).on('click', '.edit-btn', async function() {
            const $this = $(this);
            const id = $(this).data('id');
            const $row = $this.closest('tr');
            $('[data-bs-toggle="tooltip"]').tooltip('hide');
            $this.prop('disabled', true).html(`<?= $this->include('spinner/spinner'); ?>`);
            $('#editDetailBMHP').remove();
            try {
                const response = await axios.get(`<?= base_url('/bmhp/detailbmhpitem') ?>/${id}`);
                const signa = response.data.signa ?? "-";
                const catatan = response.data.catatan ?? "-";
                const formHtml = `
                <tr id="editDetailBMHP">
                    <td colspan="5">
                        <form id="editDetail" enctype="multipart/form-data">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-bold">Edit Barang</div>
                            <button type="button" class="text-end btn-close ms-auto cancel-edit"></button>
                        </div>
                        <input type="hidden" id="id_detail_bmhp" name="id_detail_bmhp" value="${response.data.id_detail_bmhp}">
                        <div class="row g-2">
                            <div class="col">
                                <input type="text" id="jumlah_edit" name="jumlah_edit" class="form-control form-control-sm" placeholder="Kuantitas" value="${response.data.jumlah}" autocomplete="off">
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
                        <?= $this->include('spinner/spinner'); ?> Edit
                    `);

                    // Disable form inputs
                    $('#editDetail input, #editDetail select, .btn-close').prop('disabled', true);

                    try {
                        const response = await axios.post(`<?= base_url('/bmhp/perbaruidetailbmhp/' . $bmhp['id_bmhp']) ?>`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        if (response.data.success) {
                            $('#editDetail')[0].reset();
                            $('#id_batch_obat').val(null).trigger('change');
                            $('#editDetail .is-invalid').removeClass('is-invalid');
                            $('#editDetail .invalid-feedback').text('').hide();
                            $('#editDetailBMHP').remove();
                            const selectedObat = $('#id_batch_obat').val();
                            await fetchObatOptions(selectedObat);
                            await fetchStatusBMHP();
                            fetchDetailBMHP();
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
                    $('#editDetailBMHP').remove();
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
                <?= $this->include('spinner/spinner'); ?> Tambah
            `);

            // Disable form inputs
            $('#tambahDetail input, #tambahDetail select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/bmhp/tambahdetailbmhp/' . $bmhp['id_bmhp']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    $('#tambahDetail')[0].reset();
                    $('#id_batch_obat').val('');
                    $('#jumlah').val('');
                    $('#tambahDetail .is-invalid').removeClass('is-invalid');
                    $('#tambahDetail .invalid-feedback').text('').hide();
                    const selectedObat = $('#id_batch_obat').val();
                    await fetchObatOptions(selectedObat);
                    await fetchStatusBMHP();
                    fetchDetailBMHP();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#tambahDetail .is-invalid').removeClass('is-invalid');
                    $('#tambahDetail .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Coba cari feedback di dalam .has-validation
                            let feedbackElement = fieldElement.closest('.has-validation').find('.invalid-feedback');

                            // Jika tidak ketemu, fallback ke sibling (untuk kasus lama)
                            if (feedbackElement.length === 0) {
                                feedbackElement = fieldElement.siblings('.invalid-feedback');
                            }

                            console.log("Target Field:", fieldElement);
                            console.log("Target Feedback:", feedbackElement);

                            if (fieldElement.length > 0 && feedbackElement.length > 0) {
                                fieldElement.addClass('is-invalid');
                                feedbackElement.text(response.data.errors[field]).show();

                                // Hapus validasi jika pengguna memperbaiki input
                                fieldElement.on('input change', function() {
                                    $(this).removeClass('is-invalid');

                                    // Ulangi logika untuk cari feedbackElement
                                    let currentFeedback = $(this).closest('.has-validation').find('.invalid-feedback');
                                    if (currentFeedback.length === 0) {
                                        currentFeedback = $(this).siblings('.invalid-feedback');
                                    }

                                    currentFeedback.text('').hide();
                                });
                            } else {
                                console.warn("Elemen atau feedback tidak ditemukan:", field);
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

        $(document).on('visibilitychange', async function() {
            if (document.visibilityState === "visible") {
                const selectedObat = $('#id_batch_obat').val();
                await fetchObatOptions(selectedObat);
                await fetchStatusBMHP();
                fetchDetailBMHP();
            }
        });

        $('#refreshButton').on('click', async function(e) {
            e.preventDefault();
            const selectedObat = $('#id_batch_obat').val();
            await fetchObatOptions(selectedObat);
            await fetchStatusBMHP();
            fetchDetailBMHP();
        });

        await fetchObatOptions();
        await fetchStatusBMHP();
        fetchDetailBMHP();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>