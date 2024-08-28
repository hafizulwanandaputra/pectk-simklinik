<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MenuModel;

class Menu_api extends ResourceController
{
    use ResponseTrait;
    // all users
    public function index()
    {
        $model = new MenuModel();
        $data['menu'] = $model->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->orderBy('id_menu', 'DESC')->findAll();
        return $this->respond($data, 200);
    }

    // single user
    public function show($id = null)
    {
        $model = new MenuModel();
        $data = $model->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->where('id_menu', $id)->first();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Tidak ada menu makanan dengan ID ' . $id);
        }
    }

    // create
    public function create()
    {
        $model = new MenuModel();
        $input = $this->request->getRawInput();
        $data = [
            'id_petugas' => $input['id_petugas'],
            'tanggal' => $input['tanggal'],
            'nama_menu' => $input['nama_menu'],
            'jadwal_makan' => $input['jadwal_makan'],
            'protein_hewani' => $input['protein_hewani'],
            'protein_nabati' => $input['protein_nabati'],
            'sayur' => $input['sayur'],
            'buah' => $input['buah'],
            'jumlah' => 0,
        ];
        $model->insert($data);
        $db = db_connect();
        $petugas = $db->table('menu')->where('id_petugas', $input['id_petugas']);
        $totalpetugas = $petugas->countAllResults();
        $db->query('UPDATE petugas SET jumlah_menu = ' . $totalpetugas . ' WHERE id_petugas = ' . $input['id_petugas']);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Menu makanan "' . $this->request->getVar('nama_menu') . '" berhasil ditambahkan.'
            ]
        ];
        return $this->respondCreated($response, 201);
    }

    // update
    public function update($id = null)
    {
        $model = new MenuModel();
        $db = db_connect();
        $permintaan = $db->table('permintaan')->where('id_menu', $id);
        $totalpermintaan = $permintaan->countAllResults();
        $menu = $model->where('id_menu', $id)->first();
        $input = $this->request->getRawInput();
        $data = [
            'id_petugas' => $input['id_petugas'],
            'tanggal' => $input['tanggal'],
            'nama_menu' => $input['nama_menu'],
            'jadwal_makan' => $input['jadwal_makan'],
            'protein_hewani' => $input['protein_hewani'],
            'protein_nabati' => $input['protein_nabati'],
            'sayur' => $input['sayur'],
            'buah' => $input['buah'],
            'jumlah' => $totalpermintaan,
        ];
        $model->where(['id_menu' => $id])->update(null, $data);
        if ($menu['id_petugas'] != $input['id_petugas']) {
            $petugas1 = $db->table('menu')->where('id_petugas', $menu['id_petugas'])->countAllResults();
            $petugas2 = $db->table('menu')->where('id_petugas', $input['id_petugas'])->countAllResults();
            $db->query('UPDATE petugas SET jumlah_menu = ' . $petugas1 . ' WHERE id_petugas = ' . $menu['id_petugas']);
            $db->query('UPDATE petugas SET jumlah_menu = ' . $petugas2 . ' WHERE id_petugas = ' . $input['id_petugas']);
        }
        if ($menu['nama_menu'] == $input['nama_menu']) {
            $success = 'Menu makanan "' . $menu['nama_menu'] . '" berhasil diubah';
        } else {
            $success = 'Menu makanan "' . $menu['nama_menu'] . '" berhasil diubah menjadi "' . $input['nama_menu'] . '"';
        }
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => $success
            ]
        ];
        return $this->respond($response);
    }

    // delete product
    public function delete($id = null)
    {
        $model = new MenuModel();
        $data = $model->find($id);
        if ($data) {
            $model->delete($id);
            $db = db_connect();
            $petugas = $db->table('menu')->where('id_petugas', $data['id_petugas']);
            $totalpetugas = $petugas->countAllResults();
            $db->query('UPDATE petugas SET jumlah_menu = ' . $totalpetugas . ' WHERE id_petugas = ' . $data['id_petugas']);
            $db->query('DELETE FROM `permintaan` WHERE id_menu = ' . $id);
            $db->query('ALTER TABLE `menu` auto_increment = 1');
            $db->query('ALTER TABLE `permintaan` auto_increment = 1');
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Menu makanan "' . $data['nama_menu'] . '" berhasil dihapus'
                ]
            ];

            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('Tidak ada menu makanan dengan ID ' . $id);
        }
    }
}
