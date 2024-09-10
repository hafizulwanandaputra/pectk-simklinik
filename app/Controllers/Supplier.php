<?php

namespace App\Controllers;

use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected $SupplierModel;
    public function __construct()
    {
        $this->SupplierModel = new SupplierModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Supplier - ' . $this->systemName,
            'headertitle' => 'Supplier',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/supplier/index', $data);
    }

    public function supplierlist()
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
            0 => 'id_supplier',
            1 => 'id_supplier',
            2 => 'nama_supplier',
            3 => 'alamat_supplier',
            4 => 'kontak_supplier',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_supplier';

        // Get total records count
        $totalRecords = $this->SupplierModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->SupplierModel
                ->like('nama_supplier', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->SupplierModel->countAllResults(false);

        // Fetch the data
        $supplier = $this->SupplierModel
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($supplier as $item) {
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

    public function supplier($id)
    {
        $data = $this->SupplierModel->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_supplier' => 'required',
            'alamat_supplier' => 'required',
            'kontak_supplier' => 'required|numeric',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'nama_supplier' => $this->request->getPost('nama_supplier'),
            'alamat_supplier' => $this->request->getPost('alamat_supplier'),
            'kontak_supplier' => $this->request->getPost('kontak_supplier')
        ];
        $this->SupplierModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Supplier berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_supplier' => 'required',
            'alamat_supplier' => 'required',
            'kontak_supplier' => 'required|numeric',
        ]);
        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_supplier' => $this->request->getPost('id_supplier'),
            'nama_supplier' => $this->request->getPost('nama_supplier'),
            'alamat_supplier' => $this->request->getPost('alamat_supplier'),
            'kontak_supplier' => $this->request->getPost('kontak_supplier')
        ];
        $this->SupplierModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Supplier berhasil diedit']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->SupplierModel->delete($id);
        $db->query('ALTER TABLE `supplier` auto_increment = 1');
        $db->query('ALTER TABLE `obat` auto_increment = 1');
        $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Supplier berhasil dihapus']);
    }
}
