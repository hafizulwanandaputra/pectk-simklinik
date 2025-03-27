<?php

namespace App\Models;

use CodeIgniter\Model;

class SakitMataModel extends Model
{
    protected $table = 'medrec_keterangan_sakit_mata';
    protected $primaryKey = 'id_keterangan_sakit_mata';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'biasa',
        'keterangan',
        'waktu_dibuat'
    ];
}
