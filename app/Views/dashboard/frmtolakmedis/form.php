<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($form_penolakan_tindakan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($form_penolakan_tindakan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $form_penolakan_tindakan['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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

// Format waktu
$waktuFormatted = $dateTime->format('H.i.s');

// Hubungan
switch ($form_penolakan_tindakan['hubungan']) {
    case 'Diri Sendiri':
        $hubungan = 'Diri Saya';
        break;
    default:
        $hubungan = $form_penolakan_tindakan['hubungan']; // Gunakan nilai asli jika tidak cocok dengan kasus di atas
        break;
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
            font-size: 12pt;
            line-height: 1.2;
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
            height: calc(100vh - 5.9cm);
            overflow: hidden;
            padding: 0cm;
            font-size: 11pt;
            line-height: 1.5;
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

        .full-border {
            border-collapse: collapse;
        }

        .full-border th,
        .full-border td {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <table class="table" style="width: 100%; margin-bottom: 4px; border-bottom: 2px solid black; font-size: 9pt;">
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
        <tbody>
            <tr>
                <td style="width: 60%; vertical-align: top; padding: 0; text-align: center;">
                    <h2 style="padding: 0; text-decoration: underline;">SURAT PENOLAKAN TINDAKAN MEDIS</h2>
                    <h2 style="padding: 0;"><small>(<em>INFORMED REFUSAL</em>)</small></h2>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="box">
        <div style="padding-right: 0.25cm; padding-left: 0.25cm;">
            <p>Yang bertanda tangan di bawah ini:</p>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Nama
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $form_penolakan_tindakan['nama_pasien'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Umur
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $usia->y . " tahun " . $usia->m . " bulan" ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Jenis Kelamin
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?php if ($form_penolakan_tindakan['jenis_kelamin'] == 'L') : ?>
                                Laki-Laki
                            <?php elseif ($form_penolakan_tindakan['jenis_kelamin'] == 'P') : ?>
                                Perempuan
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Alamat
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $form_penolakan_tindakan['alamat'] ?><br>
                            <small><?= (!empty($form_penolakan_tindakan['kelurahan'])) ? $form_penolakan_tindakan['kelurahan'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_penolakan_tindakan['kecamatan'])) ? $form_penolakan_tindakan['kecamatan'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_penolakan_tindakan['kabupaten'])) ? $form_penolakan_tindakan['kabupaten'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_penolakan_tindakan['provinsi'])) ? $form_penolakan_tindakan['provinsi'] : ''; ?></small>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>Dengan ini menyatakan <strong>PENOLAKAN</strong> untuk dilakukan tindakan medis berupa “<em><?= $form_penolakan_tindakan['diagnosis'] ?></em>” terhadap <?= strtolower($hubungan) ?><?= (!empty($form_penolakan_tindakan['hubungan_lain'])) ? ' (' . strtolower($form_penolakan_tindakan['hubungan_lain']) . ')' : '';  ?>.</p>
            <p>Saya memahami keperluan dan manfaat dari tindakan tersebut sebagaimana telah dijelaskan oleh tenaga kesehatan Klinik Utama Mata Padang Eye Center Teluk Kuantan kepada saya, termasuk akibat atau risiko dan komplikasi yang mungkin dari penolakan tersebut.</p>
            <p>Demikian surat pernyataan ini dibuat dengan penuh kesadaran dan tanpa paksaan serta dapat saya pertanggungjawabkan sebagaimana mestinya.</p>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: bottom; padding-bottom: 2.5cm;">
                            <div>Yang menyatakan</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: bottom; padding-bottom: 2.5cm;">
                            <div>Teluk Kuantan, <?= $tanggalFormatted ?><br>Dokter Spesialis Mata</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top;">(.................................)</td>
                        <td style="width: 50%; text-align: center; vertical-align: top;">(.................................)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>

</body>

</html>