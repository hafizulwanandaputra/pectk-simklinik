<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_petugas', 'tanggal', 'nama_menu', 'jadwal_makan', 'protein_hewani', 'protein_nabati', 'sayur', 'buah', 'jumlah'];
    public function getMenu($id = false)
    {
        $builder = $this->table('menu');
        $builder->where('id_menu', $id);
        return $builder->first();
    }
    public function getMenuJoin($id = false)
    {
        $builder = $this->table('menu');
        $this->builder->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner');
        $builder->where('id_menu', $id);
        return $builder->first();
    }
}
