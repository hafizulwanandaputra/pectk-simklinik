<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: <?= env('PDF-FONT') ?>;
            font-size: 9pt;
            line-height: 1.1;
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
            height: 18cm;
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
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <thead>
                <tr>
                    <th style="display: flex; justify-content: center;">
                        <div style="position: relative; width: 340px;">
                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/kacamata.png')) ?>" width="340px" alt="">
                        </div>
                    </th>
                    <td style="width: 0%;">
                        <table class="table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div>Trifocus</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div>Bifocus</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 40%; vertical-align: middle; padding: 0.1cm; border: 1px solid black; font-size: 7pt; overflow: hidden;">
                                        <div style="width: 0.3cm; height: 0.3cm; text-align: center;">
                                        </div>
                                    </td>
                                    <td style="width: 60%; vertical-align: middle; padding: 0;">
                                        <div style="white-space: nowrap;">Single Focus</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px; border-collapse: collapse; font-size: 9pt;">
            <thead>
                <tr>
                    <th style="padding-top: 1px; padding-bottom: 0px; border-right: 1px solid black;"></th>
                    <th style="padding-top: 1px; padding-bottom: 0px; border: 1px solid black;" colspan="5">
                        <h2 style="text-align: center; margin: 0.25cm;">O.D</h2>
                    </th>
                    <th style="padding-top: 1px; padding-bottom: 0px; border: 1px solid black;" colspan="5">
                        <h2 style="text-align: center; margin: 0.25cm;">O.S</h2>
                    </th>
                    <th colspan="2" style="padding-top: 1px; padding-bottom: 0px; border-left: 1px solid black;"></th>
                </tr>
                <tr>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border-bottom: 1px solid black; border-right: 1px solid black;"></th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Spher</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Cyldr</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Axis</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Prisma</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Basis</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Spher</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Vitrum Cyldr</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Axis</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Prisma</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Basis</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Golor Vitror</th>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; width: 7.692307692307692%; border: 1px solid black;">Distant Pupil</th>
                </tr>
                <tr>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap;">Pro Login<br>Quitat</th>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                </tr>
                <tr>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black;">Pro<br>Domo</th>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                </tr>
                <tr>
                    <th style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black;">Propin<br>Quitat</th>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                    <td style="padding-top: 1px; padding-bottom: 0px; text-align: center; border: 1px solid black; white-space: nowrap; overflow: hidden;">
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%; margin-bottom: 4px;">
            <tbody>
                <tr>
                    <td style="text-align: center; width: 60%; max-width: 6cm; vertical-align: middle; padding: 0.1cm; font-size: 8pt; overflow: hidden; border: 1px solid black;">
                        Tempel <em>barcode</em><br>pasien di sini
                    </td>
                    <td style="width: 40%; vertical-align: top; padding: 0;">
                        <center>
                            <div>Teluk Kuantan, ........................20......</div>
                            <div style="padding-top: 1.25cm;"></div>
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>