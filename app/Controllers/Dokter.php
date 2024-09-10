<?php

namespace App\Controllers;

use App\Models\DokterModel;

class Dokter extends BaseController
{
    protected $DokterModel;
    public function __construct()
    {
        $this->DokterModel = new DokterModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dokter - ' . $this->systemName,
            'headertitle' => 'Dokter',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/dokter/index', $data);
    }

    public function dokterlist()
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
            0 => 'id_dokter',
            1 => 'id_dokter',
            2 => 'nama_dokter',
            3 => 'alamat_dokter',
            4 => 'kontak_dokter',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_dokter';

        // Get total records count
        $totalRecords = $this->DokterModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->DokterModel
                ->like('nama_dokter', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->DokterModel->countAllResults(false);

        // Fetch the data
        $dokter = $this->DokterModel
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($dokter as $item) {
            $data[] = $item;
        }

        // Return the JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function dokter($id)
    {
        $data = $this->DokterModel->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_dokter' => 'required',
            'alamat_dokter' => 'required',
            'kontak_dokter' => 'required|numeric',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'nama_dokter' => $this->request->getPost('nama_dokter'),
            'alamat_dokter' => $this->request->getPost('alamat_dokter'),
            'kontak_dokter' => $this->request->getPost('kontak_dokter')
        ];
        $this->DokterModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Dokter berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_dokter' => 'required',
            'alamat_dokter' => 'required',
            'kontak_dokter' => 'required|numeric',
        ]);
        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_dokter' => $this->request->getPost('id_dokter'),
            'nama_dokter' => $this->request->getPost('nama_dokter'),
            'alamat_dokter' => $this->request->getPost('alamat_dokter'),
            'kontak_dokter' => $this->request->getPost('kontak_dokter')
        ];
        $this->DokterModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Dokter berhasil diedit']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->DokterModel->delete($id);
        $db->query('ALTER TABLE `dokter` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Dokter berhasil dihapus']);
    }
}
