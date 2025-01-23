<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $rawatjalan['tanggal_registrasi']; // Misalnya: "2025-01-14 15:23:45"

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
            height: 22.95cm;
            overflow: hidden;
            padding: 0cm;
        }

        .border-bottom-right {
            border-bottom: 2px dotted black;
            border-right: 2px dotted black;
        }

        .border-bottom-left {
            border-bottom: 2px dotted black;
            border-left: 2px dotted black;
        }

        .border-top-right {
            border-top: 2px dotted black;
            border-right: 2px dotted black;
        }

        .border-top-left {
            border-top: 2px dotted black;
            border-left: 2px dotted black;
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
                        <div>Tanggal registrasi: <?= $rawatjalan['tanggal_registrasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: top; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">ANAMNESIS (S):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td colspan="2" style="width: 100%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>KELUHAN UTAMA:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['keluhan_utama'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT SEKARANG:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['riwayat_penyakit_sekarang'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT DAHULU:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['riwayat_penyakit_dahulu'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>RIWAYAT PENYAKIT DALAM KELUARGA:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['riwayat_penyakit_keluarga'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>RIWAYAT PENGOBATAN:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['riwayat_pengobatan'] ?></div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <div><strong>RIWAYAT PEKERJAAN, SOSIAL, EKONOMI, KEJIWAAN, DAN KEBIASAAN:</strong></div>
                            <div style="padding-left: 0.5cm; overflow: hidden; font-size: 8pt;"><?= $asesmen['riwayat_sosial_pekerjaan'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">PEMERIKSAAN UMUM:</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
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
                                            <?= $asesmen['nadi'] ?>×/menit
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
                                            <?= $asesmen['suhu'] ?>°C
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
                                            <?= $asesmen['pernapasan'] ?>×/menit
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
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
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">PEMERIKSAAN FISIK (O):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px; border-collapse: collapse; padding-left: 0.25cm; padding-right: 0.25cm; border: 1px solid black;">
                <tr>
                    <th style="width: 0%; text-align: center; vertical-align: middle; border: 1px solid black;"></th>
                    <th style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;">OD</th>
                    <th style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;">OS</th>
                </tr>
                <tr>
                    <th style="width: 0%; text-align: center; vertical-align: middle; border: 1px solid black; white-space: nowrap;">Visus UCVA</th>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['od_ucva'] ?></td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['os_ucva'] ?></td>
                </tr>
                <tr>
                    <th style="width: 0%; text-align: center; vertical-align: middle; border: 1px solid black; white-space: nowrap;">Visus BCVA</th>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['od_bcva'] ?></td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['os_bcva'] ?></td>
                </tr>
                <tr>
                    <th style="width: 0%; text-align: center; vertical-align: middle; border: 1px solid black; white-space: nowrap;">Tono</th>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['tono_od'] ?></td>
                    <td style="width: 50%; text-align: center; vertical-align: middle; border: 1px solid black;"><?= $asesmen['tono_os'] ?></td>
                </tr>
            </table>

            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">DIAGNOSIS MEDIS (A):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px; font-size: 8pt; padding-left: 0.25cm; padding-right: 0.25cm;">
                <tbody>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden;"></td>
                        <td colspan="3" style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            ICD 10
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['diagnosa_medis_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icdx_kode_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['diagnosa_medis_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icdx_kode_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['diagnosa_medis_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icdx_kode_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['diagnosa_medis_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icdx_kode_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['diagnosa_medis_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icdx_kode_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">TINDAKAN (P):</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px; font-size: 8pt; padding-left: 0.25cm; padding-right: 0.25cm;">
                <tbody>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden;"></td>
                        <td colspan="3" style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            ICD 9
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['terapi_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icd9_kode_1'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['terapi_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icd9_kode_2'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['terapi_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icd9_kode_3'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['terapi_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icd9_kode_4'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 85%; vertical-align: top; white-space: nowrap; overflow: hidden; border-bottom: 1px dotted black;">
                            <?= $asesmen['terapi_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            (
                        </td>
                        <td style="width: 15%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center; border-bottom: 1px dotted black;">
                            <?= $asesmen['icd9_kode_5'] ?>
                        </td>
                        <td style="width: 0%; vertical-align: top; white-space: nowrap; overflow: hidden; text-align: center;">
                            )
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;"></td>
                        <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;">
                            <div>Tanggal <?= $tanggalFormatted ?> pukul <?= $waktuFormatted ?><br>DPJP</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top;"></td>
                        <td style="width: 50%; text-align: center; vertical-align: top;">
                            <div><?= $asesmen['nama_dokter'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>