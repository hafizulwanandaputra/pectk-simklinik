<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rajal['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rajal['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $rajal['tanggal_registrasi']; // Misalnya: "2025-01-14 15:23:45"

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
            font-family: <?= env('PDF-FONT') ?>;
            font-size: 11pt;
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
            padding: 0cm;
            font-size: 10pt;
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
                    <h2 style="padding: 0; text-decoration: underline;">LEMBAR ISIAN OPERASI</h2>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="box">
        <div style="padding-right: 0.25cm; padding-left: 0.25cm;">
            <table class="table" style="width: 100%; margin-bottom: 0.5cm;">
                <tbody>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Nomor Rekam Medis
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['no_rm'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Nama Pasien
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['nama_pasien'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Tempat, Tanggal Lahir, Umur
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['tempat_lahir'] ?>, <?= $rajal['tanggal_lahir'] ?>, <?= $usia->y . " tahun " . $usia->m . " bulan" ?>
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
                            <?= $rajal['alamat'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Nomor Telepon
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['telpon'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Tindakan yang Akan Dilakukan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['tindakan_operasi_rajal'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Waktu Tindakan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= date('H:i', strtotime($rajal['jam_operasi_rajal'])) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Dokter Rujukan
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top;">
                            <?= $rajal['dokter'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            Tarif
                        </td>
                        <td style="width: 0%; vertical-align: top;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; border-bottom: 1px dotted black;">
                            Rp
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                            <div>Mengetahui,</div>
                            <div>Pasien / Keluarga</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                            <div>Teluk Kuantan, <?= $tanggalFormatted ?></div>
                            <div>Yang Membuat,</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                        <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top;">
                            <div>...................................................</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top;">
                            <div><?= $rajal['pendaftar'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
</body>

</html>