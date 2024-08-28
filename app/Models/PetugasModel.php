<?php

namespace App\Models;

use CodeIgniter\Model;

class PetugasModel extends Model
{
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_petugas', 'jumlah_menu'];
    public function getPetugas($id = false)
    {
        $builder = $this->table('petugas');
        $builder->where('id_petugas', $id);
        return $builder->first();
    }
    public function getPetugasJoin($id = false)
    {
        $builder = $this->table('petugas')->join('menu', 'menu.id_petugas = petugas.id_petugas', 'inner')->join('permintaan', 'permintaan.id_menu = menu.id_menu', 'inner');
        $builder->where('petugas.id_petugas', $id);
        return $builder->first();
    }
}
