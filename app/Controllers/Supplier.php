<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Supplier extends BaseController
{
    protected $SupplierModel;
    public function __construct()
    {
        $this->SupplierModel = new SupplierModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan halaman supplier
            $data = [
                'title' => 'Supplier - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Supplier', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            return view('dashboard/supplier/index', $data); // Mengembalikan tampilan halaman supplier
        } else {
            throw PageNotFoundException::forPageNotFound(); // Menampilkan halaman tidak ditemukan jika peran tidak valid
        }
    }

    public function supplierlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data dari permintaan POST
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks awal untuk pagination
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Penghitung draw untuk DataTables

            // Mendapatkan parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // asc atau desc

            // Memetakan indeks kolom ke nama kolom database
            $columnMapping = [
                0 => 'id_supplier',
                1 => 'id_supplier',
                2 => 'nama_supplier',
                3 => 'merek',
                4 => 'alamat_supplier',
                5 => 'kontak_supplier',
                6 => 'jumlah_obat',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_supplier';

            // Mendapatkan total jumlah catatan
            $totalRecords = $this->SupplierModel->countAllResults(true);

            // Modifikasi logika pengurutan untuk menangani nama_supplier
            if ($sortColumn === 'nama_supplier') {
                // Mengurutkan berdasarkan nama_supplier, kemudian berdasarkan nama_obat
                $this->SupplierModel
                    ->orderBy('nama_supplier', $sortDirection)
                    ->orderBy('merek', 'ASC');
            } else {
                // Perilaku pengurutan default
                $this->SupplierModel->orderBy($sortColumn, $sortDirection);
            }

            // Menerapkan query pencarian
            if ($search) {
                $this->SupplierModel
                    ->like('nama_supplier', $search) // Mencari nama supplier
                    ->orLike('merek', $search); // Mencari nama supplier
            }

            // Mendapatkan jumlah catatan yang difilter
            $filteredRecords = $this->SupplierModel->countAllResults(false);

            // Mengambil data supplier
            $supplier = $this->SupplierModel
                ->select('supplier.*, (SELECT COUNT(*) FROM obat WHERE obat.id_supplier = supplier.id_supplier) as jumlah_obat')
                ->orderBy($sortColumn, $sortDirection)
                ->findAll($length, $start); // Mengambil data dengan pagination

            foreach ($supplier as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no' ke setiap item
            }

            // Mengembalikan respon JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $supplier // Data supplier yang diambil
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function supplier($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data supplier berdasarkan id
            $data = $this->SupplierModel->find($id);
            return $this->response->setJSON($data); // Mengembalikan data dalam format JSON
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_supplier' => 'required', // Nama supplier wajib diisi
                'alamat_supplier' => 'required', // Alamat supplier wajib diisi
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]); // Mengembalikan pesan kesalahan
            }

            // Menyimpan data supplier
            $data = [
                'nama_supplier' => $this->request->getPost('nama_supplier'), // Mengambil nama supplier dari input
                'merek' => $this->request->getPost('merek'), // Mengambil merek dari input
                'alamat_supplier' => $this->request->getPost('alamat_supplier'), // Mengambil alamat supplier dari input
                'kontak_supplier' => $this->request->getPost('kontak_supplier') // Mengambil kontak supplier dari input
            ];
            $this->SupplierModel->save($data); // Menyimpan data ke database
            return $this->response->setJSON(['success' => true, 'message' => 'Supplier berhasil ditambahkan']); // Mengembalikan pesan sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_supplier' => 'required', // Nama supplier wajib diisi
                'alamat_supplier' => 'required', // Alamat supplier wajib diisi
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]); // Mengembalikan pesan kesalahan
            }

            // Menyimpan data supplier yang telah diperbarui
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'), // Mengambil id_supplier dari input
                'nama_supplier' => $this->request->getPost('nama_supplier'), // Mengambil nama supplier dari input
                'merek' => $this->request->getPost('merek'), // Mengambil merek dari input
                'alamat_supplier' => $this->request->getPost('alamat_supplier'), // Mengambil alamat supplier dari input
                'kontak_supplier' => $this->request->getPost('kontak_supplier') // Mengambil kontak supplier dari input
            ];
            $this->SupplierModel->save($data); // Menyimpan data ke database
            return $this->response->setJSON(['success' => true, 'message' => 'Supplier berhasil diedit']); // Mengembalikan pesan sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect(); // Menghubungkan ke database

            try {
                $this->SupplierModel->delete($id); // Menghapus supplier berdasarkan id
                $db->query('ALTER TABLE `supplier` auto_increment = 1'); // Mengatur auto increment
                return $this->response->setJSON(['message' => 'Supplier berhasil dihapus']); // Mengembalikan pesan sukses
            } catch (DatabaseException $e) {
                // Mencatat pesan kesalahan
                log_message('error', $e->getMessage());

                // Mengembalikan pesan kesalahan umum
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $e->getMessage(), // Mengembalikan pesan kesalahan dari database
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }
}
