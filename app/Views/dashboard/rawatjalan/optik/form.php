<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $rawatjalan['tanggal_registrasi']; // Misalnya: "2025-01-14 15:23:45"

// Pastikan input adalah format tanggal dan waktu yang valid
$dateTime = new DateTime($tanggalRegistrasi);

// Format tanggal dalam Bahasa Indonesia
$tanggalFormatter = new IntlDateFormatter(
    'id_ID',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Asia/Jakarta',
    IntlDateFormatter::GREGORIAN,
    'd MMMM yyyy'
);
$tanggalFormatted = $tanggalFormatter->format($dateTime);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.1;
        }

        .prescription {
            list-style-type: none;
        }

        .prescription li::before {
            content: "R/ ";
            font-weight: bold;
        }

        .listtable {
            border-collapse: collapse;
        }

        .listtable .outline-border {
            border: 1px solid black;
        }

        .listtable .outline-border-left {
            border-right: 1px solid black;
            border-left: 0;
            border-bottom: 1px solid black;
            border-top: 1px solid black;
        }

        .listtable .outline-border-right {
            border-left: 1px solid black;
            border-right: 0;
            border-bottom: 1px solid black;
            border-top: 1px solid black;
        }

        .box {
            border: 1px solid black;
            height: 18cm;
            overflow: hidden;
            padding: 0cm;
        }

        .border-bottom-right {
            border-bottom: 2px solid black;
            border-right: 2px solid black;
        }

        .border-bottom-left {
            border-bottom: 2px solid black;
            border-left: 2px solid black;
        }

        .border-top-right {
            border-top: 2px solid black;
            border-right: 2px solid black;
        }

        .border-top-left {
            border-top: 2px solid black;
            border-left: 2px solid black;
        }

        .container-kacamata {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .garis {
            position: absolute;
            width: 50%;
            transform: translateY(-50%);
            display: flex;
            justify-content: center;
        }

        .isi-garis {
            height: 2px;
            width: 70%;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0) 50%, black 50%);
        }

        .start {
            left: 0;
            transform: translateY(-50%) rotate(-<?= $optik['od_login_axis']; ?>deg);
        }

        .end {
            right: 0;
            transform: translateY(-50%) rotate(-<?= $optik['os_login_axis']; ?>deg);
        }
    </style>
</head>

<body>
    <div class="container-fluid my-3">
        <table class="table" style="width: 100%; margin-bottom: 4px; border-bottom: 2px solid black;">
            <thead>
                <tr>
                    <th style="width: 0%;">
                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/logo_pec.png')) ?>" width="64px" alt="">
                    </th>
                    <td style="width: 100%;">
                        <h3 style="margin: 0; padding: 0;">KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN</h3>
                        <div>
                            <div>Jl. Rusdi S. Abrus No. 35, LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.</div>
                        </div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="display: flex; justify-content: center;">
                        <div style="position: relative; width: 340px;">
                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/kacamata.png')) ?>" width="340px" alt="">
                            <!-- OD -->
                            <div class="container-kacamata">
                                <div class="garis start">
                                    <div class="isi-garis"></div>
                                </div>
                                <!-- OS -->
                                <div class="garis end">
                                    <div class="isi-garis"></div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <td style="width: 0%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                            <?php if ($optik['tipe_lensa'] == 'TRIFOCUS'): ?>
                                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div>Trifocus</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                            <?php if ($optik['tipe_lensa'] == 'BIFOCUS'): ?>
                                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div>Bifocus</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                            <?php if ($optik['tipe_lensa'] == 'SINGLE FOCUS'): ?>
                                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div style="white-space: nowrap;">Single Focus</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px; border-collapse: collapse; font-size: 9pt;">
            <thead>
                <tr>
                    <th style="padding-top: 3px; padding-bottom: 0px; border-right: 1px solid black;"></th>
                    <th style="padding-top: 3px; padding-bottom: 0px; border: 1px solid black;" colspan="5">
                        <h2 style="text-align: center; margin: 0.25cm;">O.D</h2>
                    </th>
                    <th style="padding-top: 3px; padding-bottom: 0px; border: 1px solid black;" colspan="5">
                        <h2 style="text-align: center; margin: 0.25cm;">O.S</h2>
                    </th>
                    <th colspan="2" style="padding-top: 3px; padding-bottom: 0px; border-left: 1px solid black;"></th>
                </tr>
                <tr>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border-bottom: 1px solid black; border-right: 1px solid black;"></th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Spher</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Cyldr</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Axis</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Prisma</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Basis</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Spher</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Cyldr</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Axis</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Prisma</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Basis</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Golor Vitror</th>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Distant Pupil</th>
                </tr>
                <tr>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap;">Pro Login<br>Quitat</th>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_login_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_login_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_login_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_login_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_login_basis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_basis']; ?>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_vitror']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_login_pupil']; ?>
                    </td>
                </tr>
                <tr>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black;">Pro Domo</th>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_domo_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_domo_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_domo_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_domo_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_domo_basis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_basis']; ?>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_vitror']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_domo_pupil']; ?>
                    </td>
                </tr>
                <tr>
                    <th style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black;">Propin Quitat</th>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_quitat_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_quitat_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_quitat_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_quitat_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['od_quitat_basis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_spher']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_cyldr']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_axis']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_prisma']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_basis']; ?>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_vitror']; ?>
                    </td>
                    <td style="padding-top: 3px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                        <?= $optik['os_quitat_pupil']; ?>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; max-width: 6cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <center>
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 4px;">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </center>
                    </td>
                    <td style="width: 40%; vertical-align: top; padding: 0;">
                        <center>
                            <div>Teluk Kuantan, <?= $tanggalFormatted ?></div>
                            <div style="padding-top: 1.25cm;"></div>
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>