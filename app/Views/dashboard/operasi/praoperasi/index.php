<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($operasi['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($operasi['tanggal_registrasi'])));

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
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/operasi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?> (Dalam Pengembangan)</div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $operasi['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $operasi['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/praoperasi/' . $previous['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_booking']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/praoperasi/' . $next['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_booking']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/spko/' . $operasi['id_sp_operasi']); ?>">SPKO</a>
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('operasi/praoperasi/' . $operasi['id_sp_operasi']); ?>">Pra Operasi</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 <?= ($activeSegment === $list['id_sp_operasi']) ? 'active activeLink' : '' ?>" href="<?= base_url('operasi/praoperasi/' . $list['id_sp_operasi']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm" style="font-size: 0.75em;"><?= $list['nomor_booking'] ?></div>
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
            <?= form_open_multipart('/operasi/praoperasi/update/' . $operasi_pra['id_operasi_pra'], 'id="PraOperasiForm"'); ?>
            <?= csrf_field(); ?>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/operasi/spko/export/' . $operasi['id_sp_operasi']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchSPKO() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('operasi/praoperasi/view/') . $operasi_pra['id_operasi_pra'] ?>');
            const data = response.data;

            $('#tanggal_operasi').val(data.tanggal_operasi);
            $('#jam_operasi').val(data.jam_operasi);
            $('#diagnosa').val(data.diagnosa);
            $('#jenis_tindakan').val(data.jenis_tindakan).trigger('change');
            $('#indikasi_operasi').val(data.indikasi_operasi);
            const jenis_bius = data.jenis_bius;
            if (jenis_bius) {
                $("input[name='jenis_bius'][value='" + jenis_bius + "']").prop('checked', true);
            }
            $('#tipe_bayar').val(data.tipe_bayar);
            $('#ruang_operasi').val(data.ruang_operasi);
            const rajal_ranap = data.rajal_ranap;
            if (rajal_ranap) {
                $("input[name='rajal_ranap'][value='" + rajal_ranap + "']").prop('checked', true);
            }
            if (data.dokter_operator !== 'Belum Ada') {
                $('#dokter_operator').val(data.dokter_operator);
            }

            // Site Marking
            $('#diagnosa_site_marking').val(data.diagnosa_site_marking);
            $('#tindakan_site_marking').val(data.tindakan_site_marking);
            if (data.site_marking) {
                $('#site_marking_preview').attr('src', `<?= base_url('uploads/site_marking') ?>/${data.site_marking}?t=${new Date().getTime()}`);
                $('#site_marking_preview_div').show();
                $('#site_marking').val(data.site_marking);
            } else {
                $('#site_marking_preview_div').hide();
                $('#site_marking').val('');
            }
            $('#cancel_changes').hide();

            // Pasien dan Keluarga
            $('#nama_pasien_keluarga').val(data.nama_pasien_keluarga);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        // Fungsi untuk mengunggah gambar dari kanvas
        $('#SPKOForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            const siteMarkingValue = $('#site_marking').val();

            // Cek apakah site_marking berisi data base64 yang valid
            if (siteMarkingValue.startsWith('data:image/')) {
                formData.append('site_marking', siteMarkingValue);
            }

            // Clear previous validation states
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#SPKOForm .is-invalid').removeClass('is-invalid');
            $('#SPKOForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#SPKOForm input, #SPKOForm select, #SPKOForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('/operasi/spko/update/' . $operasi['id_sp_operasi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSPKO();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SPKOForm .is-invalid').removeClass('is-invalid');
                    $('#SPKOForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['jenis_bius', 'rajal_ranap'].includes(field)) {
                                const radioGroup = $(`input[name='${field}']`); // Ambil grup radio berdasarkan nama
                                const feedbackElement = radioGroup.closest('.radio-group').find('.invalid-feedback'); // Gunakan pembungkus dengan class tertentu

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        radioGroup.removeClass('is-invalid');
                                        feedbackElement.text('').hide();
                                    });
                                } else {
                                    console.warn("Radio group tidak ditemukan untuk field:", field);
                                }
                            } else {
                                const feedbackElement = fieldElement.siblings('.invalid-feedback');

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
                    }
                    console.error('Perbaiki kesalahan pada formulir.');
                }
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            `);
                $('#SPKOForm input, #SPKOForm select, #SPKOForm button').prop('disabled', false);
            }
        });
        $('#loadingSpinner').hide();
        // fetchSPKO();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>