<?php

namespace App\Controllers;

use App\Models\SessionsModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Sessions extends BaseController
{
    protected $SessionsModel;
    public function __construct()
    {
        $this->SessionsModel = new SessionsModel();
    }

    public function index()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == "Admin") {
            // Jika ya, siapkan data untuk ditampilkan di tampilan
            $data = [
                'title' => 'Manajer Sesi - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Manajer Sesi', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent dari permintaan
            ];
            // Mengembalikan tampilan dengan data yang telah disiapkan
            return view('dashboard/settings/sessions', $data);
        } else {
            // Jika bukan admin, lemparkan pengecualian halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function sessionslist()
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
                0 => 'id',
                1 => 'id',
                2 => 'username',
                3 => 'ip_address',
                4 => 'user_agent',
                5 => 'created_at',
                6 => 'expires_at',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id';

            // Mendapatkan jumlah total catatan
            $totalRecords = $this->SessionsModel->where('session_token !=', session()->get('session_token'))->countAllResults(true);

            // Menerapkan kueri pencarian
            if ($search) {
                $this->SessionsModel
                    ->groupStart()
                    ->like('username', $search) // Mencari berdasarkan username
                    ->orLike('ip_address', $search) // Mencari berdasarkan alamat IP
                    ->orLike('user_agent', $search) // Mencari berdasarkan user agent
                    ->groupEnd()
                    ->where('session_token !=', session()->get('session_token')) // Mengabaikan token sesi saat ini
                    ->orderBy($sortColumn, $sortDirection); // Mengurutkan hasil
            }

            // Mendapatkan jumlah catatan yang terfilter
            $filteredRecords = $this->SessionsModel->where('session_token !=', session()->get('session_token'))->countAllResults(false);

            // Mengambil data sesi
            $sessions = $this->SessionsModel
                ->join('user', 'user.id_user = user_sessions.id_user', 'inner')
                ->where('session_token !=', session()->get('session_token'))
                ->orderBy($sortColumn, $sortDirection) // Mengurutkan hasil
                ->findAll($length, $start); // Mengambil hasil dengan batasan panjang dan awal

            // Menambahkan kolom 'no' untuk menandai urutan
            foreach ($sessions as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords, // Total catatan
                'recordsFiltered' => $filteredRecords, // Catatan terfilter
                'data' => $sessions // Data sesi
            ]);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan error
            ]);
        }
    }

    public function flush()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            /// Ambil token sesi perangkat aktif dari sesi saat ini
            $current_token = session()->get('session_token');
            // Hapus semua sesi kecuali perangkat aktif
            $db->table('user_sessions')
                ->where('session_token !=', $current_token)
                ->delete();
            $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['message' => 'Sesi pengguna berhasil dibersihkan']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function deleteexpired()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menghubungkan ke database
            $db = db_connect();
            $builder = $db->table('user_sessions');

            // Mengecek apakah ada sesi yang kadaluwarsa
            $expiredSessions = $builder->where('expires_at <', date('Y-m-d H:i:s'))->countAllResults();

            if ($expiredSessions > 0) {
                // Menghapus sesi yang kadaluwarsa
                $builder->where('expires_at <', date('Y-m-d H:i:s'))->delete();

                // Mengatur ulang nilai Auto Increment
                $db->query('ALTER TABLE `user_sessions` auto_increment = 1');

                // Mengembalikan respons JSON sukses
                return $this->response->setJSON(['message' => 'Sesi pengguna yang kadaluwarsa berhasil dihapus']);
            } else {
                // Jika tidak ada sesi yang kadaluwarsa, mengembalikan status 404 dengan pesan error
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Tidak ada sesi yang kadaluwarsa untuk dihapus',
                ]);
            }
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function deletesession($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Cek apakah sesi dengan ID yang diberikan ada
            $session = $this->SessionsModel->find($id);
            if ($session) {
                // Menghapus pengguna berdasarkan ID
                $this->SessionsModel->delete($id);
                $db = db_connect(); // Menghubungkan ke database
                // Mengatur ulang nilai Auto Increment
                $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
                // Mengembalikan respons JSON sukses
                return $this->response->setJSON(['message' => 'Sesi pengguna berhasil dihapus']);
            } else {
                // Jika sesi tidak ditemukan, mengembalikan status 404 dengan pesan error
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'ID sesi tidak ditemukan. Mungkin karena pengguna tersebut sudah keluar dari sesinya.',
                ]);
            }
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
