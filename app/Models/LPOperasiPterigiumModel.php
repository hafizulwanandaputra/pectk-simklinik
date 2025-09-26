<?php

namespace App\Models;

use CodeIgniter\Model;

class LPOperasiPterigiumModel extends Model
{
    protected $table = 'medrec_lp_operasi_pterigium';
    protected $primaryKey = 'id_lp_operasi_pterigium';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'tanggal_operasi',
        'mata',
        'operator',
        'jam_operasi',
        'lama_operasi',
        'diagnosis',
        'asisten',
        'jenis_operasi',
        'jenis_anastesi',
        'dokter_anastesi',
        'antiseptic',
        'antiseptic_lainnya',
        'spekulum',
        'spekulum_lainnya',
        'kendala_rektus_superior',
        'cangkok_konjungtiva',
        'ukuran_cangkok',
        'ukuran_cangkok',
        'cangkang_membrane_amnio',
        'ukuran_cangkang',
        'bare_sclera',
        'mytomicyn_c',
        'penjahitan',
        'laporan_operasi',
        'terapi_pascabedah',
        'waktu_dibuat',
    ];
}
