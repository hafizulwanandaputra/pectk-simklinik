<?php

namespace App\Controllers;

use App\Models\AuthModel;

class Auth extends BaseController
{
    protected $AuthModel;
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        // Mengambil semua pengguna dari model AuthModel dan mengurutkannya berdasarkan username secara ascending
        $users = $this->AuthModel->orderBy('username', 'ASC')->findAll();
        // Menyiapkan data untuk ditampilkan pada halaman login
        $data = [
            'title' => $this->systemName, // Judul halaman
            'users' => $users, // Daftar pengguna
            'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
        ];
        // Mengembalikan tampilan halaman login dengan data yang telah disiapkan
        return view('auth/login', $data);
    }

    public function check_login()
    {
        // Memvalidasi input dari form login
        if (!$this->validate([
            'username' => [
                'label' => 'Username',
                'rules' => 'required', // Username wajib diisi
                'errors' => [
                    'required' => '{field} is required!' // Pesan error jika username tidak diisi
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required', // Password wajib diisi
                'errors' => [
                    'required' => '{field} is required!' // Pesan error jika password tidak diisi
                ]
            ]
        ])) {
            // Jika validasi gagal, mengalihkan kembali ke halaman sebelumnya dengan input yang ada
            return redirect()->back()->withInput();
        }

        // Mengambil username dan password dari input
        $username = $this->request->getPost('username');
        $password = $this->request->getVar('password');
        $url = $this->request->getVar('url'); // URL redirect setelah login

        // Memeriksa apakah pengguna terdaftar
        $check = $this->AuthModel->login($username);
        if ($check) {
            // Memeriksa kecocokan password
            if (password_verify($password, $check['password'])) {
                // Memeriksa status akun aktif
                if ($check['active'] == '1') {
                    $db = db_connect();
                    // Generate token sesi baru
                    $session_token = bin2hex(random_bytes(32));

                    // Ambil data perangkat pengguna
                    $user_agent = $this->request->getUserAgent()->getAgentString();
                    $ip_address = $this->request->getIPAddress();

                    // Tentukan waktu kadaluwarsa (misalnya, 12 jam dari sekarang)
                    $expires_at = date('Y-m-d H:i:s', strtotime('+12 hours'));

                    // Simpan token sesi ke tabel `user_sessions`
                    $db->table('user_sessions')->insert([
                        'id_user' => $check['id_user'],
                        'session_token' => $session_token,
                        'user_agent' => $user_agent,
                        'ip_address' => $ip_address,
                        'created_at' => date('Y-m-d H:i:s'),
                        'expires_at' => $expires_at
                    ]);
                    // Menyimpan data sesi setelah login berhasil
                    session()->set('log', true);
                    session()->set('id_user', $check['id_user']);
                    session()->set('fullname', $check['fullname']);
                    session()->set('username', $check['username']);
                    session()->set('password', $check['password']);
                    session()->set('profilephoto', $check['profilephoto']);
                    session()->set('role', $check['role']);
                    session()->set('session_token', $session_token);
                    session()->set('expires_at', $expires_at);
                    session()->set('url', $url);
                    // Mengalihkan pengguna ke URL yang telah ditentukan
                    return redirect()->to($url);
                } else {
                    // Jika akun tidak aktif, mengatur flashdata untuk menampilkan pesan error
                    session()->setFlashdata('error', 'Akun tidak aktif');
                    return redirect()->back(); // Kembali ke halaman sebelumnya
                }
            } else {
                // Jika password salah, mengatur flashdata untuk menampilkan pesan error
                session()->setFlashdata('error', 'Kata sandi salah');
                return redirect()->back(); // Kembali ke halaman sebelumnya
            }
        } else {
            // Jika akun tidak terdaftar, mengatur flashdata untuk menampilkan pesan error
            session()->setFlashdata('error', 'Akun tidak terdaftar');
            return redirect()->back(); // Kembali ke halaman sebelumnya
        }
    }

    public function logout()
    {
        $db = db_connect();

        // Hapus token sesi untuk perangkat saat ini
        $db->table('user_sessions')
            ->where('id_user', session()->get('id_user'))
            ->where('session_token', session()->get('session_token'))
            ->delete();
        $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
        // Menghapus semua data sesi
        session()->remove('log');
        session()->remove('id_user');
        session()->remove('fullname');
        session()->remove('username');
        session()->remove('password');
        session()->remove('profilephoto');
        session()->remove('role');
        session()->remove('session_token');
        session()->remove('expires_at');
        session()->remove('url');
        // Mengatur flashdata untuk menampilkan pesan sukses logout
        session()->setFlashdata('msg', 'Berhasil keluar');
        // Mengalihkan pengguna ke halaman utama
        return redirect()->to(base_url());
    }
}
