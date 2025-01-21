<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<?= $this->include('select2/floating'); ?>
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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/rawatjalan'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $rawatjalan['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/laporanrajal/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/laporanrajal/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                        <?php if (session()->get('role') != 'Dokter') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <?php endif; ?>
                        <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/optik/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                            <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('rawatjalan/laporanrajal/' . $rawatjalan['id_rawat_jalan']); ?>">Tindakan Rajal</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active activeLink' : '' ?>" href="<?= base_url('rawatjalan/laporanrajal/' . $list['id_rawat_jalan']); ?>">
                                <div class="text-center">
                                    <div class="text-nowrap lh-sm"><?= $list['nomor_registrasi']; ?></div>
                                    <div class="text-nowrap lh-sm date" style="font-size: 0.75em;"><?= $list['tanggal_registrasi'] ?></div>
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
            <?= form_open_multipart('/rawatjalan/laporanrajal/update/' . $laporanrajal['id_lp_tindakan_rajal'], 'id="laporanRajalForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="mb-2">
                    <label for="nama_perawat" class="form-label">
                        Perawat
                    </label>
                    <div id="nama_perawat">
                        <?php
                        $index = 1; // Mulai dengan angka 1
                        foreach ($nama_perawat as $list) :
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perawat_<?= $index ?>" name="nama_perawat[]" value="<?= $list['fullname'] ?>">
                                <label class="form-check-label" for="perawat_<?= $index ?>"><?= $list['fullname'] ?></label>
                            </div>
                        <?php
                            $index++; // Tambah angka untuk ID berikutnya
                        endforeach;
                        ?>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="table-responsive mb-2">
                    <table class="table mb-0 table-borderless">
                        <tbody>
                            <tr>
                                <td class="align-middle ps-0 pe-1 pt-0 pb-1" style="width: 100%; min-width: 200px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="diagnosa" name="diagnosa" value="" autocomplete="off" dir="auto" placeholder="diagnosa">
                                        <label for="keluhan_utama">Diagnosis</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                                <td class="align-middle ps-1 pe-0 pt-0 pb-1" style="width: 0%; min-width: 100px;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="kode_icd_x" name="kode_icd_x" value="" autocomplete="off" dir="auto" placeholder="kode_icd_x" list="kode_icd_x_list">
                                        <label for="keluhan_utama">ICD 10</label>
                                        <datalist id="kode_icd_x_list">
                                        </datalist>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center radio-group">
                        <label for="lokasi_mata" class="col col-form-label">
                            Lokasi mata
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="lokasi_mata" id="lokasi_mata1" value="OD">
                                    <label class="form-check-label" for="lokasi_mata1">
                                        OD
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="lokasi_mata" id="lokasi_mata2" value="OS">
                                    <label class="form-check-label" for="lokasi_mata2">
                                        OS
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="lokasi_mata" id="lokasi_mata3" value="ODS">
                                    <label class="form-check-label" for="lokasi_mata3">
                                        ODS
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="isi_laporan">Isi Laporan</label>
                    <textarea class="form-control" id="isi_laporan" name="isi_laporan" rows="8" style="resize: none;"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/laporanrajal/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
    async function fetchLaporanRajal() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/laporanrajal/view/') . $laporanrajal['id_lp_tindakan_rajal'] ?>');
            const data = response.data;

            const nama_perawat = data.nama_perawat;
            $('input[name="nama_perawat[]"]').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (nama_perawat.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('checked', true);
                }
            });
            $('#diagnosa').val(data.diagnosa);
            $('#kode_icd_x').val(data.kode_icd_x);
            const lokasi_mata = data.lokasi_mata;
            if (lokasi_mata) {
                $("input[name='lokasi_mata'][value='" + lokasi_mata + "']").prop('checked', true);
            }
            $('#isi_laporan').val(data.isi_laporan);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function loadICDX(query) {
        try {
            const response = await axios.get('<?= base_url('rawatjalan/laporanrajal/icdx') ?>', {
                params: {
                    search: query
                } // Kirim query pencarian
            });
            const icdx = response.data.data;
            const dataList = $('#kode_icd_x_list');
            dataList.empty(); // Kosongkan datalist
            icdx.forEach(item => {
                dataList.append(`<option value="${item.icdKode}">${item.icdNamaIndonesia}</option>`);
            });
        } catch (error) {
            console.error('Gagal memuat ICD 10:', error);
        }
    }

    // Event listener untuk input
    $('#kode_icd_x').on('input', function() {
        const query = $(this).val();
        loadICDX(query);
    });

    $(document).ready(async function() {
        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        $('#laporanRajalForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#laporanRajalForm .is-invalid').removeClass('is-invalid');
            $('#laporanRajalForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#laporanRajalForm input, #laporanRajalForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/laporanrajal/update/' . $laporanrajal['id_lp_tindakan_rajal']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    await fetchLaporanRajal();
                    loadICDX();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#laporanRajalForm .is-invalid').removeClass('is-invalid');
                    $('#laporanRajalForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['lokasi_mata'].includes(field)) {
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
                if (error.response.request.status === 422 || error.response.request.status === 401) {
                    showFailedToast(error.response.data.message);
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                }
            } finally {
                $('#submitBtn').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#laporanRajalForm input, #laporanRajalForm select').prop('disabled', false);
            }
        });
        await fetchLaporanRajal();
        loadICDX();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>