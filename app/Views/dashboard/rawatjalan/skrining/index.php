<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(3); // Get the first segment
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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nomor_registrasi']; ?> • <?= $rawatjalan['no_rm'] ?> • <?= $rawatjalan['nama_pasien']; ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/skrining/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/skrining/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                <nav class="nav nav-underline nav-justified flex-nowrap overflow-auto">
                    <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/asesmen/' . $rawatjalan['id_rawat_jalan']); ?>">Asesmen</a>
                    <?php if (session()->get('role') != 'Dokter') : ?>
                        <a class="nav-link text-nowrap active" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                        <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
                    <?php endif; ?>
                    <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                    <?php if (session()->get('role') != 'Perawat') : ?>
                        <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                        <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/resepkacamata/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                    <?php endif; ?>
                    <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/lptindakan/' . $rawatjalan['id_rawat_jalan']); ?>">Laporan Tindakan</a>
                </nav>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <nav class="nav nav-underline flex-nowrap overflow-auto mb-3">
                <?php foreach ($listRawatJalan as $list) : ?>
                    <a class="nav-link text-nowrap <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active' : '' ?>" href="<?= base_url('rawatjalan/skrining/' . $list['id_rawat_jalan']); ?>"><?= $list['nomor_registrasi']; ?></a>
                <?php endforeach; ?>
            </nav>
            <?= form_open_multipart('/rawatjalan/skrining/update/' . $skrining['id_skrining'], 'id="skriningForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">I</span> Skrining Risiko Cedera/Jatuh (<em>Get Up and Go Score</em>)</div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="jatuh_sempoyongan" class="col col-form-label">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">a</span> Perhatikan cara berjalan pasien saat duduk di kursi. Apakah pasien tampak tidak seimbang (sempoyongan/limbung)?
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_sempoyongan" id="jatuh_sempoyongan1" value="YA">
                                    <label class="form-check-label" for="jatuh_sempoyongan1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_sempoyongan" id="jatuh_sempoyongan2" value="TIDAK">
                                    <label class="form-check-label" for="jatuh_sempoyongan2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="jatuh_penopang" class="col col-form-label">
                            <span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">b</span> Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk?
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_penopang" id="jatuh_penopang1" value="YA">
                                    <label class="form-check-label" for="jatuh_penopang1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_penopang" id="jatuh_penopang2" value="TIDAK">
                                    <label class="form-check-label" for="jatuh_penopang2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="jatuh_info_dokter" class="col col-form-label">
                            Diberitahukan ke dokter?
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_info_dokter" id="jatuh_info_dokter1" value="YA">
                                    <label class="form-check-label" for="jatuh_info_dokter1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jatuh_info_dokter" id="jatuh_info_dokter2" value="TIDAK">
                                    <label class="form-check-label" for="jatuh_info_dokter2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="jatuh_info_pukul" class="col col-form-label">
                            Pukul diberitahukan
                        </label>
                        <div class="col col-form-label">
                            <input type="time" class="form-control" id="jatuh_info_pukul" name="jatuh_info_pukul" value="" autocomplete="off" dir="auto">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">II</span> Status Fungsional</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="status_fungsional" name="status_fungsional" aria-label="status_fungsional">
                            <option value="" disabled selected>-- Pilih Status Fungsional --</option>
                            <option value="MANDIRI">MANDIRI</option>
                            <option value="PERLU BANTUAN">PERLU BANTUAN</option>
                            <option value="KETERGANTUNGAN TOTAL">KETERGANTUNGAN TOTAL</option>
                        </select>
                        <label for="status_fungsional">Status Fungsional</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="time" class="form-control" id="status_info_pukul" name="status_info_pukul" value="" autocomplete="off" dir="auto" placeholder="status_info_pukul">
                        <label for="status_info_pukul">Diberitahukan ke dokter pukul</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><span class="badge bg-body text-body border px-2 align-self-start date" style="font-weight: 900; font-size: 1em; padding-top: .1rem !important; padding-bottom: .1rem !important;">III</span> Skrining Nyeri</div>
                <div class="mb-2">
                    <center>
                        <figure class="figure mb-0">
                            <img src="<?= base_url('assets/images/skala_nyeri.png') ?>" class="figure-img img-fluid rounded border shadow-sm mb-0 pb-0" alt="Skala Nyeri">
                        </figure>
                    </center>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="nyeri_kategori" name="nyeri_kategori" aria-label="nyeri_kategori">
                            <option value="" disabled selected>-- Pilih Kategori Nyeri --</option>
                            <option value="TIDAK ADA">TIDAK ADA</option>
                            <option value="KRONIS">KRONIS</option>
                            <option value="AKUT">AKUT</option>
                        </select>
                        <label for="nyeri_kategori">Kategori Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="mb-0 row g-1 align-items-end">
                        <label for="nyeri_skala" class="col col-form-label">Skala Nyeri</label>
                        <div class="col col-form-label text-end">
                            <div class="date text-nowrap fw-bold" id="nyeri_skala_value">
                                0
                            </div>
                        </div>
                    </div>
                    <input type="range" class="form-range" min="0" max="10" id="nyeri_skala" name="nyeri_skala" value="0">
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nyeri_lokasi" name="nyeri_lokasi" value="" autocomplete="off" dir="auto" placeholder="nyeri_lokasi">
                        <label for="nyeri_lokasi">Lokasi Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nyeri_karakteristik" name="nyeri_karakteristik" value="" autocomplete="off" dir="auto" placeholder="nyeri_karakteristik">
                        <label for="nyeri_karakteristik">Karakteristik Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nyeri_durasi" name="nyeri_durasi" value="" autocomplete="off" dir="auto" placeholder="nyeri_durasi">
                        <label for="nyeri_durasi">Durasi Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nyeri_frekuensi" name="nyeri_frekuensi" value="" autocomplete="off" dir="auto" placeholder="nyeri_frekuensi">
                        <label for="nyeri_frekuensi">Frekuensi Nyeri</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="nyeri_hilang_bila" name="nyeri_hilang_bila" aria-label="nyeri_hilang_bila">
                            <option value="">TIDAK ADA</option>
                            <option value="MINUM OBAT">MINUM OBAT</option>
                            <option value="ISTIRAHAT">ISTIRAHAT</option>
                            <option value="MENDENGAR MUSIK">MENDENGAR MUSIK</option>
                            <option value="BERUBAH POSISI / TIDUR">BERUBAH POSISI / TIDUR</option>
                            <option value="LAIN-LAIN">LAIN-LAIN</option>
                        </select>
                        <label for="nyeri_hilang_bila">Nyeri Hilang Bila</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nyeri_hilang_lainnya" name="nyeri_hilang_lainnya" value="" autocomplete="off" dir="auto" placeholder="nyeri_hilang_lainnya">
                        <label for="nyeri_hilang_lainnya">Jika Lain-lain, Sebutkan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="nyeri_info_dokter" class="col col-form-label">
                            Diberitahukan ke dokter?
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="nyeri_info_dokter" id="nyeri_info_dokter1" value="YA">
                                    <label class="form-check-label" for="nyeri_info_dokter1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="nyeri_info_dokter" id="nyeri_info_dokter2" value="TIDAK">
                                    <label class="form-check-label" for="nyeri_info_dokter2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="nyeri_info_pukul" class="col col-form-label">
                            Pukul diberitahukan
                        </label>
                        <div class="col col-form-label">
                            <input type="time" class="form-control" id="nyeri_info_pukul" name="nyeri_info_pukul" value="" autocomplete="off" dir="auto">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                    <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/skrining/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                    <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>

</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchSkrining() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/skrining/view/') . $skrining['id_skrining'] ?>');
            const data = response.data;

            // Skrining Risiko Cedera/Jatuh (Get Up and Go Score)
            const jatuh_sempoyongan = data.jatuh_sempoyongan;
            if (jatuh_sempoyongan) {
                $("input[name='jatuh_sempoyongan'][value='" + jatuh_sempoyongan + "']").prop('checked', true);
            }
            const jatuh_penopang = data.jatuh_penopang;
            if (jatuh_penopang) {
                $("input[name='jatuh_penopang'][value='" + jatuh_penopang + "']").prop('checked', true);
            }
            const jatuh_info_dokter = data.jatuh_info_dokter;
            if (jatuh_info_dokter) {
                $("input[name='jatuh_info_dokter'][value='" + jatuh_info_dokter + "']").prop('checked', true);
            }
            $('#jatuh_info_pukul').val(data.jatuh_info_pukul);

            // Status Fungsional
            $('#status_fungsional').val(data.status_fungsional);
            $('#status_info_pukul').val(data.status_info_pukul);

            // Skrining Nyeri
            $('#nyeri_kategori').val(data.nyeri_kategori);
            const nyeri_skala = data.nyeri_skala === null ? 0 : data.nyeri_skala;
            $('#nyeri_skala_value').text(nyeri_skala);
            $('#nyeri_skala').val(nyeri_skala);
            $('#nyeri_lokasi').val(data.nyeri_lokasi);
            $('#nyeri_karakteristik').val(data.nyeri_karakteristik);
            $('#nyeri_durasi').val(data.nyeri_durasi);
            $('#nyeri_frekuensi').val(data.nyeri_frekuensi);
            $('#nyeri_hilang_bila').val(data.nyeri_hilang_bila);
            $('#nyeri_hilang_lainnya').val(data.nyeri_hilang_lainnya);
            const nyeri_info_dokter = data.nyeri_info_dokter;
            if (nyeri_info_dokter) {
                $("input[name='nyeri_info_dokter'][value='" + nyeri_info_dokter + "']").prop('checked', true);
            }
            $('#nyeri_info_pukul').val(data.nyeri_info_pukul);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        $('#nyeri_skala').on('input', function() {
            $('#nyeri_skala_value').text($(this).val());
        });

        $('#skriningForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#skriningForm .is-invalid').removeClass('is-invalid');
            $('#skriningForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#skriningForm input, #skriningForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/skrining/update/' . $skrining['id_skrining']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchSkrining();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#skriningForm .is-invalid').removeClass('is-invalid');
                    $('#skriningForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'jatuh_sempoyongan' || field === 'jatuh_penopang' || field === 'jatuh_info_dokter' || field === 'nyeri_info_dokter') {
                                const radioGroup = $("input[type='radio']");
                                const feedbackElement = radioGroup.closest('.col-form-label').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[type='radio']").removeClass('is-invalid');
                                        feedbackElement.removeAttr('style').hide();
                                    });
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
                $('#skriningForm input, #skriningForm select').prop('disabled', false);
            }
        });
        fetchSkrining();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>