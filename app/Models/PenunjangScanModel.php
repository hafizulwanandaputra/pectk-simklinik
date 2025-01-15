<?php

namespace App\Models;

use CodeIgniter\Model;

class PenunjangScanModel extends Model
{
    protected $table = 'medrec_permintaan_penunjang_scan';
    protected $primaryKey = 'id_penunjang_scan';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'pemeriksaan', 'gambar', 'keterangan', 'waktu_dibuat'];
}
