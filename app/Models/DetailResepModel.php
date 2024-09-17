<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailResepModel extends Model
{
    protected $table = 'detail_resep';
    protected $primaryKey = 'id_detail_resep';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_resep', 'id_obat', 'jumlah', 'harga_satuan'];
}
