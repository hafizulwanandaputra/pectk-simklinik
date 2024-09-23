<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Supplier extends BaseController
{
    protected $SupplierModel;
    public function __construct()
    {
        $this->SupplierModel = new SupplierModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = [
                'title' => 'Supplier - ' . $this->systemName,
                'headertitle' => 'Supplier',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/supplier/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function supplierlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
                5 => 'jumlah_obat',
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
                ->select('supplier.*, (SELECT COUNT(*) FROM obat WHERE obat.id_supplier = supplier.id_supplier) as jumlah_obat')
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function supplier($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->SupplierModel->find($id);
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            try {
                $this->SupplierModel->delete($id);
                $db->query('ALTER TABLE `supplier` auto_increment = 1');
                return $this->response->setJSON(['message' => 'Supplier berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Log the error message
                log_message('error', $e->getMessage());

                // Return a generic error message
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
