<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('assets_public/fonts/texgyre-heros/stylesheet.css') ?>">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: TeXGyreHeros, Helvetica, Arial, sans-serif;
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
            height: calc(100vh - 5.25cm);
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
                        <div style="white-space: nowrap;"><strong>FRM: 3a<br>Rev: 000</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">PEMERIKSAAN PENUNJANG PASIEN RAWAT JALAN</h2>
                        <div>Tanggal registrasi: <?= $rawatjalan['tanggal_registrasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 4px;">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; padding-top: 0.25cm; margin: 0;">PEMERIKSAAN PENUNJANG</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Dokter Pengirim
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $penunjang['dokter_pengirim'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Rujukan Dari
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $penunjang['rujukan_dari'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Pemeriksaan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $penunjang['pemeriksaan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Pemeriksaan Lainnya
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= ($penunjang['pemeriksaan_lainnya'] != NULL || $penunjang['pemeriksaan_lainnya'] != '') ? $penunjang['pemeriksaan_lainnya'] : 'TIDAK ADA'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Lokasi Pemeriksaan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $penunjang['lokasi_pemeriksaan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Hasil Pemeriksaan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $penunjang['hasil_pemeriksaan'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>