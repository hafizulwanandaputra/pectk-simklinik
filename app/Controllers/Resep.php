<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\DokterModel;
use App\Models\ObatModel;
use App\Models\PasienModel;

class Resep extends BaseController
{
    protected $ResepModel;
    protected $DetailResepModel;
    public function __construct()
    {
        $this->ResepModel = new ResepModel();
        $this->DetailResepModel = new DetailResepModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Resep - ' . $this->systemName,
            'headertitle' => 'Resep',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/resep/index', $data);
    }

    public function listresep()
    {
        $search = $this->request->getGet('search');
        $limit = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');

        $limit = $limit ? intval($limit) : 0;
        $offset = $offset ? intval($offset) : 0;

        $ResepModel = $this->ResepModel;

        // Join tables before applying search filter
        $ResepModel
            ->select('resep.*, 
            pasien.nama_pasien as pasien_nama_pasien, 
            dokter.nama_dokter as dokter_nama_dokter')
            ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
            ->join('dokter', 'dokter.id_dokter = resep.id_dokter', 'inner');

        // Apply search filter on supplier name or purchase date
        if ($search) {
            $ResepModel
                ->groupStart()
                ->like('pasien.nama_pasien', $search)
                ->orLike('dokter.nama_dokter', $search)
                ->orLike('tanggal_resep', $search)
                ->groupEnd();
        }

        // Count total results
        $total = $ResepModel->countAllResults(false);

        // Get paginated results
        $Resep = $ResepModel
            ->orderBy('id_resep', 'DESC')
            ->findAll($limit, $offset);

        // Calculate the starting number for the current page
        $startNumber = $offset + 1;

        $dataResep = array_map(function ($data, $index) use ($startNumber) {
            $data['number'] = $startNumber + $index;
            return $data;
        }, $Resep, array_keys($Resep));

        return $this->response->setJSON([
            'resep' => $dataResep,
            'total' => $total
        ]);
    }

    public function pasienlist()
    {
        $PasienModel = new PasienModel();

        $results = $PasienModel->orderBy('nama_pasien', 'DESC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $options[] = [
                'value' => $row['id_pasien'],
                'text' => $row['nama_pasien'] . ' (' . $this->formatNoMr($row['no_mr']) . ' - ' . $row['no_registrasi'] . ')'
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
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

    public function dokterlist()
    {
        $DokterModel = new DokterModel();

        $results = $DokterModel->orderBy('nama_dokter', 'DESC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $options[] = [
                'value' => $row['id_dokter'],
                'text' => $row['nama_dokter']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function resep($id)
    {
        $data = $this->ResepModel
            ->select('resep.*, pasien.nama_pasien as pasien_nama_pasien, dokter.nama_dokter as dokter_nama_dokter')
            ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
            ->join('dokter', 'dokter.id_dokter = resep.id_dokter', 'inner')
            ->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_pasien' => 'required',
            'id_dokter' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_pasien' => $this->request->getPost('id_pasien'),
            'id_dokter' => $this->request->getPost('id_dokter'),
            'tanggal_resep' => date('Y-m-d H:i:s'),
            'jumlah_resep' => 0,
            'keterangan' => '',
        ];
        $this->ResepModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_pasien' => 'required',
            'id_dokter' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $resep = $this->ResepModel->find($this->request->getPost('id_resep'));

        // Save Data
        $data = [
            'id_resep' => $this->request->getPost('id_resep'),
            'id_pasien' => $this->request->getPost('id_pasien'),
            'id_dokter' => $this->request->getPost('id_dokter'),
            'tanggal_resep' => $resep['tanggal_resep'],
            'jumlah_resep' => $resep['jumlah_resep'],
            'keterangan' => $resep['keterangan'],
        ];
        $this->ResepModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil diedit']);
    }

    public function delete($id)
    {
        $db = db_connect();
        $this->ResepModel->delete($id);
        $db->query('ALTER TABLE `resep` auto_increment = 1');
        $db->query('ALTER TABLE `detail_resep` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Resep berhasil dihapus']);
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        $resep = $this->ResepModel
            ->join('id_pasien', 'id_pasien.id_pasien = resep.id_pasien', 'inner')
            ->join('id_dokter', 'id_dokter.id_dokter = resep.id_dokter', 'inner')
            ->find($id);
        $data = [
            'resep' => $resep,
            'title' => 'Detail Resep ' . $id . ' - ' . $this->systemName,
            'headertitle' => 'Detail Resep',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/resep/details', $data);
    }

    // DETAIL PEMBELIAN OBAT
    public function pembelianobat($id)
    {
        $data = $this->PembelianObatModel
            ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
            ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
            ->find($id);
        return $this->response->setJSON($data);
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
            'jumlah' => 'required',
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
            'jumlah_edit' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        $harga_satuan = $this->DetailPembelianObatModel->find($this->request->getPost('id_detail_pembelian_obat'));

        // Save Data
        $data = [
            'id_detail_pembelian_obat' => $this->request->getPost('id_detail_pembelian_obat'),
            'id_pembelian_obat' => $id,
            'id_obat' => $this->request->getPost('id_obat_edit'),
            'jumlah' => $this->request->getPost('jumlah_edit'),
            'harga_satuan' => $harga_satuan['harga_satuan'],
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
