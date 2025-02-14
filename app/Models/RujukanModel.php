<?php

namespace App\Models;

use CodeIgniter\Model;

class RujukanModel extends Model
{
    protected $table = 'medrec_rujukan';
    protected $primaryKey = 'id_rujukan';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'diagnosis',
        'diagnosis_diferensial',
        'terapi',
        'dokter_rujukan',
        'alamat_dokter_rujukan',
        'waktu_dibuat'
    ];
}
