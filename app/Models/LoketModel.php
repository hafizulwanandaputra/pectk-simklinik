<?php

namespace App\Models;

use CodeIgniter\Model;

class LoketModel extends Model
{
    protected $table = 'master_loket';
    protected $primaryKey = 'id_loket';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nama_loket',
        'status'
    ];
}
