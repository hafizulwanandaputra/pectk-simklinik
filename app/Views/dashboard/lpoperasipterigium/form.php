<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($lp_operasi_pterigium['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($lp_operasi_pterigium['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $lp_operasi_pterigium['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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
    <link href="<?= base_url(); ?>assets_public/fonts/helvetica/stylesheet.css" rel="stylesheet">
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
            border: 1px solid black;
            height: 22.5cm;
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
                        <div style="white-space: nowrap;"><strong>FRM: 5e<br>Rev: 001</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">LAPORAN OPERASI PTERIGIUM</h2>
                        <div>Tanggal operasi: <?= $lp_operasi_pterigium['tanggal_operasi'] . ' ' . $lp_operasi_pterigium['jam_operasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $lp_operasi_pterigium['nama_pasien']; ?></div>
                            <div><?= $lp_operasi_pterigium['no_rm']; ?></div>
                            <div><?= $lp_operasi_pterigium['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode">
                            <div><?= $lp_operasi_pterigium['nomor_registrasi']; ?></div>
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
                                Mata
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['mata'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Dokter Operator
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['operator'] ?>
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
                                <?= $lp_operasi_pterigium['lama_operasi'] ?> menit
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
                                <?= $lp_operasi_pterigium['diagnosis'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Asisten
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['asisten'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Jenis Operasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['jenis_operasi'] ?>
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
                                <?= $lp_operasi_pterigium['jenis_anastesi'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Anestesiologis
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['dokter_anastesi'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Antiseptik
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= ($lp_operasi_pterigium['antiseptic'] == 'BETADINE') ? $lp_operasi_pterigium['antiseptic'] : $lp_operasi_pterigium['antiseptic_lainnya']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Spekulum
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= ($lp_operasi_pterigium['spekulum'] == 'WIRE') ? $lp_operasi_pterigium['spekulum'] : $lp_operasi_pterigium['spekulum_lainnya']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Kendala Rektus Superior
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['kendala_rektus_superior']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Cangkok Konjungtiva
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= ($lp_operasi_pterigium['cangkok_konjungtiva'] == 'YA') ? $lp_operasi_pterigium['cangkok_konjungtiva'] . ' (Ukuran: ' . $lp_operasi_pterigium['ukuran_cangkok'] . ') ' : $lp_operasi_pterigium['cangkok_konjungtiva']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Cangkang Membran Amnio
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= ($lp_operasi_pterigium['cangkang_membrane_amnio'] == 'YA') ? $lp_operasi_pterigium['cangkang_membrane_amnio'] . ' (Ukuran: ' . $lp_operasi_pterigium['ukuran_cangkang'] . ') ' : $lp_operasi_pterigium['cangkang_membrane_amnio']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                <em>Bare Sclera</em>
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['bare_sclera']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Mitomisin C
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['mytomicyn_c']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Penjahitan
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_pterigium['penjahitan']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 100%; vertical-align: top;">
                                <div><strong>Tindakan:</strong></div>
                                <div style="padding-left: 0.5cm;"><?= nl2br($lp_operasi_pterigium['keterangan_tambahan']) ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 100%; vertical-align: top;">
                                <div><strong>Terapi Pasca Bedah:</strong></div>
                                <div style="padding-left: 0.5cm;"><?= nl2br($lp_operasi_pterigium['terapi_pasca_bedah']) ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;"></td>
                            <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                                <div>Tanggal <?= $tanggalFormatted ?> pukul <?= $waktuFormatted ?><br>Dokter Operator</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                            <td style="width: 50%; text-align: center; vertical-align: top;">
                                <div><?= $lp_operasi_pterigium['operator'] ?></div>
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