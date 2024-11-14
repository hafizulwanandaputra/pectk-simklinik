<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $table = 'resep';
    protected $primaryKey = 'id_resep';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'no_rm', 'nama_pasien', 'alamat', 'telpon', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'dokter', 'apoteker', 'tanggal_resep', 'jumlah_resep', 'total_biaya', 'confirmed', 'status'];
}
