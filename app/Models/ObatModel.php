<?php

namespace App\Models;

use CodeIgniter\Model;

class ObatModel extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_supplier', 'nama_obat', 'kategori_obat', 'bentuk_obat', 'harga_obat', 'harga_jual', 'jumlah_masuk', 'jumlah_keluar', 'updated_at'];
}
