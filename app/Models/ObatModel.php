<?php

namespace App\Models;

use CodeIgniter\Model;

class ObatModel extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_supplier', 'nama_obat', 'isi_obat', 'kategori_obat', 'bentuk_obat', 'harga_obat', 'ppn', 'mark_up', 'penyesuaian_harga', 'jumlah_masuk', 'jumlah_keluar', 'updated_at'];
}
