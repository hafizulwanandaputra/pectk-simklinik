<?php

namespace App\Models;

use CodeIgniter\Model;

class EdukasiModel extends Model
{
    protected $table = 'medrec_edukasi';
    protected $primaryKey = 'id_edukasi';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'bahasa',
        'bahasa_lainnya',
        'penterjemah',
        'pendidikan',
        'baca_tulis',
        'cara_belajar',
        'budaya',
        'hambatan',
        'keyakinan',
        'keyakinan_khusus',
        'topik_pembelajaran',
        'topik_lainnya',
        'kesediaan_pasien',
        'waktu_dibuat'
    ];
}
