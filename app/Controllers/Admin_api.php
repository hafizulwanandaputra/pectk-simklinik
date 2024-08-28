<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AuthModel;

class Admin_api extends ResourceController
{
    use ResponseTrait;
    // all users
    public function index()
    {
        $model = new AuthModel();
        $data['user'] = $model->select('fullname, username, role')->orderBy('id_user', 'DESC')->findAll();
        return $this->respond($data, 200);
    }
}
