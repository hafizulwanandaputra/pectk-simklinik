<?php

namespace App\Models;

use CodeIgniter\Model;

class ButaWarnaModel extends Model
{
    protected $table = 'medrec_keterangan_buta_warna';
    protected $primaryKey = 'id_keterangan_buta_warna';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'keperluan',
        'od_ukuran_kacamata',
        'od_visus',
        'os_ukuran_kacamata',
        'os_visus',
        'status_buta_warna',
        'waktu_dibuat'
    ];
}
