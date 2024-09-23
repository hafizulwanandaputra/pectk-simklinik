<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ResepModel;
use App\Models\PasienModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;

class Transaksi extends BaseController
{
    protected $TransaksiModel;
    protected $DetailTransaksiModel;
    public function __construct()
    {
        $this->TransaksiModel = new TransaksiModel();
        $this->DetailTransaksiModel = new DetailTransaksiModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = [
                'title' => 'Kasir - ' . $this->systemName,
                'headertitle' => 'Kasir',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/transaksi/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function listtransaksi()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $status = $this->request->getGet('status');

            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $TransaksiModel = $this->TransaksiModel;

            $TransaksiModel
                ->select('transaksi.*, 
                pasien.nama_pasien as pasien_nama_pasien, 
                user.fullname as user_fullname,
                user.username as user_username')
                ->join('pasien', 'pasien.id_pasien = transaksi.id_pasien', 'inner')
                ->join('user', 'user.id_user = transaksi.id_user', 'inner');

            // Apply status filter if provided
            if ($status === '1') {
                $TransaksiModel->where('lunas', 1);
            } elseif ($status === '0') {
                $TransaksiModel->where('lunas', 0);
            }

            // Apply search filter on supplier name or purchase date
            if ($search) {
                $TransaksiModel
                    ->groupStart()
                    ->like('pasien.nama_pasien', $search)
                    ->orLike('user.fullname', $search)
                    ->orLike('user.username', $search)
                    ->orLike('tgl_transaksi', $search)
                    ->groupEnd();
            }

            // Count total results
            $total = $TransaksiModel->countAllResults(false);

            // Get paginated results
            $Transaksi = $TransaksiModel
                ->orderBy('id_transaksi', 'DESC')
                ->findAll($limit, $offset);

            // Calculate the starting number for the current page
            $startNumber = $offset + 1;

            $dataTransaksi = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                $db = db_connect();
                // Calculate total_pembayaran
                $builder = $db->table('detail_transaksi');
                $builder->select('SUM(harga_resep * (1 - (diskon / 100))) as total_pembayaran');
                $builder->where('id_transaksi', $data['id_transaksi']);
                $result = $builder->get()->getRow();

                $total_pembayaran = $result->total_pembayaran;

                // Update transaksi table
                $transaksiBuilder = $db->table('transaksi');
                $transaksiBuilder->where('id_transaksi', $data['id_transaksi']);
                $transaksiBuilder->update([
                    'total_pembayaran' => $total_pembayaran,
                ]);
                return $data;
            }, $Transaksi, array_keys($Transaksi));

            return $this->response->setJSON([
                'transaksi' => $dataTransaksi,
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $PasienModel = new PasienModel();

            $results = $PasienModel->select('pasien.id_pasien, pasien.nama_pasien, pasien.no_mr, pasien.no_registrasi')
                ->join('resep', 'resep.id_pasien = pasien.id_pasien')
                ->where('resep.status', 0)
                ->groupBy('pasien.id_pasien')
                ->orderBy('pasien.nama_pasien', 'DESC')
                ->findAll();

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

    public function transaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = $this->TransaksiModel
                ->join('pasien', 'pasien.id_pasien = transaksi.id_pasien', 'inner')
                ->join('user', 'user.id_user = transaksi.id_user', 'inner')
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
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
                'id_user' => session()->get('id_user'),
                'id_pasien' => $this->request->getPost('id_pasien'),
                'tgl_transaksi' => date('Y-m-d H:i:s'),
                'total_pembayaran' => 0,
                'metode_pembayaran' => '',
                'lunas' => 0,
            ];
            $this->TransaksiModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $db = db_connect();

            // Find all `id_resep` related to the transaction being deleted
            $query = $db->query('SELECT DISTINCT id_resep FROM detail_transaksi WHERE id_transaksi = ?', [$id]);
            $results = $query->getResult();

            if (!empty($results)) {
                // Loop through each related `id_resep` and update its status to 0
                foreach ($results as $row) {
                    $db->query('UPDATE resep SET status = 0 WHERE id_resep = ?', [$row->id_resep]);
                }
            }

            // Delete the transaction
            $this->TransaksiModel->delete($id);

            // Reset auto increment
            $db->query('ALTER TABLE `transaksi` auto_increment = 1');
            $db->query('ALTER TABLE `detail_transaksi` auto_increment = 1');

            return $this->response->setJSON(['message' => 'Transaksi berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }


    // DETAIL TRANSAKSI
    public function detailtransaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $transaksi = $this->TransaksiModel
                ->join('pasien', 'pasien.id_pasien = transaksi.id_pasien', 'inner')
                ->join('user', 'user.id_user = transaksi.id_user', 'inner')
                ->find($id);
            if (!empty($transaksi)) {
                $transaksi['no_mr'] = $this->formatNoMr($transaksi['no_mr']);
                $data = [
                    'transaksi' => $transaksi,
                    'title' => 'Detail Transaksi ' . $id . ' - ' . $this->systemName,
                    'headertitle' => 'Detail Transaksi',
                    'agent' => $this->request->getUserAgent()
                ];
                return view('dashboard/transaksi/details', $data);
            } else {
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailtransaksilist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $transaksi = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('user', 'resep.id_user = user.id_user', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'detail_resep.id_obat = obat.id_obat', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Untuk memetakan setiap transaksi
            foreach ($transaksi as $row) {
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'harga_resep' => $row['harga_resep'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'id_user' => $row['id_user'],
                            'id_pasien' => $row['id_pasien'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'keterangan' => $row['keterangan'],
                            'status' => $row['status'],
                            'user' => [
                                'id_user' => $row['id_user'],
                                'fullname' => $row['fullname'],
                                'username' => $row['username'],
                            ],
                            'detail_resep' => []
                        ],
                    ];
                }

                // Tambahkan detail_resep ke transaksi
                $result[$row['id_detail_transaksi']]['resep']['detail_resep'][] = [
                    'id_detail_resep' => $row['id_detail_resep'],
                    'id_resep' => $row['id_resep'],
                    'id_obat' => $row['id_obat'],
                    'jumlah' => $row['jumlah'],
                    'harga_satuan' => $row['harga_satuan'],
                    'obat' => [
                        [
                            'id_obat' => $row['id_obat'],
                            'id_supplier' => $row['id_supplier'],
                            'nama_obat' => $row['nama_obat'],
                            'kategori_obat' => $row['kategori_obat'],
                            'bentuk_obat' => $row['bentuk_obat'],
                            'harga_obat' => $row['harga_obat'],
                            'harga_jual' => $row['harga_jual'],
                            'dosis_kali' => $row['dosis_kali'],
                            'dosis_hari' => $row['dosis_hari'],
                            'cara_pakai' => $row['cara_pakai'],
                            'jumlah_masuk' => $row['jumlah_masuk'],
                            'jumlah_keluar' => $row['jumlah_keluar'],
                            'updated_at' => $row['updated_at']
                        ]
                    ],
                ];
            }

            // Mengembalikan hasil dalam bentuk JSON
            return $this->response->setJSON(array_values($result));
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailtransaksiitem($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = $this->DetailTransaksiModel
                ->where('id_detail_transaksi', $id)
                ->orderBy('id_detail_transaksi', 'ASC')
                ->find($id);

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function reseplist($id_transaksi, $id_pasien)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $ResepModel = new ResepModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            $results = $ResepModel
                ->where('id_pasien', $id_pasien)
                ->where('status', 0)
                ->where('total_biaya >', 0)
                ->orderBy('resep.id_resep', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $total_biaya = (int) $row['total_biaya'];
                $total_biaya_terformat = number_format($total_biaya, 0, ',', '.');

                $isUsed = $DetailTransaksiModel->where('id_resep', $row['id_resep'])
                    ->where('id_transaksi', $id_transaksi)
                    ->first();

                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_resep'],
                        'text' => $row['tanggal_resep'] . ' (Rp' . $total_biaya_terformat . ')'
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

    public function tambahdetailtransaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_resep' => 'required',
                'diskon' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $ResepModel = new ResepModel();
            $resep = $ResepModel->find($this->request->getPost('id_resep'));

            // Save Data
            $data = [
                'id_resep' => $this->request->getPost('id_resep'),
                'id_transaksi' => $id,
                'harga_resep' => $resep['total_biaya'],
                'diskon' => $this->request->getPost('diskon'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM(harga_resep * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran;

            // Update transaksi table
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailtransaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'diskon_edit' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $detail_transaksi = $this->DetailTransaksiModel->find($this->request->getPost('id_detail_transaksi'));

            // Save Data
            $data = [
                'id_detail_transaksi' => $this->request->getPost('id_detail_transaksi'),
                'id_resep' => $detail_transaksi['id_resep'],
                'id_transaksi' => $id,
                'harga_resep' => $detail_transaksi['harga_resep'],
                'diskon' => $this->request->getPost('diskon_edit'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM(harga_resep * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran;

            // Update transaksi table
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran,
            ]);


            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailtransaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $db = db_connect();

            // Find the detail pembelian obat before deletion to get id_transaksi
            $detail = $this->DetailTransaksiModel->find($id);

            $id_transaksi = $detail['id_transaksi'];

            // Delete the detail pembelian obat
            $this->DetailTransaksiModel->delete($id);

            // Reset auto_increment
            $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM(harga_resep * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id_transaksi);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran;

            // Update transaksi table
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id_transaksi);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran,
            ]);

            return $this->response->setJSON(['message' => 'Item transaksi berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function process($id_transaksi, $id_pasien)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'terima_uang' => 'required|numeric|greater_than[0]',
                'metode_pembayaran' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $terima_uang = $this->request->getPost('terima_uang');

            $db = db_connect();
            $db->transBegin();  // Start transaction

            // Get total_pembayaran from transaksi table
            $transaksi = $db->table('transaksi')
                ->select('total_pembayaran')
                ->where('id_transaksi', $id_transaksi)
                ->get()
                ->getRow();

            $total_pembayaran = $transaksi->total_pembayaran;

            // Check if terima_uang is less than total_pembayaran
            if ($terima_uang < $total_pembayaran) {
                return $this->response->setJSON(['success' => false, 'message' => 'Uang yang diterima kurang dari total pembayaran', 'errors' => NULL]);
            }

            // Calculate uang_kembali if terima_uang is greater than total_pembayaran
            $uang_kembali = $terima_uang > $total_pembayaran ? $terima_uang - $total_pembayaran : 0;

            // Update transaksi
            $transaksi = $db->table('transaksi');
            $transaksi->where('id_transaksi', $id_transaksi);
            $transaksi->update([
                'terima_uang' => $terima_uang,
                'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
                'uang_kembali' => $uang_kembali,
                'lunas' => 1,
            ]);

            // Update resep status
            $resep = $db->table('resep');
            $resep->where('id_pasien', $id_pasien);
            $resep->update([
                'status' => 1,
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();  // Rollback if there is any issue
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses transaksi', 'errors' => NULL]);
            } else {
                $db->transCommit();  // Commit the transaction if everything is fine
                return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil diproses. Silakan cetak struk transaksi.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function struk($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $transaksi = $this->TransaksiModel
                ->join('pasien', 'pasien.id_pasien = transaksi.id_pasien', 'inner')
                ->join('user', 'user.id_user = transaksi.id_user', 'inner')
                ->find($id);
            $detail_transaksi = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('user', 'resep.id_user = user.id_user', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'detail_resep.id_obat = obat.id_obat', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();
            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Untuk memetakan setiap transaksi
            foreach ($detail_transaksi as $row) {
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'harga_resep' => $row['harga_resep'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'id_user' => $row['id_user'],
                            'id_pasien' => $row['id_pasien'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'keterangan' => $row['keterangan'],
                            'status' => $row['status'],
                            'user' => [
                                'id_user' => $row['id_user'],
                                'fullname' => $row['fullname'],
                                'username' => $row['username'],
                            ],
                            'detail_resep' => []
                        ],
                    ];
                }

                // Tambahkan detail_resep ke transaksi
                $result[$row['id_detail_transaksi']]['resep']['detail_resep'][] = [
                    'id_detail_resep' => $row['id_detail_resep'],
                    'id_resep' => $row['id_resep'],
                    'id_obat' => $row['id_obat'],
                    'jumlah' => $row['jumlah'],
                    'harga_satuan' => $row['harga_satuan'],
                    'obat' => [
                        [
                            'id_obat' => $row['id_obat'],
                            'id_supplier' => $row['id_supplier'],
                            'nama_obat' => $row['nama_obat'],
                            'kategori_obat' => $row['kategori_obat'],
                            'bentuk_obat' => $row['bentuk_obat'],
                            'harga_obat' => $row['harga_obat'],
                            'harga_jual' => $row['harga_jual'],
                            'dosis_kali' => $row['dosis_kali'],
                            'dosis_hari' => $row['dosis_hari'],
                            'cara_pakai' => $row['cara_pakai'],
                            'jumlah_masuk' => $row['jumlah_masuk'],
                            'jumlah_keluar' => $row['jumlah_keluar'],
                            'updated_at' => $row['updated_at']
                        ]
                    ],
                ];
            }
            if (!empty($transaksi) && $transaksi['lunas'] == 1) {
                // dd(array_values($result));
                // die;
                $transaksi['no_mr'] = $this->formatNoMr($transaksi['no_mr']);
                $data = [
                    'transaksi' => $transaksi,
                    'detail_transaksi' => array_values($result),
                    'title' => 'Detail Transaksi ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/transaksi/struk', $data);
                // die;
                $dompdf = new Dompdf();
                $html = view('dashboard/transaksi/struk', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('eticket-id-' . $transaksi['id_transaksi'] . '-' . $transaksi['tgl_transaksi'] . '-' . urlencode($transaksi['nama_pasien']) . '.pdf', [
                    'Attachment' => FALSE
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
