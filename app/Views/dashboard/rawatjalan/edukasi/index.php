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
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/edukasi/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/edukasi/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                        <a class="nav-link text-nowrap" href="<?= base_url('rawatjalan/skrining/' . $rawatjalan['id_rawat_jalan']); ?>">Skrining</a>
                        <a class="nav-link text-nowrap active" href="<?= base_url('rawatjalan/edukasi/' . $rawatjalan['id_rawat_jalan']); ?>">Edukasi</a>
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
                    <a class="nav-link text-nowrap <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active' : '' ?>" href="<?= base_url('rawatjalan/edukasi/' . $list['id_rawat_jalan']); ?>"><?= $list['nomor_registrasi']; ?></a>
                <?php endforeach; ?>
            </nav>
            <?= form_open_multipart('/rawatjalan/edukasi/update/' . $edukasi['id_edukasi'], 'id="edukasiForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pengkajian Kebutuhan Informasi dan Edukasi</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="bahasa" name="bahasa" aria-label="bahasa">
                            <option value="" disabled selected>-- Pilih Bahasa --</option>
                            <option value="INDONESIA">INDONESIA</option>
                            <option value="INGGRIS">INGGRIS</option>
                            <option value="DAERAH">DAERAH</option>
                            <option value="LAINNYA">LAINNYA</option>
                        </select>
                        <label for="bahasa">Bahasa</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="bahasa_lainnya" name="bahasa_lainnya" value="" autocomplete="off" dir="auto" placeholder="bahasa_lainnya">
                        <label for="bahasa_lainnya">Bahasa Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="penterjemah" class="col col-form-label">
                            Kebutuhan Penerjemah
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penterjemah" id="penterjemah1" value="YA">
                                    <label class="form-check-label" for="penterjemah1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="penterjemah" id="penterjemah2" value="TIDAK">
                                    <label class="form-check-label" for="penterjemah2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="pendidikan" name="pendidikan" aria-label="pendidikan">
                            <option value="" disabled selected>-- Pilih Pendidikan --</option>
                            <?php foreach ($pendidikan as $list) : ?>
                                <option value="<?= $list['pendidikan'] ?>"><?= $list['keterangan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="pendidikan">Pendidikan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="baca_tulis" class="col col-form-label">
                            Baca dan Tulis
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="baca_tulis" id="baca_tulis1" value="BAIK">
                                    <label class="form-check-label" for="baca_tulis1">
                                        Baik
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="baca_tulis" id="baca_tulis2" value="KURANG">
                                    <label class="form-check-label" for="baca_tulis2">
                                        Kurang
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="cara_belajar" class="col col-form-label">
                            Pilihan Cara Belajar
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cara_belajar" id="cara_belajar1" value="VERBAL">
                                    <label class="form-check-label" for="cara_belajar1">
                                        Verbal
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cara_belajar" id="cara_belajar2" value="TULISAN">
                                    <label class="form-check-label" for="cara_belajar2">
                                        Tulisan
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="budaya" name="budaya" value="" autocomplete="off" dir="auto" placeholder="budaya">
                        <label for="budaya">Budaya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="hambatan" class="form-label">
                        Hambatan<br><small class="text-muted">Tekan dan tahan <kbd>Ctrl</kbd> atau <kbd>⌘ Command</kbd> jika tidak bisa memilih lebih dari satu</small>
                    </label>
                    <select class="form-select" id="hambatan" name="hambatan[]" aria-label="hambatan" size="9" multiple>
                        <option value="TIDAK ADA">TIDAK ADA</option>
                        <option value="BAHASA">BAHASA</option>
                        <option value="EMOSIONAL">EMOSIONAL</option>
                        <option value="FISIK LEMAH">FISIK LEMAH</option>
                        <option value="GANGGUAN BICARA">GANGGUAN BICARA</option>
                        <option value="KOGNITIF TERBATAS">KOGNITIF TERBATAS</option>
                        <option value="MOTIVASI KURANG">MOTIVASI KURANG</option>
                        <option value="PENDENGARAN TERGANGGU">PENDENGARAN TERGANGGU</option>
                        <option value="PENGLIHATAN TERGANGGU">PENGLIHATAN TERGANGGU</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="keyakinan" name="keyakinan" aria-label="keyakinan">
                            <option value="" disabled selected>-- Pilih Keyakinan --</option>
                            <option value="SPIRITUAL">SPIRITUAL</option>
                            <option value="ISLAM">ISLAM</option>
                            <option value="KRISTEN">KRISTEN</option>
                            <option value="HINDU">HINDU</option>
                            <option value="BUDDHA">BUDDHA</option>
                            <option value="KONGHUCU">KONGHUCU</option>
                            <option value="KHUSUS">KHUSUS</option>
                        </select>
                        <label for="status_fungsional">Keyakinan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="keyakinan_khusus" name="keyakinan_khusus" value="" autocomplete="off" dir="auto" placeholder="keyakinan_khusus">
                        <label for="keyakinan_khusus">Keyakinan Khusus</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="topik_pembelajaran" name="topik_pembelajaran" aria-label="topik_pembelajaran">
                            <option value="" disabled selected>-- Pilih Keyakinan --</option>
                            <option value="Proses penyakit">Proses penyakit</option>
                            <option value="Rencana tindakan/terapi">Rencana tindakan/terapi</option>
                            <option value="Pengobatan dan prosedur yang diberikan atau diperlukan">Pengobatan dan prosedur yang diberikan atau diperlukan</option>
                            <option value="Hasil pelayanan termasuk terjadinya kejadian yang diharapkan dan tidak diharapkan">Hasil pelayanan termasuk terjadinya kejadian yang diharapkan dan tidak diharapkan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <label for="status_fungsional">Topik Pembelajaran</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="topik_lainnya" name="topik_lainnya" value="" autocomplete="off" dir="auto" placeholder="topik_lainnya">
                        <label for="topik_lainnya">Topik Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row g-1 align-items-center">
                        <label for="kesediaan_pasien" class="col col-form-label">
                            Kesediaan pasien dan keluarga untuk menerima informasi dan edukasi
                        </label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kesediaan_pasien" id="kesediaan_pasien1" value="YA">
                                    <label class="form-check-label" for="kesediaan_pasien1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kesediaan_pasien" id="kesediaan_pasien2" value="TIDAK">
                                    <label class="form-check-label" for="kesediaan_pasien2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/edukasi/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Evaluasi Edukasi</div>
            </div>
            <?= form_close(); ?>
        </div>

</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    async function fetchEdukasi() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/edukasi/view/') . $edukasi['id_edukasi'] ?>');
            const data = response.data;

            $('#bahasa').val(data.bahasa);
            $('#bahasa_lainnya').val(data.bahasa_lainnya);
            const penterjemah = data.penterjemah;
            if (penterjemah) {
                $("input[name='penterjemah'][value='" + penterjemah + "']").prop('checked', true);
            }
            $('#pendidikan').val(data.pendidikan);
            const baca_tulis = data.baca_tulis;
            if (baca_tulis) {
                $("input[name='baca_tulis'][value='" + baca_tulis + "']").prop('checked', true);
            }
            const cara_belajar = data.cara_belajar;
            if (cara_belajar) {
                $("input[name='cara_belajar'][value='" + cara_belajar + "']").prop('checked', true);
            }
            $('#budaya').val(data.budaya);
            const hambatan = data.hambatan;
            $('#hambatan option').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (hambatan.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('selected', true);
                }
            });
            $('#keyakinan').val(data.keyakinan);
            $('#keyakinan_khusus').val(data.keyakinan_khusus);
            $('#topik_pembelajaran').val(data.topik_pembelajaran);
            $('#topik_lainnya').val(data.topik_lainnya);
            const kesediaan_pasien = data.kesediaan_pasien;
            if (kesediaan_pasien) {
                $("input[name='kesediaan_pasien'][value='" + kesediaan_pasien + "']").prop('checked', true);
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        $('#edukasiForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#edukasiForm .is-invalid').removeClass('is-invalid');
            $('#edukasiForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#edukasiForm input, #edukasiForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/edukasi/update/' . $edukasi['id_edukasi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchEdukasi();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#edukasiForm .is-invalid').removeClass('is-invalid');
                    $('#edukasiForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'penterjemah' || field === 'baca_tulis' || field === 'cara_belajar' || field === 'kesediaan_pasien') {
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
                $('#edukasiForm input, #edukasiForm select').prop('disabled', false);
            }
        });
        fetchEdukasi();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>