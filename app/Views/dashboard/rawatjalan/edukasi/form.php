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
            border: 1px solid black;
            height: 22.5cm;
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
                        <div style="white-space: nowrap;"><strong>FRM: 2a hal 2<br>Rev: 001</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">FORMULIR INFORMASI DAN EDUKASI PASIEN DAN KELUARGA TERINTEGRASI RAWAT JALAN</h2>
                        <div>Tanggal registrasi: <?= $rawatjalan['tanggal_registrasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 2px;">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; padding-top: 0.25cm; margin: 0;">PENGKAJIAN KEBUTUHAN INFORMASI DAN EDUKASI</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Bahasa
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= ($edukasi['bahasa'] == 'LAINNYA') ? $edukasi['bahasa_lainnya'] : $edukasi['bahasa']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Kebutuhan Penerjemah
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['penterjemah'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Pendidikan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['pendidikan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Baca dan Tulis
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['baca_tulis'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Pilihan Cara Belajar
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['cara_belajar'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Hambatan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['hambatan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Keyakinan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['keyakinan'] ?> <?= ($edukasi['keyakinan'] == 'KHUSUS') ? '(' . $edukasi['keyakinan_khusus'] . ')' : ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Topik Pembelajaran
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['topik_pembelajaran'] ?> <?= ($edukasi['topik_pembelajaran'] == 'Lainnya') ? '(' . $edukasi['topik_lainnya'] . ')' : ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Kesediaan pasien dan keluarga untuk menerima informasi dan edukasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $edukasi['kesediaan_pasien'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="padding-left: 0.25cm; padding-right: 0.25cm;">
                <table class="table" style="width: 100%; margin-bottom: 4px; border: 1px solid black; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Tanggal
                            </th>
                            <th rowspan="2" style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Unit
                            </th>
                            <th rowspan="2" style="width: 50%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Informasi/Edukasi Tentang
                            </th>
                            <th colspan="2" style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Edukator
                            </th>
                            <th colspan="2" style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Sasaran (Pasien / Keluarga / Lainnya)
                            </th>
                            <th rowspan="2" style="width: 50%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Evaluasi
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Nama dan Profesi
                            </th>
                            <th style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                TTD
                            </th>
                            <th style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                Nama
                            </th>
                            <th style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                TTD
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($edukasi_evaluasi as $evaluasi) : ?>
                            <tr>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black; white-space: nowrap;">
                                    <?= date('Y-m-d', strtotime($evaluasi['waktu_dibuat'])) ?>
                                </td>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?= $evaluasi['unit'] ?>
                                </td>
                                <td style="width: 50%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?= $evaluasi['informasi_edukasi'] ?>
                                </td>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?= $evaluasi['nama_edukator'] ?> (<?= $evaluasi['profesi_edukator'] ?>)
                                </td>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?php if ($evaluasi['tanda_tangan_edukator'] != NULL) : ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'uploads/ttd_edukator_evaluasi/' . $evaluasi['tanda_tangan_edukator'])) ?>" width="36px" alt="">
                                    <?php endif; ?>
                                </td>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?= $evaluasi['nama_pasien_keluarga'] ?>
                                </td>
                                <td style="width: 0%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?php if ($evaluasi['tanda_tangan_edukator'] != NULL) : ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'uploads/ttd_pasien_evaluasi/' . $evaluasi['tanda_tangan_pasien'])) ?>" width="36px" alt="">
                                    <?php endif; ?>
                                </td>
                                <td style="width: 50%; vertical-align: middle; padding-top: 2px; padding-left: 0.125cm; padding-right: 0.125cm; border: 1px solid black;">
                                    <?= $evaluasi['evaluasi'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>