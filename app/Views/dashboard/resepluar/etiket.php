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
    <title><?= $title; ?></title>
    <style>
        @page {
            size: 5.5cm 3.75cm;
            margin-top: 0.1cm;
            margin-left: 0.65cm;
            margin-right: 0.65cm;
            margin-bottom: 0.55cm;
        }

        body {
            font-family: Times, 'Times New Roman', serif;
            font-size: 5pt;
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
            font-weight: bold;
            text-align: center;
            font-size: 4.8pt;
        }
    </style>
</head>

<body>
    <div class="container-fluid my-3">
        <?php foreach ($detail_resep as $detail) : ?>
            <center>
                <h2 style="font-size: 5.7pt;">KLINIK UTAMA MATA<br>PADANG EYE CENTER TELUK KUANTAN</h2>
            </center>
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
                            <h2 style="text-align: center;"><?= ($resep['nama_pasien'] == NULL) ? '<em>PASIEN ANONIM</em>' : $resep['nama_pasien']; ?></h2>
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
                <?php elseif ($detail['bentuk_obat'] == 'Tablet/Kapsul') : ?>
                    <br>
                    OBAT DALAM
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>