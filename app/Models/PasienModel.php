<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    protected $useTimestamps = false;
    protected $allowedFields = ['no_rm', 'nama_pasien', 'nik', 'no_bpjs', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'rt', 'rw', 'telpon', 'kewarganegaraan', 'agama', 'status_nikah', 'pekerjaan'];
}
