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
            size: 21.5cm 16.5cm;
            margin: 0.5cm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
        }

        #listTable {
            border: 1px solid black;
            border-collapse: collapse;
        }

        #listTable tr {
            border: none;
        }

        #listTable th,
        #listTable td {
            border-right: solid 1px;
            border-left: solid 1px;
        }
    </style>
</head>

<body>
    <div class="container-fluid my-3">
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px; border-bottom: 2px solid black;">
            <thead>
                <tr>
                    <th style="width: 0%;">
                        <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/logo_pec.png')) ?>" width="64px" alt="">
                    </th>
                    <td style="width: 100%;">
                        <h2 style="margin: 0; padding: 0;">KLINIK MATA PECTK</h2>
                        <div>
                            <div>Jl. Rusdi S. Abrus LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.</div>
                        </div>
                    </td>
                    <td style="width: 0%;">
                        <div style="font-size: 18pt;">KWITANSI</div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nomor RM:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['no_mr'] ?></div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nomor Kwitansi:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['no_kwitansi'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Nama Pasien:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['nama_pasien'] ?></div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Tanggal/Waktu:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div><?= $tanggal ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>No. Telp:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['no_hp_pasien'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <div>Alamat:</div>
                    </td>
                    <td style="width: 25%; vertical-align: top; padding: 0;" colspan="3">
                        <div><?= $transaksi['alamat_pasien'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="listTable" class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr style="border: 1px solid">
                    <th colspan="5" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">Tindakan</th>
                </tr>
                <tr style="border: 1px solid">
                    <th style="width: 100%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                        Deskripsi
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                        Qty
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                        Biaya
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Disc %
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Sub Total
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr style="border: 1px solid">
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px;">
                        <ol style="margin: 0; padding-left: 2rem;">
                            <?php foreach ($layanan as $list) : ?>
                                <li><?= $list['layanan']['nama_layanan'] ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </ol>
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                            <?php foreach ($layanan as $list) : ?>
                                <li><?= $list['qty_transaksi'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                            <?php foreach ($layanan as $list) : ?>
                                <li><?= 'Rp' . number_format($list['harga_transaksi'], 0, ',', '.') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                            <?php foreach ($layanan as $list) : ?>
                                <li><?= $list['diskon'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                            <?php foreach ($layanan as $list) : ?>
                                <li><?= 'Rp' . number_format(($list['harga_transaksi'] * $list['qty_transaksi']) * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
                <tr style="border: 1px solid">
                    <th colspan="4" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Sub Total
                    </th>
                    <th style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($total_layanan['harga_transaksi'], 0, ',', '.') ?>
                    </th>
                </tr>
            </tbody>
        </table>
        <table id="listTable" class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr style="border: 1px solid">
                    <th colspan="4" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">Obat dan Alkes</th>
                </tr>
                <tr style="border: 1px solid">
                    <th style="width: 100%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: left;">
                        Deskripsi
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center;">
                        Biaya
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Disc %
                    </th>
                    <th style="width: 0%; vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: center; white-space: nowrap;">
                        Sub Total
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($obatalkes as $list) : ?>
                    <tr style="border: 1px solid">
                        <td style="vertical-align: top; padding-left: 2px; padding-right: 2px;">
                            <ol style="margin: 0; padding-left: 2rem;">
                                <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                                    <?php foreach ($resep['obat'] as $obat) : ?>
                                        <li><?= $obat['nama_obat'] ?> (<?= $obat['jumlah'] ?> × <?= 'Rp' . number_format($obat['harga_satuan'], 0, ',', '.') ?>)</li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                        <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                            <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                                <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                                    <?php foreach ($resep['obat'] as $obat) : ?>
                                        <li><?= 'Rp' . number_format($obat['harga_satuan'] * $obat['jumlah'], 0, ',', '.') ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <!-- <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($list['harga_transaksi'], 0, ',', '.') ?>
                    </td> -->
                        <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                            <?= $list['diskon'] ?>
                        </td>
                        <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                            <?= 'Rp' . number_format($list['harga_transaksi'] * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Sub Total
                    </th>
                    <th style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($total_obatalkes['harga_transaksi'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Grand Total
                    </th>
                    <th style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['total_pembayaran'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <td colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Terima Uang
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['terima_uang'], 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Uang Kembali
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['uang_kembali'], 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        Metode Bayar
                    </td>
                    <td style="vertical-align: top; padding-left: 2px; padding-right: 2px; text-align: right; white-space: nowrap;">
                        <?= $transaksi['metode_pembayaran'] ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;">
                        <div>Penerima</div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top; padding-bottom: 1.25cm;">
                        <div>Kasir</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div><?= $transaksi['nama_pasien'] ?></div>
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