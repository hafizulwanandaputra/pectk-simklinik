<?php

use CodeIgniter\I18n\Time;

$tanggal = Time::parse($resep['tanggal_resep']);
$tanggal_isi_resep = new DateTime($resep['tanggal_resep']);
// Buat formatter untuk tanggal dan waktu
$formatter = new IntlDateFormatter(
    'id_ID', // Locale untuk bahasa Indonesia
    IntlDateFormatter::LONG, // Format untuk tanggal
    IntlDateFormatter::NONE, // Tidak ada waktu
    'Asia/Jakarta', // Timezone
    IntlDateFormatter::GREGORIAN, // Calendar
    'd MMMM yyyy' // Format tanggal lengkap dengan nama hari
);

// Format tanggal
$tanggalFormat = $formatter->format($tanggal_isi_resep);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        @page {
            size: 21cm 29.7cm;
            margin: 1cm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
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
        <h2 style="text-align: center;">RESEP DOKTER</h2>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Tanggal dan Waktu:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $tanggal ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Dokter:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['dokter'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nama Pasien:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['nama_pasien'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nomor RM:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['no_rm'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nomor Registrasi:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['nomor_registrasi'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Jenis Kelamin:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div>
                            <?php if ($resep['jenis_kelamin'] == 'L') : ?>
                                Laki-Laki
                            <?php elseif ($resep['jenis_kelamin'] == 'P') : ?>
                                Perempuan
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Tanggal Lahir:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['tanggal_lahir'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Alamat:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['alamat'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nomor Telepon:</div>
                    </td>
                    <td style="width: 75%; vertical-align: top; padding: 0;">
                        <div><?= $resep['telpon'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <ul class="prescription">
            <?php foreach ($detailresep as $item) : ?>
                <li style="padding-bottom: 0.125cm; padding-top: 0.125cm; font-size: 11pt;">
                    <?= $item['nama_obat'] ?> • <?= $item['jumlah'] ?> item<br><span style="font-size: 10pt;"><?= $item['signa'] ?> • <?= $item['cara_pakai'] ?><br><?= $item['catatan'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="text-align: center; vertical-align: bottom; width: 50%;">Apoteker</td>
                    <td style="text-align: center; vertical-align: bottom; width: 50%;">Teluk Kuantan, <?= $tanggalFormat ?><br>Pasien</td>
                </tr>
                <tr>
                    <td style="text-align: center; padding-top: 2cm; width: 50%;"><?= session()->get('fullname') ?></td>
                    <td style="text-align: center; padding-top: 2cm; width: 50%;"><?= $resep['nama_pasien'] ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>