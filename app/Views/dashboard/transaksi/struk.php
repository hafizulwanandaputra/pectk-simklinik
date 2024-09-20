<?php

use CodeIgniter\I18n\Time;

$tanggal = Time::parse($transaksi['tgl_transaksi']);
?>
<?= $this->extend('dashboard/templates/struk'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid my-3">
    <div style="font-family: monospace; font-size: 8pt;">
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr>
                    <th>
                        <div style="margin: 0;">KLINIK UTAMA MATA<br>PADANG EYE CENTER<br>TELUK KUANTAN</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border-bottom: 1px solid black; text-align: center;">
                        <div style="margin: 0;">Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.</div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>ID:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['id_transaksi'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>Tanggal/Waktu:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $tanggal ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>Nama Pasien:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['nama_pasien'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>Nomor MR:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['no_mr'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>Nomor Registrasi:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['no_registrasi'] ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div>Kasir:</div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 0;">
                        <div><?= $transaksi['fullname'] ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-borderless" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="width: 100%; vertical-align: top; padding: 0; text-align: left;">
                        Resep
                    </th>
                    <th style="width: 0%; vertical-align: top; padding: 0; text-align: left;">
                        Harga
                    </th>
                    <th style="width: 0%; vertical-align: top; padding: 0; text-align: left;">
                        Total
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
                            <?= $list['resep']['user']['fullname'] ?>
                            <ul style="margin: 0; padding-left: 1rem;">
                                <?php foreach ($list['resep']['detail_resep'] as $resep) : ?>
                                    <?php foreach ($resep['obat'] as $obat) : ?>
                                        <li><?= $obat['nama_obat'] ?><br><?= $obat['kategori_obat'] ?> | <?= $obat['bentuk_obat'] ?><br><?= $obat['dosis_kali'] ?> × <?= $obat['dosis_hari'] ?> hari | <?= $obat['cara_pakai'] ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                            <?= $list['resep']['keterangan'] ?>
                        </td>
                        <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                            <?= 'Rp' . number_format($list['harga_resep'], 0, ',', '.') ?><br><?= $list['diskon'] . '%' ?>
                        </td>
                        <td style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                            <?= 'Rp' . number_format($list['harga_resep'] * (1 - ($list['diskon'] / 100)), 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4" style="border-bottom: 1px solid black;"></th>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        Total
                    </th>
                    <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['total_pembayaran'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        Terima Uang
                    </th>
                    <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['terima_uang'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        Uang Kembali
                    </th>
                    <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= 'Rp' . number_format($transaksi['uang_kembali'], 0, ',', '.') ?>
                    </th>
                </tr>
                <tr>
                    <th style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        Metode Bayar
                    </th>
                    <th colspan="2" style="vertical-align: top; padding: 0; text-align: right; white-space: nowrap;">
                        <?= $transaksi['metode_pembayaran'] ?>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>