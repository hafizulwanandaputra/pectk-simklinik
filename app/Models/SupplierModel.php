<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_supplier', 'alamat_supplier', 'kontak_supplier'];
}
