<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal sekarang
$sekarang = new DateTime();

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $sekarang->diff($tanggal_lahir);
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
            font-size: 9pt;
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
                        <div style="white-space: nowrap;"><strong>FRM: 2a hal 3<br>Rev: 006</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">ASESMEN PASIEN RAWAT JALAN</h2>
                        <div>Diperiksa pada: <?= $asesmen['tanggal_registrasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: top; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <center>
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <h3 style="padding: 0.25cm; margin: 0;">ANAMNESIS (S):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td colspan="2" style="width: 100%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>KELUHAN UTAMA:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['keluhan_utama'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT SEKARANG:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['riwayat_penyakit_sekarang'] ?></div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT DAHULU:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['riwayat_penyakit_dahulu'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT DALAM KELUARGA:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['riwayat_penyakit_keluarga'] ?></div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>RIWAYAT PENGOBATAN:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['riwayat_pengobatan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%; vertical-align: top; padding: 0.25cm;">
                            <div><strong>RIWAYAT PEKERJAAN, SOSIAL, EKONOMI, KEJIWAAN, DAN KEBIASAAN:</strong></div>
                            <div style="padding-left: 0.5cm;"><?= $asesmen['riwayat_sosial_pekerjaan'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding: 0.25cm; margin: 0;">PEMERIKSAAN UMUM:</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <table class="table" style="width: 100%; margin-bottom: 4px; border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Kesadaran
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['kesadaran'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Tekanan Darah
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['tekanan_darah'] ?> mmHg
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Nadi
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['nadi'] ?> ×/menit
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Suhu
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['suhu'] ?> °C
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Suhu
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['pernapasan'] ?> ×/menit
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 0.25cm;">
                            <table class="table" style="width: 100%; margin-bottom: 4px; border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Keadaan Umum
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['keadaan_umum'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Kesadaran Mental
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['kesadaran_mental'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Alergi
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['alergi'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Ket. Alergi
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['alergi_keterangan'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; vertical-align: top; padding: 0;">
                                            Penyakit Lainnya
                                        </td>
                                        <td style="width: 0%; vertical-align: top; padding: 0;">
                                            :
                                        </td>
                                        <td style="width: 60%; vertical-align: top; padding: 0;">
                                            <?= $asesmen['sakit_lainnya'] ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>