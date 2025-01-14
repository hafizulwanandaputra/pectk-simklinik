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
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $rawatjalan['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $rawatjalan['no_rm'] ?> • <?= $rawatjalan['nomor_registrasi']; ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/penunjang/' . $previous['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_registrasi']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada rawat jalan sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('rawatjalan/penunjang/' . $next['id_rawat_jalan']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_registrasi']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap active" href="<?= base_url('rawatjalan/penunjang/' . $rawatjalan['id_rawat_jalan']); ?>">Penunjang</a>
                        <!-- <?php if (session()->get('role') != 'Perawat') : ?>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepobat/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Obat</a>
                            <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/resepkacamata/' . $rawatjalan['id_rawat_jalan']); ?>">Resep Kacamata</a>
                        <?php endif; ?>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('rawatjalan/lptindakan/' . $rawatjalan['id_rawat_jalan']); ?>">Laporan Tindakan</a> -->
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="nav-link py-1 text-nowrap <?= ($activeSegment === $list['id_rawat_jalan']) ? 'active' : '' ?>" href="<?= base_url('rawatjalan/edukasi/' . $list['id_rawat_jalan']); ?>"><?= $list['nomor_registrasi']; ?></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </li>
        </ul>
    </div>
    <div class="px-3 mt-3">
        <div class="no-fluid-content">
            <?= form_open_multipart('/rawatjalan/penunjang/update/' . $penunjang['id_penunjang'], 'id="penunjangForm"'); ?>
            <?= csrf_field(); ?>
            <div class="mb-3">
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="diagnosa" name="diagnosa" value="" autocomplete="off" dir="auto" placeholder="diagnosa">
                        <label for="diagnosa">Diagnosis</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="dokter_pengirim" name="dokter_pengirim" aria-label="dokter_pengirim">
                            <option value="" disabled selected>-- Pilih Dokter --</option>
                        </select>
                        <label for="dokter_pengirim">Bahasa</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="rujukan_dari" name="rujukan_dari" aria-label="rujukan_dari">
                            <option value="" disabled selected>-- Pilih Asal Rujukan --</option>
                        </select>
                        <label for="rujukan_dari">Rujukan dari</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="pemeriksaan" class="form-label">
                        Pemeriksaan<br><small class="text-muted">Tekan dan tahan <kbd>Ctrl</kbd> atau <kbd>⌘ Command</kbd> jika tidak bisa memilih lebih dari satu</small>
                    </label>
                    <select class="form-select" id="pemeriksaan" name="pemeriksaan[]" aria-label="pemeriksaan" size="16" multiple>
                        <option value="AUTOREF">AUTOREF</option>
                        <option value="TONO">TONO</option>
                        <option value="OCT">OCT</option>
                        <option value="FOTO FUNDUS">FOTO FUNDUS</option>
                        <option value="USG">USG</option>
                        <option value="YAG LASER">YAG LASER</option>
                        <option value="PERIMETRI">PERIMETRI</option>
                        <option value="BIOMETRI">BIOMETRI</option>
                        <option value="LABOR">LABOR</option>
                        <option value="KERATOMETRI">KERATOMETRI</option>
                        <option value="EKG">EKG</option>
                        <option value="CT SCAN">CT SCAN</option>
                        <option value="FFA">FFA</option>
                        <option value="ANTERIOR MATA">ANTERIOR MATA</option>
                        <option value="PALPEBRA">PALPEBRA</option>
                        <option value="KONJUNGTIVA">KONJUNGTIVA</option>
                        <option value="KETERANGAN">KETERANGAN</option>
                        <option value="SURAT KETERANGAN">SURAT KETERANGAN</option>
                        <option value="SURAT RUJUKAN">SURAT RUJUKAN</option>
                        <option value="UKURAN KACAMATA">UKURAN KACAMATA</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="pemeriksaan_lainnya" name="pemeriksaan_lainnya" value="" autocomplete="off" dir="auto" placeholder="pemeriksaan_lainnya">
                        <label for="pemeriksaan_lainnya">Pemeriksaan Lainnya</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <select class="form-select" id="lokasi_pemeriksaan" name="lokasi_pemeriksaan" aria-label="lokasi_pemeriksaan">
                            <option value="" disabled selected>-- Pilih Lokasi Pemeriksaan --</option>
                            <option value="OD">OD</option>
                            <option value="OS">OS</option>
                            <option value="ODS">ODS</option>
                        </select>
                        <label for="lokasi_pemeriksaan">Lokasi Pemeriksaan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="hasil_pemeriksaan" name="hasil_pemeriksaan" value="" autocomplete="off" dir="auto" placeholder="hasil_pemeriksaan">
                        <label for="hasil_pemeriksaan">Hasil Pemeriksaan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div>
                    <hr>
                    <div class="d-grid gap-2 d-lg-flex justify-content-lg-end mb-3">
                        <button class="btn btn-body  bg-gradient" type="button" onclick="window.open(`<?= base_url('/rawatjalan/penunjang/export/' . $rawatjalan['id_rawat_jalan']) ?>`)"><i class="fa-solid fa-print"></i> Cetak Form</button>
                        <button class="btn btn-primary bg-gradient" type="submit" id="submitBtn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pemindaian Pemeriksaan Penunjang</div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm bg-gradient mb-2" type="button" id="addScanButton">
                        <i class="fa-solid fa-plus"></i> Tambah Pemindaian
                    </button>
                </div>
                <center id="empty-placeholder" class="my-3" style="display: none;">
                    <h1><i class="fa-solid fa-stethoscope"></i></h1>
                    <h3>Pemindaian Pemeriksaan Penunjang</h3>
                    <div class="text-muted">Klik "Tambah Pemindaian" untuk menambahkan pemindaian pemeriksaan penunjang</div>
                </center>
                <div id="scanPenunjangList" class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                    <?php for ($i = 0; $i < 6; $i++) : ?>
                        <div class="col">
                            <div class="card shadow-sm h-100" style="cursor: wait;">
                                <div class="card-img-top" style="background-color: var(--bs-card-cap-bg); aspect-ratio: 16/9; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);"></div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="align-self-center w-100 placeholder-glow">
                                            <span class="placeholder" style="width: 100%;"></span><br>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="w-100 placeholder-glow">
                                        <span class="placeholder" style="width: 100%;"></span><br>
                                        <span class="placeholder" style="width: 100%;"></span>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end gap-1">
                                    <a class="btn btn-body btn-sm bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                    <a class="btn btn-danger bg-gradient disabled placeholder" aria-disabled="true" style="width: 32px; height: 31px;"></a>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="scanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
            <form id="scanForm" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" id="scanModalLabel" style="font-weight: bold;"></h6>
                    <button id="closeBtn" type="button" class="btn btn-danger bg-gradient" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body py-2">
                    <input type="hidden" id="id_penunjang_scan" name="id_penunjang_scan" value="">
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select" id="pemeriksaan_scan" name="pemeriksaan_scan" aria-label="pemeriksaan_scan">
                            <option value="" disabled selected>-- Pilih Pemeriksaan --</option>
                            <option value="AUTOREF">AUTOREF</option>
                            <option value="TONO">TONO</option>
                            <option value="OCT">OCT</option>
                            <option value="FOTO FUNDUS">FOTO FUNDUS</option>
                            <option value="USG">USG</option>
                            <option value="YAG LASER">YAG LASER</option>
                            <option value="PERIMETRI">PERIMETRI</option>
                            <option value="BIOMETRI">BIOMETRI</option>
                            <option value="LABOR">LABOR</option>
                            <option value="KERATOMETRI">KERATOMETRI</option>
                            <option value="EKG">EKG</option>
                            <option value="CT SCAN">CT SCAN</option>
                            <option value="FFA">FFA</option>
                            <option value="ANTERIOR MATA">ANTERIOR MATA</option>
                            <option value="PALPEBRA">PALPEBRA</option>
                            <option value="KONJUNGTIVA">KONJUNGTIVA</option>
                            <option value="KETERANGAN">KETERANGAN</option>
                            <option value="SURAT KETERANGAN">SURAT KETERANGAN</option>
                            <option value="SURAT RUJUKAN">SURAT RUJUKAN</option>
                            <option value="UKURAN KACAMATA">UKURAN KACAMATA</option>
                        </select>
                        <label for="status_fungsional">Pemeriksaan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1 mt-1">
                        <label for="gambar" class="form-label mb-0">Unggah Gambar (maks 8 MB)</label>
                        <input class="form-control" type="file" id="gambar" name="gambar" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="gambar_preview_div" style="display: none;" class="mb-1 mt-1">
                        <div class="d-flex justify-content-center">
                            <img id="gambar_preview" src="#" alt="Gambar" class="img-thumbnail" style="max-width: 100%">
                        </div>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <input type="text" class="form-control" autocomplete="off" dir="auto" placeholder="keterangan" id="keterangan" name="keterangan">
                        <label for="keterangan">Keterangan</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end pt-2 pb-2" style="border-top: 1px solid var(--bs-border-color-translucent);">
                    <!-- Progress bar -->
                    <div class="mb-1 mt-1 w-100" id="uploadProgressDiv">
                        <div class="progress" style="border-top: 1px solid var(--bs-border-color-translucent); border-bottom: 1px solid var(--bs-border-color-translucent); border-left: 1px solid var(--bs-border-color-translucent); border-right: 1px solid var(--bs-border-color-translucent);">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient" role="progressbar" style="width: 0%; transition: none;" id="uploadProgressBar"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" id="cancelButton" class="btn btn-danger bg-gradient" style="display: none;" disabled>
                                <i class="fa-solid fa-xmark"></i> Batalkan
                            </button>
                        </div>
                        <button type="submit" id="submitButton" class="btn btn-primary bg-gradient">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
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
<?= $this->section('javascript'); ?>
<script>
    async function fetchPenunjang() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/penunjang/view/') . $penunjang['id_penunjang'] ?>');
            const data = response.data;

            $('#diagnosa').val(data.diagnosa);
            $('#dokter_pengirim').val(data.dokter_pengirim);
            $('#rujukan_dari').val(data.rujukan_dari);
            const pemeriksaan = data.pemeriksaan;
            $('#pemeriksaan option').each(function() {
                const value = $(this).val(); // Dapatkan nilai opsi
                if (pemeriksaan.includes(value)) {
                    // Tandai opsi jika ada dalam array
                    $(this).prop('selected', true);
                }
            });
            $('#pemeriksaan_lainnya').val(data.pemeriksaan_lainnya);
            $('#lokasi_pemeriksaan').val(data.lokasi_pemeriksaan);
            $('#hasil_pemeriksaan').val(data.hasil_pemeriksaan);
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
        } finally {
            $('#loadingSpinner').hide();
        }
    }

    async function fetchDokterOptions() {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/penunjang/dokteroptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#dokter_pengirim').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#dokter_pengirim').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan dokter.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchRuanganOptions() {
        $('#loadingSpinner').show();
        try {
            // Panggil API dengan query string tanggal
            const response = await axios.get(`<?= base_url('rawatjalan/penunjang/ruanganoptions') ?>`);

            if (response.data.success) {
                const options = response.data.data;

                // Hapus opsi yang ada, kecuali opsi pertama (default)
                $('#rujukan_dari').find('option:not(:first)').remove();

                // Tambahkan opsi ke elemen select
                options.forEach(option => {
                    $('#rujukan_dari').append(`<option value="${option.value}">${option.text}</option>`);
                });
            } else {
                showFailedToast('Gagal mendapatkan ruangan.');
            }
        } catch (error) {
            console.error(error);
            showFailedToast(`${error}`);
        }
    }

    async function fetchScanPenunjang() {
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('rawatjalan/penunjang/scan/list/') . $rawatjalan['id_rawat_jalan'] ?>');

            const data = response.data;
            $('#scanPenunjangList').empty();

            let totalPembayaran = 0;

            if (data.length === 0) {
                $('#empty-placeholder').show();
                $('#scanPenunjangList').hide();
            } else {
                $('#empty-placeholder').hide();
                data.forEach(function(penunjang_scan) {
                    const keterangan = penunjang_scan.keterangan ? penunjang_scan.keterangan : `<em>Tidak ada keterangan</em>`;
                    const penunjangScanElement = `
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-img-top" style="background-image: url('<?= base_url('uploads/scan_penunjang') ?>/${penunjang_scan.gambar}?t=${new Date().getTime()}'); background-color: var(--bs-card-cap-bg); aspect-ratio: 16/9; background-position: center; background-repeat: no-repeat; background-size: cover; position: relative; border-bottom: var(--bs-card-border-width) solid var(--bs-card-border-color);"></div>
                        <div class="card-body">
                            <div class="d-inline-flex">
                                <div class="align-self-center">
                                    <span class="card-text fw-bold">${penunjang_scan.pemeriksaan_scan}</span>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <small class="text-body-secondary">${keterangan}</small><br>
                                <small class="text-body-secondary date">${penunjang_scan.waktu_dibuat}</small>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-1">
                            <button class="btn btn-body btn-sm bg-gradient edit-btn" data-id="${penunjang_scan.id_penunjang_scan}"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                            <button class="btn btn-danger btn-sm bg-gradient delete-btn" data-id="${penunjang_scan.id_penunjang_scan}"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </div>                               
                    </div>
                </div>
                    `;
                    $('#scanPenunjangList').show();
                    $('#scanPenunjangList').append(penunjangScanElement);
                });
            }
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#scanPenunjangList').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }

    $(document).ready(async function() {
        // Tampilkan modal tambah scan penunjang
        $('#addScanButton').click(function() {
            $('#scanModalLabel').text('Tambah Pemindaian Pemeriksaan Penunjang'); // Ubah judul modal menjadi 'Tambah Pemindaian Pemeriksaan Penunjang'
            $('#id_penunjang_scan').val('');
            $('#scanModal').modal('show'); // Tampilkan modal resep luar
        });

        $('#gambar').change(function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#gambar_preview').attr('src', e.target.result);
                $('#gambar_preview_div').show();
            };
            reader.readAsDataURL(this.files[0]);
        });

        $(document).on('click', '.edit-btn', async function() {
            var $this = $(this);
            var id = $this.data('id');
            $this.prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Edit`);

            try {
                let response = await axios.get(`<?= base_url('/rawatjalan/penunjang/scan/view') ?>/` + id);
                let data = response.data;
                console.log(data);

                $('#scanModalLabel').text('Edit Evaluasi Edukasi');
                $('#id_penunjang_scan').val(data.id_penunjang_scan);
                $('#pemeriksaan_scan').val(data.pemeriksaan_scan);
                if (data.gambar) {
                    $('#gambar_preview').attr('src', `<?= base_url('uploads/scan_penunjang') ?>/` + data.gambar);
                    $('#gambar_preview_div').show();
                } else {
                    $('#gambar_preview_div').hide();
                }
                $('#keterangan').val(data.keterangan);
                $('#scanModal').modal('show');
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $this.prop('disabled', false).html(`<i class="fa-solid fa-pen-to-square"></i> Edit`);
            }
        });

        // Store the ID of the example to be deleted
        var id_penunjang_scan;

        // Show delete confirmation modal
        $(document).on('click', '.delete-btn', function() {
            id_penunjang_scan = $(this).data('id');
            $('#deleteMessage').html(`Hapus pemindaian pemeriksaan penunjang ini?`);
            $('#deleteModal').modal('show');
        });

        // Confirm deletion
        $('#confirmDeleteBtn').click(async function() {
            $('#deleteModal button').prop('disabled', true);
            $('#deleteMessage').html(`Menghapus, silakan tunggu...`);

            try {
                // Perform the delete operation
                let response = await axios.delete('<?= base_url('/rawatjalan/penunjang/scan/delete') ?>/' + id_penunjang_scan);

                // Show success message
                showSuccessToast(response.data.message);

                // Reload the table
                fetchScanPenunjang();
            } catch (error) {
                // Handle any error responses
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                // Re-enable the delete button and hide the modal
                $('#deleteModal').modal('hide');
                $('#deleteModal button').prop('disabled', false);
            }
        });

        $('#scanForm').submit(async function(ə) {
            ə.preventDefault();

            var url = $('#id_penunjang_scan').val() ? `<?= base_url('rawatjalan/penunjang/scan/update') ?>` : `<?= base_url('/rawatjalan/penunjang/scan/create/' . $rawatjalan['id_rawat_jalan']) ?>`;
            var formData = new FormData(this);
            console.log("Form URL:", url);
            console.log("Form Data:", formData);

            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();

            // Clear previous validation states
            $('#scanForm .is-invalid').removeClass('is-invalid');
            $('#scanForm .invalid-feedback').text('').hide();

            // Show processing button and progress bar
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#cancelButton').prop('disabled', false).show();
            $('#submitButton').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span role="status">Memproses <span id="uploadPercentage" style="font-variant-numeric: tabular-nums;">0%</span></span>
            `);

            // Disable form inputs
            $('#scanForm input').prop('disabled', true);

            try {
                // Perform the post request with progress handling
                let response = await axios.post(url, formData, {
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

                // Handle successful response
                if (response.data.success) {
                    showSuccessToast(response.data.message, 'success');
                    $('#scanModal').modal('hide');
                    $('#uploadProgressBar').css('width', '0%');
                    fetchScanPenunjang();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#scanForm .is-invalid').removeClass('is-invalid');
                    $('#scanForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
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
                // Reset the form and UI elements
                $('#uploadPercentage').html('0%');
                $('#cancelButton').prop('disabled', true).hide();
                $('#submitButton').prop('disabled', false).html(`
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                `);
                $('#scanForm input').prop('disabled', false);
            }

            // Attach the cancel functionality to the close button
            $('#closeBtn, #cancelButton').on('click', function() {
                source.cancel('Penambahan pemindaian pemeriksaan penunjang telah dibatalkan.');
            });
        });

        // Reset form saat modal ditutup
        $('#scanModal').on('hidden.bs.modal', function() {
            $('#scanForm')[0].reset();
            $('#uploadProgressBar').removeClass('bg-danger').css('width', '0%');
            $('#gambar_preview').attr('src', '#');
            $('#gambar_preview_div').hide();
            $('#scanForm .is-invalid').removeClass('is-invalid');
            $('#scanForm .invalid-feedback').text('').hide();
        });

        $('#penunjangForm').submit(async function(ə) {
            ə.preventDefault();

            const formData = new FormData(this);
            console.log("Form Data:", $(this).serialize());

            // Clear previous validation states
            $('#penunjangForm .is-invalid').removeClass('is-invalid');
            $('#penunjangForm .invalid-feedback').text('').hide();
            $('#submitBtn').prop('disabled', true).html(`
                <span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Simpan
            `);

            // Disable form inputs
            $('#penunjangForm input, #penunjangForm select').prop('disabled', true);

            try {
                const response = await axios.post(`<?= base_url('/rawatjalan/penunjang/update/' . $penunjang['id_penunjang']) ?>`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    showSuccessToast(response.data.message);
                    fetchPenunjang();
                } else {
                    console.log("Validation Errors:", response.data.errors);

                    // Clear previous validation states
                    $('#penunjangForm .is-invalid').removeClass('is-invalid');
                    $('#penunjangForm .invalid-feedback').text('').hide();

                    // Display new validation errors
                    for (const field in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(field)) {
                            const fieldElement = $('#' + field);
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
                $('#penunjangForm input, #penunjangForm select').prop('disabled', false);
            }
        });
        await Promise.all([fetchDokterOptions(), fetchRuanganOptions()]);
        await fetchPenunjang();
        fetchScanPenunjang();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>