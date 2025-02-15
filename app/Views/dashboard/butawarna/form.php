<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($butawarna['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($butawarna['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $butawarna['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
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
                    <h2 style="padding: 0; text-decoration: underline;">SURAT KETERANGAN BUTA WARNA</h2>
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
                        <td style="width: 20%; vertical-align: top;">
                            Nama
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 80%; vertical-align: top;">
                            <?= $butawarna['nama_pasien'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align: top;">
                            Umur
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 80%; vertical-align: top;">
                            <?= $usia->y . " tahun " . $usia->m . " bulan" ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align: top;">
                            Jenis Kelamin
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 80%; vertical-align: top;">
                            <?php if ($butawarna['jenis_kelamin'] == 'L') : ?>
                                Laki-Laki
                            <?php elseif ($butawarna['jenis_kelamin'] == 'P') : ?>
                                Perempuan
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align: top;">
                            Alamat
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 80%; vertical-align: top;">
                            <?= $butawarna['alamat'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%; vertical-align: top;">
                            Keperluan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 80%; vertical-align: top;">
                            <?= $butawarna['keperluan'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php if (!empty($butawarna['od_ukuran_kacamata']) && !empty($butawarna['od_visus']) && !empty($butawarna['os_ukuran_kacamata']) && !empty($butawarna['os_visus'])) : ?>
                <p>Pada pemeriksaan mata didapatkan:</p>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 20%; vertical-align: top;">
                                Mata Kanan
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 0%; vertical-align: top; white-space: nowrap;">
                                <?= $butawarna['od_ukuran_kacamata'] ?> &rarr; <?= $butawarna['od_visus'] ?>
                            </td>
                            <td rowspan="2" style="width: 80%; vertical-align: middle;">
                                <div style="display: flex; justify-content: flex-start; align-items: center; transform: translateY(-3px);">
                                    <div style="font-size: 2.5em">}</div>
                                    <strong style="padding-left: 0.25cm;">Emetrop</strong>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%; vertical-align: top;">
                                Mata Kiri
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 0%; vertical-align: top; white-space: nowrap;">
                                <?= $butawarna['os_ukuran_kacamata'] ?> &rarr; <?= $butawarna['os_visus'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
            <h2 style="padding: 0; text-decoration: underline; text-align: center;">
                <?= $butawarna['status_buta_warna'] ?>
            </h2>
            <p>Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya. Atas perhatiannya, saya ucapkan terima kasih.</p>
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