<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PermintaanModel;

class Permintaan_api extends ResourceController
{
    use ResponseTrait;
    // all users
    public function index()
    {
        $model = new PermintaanModel();
        $data['permintaan'] = $model->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')->orderBy('id', 'DESC')->findAll();
        return $this->respond($data, 200);
    }

    // single user
    public function show($id = null)
    {
        $model = new PermintaanModel();
        $data = $model->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')->where('id', $id)->first();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Tidak ada permintaan pasien dengan ID ' . $id);
        }
    }

    // create
    public function create()
    {
        $model = new PermintaanModel();
        $input = $this->request->getRawInput();
        $data = [
            'id_menu' => $input['id_menu'],
            'nama_pasien' => $input['nama_pasien'],
            'tanggal_lahir' => $input['tanggal_lahir'],
            'jenis_kelamin' => $input['jenis_kelamin'],
            'kamar' => $input['kamar'],
            'jenis_tindakan' => $input['jenis_tindakan'],
            'diet' => $input['diet'],
            'keterangan' => $input['keterangan'],
        ];
        $model->insert($data);
        $db = db_connect();
        $permintaan = $db->table('permintaan')->where('id_menu', $this->request->getVar('id_menu'));
        $totalpermintaan = $permintaan->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $this->request->getVar('id_menu'));
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Permintaan pasien "' . $this->request->getVar('nama_pasien') . '" berhasil ditambahkan.'
            ]
        ];
        return $this->respondCreated($response, 201);
    }

    // update
    public function update($id = null)
    {
        $model = new PermintaanModel();
        $db = db_connect();
        $permintaan = $model->where('id', $id)->first();
        $input = $this->request->getRawInput();
        $data = [
            'id_menu' => $input['id_menu'],
            'nama_pasien' => $input['nama_pasien'],
            'tanggal_lahir' => $input['tanggal_lahir'],
            'jenis_kelamin' => $input['jenis_kelamin'],
            'kamar' => $input['kamar'],
            'jenis_tindakan' => $input['jenis_tindakan'],
            'diet' => $input['diet'],
            'keterangan' => $input['keterangan'],
        ];
        $model->where(['id' => $id])->update(null, $data);
        if ($permintaan['id_menu'] != $input['id_menu']) {
            $menu1 = $db->table('permintaan')->where('id_menu', $permintaan['id_menu']);
            $menu2 = $db->table('permintaan')->where('id_menu', $input['id_menu']);
            $totalpermintaan1 = $menu1->countAllResults();
            $totalpermintaan2 = $menu2->countAllResults();
            $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan1 . ' WHERE id_menu = ' . $permintaan['id_menu']);
            $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan2 . ' WHERE id_menu = ' . $input['id_menu']);
        }
        if ($permintaan['nama_pasien'] == $input['nama_pasien']) {
            $success = 'Permintaan pasien "' . $permintaan['nama_pasien'] . '" berhasil diubah';
        } else {
            $success = 'Permintaan pasien "' . $permintaan['nama_pasien'] . '" berhasil diubah menjadi "' . $input['nama_pasien'] . '"';
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
        $model = new PermintaanModel();
        $data = $model->find($id);
        if ($data) {
            $model->delete($id);
            $db = db_connect();
            $permintaan1 = $db->table('permintaan')->where('id_menu', $data['id_menu']);
            $totalpermintaan = $permintaan1->countAllResults();
            $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $data['id_menu']);
            $db->query('ALTER TABLE `permintaan` auto_increment = 1');
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Permintaan pasien "' . $data['nama_pasien'] . '" berhasil dihapus'
                ]
            ];

            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('Tidak ada menu makanan dengan ID ' . $id);
        }
    }
}
