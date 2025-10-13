<?php
$uri = service('uri'); // Load the URI service
$activeSegment = $uri->getSegment(1); // Get the first segment
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <link rel="manifest" href="<?= base_url(); ?>/manifest.json">
    <meta name="theme-color" content="#d1e7dd">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Favicon -->
    <link href="<?= base_url(); ?>favicon.png" rel="icon" />
    <link href="<?= base_url(); ?>favicon.png" rel="apple-touch-icon" />
    <!-- Akhir dari Favicon -->
    <link href="<?= base_url(); ?>assets/css/dashboard/dashboard.css" rel="stylesheet">
    <?= $this->include('main-css/index'); ?>
    <link href="<?= base_url(); ?>assets_public/css/JawiDubai.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/fonts/IosevkaHwpMono/IosevkaHwpMono.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/fontawesome/css/all.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/localizedFormat.js"></script>
    <script>
        flatpickr.localize(flatpickr.l10ns.id);
    </script>
    <script>
        (() => {
            'use strict'

            const getStoredTheme = () => localStorage.getItem('theme');
            const setStoredTheme = theme => localStorage.setItem('theme', theme);

            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) {
                    return storedTheme;
                }

                return 'auto';
            };

            const setTheme = theme => {
                let themeColor = '';
                let isDarkMode = theme === 'auto' ? window.matchMedia('(prefers-color-scheme: dark)').matches : theme === 'dark';
                let $darkThemeLink = $('<link>', {
                    rel: 'stylesheet',
                    type: 'text/css',
                    href: 'https://npmcdn.com/flatpickr/dist/themes/dark.css',
                    id: 'dark-theme-style' // ID untuk memudahkan penghapusan
                });

                if (isDarkMode) {
                    $('html').attr('data-bs-theme', 'dark');
                    themeColor = '#051b11';
                    if (!$('#dark-theme-style').length) {
                        $('head').append($darkThemeLink);
                    }
                } else {
                    $('html').attr('data-bs-theme', theme);
                    themeColor = '#d1e7dd';
                    $('#dark-theme-style').remove();
                }

                $('meta[name="theme-color"]').attr('content', themeColor);

                const colorSettings = {
                    color: isDarkMode ? "#FFFFFF" : "#000000",
                    borderColor: isDarkMode ? "rgba(255,255,255,0.1)" : "rgba(0,0,0,0.1)",
                    backgroundColor: isDarkMode ? "rgba(255,255,0,0.1)" : "rgba(0,255,0,0.1)",
                    lineBorderColor: isDarkMode ? "rgba(255,255,0,0.4)" : "rgba(0,255,0,0.4)",
                    gridColor: isDarkMode ? "rgba(255,255,255,0.2)" : "rgba(0,0,0,0.2)"
                };

                if (typeof chartInstances !== 'undefined') {
                    chartInstances.forEach(chart => {
                        if (chart.options.scales) {
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

                        if (chart.options.elements && chart.options.elements.line) {
                            chart.options.elements.line.borderColor = colorSettings.lineBorderColor;
                        }

                        if ((chart.config.type === 'doughnut' || chart.config.type === 'pie') && chart.options.plugins && chart.options.plugins.legend) {
                            chart.options.plugins.legend.labels.color = colorSettings.color;
                        }

                        chart.update();
                    });
                }
            };

            setTheme(getPreferredTheme());

            const showActiveTheme = (theme, focus = false) => {
                const themeSwitcher = $('#bd-theme');

                if (!themeSwitcher.length) {
                    return;
                }

                const themeSwitcherText = $('#bd-theme-text');
                const activeThemeIcon = $('.theme-icon-active use');
                const btnToActive = $(`[data-bs-theme-value="${theme}"]`);

                $('[data-bs-theme-value]').removeClass('active').attr('aria-pressed', 'false');
                btnToActive.addClass('active').attr('aria-pressed', 'true');

                if (focus) {
                    themeSwitcher.focus();
                }
            };

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme();
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme());
                }
            });

            $(document).ready(() => {
                showActiveTheme(getPreferredTheme());

                $('[data-bs-theme-value]').on('click', function() {
                    const theme = $(this).attr('data-bs-theme-value');
                    // Inisialisasi flatpickr untuk semua .month-picker
                    setStoredTheme(theme);
                    setTheme(theme);
                    showActiveTheme(theme, true);
                });
            });
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilter = document.getElementById('toggleFilter');
            const filterFields = document.getElementById('filterFields');
            const toggleStateKey = 'filterFieldsToggleState';

            if (!toggleFilter || !filterFields) {
                return; // Keluar jika elemen tidak ditemukan untuk mencegah error
            }

            // Fungsi untuk menyimpan status toggle di local storage
            function saveToggleState(state) {
                localStorage.setItem(toggleStateKey, state ? 'visible' : 'hidden');
            }

            // Fungsi untuk memuat status toggle dari local storage
            function loadToggleState() {
                return localStorage.getItem(toggleStateKey);
            }

            // Atur status awal berdasarkan local storage
            const initialState = loadToggleState();
            if (initialState === 'visible') {
                filterFields.style.display = 'block';
            } else {
                filterFields.style.display = 'none'; // Pastikan ada nilai default
            }

            // Event klik untuk toggle
            toggleFilter.addEventListener('click', function(event) {
                event.preventDefault();
                const isVisible = filterFields.style.display === 'block';
                filterFields.style.display = isVisible ? 'none' : 'block';
                saveToggleState(!isVisible);
            });
        });
    </script>
    <style>
        :root {
            --bs-font-monospace: "Iosevka HWP Mono Web", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --gold: #ffe680;
            /* Emas lebih terang */
            --silver: #bfbfbf;
            /* Perak lebih terang */
            --bronze: #e7bd98;
            /* Perunggu lebih terang */
        }

        /* Warna untuk tema gelap */
        [data-bs-theme="dark"] {
            --gold: #806600;
            /* Emas lebih gelap */
            --silver: #404040;
            /* Perak lebih gelap */
            --bronze: #673e18;
            /* Perunggu lebih gelap */
        }

        /* Terapkan ke elemen */
        .bg-gold {
            background-color: var(--gold) !important;
            color: var(--bs-body-color);
        }

        .bg-silver {
            background-color: var(--silver) !important;
            color: var(--bs-body-color);
        }

        .bg-bronze {
            background-color: var(--bronze) !important;
            color: var(--bs-body-color);
        }

        html,
        body,
        input,
        select,
        button {
            font-variant-numeric: proportional-nums;
        }

        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        input[type="time"],
        input[type="month"],
        input[type="week"] {
            font-variant-numeric: tabular-nums;
        }

        input[type="password"] {
            font-family: var(--bs-font-monospace);
        }

        .date {
            font-variant-numeric: tabular-nums;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            scroll-padding-top: 0.5rem;
        }

        .loading-spinner {
            fill: currentcolor;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Use viewport height to ensure full height */
        }

        .header {
            flex-shrink: 0;
            /* Prevent header from shrinking */
        }


        .main-content-wrapper {
            display: flex;
            flex: 1;
            /* Allows this container to grow and fill the remaining space */
            overflow: hidden;
            /* Prevents overflow from affecting the container's size */
            padding-left: calc(var(--bs-gutter-x) * .5);
            padding-right: calc(var(--bs-gutter-x) * .5);
        }

        .toast-container {
            padding-top: calc(3rem + 1rem) !important;
            right: 0 !important;
        }

        .sidebar {
            box-shadow: inset 0px 0 0 rgba(0, 0, 0, 0);
            border: 1px solid var(--bs-border-color);
            overflow: auto;
            border-radius: var(--bs-border-radius);
        }

        .main-content {
            flex: 1;
            overflow: auto;
        }

        .main-content-inside {
            margin-left: calc(0.5rem + 220px);
        }

        #sidebarMenu,
        #sidebarHeader {
            max-width: 220px;
            min-width: 220px;
        }

        .profilephotosidebar {
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

        .profilephotonavbar {
            min-height: 2rem;
            max-height: 2rem;
            min-width: 2rem;
            max-width: 2rem;
        }

        .profilephotosidebar svg {
            fill: var(--bs-body-color);
            /* Bootstrap white color */
        }

        div.dataTables_processing>div:last-child {
            display: none;
        }

        div.dataTables_wrapper div.dataTables_processing.card {
            position: fixed;
            margin: 0 !important;
            z-index: 999;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            --bs-bg-opacity: 1;
            backdrop-filter: none;
            background-color: rgba(var(--bs-body-bg-rgb), var(--bs-bg-opacity)) !important;
            border: 1px solid var(--bs-border-color-translucent);
            box-shadow: var(--bs-box-shadow) !important;
            border-radius: var(--bs-border-radius) !important;
        }

        table.dataTable {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .modal-body div.dataTables_wrapper div.dataTables_processing.card {
            background-color: rgba(var(--bs-body-bg-rgb), var(--bs-bg-opacity)) !important;
            --bs-bg-opacity: 1;
            backdrop-filter: none;
        }

        .two-column-list {
            columns: 2;
            /* Creates two columns */
            -webkit-columns: 2;
            /* For Safari and older browsers */
            -moz-columns: 2;
            /* For Firefox */
        }

        .two-column-list li {
            break-inside: avoid-column;
            /* Prevents items from breaking between columns */
            word-break: break-all;
        }

        .card {
            --bs-card-border-color: var(--bs-border-color);
        }

        @media (prefers-reduced-transparency: no-preference) {
            div.dataTables_wrapper div.dataTables_processing.card {
                --bs-bg-opacity: 0.75;
                backdrop-filter: blur(20px);
            }
        }

        .no-fluid-content {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
            width: 100%;
            padding-right: calc(var(--bs-gutter-x) * 0.5);
            padding-left: calc(var(--bs-gutter-x) * 0.5);
            margin-right: auto;
            margin-left: auto;
            max-width: 960px;
        }

        .no-caret::after {
            display: none;
            /* Hilangkan ikon panah ke bawah */
        }

        .kbd {
            border-radius: 4px !important;
        }

        @media (max-width: 767.98px) {
            .toast-container {
                padding-top: <?= (!(in_array(session()->get('role'), ['Satpam', 'Monitor Antrean']) && $activeSegment === 'home')) ? 'calc(6rem + 1rem)' : 'calc(3rem + 1rem)' ?> !important;
                transform: translateX(-50%) !important;
                left: 50% !important;
            }

            .main-content-inside {
                margin-left: 0;
            }

            .sidebar {
                top: 3rem;
                width: 100%;
            }

            #sidebarMenu2 {
                height: 100%;
                padding-top: 0;
            }

            #sidebarMenu {
                height: calc(100% - 7rem);
                max-width: calc(100% - 1rem);
                min-width: 0;
                opacity: 0;
                transition: opacity 0.25s ease-out, transform 0.25s ease-out;
                transform: translateY(-5%) scale(0.9);
            }

            #sidebarHeader {
                max-width: 100%;
                min-width: 0;
            }

            #sidebarMenu.show {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            @media (prefers-reduced-motion: reduce) {
                #sidebarMenu {
                    transition: none;
                }
            }
        }
    </style>
    <style>

    </style>
    <?= $this->include('spinner/spinner-css'); ?>
    <?= $this->renderSection('css'); ?>
