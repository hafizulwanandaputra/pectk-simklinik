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
                        <div style="white-space: nowrap;"><strong>FRM: 5a<br>Rev: 000</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">LEMBARAN PENGECEKAN KESELAMATAN PASIEN OPERASI</h2>
                        <div>Nomor <em>Booking</em>: <?= $operasi['nomor_booking']; ?></div>
                        <div>Tanggal operasi: <?= $operasi['tanggal_operasi'] . ' ' . $operasi['jam_operasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8.5pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $operasi['nama_pasien']; ?></div>
                            <div><?= $operasi['no_rm']; ?></div>
                            <div><?= $operasi['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 2px;">
                            <div><?= $operasi['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; margin-bottom: 4px; padding: 0;">
            <tr>
                <td style="width: 25%; vertical-align: top;">
                    <table class="full-border" style="width: 100%; margin-bottom: 4px; padding-right: 0.25cm; padding-left: 0.25cm;">
                        <thead>
                            <tr>
                                <th colspan="4" style="padding-top: 2px; line-height: 1.1;"><em>SIGN IN</em><br>SEBELUM TINDAKAN ANASTESI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; text-align: center; width: 50%;">
                                    Perawat
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; text-align: center; width: 50%;">
                                    Dokter Anestesi
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?php if ($operasi_safety_signin['ns_konfirmasi_identitas'] == 1): ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                    <?php endif; ?>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Pasien (penanggung Jawab)* konfirmasi identitas pasien, prosedur dan lokasi tindakan (termasuk dalam tindakan anastesi)
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?php if ($operasi_safety_signin['dr_konfirmasi_identitas'] == 1): ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['ns_marker_operasi'] ?>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <em>Marker</em> pada daerah operasi
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['dr_marker_operasi'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?php if ($operasi_safety_signin['ns_inform_consent_sesuai'] == 1): ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                    <?php endif; ?>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Formulir <em>informed consent</em> ditandatangani dan sesuai dengan identitas pada gelang pasien
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?php if ($operasi_safety_signin['dr_inform_consent_sesuai'] == 1): ?>
                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['ns_identifikasi_alergi'] ?>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Jenis alergi pada pasien telah diidentifikasi (termasuk lateks)
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['dr_identifikasi_alergi'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['ns_puasa'] ?>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Puasa
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <?= $operasi_safety_signin['dr_puasa'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <div><?= $operasi_safety_signin['ns_cek_lensa_intrakuler'] ?></div>
                                    <div style="display: flex; align-items: center; justify-content: center;">
                                        <?php if ($operasi_safety_signin['ns_konfirmasi_lensa'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> KONFIRM
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> KONFIRM
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Lensa intrakuer jenis dan ukuran telah tercatat dalam rekam medis, Jika ya, perawat mengkonfimasi ketersediaan lensa tersebut
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                </td>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    Perhatikan anastesi khusus termasuk <em>veorus trombo emolism</em>
                                </td>
                                <td style="padding-top: 2px; line-height: 1.1; text-align: center; width: 20%; font-size: 8.5pt;">
                                    <div><?= $operasi_safety_signin['dr_cek_anestesi_khusus'] ?></div>
                                    <div style="display: flex; align-items: center; justify-content: center;">
                                        <?php if ($operasi_safety_signin['dr_konfirmasi_anastersi'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> KONFIRM
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> KONFIRM
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="vertical-align: top; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Nama dokter anestesi:</div>
                                    <div><small><?= $operasi_safety_signin['nama_dokter_anastesi'] ?></small></div>
                                </td>
                                <td colspan="2" style="vertical-align: top; height: 2cm; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Tanda tangan:</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 25%; vertical-align: top;">
                    <table class="full-border" style="width: 100%; margin-bottom: 4px; padding-right: 0.25cm; padding-left: 0.25cm;">
                        <thead>
                            <tr>
                                <th colspan="2" style="padding-top: 2px; line-height: 1.1;"><em>TIME OUT</em><br>SEBELUM TINDAKAN BEDAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Apakah setiap anggota tim telah memperkenalkan diri baik nama maupun posisinya?</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['perkenalan_diri'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Telah dilakukan
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Telah dilakukan
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Dokter operator (dokter spesialis mata), dokter anestesi dan perawat melakukan cek identitas pasien dan rencana tindakan (<em>informed consent</em>) yang dilakukan secara verbal</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['cek_nama_mr'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Nama, nomor RM
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Nama, nomor RM
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['cek_rencana_tindakan'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Rencana tindakan
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Rencana tindakan
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['cek_marker'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Penanda (<em>marker</em>) operasi
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Penanda (<em>marker</em>) operasi
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Alergi</div>
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div><?= $operasi_safety_timeout['alergi'] ?></div>
                                        <div style="display: flex; align-items: center; justify-content: flex-start;">
                                            <?php if ($operasi_safety_timeout['lateks'] == 1): ?>
                                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Lateks
                                            <?php else : ?>
                                                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Lateks
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Maka yang tidak dilakukan tindakan diberikan proteksi/perlindungan</div>
                                    <div style="display: flex; align-items: start; justify-content: space-between;">
                                        <div><?= $operasi_safety_timeout['proteksi'] ?></div>
                                        <?php if ($operasi_safety_timeout['proteksi'] == 'YA'): ?>
                                            <div>
                                                <div>Jika ya,</div>
                                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                                    <?php if ($operasi_safety_timeout['proteksi_kasa'] == 1): ?>
                                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Kasa dengan plester
                                                    <?php else : ?>
                                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Kasa dengan plester
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; align-items: center; justify-content: flex-start;">
                                                    <?php if ($operasi_safety_timeout['proteksi_shield'] == 1): ?>
                                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> <em>Shield drop</em>
                                                    <?php else : ?>
                                                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> <em>Shield drop</em>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Dokter operator (dokter spesialis mata) menginformasikan</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['info_instrumen_ok'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Instrumen spesifik yang dibutuhkan
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Instrumen spesifik yang dibutuhkan
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['info_teknik_ok'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Langkah/teknik tidak rutin dilakukan yang harus diketahui tim operasi
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Langkah/teknik tidak rutin dilakukan yang harus diketahui tim operasi
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Perawat menginformasikan</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['info_steril_instrumen'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Sterilisasi dan instrumen operasi
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Sterilisasi dan instrumen operasi
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_timeout['info_kelengkapan_instrumen'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Kelengkapan instrumen operasi
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Kelengkapan instrumen operasi
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Apakah hal tersebut di bawah ini diperlukan untuk mengurangi resiko infeksi operasi?</div>
                                    <small>
                                        <ul style="padding: 0; margin: 0; margin-left: 0.5cm;">
                                            <li>Antibiotik profilaksis</li>
                                            <li>Kontrol gula darah</li>
                                        </ul>
                                    </small>
                                    <div><?= $operasi_safety_timeout['perlu_antibiotik_dan_guladarah'] ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Nama perawat sirkuler:</div>
                                    <div><small><?= $operasi_safety_timeout['nama_perawat'] ?></small></div>
                                    <div>Jam: <?= $operasi_safety_timeout['jam'] ?></div>
                                </td>
                                <td style="vertical-align: top; height: 2cm; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Tanda tangan:</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 25%; vertical-align: top;">
                    <table class="full-border" style="width: 100%; margin-bottom: 4px; padding-right: 0.25cm; padding-left: 0.25cm;">
                        <thead>
                            <tr>
                                <th colspan="2" style="padding-top: 2px; line-height: 1.1;"><em>SIGN OUT</em><br>PROSEDUR AKHIR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Dokter operator dokter spesialis mata/perawat melakukan konfirmasi dengan tim</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_signout['kelengkapan_instrumen'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Perhitungan jumlah instrumen sudah lengkap
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Perhitungan jumlah instrumen sudah lengkap
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span style="<?= ($operasi_safety_signout['spesimen_kultur'] == 'KULTUR') ? 'text-decoration: line-through;' : ''; ?>">Spesimen</span>/<span style="<?= ($operasi_safety_signout['spesimen_kultur'] == 'SPESIMEN') ? 'text-decoration: line-through;' : ''; ?>">Kultur</span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_signout['label_pasien'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Label pasien
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Label pasien
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Adakah masalah pada instrumen?</div>
                                    <div><?= $operasi_safety_signout['masalah_instrumen'] ?></div>
                                    <?php if ($operasi_safety_signout['masalah_instrumen'] == 'YA'): ?>
                                        <div><small><?= $operasi_safety_signout['keterangan_masalah'] ?></small></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 30%; font-size: 8.5pt;">
                                    <div>Instruksi khusus untuk menunjang pemulihan pasca operasi</div>
                                    <div style="display: flex; align-items: center; justify-content: flex-start;">
                                        <?php if ($operasi_safety_signout['instruksi_khusus'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Ada
                                        <?php else : ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/xmark-solid.png')) ?>" width="10px" style="padding-right: 2px;" alt=""> Tidak Ada
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($operasi_safety_signout['instruksi_khusus'] == 1): ?>
                                        <div><small><?= $operasi_safety_signout['keterangan_instruksi'] ?></small></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Nama dokter operator (spesialis mata):</div>
                                    <div><small><?= $operasi_safety_signout['nama_dokter_operator'] ?></small></div>
                                    <div>Jam: <?= $operasi_safety_signout['jam'] ?></div>
                                </td>
                                <td style="vertical-align: top; height: 2cm; padding-top: 2px; line-height: 1.1; padding-left: 4px; padding-right: 4px; width: 50%; font-size: 8.5pt;">
                                    <div>Tanda tangan:</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <p style="font-size: 9pt;">*anak (&lt;18 tahun)<br>Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>