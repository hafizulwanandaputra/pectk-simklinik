<?php

namespace App\Controllers;

use App\Models\LayananModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Layanan extends BaseController
{
    protected $LayananModel;
    public function __construct()
    {
        $this->LayananModel = new LayananModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = [
                'title' => 'Tindakan - ' . $this->systemName,
                'headertitle' => 'Tindakan',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/layanan/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function layananlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
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
                0 => 'id_layanan',
                1 => 'id_layanan',
                2 => 'nama_layanan',
                3 => 'jenis_layanan',
                4 => 'tarif',
                5 => 'keterangan',
            ];

            // Get the column to sort by
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_layanan';

            // Get total records count
            $totalRecords = $this->LayananModel->countAllResults(true);

            // Modify sorting logic to handle jenis_tindakan
            if ($sortColumn === 'jenis_layanan') {
                // Sort by jenis_layanan, then by nama_layanan
                $this->LayananModel
                    ->orderBy('jenis_layanan', $sortDirection)
                    ->orderBy('id_layanan', 'ASC');
            } else {
                // Default sorting behavior
                $this->LayananModel->orderBy($sortColumn, $sortDirection);
            }

            // Apply search query
            if ($search) {
                $this->LayananModel
                    ->like('nama_layanan', $search);
            }

            // Get filtered records count
            $filteredRecords = $this->LayananModel->countAllResults(false);

            // Fetch the data
            $layanan = $this->LayananModel
                ->findAll($length, $start);

            // Tambahkan penomoran langsung ke $layanan
            foreach ($layanan as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Return the JSON response
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $layanan
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function layanan($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = $this->LayananModel->find($id);
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'nama_layanan' => 'required',
                'jenis_layanan' => 'required',
                'tarif' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'jenis_layanan' => $this->request->getPost('jenis_layanan'),
                'tarif' => $this->request->getPost('tarif'),
                'keterangan' => $this->request->getPost('keterangan')
            ];
            $this->LayananModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'nama_layanan' => 'required',
                'jenis_layanan' => 'required',
                'tarif' => 'required|numeric|greater_than[0]',
            ]);
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'id_layanan' => $this->request->getPost('id_layanan'),
                'nama_layanan' => $this->request->getPost('nama_layanan'),
                'jenis_layanan' => $this->request->getPost('jenis_layanan'),
                'tarif' => $this->request->getPost('tarif'),
                'keterangan' => $this->request->getPost('keterangan')
            ];
            $this->LayananModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Layanan berhasil diedit']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $db = db_connect();

            try {
                $this->LayananModel->delete($id);
                $db->query('ALTER TABLE `layanan` auto_increment = 1');
                return $this->response->setJSON(['message' => 'Layanan berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Log the error message
                log_message('error', $e->getMessage());

                // Return a generic error message
                return $this->response->setStatusCode(422)->setJSON([
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
