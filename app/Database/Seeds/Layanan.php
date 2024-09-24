<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Layanan extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_layanan' => 'Administrasi Pasien Baru',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 25000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Administrasi Pasien Lama',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 10000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Administrasi Pasien Operasi',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 125000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Jasa Konsultasi Dokter',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 100000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Jasa Konsultasi Dokter (Khusus/2 Dokter)',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 110000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Jasa Konsultasi Dokter (3 Dokter)',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 165000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Jasa Konsultasi Dokter Emergency (Libur)',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 150000,
                'keterangan' => '',
            ],
            [
                'nama_layanan' => 'Dokter Umum IGD + Dokter Mata IGD',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 200000,
                'keterangan' => 'Rp50.000 + Rp150.000',
            ],
            [
                'nama_layanan' => 'Dokter Umum IGD + Dokter Mata Biasa',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 150000,
                'keterangan' => 'Rp50.000 + Rp100.000',
            ],
            [
                'nama_layanan' => 'Dokter Mata IGD + Dokter Mata Biasa',
                'jenis_layanan' => 'Rawat Jalan',
                'tarif' => 150000,
                'keterangan' => '',
            ],
        ];

        $this->db->table('layanan')->insertBatch($data);
    }
}
