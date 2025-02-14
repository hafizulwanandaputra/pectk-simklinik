<?php

namespace App\Models;

use CodeIgniter\Model;

class IstirahatModel extends Model
{
    protected $table = 'medrec_keterangan_istirahat';
    protected $primaryKey = 'id_keterangan_istirahat';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_dibuat'
    ];
}
