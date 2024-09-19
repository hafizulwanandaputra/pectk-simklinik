<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\ObatModel;
use App\Models\PasienModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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
        $status = $this->request->getGet('status');

        $limit = $limit ? intval($limit) : 0;
        $offset = $offset ? intval($offset) : 0;

        $ResepModel = $this->ResepModel;

        // Join tables before applying search filter
        if (session()->get('role') == 'Admin') {
            $ResepModel
                ->select('resep.*, 
                pasien.nama_pasien as pasien_nama_pasien, 
                user.fullname as user_fullname,
                user.username as user_username')
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner');
        } else {
            $ResepModel
                ->select('resep.*, 
                pasien.nama_pasien as pasien_nama_pasien, 
                user.fullname as user_fullname,
                user.username as user_username')
                ->where('resep.id_user', session()->get('id_user'))
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner');
        }

        // Apply status filter if provided
        if ($status === '1') {
            $ResepModel->where('status', 1);
        } elseif ($status === '0') {
            $ResepModel->where('status', 0);
        }

        // Apply search filter on supplier name or purchase date
        if ($search) {
            $ResepModel
                ->groupStart()
                ->like('pasien.nama_pasien', $search)
                ->orLike('user.fullname', $search)
                ->orLike('user.username', $search)
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

    public function resep($id)
    {
        if (session()->get('role') == 'Admin') {
            $data = $this->ResepModel
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner')
                ->find($id);
        } else {
            $data = $this->ResepModel
                ->where('resep.id_user', session()->get('id_user'))
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner')
                ->find($id);
        }
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_pasien' => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_pasien' => $this->request->getPost('id_pasien'),
            'id_user' => session()->get('id_user'),
            'tanggal_resep' => date('Y-m-d H:i:s'),
            'jumlah_resep' => 0,
            'total_biaya' => 0,
            'keterangan' => '',
        ];
        $this->ResepModel->save($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil ditambahkan']);
    }

    public function delete($id)
    {
        $db = db_connect();

        $transaksiDetail = $db->query("SELECT id_transaksi FROM detail_transaksi WHERE id_resep = ?", [$id])->getRow();

        $this->ResepModel->delete($id);
        $db->query('ALTER TABLE `resep` auto_increment = 1');
        $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

        if ($transaksiDetail) {
            $id_transaksi = $transaksiDetail->id_transaksi;

            // Hitung ulang total_qty dan total_biaya berdasarkan detail pembelian yang tersisa
            $result = $db->query("
            SELECT SUM(harga_satuan) as total_pembayaran 
            FROM detail_transaksi 
            WHERE id_transaksi = ?", [$id_transaksi])->getRow();

            $total_pembayaran = $result->total_pembayaran ?? 0;

            // Update tabel pembelian_obat dengan total_qty dan total_pembayaran yang baru
            $db->query("
            UPDATE transaksi 
            SET total_pembayaran = ? 
            WHERE id_transaksi = ?", [$total_pembayaran, $id_transaksi]);
        }

        return $this->response->setJSON(['message' => 'Resep berhasil dihapus']);
    }

    // DETAIL RESEP
    public function detailresep($id)
    {
        if (session()->get('role') == 'Admin') {
            $resep = $this->ResepModel
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner')
                ->find($id);
        } else {
            $resep = $this->ResepModel
                ->where('resep.id_user', session()->get('id_user'))
                ->join('pasien', 'pasien.id_pasien = resep.id_pasien', 'inner')
                ->join('user', 'user.id_user = resep.id_user', 'inner')
                ->find($id);
        }
        if (!empty($resep)) {
            $resep['no_mr'] = $this->formatNoMr($resep['no_mr']);
            $data = [
                'resep' => $resep,
                'title' => 'Detail Resep ' . $id . ' - ' . $this->systemName,
                'headertitle' => 'Detail Resep',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/resep/details', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailreseplist($id)
    {
        $data = $this->DetailResepModel
            ->where('detail_resep.id_resep', $id)
            ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
            ->join('obat', 'obat.id_obat = detail_resep.id_obat', 'inner')
            ->orderBy('id_detail_resep', 'ASC')
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function detailresepitem($id)
    {
        $data = $this->DetailResepModel
            ->where('id_detail_resep', $id)
            ->orderBy('id_detail_resep', 'ASC')
            ->find($id);

        return $this->response->setJSON($data);
    }

    public function keterangan($id)
    {
        if (session()->get('role') == 'Admin') {
            $data = $this->ResepModel
                ->select('keterangan, status')
                ->find($id);
        } else {
            $data = $this->ResepModel
                ->select('keterangan, status')
                ->where('id_user', session()->get('id_user'))
                ->find($id);
        }
        return $this->response->setJSON($data);
    }

    public function editketerangan($id)
    {
        $db = db_connect();
        $resepBuilder = $db->table('resep');
        $resepBuilder->where('id_resep', $id);
        if (session()->get('role') != 'Admin') {
            $resepBuilder->where('id_user', session()->get('id_user'));
        }
        $resepBuilder->update([
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Keterangan resep berhasil diperbarui']);
    }

    public function obatlist($id_resep)
    {
        $ObatModel = new ObatModel();
        $DetailResepModel = new DetailResepModel(); // Model untuk tabel detail_resep

        // Ambil semua obat berdasarkan id_supplier
        $results = $ObatModel->orderBy('nama_obat', 'DESC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $harga_obat = (int) $row['harga_obat'];
            $harga_obat_terformat = number_format($harga_obat, 0, ',', '.');
            // Cek apakah id_resep sudah ada di tabel detail_resep dengan id_resep yang sama
            $isUsed = $DetailResepModel->where('id_obat', $row['id_obat'])
                ->where('id_resep', $id_resep) // Pastikan sesuai dengan id_resep yang sedang digunakan
                ->first();

            // Jika belum ada pada pembelian yang sama, tambahkan ke options
            if ($row['jumlah_masuk'] > 0 && !$isUsed) {
                $options[] = [
                    'value' => $row['id_obat'],
                    'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ' • ' . $row['dosis_kali'] . ' × ' . $row['dosis_hari'] . ' hari • ' . $row['cara_pakai'] . ' • ' . ($row['jumlah_masuk'] - $row['jumlah_keluar']) . ')'
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function tambahdetailresep($id)
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_obat' => 'required',
            'jumlah' => 'required|numeric|greater_than[0]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
        }

        $db = db_connect();

        $builderObat = $db->table('obat');
        $obat = $builderObat->where('id_obat', $this->request->getPost('id_obat'))->get()->getRowArray();

        if ($this->request->getPost('jumlah') > ($obat['jumlah_masuk'] - $obat['jumlah_keluar'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
        } else {
            // Save Data
            $data = [
                'id_resep' => $id,
                'id_obat' => $this->request->getPost('id_obat'),
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_satuan' => $obat['harga_jual'],
            ];
            $this->DetailResepModel->save($data);

            if ($obat) {
                // Mengurangi jumlah_keluar
                $new_jumlah_keluar = $obat['jumlah_keluar'] + $this->request->getPost('jumlah');
                $builderObat->where('id_obat', $this->request->getPost('id_obat'))->update(['jumlah_keluar' => $new_jumlah_keluar]);
            }

            // Calculate jumlah_resep
            $builder = $db->table('detail_resep');
            $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_resep', $id);
            $result = $builder->get()->getRow();

            $jumlah_resep = $result->jumlah_resep;
            $total_biaya = $result->total_biaya;

            // Update resep table
            $resepBuilder = $db->table('resep');
            $resepBuilder->where('id_resep', $id);
            $resepBuilder->update([
                'jumlah_resep' => $jumlah_resep,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil ditambahkan']);
        }
    }

    public function perbaruidetailresep($id)
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'jumlah_edit' => 'required|numeric|greater_than[0]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
        }

        $detail_resep = $this->DetailResepModel->find($this->request->getPost('id_detail_resep'));
        $ObatModel = new ObatModel();
        $obat = $ObatModel->find($detail_resep['id_obat']);

        if ($this->request->getPost('jumlah_edit') > ($obat['jumlah_masuk'] - $obat['jumlah_keluar'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
        } else {
            $db = db_connect();
            $obatBuilder = $db->table('obat');
            $obat2 = $obatBuilder->where('id_obat', $detail_resep['id_obat'])->get()->getRowArray();
            // Calculate the difference in quantity
            $jumlah_lama = $detail_resep['jumlah'];
            $jumlah_baru = $this->request->getPost('jumlah_edit');
            $selisih_jumlah = $jumlah_baru - $jumlah_lama;

            // Update `jumlah_keluar` in `obat` table
            $new_jumlah_keluar = $obat2['jumlah_keluar'] + $selisih_jumlah;
            $obatBuilder->where('id_obat', $detail_resep['id_obat'])->update([
                'jumlah_keluar' => $new_jumlah_keluar
            ]);

            // Save Data
            $data = [
                'id_detail_resep' => $this->request->getPost('id_detail_resep'),
                'id_resep' => $id,
                'id_obat' => $detail_resep['id_obat'],
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'harga_satuan' => $detail_resep['harga_satuan'],
            ];
            $this->DetailResepModel->save($data);

            // Calculate jumlah_resep
            $builder = $db->table('detail_resep');
            $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_resep', $id);
            $result = $builder->get()->getRow();

            $jumlah_resep = $result->jumlah_resep;
            $total_biaya = $result->total_biaya;

            // Update resep table
            $resepBuilder = $db->table('resep');
            $resepBuilder->where('id_resep', $id);
            $resepBuilder->update([
                'jumlah_resep' => $jumlah_resep,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil diperbarui']);
        }
    }

    public function hapusdetailresep($id)
    {
        $db = db_connect();

        $builderDetail = $db->table('detail_resep');
        $detail = $builderDetail->where('id_detail_resep', $id)->get()->getRowArray();

        if ($detail) {
            $id_resep = $detail['id_resep'];
            $id_obat = $detail['id_obat'];
            $jumlah_obat = $detail['jumlah'];

            // Get the current `jumlah_keluar` from obat table
            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $id_obat)->get()->getRowArray();

            if ($obat) {
                // Update `jumlah_keluar` in obat table (reduce the stock based on deleted detail)
                $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah_obat;
                $builderObat->where('id_obat', $id_obat)->update(['jumlah_keluar' => $new_jumlah_keluar]);

                // Delete the detail pembelian obat
                $builderDetail->where('id_detail_resep', $id)->delete();

                // Reset auto_increment (optional, not usually recommended in production)
                $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

                // Calculate jumlah_resep and total_biaya for resep
                $builder = $db->table('detail_resep');
                $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
                $builder->where('id_resep', $id_resep);
                $result = $builder->get()->getRow();

                $jumlah_resep = $result->jumlah_resep ?? 0;  // Handle null in case no rows are left
                $total_biaya = $result->total_biaya ?? 0;

                // Update resep table
                $resepBuilder = $db->table('resep');
                $resepBuilder->where('id_resep', $id_resep);
                $resepBuilder->update([
                    'jumlah_resep' => $jumlah_resep,
                    'total_biaya' => $total_biaya,
                ]);

                return $this->response->setJSON(['message' => 'Item resep berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['message' => 'Detail resep tidak ditemukan'], 404);
    }
}
