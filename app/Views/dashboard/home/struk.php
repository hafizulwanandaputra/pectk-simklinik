<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        @media print {
            @page {
                margin-top: 0.1cm;
                margin-bottom: 0.1cm;
                margin-left: 0cm;
                margin-right: 0cm;
            }
        }

        body {
            font-family: <?= env('PDF-FONT') ?>;
            font-size: 6pt;
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
            font-size: 7pt;
        }

        .box {
            font-family: <?= env('PDF-FONT-CONDENSED') ?>;
            border: 1px solid black;
            height: 0.7cm;
            overflow: hidden;
            padding: 0cm;
            font-size: 7pt;
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
                            <div>Bukti Antrean Pendaftaran</div>
                        </div>
                    </td>
                    <td style="width: 0%;">

                    </td>
                </tr>
            </thead>
        </table>
        <div style="text-align: center;">
            <div>Nomor Antrean:</div>
            <div style="font-size: 32px;"><strong><?= $antrean['kode_antrean'] . '-' . $antrean['nomor_antrean'] ?></strong></div>
        </div>
        <div class="box" style="width: 100%; padding-top: 2px;">
            <table class="table" style="width: 100%; margin-bottom: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Jaminan</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $antrean['nama_jaminan'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">Tanggal</div>
                        </td>
                        <td style="width: 0%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;">:</div>
                        </td>
                        <td style="width: 75%; vertical-align: top; padding-top: 0; padding-bottom: 0; padding-left: 0.1cm; padding-right: 0.1cm;">
                            <div style="white-space: nowrap;"><?= $antrean['tanggal_antrean'] ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align: center; font-size: 7pt;">
            Silakan tunggu hingga nomor antrean Anda dipanggil.<br>Terima kasih!
        </div>
    </div>

</body>

</html>