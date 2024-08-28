<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?> - Sistem Permintaan Makanan Pasien Rawat Inap RSKM PEC</title>
    <link href="<?= base_url(); ?>assets_public/fontawesome/css/all.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
        @page {
            size: 10.5cm 7.4cm;
            margin: 0.1cm;
        }

        footer {
            position: fixed;
            bottom: 2px;
            left: 0px;
            right: 10px;
            height: 12px;

            /** Extra personal styles **/
            text-align: right;
        }

        @font-face {
            font-family: 'Helvetica World';
            font-style: normal;
            font-weight: normal;
            src: url(<?= base_url('assets_public/fonts/helveticaworld/HelveticaWorld-Regular.ttf'); ?>) format('truetype');
        }
    </style>
</head>

<body>
    <?= $this->renderSection('content'); ?>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
    <script src="<?= base_url(); ?>assets_public/fontawesome/js/all.js"></script>
    <script>
        feather.replace({
            'aria-hidden': 'true'
        });
    </script>
</body>

</html>