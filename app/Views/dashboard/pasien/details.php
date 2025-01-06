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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/pasien'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $pasien['no_rm'] ?> • <span id="nama_pasien_header"><?= $pasien['nama_pasien']; ?></span></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $previous['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['no_rm'] ?> • <?= $previous['nama_pasien'] == NULL; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('pasien/detailpasien/' . $next['id_pasien']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['no_rm'] ?> • <?= $next['nama_pasien'] == NULL; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien berikutnya"><i class="fa-solid fa-circle-arrow-right"></i></span>
    <?php endif; ?>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <?= form_open_multipart('/pasien/update/' . $pasien['id_pasien'], 'id="pasienForm"'); ?>
        <?= csrf_field(); ?>
        <div class="mb-3">
            <div class="fw-bold mb-2 border-bottom">Identitas Pasien</div>
            <div class="mb-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="" autocomplete="off" dir="auto" placeholder="nama_pasien">
                    <label for="nama_pasien">Nama</label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nik" name="nik" value="" autocomplete="off" dir="auto" placeholder="nik">
                        <label for="nik">Nomor Induk Kependudukan (NIK)</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="no_bpjs" name="no_bpjs" value="" autocomplete="off" dir="auto" placeholder="no_bpjs">
                        <label for="no_bpjs">Nomor BPJS</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="" autocomplete="off" dir="auto" placeholder="tempat_lahir">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="" autocomplete="off" dir="auto" placeholder="tanggal_lahir">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="mb-2 row row-cols-2 g-2 radio-btn">
                <div class="col d-grid">
                    <input type="radio" class="btn-check" name="jenis_kelamin" id="jenis_kelamin1" autocomplete="off" value="L">
                    <label class="btn btn-body text-primary" for="jenis_kelamin1"><i class="fa-solid fa-mars"></i> Laki-Laki</label>
                </div>
                <div class="col d-grid">
                    <input type="radio" class="btn-check" name="jenis_kelamin" id="jenis_kelamin2" autocomplete="off" value="P">
                    <label class="btn btn-body text-danger" for="jenis_kelamin2"><i class="fa-solid fa-venus"></i> Perempuan</label>
                </div>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-2">
                <div class="form-floating">
                    <input type="text" class="form-control" id="alamat" name="alamat" value="" autocomplete="off" dir="auto" placeholder="alamat">
                    <label for="alamat">Alamat</label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                <div class="col">
                    <div class="form-floating">
                        <select class="form-select" id="provinsi" name="provinsi" aria-label="provinsi">
                            <option value="" disabled selected>-- Pilih Provinsi --</option>
                        </select>
                        <label for="provinsi">Provinsi</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <select class="form-select" id="kabupaten" name="kabupaten" aria-label="kabupaten">
                            <option value="" disabled selected>-- Pilih Kabupaten/Kota --</option>
                        </select>
                        <label for="kabupaten">Kabupaten/Kota</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <select class="form-select" id="kecamatan" name="kecamatan" aria-label="kecamatan">
                            <option value="" disabled selected>-- Pilih Kecamatan --</option>
                        </select>
                        <label for="kecamatan">Kecamatan</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating">
                        <select class="form-select" id="kelurahan" name="kelurahan" aria-label="kelurahan">
                            <option value="" disabled selected>-- Pilih Desa/Kelurahan --</option>
                        </select>
                        <label for="kelurahan">Desa/Kelurahan</label>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <hr>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                <button class="btn btn-body  bg-gradient" type="button" id="printBtn1" onclick="window.open(`<?= base_url('/pasien/etiket/' . $pasien['id_pasien']) ?>`)"><i class="fa-solid fa-print"></i> Cetak E-Tiket</button>
                <button class="btn btn-primary  bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>

    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body p-4 text-center">
                    <h5 class="mb-0" id="deleteMessage"></h5>
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
<?= $this->section('javascript'); ?>
<script>
    async function fetchPasien() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('pasien/pasien/') . $pasien['id_pasien'] ?>');

            const data = response.data;

            $('title').text(`Detail Pasien ${data.nama_pasien} (${data.no_rm}) - <?= $systemname ?>`);
            $('#nama_pasien_header').text(data.nama_pasien);
            $('#nama_pasien').val(data.nama_pasien);
            $('#nik').val(data.nik);
            $('#no_bpjs').val(data.no_bpjs);
            $('#tempat_lahir').val(data.tempat_lahir);
            $('#tanggal_lahir').val(data.tanggal_lahir);
            const selectedGender = response.data.jenis_kelamin;
            if (selectedGender) {
                $("input[name='jenis_kelamin'][value='" + selectedGender + "']").prop('checked', true);
            }
            $('#alamat').val(data.alamat);
            // Atur lokasi secara berurutan
            if (data.provinsi) {
                await loadProvinsi(data.provinsi); // Muat dan pilih provinsi
                if (data.kabupaten) {
                    await loadKabupaten(data.provinsi, data.kabupaten); // Muat dan pilih kabupaten
                    if (data.kecamatan) {
                        await loadKecamatan(data.kabupaten, data.kecamatan); // Muat dan pilih kecamatan
                        if (data.kelurahan) {
                            await loadKelurahan(data.kecamatan, data.kelurahan); // Muat dan pilih kelurahan
                        }
                    }
                }
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    // Fungsi untuk memuat data provinsi
    async function loadProvinsi(selectedProvinsi = null) {
        try {
            const response = await axios.get('<?= base_url('pasien/provinsi') ?>'); // Ganti dengan URL API yang sesuai
            const provinsi = response.data.data; // Sesuaikan dengan struktur data API
            $('#provinsi').append(
                provinsi.map(p => `<option value="${p.id}">${p.name}</option>`)
            );
            if (selectedProvinsi) {
                $('#provinsi').val(selectedProvinsi).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data provinsi:', error);
        }
    }

    // Fungsi untuk memuat data kabupaten berdasarkan provinsi
    async function loadKabupaten(provinsiId, selectedKabupaten = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kabupaten') ?>/${provinsiId}`);
            const kabupaten = response.data.data;
            $('#kabupaten')
                .empty()
                .append('<option value="">-- Pilih Kabupaten/Kota --</option>')
                .append(
                    kabupaten.map(k => `<option value="${k.id}">${k.name}</option>`)
                )
                .prop('disabled', false);
            if (selectedKabupaten) {
                $('#kabupaten').val(selectedKabupaten).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data kabupaten:', error);
        }
    }

    // Fungsi untuk memuat data kecamatan berdasarkan kabupaten
    async function loadKecamatan(kabupatenId, selectedKecamatan = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kecamatan') ?>/${kabupatenId}`);
            const kecamatan = response.data.data;
            $('#kecamatan')
                .empty()
                .append('<option value="">-- Pilih Kecamatan --</option>')
                .append(
                    kecamatan.map(k => `<option value="${k.id}">${k.name}</option>`)
                );
            if (selectedKecamatan) {
                $('#kecamatan').val(selectedKecamatan).trigger('change');
            }
        } catch (error) {
            console.error('Gagal memuat data kecamatan:', error);
        }
    }

    // Fungsi untuk memuat data kelurahan berdasarkan kecamatan
    async function loadKelurahan(kecamatanId, selectedKelurahan = null) {
        try {
            const response = await axios.get(`<?= base_url('pasien/kelurahan') ?>/${kecamatanId}`);
            const kelurahan = response.data.data;
            $('#kelurahan')
                .empty()
                .append('<option value="">-- Pilih Desa/Kelurahan --</option>')
                .append(
                    kelurahan.map(k => `<option value="${k.id}">${k.name}</option>`)
                );
            if (selectedKelurahan) {
                $('#kelurahan').val(selectedKelurahan);
            }
        } catch (error) {
            console.error('Gagal memuat data kelurahan:', error);
        }
    }

    // Event handler saat provinsi dipilih
    $('#provinsi').on('change', function() {
        const provinsiId = $(this).val();
        if (provinsiId) {
            loadKabupaten(provinsiId);
            $('#kecamatan').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        } else {
            $('#kabupaten, #kecamatan, #kelurahan')
                .empty()
                .append('<option value="">-- Pilih --</option>');
        }
    });

    // Event handler saat kabupaten dipilih
    $('#kabupaten').on('change', function() {
        const kabupatenId = $(this).val();
        if (kabupatenId) {
            loadKecamatan(kabupatenId);
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        } else {
            $('#kecamatan, #kelurahan')
                .empty()
                .append('<option value="">-- Pilih --</option>');
        }
    });

    // Event handler saat kecamatan dipilih
    $('#kecamatan').on('change', function() {
        const kecamatanId = $(this).val();
        if (kecamatanId) {
            loadKelurahan(kecamatanId);
        } else {
            $('#kelurahan').empty().append('<option value="">-- Pilih Desa/Kelurahan --</option>');
        }
    });

    $(document).ready(async function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_obat').select2({
            dropdownParent: $(document.body),
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        var detailResepId;
        var detailResepName;

        $('#pasienForm').submit(async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#pasienForm .is-invalid').removeClass('is-invalid');
            $('#pasienForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Tambah
            `);

            // Disable form inputs
            $('#pasienForm input, #pasienForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/pasien/update/' . $pasien['id_pasien']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchPasien();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#pasienForm .is-invalid').removeClass('is-invalid');
                    $('#pasienForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);

                            // Handle radio button group separately
                            if (field === 'jenis_kelamin') {
                                const radioGroup = $("input[name='jenis_kelamin']");
                                const feedbackElement = radioGroup.closest('.radio-btn').find('.invalid-feedback');

                                if (radioGroup.length > 0 && feedbackElement.length > 0) {
                                    radioGroup.addClass('is-invalid');
                                    feedbackElement.text(response.data.errors[field]).show();

                                    // Remove error message when the user selects any radio button in the group
                                    radioGroup.on('change', function() {
                                        $("input[name='jenis_kelamin']").removeClass('is-invalid');
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
                $('#pasienForm input, #pasienForm select').prop('disabled', false);
            }
        });
        await fetchPasien();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>