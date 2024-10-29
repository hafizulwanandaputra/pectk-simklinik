<?php

namespace App\Models;

use CodeIgniter\Model;

class OpnameObatModel extends Model
{
    protected $table = 'opname_obat';
    protected $primaryKey = 'id_opname_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_opname_obat', 'tanggal', 'apoteker'];
}
