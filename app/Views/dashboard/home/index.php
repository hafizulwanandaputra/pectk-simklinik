<?php
$db = db_connect();
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<style>
    .ratio-onecol {
        --bs-aspect-ratio: 33%;
    }

    @media (max-width: 991.98px) {
        .ratio-onecol {
            --bs-aspect-ratio: 75%;
        }
    }
</style>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm mx-2" role="status" style="min-width: 1rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3">
    <div class="no-fluid-content">
        <div class="d-flex justify-content-start align-items-start pt-3">
            <h1 class="h2 mb-0 me-3"><i class="fa-regular fa-face-smile-beam"></i></h1>
            <h1 class="h2 mb-0"><?= $txtgreeting . ', ' . session()->get('fullname') . '!'; ?></h1>
        </div>
        <hr>
        <?php if (session()->get('role') == "Admin") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Admin</div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pengguna Keseluruhan</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Pengguna Nonaktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_inactive, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Pengguna Aktif</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_user_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Sesi Keseluruhan Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Sesi Kedaluwarsa Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_expired, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Sesi Aktif Selain Anda</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_sessions_active, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Pasien dan Rawat Jalan</div>
                <div class="row row-cols-1 row-cols-lg-3 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Total Pasien</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_pasien, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Pasien yang Batal Berobat Hari Ini</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rawatjalan_batal, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Pasien yang Berobat Hari Ini</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_rawatjalan, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Agama Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="agamagraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Jenis Kelamin Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="jeniskelamingraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Provinsi Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="persebaranprovinsigraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kabupaten/Kota Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="persebarankabupatengraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kecamatan Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="persebarankecamatangraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Persebaran Kelurahan Pasien</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="persebarankelurahangraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Rawat Jalan Per Bulan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="rawatjalangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Farmasi</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <?php if (session()->get('role') != "Dokter") : ?>
                        <div class="col">
                            <div class="card bg-body-tertiary w-100  shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Supplier</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_supplier, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-body-tertiary w-100  shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Obat</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_obat, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Obat Masuk yang Belum Diterima</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_pembelian_obat_blm_diterima, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                                <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Obat Masuk yang Sudah Diterima</div>
                                <div class="card-body py-2 px-3">
                                    <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_pembelian_obat_sdh_diterima, 0, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Resep yang Belum Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_resep_blm_status, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Resep yang Sudah Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_resep_sdh_status, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="resepbydoktergraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Per Bulan Menurut Dokter</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="resepgraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Resep Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="resepallgraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
            <div class="mb-3">
                <div class="fw-bold mb-2 border-bottom">Kasir</div>
                <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                    <div class="col">
                        <div class="card bg-danger-subtle border-danger-subtle text-danger-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-danger-subtle w-100 text-truncate">Transaksi yang Belum Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_transaksi_blm_lunas, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle border-success-subtle text-success-emphasis w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-success-subtle w-100 text-truncate">Transaksi yang Sudah Diproses</div>
                            <div class="card-body py-2 px-3">
                                <h5 class="display-6 fw-medium date mb-0"><?= number_format($total_transaksi_sdh_lunas, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Menurut Petugas Kasir</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="transaksibykasirgraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100  shadow-sm">
                            <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Per Bulan Menurut Petugas Kasir</div>
                            <div class="card-body py-2 px-3">
                                <div class="ratio ratio-4x3 w-100">
                                    <canvas id="transaksiperbulangraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Transaksi Per Bulan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="transaksiperbulanallgraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-primary-subtle border-primary-subtle text-primary-emphasis w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 border-primary-subtle w-100 text-truncate">Jumlah Pemasukan Keseluruhan</div>
                        <div class="card-body py-2 px-3">
                            <h5 class="display-6 fw-medium date mb-0"><?= 'Rp' . number_format($total_pemasukan, 0, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="card bg-body-tertiary w-100  shadow-sm">
                        <div style="font-size: 0.9em;" class="card-header py-1 px-3 w-100 text-truncate">Pemasukan Per Bulan</div>
                        <div class="card-body py-2 px-3">
                            <div class="ratio ratio-onecol w-100">
                                <canvas id="pemasukanperbulangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        // Menyembunyikan spinner loading saat dokumen sudah siap
        $('#loadingSpinner').hide(); // Menyembunyikan elemen spinner loading
    });
