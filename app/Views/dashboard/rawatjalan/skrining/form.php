<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($rawatjalan['tanggal_lahir']);

// Tanggal registrasi
$registrasi = new DateTime(date('Y-m-d', strtotime($rawatjalan['tanggal_registrasi'])));

// Hitung selisih antara tanggal sekarang dan tanggal lahir
$usia = $registrasi->diff($tanggal_lahir);

// Logika penentuan risiko
if ($skrining['jatuh_sempoyongan'] === 'YA' && $skrining['jatuh_penopang'] === 'YA') {
    $hasil = 'Risiko Tinggi (ditemukan a dan b)';
} elseif ($skrining['jatuh_sempoyongan'] === 'YA' || $skrining['jatuh_penopang'] === 'YA') {
    $hasil = 'Risiko Rendah (ditemukan a atau b)';
} else {
    $hasil = 'Tidak Berisiko (tidak ditemukan a dan b)';
}
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
            border: 1px solid black;
            height: 22.5cm;
            overflow: hidden;
            padding: 0cm;
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
                        <div style="white-space: nowrap;"><strong>FRM: 2a hal 1<br>Rev: 002</strong></div>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding: 0;">
                        <h2 style="padding: 0;">SKRINING PASIEN RAWAT JALAN</h2>
                        <div>Tanggal registrasi: <?= $rawatjalan['tanggal_registrasi']; ?></div>
                    </td>
                    <td style="width: 40%; max-width: 5cm; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 8pt; overflow: hidden;">
                        <div style="text-align: center;">
                            <div style="white-space: nowrap;"><?= $rawatjalan['nama_pasien']; ?></div>
                            <div><?= $rawatjalan['no_rm']; ?></div>
                            <div><?= $rawatjalan['tanggal_lahir']; ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</div>
                            <img src="data:image/png;base64,<?= $bcNoReg ?>" width="240mm" alt="Barcode">
                            <div><?= $rawatjalan['nomor_registrasi']; ?></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; padding-top: 0.25cm; margin: 0;">I. SKRINING RISIKO CEDERA / JATUH (<em>GET UP AND GO SCORE</em>)</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            a.
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Perhatikan cara berjalan pasien saat duduk di kursi. Apakah pasien tampak tidak seimbang (sempoyongan/limbung)?
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['jatuh_sempoyongan'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            b.
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk?
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['jatuh_penopang'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Hasil
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $hasil ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;"></td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Diberitahukan ke dokter
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['jatuh_info_dokter'] ?> <?= ($skrining['jatuh_info_dokter'] == 'YA') ? ((!empty($skrining['jatuh_info_pukul'])) ? '(' . $skrining['jatuh_info_pukul'] . ')' : '') : ''; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">
                <small>
                    <table class="table" style="width: 100%; margin-bottom: 4px;">
                        <tbody>
                            <tr>
                                <td style="width: 0%; vertical-align: top; padding: 0;">
                                    Keterangan:
                                </td>
                                <td style="width: 100%; vertical-align: top; padding: 0;">
                                    Skrining risiko cedera/jatuh pasien rawat jalan <em>one day care</em>.<br>Jika temuan risiko tinggi, maka pasangkan gelang kuning pada pasien.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </small>
            </div>
            <hr>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">II. STATUS FUNGSIONAL</h3>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Status fungsional
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['status_fungsional'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Diberitahukan ke dokter pukul
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['status_info_pukul'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <h3 style="padding-left: 0.25cm; padding-right: 0.25cm; margin: 0;">II. SKRINING NYERI</h3>
            <div style="text-align: center;">
                <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/skala_nyeri.png')) ?>" width="480px" alt="">
            </div>
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Kategori nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_kategori'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Skala Nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_skala'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Lokasi nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_lokasi'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Karakteristik nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_karakteristik'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Durasi Nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_durasi'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Frekuensi nyeri
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_frekuensi'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Nyeri hilang bila
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= ($skrining['nyeri_hilang_bila'] == NULL) ? 'TIDAK ADA' : $skrining['nyeri_hilang_bila']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Jika lain-lain, sebutkan
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_hilang_lainnya'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            Diberitahukan ke dokter
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            :
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 0.25cm; padding-right: 0.25cm;">
                            <?= $skrining['nyeri_info_dokter'] ?> <?= ($skrining['nyeri_info_dokter'] == 'YA') ? ((!empty($skrining['nyeri_info_pukul'])) ? '(' . $skrining['nyeri_info_pukul'] . ')' : '') : ''; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size: 9pt;">Dicetak: <?= date("Y-m-d H:i:s") ?></p>
    </div>

</body>

</html>