<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rajal['tanggal_lahir']);

// Tanggal sekarang
$sekarang = new DateTime($rajal['tanggal_registrasi']);

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $sekarang->diff($tanggal_lahir);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: <?= env('PDF-FONT') ?>;
            font-size: 7.8pt;
            line-height: 1.2;
        }

        table {
            border-collapse: collapse;
        }

        h2 {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
            font-size: 8pt;
        }

        .box {
            border: 1px solid black;
            height: 3.4cm;
            overflow: hidden;
            padding: 0cm;
            font-size: 8.5pt;
        }

        .tindakan {
            font-family: <?= env('PDF-FONT-CONDENSED') ?>;
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .tindakan td:nth-child(1),
        .tindakan td:nth-child(3) {
            border: 1px solid black;
        }

        .tindakan-2 {
            font-family: <?= env('PDF-FONT-CONDENSED') ?>;
            width: 100%;
            border-collapse: collapse;
        }

        /* Border hanya untuk kolom pertama dan baris pertama serta kedua */
        .tindakan-2 td:nth-child(1):nth-of-type(1),
        .tindakan-2 td:nth-child(1):nth-of-type(2) {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <div>
        <table class="table" style="width: 100%; margin-bottom: 4px; border-bottom: 2px solid black;">
            <thead>
                <tr>
                    <td style="width: 100%;">
                        <strong>KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN</strong>
                        <div>
                            <div>Bukti Registrasi Rawat Jalan</div>
                        </div>
                    </td>
                    <td style="width: 0%;">
                        <div style="font-size: 24pt;">
                            <?php if ($rajal['status_kunjungan'] == 'BARU') : ?>
                                <strong>B</strong>
                            <?php elseif ($rajal['status_kunjungan'] == 'LAMA') : ?>
                                <strong>L</strong>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </thead>
        </table>
        <div style="text-align: center;">
            <div>Nomor Antrean:</div>
            <div style="font-size: 32px;"><strong><?= $rajal['kode_antrian'] . $rajal['no_antrian'] ?></strong></div>
        </div>
        <div class="box" style="width: 100%; padding-top: 2px;">
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">No RM</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['no_rm'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Nama</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['nama_pasien'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Tgl Lahir</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['tanggal_lahir'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Umur</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $usia->y . " tahun " . $usia->m . " bulan" ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">No Reg</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['nomor_registrasi'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Poliklinik</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['ruangan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Dokter</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['dokter'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Tgl & Wkt</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['tanggal_registrasi'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Operator</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $rajal['pendaftar'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="padding-top: 0.1cm; padding-bottom: 0.1cm; overflow: hidden; border-bottom: 1px solid black;">
            <table class="tindakan">
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"></td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"><em>Redressing</em></td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"><em>Indirect Fundus Copy</em></td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Autoref Keratometri</td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Korpus Alineum Kornea</td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Tonometri</td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"><em>Spooling</em></td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Keratometri</td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Retinometri</td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"><em>Heacting All</em></td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">d.l.l.: ...........................</td>
                </tr>
            </table>
        </div>
        <div style="padding-top: 0.1cm; padding-bottom: 0.1cm; overflow: hidden;">
            <table class="tindakan-2">
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; max-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"><em>Free</em> Konsul</td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="text-align: center; width: 40%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Paraf</td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; max-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">Obat</td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"></td>
                </tr>
                <tr>
                    <td style="width: 0%; min-width: 0.2cm; max-width: 0.2cm; border: none; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"></td>
                    <td style="width: 50%; border: none; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"></td>
                    <td style="width: 0%; min-width: 0.2cm; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;"> </td>
                    <td style="width: 50%; text-align: center; width: 40%; vertical-align: top; padding-top: 0.025cm; padding-bottom: 0.025cm; padding-left: 0.1cm; padding-right: 0.1cm; white-space: nowrap;">(.............................)</td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>