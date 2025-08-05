<?php

namespace App\Controllers;

use App\Models\LoketModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Loket extends BaseController
{
    protected $LoketModel;
    public function __construct()
    {
        $this->LoketModel = new LoketModel();
    }

    public function index()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == "Admin") {
            // Jika ya, siapkan data untuk ditampilkan di tampilan
            $data = [
                'title' => 'Loket - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Loket', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent dari permintaan
            ];
            // Mengembalikan tampilan dengan data yang telah disiapkan
            return view('dashboard/loket/index', $data);
        } else {
            // Jika bukan admin, lemparkan pengecualian halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function loketlist()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Mengambil data dari permintaan POST
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks awal untuk paginasi
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Hitungan gambar untuk DataTables

            // Mendapatkan parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // Arah pengurutan (asc atau desc)

            // Pemetaan indeks kolom ke nama kolom di database
            $columnMapping = [
                0 => 'id_loket',
                1 => 'id_loket',
                2 => 'nama_loket',
                3 => 'status',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_user';

            // Mendapatkan jumlah total catatan
            $totalRecords = $this->LoketModel->countAllResults(true);

            // Menerapkan kueri pencarian
            if ($search) {
                $this->LoketModel
                    ->groupStart()
                    ->like('nama_loket', $search)
                    ->groupEnd()
                    ->orderBy($sortColumn, $sortDirection); // Mengurutkan hasil
            }

            // Mendapatkan jumlah catatan yang terfilter
            $filteredRecords = $this->LoketModel->countAllResults(false);

            // Mengambil data ruangan poliklinik
            $users = $this->LoketModel
                ->orderBy($sortColumn, $sortDirection) // Mengurutkan hasil
                ->findAll($length, $start); // Mengambil hasil dengan batasan panjang dan awal

            // Menambahkan kolom 'no' untuk menandai urutan
            foreach ($users as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords, // Total catatan
                'recordsFiltered' => $filteredRecords, // Catatan terfilter
                'data' => $users // Data pengguna
            ]);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan error
            ]);
        }
    }

    public function loket($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Mengambil data ruangan loket
            $data = $this->LoketModel->find($id);
            // Mengembalikan respons JSON dengan data pengguna
            return $this->response->setJSON($data);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menginisialisasi layanan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_loket' => 'required|is_unique[master_loket.nama_loket]',
                'status' => 'required'
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pengguna baru
            $data = [
                'nama_loket' => $this->request->getPost('nama_loket'),
                'status' => $this->request->getPost('status')
            ];
            // Menyimpan data ke dalam model
            $this->LoketModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Loket berhasil ditambahkan']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }


    public function update()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menginisialisasi layanan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_loket' => 'required',
                'status' => 'required'
            ]);
            // Validasi hanya jika nama ruangan poliklinik telah diubah
            if ($this->request->getPost('nama_loket') != $this->request->getPost('nama_loket_old')) {
                $validation->setRule('nama_loket', 'nama_loket', 'required|is_unique[master_loket.nama_loket]'); // Pastikan nama ruangan poliklinik unik
            }
            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pengguna baru
            $data = [
                'id_loket' => $this->request->getPost('id_loket'),
                'nama_loket' => $this->request->getPost('nama_loket'),
                'status' => $this->request->getPost('status')
            ];
            // Menyimpan data ke dalam model
            $this->LoketModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Loket berhasil diedit']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            try {
                // Menghapus ruangan poliklinik
                $this->LoketModel->delete($id);
                $db = db_connect(); // Menghubungkan ke database
                // Mengatur ulang nilai Auto Increment
                $db->query('ALTER TABLE `master_loket` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons JSON sukses
                return $this->response->setJSON(['message' => 'Loker berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Mencatat pesan kesalahan
                log_message('error', $e->getMessage());

                // Mengembalikan pesan kesalahan generik
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function notify_clients()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => []
        ]);

        return $this->response->setJSON([
            'status' => 'Notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
