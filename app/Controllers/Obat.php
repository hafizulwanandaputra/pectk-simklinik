<?php

namespace App\Controllers;

use App\Models\ObatModel;
use App\Models\SupplierModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Obat extends BaseController
{
    protected $ObatModel;
    public function __construct()
    {
        $this->ObatModel = new ObatModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = [
                'title' => 'Obat - ' . $this->systemName,
                'headertitle' => 'Obat',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/obat/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function obatlist()
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
                0 => 'id_obat',
                1 => 'id_obat',
                2 => 'nama_supplier',
                3 => 'nama_obat',
                4 => 'kategori_obat',
                5 => 'bentuk_obat',
                6 => 'harga_obat',
                7 => 'ppn',
                8 => 'harga_jual',
                9 => 'jumlah_masuk',
                10 => 'jumlah_keluar',
                12 => 'sisa_stok',
                11 => 'updated_at',
            ];

            // Get the column to sort by
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_obat';

            // Get total records count
            $totalRecords = $this->ObatModel->countAllResults(true);

            // Modify sorting logic to handle jenis_tindakan
            if ($sortColumn === 'nama_supplier') {
                // Sort by jenis_layanan, then by nama_layanan
                $this->ObatModel
                    ->orderBy('nama_supplier', $sortDirection)
                    ->orderBy('nama_obat', 'ASC');
            } else {
                // Default sorting behavior
                $this->ObatModel->orderBy($sortColumn, $sortDirection);
            }

            // Apply search query
            if ($search) {
                $this->ObatModel
                    ->like('nama_obat', $search);
            }

            // Get filtered records count
            $filteredRecords = $this->ObatModel->countAllResults(false);

            // Fetch the data
            $obat = $this->ObatModel
                ->select('obat.*, supplier.*, (obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) as harga_jual, (obat.jumlah_masuk - obat.jumlah_keluar) as sisa_stok')
                ->join('supplier', 'supplier.id_supplier = obat.id_supplier', 'inner')
                ->findAll($length, $start);

            foreach ($obat as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Return the JSON response
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $obat
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function supplierlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $SupplierModel = new SupplierModel();

            $results = $SupplierModel->orderBy('nama_supplier', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $options[] = [
                    'value' => $row['id_supplier'],
                    'text' => $row['nama_supplier']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->ObatModel->find($id);
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
                'id_supplier' => 'required',
                'nama_obat' => 'required',
                'kategori_obat' => 'required',
                'bentuk_obat' => 'required',
                'harga_obat' => 'required|numeric|greater_than[0]',
                'ppn' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'nama_obat' => $this->request->getPost('nama_obat'),
                'kategori_obat' => $this->request->getPost('kategori_obat'),
                'bentuk_obat' => $this->request->getPost('bentuk_obat'),
                'harga_obat' => $this->request->getPost('harga_obat'),
                'ppn' => $this->request->getPost('ppn'),
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->ObatModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil ditambahkan']);
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
                'id_supplier' => 'required',
                'nama_obat' => 'required',
                'kategori_obat' => 'required',
                'bentuk_obat' => 'required',
                'harga_obat' => 'required|numeric|greater_than[0]',
                'ppn' => 'required|numeric|greater_than[0]',
            ]);
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $obat = $this->ObatModel->find($this->request->getPost('id_obat'));

            // Save Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_obat' => $this->request->getPost('id_obat'),
                'nama_obat' => $this->request->getPost('nama_obat'),
                'kategori_obat' => $this->request->getPost('kategori_obat'),
                'bentuk_obat' => $this->request->getPost('bentuk_obat'),
                'harga_obat' => $this->request->getPost('harga_obat'),
                'harga_jual' => $this->request->getPost('harga_jual'),
                'jumlah_masuk' => $obat['jumlah_masuk'],
                'ppn' => $obat['ppn'],
                'updated_at' => $obat['updated_at'],
            ];
            $this->ObatModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil diedit']);
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
                $this->ObatModel->delete($id);
                $db->query('ALTER TABLE `obat` auto_increment = 1');
                return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
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
