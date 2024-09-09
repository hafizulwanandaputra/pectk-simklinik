<?php

namespace App\Controllers;

use App\Models\PasienModel;

class Pasien extends BaseController
{
    protected $PasienModel;
    public function __construct()
    {
        $this->PasienModel = new PasienModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pasien - ' . $this->systemName,
            'headertitle' => 'Pasien',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/pasien/index', $data);
    }

    public function pasienlist()
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
            0 => 'id_pasien',
            1 => 'id_pasien',
            2 => 'nama_pasien',
            3 => 'no_mr',
            4 => 'no_registrasi',
            5 => 'jenis_pasien',
            6 => 'tanggal_lahir',
            7 => 'alamat_pasien',
            8 => 'tgl_pendaftaran',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_pasien';

        // Get total records count
        $totalRecords = $this->PasienModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->PasienModel
                ->like('nama_pasien', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->PasienModel->countAllResults(false);

        // Fetch the data
        $pasien = $this->PasienModel
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($pasien as $item) {
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

    public function pasien($id)
    {
        $data = $this->PasienModel->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_pasien' => 'required',
            'jenis_pasien' => 'required',
            'tanggal_lahir' => 'required',
            'alamat_pasien' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $no_mr = $this->generateNoMr();

        // Save Data
        $data = [
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'no_mr' => $no_mr,
            'no_registrasi' => '',
            'jenis_pasien' => $this->request->getPost('jenis_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'alamat_pasien' => $this->request->getPost('alamat_pasien'),
            'tgl_pendaftaran' => date('Y-m-d H:i:s')
        ];
        $this->PasienModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Pasien berhasil ditambahkan']);
    }

    private function generateNoMr()
    {
        $model = $this->PasienModel;

        // Get the latest record to increment
        $latest = $model->select('no_mr')->orderBy('id_pasien', 'DESC')->first();
        $latestNoMr = $latest ? $latest['no_mr'] : '00-00-00';

        // Extract the parts of the latest no_mr
        list($year, $month, $number) = explode('-', $latestNoMr);

        // Increment the number
        $newNumber = str_pad((int)$number + 1, 2, '0', STR_PAD_LEFT);

        // Construct the new no_mr
        $newNoMr = "{$year}-{$month}-{$newNumber}";

        return $newNoMr;
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'nama_pasien' => 'required',
            'jenis_pasien' => 'required',
            'tanggal_lahir' => 'required',
            'alamat_pasien' => 'required',
        ]);
        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $data = $this->PasienModel->find($this->request->getPost('id_pasien'));

        // Save Data
        $data = [
            'id_pasien' => $this->request->getPost('id_pasien'),
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'no_mr' => $data['no_mr'],
            'no_registrasi' => $data['no_registrasi'],
            'jenis_pasien' => $this->request->getPost('jenis_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'alamat_pasien' => $this->request->getPost('alamat_pasien'),
            'tgl_pendaftaran' => $data['tgl_pendaftaran']
        ];
        $this->PasienModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Pasien berhasil diedit']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->PasienModel->delete($id);
        $db->query('ALTER TABLE `pasien` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Pasien berhasil dihapus']);
    }
}
