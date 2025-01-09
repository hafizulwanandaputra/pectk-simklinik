<?php

namespace App\Models;

use CodeIgniter\Model;

class RawatJalanModel extends Model
{
    protected $table = 'rawat_jalan';
    protected $primaryKey = 'id_rawat_jalan';
    protected $useTimestamps = false;
    protected $allowedFields = ['no_rm', 'nomor_registrasi', 'tanggal_registrasi', 'jenis_kunjungan', 'status_kunjungan', 'jaminan', 'ruangan', 'dokter', 'keluhan', 'alasan_batal', 'pembatal', 'kode_antrian', 'no_antrian', 'pendaftar', 'status', 'transaksi'];
}
