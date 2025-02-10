<?php

namespace App\Models;

use CodeIgniter\Model;

class PenunjangModel extends Model
{
    protected $table = 'medrec_permintaan_penunjang';
    protected $primaryKey = 'id_penunjang';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'no_rm', 'dokter_pengirim', 'rujukan_dari', 'pemeriksaan', 'pemeriksaan_lainnya', 'lokasi_pemeriksaan', 'waktu_dibuat'];
}
