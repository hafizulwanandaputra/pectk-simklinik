<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="spinner-border spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-3 px-md-4">
    <div class="d-flex justify-content-start align-items-start pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 me-3"><i class="fa-regular fa-face-smile-beam"></i></h1>
        <h1 class="h2"><?= $txtgreeting . ', ' . session()->get('fullname') . '!'; ?></h1>
    </div>
    <div class="row row-cols-1 <?= (session()->get('role') == "Master Admin") ? 'row-cols-sm-2' : 'row-cols-sm-3'; ?> g-3 mb-3">
        <div class="col">
            <div class="card text-bg-secondary mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Menu Makanan</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"><?= number_format($totalmenu, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-bg-primary mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Permintaan</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"><?= number_format($totalpermintaan, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-bg-success mb-3 w-100 rounded-3">
                <div class="card-header w-100 text-truncate bg-gradient">Petugas Gizi</div>
                <div class="card-body">
                    <h5 class="display-5 fw-medium date"><?= number_format($totalpetugas, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <?php if (session()->get('role') == "Master Admin") : ?>
            <div class="col">
                <div class="card text-bg-danger mb-3 w-100 rounded-3">
                    <div class="card-header w-100 text-truncate bg-gradient">Admin</div>
                    <div class="card-body">
                        <h5 class="display-5 fw-medium date"><?= number_format($totaladmin, 0, ',', '.'); ?></h5>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="row row-cols-1 g-3 mb-3">
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Permintaan Pasien per Bulan</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="permintaanperbulangraph"></canvas>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Jumlah Permintaan berdasarkan Menu Makanan</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="permintaangraph"></canvas>
                </div>
            </fieldset>
        </div>
        <div class="col">
            <fieldset class="border rounded-3 px-2 py-0 mb-3">
                <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Grafik Jumlah Menu Makanan berdasarkan Petugas Gizi</legend>
                <div style="width: 100% !important;height: 400px !important;">
                    <canvas id="petugasgraph"></canvas>
                </div>
            </fieldset>
        </div>
    </div>
</main>
<?= $this->endSection(); ?>
<?= $this->section('javascript'); ?>
<script>
    $(document).ready(function() {
        $('#loadingSpinner').hide();
    });
</script>
<?= $this->endSection(); ?>
<?= $this->section('chartjs'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js" integrity="sha512-SIMGYRUjwY8+gKg7nn9EItdD8LCADSDfJNutF9TPrvEo86sQmFMh6MyralfIyhADlajSxqc7G0gs7+MwWF/ogQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
        const isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;

        const colorSettings = {
            color: isDarkMode ? "#ADBABD" : "#000000",
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

            // Update doughnut chart legend
            if (chart.config.type === 'doughnut' && chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels.color = colorSettings.color;
            }

            // Redraw the chart with updated settings
            chart.update();
        });
    }
    Chart.defaults.font.family = 'Geist, system-ui, -apple-system, BlinkMacSystemFont, "Noto Sans", "Noto Sans Arabic", "Noto Color Emoji", sans-serif';
    const data_permintaangraph = [];
    const label_permintaangraph = [];
    const data_petugasgraph = [];
    const label_petugasgraph = [];
    const data_permintaanperbulangraph = [];
    const label_permintaanperbulangraph = [];
    <?php foreach ($permintaangraph->getResult() as $key => $permintaangraph) : ?>
        data_permintaangraph.push(<?= $permintaangraph->jumlah; ?>);
        label_permintaangraph.push('<?= $permintaangraph->nama_menu; ?>');
    <?php endforeach; ?>
    <?php foreach ($petugasgraph->getResult() as $key => $petugasgraph) : ?>
        data_petugasgraph.push(<?= $petugasgraph->jumlah_menu; ?>);
        label_petugasgraph.push('<?= $petugasgraph->nama_petugas; ?>');
    <?php endforeach; ?>
    <?php foreach ($permintaanperbulangraph->getResult() as $key => $permintaanperbulangraph) : ?>
        data_permintaanperbulangraph.push(<?= $permintaanperbulangraph->jumlah_permintaan; ?>);
        label_permintaanperbulangraph.push('<?= $permintaanperbulangraph->bulan; ?>');
    <?php endforeach; ?>

    var data_content_permintaangraph = {
        labels: label_permintaangraph,
        datasets: [{
            label: 'Jumlah Permintaan',
            borderWidth: 0,
            data: data_permintaangraph
        }]
    }

    var data_content_petugasgraph = {
        labels: label_petugasgraph,
        datasets: [{
            label: 'Jumlah Menu Makanan',
            borderWidth: 0,
            data: data_petugasgraph
        }]
    }

    var data_content_permintaanperbulangraph = {
        labels: label_permintaanperbulangraph,
        datasets: [{
            label: 'Jumlah Permintaan',
            borderWidth: 2,
            pointStyle: 'rectRot',
            fill: true,
            data: data_permintaanperbulangraph
        }]
    }

    var chart_permintaangraph = createChart(document.getElementById('permintaangraph').getContext('2d'), {
        type: 'doughnut',
        data: data_content_permintaangraph,
        options: {
            layout: {
                padding: {
                    bottom: 10,
                    top: 10
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            locale: 'id',
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    })

    var chart_petugasgraph = createChart(document.getElementById('petugasgraph').getContext('2d'), {
        type: 'doughnut',
        data: data_content_petugasgraph,
        options: {
            layout: {
                padding: {
                    bottom: 10,
                    top: 10
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            locale: 'id',
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    })

    var chart_permintaanperbulangraph = createChart(document.getElementById('permintaanperbulangraph').getContext('2d'), {
        type: 'line',
        data: data_content_permintaanperbulangraph,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            locale: 'id-ID',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Jumlah Permintaan'
                    }
                },
                y: {
                    beginAtZero: true,
                }
            },
            scale: {
                ticks: {
                    precision: 0
                }
            }
        }
    })

    // Initial setup
    updateChartOptions();

    // Watch for changes in color scheme preference
    const mediaQueryList = window.matchMedia("(prefers-color-scheme: dark)");
    mediaQueryList.addEventListener("change", () => {
        updateChartOptions();
    });
</script>
<?= $this->endSection(); ?>