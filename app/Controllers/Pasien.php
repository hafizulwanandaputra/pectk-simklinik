<?php

namespace App\Controllers;

use App\Models\PasienModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pasien extends BaseController
{
    protected $PasienModel;
    public function __construct()
    {
        $this->PasienModel = new PasienModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Pasien - ' . $this->systemName,
                'headertitle' => 'Pasien',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/pasien/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $PasienModel = $this->PasienModel;

            $PasienModel->select('pasien.*'); // Mengambil semua kolom dari tabel pasien

            // Menerapkan filter pencarian berdasarkan nomor rekam medis dan nama pasien, pasien
            if ($search) {
                $PasienModel->groupStart()
                    ->orLike('no_rm', $search)
                    ->like('nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $PasienModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $PasienModel->orderBy('id_pasien', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap pasien
            $dataPasien = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data; // Mengembalikan data yang telah ditambahkan nomor urut
            }, $Pasien, array_keys($Pasien));

            // Mengembalikan data pasien dalam format JSON
            return $this->response->setJSON([
                'pasien' => $dataPasien,
                'total' => $total // Mengembalikan total hasil
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
