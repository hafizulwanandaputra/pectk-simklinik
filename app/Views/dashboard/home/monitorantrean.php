<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .no-fluid-content {
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-right: auto;
        margin-left: auto;
        max-width: 100%;
    }

    .full-card-height {
        max-height: calc((100vh - 101px) - 3rem);
        min-height: calc((100vh - 101px) - 3rem);
    }

    .main-content-inside {
        margin-left: 0px;
    }

    .ratio-onecol {
        --bs-aspect-ratio: 33%;
    }

    #img_bpjs {
        color: inherit;
    }

    @media (max-width: 991.98px) {
        .ratio-onecol {
            --bs-aspect-ratio: 75%;
        }

        .full-card-height {
            max-height: 100%;
            min-height: 100%;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column">
            <div class="fw-medium fs-6 lh-sm" id="tanggal"></div>
            <div class="fw-medium lh-sm date" id="waktu" style="font-size: 0.75em;"></div>
        </div>
    </div>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
    <a id="btnEnableVoice" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Aktifkan suara"><i class="fa-solid fa-microphone"></i></a>
    <a id="btnPilihLoket" class="fs-6 mx-2 text-success-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Pilih Loket"><i class="fa-solid fa-user-gear"></i></a>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside p-3">
    <div id="alert-voice" class="fixed-top" style="z-index: 99; margin-top: 48px;">
        <ul class="list-group shadow-sm rounded-0">
            <li class="list-group-item border-top-0 border-end-0 border-start-0 border-warning-subtle bg-warning-subtle text-warning-emphasis transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Mulai Chrome 71+ dan mayoritas peramban modern, <code>speechSynthesis.speak()</code> tidak boleh jalan otomatis tanpa interaksi pengguna (klik, ketuk, atau <em>keypress</em>) karena alasan privasi atau spam audio. Silakan klik tombol <kbd><i class="fa-solid fa-microphone"></i></kbd> untuk mengaktifkan suara.
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="no-fluid-content">
        <div class="row row-cols-1 row-cols-lg-2 g-3">
            <div class="col col-lg-6 col-xl-5">
                <div class="mb-3" style="max-height: 52px; min-height: 52px;">
                    <span class="d-flex justify-content-center align-items-center" style="font-size: 12pt;">
                        <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="56px">
                        <div class="ps-3">
                            <div class="lh-sm text-start text-success-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                            <div class="lh-1"><em style="font-size: 8pt;">Melayani dengan Hati</em></div>
                        </div>
                    </span>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 g-2">
                    <div class="col full-card-height">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div class="fs-3 fw-light" id="nama_loket_1"><em>Loket Tutup</em></div>
                                <div class="fs-6">Nomor antrean:</div>
                                <h1 class="fw-medium mb-0" id="nomor_antrean_label_1"><i class="fa-solid fa-minus"></i></h1>
                            </div>
                            <div class="card-body p-0 overflow-hidden">
                                <ul class="list-group list-group-flush" id="list_antrean_monitor_1">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col full-card-height">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div class="fs-3 fw-light" id="nama_loket_2"><em>Loket Tutup</em></div>
                                <div class="fs-6">Nomor antrean:</div>
                                <h1 class="fw-medium mb-0" id="nomor_antrean_label_2"><i class="fa-solid fa-minus"></i></h1>
                            </div>
                            <div class="card-body p-0 overflow-hidden">
                                <ul class="list-group list-group-flush" id="list_antrean_monitor_2">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-lg-6 col-xl-7">
                <div class="card shadow-sm" id="top-card-right">
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pilihLoketModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="pilihLoketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable ">
            <div id="rajaldiv" enctype="multipart/form-data" class="modal-content bg-body-tertiary shadow-lg transparent-blur">
                <div class="modal-header justify-content-between pt-2 pb-2" style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                    <h6 class="pe-2 modal-title fs-6 text-truncate" style="font-weight: bold;">Pilih Loket</h6>
                    <button id="pilihLoketCloseBtn" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2">
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select form-select-sm" id="pilih_loket_1">
                            <option value="" selected>Loket Tutup</option>
                            <?php foreach ($loket as $l) : ?>
                                <option value="<?= $l['nama_loket']; ?>"><?= $l['nama_loket']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="pilih_loket_1">Kolom 1</label>
                    </div>
                    <div class="form-floating mb-1 mt-1">
                        <select class="form-select form-select-sm" id="pilih_loket_2">
                            <option value="" selected>Loket Tutup</option>
                            <?php foreach ($loket as $l) : ?>
                                <option value="<?= $l['nama_loket']; ?>"><?= $l['nama_loket']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="pilih_loket_1">Kolom 2</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    })
