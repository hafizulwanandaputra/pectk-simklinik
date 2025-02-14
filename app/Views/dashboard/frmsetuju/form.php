<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($form_persetujuan_tindakan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($form_persetujuan_tindakan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $form_persetujuan_tindakan['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"
$tanggalTindakan = $form_persetujuan_tindakan['tanggal_tindakan']; // Misalnya: "2025-01-14 15:23:45"

// Pastikan input adalah format tanggal dan waktu yang valid
$dateTime = new DateTime($tanggalRegistrasi);
$dateTime2 = new DateTime($tanggalTindakan);

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
$tanggalTindakanFormatted = $tanggalFormatter->format($dateTime2);

// Format waktu
$waktuFormatted = $dateTime->format('H.i');
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
            height: calc(100vh - 5.25cm);
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
                        <div style="white-space: nowrap;"><strong>FRM: 4a<br>Rev: 003</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">FORMULIR PERSETUJUAN TINDAKAN KEDOKTERAN</h2>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $form_persetujuan_tindakan['nama_pasien']; ?></div>
                            <div><?= $form_persetujuan_tindakan['no_rm']; ?></div>
                            <div><?= $form_persetujuan_tindakan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 4px;">
                            <div><?= $form_persetujuan_tindakan['nomor_registrasi']; ?></div>
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
                                Dokter Pelaksana Tindakan
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $form_persetujuan_tindakan['dokter_pelaksana'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Pemberi Informasi
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $form_persetujuan_tindakan['pemberi_informasi'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="full-border" style="width: 100%; margin-bottom: 4px; font-size: 9pt;">
                    <thead>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">No</th>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Jenis Informasi</th>
                            <th style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Isi Informasi</th>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Centang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                1
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Diagnosa (WD dan atau DD)</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_diagnosa'])) ? $form_persetujuan_tindakan['info_diagnosa'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_diagnosa'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                2
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Dasar Diagnosis</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_dasar_diagnosis'])) ? $form_persetujuan_tindakan['info_dasar_diagnosis'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_dasar_diagnosis'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                3
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Tindakan Kedokteran</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_tindakan'])) ? $form_persetujuan_tindakan['info_tindakan'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_tindakan'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                4
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Indikasi Tindakan</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_indikasi'])) ? $form_persetujuan_tindakan['info_indikasi'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_indikasi'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                5
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Tata Cara</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_tatacara'])) ? $form_persetujuan_tindakan['info_tatacara'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_tatacara'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                6
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Tata Cara</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_tujuan'])) ? $form_persetujuan_tindakan['info_tujuan'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_tujuan'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                7
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Risiko</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_resiko'])) ? $form_persetujuan_tindakan['info_resiko'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_resiko'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                8
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Komplikasi</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_komplikasi'])) ? $form_persetujuan_tindakan['info_komplikasi'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_komplikasi'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                9
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Prognosis</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_prognosis'])) ? $form_persetujuan_tindakan['info_prognosis'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_prognosis'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                10
                            </th>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Alternatif dan Risiko</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_alternatif'])) ? $form_persetujuan_tindakan['info_alternatif'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_alternatif'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; white-space: nowrap;">
                                <strong>Lain-lain</strong>
                            </td>
                            <td style="width: 100%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">
                                <?= (!empty($form_persetujuan_tindakan['info_lainnya'])) ? $form_persetujuan_tindakan['info_lainnya'] : '<em>Tidak ada</em>'; ?>
                            </td>
                            <td style="width: 0%; padding-top: 2px; padding-bottom: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if (!empty($form_persetujuan_tindakan['info_lainnya'])): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>
                    <small>
                        Dengan ini saya menyatakan bahwa saya telah menerangkan hal-hal diatas secara benar dan jelas serta memberikan kesempatan bertanya dan/atau berdiskusi.
                    </small>
                </p>
                <p>
                    <small>
                        Dengan ini saya menyatakan bahwa saya telah menerima informasi sebagaimana diatas yang saya beri tanda tangan atau paraf di kolom kananya dan telah memahaminya.
                    </small>
                </p>
                <p>
                    <small>
                        Bila pasien tidak kompeten atau tidak mau menerima informasi, maka penerima informasi adalah wali atau keluarga terdekat.
                    </small>
                </p>
                <h4 class="margin: 0;">PERSETUJUAN TINDAKAN KEDOKTERAN</h4>
                <small>
                    <div>Yang bertanda tangan di bawah ini, saya:</div>
                    <table class="table" style="width: 100%; margin-bottom: 4px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Nama
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['penerima_informasi'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Jenis Kelamin
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?php if ($form_persetujuan_tindakan['jenis_kelamin'] == 'L') : ?>
                                        Laki-Laki
                                    <?php elseif ($form_persetujuan_tindakan['jenis_kelamin'] == 'P') : ?>
                                        Perempuan
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Tanggal lahir
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['pererima_tanggal_lahir'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Jenis Kelamin
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?php if ($form_persetujuan_tindakan['penerima_jenis_kelamin'] == 'L') : ?>
                                        Laki-Laki
                                    <?php elseif ($form_persetujuan_tindakan['penerima_jenis_kelamin'] == 'P') : ?>
                                        Perempuan
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Alamat
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['penerima_alamat'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Hubungan dengan pasien
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= ($form_persetujuan_tindakan['penerima_hubungan'] == 'KELUARGA') ? $form_persetujuan_tindakan['keterangan_hubungan'] : $form_persetujuan_tindakan['penerima_hubungan']; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div>Dengan ini menyatakan <strong>persetujuan</strong> untuk dilakukan tindakan <?= $form_persetujuan_tindakan['tindakan_kedoteran'] ?> pada tanggal <?= $tanggalTindakanFormatted ?> terhadap:</div>
                    <table class="table" style="width: 100%; margin-bottom: 4px;">
                        <tbody>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Nama
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['nama_pasien'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Tanggal lahir
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['tanggal_lahir'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Jenis Kelamin
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?php if ($form_persetujuan_tindakan['jenis_kelamin'] == 'L') : ?>
                                        Laki-Laki
                                    <?php elseif ($form_persetujuan_tindakan['jenis_kelamin'] == 'P') : ?>
                                        Perempuan
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    Alamat
                                </td>
                                <td style="width: 0%; vertical-align: top;">
                                    :
                                </td>
                                <td style="width: 60%; vertical-align: top;">
                                    <?= $form_persetujuan_tindakan['alamat'] ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </small>
                <p>
                    <small>
                        Saya memahami perlunya dan manfaat tindakan sebagaimana telah dijelaskan seperti di atas kepada saya, termasuk risiko dan komplikasi yang mungkin timbul.
                    </small>
                </p>
                <p>
                    <small>
                        Saya juga menyadari bahwa dokter melakukan suatu upaya dan karena ilmu kedokteran bukanlah ilmu pasti, maka keberhasilan tindakan kedokteran bukanlah keniscayaan, melainkan saya bergantung kepada izin Tuhan Yang Maha Esa.
                    </small>
                </p>
                <small>
                    <table class="table" style="width: 100%; margin-bottom: 4px;">
                        <tbody>
                            <tr>
                                <td style="width: 33.33%; text-align: center; vertical-align: top; padding-bottom: 1.3cm;">
                                    <div>Teluk Kuantan, <?= $tanggalFormatted ?> pukul <?= $waktuFormatted ?><br>Yang menyatakan</div>
                                </td>
                                <td style="width: 33.33%; text-align: center; vertical-align: top; padding-bottom: 1.3cm;">
                                    <div>Saksi I</div>
                                </td>
                                <td style="width: 33.33%; text-align: center; vertical-align: top; padding-bottom: 1.3cm;">
                                    <div>Saksi II</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 33.33%; text-align: center; vertical-align: top;">
                                    <div><?= $form_persetujuan_tindakan['penerima_informasi'] ?></div>
                                </td>
                                <td style="width: 33.33%; text-align: center; vertical-align: top;">
                                    <div><?= $form_persetujuan_tindakan['nama_saksi_1'] ?></div>
                                </td>
                                <td style="width: 33.33%; text-align: center; vertical-align: top;">
                                    <div><?= $form_persetujuan_tindakan['nama_saksi_2'] ?></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </small>
            </div>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>