<?php

namespace App\Controllers;

use App\Models\PetugasModel;
use App\Models\DataTables;

class Petugas extends BaseController
{
    protected $PetugasModel;
    protected $DataTables;
    public function __construct()
    {
        $this->PetugasModel = new PetugasModel();
        $this->DataTables = new DataTables();
    }

    public function index()
    {
        $data = [
            'title' => 'Petugas Gizi - ' . $this->systemName,
            'headertitle' => 'Petugas Gizi',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/petugas/index', $data);
    }

    public function petugaslist()
    {
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
            0 => 'id_petugas',
            1 => 'id_petugas',
            2 => 'nama_petugas',
            3 => 'jumlah_menu',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_petugas';

        // Get total records count
        $totalRecords = $this->PetugasModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->PetugasModel
                ->like('nama_petugas', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->PetugasModel->countAllResults(false);

        // Fetch the data
        $officers = $this->PetugasModel
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($officers as $officer) {
            $data[] = $officer;
        }

        // Return the JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function petugas($id)
    {
        $data = $this->PetugasModel->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_petugas' => 'required|is_unique[petugas.nama_petugas]|min_length[3]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'nama_petugas' => $this->request->getPost('nama_petugas'),
            'jumlah_menu' => 0
        ];
        $this->PetugasModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Petugas berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_petugas' => 'required|min_length[3]',
        ]);
        // Validate only if username has changed
        if ($this->request->getPost('nama_petugas') != $this->request->getPost('nama_petugas_lama')) {
            $validation->setRule('nama_petugas', 'nama_petugas', 'required|is_unique[petugas.nama_petugas]|min_length[3]');
        }
        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_petugas' => $this->request->getPost('id_petugas'),
            'nama_petugas' => $this->request->getPost('nama_petugas'),
            'jumlah_menu' => $this->request->getPost('jumlah_menu')
        ];
        $this->PetugasModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Petugas berhasil diedit']);
    }

    public function delete($id)
    {
        $petugas = $this->PetugasModel->find($id);
        $db = db_connect();
        $this->PetugasModel->delete($id);
        $db->query('ALTER TABLE `menu` auto_increment = 1');
        $db->query('ALTER TABLE `permintaan` auto_increment = 1');
        $db->query('ALTER TABLE `petugas` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Petugas berhasil dihapus']);
    }
}
