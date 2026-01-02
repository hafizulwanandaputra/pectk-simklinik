<?php
// Tanggal lahir pasien
$tanggal_lahir = new DateTime($pasien['tanggal_lahir']);

// Tanggal sekarang
$sekarang = new DateTime();

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
            font-size: 6.5pt;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
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
            border: 1px solid black;
            height: 0.9cm;
            overflow: hidden;
            padding: 0cm;
            font-weight: bold;
            text-align: center;
            font-size: 4.8pt;
        }
    </style>
</head>

<body>
    <div>
        <div style="text-align: center;">
            <div style="padding-top: 0.05cm; padding-bottom: 0.1cm;">
                <strong style="white-space: nowrap;"><?= $pasien['nama_pasien'] ?><br>[ <?= $pasien['jenis_kelamin'] ?> ] <?= $pasien['tanggal_lahir'] ?> (<?= $usia->y . " tahun " . $usia->m . " bulan" ?>)</strong>
            </div>
            <div>
                <img src="data:image/png;base64,<?= $bcNoRM ?>" width="160mm" alt="Barcode">
            </div>
            <div>
                <strong><?= $pasien['no_rm'] ?></strong>
            </div>
        </div>
    </div>

</body>

</html>