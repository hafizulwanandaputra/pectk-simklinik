<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetySignOutModel extends Model
{
    protected $table = 'medrec_operasi_safety_timeout';
    protected $primaryKey = 'id_timeout';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'no_rm',
        'nomor_registrasi',
        'nomor_booking',
        'perkenalan_diri',
        'cek_nama_mr',
        'cek_rencana_tindakan',
        'cek_marker',
        'alergi',
        'lateks',
        'proteksi',
        'proteksi_kasa',
        'proteksi_shield',
        'info_instrumen_ok',
        'info_teknik_ok',
        'info_steril_instrumen',
        'info_kelengkapan_instrumen',
        'perlu_antibiotik_dan_guladarah',
        'nama_perawat',
        'tanda_tangan_perawat',
        'waktu_dibuat',
    ];
}
