<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    protected $useTimestamps = false;
    protected $allowedFields = ['nama_pasien', 'no_mr', 'no_registrasi', 'jenis_pasien', 'tanggal_lahir', 'alamat_pasien', 'tgl_pendaftaran'];
}
