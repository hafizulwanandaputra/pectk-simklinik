<?php

namespace App\Models;

use CodeIgniter\Model;

class LPOperasiModel extends Model
{
    protected $table = 'medrec_lp_operasi';
    protected $primaryKey = 'id_lp_operasi';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'dokter_bedah',
        'asisten_dokter_bedah',
        'dokter_anastesi',
        'jenis_anastesi',
        'jenis_operasi',
        'diagnosis_pra_bedah',
        'diagnosis_pasca_bedah',
        'indikasi_operasi',
        'nama_operasi',
        'jaringan_eksisi',
        'pemeriksaan_pa',
        'tanggal_operasi',
        'jam_operasi',
        'lama_operasi',
        'laporan_operasi',
        'terapi_pascabedah',
        'waktu_dibuat',
    ];
}
