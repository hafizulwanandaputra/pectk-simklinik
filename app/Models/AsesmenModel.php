<?php

namespace App\Models;

use CodeIgniter\Model;

class AsesmenModel extends Model
{
    protected $table = 'medrec_assesment';
    protected $primaryKey = 'id_asesmen';
    protected $useTimestamps = false;
    protected $allowedFields = ['nomor_registrasi', 'no_rm', 'keluhan_utama', 'riwayat_penyakit_sekarang', 'riwayat_penyakit_dahulu', 'riwayat_penyakit_keluarga', 'riwayat_pengobatan', 'riwayat_sosial_pekerjaan', 'kesadaran', 'tekanan_darah', 'nadi', 'suhu', 'pernapasan', 'keadaan_umum', 'kesadaran_mental', 'alergi', 'alergi_keterangan', 'sakit_lainnya', 'od_ucva', 'od_bcva', 'os_ucva', 'os_bcva', 'diagnosa_medis_1', 'icdx_kode_1', 'icdx_nama_1', 'diagnosa_medis_2', 'icdx_kode_2', 'icdx_nama_2', 'diagnosa_medis_3', 'icdx_kode_3', 'icdx_nama_3', 'diagnosa_medis_4', 'icdx_kode_4', 'icdx_nama_4', 'diagnosa_medis_5', 'icdx_kode_5', 'icdx_nama_5', 'terapi_1', 'icd9_kode_1', 'icd9_nama_1', 'terapi_2', 'icd9_kode_2', 'icd9_nama_2', 'terapi_3', 'icd9_kode_3', 'icd9_nama_3', 'terapi_4', 'icd9_kode_4', 'icd9_nama_4', 'terapi_5', 'icd9_kode_5', 'icd9_nama_5', 'persetujuan_dokter', 'nama_dokter', 'tanggal_registrasi'];
}
