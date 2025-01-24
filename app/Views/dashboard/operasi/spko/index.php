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
    <a class="fs-6 mx-2 text-danger" href="#" id="cancelButton" data-bs-placement="bottom" data-bs-title="Batalkan Proses" style="display: none;"><i class="fa-solid fa-xmark"></i></a>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/spko/' . $previous['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_booking']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/spko/' . $next['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_booking']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('operasi/spko/' . $operasi['id_sp_operasi']); ?>">SPKO</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 <?= ($activeSegment === $list['id_sp_operasi']) ? 'active activeLink' : '' ?>" href="<?= base_url('operasi/spko/' . $list['id_sp_operasi']); ?>">
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
            <?= form_open_multipart('/operasi/spko/update/' . $operasi['id_sp_operasi'], 'id="SPKOForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="tanggal_operasi" name="tanggal_operasi" value="" autocomplete="off" dir="auto" placeholder="tanggal_operasi">
                            <label for="tanggal_operasi">Tanggal Operasi</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating">
                            <input type="time" class="form-control" id="jam_operasi" name="jam_operasi" value="" autocomplete="off" dir="auto" placeholder="jam_operasi">
                            <label for="jam_operasi">Jam Operasi</label>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="diagnosa" name="diagnosa" value="" autocomplete="off" dir="auto" placeholder="diagnosa">
                        <label for="diagnosa">Diagnosis</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="jenis_tindakan" class="form-label mb-0">
                        Jenis Tindakan
                    </label>
                    <select class="form-select" id="jenis_tindakan" name="jenis_tindakan[]" multiple>
                        <?php foreach ($master_tindakan_operasi as $list) : ?>
                            <option value="<?= $list['nama_tindakan'] ?>"><?= $list['nama_tindakan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="indikasi_operasi" name="indikasi_operasi" value="" autocomplete="off" dir="auto" placeholder="indikasi_operasi">
                        <label for="indikasi_operasi">Indikasi Operasi</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="jenis_bius" class="col col-form-label">Jenis Anestesi</label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_bius" id="jenis_bius1" value="UMUM">
                                    <label class="form-check-label" for="jenis_bius1">
                                        Umum
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_bius" id="jenis_bius2" value="LOKAL">
                                    <label class="form-check-label" for="jenis_bius2">
                                        Lokal
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_bius" id="jenis_bius3" value="TOTAL">
                                    <label class="form-check-label" for="jenis_bius3">
                                        Total
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="tipe_bayar" name="tipe_bayar" aria-label="tipe_bayar">
                            <option value="" selected>Tidak Ada</option>
                            <option value="PRIBADI">Pribadi</option>
                            <option value="BPJS">BPJS</option>
                            <option value="ASURANSI">Asuransi</option>
                            <option value="JAMINAN PERUSAHAAN">Jaminan Perusahaan</option>
                        </select>
                        <label for="tipe_bayar">Tipe Bayar</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="row gx-1 radio-group">
                        <label for="rajal_ranap" class="col col-form-label">Jenis Rawat</label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rajal_ranap" id="rajal_ranap1" value="RAJAL">
                                    <label class="form-check-label" for="rajal_ranap1">
                                        Rawat Jalan
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rajal_ranap" id="rajal_ranap2" value="RANAP">
                                    <label class="form-check-label" for="rajal_ranap2">
                                        Rawat Inap
                                    </label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="ruang_operasi" name="ruang_operasi" aria-label="ruang_operasi">
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            <option value="OK1">OK1</option>
                        </select>
                        <label for="ruang_operasi">Ruangan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="dokter_operator" name="dokter_operator" aria-label="dokter_operator">
                            <option value="" disabled selected>-- Pilih Dokter Operator --</option>
                            <?php foreach ($dokter as $list) : ?>
                                <option value="<?= $list['fullname'] ?>"><?= $list['fullname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="dokter_operator">Dokter Operator</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom"><em>Site Marking</em></div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="diagnosa_site_marking" name="diagnosa_site_marking" value="" autocomplete="off" dir="auto" placeholder="diagnosa_site_marking">
                        <label for="diagnosa_site_marking">Diagnosis</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="tindakan_site_marking" name="tindakan_site_marking" value="" autocomplete="off" dir="auto" placeholder="tindakan_site_marking">
                        <label for="tindakan_site_marking">Tindakan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="site_marking" class="form-label mb-0">Unggah <em>Site Marking</em> (maks 8 MB)</label>
                    <div class="d-grid mb-2">
                        <button class="btn btn-body bg-gradient btn-sm" type="button" id="download-template"><i class="fa-solid fa-download"></i> Unduh Templat <em>Site Marking</em></button>
                    </div>
                    <input class="form-control" type="file" id="site_marking" name="site_marking" accept="image/*">
                    <div class="invalid-feedback"></div>
                </div>
                <div id="site_marking_preview_div" style="display: none;" class="mb-2">
                    <div class="d-flex justify-content-center">
                        <img id="site_marking_preview" src="#" alt="Gambar" class="img-thumbnail" style="width: 100%; max-width: 256px;">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pasien dan Keluarga</div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nama_pasien_keluarga" name="nama_pasien_keluarga" value="" autocomplete="off" dir="auto" placeholder="nama_pasien_keluarga">
                        <label for="nama_pasien_keluarga">Nama Pasien dan Keluarga</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div>
                <hr>
                <!-- Progress bar -->
                <div class="mb-2 mt-1 w-100" id="uploadProgressDiv">
                    <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient" role="progressbar" style="width: 0%; transition: none;" id="uploadProgressBar"></div>
                    </div>
                </div>
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
            const response = await axios.get('<?= base_url('operasi/spko/view/') . $operasi['id_sp_operasi'] ?>');
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
                $('#site_marking_preview').attr('src', `<?= base_url('uploads/site_marking') ?>/` + data.site_marking);
                $('#site_marking_preview_div').show();
            } else {
                $('#site_marking_preview_div').hide();
            }

            // Pasien dan Keluarga
            $('#nama_pasien_keluarga').val(data.nama_pasien_keluarga);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        $('#jenis_tindakan').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: "Pilih jenis tindakan"
        });
        // Cari semua elemen dengan kelas 'activeLink' di kedua navigasi
        $(".nav .activeLink").each(function() {
            // Scroll ke elemen yang aktif
            this.scrollIntoView({
                block: "nearest", // Fokus pada elemen aktif
                inline: "center" // Elemen di-scroll ke tengah horizontal
            });
        });

        $("#download-template").on("click", async function() {
            $('#loadingSpinner').show();
            try {
                // URL file gambar
                const fileUrl = "<?= base_url('/assets/images/site_marking.jpg') ?>";

                // Permintaan Axios untuk mendapatkan file gambar
                const response = await axios.get(fileUrl, {
                    responseType: "blob", // Mengatur respons sebagai blob
                });

                // Membuat URL blob
                const blob = response.data;
                const downloadUrl = URL.createObjectURL(blob);

                // Membuat elemen <a> untuk mengunduh file
                const link = document.createElement("a");
                link.href = downloadUrl;
                link.download = "site_marking.jpg"; // Nama file yang akan diunduh
                link.style.display = "none";

                // Menambahkan elemen <a> ke dokumen, lalu klik secara otomatis
                document.body.appendChild(link);
                link.click();

                // Membersihkan elemen dan URL blob
                document.body.removeChild(link);
                URL.revokeObjectURL(downloadUrl);

                console.log("File berhasil diunduh!");
            } catch (error) {
                showFailedToast("Gagal mengunduh file<br>" + error);
                console.error("Gagal mengunduh file:", error);
            } finally {
                $('#loadingSpinner').hide();
            }
        });

        $('#site_marking').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#site_marking_preview').attr('src', e.target.result);
                $('#site_marking_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $('#SPKOForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Clear previous validation states
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#SPKOForm .is-invalid').removeClass('is-invalid');
            $('#SPKOForm .invalid-feedback').text('').hide();
            $('#cancelButton').show();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

            // Disable form inputs
            $('#SPKOForm input, #SPKOForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/operasi/spko/update/' . $operasi['id_sp_operasi']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        if (progressEvent.lengthComputable) {
                            var percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                            $('#uploadProgressBar').css('width', percent + '%');
                            $('#uploadPercentage').html(percent + '%');
                        }
                    },
                    cancelToken: source.token // Attach the token here
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    $('#uploadProgressBar').css('width', '0%');
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
                    $('#uploadProgressBar').addClass('bg-danger');
                }
            } catch (error) {
                if (axios.isCancel(error)) {
                    showFailedToast(error.message);
                    $('#uploadProgressBar').css('width', '0%');
                } else {
                    showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
                    $('#uploadProgressBar').addClass('bg-danger');
                }
            } finally {
                $('#uploadPercentage').html('0%');
                $('#cancelButton').hide();
                $('#submitBtn').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#SPKOForm input, #SPKOForm select').prop('disabled', false);
            }
            // Attach the cancel functionality to the close button
            $('#cancelButton').on('click', function(ə) {
                ə.preventDefault();
                source.cancel('Perubahan pada SPKO ini telah dibatalkan.');
            });
        });
        // $('#loadingSpinner').hide();
        fetchSPKO();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>