<?php

namespace App\Models;

use CodeIgniter\Model;

class BMHPModel extends Model
{
    protected $table = 'bmhp';
    protected $primaryKey = 'id_bmhp';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'tanggal_bmhp',
        'apoteker',
        'jumlah_bmhp',
        'total_biaya',
        'konfirmasi_kasir',
        'diperbarui',
        'status'
    ];
}
