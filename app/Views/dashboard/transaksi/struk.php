<?php

use CodeIgniter\I18n\Time;

$tanggal = Time::parse($transaksi['tgl_transaksi']);
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
            margin: 0.5cm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
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
                        <div style="font-size: 18pt;">KUITANSI</div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <?php if ($transaksi['dokter'] == 'Resep Luar') : ?>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Nama Pasien:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= ($transaksi['nama_pasien'] == NULL) ? '<em>Anonim</em>' : $transaksi['nama_pasien']; ?></div>
                        </td>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Nomor Kuitansi:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['no_kwitansi'] ?></div>
                        </td>
                    <?php else : ?>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Nomor RM:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['no_rm'] ?></div>
                        </td>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Nomor Kuitansi:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['no_kwitansi'] ?></div>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php if ($transaksi['dokter'] == 'Resep Luar') : ?>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>No. Telp:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['telpon'] ?></div>
                        </td>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Tanggal/Waktu:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $tanggal ?></div>
                        </td>
                    <?php else : ?>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Nama Pasien:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= ($transaksi['nama_pasien'] == NULL) ? '<em>Anonim</em>' : $transaksi['nama_pasien']; ?></div>
                        </td>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Tanggal/Waktu:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $tanggal ?></div>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <?php if ($transaksi['dokter'] != 'Resep Luar') : ?>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>No. Telp:</div>
                        </td>
                        <td style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['telpon'] ?></div>
                        </td>
                        <td style="width: 15%; vertical-align: top; padding: 0;">
                            <div>Dokter:</div>
                        </td>
                        <td rowspan="2" style="width: 35%; vertical-align: top; padding: 0;">
                            <div><?= $transaksi['dokter'] ?></div>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td style="width: 15%; vertical-align: top; padding: 0;">
                        <div>Alamat:</div>
                    </td>
                    <td style="width: 35%; vertical-align: top; padding: 0;" colspan="2">
                        <div><?= $transaksi['alamat'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table listtable" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <?php if ($transaksi['dokter'] != 'Resep Luar') : ?>
                    <tr class="outline-border">
                        <th colspan="8" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">Tindakan</th>
                    </tr>
                    <tr class="outline-border">
                        <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                            No
                        </th>
                        <th class="outline-border" style="width: 60%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                            Deskripsi
                        </th>
                        <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                            Qty
                        </th>
                        <th colspan="2" class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                            Biaya
                        </th>
                        <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                            Disc %
                        </th>
                        <th colspan="2" class="outline-border" style="width: 40%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                            Sub Total
                        </th>
                    </tr>
                    <?php
                    $no_layanan = 1; // Inisialisasi variabel untuk penomoran
                    foreach ($layanan as $list) : ?>
                        <tr class="outline-border">
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                                <?= $no_layanan++ ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px;">
                                <?= $list['layanan']['nama_layanan'] ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= $list['qty_transaksi'] ?>
                            </td>
                            <td class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                                Rp
                            </td>
                            <td class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= number_format($list['harga_transaksi'], 0, ',', '.') ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= $list['diskon'] ?>
                            </td>
                            <td class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                                Rp
                            </td>
                            <td class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= number_format(($list['harga_transaksi'] * $list['qty_transaksi']) * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                        <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                            Sub Total
                        </th>
                        <th class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                            Rp
                        </th>
                        <th class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                            <?= number_format($total_layanan, 0, ',', '.') ?>
                        </th>
                    </tr>
                <?php endif; ?>
                <tr class="outline-border">
                    <th colspan="8" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">Obat dan Alkes</th>
                </tr>
                <tr class="outline-border">
                    <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                        No
                    </th>
                    <th class="outline-border" style="width: 60%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                        Deskripsi
                    </th>
                    <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                        Qty
                    </th>
                    <th colspan="2" class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                        Biaya
                    </th>
                    <th class="outline-border" style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Disc %
                    </th>
                    <th colspan="2" class="outline-border" style="width: 40%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Sub Total
                    </th>
                </tr>
                <?php
                $no_obat_alkes = 1; // Inisialisasi variabel untuk penomoran
                foreach ($obatalkes as $list) : ?>
                    <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                        <tr class="outline-border">
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                                <?= $no_obat_alkes++ ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px;">
                                <?= $resep['nama_obat'] ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= $resep['jumlah'] ?>
                            </td>
                            <td class="outline-border-rigth" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                                Rp
                            </td>
                            <td class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= number_format($resep['harga_satuan'], 0, ',', '.') ?>
                            </td>
                            <td class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= $list['diskon'] ?>
                            </td>
                            <td class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                                Rp
                            </td>
                            <td class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                                <?= number_format(($resep['harga_satuan'] * $resep['jumlah']) * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                    <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Sub Total
                    </th>
                    <th class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                        Rp
                    </th>
                    <th class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= number_format($total_obatalkes, 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                    <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Grand Total
                    </th>
                    <th class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                        Rp
                    </th>
                    <th class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= number_format($transaksi['total_pembayaran'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                    <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Terima Uang
                    </th>
                    <th class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                        Rp
                    </th>
                    <th class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= number_format($transaksi['terima_uang'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                    <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Uang Kembali
                    </th>
                    <th class="outline-border-right" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left; white-space: nowrap;">
                        Rp
                    </th>
                    <th class="outline-border-left" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= number_format($transaksi['uang_kembali'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;"></th>
                    <th colspan="3" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Metode Bayar
                    </th>
                    <th colspan="2" class="outline-border" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right;">
                        <?= $transaksi['metode_pembayaran'] ?> <?= ($transaksi['bank'] == NULL) ? '' : '(' . $transaksi['bank'] . ')' ?>
                    </th>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;">
                        <?php if ($transaksi['nama_pasien'] != NULL) : ?>
                            <div>Penerima</div>
                        <?php endif; ?>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;">
                        <div>Kasir</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <?php if ($transaksi['nama_pasien'] != NULL) : ?>
                            <div><?= $transaksi['nama_pasien'] ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div><?= $transaksi['kasir'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>