</head>

<body class="bg-body-hwpweb user-select-none">
    <div class="wrapper">
        <!-- HEADER -->
        <header class="navbar sticky-top flex-md-nowrap p-0 shadow-sm bg-success-subtle text-success-emphasis border-bottom border-success-subtle header">
            <?php if (!(in_array(session()->get('role'), ['Satpam', 'Monitor Antrean']) && $activeSegment === 'home')) : ?>
                <div id="sidebarHeader" class="d-flex justify-content-center align-items-center mx-0 mx-md-2 px-3 py-md-1" style="min-height: 3rem; max-height: 3rem;">
                    <span class="navbar-brand mx-0 text-start text-md-center lh-sm d-flex justify-content-center align-items-center" style="font-size: 7.5pt;">
                        <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="24px">
                        <div class="ps-2 text-start text-success-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                    </span>
                </div>
                <button type="button" class="btn btn-outline-success bg-gradient d-md-none mx-3" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"><i class="fa-solid fa-bars"></i></button>
            <?php endif; ?>
            <div class="d-flex w-100 align-items-center text-truncate" style="min-height: 3rem; max-height: 3rem;">
                <div class="w-100 ps-3 pe-1 pe-lg-2 text-truncate" style="flex: 1; min-width: 0;">
                    <?= $this->renderSection('title'); ?>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="vr d-none d-lg-block border-success-subtle" style="height: 2.5rem;"></div>
                </div>
                <div class="me-3 ms-1 ms-lg-3">
                    <a href="#" class="d-flex align-items-center text-success-emphasis text-decoration-none" data-bs-toggle="offcanvas" data-bs-target="#userOffcanvas" role="button" aria-controls="userOffcanvas">
                        <div class="me-2 d-none d-lg-block text-end">
                            <div class="d-flex flex-column">
                                <div class="text-nowrap fw-medium lh-sm" style="font-size: 0.75em;"><?= session()->get('fullname') ?></div>
                                <div class="text-nowrap lh-sm" style="font-size: 0.7em;">@<?= session()->get('username') ?> • <span class="date"><?= $_SERVER['REMOTE_ADDR'] ?></span></div>
                            </div>
                        </div>
                        <div class="rounded-pill bg-body profilephotosidebar d-flex justify-content-center align-items-center profilephotonavbar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5rem" height="1.5rem" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14 14s-1-4-6-4-6 4-6 4 1 0 6 0 6 0 6 0z" />
                            </svg>
                        </div>
                    </a>
                    <div class="offcanvas offcanvas-end bg-body-tertiary shadow-sm transparent-blur" tabindex="-1" id="userOffcanvas" aria-labelledby="userOffcanvasLabel">
                        <div class="offcanvas-header pt-0 pb-0 d-flex justify-content-between align-items-center" style="min-height: 3rem; max-height: 3rem;">
                            <div>
                                <span class="navbar-brand mx-0 text-start text-md-center lh-sm d-flex justify-content-center align-items-center" style="font-size: 7.5pt;">
                                    <img src="<?= base_url('/assets/images/pec-klinik-logo.png'); ?>" alt="KLINIK MATA PECTK" height="24px">
                                    <div class="ps-2 text-start text-success-emphasis fw-bold">PADANG EYE CENTER<br>TELUK KUANTAN</div>
                                </span>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="dropdown">
                                    <button class="btn btn-outline-success bg-gradient dropdown-toggle" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (auto)">
                                        <i class="fa-solid fa-palette"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm dropdown-menu-end bg-body-tertiary transparent-blur" aria-labelledby="bd-theme-text">
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-theme-value="light" aria-pressed="false">
                                                Terang
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-theme-value="dark" aria-pressed="false">
                                                Gelap
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item active" data-bs-theme-value="auto" aria-pressed="true">
                                                Sistem
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <button id="closeOffcanvasBtn" type="button" class="btn btn-success bg-gradient ms-2" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-angles-right"></i></button>
                            </div>
                        </div>
                        <div class="offcanvas-body p-1">
                            <div class="d-flex justify-content-center">
                                <div class="rounded-pill bg-body profilephotosidebar m-2 d-flex justify-content-center align-items-center" style="width: 96px; height: 96px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM14 14s-1-4-6-4-6 4-6 4 1 0 6 0 6 0 6 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center w-100 lh-sm mb-3">
                                <span>
                                    <?= session()->get('fullname'); ?><br>
                                    <span style="font-size: 0.85em;">@<?= session()->get('username'); ?></span><br>
                                    <span style="font-size: 0.75em;"><?= session()->get('role'); ?></span><br>
                                    <span class="date" style="font-size: 0.75em;">Alamat IP: <?= $_SERVER['REMOTE_ADDR'] ?></span><br>
                                    <span class="date" style="font-size: 0.75em;">Waktu masuk: <?= session()->get('created_at'); ?></span><br>
                                    <span class="date" style="font-size: 0.75em;">Kedaluwarsa: <?= session()->get('expires_at'); ?></span>
                                </span>
                            </div>
                            <hr class="my-1">
                            <ul class="nav nav-pills flex-column">
                                <?php if (!(in_array(session()->get('role'), ['Satpam', 'Monitor Antrean']))) : ?>
                                    <li class="nav-item">
                                        <span style="font-size: 0.95em;" class="nav-link px-2 py-1 link-success" role="button">
                                            <div class="d-flex align-items-between">
                                                <label for="auto_date" class="d-flex align-items-start w-100" role="button">
                                                    <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </div>
                                                    <div class="ms-2 link-body-emphasis">
                                                        Tanggal Hari Ini
                                                    </div>
                                                </label>
                                                <div style="text-align: center;">
                                                    <div class="form-check form-switch ps-4 mb-0">
                                                        <input class="form-check-input" role="button" value="<?= (session()->get('auto_date') == 1) ? '1' : '0'; ?>" type="checkbox" role="switch" id="auto_date" name="auto_date" <?= (session()->get('auto_date') == 1) ? 'checked' : ''; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link nav-link-offcanvas px-2 py-1 link-success" href="<?= base_url('/settings'); ?>">
                                        <div class="d-flex align-items-start">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-gear"></i>
                                            </div>
                                            <div class="ms-2 link-body-emphasis">
                                                Pengaturan
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" id="logoutButton" class="nav-link px-2 py-1 link-success" href="#">
                                        <div class="d-flex align-items-start link-danger">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-right-from-bracket"></i>
                                            </div>
                                            <div class="ms-2 link-body-emphasis">
                                                Keluar
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="modal modal-sheet p-4 py-md-5 fade" id="logoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="logoutModal" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-body-tertiary rounded-5 shadow-lg transparent-blur">
                    <div class="modal-body p-4">
                        <h5 class="mb-0" id="logoutMessage">Apakah Anda ingin keluar?</h5>
                        <div class="row gx-2 pt-4">
                            <div class="col d-grid">
                                <button type="button" class="btn btn-lg btn-body bg-gradient fs-6 mb-0 rounded-4" data-bs-dismiss="modal">Batal</button>
                            </div>
                            <div class="col d-grid">
                                <button type="button" class="btn btn-lg btn-danger bg-gradient fs-6 mb-0 rounded-4" id="confirmLogout" onclick="window.location.href='<?= base_url('/logout'); ?>';">Keluar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENTS -->
        <div class="main-content-wrapper">
            <?php if (!(in_array(session()->get('role'), ['Satpam', 'Monitor Antrean']) && $activeSegment === 'home')) : ?>
                <nav id="sidebarMenu" class="d-md-block sidebar bg-body-secondary shadow-sm collapse transparent-blur">
                    <div id="sidebarMenu2" class="position-sticky sidebar-sticky p-1">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'home') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/home'); ?>">
                                    <div class="d-flex align-items-start <?= ($activeSegment === 'home') ? 'text-white' : 'link-success' ?>">
                                        <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                            <i class="fa-solid fa-house"></i>
                                        </div>
                                        <div class="flex-fill mx-2 <?= ($activeSegment === 'home') ? 'text-white' : 'link-body-emphasis' ?>">
                                            Beranda
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'antrean') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/antrean'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'antrean') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-user-large"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'antrean') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Antrean
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'pasien') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/pasien'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'pasien') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-user-injured"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'pasien') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Pasien
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'rawatjalan') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/rawatjalan'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'rawatjalan') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-hospital-user"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'rawatjalan') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Rawat Jalan
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'operasi') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/operasi'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'operasi') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-stethoscope"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'operasi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Pasien Operasi
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Dokter" || session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
                                <li class="nav-item">
                                    <span style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'lpoperasikatarak' || $activeSegment === 'lpoperasipterigium' || $activeSegment === 'lpoperasi') ? 'active bg-success' : '' ?>" role="button" data-bs-toggle="collapse" data-bs-target="#submenu-laporan-operasi">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'lpoperasikatarak' || $activeSegment === 'lpoperasipterigium' || $activeSegment === 'lpoperasi') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-file-medical"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'lpoperasikatarak' || $activeSegment === 'lpoperasipterigium' || $activeSegment === 'lpoperasi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Laporan Operasi
                                            </div>
                                            <div style="min-width: 16px; max-width: 16px; text-align: center;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <div id="submenu-laporan-operasi" class="collapse <?= ($activeSegment === 'lpoperasikatarak' || $activeSegment === 'lpoperasipterigium' || $activeSegment === 'lpoperasi') ? 'show' : '' ?>">
                                    <ul class="nav nav-pills flex-column my-1">
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'lpoperasikatarak') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/lpoperasikatarak'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'lpoperasikatarak') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Katarak
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'lpoperasipterigium') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/lpoperasipterigium'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'lpoperasipterigium') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Pterigium
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'lpoperasi') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/lpoperasi'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'lpoperasi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Lainnya
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <li class="nav-item">
                                    <span style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'frmsetujukedokteran' || $activeSegment === 'frmsetujuanestesi' || $activeSegment === 'frmsetujuphaco') ? 'active bg-success' : '' ?>" role="button" data-bs-toggle="collapse" data-bs-target="#submenu-formulir">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'frmsetujukedokteran' || $activeSegment === 'frmsetujuanestesi' || $activeSegment === 'frmsetujuphaco') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-file-contract"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'frmsetujukedokteran' || $activeSegment === 'frmsetujuanestesi' || $activeSegment === 'frmsetujuphaco') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Formulir
                                            </div>
                                            <div style="min-width: 16px; max-width: 16px; text-align: center;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <div id="submenu-formulir" class="collapse <?= ($activeSegment === 'frmsetujukedokteran' || $activeSegment === 'frmsetujuanestesi' || $activeSegment === 'frmsetujuphaco') ? 'show' : '' ?>">
                                    <ul class="nav nav-pills flex-column my-1">
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'frmsetujukedokteran') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/frmsetujukedokteran'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'frmsetujukedokteran') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Persetujuan Tindakan Kedokteran
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'frmsetujuanestesi') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/frmsetujuanestesi'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'frmsetujuanestesi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Persetujuan Tindakan Anestesi
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'frmsetujuphaco') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/frmsetujuphaco'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'frmsetujuphaco') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Persetujuan Tindakan Phacoemulsifikasi
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
                                <li class="nav-item">
                                    <span style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'rujukan' || $activeSegment === 'sakitmata' || $activeSegment === 'istirahat' || $activeSegment === 'butawarna') ? 'active bg-success' : '' ?>" role="button" data-bs-toggle="collapse" data-bs-target="#submenu-surat">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'rujukan' || $activeSegment === 'sakitmata' || $activeSegment === 'istirahat' || $activeSegment === 'butawarna') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'rujukan' || $activeSegment === 'sakitmata' || $activeSegment === 'istirahat' || $activeSegment === 'butawarna') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Surat
                                            </div>
                                            <div style="min-width: 16px; max-width: 16px; text-align: center;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <div id="submenu-surat" class="collapse <?= ($activeSegment === 'rujukan' || $activeSegment === 'sakitmata' || $activeSegment === 'istirahat' || $activeSegment === 'butawarna') ? 'show' : '' ?>">
                                    <ul class="nav nav-pills flex-column my-1">
                                        <?php if (session()->get('role') != "Perawat") : ?>
                                            <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                                <a class="nav-link px-2 py-1 <?= ($activeSegment === 'rujukan') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/rujukan'); ?>">
                                                    <div class="d-flex align-items-start <?= ($activeSegment === 'rujukan') ? 'text-white' : 'link-body-emphasis' ?>">
                                                        <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                            Rujukan
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'sakitmata') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/sakitmata'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'sakitmata') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Keterangan Sakit Mata
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'istirahat') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/istirahat'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'istirahat') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Keterangan Istirahat
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'butawarna') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/butawarna'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'butawarna') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Keterangan Buta Warna
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Apoteker" || session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'supplier') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/supplier'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'supplier') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-truck-field"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'supplier') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Pemasok
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'obat') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/obat'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'obat') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-prescription-bottle-medical"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'obat') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Obat
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'batchobat') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/batchobat'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'batchobat') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-boxes-stacked"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'batchobat') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Faktur Obat
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'opnameobat') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/opnameobat'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'opnameobat') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'opnameobat') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Laporan Stok Obat
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'resepluar') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/resepluar'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'resepluar') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-prescription"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'resepluar') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Resep Luar
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Apoteker" || session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'resep') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/resep'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'resep') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-prescription"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'resep') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Resep Dokter
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Apoteker" || session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'laporanresep') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/laporanresep'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'laporanresep') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-file-prescription"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'laporanresep') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Laporan Resep
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Kasir" || session()->get('role') == "Dokter" || session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'layanan') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/layanan'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'layanan') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-user-nurse"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'layanan') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Layanan
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Admisi" || session()->get('role') == "Kasir" || session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'transaksi') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/transaksi'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'transaksi') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-cash-register"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'transaksi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Kasir
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Admin" || session()->get('role') == "Admisi") : ?>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'unduhdokumen') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/unduhdokumen'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'unduhdokumen') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-file-arrow-down"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'unduhdokumen') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Unduh Dokumen
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (session()->get('role') == "Admin") : ?>
                                <li class="nav-item">
                                    <span style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'loket' || $activeSegment === 'jaminan' || $activeSegment === 'tindakanoperasi' || $activeSegment === 'poliklinik') ? 'active bg-success' : '' ?>" role="button" data-bs-toggle="collapse" data-bs-target="#submenu-masterdata">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'loket' || $activeSegment === 'jaminan' || $activeSegment === 'tindakanoperasi' || $activeSegment === 'poliklinik') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-database"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'loket' || $activeSegment === 'jaminan' || $activeSegment === 'tindakanoperasi' || $activeSegment === 'poliklinik') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Master Data
                                            </div>
                                            <div style="min-width: 16px; max-width: 16px; text-align: center;">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </span>
                                </li>
                                <div id="submenu-masterdata" class="collapse <?= ($activeSegment === 'loket' || $activeSegment === 'jaminan' || $activeSegment === 'tindakanoperasi' || $activeSegment === 'poliklinik') ? 'show' : '' ?>">
                                    <ul class="nav nav-pills flex-column my-1">
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'loket') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/loket'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'loket') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Loket
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'jaminan') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/jaminan'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'jaminan') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Jaminan
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'tindakanoperasi') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/tindakanoperasi'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'tindakanoperasi') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Tindakan Operasi
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item" style="margin-left: calc(24px + 0.5rem);">
                                            <a class="nav-link px-2 py-1 <?= ($activeSegment === 'poliklinik') ? 'active bg-success activeLinkSideBar' : '' ?>" href="<?= base_url('/poliklinik'); ?>">
                                                <div class="d-flex align-items-start <?= ($activeSegment === 'poliklinik') ? 'text-white' : 'link-body-emphasis' ?>">
                                                    <div class="flex-fill fw-normal" style="font-size: 0.75em;">
                                                        Ruangan Poliklinik
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <li class="nav-item">
                                    <a style="font-size: 0.95em;" class="nav-link px-2 py-1 <?= ($activeSegment === 'admin') ? 'active bg-success activeLinkSideBar' : '' ?>" href=" <?= base_url('/admin'); ?>">
                                        <div class="d-flex align-items-start <?= ($activeSegment === 'admin') ? 'text-white' : 'link-success' ?>">
                                            <div style="min-width: 24px; max-width: 24px; text-align: center;">
                                                <i class="fa-solid fa-users"></i>
                                            </div>
                                            <div class="flex-fill mx-2 <?= ($activeSegment === 'admin') ? 'text-white' : 'link-body-emphasis' ?>">
                                                Pengguna
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>
            <main class="main-content">
                <?= $this->renderSection('content'); ?>
            </main>
        </div>
        <div id="toastContainer" class="toast-container position-fixed top-0 p-3" aria-live="polite" aria-atomic="true">
            <?php if (session()->getFlashdata('info')) : ?>
                <div id="infoToast" class="toast align-items-center text-bg-info border border-info transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body d-flex align-items-start">
                        <div style="width: 24px; text-align: center;">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="w-100 mx-2 text-start">
                            <?= session()->getFlashdata('info'); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('msg')) : ?>
                <div id="msgToast" class="toast align-items-center text-bg-success border border-success transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body d-flex align-items-start">
                        <div style="width: 24px; text-align: center;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="w-100 mx-2 text-start">
                            <?= session()->getFlashdata('msg'); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div id="errorToast" class="toast align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body d-flex align-items-start">
                        <div style="width: 24px; text-align: center;">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div class="w-100 mx-2 text-start">
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>
            <?= $this->renderSection('toast'); ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?= base_url(); ?>assets_public/fontawesome/js/all.js"></script>
    <script src="<?= base_url(); ?>assets/js/dashboard/dashboard.js"></script>
    <?= $this->renderSection('javascript'); ?>
    <?= $this->renderSection('datatable'); ?>
    <?= $this->renderSection('chartjs'); ?>
    <script>
        // Fungsi untuk menampilkan spinner loading
        function showSpinner() {
            $('#loadingSpinner').show(); // Menampilkan elemen spinner
        }

        // Event listener untuk menangani sebelum halaman di-unload
        $(window).on('beforeunload', function() {
            showSpinner(); // Menampilkan spinner saat pengguna meninggalkan halaman
        });

        $('[data-bs-toggle="tooltip"]').each(function() {
            const tooltip = new bootstrap.Tooltip(this);

            // Dismiss the tooltip when the button is clicked
            $(this).on('click', function() {
                tooltip.hide();
            });
        });

        // Event listener untuk menangani klik pada tombol konfirmasi logout
        $(document).on('click', '#confirmLogout', function() {
            $('#logoutModal button').prop('disabled', true); // Nonaktifkan tombol logout
            $('#confirmLogout').html(`<?= $this->include('spinner/spinner'); ?>`); // Tampilkan pesan menunggu saat logout
        });

        // Ketika dokumen siap
        $(document).ready(function() {
            // Cari semua elemen dengan kelas 'activeLinkSideBar' di kedua navigasi
            $(".nav .activeLinkSideBar").each(function() {
                // Scroll ke elemen yang aktif
                this.scrollIntoView({
                    block: "center", // Fokus pada elemen aktif
                    inline: "center" // Elemen di-scroll ke tengah horizontal
                });
            });
            $('.nav-link-offcanvas-ext').on('click', function(e) {
                const offcanvasInstance = bootstrap.Offcanvas.getInstance($('#userOffcanvas')[0]);
                if (offcanvasInstance) {
                    e.preventDefault(); // Prevent the immediate navigation

                    offcanvasInstance.hide(); // Hide the offcanvas

                    // Get the target URL from the clicked link
                    const targetUrl = $(this).attr('href');

                    // Once the offcanvas is hidden, navigate to the settings page
                    $('#userOffcanvas').one('hidden.bs.offcanvas', function() {
                        window.open(targetUrl, '_blank'); // Open the URL in a new tab
                    });
                }
            });
            $('.nav-link-offcanvas').on('click', function(e) {
                const offcanvasInstance = bootstrap.Offcanvas.getInstance($('#userOffcanvas')[0]);
                if (offcanvasInstance) {
                    e.preventDefault(); // Prevent the immediate navigation

                    offcanvasInstance.hide(); // Hide the offcanvas

                    // Get the target URL from the clicked link
                    const targetUrl = $(this).attr('href');

                    // Once the offcanvas is hidden, navigate to the settings page
                    $('#userOffcanvas').one('hidden.bs.offcanvas', function() {
                        window.location.href = targetUrl;
                    });
                }
            });
            $('#auto_date').on('change', async function() {
                let $this = $(this); // Simpan referensi ke elemen checkbox
                let prevChecked = $this.is(':checked'); // Simpan status sebelum perubahan

                $this.prop('disabled', true); // Nonaktifkan checkbox sementara

                let url = prevChecked ?
                    `<?= base_url('settings/autodate-on/' . session()->get('id_user')); ?>` :
                    `<?= base_url('settings/autodate-off/' . session()->get('id_user')); ?>`;

                try {
                    let response = await axios.post(url);
                    window.location.reload();
                } catch (error) {
                    showFailedToast('Gagal mengatur status tanggal otomatis.<br>' + error);
                    $this.prop('checked', !prevChecked); // Kembalikan status awal jika gagal
                    $this.prop('disabled', false); // Aktifkan kembali checkbox
                }
            });
            $('#logoutButton').on('click', function(e) {
                e.preventDefault(); // Prevent default anchor behavior

                const offcanvasInstance = bootstrap.Offcanvas.getInstance($('#userOffcanvas')[0]);
                if (offcanvasInstance) {
                    offcanvasInstance.hide();

                    // Attach the event listener only once for the next time offcanvas is hidden
                    $('#userOffcanvas').one('hidden.bs.offcanvas', function() {
                        $('#logoutModal').modal('show');
                    });
                }
            });
            // Tampilkan pesan toast jika ada
            if ($('#infoToast').length) {
                var infoToast = new bootstrap.Toast($('#infoToast')[0]);
                infoToast.show(); // Menampilkan toast informasi
            }

            if ($('#msgToast').length) {
                var msgToast = new bootstrap.Toast($('#msgToast')[0]);
                msgToast.show(); // Menampilkan toast pesan
            }

            if ($('#errorToast').length) {
                var errorToast = new bootstrap.Toast($('#errorToast')[0]);
                errorToast.show(); // Menampilkan toast error
            }

            // Mengatur waktu untuk menyembunyikan toast setelah 5 detik
            setTimeout(function() {
                // Mengecek dan menyembunyikan setiap toast jika ada
                if ($('#infoToast').length) {
                    var infoToast = new bootstrap.Toast($('#infoToast')[0]);
                    infoToast.hide(); // Menyembunyikan toast informasi
                }

                if ($('#msgToast').length) {
                    var msgToast = new bootstrap.Toast($('#msgToast')[0]);
                    msgToast.hide(); // Menyembunyikan toast pesan
                }

                if ($('#errorToast').length) {
                    var errorToast = new bootstrap.Toast($('#errorToast')[0]);
                    errorToast.hide(); // Menyembunyikan toast error
                }
            }, 5000); // Durasi waktu untuk menyembunyikan toast (5000 ms)
        });
        // Show toast notification
        <?= $this->include('toast/index') ?>
    </script>
    <script>
        feather.replace({
            'aria-hidden': 'true'
        });
    </script>
</body>

</html>