<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($operasi['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($operasi['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $operasi['tanggal_registrasi']; // Misalnya: "2025-01-14 15:23:45"

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
            font-size: 8pt;
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
                        <div style="white-space: nowrap;"><strong>FRM: 5c<br>Rev: 001</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">CATATAN KEPERAWATAN PRA-OPERASI</h2>
                        <div>Nomor <em>Booking</em>: <?= $operasi['nomor_booking']; ?></div>
                        <div>Tanggal operasi: <?= $operasi['tanggal_operasi'] . ' ' . $operasi['jam_operasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: top; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $operasi['nama_pasien']; ?></div>
                            <div><?= $operasi['no_rm']; ?></div>
                            <div><?= $operasi['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode">
                            <div><?= $operasi['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            DPJP
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi['dokter_operator'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Perawat Pra Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['perawat_praoperasi'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jenis Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['jenis_operasi'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; padding-top: 0.25cm; margin: 0;">A. CATATAN KEPERAWATAN PRA OPERASI</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>1.</strong>
                        </td>
                        <td colspan="3" style="width: 100%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Tanda-tanda Vital</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Suhu
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_vital_suhu'] ?>°C
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Nadi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_vital_nadi'] ?>×/menit
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Pernapasan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_vital_rr'] ?>×/menit
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Tekanan Darah
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_vital_td'] ?> mmHg
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>2.</strong>
                        </td>
                        <td colspan="3" style="width: 100%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Status Mental</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Kesadaran
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_mental'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>3.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Riwayat Penyakit</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_riwayat_sakit'] ?><?= ($operasi_pra['ctt_riwayat_sakit_lain'] == NULL) ? '' : ', ' . $operasi_pra['ctt_riwayat_sakit_lain'];  ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>4.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Pengobatan Saat Ini</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_pengobatan_sekarang'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>5.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Alat Bantu yang Digunakan</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_alat_bantu'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>6.</strong>
                        </td>
                        <td colspan="3" style="width: 100%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Operasi Sebelumnya</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jenis Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_operasi_jenis'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Tanggal Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_operasi_tanggal'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Lokasi Operasi
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_operasi_lokasi'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>7.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Alergi</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_alergi'] ?> <?= ($operasi_pra['ctt_alergi'] == 'TIDAK' || $operasi_pra['ctt_alergi'] == NULL) ? '' : '(' . $operasi_pra['ctt_alergi_jelaskan'] . ')';  ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>8.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Hasil Laboratorium</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <table class="table" style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_hb'] == 1): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>HB</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_bt'] == 1): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>BT</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_ctaptt'] == 1): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>CT/APTT</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_goldarah'] == 1): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>GOLONGAN DARAH</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_urin'] == 1): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>URIN</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 0%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                            <div style="width: 0.2cm; height: 0.2cm; text-align: center;">
                                                <?php if ($operasi_pra['ctt_lab_lainnya'] != NULL): ?>
                                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="100%" alt="">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="width: 100%; vertical-align: middle; padding: 0;">
                                            <div>LAINNYA: <?= ($operasi_pra['ctt_lab_lainnya'] == NULL) ? '' : $operasi_pra['ctt_lab_lainnya']; ?></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>9.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Haid/menstruasi?</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= ($operasi['jenis_kelamin'] == 'L') ? 'Pasien laki-laki' : $operasi_pra['ctt_haid'];  ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>10.</strong>
                        </td>
                        <td style="width: 40%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <strong>Perhatian khusus terkait budaya dan kepercayaan?</strong>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 60%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $operasi_pra['ctt_kepercayaan'];  ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; padding-top: 0.25cm; margin: 0;">B. <em>CHECKLIST</em> PERSIAPAN PASIEN PRA OPERASI</h3>
            <div style="padding-right: 0.25cm; padding-top: 0.25cm;">
                <table class="full-border" style="width: 100%; margin-bottom: 4px; padding-right: 0.25cm; padding-left: 0.25cm;">
                    <tr>
                        <th style="padding-top: 4px; line-height: 1.0; width: 0%;">No</th>
                        <th style="padding-top: 4px; line-height: 1.0; width: 50%;">Hal-hal yang harus diperhatikan</th>
                        <th style="padding-top: 4px; line-height: 1.0; width: 0%;">Cek/Isi</th>
                        <th style="padding-top: 4px; line-height: 1.0; width: 0%;">No</th>
                        <th style="padding-top: 4px; line-height: 1.0; width: 50%;">Hal-hal yang harus diperhatikan</th>
                        <th style="padding-top: 4px; line-height: 1.0; width: 0%;">Cek/Isi</th>
                    </tr>

                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">1</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil biometri</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_biometri'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">10</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil Foto Fundus</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_foto_fundus'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">2</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil retinometri</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_retinometri'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">11</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil USG</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_usg'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">3</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil laboratorium (labor lengkap/GDS)</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_labor'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">12</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Melepas perhiasan</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_perhiasan'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">4</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil radiologi</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_radiologi'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">13</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Tanda tangan <em>informed concent</em></td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_ttd'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">5</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Puasa</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_puasa'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">14</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Cuci muka + ganti pakaian</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_cuci'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">6</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Instruksi khusus dari dokter</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_instruksi'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">15</td>
                        <td style="padding-top: 4px; line-height: 1.0;"><em>Sign mark</em> + gelang pasien</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_mark'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">7</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Lensa</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_lensa'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">16</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Pantocain 2%
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_pantocain'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="3" style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">8</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Rontgen</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_rotgen'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">17</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Efrisel I
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_efrisel1'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">ECG, Usia > 40 Tahun</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_rotgen_usia'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">18</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Efrisel II
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_efrisel2'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">Hasil konsul dokter anak/<em>internist</em>/retina</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_rotgen_konsul'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">19</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Midriatil I
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_midriatil1'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="5" style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">9</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Cek File: Hepatitis, DM</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_penyakit'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">20</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Midriatil II
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_midriatil2'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">Jika Hepatitis(+), jadwal paling akhir</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_hepatitis_akhir'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">21</td>
                        <td style="padding-top: 4px; line-height: 1.0;">
                            Tetes Midriatil III
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tetes_midriatil3'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">Penyakit lainnya</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_penyakit_lainnya'] ?>
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">22</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Makan pagi/siang</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_makan'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">Tekanan darah</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_tekanan_darah'] ?> mmHg
                        </td>
                        <td rowspan="2" style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">23</td>
                        <td style="padding-top: 4px; line-height: 1.0;">Obat-obatan sebelumnya</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?php if ($operasi_pra['cek_obat'] == 1): ?>
                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 4px; line-height: 1.0;">Berat badan</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_berat_badan'] ?> kg
                        </td>
                        <td style="padding-top: 4px; line-height: 1.0;">Jenis obat-obatan</td>
                        <td style="padding-top: 4px; line-height: 1.0; text-align: center; white-space: nowrap;">
                            <?= $operasi_pra['cek_jenis_obat'] ?> kg
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>