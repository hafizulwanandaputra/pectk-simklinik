<?php

namespace App\Models;

use CodeIgniter\Model;

class SPOperasiModel extends Model
{
    protected $table = 'medrec_sp_operasi';
    protected $primaryKey = 'id_sp_operasi';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nomor_booking',
        'nomor_registrasi',
        'no_rm',
        'tanggal_operasi',
        'jam_operasi',
        'diagnosa',
        'jenis_tindakan',
        'indikasi_operasi',
        'jenis_bius',
        'tipe_bayar',
        'rajal_ranap',
        'ruang_operasi',
        'dokter_operator',
        'status_operasi',
        'stie_marking',
        'nama_pasien_keluarga',
        'tanda_tangan_pasien',
        'waktu_dibuat'
    ];
    public function getLastNoBooking($tahun, $bulan, $tanggal)
    {
        return $this
            ->where('nomor_booking LIKE', 'OK' . $tahun . $bulan . $tanggal . '%')
            ->orderBy('nomor_booking', 'DESC')
            ->limit(1)
            ->findColumn('nomor_booking')[0] ?? null;
    }
}
