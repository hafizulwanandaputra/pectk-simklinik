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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }
    </style>
    <?= $this->renderSection('css'); ?>
</head>

<body>
    <?= $this->renderSection('content'); ?>
</body>

</html>