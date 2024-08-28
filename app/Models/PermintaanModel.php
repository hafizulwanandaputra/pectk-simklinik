<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanModel extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_menu', 'nama_pasien', 'tanggal_lahir', 'jenis_kelamin', 'kamar', 'jenis_tindakan', 'diet', 'keterangan'];
    public function getPermintaanAll()
    {
        $builder = $this->table('permintaan');
        return $builder->findAll();
    }
    public function getPermintaanInit()
    {
        $builder = $this->table('permintaan');
        return $builder->first();
    }
    public function getPermintaan($id = false)
    {
        $builder = $this->table('permintaan');
        $builder->where('id', $id);
        return $builder->first();
    }
    public function search($keyword)
    {
        $builder = $this->table('permintaan');
        $builder->where('permintaan.id_menu', $keyword);
        return $builder;
    }
}
