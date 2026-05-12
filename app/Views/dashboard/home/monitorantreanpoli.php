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

    #foto_dokter {
        background-image: url('');
        width: 32px;
        background-color: var(--bs-body-bg);
        aspect-ratio: 1/1;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
        outline: 1px solid var(--bs-body-bg);
        box-shadow: 0 0 0 2px var(--bs-secondary);
    }

    .nomor-antrean {
        font-size: 5rem;
        font-weight: 900;
    }

    #nama_poli {
        font-size: 3rem;
        font-weight: 700;
    }

    #label_dokter_2 {
        font-size: 2rem;
        font-weight: 700;
    }

    #label_no_rm {
        font-size: 3rem;
        font-weight: 900;
    }

    .full-card-height {
        max-height: calc((100dvh - 3rem - 57px) - 3rem);
        min-height: calc((100dvh - 3rem - 57px) - 3rem);
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

    #logo-pec-header {
        max-height: 56px;
        min-height: 56px;
    }

    #logo-pec {
        height: 56px;
        width: auto;
    }

    #logo-pec-text {
        font-size: 14pt;
    }

    #logo-pec-subtext {
        font-size: 10pt;
    }

    #waktu2 {
        font-size: 28pt;
    }

    #tanggal2 {
        font-size: 11pt;
    }

    @media (min-width: 992px) {
        .full-card-height {
            max-height: calc((100dvh - 3rem - 97px) - 3rem);
            min-height: calc((100dvh - 3rem - 97px) - 3rem);
        }

        #logo-pec-header {
            max-height: 96px;
            min-height: 96px;
        }

        #logo-pec {
            height: 96px;
            width: auto;
        }

        #logo-pec-text {
            font-size: 20pt;
        }

        #logo-pec-subtext {
            font-size: 12pt;
        }

        #waktu2 {
            font-size: 44.8pt;
        }

        #tanggal2 {
            font-size: 17.6pt;
        }
    }

    @media (min-width: 1280px) {
        .full-card-height {
            max-height: calc((100dvh - 3rem - 129px) - 3rem);
            min-height: calc((100dvh - 3rem - 129px) - 3rem);
        }

        #logo-pec-header {
            max-height: 128px;
            min-height: 128px;
        }

        #logo-pec {
            height: 128px;
            width: auto;
        }

        #logo-pec-text {
            font-size: 24pt;
        }

        #logo-pec-subtext {
            font-size: 18pt;
        }

        #waktu2 {
            font-size: 56pt;
        }

        #tanggal2 {
            font-size: 22pt;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <div class="flex-fill text-truncate">
        <div class="d-flex flex-column d-md-none">
            <div class="fw-medium fs-6 lh-sm date" id="waktu1"></div>
            <div class="fw-medium lh-sm" id="tanggal1" style="font-size: 0.75em;"></div>
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
                <div id="logo-pec-header" class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <img id="logo-pec" src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK">
                        <div class="ps-3">
                            <div id="logo-pec-text" class="lh-sm text-start text-body-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                            <div id="logo-pec-subtext" class="lh-1"><em>Melayani dengan Hati</em></div>
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold lh-sm text-end date" id="waktu2"></div>
                        <div class="fw-semibold lh-sm text-end" id="tanggal2"></div>
                    </div>
                </div>
                <div class="row row-cols-1 g-2">
                    <div class="col full-card-height">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <div id="nama_poli"><i class="fa-solid fa-minus"></i></div>
                            </div>
                            <div class="card-body overflow-hidden">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="nomor-antrean lh-sm" id="nomor_antrean_label"><i class="fa-solid fa-minus"></i></div>
                                        <div class="fw-semibold fs-1 lh-sm date"><span id="label_no_rm"><i class="fa-solid fa-minus"></i></span><br><span id="label_no_reg"><i class="fa-solid fa-minus"></i></span><br><span class="d-block d-xl-none" id="label_dokter_1"><i class="fa-solid fa-minus"></i></span></div>
                                    </div>
                                    <div class="d-none d-xl-block" style="max-width: 440px; min-width: 440px;">
                                        <div class="d-flex justify-content-end">
                                            <div id="foto_dokter" class="rounded-pill bg-body m-2 d-flex justify-content-center align-items-center" style="width: 240px; height: 240px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="140" height="140" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14 14s-1-4-6-4-6 4-6 4 1 0 6 0 6 0 6 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-end w-100">
                                            <span>
                                                <span id="label_dokter_2"><i class="fa-solid fa-minus"></i></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-sheet p-4 py-md-5 fade" id="refreshModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="refreshModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                <div class="modal-body p-4">
                    <h5 id="refreshMessage"><em>Websocket</em> terputus!</h5>
                    <h6 class="fw-normal">Silakan periksa koneksi <em>websocket</em> Anda. Jika ada masalah, hubungi pengembang aplikasi.</h6>
                    <h6 class="mb-0 fw-normal date" id="refreshSubmessage">Mencoba menghubungkan ulang dalam 5 detik</h6>
                    <div class="row gy-2 pt-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="logoutRefreshBtn">Keluar</button>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-lg btn-primary bg-gradient fs-6 mb-0 rounded-4" id="refreshBtn">Hubungkan ulang sekarang</button>
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
    let socket;
    let reconnectDelay = 5000; // 5 detik fix
    let countdownTimer = null; // Untuk menyimpan referensi timer agar bisa dibatalkan
    let voiceEnabled = false;
    let googleVoice = null;

    // Aktifkan plugin dan set locale ke Bahasa Indonesia
    dayjs.extend(dayjs_plugin_localizedFormat);
    dayjs.locale('id');

    function updateDateTime() {
        const now = dayjs();
        $('#tanggal1').text(now.format('dddd, D MMMM YYYY'));
        $('#tanggal2').text(now.format('UTCZ • dddd, D MMMM YYYY'));
        $('#waktu1').text(now.format('HH.mm.ss (UTCZ)'));
        $('#waktu2').text(now.format('HH.mm.ss'));
    }

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

    function connectWebSocket() {
        socket = new WebSocket('<?= env('WS-URL-JS') ?>');

        socket.onopen = () => {
            console.log("Connected to WebSocket server");

            $('#refreshModal').modal('hide');
            showSuccessToast('<em>Websocket</em> terhubung');

            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
        };

        socket.onmessage = async function(event) {
            const message = JSON.parse(event.data);
            console.log(event);

            if (message.panggil_antrean_poli && message.data) {
                const nama = toTitleCase(message.data.nama_pasien);
                const ruangan = toTitleCase(message.data.ruangan);
                const kalimat = `Pasien atas nama ${nama}, silakan menuju ke ${ruangan}.`;

                // anti suara numpuk
                speechSynthesis.cancel();

                const utterance = new SpeechSynthesisUtterance(kalimat);
                utterance.lang = 'id-ID';
                if (googleVoice) {
                    utterance.voice = googleVoice;
                }
                speechSynthesis.speak(utterance);

                let profilephoto = message.data.profilephoto;
                if (profilephoto === true) {
                    $('#foto_dokter').css('background-image', `url('<?= base_url('/profilephoto'); ?>/${message.data.id_dokter}')`).html("");
                } else {
                    $('#foto_dokter').css('background-image', `url('')`).html(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="140" height="140" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14 14s-1-4-6-4-6 4-6 4 1 0 6 0 6 0 6 0z" />
                    </svg>
                `);
                }

                $('#nomor_antrean_label').text(message.data.nama_pasien);
                $('#label_no_rm').text(message.data.no_rm);
                $('#label_no_reg').text(message.data.nomor_registrasi);
                $('#label_dokter_1, #label_dokter_2').text(message.data.dokter);
                $('#nama_poli').text(message.data.ruangan);
            }
        };

        socket.onclose = () => {
            console.log("Disconnected from WebSocket server");

            $('#refreshModal').modal('show');

            let countdown = 5;

            $('#refreshSubmessage').html(
                `Mencoba menghubungkan ulang dalam ${countdown} detik`
            );

            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
            }

            countdownTimer = setInterval(() => {
                countdown--;

                if (countdown > 0) {
                    $('#refreshSubmessage').html(
                        `Mencoba menghubungkan ulang dalam ${countdown} detik`
                    );
                } else {
                    clearInterval(countdownTimer);
                    countdownTimer = null;

                    connectWebSocket(); // reconnect
                }
            }, 1000);
        };

        socket.onerror = (error) => {
            console.error("WebSocket error:", error);
            showFailedToast(`<em>Websocket</em> mengalami kesalahan.<br>` + error);
            socket.close();
        };
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

        $('#refreshBtn').on('click', function() {
            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }

            $('#refreshModal').modal('hide');

            if (socket) {
                socket.close(); // trigger reconnect normal
            } else {
                connectWebSocket();
            }
        });

        // Event listener untuk menangani klik pada tombol konfirmasi logout
        $('#logoutRefreshBtn').on('click', function(e) {
            e.preventDefault();
            $('#refreshModal').modal('hide');
            window.location.href = '<?= base_url('/logout'); ?>'; // Redirect ke halaman logout
        });

        $('#refreshModal').on('hidden.bs.modal', function() {
            // Jika sedang menghitung mundur, batalkan
            if (countdownTimer !== null) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                $('#refreshSubmessage').html('Mencoba menghubungkan ulang dalam 5 detik');
            }
        });

        connectWebSocket();
        // Panggil fungsi untuk mengambil data pasien saat dokumen siap
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>