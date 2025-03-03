<?php

namespace App\Models;

use CodeIgniter\Model;

class FRMSetujuAnestesiModel extends Model
{
    protected $table = 'medrec_form_persetujuan_tindakan_anestesi';
    protected $primaryKey = 'id_form_persetujuan_tindakan_anestesi';
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
        'info_tindakan_pembedahan',
        'info_tindakan_anestesi',
        'info_indikasi_tindakan_anestesi',
        'info_tujuan_tindakan_anestesi',
        'info_tata_cara_teknis_anestesi',
        'info_status_fisik',
        'info_status_fisik_lainnya',
        'info_komplikasi_anestesi',
        'info_komplikasi_anestesi_lainnya',
        'info_alternatif_risiko',
        'info_alternatif_risiko_lainnya',
        'info_prognosis',
        'nama_saksi_1',
        'nama_saksi_2',
        'waktu_dibuat',
    ];
}
