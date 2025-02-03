<?php

namespace App\Models;

use CodeIgniter\Model;

class SafetySignInModel extends Model
{
    protected $table = 'medrec_operasi_safety_signin';
    protected $primaryKey = 'id_signin';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'no_rm',
        'nomor_registrasi',
        'nomor_booking',
        'ns_konfirmasi_identitas',
        'dr_konfirmasi_identitas',
        'ns_marker_operasi',
        'dr_marker_operasi',
        'ns_inform_consent_sesuai',
        'dr_inform_consent_sesuai',
        'ns_identifikasi_alergi',
        'dr_identifikasi_alergi',
        'ns_puasa',
        'dr_puasa',
        'ns_cek_lensa_intrakuler',
        'ns_konfirmasi_lensa',
        'dr_cek_anestesi_khusus',
        'dr_konfirmasi_anestersi',
        'nama_dokter_anastesi',
        'tanda_dokter_anastesi',
        'waktu_dibuat',
    ];
}
