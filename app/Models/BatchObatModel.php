<?php

namespace App\Models;

use CodeIgniter\Model;

class BatchObatModel extends Model
{
    protected $table = 'batch_obat';
    protected $primaryKey = 'id_batch_obat';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id_obat',
        'nama_batch',
        'tgl_kedaluwarsa',
        'jumlah_masuk',
        'jumlah_keluar',
        'diperbarui',
    ];
}
