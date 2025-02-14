<?php

namespace App\Models;

use CodeIgniter\Model;

class SkriningModel extends Model
{
    protected $table = 'medrec_skrining';
    protected $primaryKey = 'id_skrining';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'jatuh_sempoyongan',
        'jatuh_penopang',
        'jatuh_info_dokter',
        'jatuh_info_pukul',
        'status_fungsional',
        'status_info_pukul',
        'nyeri_kategori',
        'nyeri_skala',
        'nyeri_lokasi',
        'nyeri_karakteristik',
        'nyeri_durasi',
        'nyeri_frekuensi',
        'nyeri_hilang_bila',
        'nyeri_hilang_lainnya',
        'nyeri_info_dokter',
        'nyeri_info_pukul',
        'waktu_dibuat'
    ];
}
