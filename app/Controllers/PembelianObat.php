<?php

namespace App\Controllers;

use App\Models\PembelianObatModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianObatModel;

class PembelianObat extends BaseController
{
    protected $PembelianObatModel;
    protected $DetailPembelianObatModel;
    public function __construct()
    {
        $this->PembelianObatModel = new PembelianObatModel();
        $this->DetailPembelianObatModel = new DetailPembelianObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pembelian Obat - ' . $this->systemName,
            'headertitle' => 'Pembelian Obat',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/pembelian_obat/index', $data);
    }

    public function pembelianobatlist()
    {
        $search = $this->request->getGet('search');
        $limit = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');

        $limit = $limit ? intval($limit) : 0;
        $offset = $offset ? intval($offset) : 0;

        $PembelianObatModel = $this->PembelianObatModel;

        // Apply search filter
        $PembelianObatModel->groupStart()
            ->like('tgl_pembelian', $search)
            ->groupEnd();

        // Count total results
        $total = $PembelianObatModel->countAllResults(false);

        // Get paginated results
        $PembelianObat = $PembelianObatModel
            ->select('pembelian_obat.*, 
                supplier.nama_supplier as supplier_nama_supplier, 
                user.fullname as user_fullname, 
                user.username as user_username, 
                (SELECT SUM(harga_satuan) FROM detail_pembelian_obat WHERE detail_pembelian_obat.id_pembelian_obat = pembelian_obat.id_pembelian_obat) as total_harga')
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
            ->orderBy('tgl_pembelian', 'DESC')
            ->findAll($limit, $offset);

        // Calculate the starting number for the current page
        $startNumber = $offset + 1;

        $dataPembelianObat = array_map(function ($data, $index) use ($startNumber) {
            $data['number'] = $startNumber + $index;
            return $data;
        }, $PembelianObat, array_keys($PembelianObat));

        return $this->response->setJSON([
            'pembelian_obat' => $dataPembelianObat,
            'total' => $total
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

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_supplier' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_user' => session()->get('id_user'),
            'tgl_pembelian' => date('Y-m-d H:i:s'),
        ];
        $this->PembelianObatModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Pembelian berhasil ditambahkan']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->PembelianObatModel->delete($id);
        $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        $pembelianobat = $this->PembelianObatModel
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
            ->find($id);
        // dd($pembelianobat);
        // die;
        $data = [
            'pembelianobat' => $pembelianobat,
            'title' => 'Detail Pembelian Obat dengan ID ' . $id . ' - ' . $this->systemName,
            'headertitle' => 'Detail Pembelian Obat',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/pembelian_obat/details', $data);
    }

    public function detailpembelianobatlist($id)
    {
        $data = $this->DetailPembelianObatModel
            ->where('detail_pembelian_obat.id_pembelian_obat', $id)
            ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
            ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
            ->orderBy('id_detail_pembelian_obat', 'ASC')
            ->findAll();

        return $this->response->setJSON($data);
    }
}
