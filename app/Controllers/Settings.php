<?php

namespace App\Controllers;

use App\Models\SettingsModel;

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
            session()->set('fullname', $this->request->getVar('fullname')); // Memperbarui fullname di session
            session()->set('username', $this->request->getVar('username')); // Memperbarui username di session
            session()->setFlashdata('msg', 'Informasi Pengguna berhasil diubah!'); // Pesan sukses
        }

        return redirect()->back(); // Kembali ke halaman sebelumnya
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
