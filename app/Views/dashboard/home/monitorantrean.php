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
                                <h1 class="fw-medium mb-0"></h1>
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
                <div class="card shadow-sm mt-lg-3">
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
                                <div class="fw-bold fs-4">${antrean.kode_antrean}-${antrean.nomor_antrean}</div>
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
        fetchAntrean();
        updateDateTime(); // Jalankan sekali saat load
        setInterval(updateDateTime, 1000); // Update tiap 1 detik
    });
</script>
<?= $this->endSection(); ?>