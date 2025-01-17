<?php

namespace App\Models;

use CodeIgniter\Model;

class OptikModel extends Model
{
    protected $table = 'medrec_optik';
    protected $primaryKey = 'id_optik';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'tipe_lensa',
        'od_login_spher',
        'od_login_cyldr',
        'od_login_axis',
        'od_login_prisma',
        'od_login_basis',
        'od_domo_spher',
        'od_domo_cyldr',
        'od_domo_axis',
        'od_domo_prisma',
        'od_domo_basis',
        'od_quitat_spher',
        'od_quitat_cyldr',
        'od_quitat_axis',
        'od_quitat_prisma',
        'od_quitat_basis',
        'os_login_spher',
        'os_login_cyldr',
        'os_login_axis',
        'os_login_prisma',
        'os_login_basis',
        'os_login_vitror',
        'os_login_pupil',
        'os_domo_spher',
        'os_domo_cyldr',
        'os_domo_axis',
        'os_domo_prisma',
        'os_domo_basis',
        'os_domo_vitror',
        'os_domo_pupil',
        'os_quitat_spher',
        'os_quitat_cyldr',
        'os_quitat_axis',
        'os_quitat_prisma',
        'os_quitat_basis',
        'os_quitat_vitror',
        'os_quitat_pupil',
        'waktu_dibuat'
    ];
}
