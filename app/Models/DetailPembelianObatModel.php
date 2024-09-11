<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianObatModel extends Model
{
    protected $table = 'detail_pembelian_obat';
    protected $primaryKey = 'id_detail_pembelian_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_pembelian_obat', 'id_obat', 'jumlah', 'harga_satuan'];
}
