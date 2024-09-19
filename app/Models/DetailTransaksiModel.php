<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail_transaksi';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_resep', 'id_transaksi', 'harga_resep', 'diskon'];
}
