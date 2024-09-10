<?php

namespace App\Models;

use CodeIgniter\Model;

class DokterModel extends Model
{
    protected $table = 'dokter';
    protected $primaryKey = 'id_dokter';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_dokter', 'alamat_dokter', 'kontak_dokter'];
}
