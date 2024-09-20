<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <style>
        @page {
            size: 7.5cm 30cm;
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
    </style>
    <?= $this->renderSection('css'); ?>
</head>

<body>
    <?= $this->renderSection('content'); ?>
</body>

</html>