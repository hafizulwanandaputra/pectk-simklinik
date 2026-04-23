<?php

use CodeIgniter\I18n\Time;

$tanggal_resep = Time::parse($resep['tanggal_resep']);

if (!empty($resep['tanggal_lahir']) && $resep['tanggal_lahir'] != '0000-00-00') {
    $tanggal_lahir = Time::parse($resep['tanggal_lahir']);
    $tanggal_lahir_formatted = $tanggal_lahir->toLocalizedString('dd/MM/yyyy');
} else {
    $tanggal_lahir_formatted = '<em>Tidak ada</em>';
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wdth,wght@0,75..100,100..900;1,75..100,100..900&display=swap" rel="stylesheet">
    <?php if (!empty(env('PDF-FONT-CSS')) || !empty(env('PDF-FONT-MONOSPACE-CSS'))): ?>

        <?php if (!empty(env('PDF-FONT-CSS'))): ?>
            <link href="<?= base_url(env('PDF-FONT-CSS')) ?>" rel="stylesheet">
        <?php endif; ?>

        <?php if (!empty(env('PDF-FONT-MONOSPACE-CSS'))): ?>
            <link href="<?= base_url(env('PDF-FONT-MONOSPACE-CSS')) ?>" rel="stylesheet">
        <?php endif; ?>

    <?php endif; ?>
    <style>
        body {
            font-family: <?= env('PDF-FONT') ?>;
            font-feature-settings: <?= env('PDF-FONT-OPENTYPE') ?>;
            font-size: 4pt;
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
    <div class="container-fluid my-3">
        <?php foreach ($detail_resep as $detail) : ?>
            <div style="text-align: center; padding-bottom: 0.05cm;">
                <h2 style="font-size: 5.4pt;">KLINIK UTAMA MATA<br>PADANG EYE CENTER TELUK KUANTAN</h2>
            </div>
            <table width="100%">
                <tbody style="vertical-align: top;">
                    <tr>
                        <td style="width: 100%;">No. RM: <?= $resep['no_rm'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">Tgl. Resep: <?= $tanggal_resep->toLocalizedString('dd/MM/yyyy') ?></td>
                    </tr>
                    <tr>
                        <td style="width: 100%; height: 0.425cm;">DPJP: <?= $resep['dokter'] ?></td>
                        <td style="width: 0%; text-align: right; white-space: nowrap;">Tgl. Lahir: <?= $tanggal_lahir_formatted ?></td>
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
                <span style="font-size: 7pt;"><?= (empty($detail['signa'])) ? '<em>Tidak ada dosis</em>' : $detail['signa'] ?> • <?= (empty($detail['catatan'])) ? '<em>Tidak ada catatan</em>' : $detail['catatan'] ?></span>
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