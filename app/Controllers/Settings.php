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
            $new_password = $this->request->getVar('new_password1');

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
        $query_compile_os = $db->query('SHOW VARIABLES LIKE "version_compile_os"'); // Mengambil sistem operasi
        $query_compile_machine = $db->query('SHOW VARIABLES LIKE "version_compile_machine"'); // Mengambil jenis mesin
        $row_version = $query_version->getRow(); // Mendapatkan hasil query versi
        $row_comment = $query_comment->getRow(); // Mendapatkan hasil query komentar versi
        $row_compile_os = $query_compile_os->getRow(); // Mendapatkan hasil sistem operasi
        $row_compile_machine = $query_compile_machine->getRow(); // Mendapatkan hasil jenis mesin
        $agent = $this->request->getUserAgent(); // Mengambil informasi user agent

        // Menyiapkan data untuk tampilan halaman tentang
        $data = [
            'php_extensions' => implode(', ', $php_extensions), // Menggabungkan ekstensi PHP menjadi string
            'version' => $row_version->version, // Versi database
            'version_comment' => $row_comment->Value, // Komentar versi database
            'version_compile_os' => $row_compile_os->Value, // Sistem operasi database
            'version_compile_machine' => $row_compile_machine->Value, // Jenis mesin database
            'systemName' => $this->systemName,
            'systemSubtitleName' => $this->systemSubtitleName,
            'companyName' => $this->companyName,
            'agent' => $agent, // Informasi user agent
            'title' => 'Tentang ' . $this->systemName, // Judul halaman
            'headertitle' => 'Tentang Sistem' // Judul header
        ];

        return view('dashboard/settings/about', $data); // Mengembalikan tampilan halaman tentang
    }
}
