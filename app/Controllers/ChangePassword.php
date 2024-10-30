<?php

namespace App\Controllers;

use App\Models\ChangePasswordModel;

class ChangePassword extends BaseController
{
    protected $ChangePasswordModel;
    public function __construct()
    {
        $this->ChangePasswordModel = new ChangePasswordModel();
    }

    public function index()
    {
        // Menyiapkan data untuk tampilan halaman ubah kata sandi
        $data = [
            'title' => 'Ubah Kata Sandi Pengguna - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Ubah Kata Sandi Pengguna', // Judul header
            'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
        ];
        // Mengembalikan tampilan halaman ubah kata sandi dengan data yang telah disiapkan
        return view('dashboard/changepassword/index', $data);
    }

    public function update()
    {
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
                'rules' => 'required|min_length[3]|matches[new_password2]', // Validasi untuk kata sandi baru
                'errors' => [
                    'required' => '{field} wajib diisi!', // Pesan error jika kata sandi baru tidak diisi
                    'min_length' => '{field} harus sekurang-kurangnya tiga karakter', // Pesan error jika kata sandi baru kurang dari 3 karakter
                    'matches' => '{field} tidak cocok dengan Konfirmasi Kata Sandi!' // Pesan error jika kata sandi baru tidak cocok dengan konfirmasi
                ]
            ],
            'new_password2' => [
                'label' => 'Konfirmasi Kata Sandi', // Label untuk konfirmasi kata sandi baru
                'rules' => 'required|min_length[3]|matches[new_password1]', // Validasi untuk konfirmasi kata sandi baru
                'errors' => [
                    'required' => '{field} wajib diisi!', // Pesan error jika konfirmasi tidak diisi
                    'min_length' => '{field} harus sekurang-kurangnya tiga karakter', // Pesan error jika konfirmasi kurang dari 3 karakter
                    'matches' => '{field} tidak cocok dengan Kata Sandi Baru!' // Pesan error jika konfirmasi tidak cocok dengan kata sandi baru
                ]
            ]
        ])) {
            // Jika validasi gagal, mengalihkan kembali ke halaman ubah kata sandi dengan input yang ada
            return redirect()->back()->withInput();
        }

        // Mengambil kata sandi lama dan baru dari input
        $current_password = $this->request->getVar('current_password');
        $new_password = $this->request->getVar('new_password1');

        // Memeriksa apakah kata sandi lama yang diinput benar
        if (!password_verify($current_password, session()->get('password'))) {
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
                $db = db_connect();
                // Menghash kata sandi baru
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                // Ambil token sesi perangkat aktif dari sesi saat ini
                $current_token = session()->get('session_token');
                // Hapus semua sesi kecuali perangkat aktif
                $db->table('user_sessions')
                    ->where('id_user', session()->get('id_user'))
                    ->where('session_token !=', $current_token)
                    ->delete();
                $db->query('ALTER TABLE `user_sessions` auto_increment = 1');
                // Perbarui token perangkat aktif (opsional, untuk keamanan tambahan)
                $new_token = bin2hex(random_bytes(32));
                $db->table('user_sessions')
                    ->where('session_token', $current_token)
                    ->update(['session_token' => $new_token]);
                // Menyimpan kata sandi baru ke model
                $this->ChangePasswordModel->save([
                    'id_user' => session()->get('id_user'),
                    'password' => $password_hash // Menyimpan hash kata sandi baru
                ]);
                // Menghapus kata sandi lama dari sesi dan menyimpan kata sandi baru
                session()->remove('password');
                // Perbarui token sesi di sesi saat ini
                session()->set('session_token', $new_token);
                session()->set('password', $password_hash);
                // Mengatur flashdata untuk menampilkan pesan sukses
                session()->setFlashdata('msg', 'Anda berhasil mengubah kata sandi baru Anda!');
                return redirect()->back(); // Kembali ke halaman sebelumnya
            }
        }
    }
}
