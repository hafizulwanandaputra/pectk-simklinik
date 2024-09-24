<?php

use CodeIgniter\I18n\Time;

$tanggal = Time::parse($transaksi['tgl_transaksi']);
?>
<?= $this->extend('dashboard/templates/struk'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid my-3">
    <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
        <thead>
            <tr>
                <th>
                    <div style="margin: 0;">KLINIK MATA PECTK</div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border-bottom: 1px solid black; text-align: center;">
                    <div style="margin: 0;">Jl. Rusdi S. Abrus LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.</div>
                </td>
            </tr>
        </tbody>
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
    <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
        <thead>
            <tr>
                <th colspan="4" style="vertical-align: top; padding: 0; text-align: left;">Obat dan Alkes</th>
            </tr>
            <tr>
                <th colspan="4" style="border-bottom: 1px solid black;"></th>
            </tr>
            <tr>
                <th style="width: 100%; vertical-align: top; padding: 0; text-align: left;">
                    Deskripsi
                </th>
                <th style="width: 0%; vertical-align: top; padding: 0; text-align: center;">
                    Biaya
                </th>
                <th style="width: 0%; vertical-align: top; padding: 0; text-align: center; white-space: nowrap;">
                    Disc %
                </th>
                <th style="width: 0%; vertical-align: top; padding: 0; text-align: center; white-space: nowrap;">
                    Sub Total
                </th>
            </tr>
            <tr>
                <th colspan="4" style="border-bottom: 1px solid black;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detail_transaksi as $list) : ?>
                <tr>
                    <td style="vertical-align: top; padding: 0;">
                        <ol style="margin: 0; padding-left: 2rem;">
                            <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                                <?php foreach ($resep['obat'] as $obat) : ?>
                                    <li><?= $obat['nama_obat'] ?> (<?= $obat['jumlah'] ?> × <?= 'Rp' . number_format($obat['harga_satuan'], 0, ',', '.') ?>)</li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ol>
                    </td>
                    <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <ul style="margin: 0; padding-left: 0; list-style-type:none;">
                            <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                                <?php foreach ($resep['obat'] as $obat) : ?>
                                    <li><?= 'Rp' . number_format($obat['harga_satuan'] * $obat['jumlah'], 0, ',', '.') ?></li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <!-- <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($list['harga_resep'], 0, ',', '.') ?>
                    </td> -->
                    <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= $list['diskon'] ?>
                    </td>
                    <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($list['harga_resep'] * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <th colspan="5" style="border-bottom: 1px solid black;"></th>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    Total
                </th>
                <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    <?= 'Rp' . number_format($transaksi['total_pembayaran'], 0, ',', '.') ?>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    Terima Uang
                </td>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    <?= 'Rp' . number_format($transaksi['terima_uang'], 0, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    Uang Kembali
                </td>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    <?= 'Rp' . number_format($transaksi['uang_kembali'], 0, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    Metode Bayar
                </td>
                <td colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                    <?= $transaksi['metode_pembayaran'] ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?= $this->endSection(); ?>