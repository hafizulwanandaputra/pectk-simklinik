<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_layanan', 'jenis_layanan', 'tarif', 'keterangan'];
}
