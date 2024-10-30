<?php

namespace App\Controllers;

use App\Models\SettingsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Settings extends BaseController
{
    protected $SettingsModel;
    public function __construct()
    {
        $this->SettingsModel = new SettingsModel();
    }

    public function index()
    {
        // Menyiapkan data untuk tampilan halaman pengaturan
        $data = [
            'title' => 'Pengaturan - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Pengaturan', // Judul header
            'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
        ];
        return view('dashboard/settings/index', $data); // Mengembalikan tampilan halaman pengaturan
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
            // Mengatur flashdata untuk menampilkan pesan sukses
            session()->setFlashdata('msg', 'Anda berhasil membersihkan sesi!');
            return redirect()->back(); // Kembali ke halaman sebelumnya
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function edit()
    {
        // Menyiapkan data untuk tampilan halaman edit informasi pengguna
        $data = [
            'title' => 'Ubah Informasi Pengguna - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Ubah Informasi Pengguna', // Judul header
            'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
            'validation' => \Config\Services::validation() // Mengambil layanan validasi
        ];
        echo view('dashboard/settings/edit', $data); // Mengembalikan tampilan halaman edit
    }

    public function update()
    {
        // Memeriksa apakah username yang diinput sama dengan username yang ada di session
        if (session()->get('username') == $this->request->getVar('username')) {
            $username = 'required|alpha_numeric_punct'; // Aturan validasi jika username tidak berubah
        } else {
            $username = 'required|is_unique[user.username]|alpha_numeric_punct'; // Aturan validasi jika username berubah
        }

        // Memvalidasi input
        if (!$this->validate([
            'username' => [
                'label' => 'Nama Pengguna', // Label untuk kesalahan validasi
                'rules' => $username, // Aturan yang diterapkan
                'errors' => [
                    'required' => '{field} wajib diisi!', // Pesan kesalahan jika tidak diisi
                    'is_unique' => '{field} harus berbeda dari pengguna lainnya!' // Pesan kesalahan jika tidak unik
                ]
            ]
        ])) {
            return redirect()->back()->withInput(); // Kembali jika validasi gagal
        }

        // Menyimpan perubahan username ke dalam model SettingsModel
        $this->SettingsModel->save([
            'id_user' => session()->get('id_user'), // Mengambil id_user dari session
            'username' => $this->request->getVar('username'), // Mengambil username baru dari input
        ]);

        // Memeriksa apakah username yang diinput sama dengan username yang ada di session
        if ($this->request->getVar('username') == session()->get('username')) {
            session()->setFlashdata('info', 'Tidak ada perubahan apa-apa dalam formulir ini!'); // Pesan jika tidak ada perubahan
        } else {
            session()->remove('username'); // Menghapus username lama dari session
            session()->set('fullname', $this->request->getVar('fullname')); // Memperbarui fullname di session
            session()->set('username', $this->request->getVar('username')); // Memperbarui username di session
            session()->setFlashdata('msg', 'Informasi Pengguna berhasil diubah!'); // Pesan sukses
        }

        return redirect()->back(); // Kembali ke halaman sebelumnya
    }

    public function pwdTransaksi()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menyiapkan data untuk tampilan halaman ubah kata sandi
            $data = [
                'title' => 'Ubah Kata Sandi Transaksi - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Ubah Kata Sandi Transaksi', // Judul header
                'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
            ];
            // Mengembalikan tampilan halaman ubah kata sandi dengan data yang telah disiapkan
            return view('dashboard/settings/pwdtransaksi', $data);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function updatePwdTransaksi()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Memvalidasi input dari form ubah kata sandi
            if (!$this->validate([
                'current_password' => [
                    'label' => 'Kata Sandi Lama', // Label untuk kata sandi lama
                    'rules' => 'required', // Kata sandi lama wajib diisi
                    'errors' => [
                        'required' => '{field} wajib diisi!' // Pesan error jika kata sandi lama tidak diisi
                    ]
                ],
                'new_password1' => [
                    'label' => 'Kata Sandi Baru', // Label untuk kata sandi baru
                    'rules' => 'required|min_length[5]|matches[new_password2]', // Validasi untuk kata sandi baru
                    'errors' => [
                        'required' => '{field} wajib diisi!', // Pesan error jika kata sandi baru tidak diisi
                        'min_length' => '{field} harus sekurang-kurangnya lima karakter', // Pesan error jika kata sandi baru kurang dari 5 karakter
                        'matches' => '{field} tidak cocok dengan Konfirmasi Kata Sandi!' // Pesan error jika kata sandi baru tidak cocok dengan konfirmasi
                    ]
                ],
                'new_password2' => [
                    'label' => 'Konfirmasi Kata Sandi', // Label untuk konfirmasi kata sandi baru
                    'rules' => 'required|min_length[5]|matches[new_password1]', // Validasi untuk konfirmasi kata sandi baru
                    'errors' => [
                        'required' => '{field} wajib diisi!', // Pesan error jika konfirmasi tidak diisi
                        'min_length' => '{field} harus sekurang-kurangnya lima karakter', // Pesan error jika konfirmasi kurang dari 5 karakter
                        'matches' => '{field} tidak cocok dengan Kata Sandi Baru!' // Pesan error jika konfirmasi tidak cocok dengan kata sandi baru
                    ]
                ]
            ])) {
                // Jika validasi gagal, mengalihkan kembali ke halaman ubah kata sandi dengan input yang ada
                return redirect()->back()->withInput();
            }

            $db = db_connect();
            // Ambil data kata sandi transaksi dengan ID 1
            $query_pwd_transaksi = $db->table('pwd_transaksi')->getWhere(['id' => 1]);

            $pwd_transaksi = $query_pwd_transaksi->getRowArray(); // Ambil satu baris data

            // Mengambil kata sandi lama dan baru dari input
            $current_password = $this->request->getVar('current_password');
            $new_password = $this->request->getVar('new_password1');

            // Memeriksa apakah kata sandi lama yang diinput benar
            if (!password_verify($current_password, $pwd_transaksi['pwd_transaksi'])) {
                // Jika salah, mengatur flashdata untuk menampilkan pesan error
                session()->setFlashdata('error', 'Kata sandi lama Anda salah!');
                return redirect()->back(); // Kembali ke halaman ubah kata sandi
            } else {
                // Memeriksa apakah kata sandi baru sama dengan kata sandi lama
                if ($current_password == $new_password) {
                    // Jika sama, mengatur flashdata untuk menampilkan pesan error
                    session()->setFlashdata('error', 'Kata sandi baru harus berbeda dengan kata sandi lama');
                    return redirect()->back(); // Kembali ke halaman ubah kata sandi
                } else {
                    // Menghash kata sandi baru
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    // Menyimpan kata sandi baru ke database pwd_transaksi
                    $data = [
                        'id' => 1,
                        'pwd_transaksi' => $password_hash
                    ];
                    $db->table('pwd_transaksi')->update($data, ['id' => 1]); // Update data berdasarkan id
                    // Mengatur flashdata untuk menampilkan pesan sukses
                    session()->setFlashdata('msg', 'Anda berhasil mengubah kata sandi transaksi!');
                    return redirect()->back(); // Kembali ke halaman sebelumnya
                }
            }
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function about()
    {
        // Menghubungkan ke database
        $db = db_connect();
        $php_extensions = get_loaded_extensions(); // Mengambil ekstensi PHP yang terpasang
        $query_version = $db->query('SELECT VERSION() as version'); // Mengambil versi database
        $query_comment = $db->query('SHOW VARIABLES LIKE "version_comment"'); // Mengambil komentar versi database
        $row_version = $query_version->getRow(); // Mendapatkan hasil query versi
        $row_comment = $query_comment->getRow(); // Mendapatkan hasil query komentar versi
        $agent = $this->request->getUserAgent(); // Mengambil informasi user agent

        // Menyiapkan data untuk tampilan halaman tentang
        $data = [
            'php_extensions' => implode(', ', $php_extensions), // Menggabungkan ekstensi PHP menjadi string
            'version' => $row_version->version, // Versi database
            'version_comment' => $row_comment->Value, // Komentar versi database
            'agent' => $agent, // Informasi user agent
            'title' => 'Tentang ' . $this->systemName, // Judul halaman
            'headertitle' => 'Tentang Sistem' // Judul header
        ];

        return view('dashboard/settings/about', $data); // Mengembalikan tampilan halaman tentang
    }
}
