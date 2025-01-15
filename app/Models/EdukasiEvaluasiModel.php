<?php

namespace App\Models;

use CodeIgniter\Model;

class EdukasiEvaluasiModel extends Model
{
    protected $table = 'medrec_edukasi_evaluasi';
    protected $primaryKey = 'id_edukasi_evaluasi';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'no_rm', 'unit', 'informasi_edukasi', 'nama_edukator', 'profesi_edukator', 'tanda_tangan_edukator', 'nama_pasien_keluarga', 'tanda_tangan_pasien', 'evaluasi', 'waktu_dibuat'];
}
