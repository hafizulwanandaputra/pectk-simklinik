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
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/operasi'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm"><?= $headertitle; ?></div>
            <div class="fw-medium lh-sm" style="font-size: 0.75em;"><?= $operasi['nama_pasien']; ?> • <?= $usia->y . " tahun " . $usia->m . " bulan" ?> • <?= $operasi['no_rm'] ?></div>
        </div>
    </div>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <?php if ($previous): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/spko/' . $previous['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $previous['nomor_booking']; ?> • <?= $previous['no_rm'] ?> • <?= $previous['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-left"></i></a>
    <?php else: ?>
        <span class="fs-6 mx-2 text-success-emphasis" style="cursor: no-drop; opacity: .5;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tidak ada pasien operasi sebelumnya"><i class="fa-solid fa-circle-arrow-left"></i></span>
    <?php endif; ?>
    <?php if ($next): ?>
        <a class="fs-6 mx-2 text-success-emphasis" href="<?= site_url('operasi/spko/' . $next['id_sp_operasi']) ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="<?= $next['nomor_booking']; ?> • <?= $next['no_rm'] ?> • <?= $next['nama_pasien']; ?>"><i class="fa-solid fa-circle-arrow-right"></i></a>
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
                        <a class="nav-link py-1 text-nowrap active activeLink" href="<?= base_url('operasi/spko/' . $operasi['id_sp_operasi']); ?>">SPKO</a>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/praoperasi/' . $operasi['id_sp_operasi']); ?>">Pra Operasi</a>
                        <a class="nav-link py-1 text-nowrap" href="<?= base_url('operasi/safety/' . $operasi['id_sp_operasi']); ?>">Keselamatan</a>
                    </nav>
                </div>
            </li>
            <li class="list-group-item border-top-0 border-end-0 border-start-0 bg-body-tertiary transparent-blur">
                <div class="no-fluid-content">
                    <nav class="nav nav-underline flex-nowrap overflow-auto">
                        <?php foreach ($listRawatJalan as $list) : ?>
                            <a class="<?= (date('Y-m-d', strtotime($list['tanggal_registrasi'])) != date('Y-m-d')) ? 'text-danger' : ''; ?> nav-link py-1 <?= ($activeSegment === $list['id_sp_operasi']) ? 'active activeLink' : '' ?>" href="<?= base_url('operasi/spko/' . $list['id_sp_operasi']); ?>">
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
            <?php if (date('Y-m-d', strtotime($operasi['tanggal_registrasi'])) != date('Y-m-d')) : ?>
                <div id="alert-date" class="alert alert-warning alert-dismissible" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Saat ini Anda melihat data kunjungan pasien pada <?= date('Y-m-d', strtotime($operasi['tanggal_registrasi'])) ?>. Pastikan Anda mengisi data sesuai dengan tanggal kunjungan pasien.
                        </div>
                        <button type="button" id="close-alert" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
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
                        <label for="jenis_bius" class="col col-form-label">Jenis Bius</label>
                        <div class="col col-form-label">
                            <div class="d-flex align-items-center justify-content-evenly">
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
                    <div class="mb-2 row row-cols-1 row-cols-lg-2 g-2">
                        <div class="col">
                            <!-- Canvas untuk menggambar -->
                            <div class="card h-100 shadow-sm">
                                <div class="card-header">Gambar <em>Site Marking</em></div>
                                <div class="card-body p-2">
                                    <!-- Canvas yang responsif -->
                                    <div class="rounded border w-100 overflow-hidden bg-white text-center">
                                        <div class="canvas-container overflow-x-scroll">
                                            <canvas id="site_marking_canvas" width="384" height="411"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-grid">
                                    <div class="btn-group">
                                        <button type="button" id="clear_drawing" class="btn btn-danger btn-sm bg-gradient"><i class="fa-solid fa-xmark"></i> Bersihkan</button>
                                        <button type="button" id="apply_drawing" class="btn btn-success btn-sm bg-gradient"><i class="fa-solid fa-check"></i> Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <!-- Pratinjau -->
                            <div class="card h-100 shadow-sm">
                                <div class="card-header">Pratinjau <em>Site Marking</em></div>
                                <div class="card-body p-2">
                                    <div id="site_marking_preview_div" style="display: none;">
                                        <div class="d-flex justify-content-center">
                                            <img id="site_marking_preview" src="#" alt="Gambar" class="img-thumbnail" style="width: 100%; max-width: 384px;">
                                        </div>
                                    </div>
                                    <input type="hidden" id="site_marking" name="site_marking" value="" />
                                </div>
                                <div class="card-footer" id="cancel_changes" style="display: none;">
                                    <div class="d-grid">
                                        <div class="btn-group">
                                            <button type="button" id="cancel_drawing" class="btn btn-danger btn-sm bg-gradient"><i class="fa-solid fa-xmark"></i> Batalkan Perubahan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const data = JSON.parse(event.data);

            if (data.delete) {
                console.log("Received delete from WebSocket, going back...");
                location.href = `<?= base_url('/operasi'); ?>`;
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

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

        const canvas = document.getElementById('site_marking_canvas');
        const ctx = canvas.getContext('2d');

        // Atur properti default agar gambar lebih tebal
        ctx.lineWidth = 5; // Tebal garis
        ctx.lineCap = 'round'; // Ujung garis membulat
        ctx.lineJoin = 'round'; // Sudut garis membulat

        const backgroundImage = new Image();
        backgroundImage.src = '<?= base_url('assets/images/site_marking.jpg') ?>'; // Ganti dengan path gambar latar belakang yang diinginkan

        let backgroundSnapshot = null; // Menyimpan snapshot latar belakang
        let isDrawing = false;

        backgroundImage.onload = function() {
            ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
            backgroundSnapshot = ctx.getImageData(0, 0, canvas.width, canvas.height); // Simpan gambar latar awal
        };

        // Fungsi mendapatkan posisi (mouse/touch)
        function getPosition(event) {
            let rect = canvas.getBoundingClientRect();
            if (event.touches) {
                return {
                    x: event.touches[0].clientX - rect.left,
                    y: event.touches[0].clientY - rect.top
                };
            } else {
                return {
                    x: event.offsetX,
                    y: event.offsetY
                };
            }
        }

        // Mulai menggambar (mouse & sentuh)
        function startDrawing(event) {
            event.preventDefault();
            isDrawing = true;
            let pos = getPosition(event);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        // Menggambar (mouse & sentuh)
        function draw(event) {
            if (!isDrawing) return;
            event.preventDefault();
            let pos = getPosition(event);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        // Selesai menggambar (mouse & sentuh)
        function stopDrawing(event) {
            isDrawing = false;
            ctx.beginPath(); // Reset jalur agar tidak terhubung ke gambar berikutnya
        }

        // Event untuk mouse
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        // Event untuk layar sentuh
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        // Fungsi untuk membersihkan hanya gambar tanpa latar belakang
        $('#clear_drawing').click(function() {
            if (backgroundSnapshot) {
                ctx.putImageData(backgroundSnapshot, 0, 0); // Pulihkan snapshot awal
            } else {
                ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
            }
        });

        // Terapkan hasil gambar ke pratinjau
        $('#apply_drawing').click(function() {
            const dataURL = canvas.toDataURL('image/png');
            $('#site_marking_preview').attr('src', dataURL);
            $('#site_marking_preview_div').show();
            // Menyimpan gambar base64 ke input tersembunyi
            $('#site_marking').val(dataURL);
            $('#cancel_changes').show();
        });

        // Batalkan perubahan jika tidak jadi
        $('#cancel_drawing').click(async function() {
            $('#loadingSpinner').show();
            $(this).prop('disabled', true).html(`<span class="spinner-border" style="width: 1em; height: 1em;" aria-hidden="true"></span> Batalkan Perubahan`);
            try {
                const response = await axios.get('<?= base_url('operasi/spko/view/') . $operasi['id_sp_operasi'] ?>');
                const data = response.data;
                if (data.site_marking) {
                    $('#site_marking_preview').attr('src', `<?= base_url('uploads/site_marking') ?>/${data.site_marking}?t=${new Date().getTime()}`);
                    $('#site_marking_preview_div').show();
                    $('#site_marking').val(data.site_marking);
                } else {
                    $('#site_marking_preview_div').hide();
                    $('#site_marking').val('');
                }
                $('#cancel_changes').hide();
            } catch (error) {
                showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            } finally {
                $('#loadingSpinner').hide();
                $(this).prop('disabled', false).html(`<i class="fa-solid fa-xmark"></i> Batalkan Perubahan`)
            }
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
                $('#SPKOForm input, #SPKOForm select, #SPKOForm button').prop('disabled', false);
            }
        });
        // $('#loadingSpinner').hide();
        fetchSPKO();
    });
    // Show toast notification
    <?= $this->include('toast/index') ?>
</script>
<?= $this->endSection(); ?>