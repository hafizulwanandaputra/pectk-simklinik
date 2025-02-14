<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($lp_operasi['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($lp_operasi['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $lp_operasi['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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
            font-size: 9pt;
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
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            height: calc(100vh - 5.5cm);
            overflow: hidden;
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
                    <td style="width: 0%;">
                        <div style="white-space: nowrap;"><strong>FRM: 5b<br>Rev: 000</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">LAPORAN OPERASI</h2>
                        <div>Tanggal operasi: <?= $lp_operasi['tanggal_operasi'] . ' ' . $lp_operasi['jam_operasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $lp_operasi['nama_pasien']; ?></div>
                            <div><?= $lp_operasi['no_rm']; ?></div>
                            <div><?= $lp_operasi['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 4px;">
                            <div><?= $lp_operasi['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <div style="padding-right: 0.25cm; padding-left: 0.25cm;">
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Dokter Bedah
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['dokter_bedah'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Asisten Dokter
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['asisten_dokter_bedah'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Dokter Anestesi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['dokter_anastesi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Jenis Anestesi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['jenis_anastesi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Diagnosis
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%; text-align: left;">Pra Bedah</th>
                                            <th style="width: 50%; text-align: left;">Pasca Bedah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $lp_operasi['diagnosis_pra_bedah'] ?></td>
                                            <td><?= $lp_operasi['diagnosis_pasca_bedah'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Indikasi Operasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['indikasi_operasi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Nama Operasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['nama_operasi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Jaringan yang dieksisi/inisiasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['jaringan_eksisi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Pemeriksaan PA
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['pemeriksaan_pa'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Lama Operasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi['lama_operasi'] ?> menit
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 100%; vertical-align: top;">
                                <div><strong>Laporan Operasi:</strong></div>
                                <div style="padding-left: 0.5cm;"><?= nl2br($lp_operasi['laporan_operasi']) ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;"></td>
                            <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                                <div>Tanggal <?= $tanggalFormatted ?> pukul <?= $waktuFormatted ?><br>Dokter Bedah</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                            <td style="width: 50%; text-align: center; vertical-align: top;">
                                <div><?= $lp_operasi['dokter_bedah'] ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>