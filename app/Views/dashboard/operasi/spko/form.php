<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($operasi['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($operasi['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggal_operasi = $operasi['tanggal_operasi'];
$jam_operasi = $operasi['jam_operasi'];

// Pastikan input adalah format tanggal dan waktu yang valid
$date = new DateTime($tanggal_operasi);
$time = new DateTime($jam_operasi);

// Format tanggal dalam Bahasa Indonesia
$tanggalFormatter = new IntlDateFormatter(
    'id_ID',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Asia/Jakarta',
    IntlDateFormatter::GREGORIAN,
    'd MMMM yyyy'
);
$tanggalFormatted = $tanggalFormatter->format($date);

// Format waktu
$waktuFormatted = $time->format('H.i');
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.typekit.net/dew1xab.css">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: neue-haas-unica, Helvetica, Arial, sans-serif;
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
            height: 22.95cm;
            overflow: hidden;
            padding: 0cm;
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
                        <div style="white-space: nowrap;"><strong>FRM: 3b<br>Rev: 000</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0; text-align: center;">SURAT PERINTAH KAMAR OPERASI</h2>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Nomor Rekam Medis
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['no_rm']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Nama Pasien
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['nama_pasien']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Tempat dan Tanggal Lahir
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['tempat_lahir'] . ', ' . $operasi['tanggal_lahir']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Alamat
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['alamat']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Diagnosis
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['diagnosa']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jenis Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['jenis_tindakan']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Indikasi Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['indikasi_operasi']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jenis Bius
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['jenis_bius']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jenis Rawat (Jalan/Inap)
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['rajal_ranap']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Ruangan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['ruang_operasi']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Nama Operator
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['dokter_operator']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Tanggal dan Waktu Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['tanggal_operasi'] . ' ' . $operasi['jam_operasi']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Tipe Bayar
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 70%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['tipe_bayar']; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 100%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <h3 style="margin: 0;"><em>SITE MARKING</em><br><small>Penandaan Lokasi Operasi</small></h3>
                            <dl>
                                <dt><strong>Diagnosis</strong></dt>
                                <dd><?= ($operasi['diagnosa_site_marking']) ? $operasi['diagnosa_site_marking'] : '-'; ?></dd>
                                <dt><strong>Tindakan</strong></dt>
                                <dd><?= ($operasi['tindakan_site_marking']) ? $operasi['tindakan_site_marking'] : '-'; ?></dd>
                            </dl>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'uploads/site_marking/' . $operasi['site_marking'])) ?>" width="240px" alt="">
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                            <div><br>DPJP</div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 2cm;">
                            <div>Tanggal <?= $tanggalFormatted ?> pukul <?= $waktuFormatted ?><br>Pasien/Keluarga Pasien</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; border-bottom: 1px dotted black;">
                            <div><?= $operasi['dokter_operator'] ?></div>
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; border-bottom: 1px dotted black;">
                            <div><?= $operasi['nama_pasien_keluarga'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>