<?php

namespace App\Controllers;

use App\Models\LayananModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Layanan extends BaseController
{
    protected $LayananModel;
    public function __construct()
    {
        $this->LayananModel = new LayananModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengatur data untuk tampilan
            $data = [
                'title' => 'Tindakan - ' . $this->systemName,
                'headertitle' => 'Tindakan',
                'agent' => $this->request->getUserAgent()
            ];
            // Mengembalikan tampilan layanan
            return view('dashboard/layanan/index', $data);
        } else {
            // Jika peran tidak dikenali, lempar pengecualian untuk halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function layananlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks mulai untuk paginasi
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Hitungan draw untuk DataTables

            // Mengambil parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // arah asc atau desc

            // Memetakan indeks kolom ke nama kolom di database
            $columnMapping = [
                0 => 'id_layanan',
                1 => 'id_layanan',
                2 => 'nama_layanan',
                3 => 'jenis_layanan',
                4 => 'tarif',
                5 => 'keterangan',
            ];

            // Mengambil kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_layanan';

            // Menghitung total record
            $totalRecords = $this->LayananModel->countAllResults(true);

            // Memodifikasi logika pengurutan untuk menangani jenis_layanan
            if ($sortColumn === 'jenis_layanan') {
                // Mengurutkan berdasarkan jenis_layanan, kemudian berdasarkan nama_layanan
                $this->LayananModel
                    ->orderBy('jenis_layanan', $sortDirection)
                    ->orderBy('id_layanan', 'ASC');
            } else {
                // Perilaku pengurutan default
                $this->LayananModel->orderBy($sortColumn, $sortDirection);
            }

            // Menerapkan kueri pencarian
            if ($search) {
                $this->LayananModel
                    ->like('nama_layanan', $search);
            }

            // Menghitung jumlah record yang difilter
            $filteredRecords = $this->LayananModel->countAllResults(false);

            // Mengambil data
            $layanan = $this->LayananModel
                ->findAll($length, $start);

            // Menambahkan penomoran langsung ke $layanan
            foreach ($layanan as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $layanan
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function layanan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data layanan berdasarkan ID
            $data = $this->LayananModel->find($id);
            return $this->response->setJSON($data);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi
            $validation = \Config\Services::validation();
            // Mengatur aturan validasi dasar
            $validation->setRules([
                'nama_layanan' => 'required',
                'jenis_layanan' => 'required',
                'tarif' => 'required|numeric|greater_than[0]',
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data
            $data = [
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'jenis_layanan' => $this->request->getPost('jenis_layanan'),
                'tarif' => $this->request->getPost('tarif'),
                'keterangan' => $this->request->getPost('keterangan')
            ];
            $this->LayananModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil ditambahkan']);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi
            $validation = \Config\Services::validation();
            // Mengatur aturan validasi dasar
            $validation->setRules([
                'nama_layanan' => 'required',
                'jenis_layanan' => 'required',
                'tarif' => 'required|numeric|greater_than[0]',
            ]);
            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data
            $data = [
                'id_layanan' => $this->request->getPost('id_layanan'),
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'jenis_layanan' => $this->request->getPost('jenis_layanan'),
                'tarif' => $this->request->getPost('tarif'),
                'keterangan' => $this->request->getPost('keterangan')
            ];
            $this->LayananModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil diedit']);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $db = db_connect();

            try {
                // Menghapus data layanan berdasarkan ID
                $this->LayananModel->delete($id);
                // Mengatur auto increment
                $db->query('ALTER TABLE `layanan` auto_increment = 1');
                return $this->response->setJSON(['message' => 'Layanan berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Mencatat pesan kesalahan
                log_message('error', $e->getMessage());

                // Mengembalikan pesan kesalahan generik
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
