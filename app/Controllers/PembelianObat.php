<?php

namespace App\Controllers;

use App\Models\PembelianObatModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianObatModel;
use App\Models\ObatModel;

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
        $status = $this->request->getGet('status');

        $limit = $limit ? intval($limit) : 0;
        $offset = $offset ? intval($offset) : 0;

        $PembelianObatModel = $this->PembelianObatModel;

        // Join tables before applying search filter
        $PembelianObatModel
            ->select('pembelian_obat.*, 
            supplier.nama_supplier as supplier_nama_supplier, 
            user.fullname as user_fullname, 
            user.username as user_username')
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner');

        // Apply status filter if provided
        if ($status === '1') {
            $PembelianObatModel->where('diterima', 1);
        } elseif ($status === '0') {
            $PembelianObatModel->where('diterima', 0);
        }

        // Apply search filter on supplier name or purchase date
        if ($search) {
            $PembelianObatModel
                ->groupStart()
                ->like('supplier.nama_supplier', $search)
                ->orLike('user.fullname', $search)
                ->orLike('user.username', $search)
                ->orLike('tgl_pembelian', $search)
                ->groupEnd();
        }

        // Count total results
        $total = $PembelianObatModel->countAllResults(false);

        // Get paginated results
        $PembelianObat = $PembelianObatModel
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

    public function pembelianobat($id)
    {
        $data = $this->PembelianObatModel
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
            ->find($id);
        return $this->response->setJSON($data);
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
            'total_qty' => 0,
            'total_biaya' => 0,
            'diterima' => 0,
        ];
        $this->PembelianObatModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Pembelian berhasil ditambahkan']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->PembelianObatModel->delete($id);
        $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
        $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
    }

    public function complete($id)
    {
        $db = db_connect();
        $db->table('pembelian_obat')
            ->set('diterima', 1)
            ->where('id_pembelian_obat', $id)
            ->update();

        $details = $db->table('detail_pembelian_obat')
            ->where('id_pembelian_obat', $id)
            ->get()
            ->getResultArray();

        // Update jumlah_masuk di tabel obat untuk setiap id_obat di detail_pembelian_obat
        foreach ($details as $detail) {
            $id_obat = $detail['id_obat'];
            $jumlah_masuk = $detail['jumlah'];

            // Update jumlah_masuk di tabel obat
            $db->table('obat')
                ->set('jumlah_masuk', "jumlah_masuk + $jumlah_masuk", false) // false untuk menghindari quoting otomatis
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('id_obat', $id_obat)
                ->update();
        }
        return $this->response->setJSON(['message' => 'Obat sudah diterima. Periksa jumlah masuk di menu obat.']);
    }

    public function cancel($id)
    {
        $db = db_connect();
        $details = $db->table('detail_pembelian_obat')
            ->where('id_pembelian_obat', $id)
            ->get()
            ->getResultArray();

        foreach ($details as $detail) {
            $id_obat = $detail['id_obat'];
            $jumlah_masuk = $detail['jumlah'];
            $db->table('obat')
                ->set('jumlah_masuk', "jumlah_masuk - $jumlah_masuk", false) // false untuk menghindari quoting otomatis
                ->set('updated_at', date('Y-m-d H:i:s'))
                ->where('id_obat', $id_obat)
                ->update();
        }

        $db->table('pembelian_obat')
            ->set('diterima', 0)
            ->where('id_pembelian_obat', $id)
            ->update();

        return $this->response->setJSON(['message' => 'Pembelian obat telah dibatalkan. Jumlah masuk obat telah dikurangi.']);
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        $pembelianobat = $this->PembelianObatModel
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
            ->find($id);
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

    public function detailpembelianobatitem($id)
    {
        $data = $this->DetailPembelianObatModel
            ->where('id_detail_pembelian_obat', $id)
            ->orderBy('id_detail_pembelian_obat', 'ASC')
            ->find($id);

        return $this->response->setJSON($data);
    }

    public function obatlist($id_supplier, $id_pembelian_obat)
    {
        $ObatModel = new ObatModel();
        $DetailPembelianObatModel = new DetailPembelianObatModel(); // Model untuk tabel detail_pembelian_obat

        // Ambil semua obat berdasarkan id_supplier
        $results = $ObatModel->where('id_supplier', $id_supplier)->orderBy('nama_obat', 'DESC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $harga_obat = (int) $row['harga_obat'];
            $harga_obat_terformat = number_format($harga_obat, 0, ',', '.');
            // Cek apakah id_obat sudah ada di tabel detail_pembelian_obat dengan id_pembelian_obat yang sama
            $isUsed = $DetailPembelianObatModel->where('id_obat', $row['id_obat'])
                ->where('id_pembelian_obat', $id_pembelian_obat) // Pastikan sesuai dengan id_pembelian_obat yang sedang digunakan
                ->first();

            // Jika belum ada pada pembelian yang sama, tambahkan ke options
            if (!$isUsed) {
                $options[] = [
                    'value' => $row['id_obat'],
                    'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ' • ' . $row['dosis_kali'] . ' × ' . $row['dosis_hari'] . ' hari • ' . $row['cara_pakai'] . ')'
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function tambahdetailpembelianobat($id)
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_obat' => 'required',
            'jumlah' => 'required|numeric|greater_than[0]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $ObatModel = new ObatModel();
        $obat = $ObatModel->find($this->request->getPost('id_obat'));

        // Save Data
        $data = [
            'id_pembelian_obat' => $id,
            'id_obat' => $this->request->getPost('id_obat'),
            'jumlah' => $this->request->getPost('jumlah'),
            'harga_satuan' => $obat['harga_obat'],
        ];
        $this->DetailPembelianObatModel->save($data);

        $db = db_connect();

        // Calculate total_qty and total_biaya
        $builder = $db->table('detail_pembelian_obat');
        $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
        $builder->where('id_pembelian_obat', $id);
        $result = $builder->get()->getRow();

        $total_qty = $result->total_qty;
        $total_biaya = $result->total_biaya;

        // Update pembelian_obat table
        $pembelianObatBuilder = $db->table('pembelian_obat');
        $pembelianObatBuilder->where('id_pembelian_obat', $id);
        $pembelianObatBuilder->update([
            'total_qty' => $total_qty,
            'total_biaya' => $total_biaya,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil ditambahkan']);
    }

    public function perbaruidetailpembelianobat($id)
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'jumlah_edit' => 'required|numeric|greater_than[0]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $detail_pembelian_obat = $this->DetailPembelianObatModel->find($this->request->getPost('id_detail_pembelian_obat'));

        // Save Data
        $data = [
            'id_detail_pembelian_obat' => $this->request->getPost('id_detail_pembelian_obat'),
            'id_pembelian_obat' => $id,
            'id_obat' => $detail_pembelian_obat['id_obat'],
            'jumlah' => $this->request->getPost('jumlah_edit'),
            'harga_satuan' => $detail_pembelian_obat['harga_satuan'],
        ];
        $this->DetailPembelianObatModel->save($data);

        $db = db_connect();

        // Calculate total_qty and total_biaya
        $builder = $db->table('detail_pembelian_obat');
        $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
        $builder->where('id_pembelian_obat', $id);
        $result = $builder->get()->getRow();

        $total_qty = $result->total_qty;
        $total_biaya = $result->total_biaya;

        // Update pembelian_obat table
        $pembelianObatBuilder = $db->table('pembelian_obat');
        $pembelianObatBuilder->where('id_pembelian_obat', $id);
        $pembelianObatBuilder->update([
            'total_qty' => $total_qty,
            'total_biaya' => $total_biaya,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil diperbarui']);
    }

    public function hapusdetailpembelianobat($id)
    {
        $db = db_connect();

        // Find the detail pembelian obat before deletion to get id_pembelian_obat
        $detail = $this->DetailPembelianObatModel->find($id);

        $id_pembelian_obat = $detail['id_pembelian_obat'];

        // Delete the detail pembelian obat
        $this->DetailPembelianObatModel->delete($id);

        // Reset auto_increment
        $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');

        // Recalculate total_qty and total_biaya after deletion
        $builder = $db->table('detail_pembelian_obat');
        $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
        $builder->where('id_pembelian_obat', $id_pembelian_obat);
        $result = $builder->get()->getRow();

        $total_qty = $result->total_qty ?? 0; // Handle case when no rows are left
        $total_biaya = $result->total_biaya ?? 0;

        // Update pembelian_obat table
        $pembelianObatBuilder = $db->table('pembelian_obat');
        $pembelianObatBuilder->where('id_pembelian_obat', $id_pembelian_obat);
        $pembelianObatBuilder->update([
            'total_qty' => $total_qty,
            'total_biaya' => $total_biaya,
        ]);

        return $this->response->setJSON(['message' => 'Item pembelian berhasil dihapus']);
    }
}
