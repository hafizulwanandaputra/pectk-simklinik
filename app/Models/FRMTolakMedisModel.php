<?php

namespace App\Models;

use CodeIgniter\Model;

class FRMTolakMedisModel extends Model
{
    protected $table = 'medrec_form_penolakan_tindakan';
    protected $primaryKey = 'id_form_penolakan_tindakan';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'diagnosis',
        'hubungan',
        'hubungan_lain',
        'waktu_dibuat',
    ];
}
