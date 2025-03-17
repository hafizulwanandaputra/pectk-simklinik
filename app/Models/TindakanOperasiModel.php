<?php

namespace App\Models;

use CodeIgniter\Model;

class TindakanOperasiModel extends Model
{
    protected $table = 'master_tindakan_operasi';
    protected $primaryKey = 'id_tindakanok';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nama_tindakan'
    ];
}
