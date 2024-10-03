<?php

namespace App\Controllers;

use App\Models\PembelianObatModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianObatModel;
use App\Models\ItemObatModel;
use App\Models\ObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\I18n\Time;

class PembelianObat extends BaseController
{
    protected $PembelianObatModel;
    protected $DetailPembelianObatModel;
    protected $ItemObatModel;
    public function __construct()
    {
        $this->PembelianObatModel = new PembelianObatModel();
        $this->DetailPembelianObatModel = new DetailPembelianObatModel();
        $this->ItemObatModel = new ItemObatModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = [
                'title' => 'Pembelian Obat - ' . $this->systemName,
                'headertitle' => 'Pembelian Obat',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pembelian_obat/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pembelianobatlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
            supplier.nama_supplier as supplier_nama_supplier')
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner');

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
                    ->orLike('apoteker', $search)
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

    public function pembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);
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
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'apoteker' => session()->get('fullname'),
                'tgl_pembelian' => date('Y-m-d H:i:s'),
                'total_qty' => 0,
                'total_biaya' => 0,
                'diterima' => 0,
            ];
            $this->PembelianObatModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pembelian berhasil ditambahkan']);
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

            // Find all detail pembelian obat before deletion
            $details = $db->table('detail_pembelian_obat')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getResultArray();

            // Check if any obat related to detail has jumlah_keluar > 0
            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $jumlah_keluar = $db->table('obat')
                    ->select('jumlah_keluar')
                    ->where('id_obat', $id_obat)
                    ->get()
                    ->getRowArray()['jumlah_keluar'];

