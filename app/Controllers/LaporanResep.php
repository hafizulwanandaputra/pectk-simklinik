<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DetailResepModel;

class LaporanResep extends BaseController
{
    protected $DetailResepModel;
    public function __construct()
    {
        $this->DetailResepModel = new DetailResepModel();
    }

    // public function index()
    // {
    //     //
    // }

    public function export($tanggal)
    {
        // Memeriksa peran pengguna, hanya 'Admin' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Ambil laporan resep
            $laporanresep = $this->DetailResepModel
                ->select('obat.nama_obat AS nama_obat, 
                    SUM(detail_resep.jumlah) AS total_keluar, 
                    detail_resep.harga_satuan AS harga_satuan, 
                    (SUM(detail_resep.jumlah) * harga_satuan) AS total_harga')
                ->join('resep', 'resep.id_resep = detail_resep.id_resep')
                ->join('obat', 'obat.id_obat = detail_resep.id_obat')
                ->where('DATE(resep.tanggal_resep)', $tanggal) // Kondisi berdasarkan tanggal
                ->groupBy('obat.nama_obat, DATE(resep.tanggal_resep)')
                ->findAll();

            // Hitung total keseluruhan obat keluar dan harga
            $totalKeluarKeseluruhan = array_sum(array_column($laporanresep, 'total_keluar'));
            $totalHargaKeseluruhan = array_sum(array_column($laporanresep, 'total_harga'));

            // Kirim dalam bentuk JSON
            return $this->response->setStatusCode(200)->setJSON([
                'laporanresep' => $laporanresep,
                'tanggal' => $tanggal,
                'total_keluar_keseluruhan' => $totalKeluarKeseluruhan,
                'total_harga_keseluruhan' => $totalHargaKeseluruhan
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