</script>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    let countdownTimer = null; // Untuk menyimpan referensi timer agar bisa dibatalkan

    // Aktifkan plugin dan set locale ke Bahasa Indonesia
    dayjs.extend(dayjs_plugin_localizedFormat);
    dayjs.locale('id');

    function updateDateTime() {
        const now = dayjs();
        $('#tanggal').text(now.format('dddd, D MMMM YYYY'));
        $('#waktu').text(now.format('HH.mm.ss (UTCZ)'));
    }

    let voiceEnabled = false;
    let googleVoice = null;

    // Ambil daftar voice Google Indonesia
    function loadVoices() {
        const voices = speechSynthesis.getVoices();
        googleVoice = voices.find(voice =>
            voice.name.includes("Google") && voice.lang === "id-ID"
        );
    }

    // Chrome/Edge kadang perlu event onvoiceschanged
    speechSynthesis.onvoiceschanged = loadVoices;

    // Fungsi untuk aktifkan suara
    function enableVoice() {
        loadVoices();
        const u = new SpeechSynthesisUtterance("");
        u.lang = 'id-ID';
        if (googleVoice) {
            u.voice = googleVoice;
        }
        speechSynthesis.speak(u);
        voiceEnabled = true;
        console.log("Voice enabled with Google Indonesia");
    }

    async function setNamaLoket1FromLocalStorage() {
        return new Promise((resolve) => {
            const savedLoket = localStorage.getItem('loket_1');
            if (savedLoket) {
                $('#pilih_loket_1').val(savedLoket);
                if (savedLoket === '') {
                    $('#nama_loket_1').html(`<em>Loket Tutup</em>`);
                } else {
                    $('#nama_loket_1').text(savedLoket);
                }
            }
            resolve(); // selesai
        });
    }

    async function setNamaLoket2FromLocalStorage() {
        return new Promise((resolve) => {
            const savedLoket = localStorage.getItem('loket_2');
            if (savedLoket) {
                $('#pilih_loket_2').val(savedLoket);
                if (savedLoket === '') {
                    $('#nama_loket_2').html(`<em>Loket Tutup</em>`);
                } else {
                    $('#nama_loket_2').text(savedLoket);
                }
            }
            resolve(); // selesai
        });
    }

    async function fetchAntrean() {
        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const loket_1 = await axios.get('<?= base_url('home/list_antrean_monitor') ?>', {
                params: {
                    loket: $('#pilih_loket_1').val(),
                    tanggal_antrean: `<?= date('Y-m-d'); ?>`,
                }
            });

            const loket_2 = await axios.get('<?= base_url('home/list_antrean_monitor') ?>', {
                params: {
                    loket: $('#pilih_loket_2').val(),
                    tanggal_antrean: `<?= date('Y-m-d'); ?>`,
                }
            });

            const data_loket_1 = loket_1.data;
            const data_loket_2 = loket_2.data;
            $('#list_antrean_monitor_1').empty();
            $('#list_antrean_monitor_2').empty();

            data_loket_1.antrean.forEach(function(antrean) {
                const antreanElement = `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold fs-5">${antrean.kode_antrean}-${antrean.nomor_antrean}</div>
                                ${antrean.loket}
                            </div>
                        </li>
                    `;
                $('#list_antrean_monitor_1').append(antreanElement);
            });
            data_loket_2.antrean.forEach(function(antrean) {
                const antreanElement = `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold fs-5">${antrean.kode_antrean}-${antrean.nomor_antrean}</div>
                                ${antrean.loket}
                            </div>
                        </li>
                    `;
                $('#list_antrean_monitor_2').append(antreanElement);
            });
        } catch (error) {
            showFailedToast('Terjadi kesalahan. Silakan coba lagi.<br>' + error);
            $('#list_antrean_monitor').empty();
        } finally {
            // Hide the spinner when done
            $('#loadingSpinner').hide();
        }
    }
    $(document).ready(async function() {
        $('#btnPilihLoket').on('click', function(ə) {
            ə.preventDefault();
            $('#pilihLoketModal').modal('show');
        });

        $('#btnEnableVoice').on('click', function(ə) {
            ə.preventDefault();
            enableVoice();
            $('#alert-voice').remove();
            $(this).remove();
            showSuccessToast('Suara diaktifkan. Pemanggilan nomor antrean sudah bisa digunakan.')
        });

        $('#pilih_loket_1').on('change', function() {
            const selectedValue = $(this).val();
            localStorage.setItem('loket_1', selectedValue);
            if (selectedValue === '') {
                $('#nama_loket_1').html(`<em>Loket Tutup</em>`);
            } else {
                $('#nama_loket_1').text(selectedValue);
            }
            $('#nomor_antrean_label_1').html(`<i class="fa-solid fa-minus"></i>`);
            fetchAntrean();
        });

        $('#pilih_loket_2').on('change', function() {
            const selectedValue = $(this).val();
            localStorage.setItem('loket_2', selectedValue);
            if (selectedValue === '') {
                $('#nama_loket_2').html(`<em>Loket Tutup</em>`);
            } else {
                $('#nama_loket_2').text(selectedValue);
            }
            $('#nomor_antrean_label_2').html(`<i class="fa-solid fa-minus"></i>`);
            fetchAntrean();
        });

        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const message = JSON.parse(event.data);

            if (message.panggil_antrean && message.data) {
                const nomorAntrean = message.data.nomor;
                const [huruf, angka] = nomorAntrean.split('-');
                const kalimat = `Nomor antrean, ${huruf}, ${angka}, silakan menuju ${message.data.loket}.`;

                if (message.data.loket === $('#pilih_loket_1').val()) {
                    const utterance = new SpeechSynthesisUtterance(kalimat);
                    utterance.lang = 'id-ID';
                    if (googleVoice) {
                        utterance.voice = googleVoice;
                    }
                    speechSynthesis.speak(utterance);
                    $('#nomor_antrean_label_1').text(nomorAntrean);
                    $('#nama_loket_1').text(message.data.loket);
                } else if (message.data.loket === $('#pilih_loket_2').val()) {
                    const utterance = new SpeechSynthesisUtterance(kalimat);
                    utterance.lang = 'id-ID';
                    if (googleVoice) {
                        utterance.voice = googleVoice;
                    }
                    speechSynthesis.speak(utterance);
                    $('#nomor_antrean_label_2').text(nomorAntrean);
                    $('#nama_loket_2').text(message.data.loket);
                } else {
                    showFailedToast("Loket tidak sesuai dengan yang dipilih. Tidak ada suara yang diputar.");
                }
            } else if (message.update) {
                console.log("Received update from WebSocket");
                fetchAntrean();
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        $(document).on('visibilitychange', function() {
            if (document.visibilityState === "visible") {
                fetchAntrean();
            }
        });

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        await Promise.all([
            setNamaLoket1FromLocalStorage(),
            setNamaLoket2FromLocalStorage()
        ]);
        fetchAntrean();
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>