                if ($jumlah_keluar > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Obat dengan ID $id_obat tidak dapat dihapus karena sudah digunakan."
                    ]);
                }
            }

            // Reduce jumlah_masuk in obat table for each detail
            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $obat_masuk = $detail['obat_masuk'];

                $db->table('obat')
                    ->set('jumlah_masuk', "jumlah_masuk - $obat_masuk", false)
                    ->where('id_obat', $id_obat)
                    ->update();
            }
            $this->PembelianObatModel->delete($id);
            $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
            return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function complete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Menghitung total jumlah dan total obat_masuk_baru
            $totals = $db->table('detail_pembelian_obat')
                ->select('SUM(jumlah) as total_jumlah, SUM(obat_masuk_baru) as total_obat_masuk_baru')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getRow();

            // Mendapatkan nilai totalJumlah dan totalObatMasuk
            $totalJumlah = $totals->total_jumlah;
            $totalObatMasuk = $totals->total_obat_masuk_baru;

            if ($totalJumlah == $totalObatMasuk) {
                $db->table('pembelian_obat')
                    ->set('diterima', 1)
                    ->where('id_pembelian_obat', $id)
                    ->update();
            }

            $details = $db->table('detail_pembelian_obat')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getResultArray();

            // Group the details by id_obat and sum the new obat_masuk_baru
            $groupedDetails = [];
            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $new_jumlah_masuk = $detail['obat_masuk_baru'];

                // Sum obat_masuk_baru for the same id_obat
                if (isset($groupedDetails[$id_obat])) {
                    $groupedDetails[$id_obat] += $new_jumlah_masuk;
                } else {
                    $groupedDetails[$id_obat] = $new_jumlah_masuk;
                }
            }

            // Update jumlah_masuk in the obat table for each id_obat
            foreach ($groupedDetails as $id_obat => $total_jumlah_masuk_baru) {
                // Get the previous jumlah_masuk value for this obat
                $previousDetail = $db->table('detail_pembelian_obat')
                    ->select('SUM(obat_masuk) as total_obat_masuk')
                    ->where('id_obat', $id_obat)
                    ->where('id_pembelian_obat', $id)
                    ->get()
                    ->getRow();

                if ($previousDetail) {
                    $previous_jumlah_masuk = $previousDetail->total_obat_masuk;

                    // Subtract the old jumlah_masuk and then add the new one
                    $db->table('obat')
                        ->set('jumlah_masuk', "jumlah_masuk - $previous_jumlah_masuk + $total_jumlah_masuk_baru", false)
                        ->set('updated_at', date('Y-m-d H:i:s'))
                        ->where('id_obat', $id_obat)
                        ->update();

                    // Update detail_pembelian_obat with the new obat_masuk value
                    $db->table('detail_pembelian_obat')
                        ->set('obat_masuk', $total_jumlah_masuk_baru)
                        ->where('id_pembelian_obat', $id)
                        ->where('id_obat', $id_obat)
                        ->update();
                }
            }

            if ($totalJumlah == $totalObatMasuk) {
                return $this->response->setJSON(['success' => true, 'message' => 'Obat sudah diterima. Periksa jumlah masuk di menu obat.']);
            } else {
                return $this->response->setJSON(['success' => true, 'message' => 'Sebagian obat sudah diterima. Jika ada obat yang baru saja diterima, silakan perbarui item masing-masing dan klik "Terima Obat" lagi.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $pembelianobat = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);
            $data = [
                'pembelianobat' => $pembelianobat,
                'title' => 'Detail Pembelian Obat dengan ID ' . $id . ' - ' . $this->systemName,
                'headertitle' => 'Detail Pembelian Obat',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pembelian_obat/details', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpembelianobatlist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Retrieve all detail pembelian obat records based on the id_pembelian_obat
            $detail_pembelian_obat = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('detail_pembelian_obat.id_detail_pembelian_obat', 'ASC')
                ->findAll();

            $result = [];

            // Loop through each $detail_pembelian_obat row
            foreach ($detail_pembelian_obat as $row) {
                // Fetch the items associated with the current id_detail_pembelian_obat
                $item_obat = $this->ItemObatModel
                    ->where('id_detail_pembelian_obat', $row['id_detail_pembelian_obat'])
                    ->orderBy('id_detail_pembelian_obat', 'ASC')
                    ->findAll();

                // Append 'item' to the current row
                $row['item'] = $item_obat;

                // Add the modified row to the result
                $result[] = $row;
            }

            return $this->response->setJSON(array_values($result));
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailpembelianobatitem($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->DetailPembelianObatModel
                ->where('id_detail_pembelian_obat', $id)
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->find($id);

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist($id_supplier, $id_pembelian_obat)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
                        'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ')'
                    ];
                }
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

    public function tambahdetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
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
                'obat_masuk' => 0,
                'obat_masuk_baru' => 0,
                'harga_satuan' => $obat['harga_obat'],
            ];
            $this->DetailPembelianObatModel->save($data);

            $db = db_connect();

            // Calculate total_qty and total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk_baru) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_masuk = $result->total_masuk;
            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'jumlah_edit' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $detail_pembelian_obat = $this->DetailPembelianObatModel->find($this->request->getPost('id_detail_pembelian_obat'));

            if ($this->request->getPost('jumlah_edit') < $detail_pembelian_obat['obat_masuk_baru']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat yang diminta kurang dari jumlah obat yang masuk', 'errors' => NULL]);
            }

            // Save Data
            $data = [
                'id_detail_pembelian_obat' => $this->request->getPost('id_detail_pembelian_obat'),
                'id_pembelian_obat' => $id,
                'id_obat' => $detail_pembelian_obat['id_obat'],
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'obat_masuk' => $detail_pembelian_obat['obat_masuk'],
                'obat_masuk_baru' => $detail_pembelian_obat['obat_masuk_baru'],
                'harga_satuan' => $detail_pembelian_obat['harga_satuan'],
            ];
            $this->DetailPembelianObatModel->save($data);

            $db = db_connect();

            // Calculate total_qty and total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_masuk = $result->total_masuk;
            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function itemobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->ItemObatModel
                ->where('id_item_obat', $id)
                ->find($id);

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahitemobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'no_batch' => 'required',
                'expired' => 'required',
                'jumlah_item' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $detailBuilder1 = $db->table('detail_pembelian_obat');
            $detailBuilder1->where('id_detail_pembelian_obat', $id);

            $detailJumlah = $detailBuilder1->get()->getRowArray();
            if ($this->request->getPost('jumlah_item') > ($detailJumlah['jumlah'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat yang diterima sementara melebihi jumlah yang diminta', 'errors' => NULL]);
            }

            // Save Data
            $data = [
                'id_detail_pembelian_obat' => $id,
                'no_batch' => $this->request->getPost('no_batch'),
                'expired' => $this->request->getPost('expired'),
                'jumlah_item' => $this->request->getPost('jumlah_item'),
            ];
            $this->ItemObatModel->save($data);

            // Get the sum of 'jumlah_item' from 'item_obat'
            $itemBuilder = $db->table('item_obat');
            $itemBuilder->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder->where('id_detail_pembelian_obat', $id);
            $itemSum = $itemBuilder->get()->getRowArray();

            // Update 'obat_masuk_baru' in 'detail_pembelian_obat' with the sum
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $id);
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruiitemobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'no_batch_edit' => 'required',
                'expired_edit' => 'required',
                'jumlah_item_edit' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $detailBuilder1 = $db->table('detail_pembelian_obat');
            $detailBuilder1->where('id_detail_pembelian_obat', $id);

            $detailJumlah = $detailBuilder1->get()->getRowArray();
            if ($this->request->getPost('jumlah_item_edit') > ($detailJumlah['jumlah'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat yang diterima sementara melebihi jumlah yang diminta', 'errors' => NULL]);
            }

            $itemBuilder1 = $db->table('item_obat');
            $itemBuilder1->where('id_detail_pembelian_obat', $id);
            $itemBuilder1->update([
                'id_item_obat' => $this->request->getPost('id_item_obat'),
                'no_batch' => $this->request->getPost('no_batch_edit'),
                'expired' => $this->request->getPost('expired_edit'),
                'jumlah_item' => $this->request->getPost('jumlah_item_edit'),
            ]);

            // Get the sum of 'jumlah_item' from 'item_obat'
            $itemBuilder2 = $db->table('item_obat');
            $itemBuilder2->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder2->where('id_detail_pembelian_obat', $id);
            $itemSum = $itemBuilder2->get()->getRowArray();

            // Update 'obat_masuk_baru' in 'detail_pembelian_obat' with the sum
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $id);
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil diedit']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusitemobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            $itemBuilder = $db->table('item_obat');
            $itemBuilder->select('id_detail_pembelian_obat');
            $itemBuilder->where('id_item_obat', $id); // Assuming 'id_item_obat' is the column name
            $itemObat = $itemBuilder->get()->getRowArray();

            $id_detail_pembelian_obat = $itemObat['id_detail_pembelian_obat'];

            // Delete the item from 'item_obat'
            $this->ItemObatModel->delete($id);

            $db->query('ALTER TABLE `item_obat` auto_increment = 1');

            // Get the sum of 'jumlah_item' from 'item_obat' for the same 'id_detail_pembelian_obat'
            $itemBuilder->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder->where('id_detail_pembelian_obat', $id_detail_pembelian_obat);
            $itemSum = $itemBuilder->get()->getRowArray();

            // Update 'obat_masuk_baru' in 'detail_pembelian_obat' with the new sum
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $id_detail_pembelian_obat);
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Find the detail pembelian obat before deletion to get id_pembelian_obat and other details
            $detail = $this->DetailPembelianObatModel->find($id);
            $id_pembelian_obat = $detail['id_pembelian_obat'];
            $id_obat = $detail['id_obat'];
            $obat_masuk = $detail['obat_masuk'];

            // Reduce jumlah_masuk in obat table for the corresponding id_obat
            $db->table('obat')
                ->set('jumlah_masuk', "jumlah_masuk - $obat_masuk", false)
                ->where('id_obat', $id_obat)
                ->update();

            // Delete the detail pembelian obat
            $this->DetailPembelianObatModel->delete($id);

            // Reset auto_increment
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');

            // Recalculate total_qty, total_masuk, and total_biaya for the pembelian_obat
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id_pembelian_obat);
            $result = $builder->get()->getRow();

            $total_qty = $result->total_qty;
            $total_masuk = $result->total_masuk;
            $total_biaya = $result->total_biaya;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['message' => 'Item pembelian berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function fakturpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $pembelianobat = $this->PembelianObatModel
                ->where('diterima', 0)
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);
            $detailpembelianobat = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->findAll();
            if (empty($detailpembelianobat)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                $filename = $pembelianobat['tgl_pembelian'] . '-pembelian-obat';
                $tanggal = Time::parse($pembelianobat['tgl_pembelian']);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'FAKTUR PEMBELIAN OBAT');

                $sheet->setCellValue('A4', 'Hari/Tanggal:');
                $sheet->setCellValue('C4', $tanggal->toLocalizedString('d MMMM yyyy HH.mm.ss'));
                $sheet->setCellValue('A5', 'Nama Supplier:');
                $sheet->setCellValue('C5', $pembelianobat['nama_supplier']);
                $sheet->setCellValue('A6', 'Alamat Supplier:');
                $sheet->setCellValue('C6', $pembelianobat['alamat_supplier']);
                $sheet->setCellValue('A7', 'Nomor Telepon Supplier:');
                $sheet->setCellValue('C7', $pembelianobat['kontak_supplier']);
                $sheet->setCellValue('A8', 'Apoteker:');
                $sheet->setCellValue('C8', $pembelianobat['apoteker']);
                $sheet->setCellValue('A9', 'ID Pembelian:');
                $sheet->setCellValue('C9', $pembelianobat['id_pembelian_obat']);

                $sheet->setCellValue('A10', 'No.');
                $sheet->setCellValue('B10', 'Nama Obat');
                $sheet->setCellValue('C10', 'Kategori Obat');
                $sheet->setCellValue('D10', 'Bentuk Obat');
                $sheet->setCellValue('E10', 'Harga Satuan');
                $sheet->setCellValue('F10', 'Qty');
                $sheet->setCellValue('G10', 'Total Harga');

                $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:G2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:G3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                $column = 11;
                foreach ($detailpembelianobat as $list) {
                    $sheet->setCellValue('A' . $column, ($column - 10));
                    $sheet->setCellValue('B' . $column, $list['nama_obat']);
                    $sheet->setCellValue('C' . $column, $list['kategori_obat']);
                    $sheet->setCellValue('D' . $column, $list['bentuk_obat']);
                    $sheet->setCellValue('E' . $column, $list['harga_satuan']);
                    $sheet->getStyle('E' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    $sheet->setCellValue('F' . $column, $list['jumlah']);
                    $total = $list['harga_satuan'] * $list['jumlah'];
                    $sheet->setCellValue('G' . $column, $total);
                    $sheet->getStyle('G' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    $sheet->getStyle('B' . $column . ':G' . $column)->getAlignment()->setWrapText(true);
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'Total Pembelian');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':E' . ($column));
                $sheet->setCellValue('F' . ($column), $pembelianobat['total_qty']);
                $sheet->setCellValue('G' . ($column), $pembelianobat['total_biaya']);
                $sheet->getStyle('G' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');

                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:C9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A10:G10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('A1:A9')->getFont()->setBold(TRUE);
                $sheet->getStyle('A10:G10')->getFont()->setBold(TRUE);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(TRUE);

                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:G2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A10:G' . ($column))->applyFromArray($tableBorder);

                $sheet->getColumnDimension('A')->setWidth(50, 'px');
                $sheet->getColumnDimension('B')->setWidth(210, 'px');
                $sheet->getColumnDimension('C')->setWidth(120, 'px');
                $sheet->getColumnDimension('D')->setWidth(120, 'px');
                $sheet->getColumnDimension('E')->setWidth(180, 'px');
                $sheet->getColumnDimension('F')->setWidth(50, 'px');
                $sheet->getColumnDimension('G')->setWidth(180, 'px');

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
