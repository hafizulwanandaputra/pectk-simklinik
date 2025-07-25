<?php
$platform = $agent->getPlatform();
$iconClass = '';
$browser = $agent->getBrowser();
$browserIcon = '';

if (stripos($platform, 'Windows') !== false) {
    $iconClass = '<i class="fa-brands fa-microsoft"></i>';
} elseif (stripos($platform, 'Mac') !== false || stripos($platform, 'macOS') !== false || stripos($platform, 'iOS') !== false || stripos($platform, 'iPadOS') !== false) {
    $iconClass = '<i class="fa-brands fa-apple"></i>';
} elseif (stripos($platform, 'Linux') !== false) {
    $iconClass = '<i class="fa-brands fa-linux"></i>';
} elseif (stripos($platform, 'Android') !== false) {
    $iconClass = '<i class="fa-brands fa-android"></i>';
} elseif (stripos($platform, 'BlackBerry') !== false) {
    $iconClass = '<i class="fa-brands fa-blackberry"></i>';
} elseif (stripos($platform, 'FreeBSD') !== false) {
    $iconClass = '<i class="fa-brands fa-freebsd"></i>';
} elseif (stripos($platform, 'Debian') !== false) {
    $iconClass = '<i class="fa-brands fa-debian"></i>';
} else {
    $iconClass = '<i class="fa-solid fa-computer"></i>';
}

if (stripos($browser, 'Chrome') !== false) {
    $browserIcon = '<i class="fa-brands fa-chrome"></i>';
} elseif (stripos($browser, 'Firefox') !== false) {
    $browserIcon = '<i class="fa-brands fa-firefox-browser"></i>';
} elseif (stripos($browser, 'Safari') !== false) {
    $browserIcon = '<i class="fa-brands fa-safari"></i>';
} elseif (stripos($browser, 'Edge') !== false || stripos($browser, 'Spartan') !== false) {
    $browserIcon = '<i class="fa-brands fa-edge"></i>';
} elseif (stripos($browser, 'Opera') !== false || stripos($browser, 'OPR') !== false) {
    $browserIcon = '<i class="fa-brands fa-opera"></i>';
} elseif (stripos($browser, 'Internet Explorer') !== false || stripos($browser, 'IE') !== false) {
    $browserIcon = '<i class="fa-brands fa-internet-explorer"></i>';
} else {
    $browserIcon = '<i class="fa-solid fa-globe"></i>';
}
?>
<?= $this->extend('dashboard/templates/dashboard'); ?>
<?= $this->section('css'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jqueryVersion = $.fn.jquery;
        document.getElementById('jquery-version').innerHTML = `${jqueryVersion}`;
    });
</script>
<?= $this->endSection(); ?>
<?= $this->section('title'); ?>
<div class="d-flex justify-content-start align-items-center">
    <a class="fs-5 me-3 text-success-emphasis" href="<?= base_url('/settings'); ?>"><i class="fa-solid fa-arrow-left"></i></a>
    <span class="fw-medium fs-5 flex-fill text-truncate"><?= $headertitle; ?></span>
    <div id="loadingSpinner" class="px-2">
        <?= $this->include('spinner/spinner'); ?>
    </div>
</div>
<div style="min-width: 1px; max-width: 1px;"></div>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>
<main class="main-content-inside px-3 pt-3">
    <div class="no-fluid-content">
        <div>
            <p><span class="h2"><?= $systemName ?></span><br><span class="fs-4"><?= $systemSubtitleName ?></span><br>&copy; 2025 <?= (date('Y') !== "2025") ? "- " . date('Y') : ''; ?> <?= $companyName ?></p>
        </div>
        <hr>
        <h5>Informasi Klien</h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><?= $iconClass; ?></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Sistem Operasi</h5>
                            <span><?= $agent->getPlatform(); ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><?= $browserIcon; ?></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Peramban Web</h5>
                            <span><?= $agent->getBrowser() . ' ' . $agent->getVersion(); ?></span>
                        </div>
                    </div>
            </li>
            <?php if ($agent->isMobile()) : ?>
                <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                    <div class="d-flex align-items-start">
                        <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-mobile-screen"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <div>
                                <h5 class="card-title">Telepon Seluler</h5>
                                <span><?= $agent->getMobile(); ?></span>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-globe"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Alamat IP Klien</h5>
                            <span><?= $_SERVER['REMOTE_ADDR'] ?> melalui port <?= $_SERVER['REMOTE_PORT'] ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-user-large"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">User Agent</h5>
                            <span><?= $agent->getAgentString(); ?></span>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <h5>Informasi <em>Backend</em></h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-server"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Peladen Web</h5>
                            <span><?= $_SERVER['SERVER_SOFTWARE']; ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-globe"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Nama Hos dan Alamat IP Peladen</h5>
                            <span><?= ($_SERVER['SERVER_NAME'] == $_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '<span class="text-nowrap">' . $_SERVER['SERVER_NAME'] . '</span> (' . $_SERVER['SERVER_ADDR'] . ')'; ?> melalui port <?= $_SERVER['SERVER_PORT'] ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-globe"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Sambungan HTTPS</h5>
                            <span><?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'Digunakan' : 'Tidak Digunakan'; ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-solid fa-database"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Versi Peladen Basis Data MySQL</h5>
                            <span><?= esc($version) ?> (<?= esc($version_comment) . ' • ' . esc($version_compile_os) . ' ' . esc($version_compile_machine) ?>)</span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-brands fa-php"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Versi PHP dan CodeIgniter</h5>
                            <span><?= phpversion(); ?> • <?= CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-brands fa-php"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Ekstensi PHP</h5>
                            <span><?= esc($php_extensions) ?></span>
                        </div>
                    </div>
                </div>
            </li>
            <?php if ($node_version !== null) : ?>
                <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                    <div class="d-flex align-items-start">
                        <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                            <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-brands fa-node-js"></i></p>
                        </a>
                        <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                            <div>
                                <h5 class="card-title">Versi Node.js</h5>
                                <span><?= esc($node_version) ?></span>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
        <h5>Informasi <em>Frontend</em></h5>
        <ul class="list-group shadow-sm  mb-3">
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-brands fa-bootstrap"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Versi Bootstrap</h5>
                            <span>5.3.7</span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item p-1 list-group-item-action disabled" aria-disabled="true">
                <div class="d-flex align-items-start">
                    <a href="#" class="stretched-link" style="min-width: 3rem; max-width: 3rem; text-align: center;">
                        <p class="mb-0" style="font-size: 1.75rem!important;"><i class="fa-brands fa-js"></i></p>
                    </a>
                    <div class="align-self-stretch flex-fill ps-1 text-wrap overflow-hidden d-flex align-items-center" style="text-overflow: ellipsis;">
                        <div>
                            <h5 class="card-title">Versi jQuery</h5>
                            <span id="jquery-version">Memuat...</span>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <hr>
        <div>
            <p>Aplikasi ini didasarkan pada <a class="text-decoration-none" href="https://github.com/hafizulwanandaputra/hwpweb-admin-template" target="_blank"><span style="font-weight: 900;">HWP</span><span style="font-weight: 300;">web</span> ADMIN Template</a></p>
        </div>
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