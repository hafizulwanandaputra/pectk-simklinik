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
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker" || session()->get('role') == "Dokter") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Farmasi</legend>
            <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                <?php if (session()->get('role') != "Dokter") : ?>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Supplier</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_supplier ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_obat ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat Masuk yang Belum Diterima</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_pembelian_obat_blm_diterima ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Obat Masuk yang Sudah Diterima</div>
                            <div class="card-body">
                                <h5 class="display-5 fw-medium date mb-0"><?= $total_pembelian_obat_sdh_diterima ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col">
                    <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Resep yang Belum Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_resep_blm_status ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Resep yang Sudah Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_resep_sdh_status ?></h5>
                        </div>
                    </div>
                </div>
                <?php if (session()->get('role') != "Dokter") : ?>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Resep Menurut Dokter</div>
                            <div class="card-body">
                                <div style="width: 100% !important;height: 400px !important;">
                                    <canvas id="resepbydoktergraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-body-tertiary w-100 rounded-3">
                            <div class="card-header w-100 text-truncate">Resep Per Bulan</div>
                            <div class="card-body">
                                <div style="width: 100% !important;height: 400px !important;">
                                    <canvas id="resepgraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Kasir</legend>
            <div class="row row-cols-1 row-cols-lg-2 g-2 mb-2">
                <div class="col">
                    <div class="card bg-danger-subtle text-danger-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Transaksi yang Belum Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_transaksi_blm_lunas ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-success-subtle text-success-emphasis w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Transaksi yang Sudah Diproses</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_transaksi_sdh_lunas ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Transaksi Per Bulan</div>
                        <div class="card-body">
                            <div style="width: 100% !important;height: 400px !important;">
                                <canvas id="transaksiperbulangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card bg-body-tertiary w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Pemasukan Per Bulan</div>
                        <div class="card-body">
                            <div style="width: 100% !important;height: 400px !important;">
                                <canvas id="pemasukanperbulangraph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin") : ?>
        <fieldset class="border rounded-3 px-2 py-0 mb-3">
            <legend class="float-none w-auto mb-0 px-1 fs-6 fw-bold">Admin</legend>
            <div class="row row-cols-1 g-2 mb-2">
                <div class="col">
                    <div class="card bg-body-tertiary w-100 rounded-3">
                        <div class="card-header w-100 text-truncate">Pengguna</div>
                        <div class="card-body">
                            <h5 class="display-5 fw-medium date mb-0"><?= $total_user ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
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
    Chart.defaults.font.family = '"Helvetica Neue", Helvetica, Arial, "Liberation Sans", sans-serif';
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker") : ?>
        const data_resepbydoktergraph = [];
        const label_resepbydoktergraph = [];
        const data_resepgraph = [];
        const label_resepgraph = [];
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        const data_transaksiperbulangraph = [];
        const label_transaksiperbulangraph = [];
        const data_pemasukanperbulangraph = [];
        const label_pemasukanperbulangraph = [];
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker") : ?>
        <?php foreach ($resepbydoktergraph->getResult() as $key => $resepbydoktergraph) : ?>
            data_resepbydoktergraph.push(<?= $resepbydoktergraph->jumlah; ?>);
            label_resepbydoktergraph.push('<?= $resepbydoktergraph->dokter; ?>');
        <?php endforeach; ?>
        <?php foreach ($resepgraph->getResult() as $key => $resepgraph) : ?>
            data_resepgraph.push(<?= $resepgraph->total_resep; ?>);
            label_resepgraph.push('<?= $resepgraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        <?php foreach ($transaksiperbulangraph->getResult() as $key => $transaksiperbulangraph) : ?>
            data_transaksiperbulangraph.push(<?= $transaksiperbulangraph->total_transaksi; ?>);
            label_transaksiperbulangraph.push('<?= $transaksiperbulangraph->bulan; ?>');
        <?php endforeach; ?>
        <?php foreach ($pemasukanperbulangraph->getResult() as $key => $pemasukanperbulangraph) : ?>
            data_pemasukanperbulangraph.push(<?= $pemasukanperbulangraph->total_pemasukan; ?>);
            label_pemasukanperbulangraph.push('<?= $pemasukanperbulangraph->bulan; ?>');
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker") : ?>
        var data_content_resepbydoktergraph = {
            labels: label_resepbydoktergraph,
            datasets: [{
                label: 'Resep Menurut Dokter',
                borderWidth: 2,
                pointStyle: 'rectRot',
                fill: true,
                data: data_resepbydoktergraph
            }]
        }
        var data_content_resepgraph = {
            labels: label_resepgraph,
            datasets: [{
                label: 'Resep Per Bulan',
                borderWidth: 2,
                pointStyle: 'rectRot',
                fill: true,
                data: data_resepgraph
            }]
        }
    <?php endif; ?>
    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Kasir") : ?>
        var data_content_transaksiperbulangraph = {
            labels: label_transaksiperbulangraph,
            datasets: [{
                label: 'Transaksi Per Bulan',
                borderWidth: 2,
                pointStyle: 'rectRot',
                fill: true,
                data: data_transaksiperbulangraph
            }]
        }
        var data_content_pemasukanperbulangraph = {
            labels: label_pemasukanperbulangraph,
            datasets: [{
                label: 'Pemasukan Per Bulan',
                borderWidth: 2,
                pointStyle: 'rectRot',
                fill: true,
                data: data_pemasukanperbulangraph
            }]
        }
    <?php endif; ?>

    <?php if (session()->get('role') == "Admin" || session()->get('role') == "Apoteker") : ?>
        var chart_resepbydoktergraph = createChart(document.getElementById('resepbydoktergraph').getContext('2d'), {
            type: 'pie',
            data: data_content_resepbydoktergraph,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                locale: 'id-ID',
                plugins: {
                    legend: {
                        display: false
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
        var chart_transaksiperbulangraph = createChart(document.getElementById('transaksiperbulangraph').getContext('2d'), {
            type: 'line',
            data: data_content_transaksiperbulangraph,
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