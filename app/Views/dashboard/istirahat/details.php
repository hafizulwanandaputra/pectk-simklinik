<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($istirahat['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($istirahat['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggal_registrasi = date('Y-m-d', strtotime($istirahat['tanggal_registrasi']));
$today = date('Y-m-d');
$seven_days_ago = date('Y-m-d', strtotime('-6 days'));
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

    #site_marking_canvas {
        cursor: crosshair;
    }

    .canvas-container::-webkit-scrollbar {
        width: 16px;
        height: 16px;
    }

    .canvas-container::-webkit-scrollbar-track {
        background-color: var(--bs-secondary-bg);
    }

    .canvas-container::-webkit-scrollbar-thumb {
        background-color: var(--bs-secondary);
    }

    .canvas-container::-webkit-scrollbar-thumb:hover {
        background-color: var(--bs-body-color);
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/istirahat'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $istirahat['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $istirahat['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('istirahat/details/' . $previous['id_keterangan_istirahat']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada surat sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('istirahat/details/' . $next['id_keterangan_istirahat']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada surat berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside">
    <div class="sticky-top" style="z-index: 99;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-secondary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= ($tanggal_registrasi < $seven_days_ago) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_keterangan_istirahat']) ? 'active activeLink' : '' ?>" href="<?= base_url('istirahat/details/' . $list['id_keterangan_istirahat']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm" style="font-size: 0.75em;"><?= $list['tanggal_registrasi'] ?></div>
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
            <?= form_open_multipart('istirahat/update/' . $istirahat['id_keterangan_istirahat'], 'id="SuratForm"'); ?>
            <?= csrf_field(); ?>
            <?php if ($tanggal_registrasi < $seven_days_ago) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat surat keterangan istirahat pasien pada <?= date('Y-m-d', strtotime($istirahat['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien (7 hari yang lalu hingga hari ini).
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="diagnosis" name="diagnosis" value="" autocomplete="off" dir="auto" placeholder="diagnosis">
                        <label for="diagnosis">Diagnosis</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="" autocomplete="off" dir="auto" placeholder="tanggal_mulai">
                            <label for="tanggal_mulai">Tanggal Mulai<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="" autocomplete="off" dir="auto" placeholder="tanggal_selesai">
                            <label for="tanggal_selesai">Tanggal Selesai<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <div class="btn-group">
                        <button class="btn btn-body dropdown-toggle bg-gradient" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-print"></i> Cetak Form</button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm w-100">
                            <li><a class="dropdown-item" href="<?= base_url('istirahat/export/' . $istirahat['id_keterangan_istirahat']) ?>?side=left" target="_blank">Sisi kiri</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('istirahat/export/' . $istirahat['id_keterangan_istirahat']) ?>?side=right" target="_blank">Sisi kanan</a></li>
                        </ul>
                    </div>
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
    async function fetchSurat() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('istirahat/view/') . $istirahat['id_keterangan_istirahat'] ?>');
            const data = response.data;

            $('#diagnosis').val(data.diagnosis);
            $('#tanggal_mulai').val(data.tanggal_mulai);
            $('#tanggal_selesai').val(data.tanggal_selesai);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        // Fungsi untuk mengunggah gambar dari kanvas
        $('#SuratForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#SuratForm .is-invalid').removeClass('is-invalid');
            $('#SuratForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#SuratForm input, #SuratForm select, #SuratForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('istirahat/update/' . $istirahat['id_keterangan_istirahat']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSurat();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SuratForm .is-invalid').removeClass('is-invalid');
                    $('#SuratForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
                            let feedbackElement = fieldElement.siblings('.invalid-feedback');

                            // Handle input-group cases
                            if (fieldElement.closest('.input-group').length) {
                                feedbackElement = fieldElement.closest('.input-group').find('.invalid-feedback');
                            }

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
                $('#submitBtn').prop('disabled', false).html(`
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            `);
                $('#SuratForm input, #SuratForm select, #SuratForm button').prop('disabled', false);
            }
        });
        // $('#loadingSpinner').hide();
        fetchSurat();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>