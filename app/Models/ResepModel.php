<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $table = 'resep';
    protected $primaryKey = 'id_resep';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_pasien', 'id_user', 'tanggal_resep', 'jumlah_resep', 'total_biaya', 'keterangan', 'status'];
}
