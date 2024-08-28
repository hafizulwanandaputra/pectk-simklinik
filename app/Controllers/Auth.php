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
        $users = $this->AuthModel->orderBy('username', 'ASC')->findAll();
        $data = [
            'title' => $this->systemName,
            'users' => $users,
            'agent' => $this->request->getUserAgent()
        ];
        return view('auth/login', $data);
    }

    public function check_login()
    {
        if (!$this->validate([
            'username' => [
                'label' => 'Username',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} is required!'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} is required!'
                ]
            ]
        ])) {
            return redirect()->back()->withInput();
        }
        $username = $this->request->getPost('username');
        $password = $this->request->getVar('password');
        $url = $this->request->getVar('url');
        $check = $this->AuthModel->login($username);
        if ($check) {
            if (password_verify($password, $check['password'])) {
                session()->set('log', true);
                session()->set('id_user', $check['id_user']);
                session()->set('fullname', $check['fullname']);
                session()->set('username', $check['username']);
                session()->set('password', $check['password']);
                session()->set('profilephoto', $check['profilephoto']);
                session()->set('role', $check['role']);
                session()->set('url', $url);
                return redirect()->to($url);
            } else {
                session()->setFlashdata('error', 'Kata sandi salah');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error', 'Akun tidak terdaftar');
            return redirect()->back();
        }
    }

    public function logout()
    {
        $db = db_connect();
        session()->remove('log');
        session()->remove('id_user');
        session()->remove('fullname');
        session()->remove('username');
        session()->remove('password');
        session()->remove('profilephoto');
        session()->remove('role');
        session()->remove('url');
        session()->setFlashdata('msg', 'Berhasil keluar');
        return redirect()->to(base_url());
    }
}
