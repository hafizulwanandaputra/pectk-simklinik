<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemObatModel extends Model
{
    protected $table = 'item_obat';
    protected $primaryKey = 'id_item_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_detail_pembelian_obat', 'no_batch', 'expired', 'jumlah_item'];
}
