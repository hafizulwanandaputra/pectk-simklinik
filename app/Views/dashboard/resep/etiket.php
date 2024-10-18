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
            size: 7cm 4.2cm;
            margin: 0.2cm;
        }

        body {
            font-family: Times, 'Times New Roman', serif;
            font-size: 5.5pt;
            color: #2F5496;
        }

        table {
            border-collapse: collapse;
        }

        h2 {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
            font-size: 9pt;
        }

        .box {
            border: 1px solid #4472C4;
            height: 1.3cm;
            overflow: hidden;
            padding: 0.1cm;
            font-weight: bold;
            text-align: center;
            font-size: 7.5pt;
        }
    </style>
</head>

<body>
    <div class="container-fluid my-3">
        <?php foreach ($detail_resep as $detail) : ?>
            <center>
                <h2>KLINIK MATA<br>PADANG EYE CENTER • TELUK KUANTAN</h2>
            </center>
            <table width="100%">
                <tbody>
                    <tr>
                        <td style="width: 100%;">No. RM: <?= $resep['no_rm'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">Tgl: <?= $tanggal_resep->toLocalizedString('dd/MM/yyyy') ?></td>
                    </tr>
                    <tr>
                        <td style="width: 100%;">DPJP: <?= $resep['dokter'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">DOB: <?= $tanggal_lahir->toLocalizedString('dd/MM/yyyy') ?></td>
                    </tr>
                </tbody>
            </table>
            <center>
                <h2 style="padding-top: 0.2cm; padding-bottom: 0.2cm; white-space: nowrap;"><?= $resep['nama_pasien'] ?></h2>
            </center>
            <div class="box">
                <?= $detail['nama_obat'] ?>
                <br>
                <?= $detail['signa'] ?> hari • <?= $detail['catatan'] ?>
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