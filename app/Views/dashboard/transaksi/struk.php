<?php

use CodeIgniter\I18n\Time;

$tanggal = Time::parse($permintaan['tanggal']);
$tanggal_lahir = Time::parse($permintaan['tanggal_lahir']);
?>
<?= $this->extend('dashboard/templates/struk'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid my-3">
    <footer style="font-family: sans-serif; font-size: 9pt;">
        Gizi PEC
    </footer>
    <div style="font-family: sans-serif;">
        <div style="font-size: 14pt;">
            <table class="table table-borderless" style="width: 100%;">
                <thead>
                    <tr>
                        <th>
                            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(FCPATH . 'assets/images/logo_pec.png')) ?>" width="64px" alt="">
                        </th>
                        <th>
                            <span style=" margin: 0;">ETIKET MAKANAN PASIEN</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="vertical-align: top; padding: 0; width: 30%; font-weight: bold;" scope="row">Nama<br><br></td>
                        <td style="vertical-align: top; padding: 0; width: 70%;" class="date"><?= $permintaan['nama_pasien']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 0; width: 30%; font-weight: bold;" scope="row">Tgl. Lahir<br><br></td>
                        <td style="vertical-align: top; padding: 0; width: 70%;"><?= $tanggal_lahir->toLocalizedString('d MMMM yyyy'); ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 0; width: 30%; font-weight: bold;" scope="row">Ruangan<br><br></td>
                        <td style="vertical-align: top; padding: 0; width: 70%;"><?= $permintaan['kamar']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>