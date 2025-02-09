<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($lp_operasi_katarak['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($lp_operasi_katarak['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

$tanggalRegistrasi = $lp_operasi_katarak['waktu_dibuat']; // Misalnya: "2025-01-14 15:23:45"

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

        .box {
            border: 1px solid black;
            height: 23.4cm;
            overflow: hidden;
            padding: 0cm;
            font-size: 10pt;
        }

        .box-long {
            border: 1px solid black;
            height: 26.9cm;
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
                        <div style="white-space: nowrap;"><strong>FRM: 5f<br>Rev: 001</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">LAPORAN OPERASI KATARAK</h2>
                        <div>Tanggal operasi: <?= $lp_operasi_katarak['tanggal_operasi'] . ' ' . $lp_operasi_katarak['jam_operasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $lp_operasi_katarak['nama_pasien']; ?></div>
                            <div><?= $lp_operasi_katarak['no_rm']; ?></div>
                            <div><?= $lp_operasi_katarak['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode" style="padding-top: 4px;">
                            <div><?= $lp_operasi_katarak['nomor_registrasi']; ?></div>
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
                                <?= $lp_operasi_katarak['mata'] ?>
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
                                <?= $lp_operasi_katarak['operator'] ?>
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
                                <?= $lp_operasi_katarak['lama_operasi'] ?> menit
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
                                <?= $lp_operasi_katarak['diagnosis'] ?>
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
                                <?= $lp_operasi_katarak['asisten'] ?>
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
                                <?= $lp_operasi_katarak['jenis_operasi'] ?>
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
                                <?= $lp_operasi_katarak['jenis_anastesi'] ?>
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
                                <?= $lp_operasi_katarak['dokter_anastesi'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%;">
                    <td style="vertical-align: top; width: 50%; padding-right: 0.25cm;">
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Anestesi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Retrobulbar</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_retrobulbar'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Peribulbar</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_peribulber'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Topikal</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_topikal'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Subtenom</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_subtenom'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Lidocain 2%</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_lidocain_2'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Marcain 0,5%</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['anastesi_marcain_05'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Lainnya: <?= $lp_operasi_katarak['anastesi_lainnya'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Peritomi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Basis Forniks</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['peritomi_basis_forniks'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Basis Limbus</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['peritomi_basis_limbus'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Superonasal</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_superonasal'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Superior</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_superior'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Supertemporal</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_supertemporal'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Lainnya: <?= $lp_operasi_katarak['anastesi_lainnya'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Lokasi Insisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Kornea</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_insisi_kornea'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Limbus</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_insisi_limbus'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Skelera</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_insisi_skelera'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Skelera Tunnel</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_insisi_skeleratunnel'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Side Port</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['lokasi_insisi_sideport'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table" style="width: 100%; margin-bottom: 4px;">
                            <tbody>
                                <tr>
                                    <td style="width: 40%; vertical-align: top;">
                                        Ukuran Insisi
                                    </td>
                                    <td style="width: 0%; vertical-align: top;">
                                        :
                                    </td>
                                    <td style="width: 60%; vertical-align: top;">
                                        <?= $lp_operasi_katarak['ukuran_inisiasi'] ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Alat Insisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Jarum</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['alat_insisi_jarum'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Crescent</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['alat_insisi_crescent'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Diamond</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['alat_insisi_diamond'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Kapsulotomi Anterior</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Can Opener</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['capsulectomy_canopener'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Envelope</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['capsulectomy_envelope'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">CCC</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['capsulectomy_ccc'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="vertical-align: top; width: 50%; padding-left: 0.25cm;">
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Ekstraksi Lensa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">ICCE</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_icce'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">ECCE</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_ecce'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">SUCEA</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_sucea'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Phaco</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_phaco'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">CLE</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_cle'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">AI</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['ekstraksi_lenca_ai'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Tindakan Tambahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Sphincter Otomy</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['tindakan_sphincter'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Jahitan Iris</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['tindakan_jahitan_iris'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Virektomi</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?= (!empty($lp_operasi_katarak['tindakan_virektomi'])) ? $lp_operasi_katarak['tindakan_virektomi'] . ' cm' : ''; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Kapsulotomi Post</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['tindakan_kapsulotomi_post'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;"><em>Synechiolysis</em></td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['tindakan_sinechiolyssis'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Cairan Irigasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">RI</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_irigasi_ri'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">BSS</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_irigasi_bss'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Lainnya: <?= $lp_operasi_katarak['cairan_irigasi_lainnya'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Penanaman</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Diputar</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['penanaman_diputar'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Tidak Diputar</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['penanaman_tidak_diputar'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Jenis LIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Dilipat</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['jenis_dilipat'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Tidak Dilipat</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['jenis_tidak_dilipat'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Posisi LIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Vertikal</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['posisi_vertikal'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Horizontal</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['posisi_horizontal'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Miring</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['posisi_miring'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Cairan Viskoelastis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Healon</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_viscoelastik_healon'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Viscoat</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_viscoelastik_viscoat'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Amvisca</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_viscoelastik_amvisca'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Healon 5</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_viscoelastik_healon_5'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Rohtovisc</td>
                                    <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                        <?php if ($lp_operasi_katarak['cairan_viscoelastik_rohtovisc'] == 1): ?>
                                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </table>
            </div>
        </div>
        <div class="box-long">
            <div style="padding-right: 0.25cm; padding-left: 0.25cm; padding-top: 0.25cm;">
                <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                    <thead>
                        <tr>
                            <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Benang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Vicryl 8-0</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['benang_vicryl_8_0'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Rthylon 10-0</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['benang_ethylon_10_0'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 40%; vertical-align: top;">
                                Jumlah Jahitan
                            </td>
                            <td style="width: 0%; vertical-align: top;">
                                :
                            </td>
                            <td style="width: 60%; vertical-align: top;">
                                <?= $lp_operasi_katarak['jumlah_jahitan'] ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                    <thead>
                        <tr>
                            <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Tio Pra Bedah</th>
                        </tr>
                        <tr>
                            <th style="width: 50%; padding-top: 2px; line-height: 1.0;">OD</th>
                            <th style="width: 50%; padding-top: 2px; line-height: 1.0;">OS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 50%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap;"><?= (!empty($lp_operasi_katarak['prabedah_od'])) ? $lp_operasi_katarak['prabedah_od'] . ' mmHg' : ''; ?></td>
                            <td style="width: 50%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap;"><?= (!empty($lp_operasi_katarak['prabedah_os'])) ? $lp_operasi_katarak['prabedah_os'] . ' mmHg' : ''; ?></td>
                        </tr>
                    </tbody>
                </table>
                <table class="full-border" style="width: 100%; margin-bottom: 4px;">
                    <thead>
                        <tr>
                            <th colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0;">Komplikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Tidak Ada</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['komplikasi_tidak_ada'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Ada</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['komplikasi_ada'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Prolaps Vitreus</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['komplikasi_prolaps'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Pendarahan</td>
                            <td style="width: 0%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm; text-align: center; white-space: nowrap; width: 0.5cm; min-width: 0.5cm;">
                                <?php if ($lp_operasi_katarak['komplikasi_pendarahan'] == 1): ?>
                                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/check-solid.png')) ?>" width="10px" alt="">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width: 100%; padding-top: 2px; line-height: 1.0; padding-right: 0.1cm; padding-left: 0.1cm;">Lainnya: <?= $lp_operasi_katarak['komplikasi_lainnya'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 100%; vertical-align: top;">
                                <div><strong>Tindakan:</strong></div>
                                <div style="padding-left: 0.5cm;"><?= nl2br($lp_operasi_katarak['tindakan_operasi']) ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width: 100%; margin-bottom: 4px;">
                    <tbody>
                        <tr>
                            <td style="width: 100%; vertical-align: top;">
                                <div><strong>Terapi Pasca Bedah:</strong></div>
                                <div style="padding-left: 0.5cm;"><?= nl2br($lp_operasi_katarak['terapi_pascabedah']) ?></div>
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
                                <div><?= $lp_operasi_katarak['operator'] ?></div>
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