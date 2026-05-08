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
        'od_tekanan_bola_mata',
        'os_ukuran_kacamata',
        'os_visus',
        'os_tekanan_bola_mata',
        'jenis_rabun',
        'status_buta_warna',
        'waktu_dibuat'
    ];
}
