<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($form_persetujuan_tindakan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($form_persetujuan_tindakan['tanggal_registrasi'])));

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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/frmsetujukedokteran'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $form_persetujuan_tindakan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $form_persetujuan_tindakan['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('frmsetujukedokteran/details/' . $previous['id_form_persetujuan_tindakan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('frmsetujukedokteran/details/' . $next['id_form_persetujuan_tindakan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada laporan operasi berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
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
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_form_persetujuan_tindakan']) ? 'active activeLink' : '' ?>" href="<?= base_url('frmsetujukedokteran/details/' . $list['id_form_persetujuan_tindakan']); ?>">
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
            <?= form_open_multipart('frmsetujukedokteran/update/' . $form_persetujuan_tindakan['id_form_persetujuan_tindakan'], 'id="FormulirForm"'); ?>
            <?= csrf_field(); ?>
            <?php if (date('Y-m-d', strtotime($form_persetujuan_tindakan['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($form_persetujuan_tindakan['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="dokter_pelaksana" name="dokter_pelaksana" aria-label="dokter_pelaksana">
                            <option value="" disabled selected>-- Pilih Dokter Pelaksana Tindakan --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="dokter_pelaksana">Dokter Pelaksana Tindakan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="pemberi_informasi" name="pemberi_informasi" aria-label="pemberi_informasi">
                            <option value="" disabled selected>-- Pilih Pemberi Informasi --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="pemberi_informasi">Pemberi Informasi<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Informasi</div>
                <div class="mb-2">
                    <label for="info_diagnosa"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">1</span> Diagnosis (WD dan/atau DD)</label>
                    <input type="text" class="form-control" id="info_diagnosa" name="info_diagnosa" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_dasar_diagnosis"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">2</span> Dasar Diagnosis</label>
                    <input type="text" class="form-control" id="info_dasar_diagnosis" name="info_dasar_diagnosis" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_tindakan"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">3</span> Tindakan Kedokteran</label>
                    <input type="text" class="form-control" id="info_tindakan" name="info_tindakan" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_indikasi"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">4</span> Indikasi Tindakan</label>
                    <input type="text" class="form-control" id="info_indikasi" name="info_indikasi" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_tatacara"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">5</span> Tata Cara</label>
                    <input type="text" class="form-control" id="info_tatacara" name="info_tatacara" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_tujuan"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">6</span> Tujuan</label>
                    <input type="text" class="form-control" id="info_tujuan" name="info_tujuan" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_resiko"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">7</span> Risiko</label>
                    <input type="text" class="form-control" id="info_resiko" name="info_resiko" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_komplikasi"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">8</span> Komplikasi</label>
                    <input type="text" class="form-control" id="info_komplikasi" name="info_komplikasi" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_prognosis"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">9</span> Prognosis</label>
                    <input type="text" class="form-control" id="info_prognosis" name="info_prognosis" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_alternatif"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">10</span> Alternatif dan Risiko</label>
                    <input type="text" class="form-control" id="info_alternatif" name="info_alternatif" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <label for="info_lainnya">Lain-lain</label>
                    <input type="text" class="form-control" id="info_lainnya" name="info_lainnya" value="" autocomplete="off" dir="auto">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Persetujuan Tindakan</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="penerima_informasi" name="penerima_informasi" value="" autocomplete="off" dir="auto" placeholder="penerima_informasi">
                        <label for="penerima_informasi">Nama<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="pererima_tanggal_lahir" name="pererima_tanggal_lahir" value="" autocomplete="off" dir="auto" placeholder="pererima_tanggal_lahir">
                        <label for="pererima_tanggal_lahir">Tanggal Lahir<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="penerima_jenis_kelamin" class="col col-form-label">Jenis Kelamin<span class="text-danger">*</span></label>
                        <div class="col-lg col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penerima_jenis_kelamin" id="penerima_jenis_kelamin1" value="L">
                                    <label class="form-check-label" for="penerima_jenis_kelamin1">
                                        Laki-Laki
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penerima_jenis_kelamin" id="penerima_jenis_kelamin2" value="P">
                                    <label class="form-check-label" for="penerima_jenis_kelamin2">
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="penerima_alamat" name="penerima_alamat" value="" autocomplete="off" dir="auto" placeholder="penerima_alamat">
                        <label for="penerima_alamat">Alamat</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">

                </div>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <select class="form-select" id="penerima_hubungan" name="penerima_hubungan" aria-label="penerima_hubungan">
                                <option value="" disabled selected>-- Pilih Hubungan --</option>
                                <option value="DIRI SENDIRI">Diri Sendiri</option>
                                <option value="SUAMI">Suami</option>
                                <option value="ISTRI">Istri</option>
                                <option value="ANAK">Anak</option>
                                <option value="ORANGTUA">Orang Tua</option>
                                <option value="KELUARGA">Keluarga</option>
                            </select>
                            <label for="penerima_hubungan">Hubungan<span class="text-danger">*</span></label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="keterangan_hubungan" name="keterangan_hubungan" value="" autocomplete="off" dir="auto" placeholder="keterangan_hubungan">
                            <label for="keterangan_hubungan">Keterangan Hubungan (jika Keluarga)</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="mb-2">Menyatakan <strong class="text-success">SETUJU</strong> untuk dilakukannya tindakan:</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="tindakan_kedoteran" name="tindakan_kedoteran" value="" autocomplete="off" dir="auto" placeholder="tindakan_kedoteran">
                        <label for="tindakan_kedoteran">Tindakan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="tanggal_tindakan" name="tanggal_tindakan" value="" autocomplete="off" dir="auto" placeholder="tanggal_tindakan">
                        <label for="tanggal_tindakan">Tanggal Tindakan<span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">Terhadap:</div>
                <div style="font-size: 0.75rem;">
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Nama:</div>
                        <div class="col">
                            <div>
                                <?= $form_persetujuan_tindakan['nama_pasien'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Jenis Kelamin</div>
                        <div class="col">
                            <div class="date">
                                <?php if ($form_persetujuan_tindakan['jenis_kelamin'] == 'L') : ?>
                                    Laki-Laki
                                <?php elseif ($form_persetujuan_tindakan['jenis_kelamin'] == 'P') : ?>
                                    Perempuan
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Tanggal Lahir</div>
                        <div class="col">
                            <div class="date">
                                <?= $form_persetujuan_tindakan['tanggal_lahir'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 row g-1">
                        <div class="col-5 fw-medium text-truncate">Alamat</div>
                        <div class="col">
                            <div>
                                <?= $form_persetujuan_tindakan['alamat'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama_saksi_1" name="nama_saksi_1" value="" autocomplete="off" dir="auto" placeholder="nama_saksi_1">
                            <label for="nama_saksi_1">Saksi I</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama_saksi_2" name="nama_saksi_2" value="" autocomplete="off" dir="auto" placeholder="nama_saksi_2">
                            <label for="nama_saksi_2">Saksi II</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('frmsetujukedokteran/export/' . $form_persetujuan_tindakan['id_form_persetujuan_tindakan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
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
    async function fetchFormulir() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('frmsetujukedokteran/view/') . $form_persetujuan_tindakan['id_form_persetujuan_tindakan'] ?>');
            const data = response.data;

            $('#dokter_pelaksana').val(data.dokter_pelaksana);
            $('#pemberi_informasi').val(data.pemberi_informasi);

            $('#info_diagnosa').val(data.info_diagnosa);
            $('#info_dasar_diagnosis').val(data.info_dasar_diagnosis);
            $('#info_tindakan').val(data.info_tindakan);
            $('#info_indikasi').val(data.info_indikasi);
            $('#info_tatacara').val(data.info_tatacara);
            $('#info_tujuan').val(data.info_tujuan);
            $('#info_resiko').val(data.info_resiko);
            $('#info_komplikasi').val(data.info_komplikasi);
            $('#info_prognosis').val(data.info_prognosis);
            $('#info_alternatif').val(data.info_alternatif);
            $('#info_lainnya').val(data.info_lainnya);

            $('#penerima_informasi').val(data.penerima_informasi);
            $('#pererima_tanggal_lahir').val(data.pererima_tanggal_lahir);
            const penerima_jenis_kelamin = data.penerima_jenis_kelamin;
            if (penerima_jenis_kelamin) {
                $("input[name='penerima_jenis_kelamin'][value='" + penerima_jenis_kelamin + "']").prop('checked', true);
            }
            $('#penerima_alamat').val(data.penerima_alamat);
            $('#penerima_hubungan').val(data.penerima_hubungan);
            $('#keterangan_hubungan').val(data.keterangan_hubungan);

            $('#tindakan_kedoteran').val(data.tindakan_kedoteran);
            $('#tanggal_tindakan').val(data.tanggal_tindakan);
            $('#nama_saksi_1').val(data.nama_saksi_1);
            $('#nama_saksi_2').val(data.nama_saksi_2);
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
        $('#FormulirForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);

            // Clear previous validation states
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#FormulirForm .is-invalid').removeClass('is-invalid');
            $('#FormulirForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <?= $this->include('spinner/spinner'); ?> Simpan
            `);

            // Disable form inputs
            $('#FormulirForm input, #FormulirForm select, #FormulirForm button').prop('disabled', true);
            $('#cancel_changes').hide();

            try {
                const response = await axios.post(`<?= base_url('frmsetujukedokteran/update/' . $form_persetujuan_tindakan['id_form_persetujuan_tindakan']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchFormulir();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#FormulirForm .is-invalid').removeClass('is-invalid');
                    $('#FormulirForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (['penerima_jenis_kelamin'].includes(field)) {
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
                $('#FormulirForm input, #FormulirForm select, #FormulirForm button').prop('disabled', false);
            }
        });
        // $('#loadingSpinner').hide();
        fetchFormulir();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>