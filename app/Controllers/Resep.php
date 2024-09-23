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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $data = [
                'title' => 'Resep - ' . $this->systemName,
                'headertitle' => 'Resep',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/resep/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function listresep()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function pasienlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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

    public function resep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            // Ambil semua id_obat dan jumlah dari detail_resep yang terkait dengan resep yang dihapus
            $detailResep = $db->query("SELECT id_obat, jumlah FROM detail_resep WHERE id_resep = ?", [$id])->getResultArray();

            // Kurangi jumlah_keluar pada tabel obat
            foreach ($detailResep as $detail) {
                $id_obat = $detail['id_obat'];
                $jumlah = $detail['jumlah'];

                // Ambil jumlah_keluar dari tabel obat
                $obat = $db->query("SELECT jumlah_keluar FROM obat WHERE id_obat = ?", [$id_obat])->getRowArray();

                if ($obat) {
                    // Kurangi jumlah_keluar
                    $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah;

                    if ($new_jumlah_keluar < 0) {
                        $new_jumlah_keluar = 0;
                    }

                    // Update jumlah_keluar di tabel obat
                    $db->query("UPDATE obat SET jumlah_keluar = ? WHERE id_obat = ?", [$new_jumlah_keluar, $id_obat]);
                }
            }

            // Lanjutkan penghapusan resep
            $transaksiDetail = $db->query("SELECT id_transaksi FROM detail_transaksi WHERE id_resep = ?", [$id])->getRow();

            // Hapus resep dan detail terkait
            $this->ResepModel->where('status', 0)->delete($id);
            $db->query('ALTER TABLE `resep` auto_increment = 1');
            $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

            // Jika ada transaksi terkait, hitung ulang total_pembayaran
            if ($transaksiDetail) {
                $id_transaksi = $transaksiDetail->id_transaksi;

                // Hitung ulang total_pembayaran berdasarkan detail transaksi yang tersisa
                $result = $db->query("
        SELECT SUM(harga_satuan) as total_pembayaran 
        FROM detail_transaksi 
        WHERE id_transaksi = ?", [$id_transaksi])->getRow();

                $total_pembayaran = $result->total_pembayaran ?? 0;

                // Update tabel transaksi dengan total_pembayaran yang baru
                $db->query("
        UPDATE transaksi 
        SET total_pembayaran = ? 
        WHERE id_transaksi = ?", [$total_pembayaran, $id_transaksi]);
            }

            return $this->response->setJSON(['message' => 'Resep berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL RESEP
    public function detailresep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailreseplist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $data = $this->DetailResepModel
                ->where('detail_resep.id_resep', $id)
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'obat.id_obat = detail_resep.id_obat', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->findAll();

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailresepitem($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $data = $this->DetailResepModel
                ->where('id_detail_resep', $id)
                ->join('obat', 'obat.id_obat = detail_resep.id_obat', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->find($id);

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function keterangan($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function editketerangan($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist($id_resep)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
                        'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ' • ' . ($row['jumlah_masuk'] - $row['jumlah_keluar']) . ')'
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

    public function tambahdetailresep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_obat' => 'required',
                'dosis_kali' => 'required|numeric|greater_than[0]',
                'dosis_hari' => 'required|numeric|greater_than[0]',
                'cara_pakai' => 'required',
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
                    'dosis_kali' => $this->request->getPost('dosis_kali'),
                    'dosis_hari' => $this->request->getPost('dosis_hari'),
                    'cara_pakai' => $this->request->getPost('cara_pakai'),
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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailresep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'dosis_kali_edit' => 'required|numeric|greater_than[0]',
                'dosis_hari_edit' => 'required|numeric|greater_than[0]',
                'cara_pakai_edit' => 'required',
                'jumlah_edit' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $detail_resep = $this->DetailResepModel->find($this->request->getPost('id_detail_resep'));
            $ObatModel = new ObatModel();
            $obat = $ObatModel->find($detail_resep['id_obat']);

            $db = db_connect();

            $obatBuilder = $db->table('obat');

            // Reset jumlah_keluar to 0 before updating
            $obatBuilder->where('id_obat', $detail_resep['id_obat'])->update([
                'jumlah_keluar' => 0
            ]);

            $obatBuilderNew = $db->table('obat');
            $obatBuilderNew->select('jumlah_masuk, jumlah_keluar');
            $obatBuilderNew->where('id_obat', $detail_resep['id_obat']);
            $obatNew = $obatBuilderNew->get()->getRowArray();

            if ($this->request->getPost('jumlah_edit') > ($obatNew['jumlah_masuk'] - $obatNew['jumlah_keluar'])) {
                $obatBuilder->where('id_obat', $detail_resep['id_obat'])->update([
                    'jumlah_keluar' => $obat['jumlah_keluar']
                ]);
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
            }
            // Calculate the new quantity
            $jumlah_baru = $this->request->getPost('jumlah_edit');

            // Update jumlah_keluar with the new value from detail_resep
            $obatBuilder->where('id_obat', $detail_resep['id_obat'])->update([
                'jumlah_keluar' => $jumlah_baru
            ]);

            // Save Data
            $data = [
                'id_detail_resep' => $this->request->getPost('id_detail_resep'),
                'id_resep' => $id,
                'id_obat' => $detail_resep['id_obat'],
                'dosis_kali' => $this->request->getPost('dosis_kali_edit'),
                'dosis_hari' => $this->request->getPost('dosis_hari_edit'),
                'cara_pakai' => $this->request->getPost('cara_pakai_edit'),
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

            // Update detail_transaksi with new harga_resep
            $harga_resep = $jumlah_baru * $detail_resep['harga_satuan'];

            $detailTransaksiBuilder = $db->table('detail_transaksi');
            $detailTransaksiBuilder->where('id_resep', $id);
            $detailTransaksiBuilder->update([
                'harga_resep' => $harga_resep
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailresep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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
                    if ($new_jumlah_keluar < 0) {
                        $new_jumlah_keluar = 0;
                    }
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

                    // Delete related detail_transaksi records
                    $builderTransaksiDetail = $db->table('detail_transaksi');
                    $builderTransaksiDetail->where('id_resep', $id_resep)->delete();

                    return $this->response->setJSON(['message' => 'Item resep berhasil dihapus']);
                }
            }

            return $this->response->setJSON(['message' => 'Detail resep tidak ditemukan'], 404);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
