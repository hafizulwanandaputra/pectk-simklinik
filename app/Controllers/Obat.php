<?php

namespace App\Controllers;

use App\Models\ObatModel;
use App\Models\SupplierModel;

class Obat extends BaseController
{
    protected $ObatModel;
    public function __construct()
    {
        $this->ObatModel = new ObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Obat - ' . $this->systemName,
            'headertitle' => 'Obat',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/obat/index', $data);
    }

    public function obatlist()
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
            0 => 'id_obat',
            1 => 'id_obat',
            2 => 'nama_supplier',
            3 => 'nama_obat',
            4 => 'kategori_obat',
            5 => 'bentuk_obat',
            6 => 'harga_obat',
            7 => 'harga_jual',
            8 => 'dosis_kali',
            9 => 'cara_pakai',
            10 => 'jumlah_masuk',
            11 => 'jumlah_keluar',
            12 => 'updated_at',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_obat';

        // Get total records count
        $totalRecords = $this->ObatModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->ObatModel
                ->like('nama_obat', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->ObatModel->countAllResults(false);

        // Fetch the data
        $obat = $this->ObatModel
            ->join('supplier', 'supplier.id_supplier = obat.id_supplier', 'inner')
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($obat as $item) {
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

    public function supplierlist()
    {
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
    }

    public function obat($id)
    {
        $data = $this->ObatModel->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_supplier' => 'required',
            'nama_obat' => 'required',
            'kategori_obat' => 'required',
            'bentuk_obat' => 'required',
            'harga_obat' => 'required|numeric|greater_than[0]',
            'harga_jual' => 'required|numeric|greater_than[0]',
            'dosis_kali' => 'required|numeric|greater_than[0]',
            'dosis_hari' => 'required|numeric|greater_than[0]',
            'cara_pakai' => 'required',
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
            'harga_jual' => $this->request->getPost('harga_jual'),
            'dosis_kali' => $this->request->getPost('dosis_kali'),
            'dosis_hari' => $this->request->getPost('dosis_hari'),
            'cara_pakai' => $this->request->getPost('cara_pakai'),
            'jumlah_masuk' => 0,
            'jumlah_keluar' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->ObatModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_supplier' => 'required',
            'nama_obat' => 'required',
            'kategori_obat' => 'required',
            'bentuk_obat' => 'required',
            'harga_obat' => 'required|numeric|greater_than[0]',
            'harga_jual' => 'required|numeric|greater_than[0]',
            'dosis_kali' => 'required|numeric|greater_than[0]',
            'dosis_hari' => 'required|numeric|greater_than[0]',
            'cara_pakai' => 'required',
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
            'dosis_kali' => $this->request->getPost('dosis_kali'),
            'dosis_hari' => $this->request->getPost('dosis_hari'),
            'cara_pakai' => $this->request->getPost('cara_pakai'),
            'jumlah_masuk' => $obat['jumlah_masuk'],
            'jumlah_keluar' => $obat['jumlah_keluar'],
            'updated_at' => $obat['updated_at'],
        ];
        $this->ObatModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil diedit']);
    }

    public function delete($id)
    {
        $db = db_connect();

        // Dapatkan id_pembelian dari detail pembelian obat yang akan dihapus
        $obatDetail = $db->query("SELECT id_pembelian_obat FROM detail_pembelian_obat WHERE id_obat = ?", [$id])->getRow();
        $resepDetail = $db->query("SELECT id_resep FROM detail_resep WHERE id_obat = ?", [$id])->getRow();

        // Hapus obat dari tabel obat
        $this->ObatModel->delete($id);

        // Set ulang auto_increment
        $db->query('ALTER TABLE `obat` auto_increment = 1');
        $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
        $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

        // Perbarui total_qty dan total_biaya di tabel pembelian_obat setelah penghapusan
        if ($obatDetail) {
            $id_pembelian_obat = $obatDetail->id_pembelian_obat;

            // Hitung ulang total_qty dan total_biaya berdasarkan detail pembelian yang tersisa
            $result = $db->query("
            SELECT SUM(jumlah) as total_qty, SUM(harga_satuan * jumlah) as total_biaya 
            FROM detail_pembelian_obat 
            WHERE id_pembelian_obat = ?", [$id_pembelian_obat])->getRow();

            $total_qty = $result->total_qty ?? 0;
            $total_biaya = $result->total_biaya ?? 0;

            // Update tabel pembelian_obat dengan total_qty dan total_biaya yang baru
            $db->query("
            UPDATE pembelian_obat 
            SET total_qty = ?, total_biaya = ? 
            WHERE id_pembelian_obat = ?", [$total_qty, $total_biaya, $id_pembelian_obat]);
        }
        if ($resepDetail) {
            $id_resep = $resepDetail->id_resep;

            // Hitung ulang jumlah_resep dan total_biaya berdasarkan detail pembelian yang tersisa
            $result = $db->query("
            SELECT SUM(jumlah) as jumlah_resep, SUM(harga_satuan * jumlah) as total_biaya 
            FROM detail_resep 
            WHERE id_resep = ?", [$id_resep])->getRow();

            $jumlah_resep = $result->jumlah_resep ?? 0;
            $total_biaya = $result->total_biaya ?? 0;

            // Update tabel resep dengan jumlah_resep dan total_biaya yang baru
            $db->query("
            UPDATE resep 
            SET jumlah_resep = ?, total_biaya = ? 
            WHERE id_resep = ?", [$jumlah_resep, $total_biaya, $id_resep]);
        }
        return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
    }
}
