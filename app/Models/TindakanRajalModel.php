<?php

namespace App\Models;

use CodeIgniter\Model;

class TindakanRajalModel extends Model
{
    protected $table = 'medrec_lp_tindakan_rajal';
    protected $primaryKey = 'id_lp_tindakan_rajal';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'nama_perawat',
        'diagnosa',
        'kode_icd_x',
        'lokasi_mata',
        'isi_laporan',
        'waktu_dibuat'
    ];
}
