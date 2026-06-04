<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($form_pemeriksaan_pasien['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($form_pemeriksaan_pasien['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $form_pemeriksaan_pasien['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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
            height: calc(100vh - 4.6cm);
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
                    <h2 style="padding: 0; text-decoration: underline;">FORMULIR PEMERIKSAAN PASIEN</h2>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="box">
        <div style="padding-right: 0.25cm; padding-left: 0.25cm;">
            <p>Saya yang bertanda tangan di bawah ini menerangkan bahwa:</p>
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
                            <?= $form_pemeriksaan_pasien['nama_pasien'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Nomor Induk Kependudukan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $form_pemeriksaan_pasien['nik'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Tempat dan Tanggal Lahir
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $form_pemeriksaan_pasien['tempat_lahir'] . ', ' . $form_pemeriksaan_pasien['tanggal_lahir'] ?>
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
                            <?php if ($form_pemeriksaan_pasien['jenis_kelamin'] == 'L') : ?>
                                Laki-Laki
                            <?php elseif ($form_pemeriksaan_pasien['jenis_kelamin'] == 'P') : ?>
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
                            <?= $form_pemeriksaan_pasien['alamat'] ?><br>
                            <small><?= (!empty($form_pemeriksaan_pasien['kelurahan'])) ? $form_pemeriksaan_pasien['kelurahan'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_pemeriksaan_pasien['kecamatan'])) ? $form_pemeriksaan_pasien['kecamatan'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_pemeriksaan_pasien['kabupaten'])) ? $form_pemeriksaan_pasien['kabupaten'] . ', ' : ''; ?></small>
                            <small><?= (!empty($form_pemeriksaan_pasien['provinsi'])) ? $form_pemeriksaan_pasien['provinsi'] : ''; ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Jaminan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $form_pemeriksaan_pasien['jaminan'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>Pada pemeriksaan ini didapatkan diagnosis dan tindakan sebagai berikut:</p>

            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">DIAGNOSIS MEDIS (A):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px; font-size: 10pt; padding-left: 0.25cm; padding-right: 0.25cm;">
                <tbody>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden;"></td>
                        <td colspan="3" style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            ICD-10
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['diagnosa_medis_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icdx_kode_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['diagnosa_medis_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icdx_kode_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['diagnosa_medis_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icdx_kode_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['diagnosa_medis_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icdx_kode_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['diagnosa_medis_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icdx_kode_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">TINDAKAN (P):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px; font-size: 10pt; padding-left: 0.25cm; padding-right: 0.25cm;">
                <tbody>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden;"></td>
                        <td colspan="3" style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            ICD-9 CM
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['terapi_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icd9_kode_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['terapi_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icd9_kode_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['terapi_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icd9_kode_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['terapi_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icd9_kode_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['terapi_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $form_pemeriksaan_pasien['icd9_kode_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>Demikian formulir pemeriksaan ini dibuat untuk dipergunakan seperlunya. Atas perhatiannya, saya ucapkan terima kasih.</p>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;"></td>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                            <div>Teluk Kuantan, <?= $tanggalFormatted ?><br>Dokter Spesialis Mata</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                        <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>

</body>

</html>