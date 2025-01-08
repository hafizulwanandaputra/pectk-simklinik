<?php

namespace App\Models;

use CodeIgniter\Model;

class RawatJalanModel extends Model
{
    protected $table = 'rawat_jalan';
    protected $primaryKey = 'id_rawat_jalan';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_pasien', 'nomor_registrasi', 'tanggal_registrasi', 'jenis_kunjungan', 'jaminan', 'ruangan', 'dokter', 'no_antrian', 'status', 'transaksi'];
}
