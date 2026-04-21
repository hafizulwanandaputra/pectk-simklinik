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

    .nomor-antrean {
        font-size: 6rem;
    }

    .full-card-height {
        max-height: calc((100dvh - 3rem - 53px) - 3rem);
        min-height: calc((100dvh - 3rem - 53px) - 3rem);
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
    <a id="btnEnableVoice" class="fs-6 mx-2 text-body-emphasis" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Aktifkan suara"><i class="fa-solid fa-microphone"></i></a>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside p-3">
    <div id="alert-voice" class="fixed-top p-2" style="z-index: 99; margin-top: 3rem;">
        <ul class="list-group shadow-sm">
            <li class="list-group-item list-group-item-warning transparent-blur">
                <div class="no-fluid-content">
                    <div class="d-flex align-items-start">
                        <div style="width: 12px; text-align: center;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="w-100 ms-3">
                            Mulai Chrome 71+ dan mayoritas peramban modern, <code>speechSynthesis.speak()</code> tidak boleh jalan otomatis tanpa interaksi pengguna (klik, ketuk, atau <em>keypress</em>) karena alasan privasi atau spam audio. Silakan klik tombol <kbd><i class="fa-solid fa-microphone"></i></kbd> untuk mengaktifkan suara setiap mengakses atau menyegarkan (<em>refresh</em>) halaman ini.
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="no-fluid-content">
        <div class="row">
            <div class="col">
                <div class="mb-3" style="max-height: 52px; min-height: 52px;">
                    <span class="d-flex justify-content-center align-items-center" style="font-size: 12pt;">
                        <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="56px">
                        <div class="ps-3">
                            <div class="lh-sm text-start text-body-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                            <div class="lh-1"><em style="font-size: 8pt;">Melayani dengan Hati</em></div>
                        </div>
                    </span>
                </div>
                <div class="row row-cols-1 g-2">
                    <div class="col full-card-height">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div class="fs-3 fw-light" id="nama_poli"><i class="fa-solid fa-minus"></i></div>
                            </div>
                            <div class="card-body overflow-hidden">
                                <h1 class="fw-medium nomor-antrean" id="nomor_antrean_label"><i class="fa-solid fa-minus"></i></h1>
                                <div class="fs-6 date"><span id="label_no_rm"><i class="fa-solid fa-minus"></i></span><br><span id="label_no_reg"><i class="fa-solid fa-minus"></i></span><br><span id="label_dokter"><i class="fa-solid fa-minus"></i></span></div>
                            </div>
                        </div>
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

    $(document).ready(async function() {
        $('#btnEnableVoice').on('click', function(ə) {
            ə.preventDefault();
            enableVoice();
            $('#alert-voice').remove();
            $(this).remove();
            showSuccessToast('Suara diaktifkan. Pemanggilan nomor antrean sudah bisa digunakan.')
        });

        function toTitleCase(str) {
            return str.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
        }

        const socket = new WebSocket('<?= env('WS-URL-JS') ?>'); // Ganti dengan domain VPS

        socket.onopen = () => {
            console.log("Connected to WebSocket server");
        };

        socket.onmessage = async function(event) {
            const message = JSON.parse(event.data);
            console.log(event);

            if (message.panggil_antrean_poli && message.data) {
                const nama = toTitleCase(message.data.nama_pasien);
                const ruangan = toTitleCase(message.data.ruangan);
                const kalimat = `Pasien atas nama ${nama}, silakan menuju ke ${ruangan}.`;

                const utterance = new SpeechSynthesisUtterance(kalimat);
                utterance.lang = 'id-ID';
                if (googleVoice) {
                    utterance.voice = googleVoice;
                }
                speechSynthesis.speak(utterance);
                $('#nomor_antrean_label').text(message.data.nama_pasien);
                $('#label_no_rm').text(message.data.no_rm);
                $('#label_no_reg').text(message.data.nomor_registrasi);
                $('#label_dokter').text(message.data.dokter);
                $('#nama_poli').text(message.data.ruangan);
            } else if (message.update) {
                console.log("Received update from WebSocket");
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");
        };

        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>