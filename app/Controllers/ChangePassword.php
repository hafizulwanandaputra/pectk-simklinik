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
        $data = [
            'title' => 'Ubah Kata Sandi - ' . $this->systemName,
            'headertitle' => 'Ubah Kata Sandi',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/changepassword/index', $data);
    }
    public function update()
    {
        if (!$this->validate([
            'current_password' => [
                'label' => 'Kata Sandi Lama',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'new_password1' => [
                'label' => 'Kata Sandi Baru',
                'rules' => 'required|min_length[3]|matches[new_password2]',
                'errors' => [
                    'required' => '{field} wajib diisi!',
                    'min_length' => '{field} harus sekurang-kurangnya tiga karakter',
                    'matches' => '{field} tidak cocok dengan Konfirmasi Kata Sandi!'
                ]
            ],
            'new_password2' => [
                'label' => 'Konfirmasi Kata Sandi',
                'rules' => 'required|min_length[3]|matches[new_password1]',
                'errors' => [
                    'required' => '{field} wajib diisi!',
                    'min_length' => '{field} harus sekurang-kurangnya tiga karakter',
                    'matches' => '{field} tidak cocok dengan Kata Sandi Baru!'
                ]
            ]
        ])) {
            return redirect()->to(base_url('/settings/changepassword'))->withInput();
        }
        $current_password = $this->request->getVar('current_password');
        $new_password = $this->request->getVar('new_password1');
        if (!password_verify($current_password, session()->get('password'))) {
            session()->setFlashdata('error', 'Kata sandi lama Anda salah!');
            return redirect()->to(base_url('/settings/changepassword'));
        } else {
            if ($current_password == $new_password) {
                session()->setFlashdata('error', 'Kata sandi baru harus berbeda dengan kata sandi lama');
                return redirect()->to(base_url('/settings/changepassword'));
            } else {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $this->ChangePasswordModel->save([
                    'id_user' => session()->get('id_user'),
                    'password' => $password_hash
                ]);
                session()->remove('password');
                session()->set('password', $password_hash);
                session()->setFlashdata('msg', 'Anda berhasil mengubah kata sandi baru Anda!');
                return redirect()->back();
            }
        }
    }
}
