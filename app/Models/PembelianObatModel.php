<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianObatModel extends Model
{
    protected $table = 'pembelian_obat';
    protected $primaryKey = 'id_pembelian_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_supplier', 'apoteker', 'tgl_pembelian', 'total_qty', 'total_biaya', 'diterima'];
}
