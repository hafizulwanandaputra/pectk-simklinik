<?php

namespace App\Models;

use CodeIgniter\Model;

class FRMSetujuPhacoModel extends Model
{
    protected $table = 'medrec_form_persetujuan_tindakan_phaco';
    protected $primaryKey = 'id_form_persetujuan_tindakan_phaco';
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
        'info_tujuan',
        'info_tujuan_lainnya',
        'info_risiko',
        'info_risiko_lainnya',
        'info_komplikasi',
        'info_komplikasi_lainnya',
        'info_lain_lain',
        'nama_saksi_1',
        'nama_saksi_2',
        'waktu_dibuat',
    ];
}
