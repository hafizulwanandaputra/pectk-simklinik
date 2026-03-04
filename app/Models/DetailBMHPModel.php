<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailBMHPModel extends Model
{
    protected $table = 'detail_bmhp';
    protected $primaryKey = 'id_detail_bmhp';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id_bmhp',
        'id_obat',
        'id_batch_obat',
        'nama_obat',
        'kategori_obat',
        'bentuk_obat',
        'nama_batch',
        'jumlah',
        'harga_satuan'
    ];
}
