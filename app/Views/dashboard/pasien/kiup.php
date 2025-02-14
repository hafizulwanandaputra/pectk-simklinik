<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
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
            height: 21.5cm;
            overflow: hidden;
            padding: 0.1cm;
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
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 85%; vertical-align: top; padding: 0;">
                        <h2>FORMULIR PENDAFTARAN PASIEN BARU</h2>
                    </td>
                    <td style="width: 20%; vertical-align: top; padding: 0;">
                        <div style="text-align: center;">
                            <img src="data:image/svg+xml;base64,<?= $qrNoRM ?>" />
                            <div>No RM: <?= $pasien['no_rm']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Nama Pasien</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['nama_pasien'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>NIK</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['nik'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>No BPJS</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['no_bpjs'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Jenis Kelamin</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div>
                                <?php if ($pasien['jenis_kelamin'] == 'L') : ?>
                                    Laki-Laki
                                <?php elseif ($pasien['jenis_kelamin'] == 'P') : ?>
                                    Perempuan
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Tempat dan Tanggal Lahir</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['tempat_lahir'] ?>, <?= $pasien['tanggal_lahir'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Alamat</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['alamat'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div style="padding-left: 1cm;">RT/RW</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['rt'] ?>/<?= $pasien['rw'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div style="padding-left: 1cm;">Kelurahan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['kelurahan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div style="padding-left: 1cm;">Kecamatan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['kecamatan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div style="padding-left: 1cm;">Kab/Kota</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['kabupaten'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div style="padding-left: 1cm;">Provinsi</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['provinsi'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Agama</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['agama'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Pekerjaan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['pekerjaan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Status Perkawinan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['status_nikah'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Nomor HP</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['telpon'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding: 0;">
                            <div>Didaftarkan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding: 0;">
                            <div>:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding: 0;">
                            <div><?= $pasien['tanggal_daftar'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">* Identitas pasien sesuai dengan tanda pengenal (KTP, SIM, d.l.l.)</p>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>