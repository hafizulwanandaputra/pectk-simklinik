<?php

namespace App\Controllers;

use App\Models\PasienModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pasien extends BaseController
{
    protected $PasienModel;
    public function __construct()
    {
        $this->PasienModel = new PasienModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin') {
            $data = [
                'title' => 'Pasien - ' . $this->systemName,
                'headertitle' => 'Pasien',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pasien/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienlist()
    {
        if (session()->get('role') == 'Admin') {
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
                3 => 'jenis_kelamin',
                4 => 'no_mr',
                5 => 'no_registrasi',
                6 => 'nik',
                7 => 'jenis_pasien',
                8 => 'tanggal_lahir',
                9 => 'agama_pasien',
                10 => 'no_hp_pasien',
                11 => 'alamat_pasien',
                12 => 'status_kawin',
                13 => 'tgl_pendaftaran',
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
                $item['no_mr'] = $this->formatNoMr($item['no_mr']);
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

    public function pasien($id)
    {
        if (session()->get('role') == 'Admin') {
            $data = $this->PasienModel->find($id);
            $data['no_mr'] = $this->formatNoMr($data['no_mr']);
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    private function formatNoMr($no_mr)
    {
        // Format no_mr ke xx-xx-xx
        $part1 = substr($no_mr, 0, 2);  // Ambil 2 digit pertama
        $part2 = substr($no_mr, 2, 2);  // Ambil 2 digit kedua
        $part3 = substr($no_mr, 4, 2);  // Ambil 2 digit terakhir

        // Gabungkan menjadi xx-xx-xx
        return "{$part1}-{$part2}-{$part3}";
    }

    public function create()
    {
        if (session()->get('role') == 'Admin') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'nama_pasien' => 'required',
                'jenis_kelamin' => 'required',
                'jenis_pasien' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'agama_pasien' => 'required',
                'alamat_pasien' => 'required',
                'provinsi' => 'required',
                'kota' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'status_kawin' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Generate nomor registrasi
            $jenis_pasien = $this->request->getPost('jenis_pasien'); // 'UMUM' or 'BPJS'
            $date = new \DateTime(); // Get current date and time
            $tanggal = $date->format('d'); // Day (2 digit)
            $bulan = $date->format('m'); // Month (2 digit)
            $tahun = $date->format('y'); // Year (2 digit)

            // Get last registration number to increment
            $lastNoReg = $this->PasienModel->getLastNoReg($jenis_pasien, $tahun, $bulan, $tanggal);
            $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -3)) : 0;
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

            // Format the nomor registrasi
            $no_registrasi = sprintf('RJ%s%s%s%s-%s', $jenis_pasien, $tanggal, $bulan, $tahun, $nextNumber);

            $no_mr = $this->generateNoMr();

            // Save Data
            $data = [
                'nama_pasien' => $this->request->getPost('nama_pasien'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'no_mr' => $no_mr,
                'no_registrasi' => $no_registrasi,
                'nik' => $this->request->getPost('nik'),
                'jenis_pasien' => $this->request->getPost('jenis_pasien'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'agama_pasien' => $this->request->getPost('agama_pasien'),
                'no_hp_pasien' => $this->request->getPost('no_hp_pasien'),
                'alamat_pasien' => $this->request->getPost('alamat_pasien'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kota' => $this->request->getPost('kota'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'desa' => $this->request->getPost('desa'),
                'status_kawin' => $this->request->getPost('status_kawin'),
                'tgl_pendaftaran' => date('Y-m-d H:i:s')
            ];
            $this->PasienModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pasien berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    private function generateNoMr()
    {
        $model = new \App\Models\PasienModel();

        // Dapatkan nomor MR terakhir dari database
        $latest = $model->select('no_mr')->orderBy('id_pasien', 'DESC')->first();

        // Tentukan no_mr awal jika belum ada data
        $latestNoMr = $latest ? $latest['no_mr'] : '000000';

        // Convert to integer and increment
        $newNumber = str_pad((int)$latestNoMr + 1, 6, '0', STR_PAD_LEFT);

        return $newNumber;
    }

    public function update()
    {
        if (session()->get('role') == 'Admin') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'nama_pasien' => 'required',
                'jenis_kelamin' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'agama_pasien' => 'required',
                'alamat_pasien' => 'required',
                'provinsi' => 'required',
                'kota' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'status_kawin' => 'required',
            ]);
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $pasien = $this->PasienModel->find($this->request->getPost('id_pasien'));

            // Save Data
            $data = [
                'id_pasien' => $this->request->getPost('id_pasien'),
                'nama_pasien' => $this->request->getPost('nama_pasien'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'no_mr' => $pasien['no_mr'],
                'no_registrasi' => $pasien['no_registrasi'],
                'nik' => $this->request->getPost('nik'),
                'jenis_pasien' => $pasien['jenis_pasien'],
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'agama_pasien' => $this->request->getPost('agama_pasien'),
                'no_hp_pasien' => $this->request->getPost('no_hp_pasien'),
                'alamat_pasien' => $this->request->getPost('alamat_pasien'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kota' => $this->request->getPost('kota'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'desa' => $this->request->getPost('desa'),
                'status_kawin' => $this->request->getPost('status_kawin'),
                'tgl_pendaftaran' => $pasien['tgl_pendaftaran']
            ];
            $this->PasienModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pasien berhasil diedit']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            try {
                $this->PasienModel->delete($id);
                $db->query('ALTER TABLE `pasien` auto_increment = 1');
                return $this->response->setJSON(['message' => 'Pasien berhasil dihapus']);
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
