<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($butawarna['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($butawarna['tanggal_registrasi'])));

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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/butawarna'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $butawarna['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $butawarna['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('butawarna/details/' . $previous['id_keterangan_buta_warna']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada surat sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('butawarna/details/' . $next['id_keterangan_buta_warna']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_keterangan_buta_warna']) ? 'active activeLink' : '' ?>" href="<?= base_url('butawarna/details/' . $list['id_keterangan_buta_warna']); ?>">
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
            <?= form_open_multipart('butawarna/update/' . $butawarna['id_keterangan_buta_warna'], 'id="SuratForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (date('Y-m-d', strtotime($butawarna['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($butawarna['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="keperluan" name="keperluan" value="" autocomplete="off" dir="auto" placeholder="keperluan">
                        <label for="keperluan">Keperluan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="fw-bold mb-2 border-bottom">Mata Kanan (OD)</div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="od_ukuran_kacamata" name="od_ukuran_kacamata" value="" autocomplete="off" dir="auto" placeholder="od_ukuran_kacamata" list="od_ukuran_kacamata_list">
                                <label for="od_ukuran_kacamata">Ukuran Kacamata</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="od_visus" name="od_visus" value="" autocomplete="off" dir="auto" placeholder="od_visus" list="od_visus_list">
                                <datalist id="od_visus_list">
                                </datalist>
                                <label for="od_visus">Visus</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fw-bold mb-2 border-bottom">Mata Kiri (OS)</div>
                        <div class="mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="os_ukuran_kacamata" name="os_ukuran_kacamata" value="" autocomplete="off" dir="auto" placeholder="os_ukuran_kacamata" list="os_ukuran_kacamata_list">
                                <label for="os_ukuran_kacamata">Ukuran Kacamata</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="os_visus" name="os_visus" value="" autocomplete="off" dir="auto" placeholder="os_visus" list="os_visus_list">
                                <datalist id="os_visus_list">
                                </datalist>
                                <label for="os_visus">Visus</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="status_buta_warna" class="col col-form-label">Status Buta Warna</label>
                        <div class="col-lg col-form-label">
                            <div class="d-flex flex-column align-items-start">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_buta_warna" id="status_buta_warna1" value="BUTA WARNA">
                                    <label class="form-check-label" for="status_buta_warna1">
                                        Buta Warna
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_buta_warna" id="status_buta_warna2" value="BUTA WARNA PARSIAL">
                                    <label class="form-check-label" for="status_buta_warna2">
                                        Buta Warna Parsial
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_buta_warna" id="status_buta_warna3" value="TIDAK BUTA WARNA">
                                    <label class="form-check-label" for="status_buta_warna3">
                                        Tidak Buta Warna
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('butawarna/export/' . $butawarna['id_keterangan_buta_warna']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
            const response = await axios.get('<?= base_url('butawarna/view/') . $butawarna['id_keterangan_buta_warna'] ?>');
            const data = response.data;

            $('#keperluan').val(data.keperluan);
            $('#od_ukuran_kacamata').val(data.od_ukuran_kacamata);
            $('#od_visus').val(data.od_visus);
            $('#os_ukuran_kacamata').val(data.os_ukuran_kacamata);
            $('#os_visus').val(data.os_visus);
            const status_buta_warna = data.status_buta_warna;
            if (status_buta_warna) {
                $("input[name='status_buta_warna'][value='" + status_buta_warna + "']").prop('checked', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function loadVisus() {
        try {
            const response = await axios.get('<?= base_url('butawarna/listvisus') ?>');
            const visus = response.data.data;

            // Array dari ID datalist yang ingin diisi
            const dataListIds = ['#od_visus_list', '#os_visus_list'];

            // Kosongkan dan isi setiap datalist
            dataListIds.forEach(id => {
                const dataList = $(id);
                dataList.empty(); // Kosongkan datalist sebelumnya
                visus.forEach(item => {
                    dataList.append(`<option value="${item.value}"></option>`);
                });
            });
        } catch (error) {
            console.error('Gagal memuat visus:', error);
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
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#SuratForm input, #SuratForm select, #SuratForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('butawarna/update/' . $butawarna['id_keterangan_buta_warna']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSurat();
                    loadVisus();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#SuratForm .is-invalid').removeClass('is-invalid');
                    $('#SuratForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (["status_buta_warna"].includes(field)) {
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
                                        feedbackElement.text('').hide();
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
                $('#SuratForm input, #SuratForm select, #SuratForm button').prop('disabled', false);
            }
        });
        // $('#loadingSpinner').hide();
        fetchSurat();
        loadVisus();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>