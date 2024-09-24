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
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
        }

        #listTable {
            border: 1px solid black;
            border-collapse: collapse;
        }

        #listTable tr {
            border: none;
        }

        #listTable th,
        #listTable td {
            border-right: solid 1px;
            border-left: solid 1px;
        }
    </style>
    <?= $this->renderSection('css'); ?>
</head>

<body>
    <?= $this->renderSection('content'); ?>
</body>

</html>