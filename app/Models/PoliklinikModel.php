<?php

namespace App\Models;

use CodeIgniter\Model;

class PoliklinikModel extends Model
{
    protected $table = 'poliklinik';
    protected $primaryKey = 'id_poli';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_poli', 'status', 'tanggal_registrasi'];
}
