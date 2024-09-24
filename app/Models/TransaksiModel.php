<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_user', 'id_pasien', 'no_kwitansi', 'tgl_transaksi', 'total_pembayaran', 'metode_pembayaran', 'lunas'];

    public function getLastNoReg($tahun, $bulan, $tanggal)
    {
        return $this
            ->where('no_kwitansi LIKE', 'TRJ' . $tanggal . $bulan . $tahun . '%')
            ->orderBy('no_kwitansi', 'DESC')
            ->limit(1)
            ->findColumn('no_kwitansi')[0] ?? null;
    }
}
