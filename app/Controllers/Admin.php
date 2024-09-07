<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\AuthModelEdit;
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
        $data = [
            'title' => 'Pengguna - ' . $this->systemName,
            'headertitle' => 'Pengguna',
            'agent' => $this->request->getUserAgent()
        ];
        if (session()->get('role') == "Admin") {
            return view('dashboard/admin/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function adminlist()
    {
        if (session()->get('role') == 'Admin') {
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Search value
            $start = $request['start']; // Start index for pagination
            $length = $request['length']; // Length of the page
            $draw = $request['draw']; // Draw counter for DataTables

            // Get sorting parameters
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Column index
            $sortDirection = $order[0]['dir']; // asc or desc

            // Map column index to the database column name
            $columnMapping = [
                0 => 'id_user',
                1 => 'id_user',
                2 => 'fullname',
                3 => 'username',
                4 => 'role',
            ];

            // Get the column to sort by
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_user';

            // Get total records count
            $totalRecords = $this->AuthModel->where('id_user !=', session()->get('id_user'))->countAllResults(true);

            // Apply search query
            if ($search) {
                $this->AuthModel
                    ->groupStart()
                    ->like('fullname', $search)
                    ->orLike('username', $search)
                    ->groupEnd()
                    ->where('id_user !=', session()->get('id_user'))
                    ->orderBy($sortColumn, $sortDirection);
            }

            // Get filtered records count
            $filteredRecords = $this->AuthModel->where('id_user !=', session()->get('id_user'))->countAllResults(false);

            // Fetch the data
            $users = $this->AuthModel->where('id_user !=', session()->get('id_user'))
                ->orderBy($sortColumn, $sortDirection)
                ->findAll($length, $start);

            // Format the data
            $data = [];
            foreach ($users as $user) {
                $data[] = $user;
            }

            // Return the JSON response
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function admin($id)
    {
        if (session()->get('role') == 'Admin') {
            $data = $this->AuthModel->where('id_user !=', session()->get('id_user'))->find($id);
            return $this->response->setJSON($data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'fullname' => 'required|min_length[3]',
                'username' => 'required|is_unique[user.username]|min_length[3]|alpha_dash',
                'role' => 'required'
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'fullname' => $this->request->getPost('fullname'),
                'username' => $this->request->getPost('username'),
                'password' => password_hash($this->request->getPost('username'), PASSWORD_DEFAULT),
                'role' => $this->request->getPost('role'),
                'active' => 0,
                'registered' => date('Y-m-d H:i:s')
            ];
            $this->AuthModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil ditambahkan']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function update()
    {
        if (session()->get('role') == 'Admin') {
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'fullname' => 'required|min_length[3]',
                'username' => 'required|min_length[3]|alpha_dash',
                'role' => 'required'
            ]);
            // Validate only if username has changed
            if ($this->request->getPost('username') != $this->request->getPost('original_username')) {
                $validation->setRule('username', 'username', 'required|is_unique[user.username]|min_length[3]|alpha_dash');
            }
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'id_user' => $this->request->getPost('id_user'),
                'fullname' => $this->request->getPost('fullname'),
                'username' => $this->request->getPost('username'),
                'role' => $this->request->getPost('role'),
            ];
            $AuthModelEdit = new AuthModelEdit();
            $AuthModelEdit->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diedit']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function resetpassword($id)
    {
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            $user = $this->AuthModel->find($id);
            $db->table('user')->set('password', password_hash($user['username'], PASSWORD_DEFAULT))->where('id_user', $id)->update();
            return $this->response->setJSON(['success' => true, 'message' => 'Kata sandi pengguna berhasil diatur ulang']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function activate($id)
    {
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            $db->table('user')->set('active', 1)->where('id_user', $id)->update();
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diaktifkan']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function deactivate($id)
    {
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            $db->table('user')->set('active', 0)->where('id_user', $id)->update();
            return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil dinonaktifkan']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin') {
            $this->AuthModel->delete($id);
            $db = db_connect();
            // Reset Auto Increment Value
            $db->query('ALTER TABLE `user` auto_increment = 1');
            return $this->response->setJSON(['message' => 'Pengguna berhasil dihapus']);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
