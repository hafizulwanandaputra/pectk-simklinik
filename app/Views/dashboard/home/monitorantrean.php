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
        max-height: calc(100vh - 110px - 2rem);
        min-height: calc(100vh - 110px - 2rem);
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
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3">
    <div class="no-fluid-content">
        <div class="row row-cols-1 row-cols-lg-2 g-4">
            <div class="col col-lg-5">
                <div class="mt-3 mb-3" style="max-height: 48px; min-height: 48px;">
                    <span class="lh-sm d-flex justify-content-center justify-content-lg-start align-items-center" style="font-size: 16pt;">
                        <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="56px">
                        <div class="ps-3 text-start text-success-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                    </span>
                </div>
                <div class="row row-cols-1 g-4">
                    <div class="col full-card-height">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div class="fs-5">Nomor antrean:</div>
                                <h1 class="fw-medium mb-0" id="nomor_antrean_label"></h1>
                                <div class="fs-5">Silakan menuju <span id="loket"></span></div>
                            </div>
                            <div class="card-body p-0 overflow-hidden">
                                <ul class="list-group list-group-flush" id="list_antrean_monitor">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-lg-7">
                <div id="alert-voice" class="alert alert-warning mt-lg-3" role="alert">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Mulai Chrome 71+ dan mayoritas peramban modern, <code>speechSynthesis.speak()</code> tidak boleh jalan otomatis tanpa interaksi pengguna (klik, ketuk, atau <em>keypress</em>) karena alasan privasi atau spam audio. Silakan klik tombol <kbd><i class="fa-solid fa-microphone"></i></kbd> untuk mengaktifkan suara.
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm" id="top-card-right">
                    <div class="card-body">

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

    async function fetchAntrean() {
        // Show the spinner
        $('#loadingSpinner').show();

        try {
            const response = await axios.get('<?= base_url('home/list_antrean_monitor') ?>', {
                params: {
                    tanggal_antrean: `<?= date('Y-m-d'); ?>`,
                }
            });

            const data = response.data;
            $('#list_antrean_monitor').empty();

            data.antrean.forEach(function(antrean) {
                const antreanElement = `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold">${antrean.kode_antrean}-${antrean.nomor_antrean}</div>
                                ${antrean.loket}
                            </div>
                        </li>
                    `;

                $('#list_antrean_monitor').append(antreanElement);
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
        $('#btnEnableVoice').on('click', function(ə) {
            ə.preventDefault();
            enableVoice();
            $('#alert-voice').remove();
            $('#top-card-right').addClass('mt-lg-3');
            $(this).remove();
            showSuccessToast('Suara diaktifkan. Pemanggilan nomor antrean sudah dapat dilakukan.')
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

                const utterance = new SpeechSynthesisUtterance(kalimat);
                utterance.lang = 'id-ID';
                if (googleVoice) {
                    utterance.voice = googleVoice;
                }
                speechSynthesis.speak(utterance);

                $('#nomor_antrean_label').text(nomorAntrean);
                $('#loket').text(message.data.loket);
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

        fetchAntrean();
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>