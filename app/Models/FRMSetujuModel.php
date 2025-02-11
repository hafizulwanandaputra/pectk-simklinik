<?php

namespace App\Models;

use CodeIgniter\Model;

class FRMSetujuModel extends Model
{
    protected $table = 'medrec_form_persetujuan_tindakan';
    protected $primaryKey = 'id_form_persetujuan_tindakan';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_registrasi',
        'no_rm',
        'dokter_pelaksana',
        'pemberi_informasi',
        'penerima_informasi',
        'pererima_tanggal_lahir',
        'penerima_jenis_kelamin',
        'penerima_alamat',
        'penerima_hubungan',
        'keterangan_hubungan',
        'tindakan_kedoteran',
        'tanggal_tindakan',
        'info_diagnosa',
        'info_dasar_diagnosis',
        'info_tindakan',
        'info_indikasi',
        'info_tatacara',
        'info_tujuan',
        'info_resiko',
        'info_komplikasi',
        'info_prognosis',
        'info_alternatif',
        'info_lainnya',
        'nama_saksi_1',
        'nama_saksi_2',
        'waktu_dibuat',
    ];
}
