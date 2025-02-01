<?php

use CodeIgniter\I18n\Time;

$tanggal_resep = Time::parse($resep['tanggal_resep']);
$tanggal_lahir = Time::parse($resep['tanggal_lahir']);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url(); ?>assets_public/fonts/helvetica/stylesheet.css" rel="stylesheet">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 5pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        h2 {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
            font-size: 7pt;
        }

        .box {
            border: 1px solid black;
            height: 0.9cm;
            overflow: hidden;
            padding: 0cm;
            padding-top: 0.02cm;
            font-weight: bold;
            text-align: center;
            font-size: 4.8pt;
        }
    </style>
</head>

<body>
    <div>
        <?php foreach ($detail_resep as $detail) : ?>
            <div style="text-align: center;">
                <h2 style="font-size: 5.4pt;">KLINIK UTAMA MATA<br>PADANG EYE CENTER TELUK KUANTAN</h2>
            </div>
            <table width="100%">
                <tbody style="vertical-align: top;">
                    <tr>
                        <td style="width: 100%;">No. RM: <?= $resep['no_rm'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">Tgl: <?= $tanggal_resep->toLocalizedString('dd/MM/yyyy') ?></td>
                    </tr>
                    <tr>
                        <td style="width: 100%; height: 0.425cm;">DPJP: <?= $resep['dokter'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">DOB: <?= $tanggal_lahir->toLocalizedString('dd/MM/yyyy') ?></td>
                    </tr>
                </tbody>
            </table>
            <table width="100%" style="padding-top: 0cm; padding-bottom: 0cm;">
                <tbody style="vertical-align: middle;">
                    <tr>
                        <td style="height: 0.5cm;">
                            <h2 style="text-align: center;"><?= $resep['nama_pasien'] ?></h2>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="box">
                <?= $detail['nama_obat'] ?>
                <br>
                <span style="font-size: 7pt;"><?= $detail['signa'] ?> • <?= $detail['catatan'] ?></span>
                <br>
                <?= $detail['cara_pakai'] ?>
                <?php if ($detail['bentuk_obat'] == 'Tetes' || $detail['bentuk_obat'] == 'Salep') : ?>
                    <br>
                    OBAT LUAR
                <?php elseif ($detail['bentuk_obat'] == 'Tablet/Kapsul' || $detail['bentuk_obat'] == 'Sirup') : ?>
                    <br>
                    OBAT DALAM
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>