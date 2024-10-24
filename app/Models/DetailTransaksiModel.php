<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail_transaksi';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_resep', 'id_layanan', 'id_transaksi', 'nama_layanan', 'jenis_transaksi', 'qty_transaksi', 'harga_transaksi', 'diskon'];
}
