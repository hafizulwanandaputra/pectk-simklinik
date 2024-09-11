<?= $this->extend('dashboard/templates/dashboard'); ?>
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
                <div class="col-lg-3 fw-medium">Kontak Supplier</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['kontak_supplier'] ?>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <div class="col-lg-3 fw-medium">Pengguna</div>
                <div class="col-lg">
                    <div class="date">
                        <?= $pembelianobat['fullname'] ?> (@<?= $pembelianobat['username'] ?>)
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="mb-2">
        <table class="table table-sm table-hover table-responsive" style="width:100%; font-size: 9pt;">
            <thead>
                <tr class="align-middle">
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 2px; width: 0%;">Tindakan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 100%;">Nama Obat</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Jumlah</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Harga Satuan</th>
                    <th scope="col" class="bg-body-secondary border-secondary" style="border-bottom-width: 2px; width: 0%;">Total Harga</th>
                </tr>
            </thead>
            <tbody class="align-top" id="detail_pembelian_obat">
            </tbody>
            <thead>
                <tr>
                    <th scope="col" class="bg-body-secondary border-secondary text-nowrap" style="border-bottom-width: 0; border-top-width: 2px;" colspan="3"></th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end" style="border-bottom-width: 0; border-top-width: 2px;">Total</th>
                    <th scope="col" class="bg-body-secondary border-secondary text-end date" style="border-bottom-width: 0; border-top-width: 2px;" id="total_harga"></th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 id="deleteMessage"></h5>
                    <h6 class="mb-0" id="deleteSubmessage"></h6>
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
<?= $this->section('datatable'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script>
    // async function fetchSupplierOptions() {
    //     try {
    //         const response = await axios.get('<?= base_url('obat/supplierlist') ?>');

    //         if (response.data.success) {
    //             const options = response.data.data;
    //             const select = $('#id_supplier');

    //             // Clear existing options except the first one
    //             select.find('option:not(:first)').remove();

    //             // Loop through the options and append them to the select element
    //             options.forEach(option => {
    //                 select.append(`<option value="${option.value}">${option.text}</option>`);
    //             });
    //         }
    //     } catch (error) {
    //         showFailedToast('Gagal mendapatkan dokter.<br>' + error);
    //     }
    // }
    async function fetchDetailPembelianObat() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pembelianobat/detailpembelianobatlist/') . $pembelianobat['id_pembelian_obat'] ?>');

            const data = response.data;
            $('#detail_pembelian_obat').empty();

            let totalKeseluruhan = 0;

            data.forEach(function(detail_pembelian_obat) {
                const jumlah = parseInt(detail_pembelian_obat.jumlah); // Konversi jumlah ke integer
                const harga_satuan = parseInt(detail_pembelian_obat.harga_satuan); // Konversi harga obat ke integer
                const total_harga = jumlah * harga_satuan; // Hitung total harga
                totalKeseluruhan += total_harga;
                const detail_pembelian_obatElement = `
                    <tr>
                        <td></td>
                        <td>${detail_pembelian_obat.nama_obat}</td>
                        <td class="date text-end">${jumlah.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${harga_satuan.toLocaleString('id-ID')}</td>
                        <td class="date text-end">Rp${total_harga.toLocaleString('id-ID')}</td>
                    </tr>
                `;

                $('#detail_pembelian_obat').append(detail_pembelian_obatElement);
            });
            const totalKeseluruhanElement = `Rp${totalKeseluruhan.toLocaleString('id-ID')}`;
            $('#total_harga').append(totalKeseluruhanElement);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#detail_pembelian_obat').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(function() {
        // Store the ID of the user to be deleted
        var pembelianObatId;
        var pembelianObatName;
        var pembelianObatDate;

        // // Show delete confirmation modal
        // $(document).on('click', '.delete-btn', function() {
        //     pembelianObatId = $(this).data('id');
        //     pembelianObatName = $(this).data('name');
        //     pembelianObatDate = $(this).data('date');
        //     $('[data-bs-toggle="tooltip"]').tooltip('hide');
        //     $('#deleteMessage').html(`Hapus pembelian dari "` + pembelianObatName + `?`);
        //     $('#deleteSubmessage').html(`Tanggal Pembelian: ` + pembelianObatDate);
        //     $('#deleteModal').modal('show');
        // });

        // $('#confirmDeleteBtn').click(async function() {
        //     $('#deleteModal button').prop('disabled', true);
        //     $('#deleteMessage').addClass('mb-0').html('Mengapus, silakan tunggu...');
        //     $('#deleteSubmessage').hide();

        //     try {
        //         await axios.delete(`<?= base_url('/pembelianobat/delete') ?>/${pembelianObatId}`);
        //         showSuccessToast('Pembelian obat berhasil dihapus.');
        //         fetchPembelianObat();
        //     } catch (error) {
        //         showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        //     } finally {
        //         $('#deleteModal').modal('hide');
        //         $('#deleteMessage').removeClass('mb-0');
        //         $('#deleteSubmessage').show();
        //         $('#deleteModal button').prop('disabled', false);
        //     }
        // });

        // $('#pembelianObatForm').submit(async function(e) {
        //     e.preventDefault();

        //     const formData = new FormData(this);
        //     console.log("Form Data:", $(this).serialize());

        //     // Clear previous validation states
        //     $('#pembelianObatForm .is-invalid').removeClass('is-invalid');
        //     $('#pembelianObatForm .invalid-feedback').text('').hide();
        //     $('#submitButton').prop('disabled', true).html(`
        //         <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        //     `);

        //     // Disable form inputs
        //     $('#pembelianObatForm select').prop('disabled', true);

        //     try {
        //         const response = await axios.post(`<?= base_url('/pembelianobat/create') ?>`, formData, {
        //             headers: {
        //                 'Content-Type': 'multipart/form-data'
        //             }
        //         });

        //         if (response.data.success) {
        //             showSuccessToast(response.data.message, 'success');
        //             $('#pembelianObatForm')[0].reset();
        //             $('#id_supplier').val('');
        //             $('#pembelianObatForm .is-invalid').removeClass('is-invalid');
        //             $('#pembelianObatForm .invalid-feedback').text('').hide();
        //             $('#submitButtonContainer').hide();
        //             fetchPembelianObat();
        //         } else {
        //             console.log("Validation Errors:", response.data.errors);

        //             // Clear previous validation states
        //             $('#pembelianObatForm .is-invalid').removeClass('is-invalid');
        //             $('#pembelianObatForm .invalid-feedback').text('').hide();

        //             // Display new validation errors
        //             for (const field in response.data.errors) {
        //                 if (response.data.errors.hasOwnProperty(field)) {
        //                     const fieldElement = $('#' + field);
        //                     const feedbackElement = fieldElement.siblings('.invalid-feedback');

        //                     console.log("Target Field:", fieldElement);
        //                     console.log("Target Feedback:", feedbackElement);

        //                     if (fieldElement.length > 0 && feedbackElement.length > 0) {
        //                         fieldElement.addClass('is-invalid');
        //                         feedbackElement.text(response.data.errors[field]).show();

        //                         // Remove error message when the user corrects the input
        //                         fieldElement.on('input change', function() {
        //                             $(this).removeClass('is-invalid');
        //                             $(this).siblings('.invalid-feedback').text('').hide();
        //                         });
        //                     } else {
        //                         console.warn("Elemen tidak ditemukan pada field:", field);
        //                     }
        //                 }
        //             }
        //             console.error('Perbaiki kesalahan pada formulir.');
        //         }
        //     } catch (error) {
        //         showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        //     } finally {
        //         $('#submitButton').prop('disabled', false).html(`
        //             <i class="fa-solid fa-plus"></i>
        //         `);
        //         $('#pembelianObatForm select').prop('disabled', false);
        //     }
        // });
        // $('#refreshButton').on('click', function() {
        //     $('#pembelianObatContainer').empty();
        //     for (let i = 0; i < limit; i++) {
        //         $('#pembelianObatContainer').append(placeholder);
        //     }
        //     fetchPembelianObat(); // Refresh articles on button click
        // });

        fetchDetailPembelianObat();
    });
    // Show toast notification
    function showSuccessToast(message) {
        var toastHTML = `<div id="toast" class="toast fade show align-items-center text-bg-success border border-success rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
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
        var toastHTML = `<div id="toast" class="toast fade show align-items-center text-bg-danger border border-danger rounded-3 transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
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