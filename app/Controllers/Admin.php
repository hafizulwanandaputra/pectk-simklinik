<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\AuthModelEdit;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Admin extends BaseController
{
    protected $AuthModel;
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == "Admin") {
            // Jika ya, siapkan data untuk ditampilkan di tampilan
            $data = [
                'title' => 'Pengguna - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Pengguna', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent dari permintaan
            ];
            // Mengembalikan tampilan dengan data yang telah disiapkan
            return view('dashboard/admin/index', $data);
        } else {
            // Jika bukan admin, lemparkan pengecualian halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function adminlist()
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
                0 => 'id_user',
                1 => 'id_user',
                2 => 'fullname',
                3 => 'username',
                4 => 'role',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_user';

            // Mendapatkan jumlah total catatan
            $totalRecords = $this->AuthModel->where('id_user !=', session()->get('id_user'))->countAllResults(true);

            // Menerapkan kueri pencarian
            if ($search) {
                $this->AuthModel
                    ->groupStart()
                    ->like('fullname', $search) // Mencari berdasarkan fullname
                    ->orLike('username', $search) // Mencari berdasarkan username
                    ->groupEnd()
                    ->where('id_user !=', session()->get('id_user')) // Mengabaikan pengguna saat ini
                    ->orderBy($sortColumn, $sortDirection); // Mengurutkan hasil
            }

            // Mendapatkan jumlah catatan yang terfilter
            $filteredRecords = $this->AuthModel->where('id_user !=', session()->get('id_user'))->countAllResults(false);

            // Mengambil data pengguna
            $users = $this->AuthModel->where('id_user !=', session()->get('id_user'))
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

    public function admin($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Mengambil data pengguna berdasarkan ID, kecuali pengguna yang sedang login
            $data = $this->AuthModel->where('id_user !=', session()->get('id_user'))->find($id);
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
                'fullname' => 'required|min_length[3]', // Fullname wajib diisi dan minimal 3 karakter
                'username' => 'required|is_unique[user.username]|min_length[3]|alpha_dash', // Username wajib diisi, unik, dan memenuhi syarat
                'role' => 'required' // Role wajib diisi
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pengguna baru
            $data = [
                'fullname' => $this->request->getPost('fullname'),
                'username' => $this->request->getPost('username'),
                // Menghitung hash password menggunakan username
                'password' => password_hash($this->request->getPost('username'), PASSWORD_DEFAULT),
                'role' => $this->request->getPost('role'),
                'active' => 0, // Status aktif pengguna awalnya diset ke 0
                'registered' => date('Y-m-d H:i:s') // Tanggal pendaftaran saat ini
            ];
            // Menyimpan data ke dalam model
            $this->AuthModel->save($data);
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil ditambahkan']);
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
                'fullname' => 'required|min_length[3]', // Fullname wajib diisi dan minimal 3 karakter
                'username' => 'required|min_length[3]|alpha_dash', // Username wajib diisi dan memenuhi syarat
                'role' => 'required' // Role wajib diisi
            ]);
            // Validasi hanya jika username telah diubah
            if ($this->request->getPost('username') != $this->request->getPost('original_username')) {
                $validation->setRule('username', 'username', 'required|is_unique[user.username]|min_length[3]|alpha_dash'); // Pastikan username unik
            }
            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pengguna yang diperbarui
            $data = [
                'id_user' => $this->request->getPost('id_user'), // ID pengguna
                'fullname' => $this->request->getPost('fullname'), // Fullname
                'username' => $this->request->getPost('username'), // Username
                'role' => $this->request->getPost('role'), // Role
            ];
            // Menggunakan model untuk menyimpan data yang telah diperbarui
            $AuthModelEdit = new AuthModelEdit();
            $AuthModelEdit->save($data);
            // Hapus semua sesi pengguna terkait di tabel `user_sessions`
            // Validasi hanya jika username telah diubah
            if ($this->request->getPost('username') != $this->request->getPost('original_username')) {
                $db = db_connect();
                $db->table('user_sessions')
                    ->where('id_user', $this->request->getPost('id_user'))
                    ->delete();
                $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
            }
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diedit']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }


    public function resetpassword($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            $db = db_connect(); // Menghubungkan ke database
            // Mengambil data pengguna berdasarkan ID
            $user = $this->AuthModel->find($id);
            // Menghash kata sandi baru
            $password_hash = password_hash($user['username'], PASSWORD_DEFAULT);
            // Mengatur ulang kata sandi pengguna dengan hash dari username
            $db->table('user')
                ->set('password', $password_hash)
                ->where('id_user', $id)
                ->update();
            // Hapus semua sesi pengguna terkait di tabel `user_sessions`
            $db->table('user_sessions')
                ->where('id_user', $id)
                ->delete();
            $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Kata sandi pengguna berhasil diatur ulang']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function activate($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            $db = db_connect(); // Menghubungkan ke database
            // Mengubah status pengguna menjadi aktif
            $db->table('user')->set('active', 1)->where('id_user', $id)->update();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diaktifkan']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function deactivate($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            $db = db_connect(); // Menghubungkan ke database
            // Mengubah status pengguna menjadi tidak aktif
            $db->table('user')->set('active', 0)->where('id_user', $id)->update();
            // Hapus semua sesi pengguna terkait di tabel `user_sessions`
            $db->table('user_sessions')
                ->where('id_user', $id)
                ->delete();
            $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil dinonaktifkan']);
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
                // Menghapus pengguna berdasarkan ID
                $this->AuthModel->delete($id);
                $db = db_connect(); // Menghubungkan ke database
                // Mengatur ulang nilai Auto Increment
                $db->query('ALTER TABLE `user` auto_increment = 1');
                // Mengembalikan respons JSON sukses
                return $this->response->setJSON(['message' => 'Pengguna berhasil dihapus']);
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
}
