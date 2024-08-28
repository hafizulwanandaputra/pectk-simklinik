<?php

namespace App\Controllers;

use App\Models\PetugasModel;
use App\Models\PermintaanModel;
use App\Models\MenuModel;
use App\Models\DataTablesMenu;
use App\Models\DataTablesPermintaan;
use CodeIgniter\Exceptions\PageNotFoundException;

class Menu extends BaseController
{
    protected $PetugasModel;
    protected $PermintaanModel;
    protected $MenuModel;
    protected $DataTablesMenu;
    protected $DataTablesPermintaan;
    public function __construct()
    {
        $this->PetugasModel = new PetugasModel();
        $this->PermintaanModel = new PermintaanModel();
        $this->MenuModel = new MenuModel();
        $this->DataTablesMenu = new DataTablesMenu();
        $this->DataTablesPermintaan = new DataTablesPermintaan();
    }

    public function index()
    {
        $data = [
            'title' => 'Menu Makanan - ' . $this->systemName,
            'headertitle' => 'Menu Makanan',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/menu/index', $data);
    }

    public function menulist()
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
            0 => 'id_menu',
            1 => 'id_menu',
            2 => 'tanggal',
            3 => 'nama_menu',
            4 => 'jadwal_makan',
            5 => 'nama_petugas',
            6 => 'jumlah',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_menu';

        // Get total records count
        $totalRecords = $this->MenuModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->MenuModel
                ->like('nama_menu', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->MenuModel->countAllResults(false);

        // Fetch the data
        $menus = $this->MenuModel->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($menus as $menu) {
            $data[] = $menu;
        }

        // Return the JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function menu($id)
    {
        $data = $this->MenuModel->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->find($id);
        return $this->response->setJSON($data);
    }

    public function petugasoptions()
    {
        $results = $this->PetugasModel->orderBy('nama_petugas', 'ASC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $options[] = [
                'value' => $row['id_petugas'],
                'text' => $row['nama_petugas']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'tanggal' => [
                'label' => 'Tanggal',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'nama_menu' => [
                'label' => 'Nama Menu',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jadwal_makan' => [
                'label' => 'Jadwal Makan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'id_petugas' => [
                'label' => 'Petugas Gizi',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_petugas' => $this->request->getPost('id_petugas'),
            'tanggal' => $this->request->getPost('tanggal'),
            'nama_menu' => $this->request->getPost('nama_menu'),
            'jadwal_makan' => $this->request->getPost('jadwal_makan'),
            'protein_hewani' => $this->request->getPost('protein_hewani'),
            'protein_nabati' => $this->request->getPost('protein_nabati'),
            'sayur' => $this->request->getPost('sayur'),
            'buah' => $this->request->getPost('buah'),
            'jumlah' => 0,
        ];
        $this->MenuModel->save($data);
        $db = db_connect();
        $petugas = $db->table('menu')->where('id_petugas', $this->request->getPost('id_petugas'));
        $totalpetugas = $petugas->countAllResults();
        $db->query('UPDATE petugas SET jumlah_menu = ' . $totalpetugas . ' WHERE id_petugas = ' . $this->request->getPost('id_petugas'));
        return $this->response->setJSON(['success' => true, 'message' => 'Menu berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'tanggal' => [
                'label' => 'Tanggal',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'nama_menu' => [
                'label' => 'Nama Menu',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jadwal_makan' => [
                'label' => 'Jadwal Makan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'id_petugas' => [
                'label' => 'Petugas Gizi',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_menu' => $this->request->getPost('id_menu'),
            'id_petugas' => $this->request->getPost('id_petugas'),
            'tanggal' => $this->request->getPost('tanggal'),
            'nama_menu' => $this->request->getPost('nama_menu'),
            'jadwal_makan' => $this->request->getPost('jadwal_makan'),
            'protein_hewani' => $this->request->getPost('protein_hewani'),
            'protein_nabati' => $this->request->getPost('protein_nabati'),
            'sayur' => $this->request->getPost('sayur'),
            'buah' => $this->request->getPost('buah'),
            'jumlah' => $this->request->getPost('jumlah'),
        ];
        $this->MenuModel->save($data);
        $db = db_connect();
        $petugaslama = $db->table('menu')->where('id_petugas', $this->request->getPost('id_petugas_lama'))->countAllResults();
        $db->query('UPDATE petugas SET jumlah_menu = ' . $petugaslama . ' WHERE id_petugas = ' . $this->request->getPost('id_petugas_lama'));
        $petugasbaru = $db->table('menu')->where('id_petugas', $this->request->getPost('id_petugas'))->countAllResults();
        $db->query('UPDATE petugas SET jumlah_menu = ' . $petugasbaru . ' WHERE id_petugas = ' . $this->request->getPost('id_petugas'));
        return $this->response->setJSON(['success' => true, 'message' => 'Menu berhasil diedit']);
    }

    public function details($id)
    {
        $menu = $this->MenuModel->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->find($id);
        if (empty($menu)) {
            throw PageNotFoundException::forPageNotFound();
        } else {
            $data = [
                'menu' => $menu,
                'title' => 'Detail "" - ' . $this->systemName,
                'headertitle' => 'Memuat...',
                'systemName' => $this->systemName,
                'agent' => $this->request->getUserAgent()
            ];
            echo view('dashboard/menu/details', $data);
        }
    }

    public function listpermintaanmenu($id)
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
            0 => 'id',
            1 => 'id',
            2 => 'nama_pasien',
            3 => 'tanggal_lahir',
            4 => 'jenis_kelamin',
            5 => 'kamar',
            6 => 'jenis_tindakan',
            6 => 'diet',
            6 => 'keterangan',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id';

        // Get total records count
        $totalRecords = $this->PermintaanModel->where('permintaan.id_menu', $id)->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->PermintaanModel
                ->groupStart()
                ->like('nama_pasien', $search)
                ->groupEnd()
                ->where('permintaan.id_menu', $id)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->PermintaanModel->where('permintaan.id_menu', $id)->countAllResults(false);

        // Fetch the data
        $demands = $this->PermintaanModel
            ->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')
            ->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')
            ->where('permintaan.id_menu', $id)
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($demands as $demand) {
            $data[] = $demand;
        }

        // Return the JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function permintaan($id)
    {
        $data = $this->PermintaanModel
            ->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')
            ->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')
            ->find($id);
        return $this->response->setJSON($data);
    }

    public function createpermintaan()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_pasien' => [
                'label' => 'Nama Pasien',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'tanggal_lahir' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'kamar' => [
                'label' => 'Kamar',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_tindakan' => [
                'label' => 'Jenis Tindakan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'diet' => [
                'label' => 'Diet',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_menu' => $this->request->getPost('id_menu'),
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kamar' => $this->request->getPost('kamar'),
            'jenis_tindakan' => $this->request->getPost('jenis_tindakan'),
            'diet' => $this->request->getPost('diet'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $this->PermintaanModel->save($data);
        $db = db_connect();
        $permintaan = $db->table('permintaan')->where('id_menu', $this->request->getPost('id_menu'));
        $totalpermintaan = $permintaan->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $this->request->getPost('id_menu'));
        return $this->response->setJSON(['success' => true, 'message' => 'Permintaan pada menu ini berhasil ditambahkan']);
    }

    public function updatepermintaan()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_pasien' => [
                'label' => 'Nama Pasien',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'tanggal_lahir' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'kamar' => [
                'label' => 'Kamar',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_tindakan' => [
                'label' => 'Jenis Tindakan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'diet' => [
                'label' => 'Diet',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id' => $this->request->getPost('id_permintaan'),
            'id_menu' => $this->request->getPost('id_menu'),
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kamar' => $this->request->getPost('kamar'),
            'jenis_tindakan' => $this->request->getPost('jenis_tindakan'),
            'diet' => $this->request->getPost('diet'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $this->PermintaanModel->save($data);
        $db = db_connect();
        $permintaan = $db->table('permintaan')->where('id_menu', $this->request->getPost('id_menu'));
        $totalpermintaan = $permintaan->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $this->request->getPost('id_menu'));
        return $this->response->setJSON(['success' => true, 'message' => 'Permintaan pada menu ini berhasil diedit']);
    }

    public function delete($id)
    {
        $menu = $this->MenuModel->find($id);
        $this->MenuModel->delete($id);
        $db = db_connect();
        $petugas = $db->table('menu')->where('id_petugas', $menu['id_petugas']);
        $totalpetugas = $petugas->countAllResults();
        $db->query('UPDATE petugas SET jumlah_menu = ' . $totalpetugas . ' WHERE id_petugas = ' . $menu['id_petugas']);
        $db->query('ALTER TABLE `menu` auto_increment = 1');
        $db->query('ALTER TABLE `permintaan` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Menu berhasil dihapus']);
    }
}
