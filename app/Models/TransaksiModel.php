<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_user', 'id_pasien', 'tgl_transaksi', 'total_pembayaran', 'metode_pembayaran', 'lunas'];
}
