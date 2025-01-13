<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\AsesmenModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Asesmen extends BaseController
{
    protected $RawatJalanModel;
    protected $AsesmenModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->AsesmenModel = new AsesmenModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah asesmen sudah ada
            $asesmen = $db->table('medrec_assesment')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$asesmen) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_assesment')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'nama_dokter' => $rawatjalan['dokter'],
                    'tanggal_registrasi' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $asesmen = $db->table('medrec_assesment')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getRowArray();
            }

            // Query untuk item sebelumnya
            $previous = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan <', $id) // Kondisi untuk id sebelumnya
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan >', $id) // Kondisi untuk id berikutnya
                ->orderBy('rawat_jalan.id_rawat_jalan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'asesmen' => $asesmen,
                'title' => 'Asesmen ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Asesmen',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/rawatjalan/asesmen/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data asesmen berdasarkan ID
            $data = $this->AsesmenModel->find($id); // Mengambil asesmen
            return $this->response->setJSON($data); // Mengembalikan data asesmen dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icdx()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mendapatkan parameter pencarian dari permintaan GET
            $search = $this->request->getGet('search');

            // Jika parameter pencarian kosong, kembalikan data kosong
            if (empty($search)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [] // Data kosong
                ]);
            }

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_x');
            $builder->select('icdKode, icdNamaIndonesia');

            // Menambahkan filter pencarian
            $builder->like('icdKode', $search);

            $result = $builder->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icd9()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mendapatkan parameter pencarian dari permintaan GET
            $search = $this->request->getGet('search');

            // Jika parameter pencarian kosong, kembalikan data kosong
            if (empty($search)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [] // Data kosong
                ]);
            }

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_9');
            $builder->select('icdKode, icdNamaIndonesia');

            // Menambahkan filter pencarian
            $builder->like('icdKode', $search);

            $result = $builder->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
