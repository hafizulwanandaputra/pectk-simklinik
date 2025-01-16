<?php

namespace App\Models;

use CodeIgniter\Model;

class AsesmenMataModel extends Model
{
    protected $table = 'medrec_assesment_mata';
    protected $primaryKey = 'id_asesmen_mata';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'gambar', 'keterangan', 'waktu_dibuat'];
}