</script>
<?= $this->endSection(); ?>
<?= $this->section('chartjs'); ?>
<script>
    // Array to keep track of chart instances
    const chartInstances = [];

    // Function to initialize a chart and add it to the instances array
    function createChart(ctx, config) {
        const chart = new Chart(ctx, config);
        chartInstances.push(chart);
        return chart;
    }

    // Function to update chart configurations based on the color scheme
    function updateChartOptions() {
        // Cek apakah data-bs-theme ada dan bernilai "dark"
        const themeAttribute = document.documentElement.getAttribute("data-bs-theme");
        const isDarkMode = themeAttribute === "dark";

        const colorSettings = {
            color: isDarkMode ? "#FFFFFF" : "#000000",
            borderColor: isDarkMode ? "rgba(255,255,255,0.1)" : "rgba(0,0,0,0.1)",
            backgroundColor: isDarkMode ? "rgba(255,255,0,0.1)" : "rgba(0,255,0,0.1)",
            lineBorderColor: isDarkMode ? "rgba(255,255,0,0.4)" : "rgba(0,255,0,0.4)",
            gridColor: isDarkMode ? "rgba(255,255,255,0.2)" : "rgba(0,0,0,0.2)"
        };

        chartInstances.forEach(chart => {
            if (chart.options.scales) {
                // Update X-axis
                if (chart.options.scales.x) {
                    if (chart.options.scales.x.ticks) {
                        chart.options.scales.x.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.title) {
                        chart.options.scales.x.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.x.grid) {
                        chart.options.scales.x.grid.color = colorSettings.gridColor;
                    }
                }

                // Update Y-axis
                if (chart.options.scales.y) {
                    if (chart.options.scales.y.ticks) {
                        chart.options.scales.y.ticks.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.title) {
                        chart.options.scales.y.title.color = colorSettings.color;
                    }
                    if (chart.options.scales.y.grid) {
                        chart.options.scales.y.grid.color = colorSettings.gridColor;
                    }
                }
            }

            // Update line chart specific settings
            if (chart.options.elements && chart.options.elements.line) {
                chart.options.elements.line.borderColor = colorSettings.lineBorderColor;
            }

            // Update doughnut and pie chart legend
            if ((chart.config.type === 'doughnut' || chart.config.type === 'pie') && chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels.color = colorSettings.color;
            }

            // Redraw the chart with updated settings
            chart.update();
        });
    }
    Chart.defaults.font.family = '"Helvetica Neue", Helvetica, Arial, Arimo, "Liberation Sans", sans-serif';

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        const data_agamagraph = [];
        const label_agamagraph = [];
        const data_jeniskelamingraph = [];
        const label_jeniskelamingraph = [];
        const data_persebaranprovinsigraph = [];
        const label_persebaranprovinsigraph = [];
        const data_persebarankabupatengraph = [];
        const label_persebarankabupatengraph = [];
        const data_persebarankecamatangraph = [];
        const label_persebarankecamatangraph = [];
        const data_persebarankelurahangraph = [];
        const label_persebarankelurahangraph = [];
        const data_rawatjalangraph = [];
        const label_rawatjalangraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        const data_resepbydoktergraph = [];
        const label_resepbydoktergraph = [];
        const data_resepallgraph = [];
        const label_resepallgraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        const data_transaksibykasirgraph = [];
        const label_transaksibykasirgraph = [];
        const data_transaksiperbulanallgraph = [];
        const label_transaksiperbulanallgraph = [];
        const data_pemasukanperbulangraph = [];
        const label_pemasukanperbulangraph = [];
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        <?php foreach ($agamagraph->getResult() as $key => $agamagraph) :
            // Query untuk mencocokkan ID agama dengan nama agama
            $agamaId = $agamagraph->agama;
            $query = $db->table('master_agama')
                ->select('agamaNama')
                ->where('agamaId', $agamaId)
                ->get();

            // Ambil nama agama
            $agamaNama = $query->getRow();

            if ($agamaNama) {
                // Ambil nama kelurahan jika ditemukan
                $agamaNama = $agamaNama->agamaNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $agamaNama = 'Tidak Ada';
            } ?>
            data_agamagraph.push(<?= $agamagraph->total_agama; ?>);
            label_agamagraph.push('<?= htmlspecialchars($agamaNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($jeniskelamingraph->getResult() as $key => $jeniskelamingraph) : ?>
            data_jeniskelamingraph.push(<?= $jeniskelamingraph->total_jeniskelamin; ?>);
            <?php
            $jenisKelamin = $jeniskelamingraph->jenis_kelamin;
            if ($jenisKelamin == 'L') {
                $jenisKelamin = 'Laki-Laki';
            } elseif ($jenisKelamin == 'P') {
                $jenisKelamin = 'Perempuan';
            } elseif ($jenisKelamin === NULL) {
                $jenisKelamin = 'Tidak Ada';
            }
            ?>
            label_jeniskelamingraph.push('<?= $jenisKelamin; ?>');
        <?php endforeach; ?>
        <?php foreach ($persebaranprovinsigraph->getResult() as $key => $persebaranprovinsigraph) :
            // Query untuk mencocokkan ID provinsi dengan nama provinsi
            $provinsiId = $persebaranprovinsigraph->provinsi;
            $query = $db->table('master_provinsi')
                ->select('provinsiNama')
                ->where('provinsiId', $provinsiId)
                ->get();

            // Ambil nama provinsi
            $provinsiNama = $query->getRow();

            if ($provinsiNama) {
                // Ambil nama kelurahan jika ditemukan
                $provinsiNama = $provinsiNama->provinsiNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $provinsiNama = 'Tidak Ada';
            } ?>
            data_persebaranprovinsigraph.push(<?= $persebaranprovinsigraph->total_provinsi; ?>);
            label_persebaranprovinsigraph.push('<?= htmlspecialchars($provinsiNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($persebarankabupatengraph->getResult() as $key => $persebarankabupatengraph) :
            // Query untuk mencocokkan ID kabupaten dengan nama kabupaten
            $kabupatenId = $persebarankabupatengraph->kabupaten;
            $query = $db->table('master_kabupaten')
                ->select('kabupatenNama')
                ->where('kabupatenId', $kabupatenId)
                ->get();

            // Ambil nama kabupaten
            $kabupatenNama = $query->getRow();

            if ($kabupatenNama) {
                // Ambil nama kelurahan jika ditemukan
                $kabupatenNama = $kabupatenNama->kabupatenNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $kabupatenNama = 'Tidak Ada';
            } ?>
            data_persebarankabupatengraph.push(<?= $persebarankabupatengraph->total_kabupaten; ?>);
            label_persebarankabupatengraph.push('<?= htmlspecialchars($kabupatenNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($persebarankecamatangraph->getResult() as $key => $persebarankecamatangraph) :
            // Query untuk mencocokkan ID kecamatan dengan nama kecamatan
            $kecamatanId = $persebarankecamatangraph->kecamatan;
            $query = $db->table('master_kecamatan')
                ->select('kecamatanNama')
                ->where('kecamatanId', $kecamatanId)
                ->get();

            // Ambil nama kecamatan
            $kecamatanNama = $query->getRow();

            if ($kecamatanNama) {
                // Ambil nama kelurahan jika ditemukan
                $kecamatanNama = $kecamatanNama->kecamatanNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $kecamatanNama = 'Tidak Ada';
            } ?>
            data_persebarankecamatangraph.push(<?= $persebarankecamatangraph->total_kecamatan; ?>);
            label_persebarankecamatangraph.push('<?= htmlspecialchars($kecamatanNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($persebarankelurahangraph->getResult() as $key => $persebarankelurahangraph) :
            // Query untuk mencocokkan ID kelurahan dengan nama kelurahan
            $kelurahanId = $persebarankelurahangraph->kelurahan;
            $query = $db->table('master_kelurahan')
                ->select('kelurahanNama')
                ->where('kelurahanId', $kelurahanId)
                ->get();

            // Ambil nama kelurahan
            $kelurahanNama = $query->getRow();

            if ($kelurahanNama) {
                // Ambil nama kelurahan jika ditemukan
                $kelurahanNama = $kelurahanNama->kelurahanNama;
            } else {
                // Jika tidak ditemukan, beri nilai default
                $kelurahanNama = 'Tidak Ada';
            } ?>
            data_persebarankelurahangraph.push(<?= $persebarankelurahangraph->total_kelurahan; ?>);
            label_persebarankelurahangraph.push('<?= htmlspecialchars($kelurahanNama, ENT_QUOTES, 'UTF-8'); ?>');
        <?php endforeach; ?>
        <?php foreach ($rawatjalangraph->getResult() as $key => $rawatjalangraph) : ?>
            data_rawatjalangraph.push(<?= $rawatjalangraph->total_rajal; ?>);
            label_rawatjalangraph.push('<?= $rawatjalangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        <?php foreach ($resepbydoktergraph->getResult() as $key => $resepbydoktergraph) : ?>
            data_resepbydoktergraph.push(<?= $resepbydoktergraph->jumlah; ?>);
            label_resepbydoktergraph.push('<?= $resepbydoktergraph->dokter; ?>');
        <?php endforeach; ?>
        <?php foreach ($resepallgraph->getResult() as $key => $resepallgraph) : ?>
            data_resepallgraph.push(<?= $resepallgraph->total_resep; ?>);
            label_resepallgraph.push('<?= $resepallgraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        <?php foreach ($transaksibykasirgraph->getResult() as $key => $transaksibykasirgraph) : ?>
            data_transaksibykasirgraph.push(<?= $transaksibykasirgraph->jumlah; ?>);
            label_transaksibykasirgraph.push('<?= $transaksibykasirgraph->kasir; ?>');
        <?php endforeach; ?>
        <?php foreach ($transaksiperbulanallgraph->getResult() as $key => $transaksiperbulanallgraph) : ?>
            data_transaksiperbulanallgraph.push(<?= $transaksiperbulanallgraph->total_transaksi; ?>);
            label_transaksiperbulanallgraph.push('<?= $transaksiperbulanallgraph->bulan; ?>');
        <?php endforeach; ?>
        <?php foreach ($pemasukanperbulangraph->getResult() as $key => $pemasukanperbulangraph) : ?>
            data_pemasukanperbulangraph.push(<?= $pemasukanperbulangraph->total_pemasukan; ?>);
            label_pemasukanperbulangraph.push('<?= $pemasukanperbulangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        var data_content_agamagraph = {
            labels: label_agamagraph,
            datasets: [{
                label: 'Total Pasien',
                borderWidth: 2,
                borderRadius: 10,
                fill: true,
                data: data_agamagraph
            }]
        }
        var data_content_jeniskelamingraph = {
            labels: label_jeniskelamingraph,
            datasets: [{
                label: 'Total Pasien',
                borderWidth: 2,
                borderRadius: 10,
                fill: true,
                data: data_jeniskelamingraph
            }]
        }
        var data_content_persebaranprovinsigraph = {
            labels: label_persebaranprovinsigraph,
            datasets: [{
                label: 'Total Pasien',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_persebaranprovinsigraph
            }]
        }
        var data_content_persebarankabupatengraph = {
            labels: label_persebarankabupatengraph,
            datasets: [{
                label: 'Total Pasien',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_persebarankabupatengraph
            }]
        }
        var data_content_persebarankecamatangraph = {
            labels: label_persebarankecamatangraph,
            datasets: [{
                label: 'Total Pasien',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_persebarankecamatangraph
            }]
        }
        var data_content_persebarankelurahangraph = {
            labels: label_persebarankelurahangraph,
            datasets: [{
                label: 'Total Pasien',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_persebarankelurahangraph
            }]
        }
        var data_content_rawatjalangraph = {
            labels: label_rawatjalangraph,
            datasets: [{
                label: 'Total Rawat Jalan',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_rawatjalangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        var data_content_resepbydoktergraph = {
            labels: label_resepbydoktergraph,
            datasets: [{
                label: 'Total Resep',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_resepbydoktergraph
            }]
        }
        var data_content_resepgraph = {
            labels: <?= $labels_resep ?>,
            datasets: <?= $datasets_resep ?>
        }
        var data_content_resepallgraph = {
            labels: label_resepallgraph,
            datasets: [{
                label: 'Total Resep',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_resepallgraph
            }]
        }
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        var data_content_transaksibykasirgraph = {
            labels: label_transaksibykasirgraph,
            datasets: [{
                label: 'Total Transaksi',
                pointStyle: 'circle',
                pointRadius: 6,
                pointHoverRadius: 12,
                borderWidth: 0,
                fill: true,
                data: data_transaksibykasirgraph
            }]
        }
        var data_content_transaksiperbulanallgraph = {
            labels: label_transaksiperbulanallgraph,
            datasets: [{
                label: 'Total Transaksi',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_transaksiperbulanallgraph
            }]
        }
        var data_content_transaksiperbulangraph = {
            labels: <?= $labels_transaksi ?>,
            datasets: <?= $datasets_transaksi ?>
        }
        var data_content_pemasukanperbulangraph = {
            labels: label_pemasukanperbulangraph,
            datasets: [{
                label: 'Total Pemasukan (Rp)',
                pointRadius: 6,
                pointHoverRadius: 12,
                fill: false,
                data: data_pemasukanperbulangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") : ?>
        var chart_agamagraph = createChart(document.getElementById('agamagraph').getContext('2d'), {
            type: 'bar',
            data: data_content_agamagraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Agama'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pasien'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_jeniskelamingraph = createChart(document.getElementById('jeniskelamingraph').getContext('2d'), {
            type: 'bar',
            data: data_content_jeniskelamingraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Jenis Kelamin'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pasien'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_persebaranprovinsigraph = createChart(document.getElementById('persebaranprovinsigraph').getContext('2d'), {
            type: 'doughnut',
            data: data_content_persebaranprovinsigraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: false
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_persebarankabupatengraph = createChart(document.getElementById('persebarankabupatengraph').getContext('2d'), {
            type: 'doughnut',
            data: data_content_persebarankabupatengraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: false
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_persebarankecamatangraph = createChart(document.getElementById('persebarankecamatangraph').getContext('2d'), {
            type: 'doughnut',
            data: data_content_persebarankecamatangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: false
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_persebarankelurahangraph = createChart(document.getElementById('persebarankelurahangraph').getContext('2d'), {
            type: 'doughnut',
            data: data_content_persebarankelurahangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: false
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_rawatjalangraph = createChart(document.getElementById('rawatjalangraph').getContext('2d'), {
            type: 'line',
            data: data_content_rawatjalangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Rawat Jalan'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        var chart_resepbydoktergraph = createChart(document.getElementById('resepbydoktergraph').getContext('2d'), {
            type: 'pie',
            data: data_content_resepbydoktergraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_resepgraph = createChart(document.getElementById('resepgraph').getContext('2d'), {
            type: 'line',
            data: data_content_resepgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Resep yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_resepallgraph = createChart(document.getElementById('resepallgraph').getContext('2d'), {
            type: 'line',
            data: data_content_resepallgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Resep yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        var chart_transaksibykasirgraph = createChart(document.getElementById('transaksibykasirgraph').getContext('2d'), {
            type: 'pie',
            data: data_content_transaksibykasirgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    filler: {
                        drawTime: 'beforeDraw'
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_transaksiperbulangraph = createChart(document.getElementById('transaksiperbulangraph').getContext('2d'), {
            type: 'line',
            data: data_content_transaksiperbulangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Transaksi yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_transaksiperbulanallgraph = createChart(document.getElementById('transaksiperbulanallgraph').getContext('2d'), {
            type: 'line',
            data: data_content_transaksiperbulanallgraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Transaksi yang Diproses'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
        var chart_pemasukanperbulangraph = createChart(document.getElementById('pemasukanperbulangraph').getContext('2d'), {
            type: 'line',
            data: data_content_pemasukanperbulangraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Pemasukan (Rp)'
                        }
                    }
                },
                scale: {
                    ticks: {
                        precision: 0
                    }
                }
            }
        })
    <?php endif; ?>

    // Initial setup
    updateChartOptions();

    // Watch for changes in color scheme preference
    const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQueryList.addEventListener("change", () => {
        updateChartOptions();
    });
</script>
<?= $this->endSection(); ?>