<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetySignOutModel extends Model
{
    protected $table = 'medrec_operasi_safety_signout';
    protected $primaryKey = 'id_signout';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'no_rm',
        'nomor_registrasi',
        'nomor_booking',
        'ns_konfirmasi_identitas',
        'kelengkapan_instrumen',
        'spesimen_kultur',
        'label_pasien',
        'masalah_instrumen',
        'keterangan_masalah',
        'instruksi_khusus',
        'keterangan_instruksi',
        'nama_dokter_operator',
        'tanda_tangan_dokter_operator',
        'waktu_dibuat',
    ];
}
