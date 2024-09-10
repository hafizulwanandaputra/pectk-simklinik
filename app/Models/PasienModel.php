<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_dokter', 'nama_pasien', 'jenis_kelamin', 'no_mr', 'no_registrasi', 'nik', 'jenis_pasien', 'tempat_lahir', 'tanggal_lahir', 'agama_pasien', 'no_hp_pasien', 'alamat_pasien', 'provinsi', 'kota', 'kecamatan', 'desa', 'tgl_pendaftaran'];

    public function getLastNoReg($jenis_pasien, $tahun, $bulan, $tanggal)
    {
        return $this->where('jenis_pasien', $jenis_pasien)
            ->where('no_registrasi LIKE', 'RJ' . $jenis_pasien . $tanggal . $bulan . $tahun . '%')
            ->orderBy('no_registrasi', 'DESC')
            ->limit(1)
            ->findColumn('no_registrasi')[0] ?? null;
    }
}
