<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PetugasModel;

class Petugas_api extends ResourceController
{
    use ResponseTrait;
    // all users
    public function index()
    {
        $model = new PetugasModel();
        $data['petugas'] = $model->orderBy('id_petugas', 'DESC')->findAll();
        return $this->respond($data, 200);
    }

    // single user
    public function show($id = null)
    {
        $model = new PetugasModel();
        $data = $model->where('id_petugas', $id)->first();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Tidak ada petugas gizi dengan ID ' . $id);
        }
    }

    // create
    public function create()
    {
        $model = new PetugasModel();
        $input = $this->request->getRawInput();
        $data = [
            'nama_petugas' => $input['nama_petugas'],
            'jumlah_menu' => 0
        ];
        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Petugas gizi "' . $input['nama_petugas'] . '" berhasil ditambahkan.'
            ]
        ];
        return $this->respondCreated($response, 201);
    }

    // update
    public function update($id = null)
    {
        $model = new PetugasModel();
        $petugas = $model->where('id_petugas', $id)->first();
        $db = db_connect();
        $menu = $db->table('menu')->where('id_petugas', $id);
        $totalmenu = $menu->countAllResults();
        $input = $this->request->getRawInput();
        $data = [
            'nama_petugas' => $input['nama_petugas'],
            'jumlah_menu' => $totalmenu
        ];
        $model->where(['id_petugas' => $id])->update(null, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Petugas gizi "' . $petugas['nama_petugas'] . '"  berhasil diubah menjadi "' . $input['nama_petugas'] . '"'
            ]
        ];
        return $this->respond($response);
    }

    // delete product
    public function delete($id = null)
    {
        $model = new PetugasModel();
        $data = $model->find($id);
        if ($data) {
            $db = db_connect();
            $menu = $db->table('menu')->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')->where('petugas.id_petugas', $id)->get()->getRowArray();
            if ($data['jumlah_menu'] != 0) {
                $db->query('DELETE FROM `permintaan` WHERE id_menu = ' . $menu['id_menu']);
            }
            $db->query('DELETE FROM `menu` WHERE id_petugas = ' . $id);
            $model->delete($id);
            $db->query('ALTER TABLE `menu` auto_increment = 1');
            $db->query('ALTER TABLE `permintaan` auto_increment = 1');
            $db->query('ALTER TABLE `petugas` auto_increment = 1');
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Petugas gizi "' . $data['nama_petugas'] . '" berhasil dihapus'
                ]
            ];

            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('Tidak ada petugas gizi dengan ID ' . $id);
        }
    }
}
