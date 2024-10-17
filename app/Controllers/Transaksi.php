<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\LayananModel;
use App\Models\ResepModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
                ->select('transaksi.*');

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
                    ->like('nama_pasien', $search)
                    ->orLike('kasir', $search)
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
                $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $client = new Client(); // Create a new Guzzle HTTP client
            $apiUrl = 'https://pectk.padangeyecenter.com/klinik/api/registrasi/rajal/all/' . date('Y-m-d');

            try {
                // Send a GET request to the API
                $response = $client->request('GET', $apiUrl, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'x-key' => env('X-KEY')
                    ],
                ]);

                // Decode JSON and handle potential errors
                $data = json_decode($response->getBody()->getContents(), true);

                // Fetch resep data where status is 0
                $ResepModel = new ResepModel();
                $resepData = $ResepModel->where('status', 0)->findAll(); // Adjust the method as needed

                // Prepare an array for pasien data
                $options = [];
                foreach ($data as $row) {
                    // Check if there is a corresponding resep record
                    foreach ($resepData as $resep) {
                        if ($resep['nomor_registrasi'] == $row['nomor_registrasi']) {
                            $options[] = [
                                'value' => $row['nomor_registrasi'],
                                'text' => $row['nama_pasien'] . ' (' . $row['no_rm'] . ' - ' . $row['nomor_registrasi'] . ')'
                            ];
                        }
                    }
                }

                return $this->response->setJSON([
                    'success' => true,
                    'data' => $options,
                ]);
            } catch (RequestException $e) {
                // Handle API request errors
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Gagal mengambil data pasien: ' . $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function transaksi($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $data = $this->TransaksiModel
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
                'nomor_registrasi' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Get nomor_registrasi from the POST request
            $nomorRegistrasi = $this->request->getPost('nomor_registrasi');

            // Fetch data from external API using Guzzle
            $client = new Client();
            try {
                $response = $client->request('GET', 'https://pectk.padangeyecenter.com/klinik/api/registrasi/rajal/all/' . date('Y-m-d'), [
                    'headers' => [
                        'x-key' => env('X-KEY'),
                    ],
                ]);

                $dataFromApi = json_decode($response->getBody(), true);

                // Check if the data contains the requested nomor_registrasi
                $patientData = null;
                foreach ($dataFromApi as $patient) {
                    if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                        $patientData = $patient;
                        break;
                    }
                }

                if (!$patientData) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Data pasien tidak ditemukan', 'errors' => NULL]);
                }

                $date = new \DateTime(); // Get current date and time
                $tanggal = $date->format('d'); // Day (2 digit)
                $bulan = $date->format('m'); // Month (2 digit)
                $tahun = $date->format('y'); // Year (2 digit)

                // Get last registration number to increment
                $lastNoReg = $this->TransaksiModel->getLastNoReg($tahun, $bulan, $tanggal);
                $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -4)) : 0;
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                // Format the nomor registrasi
                $no_kwitansi = sprintf('TRJ%s%s%s-%s', $tanggal, $bulan, $tahun, $nextNumber);

                // Save Data
                $data = [
                    'nomor_registrasi' => $nomorRegistrasi,
                    'no_rm' => $patientData['no_rm'],
                    'nama_pasien' => $patientData['nama_pasien'],
                    'alamat' => $patientData['alamat'],
                    'telpon' => $patientData['telpon'],
                    'jenis_kelamin' => $patientData['jenis_kelamin'],
                    'tempat_lahir' => $patientData['tempat_lahir'],
                    'tanggal_lahir' => $patientData['tanggal_lahir'],
                    'kasir' => session()->get('fullname'),
                    'no_kwitansi' => $no_kwitansi,
                    'tgl_transaksi' => date('Y-m-d H:i:s'),
                    'total_pembayaran' => 0,
                    'metode_pembayaran' => '',
                    'lunas' => 0,
                ];
                $this->TransaksiModel->save($data);
                return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil ditambahkan']);
            } catch (\Exception $e) {
                return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(), 'errors' => NULL]);
            }
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
                ->find($id);
            $LayananModel = new LayananModel();
            $layanan = $LayananModel
                ->select('jenis_layanan')
                ->groupBy('jenis_layanan')
                ->findAll();
            if (!empty($transaksi)) {
                $data = [
                    'transaksi' => $transaksi,
                    'layanan' => $layanan,
                    'title' => 'Detail Transaksi ' . $transaksi['no_kwitansi'] . ' - ' . $this->systemName,
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

    public function detaillayananlist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $layanan = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('layanan', 'layanan.id_layanan = detail_transaksi.id_layanan', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Untuk memetakan setiap transaksi
            foreach ($layanan as $row) {
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'layanan' => [
                            'id_layanan' => $row['id_layanan'],
                            'nama_layanan' => $row['nama_layanan'],
                            'jenis_layanan' => $row['jenis_layanan'],
                            'tarif' => $row['tarif'],
                            'keterangan' => $row['keterangan'],
                        ],
                    ];
                }
            }

            // Mengembalikan hasil dalam bentuk JSON
            return $this->response->setJSON(array_values($result));
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailobatalkeslist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $obatalkes = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'detail_resep.id_obat = obat.id_obat', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();


            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Untuk memetakan setiap transaksi
            foreach ($obatalkes as $row) {
                $ppn = $row['ppn'];
                $mark_up = $row['mark_up'];
                $harga_obat = $row['harga_obat'];

                // Hitung PPN terlebih dahulu
                $jumlah_ppn = ($harga_obat * $ppn) / 100;
                $total_harga_ppn = $harga_obat + $jumlah_ppn;

                // Setelah itu, terapkan mark-up
                $jumlah_mark_up = ($total_harga_ppn * $mark_up) / 100;
                $total_harga = $total_harga_ppn + $jumlah_mark_up;
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
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
                            'harga_jual' => $total_harga,
                            'signa' => $row['signa'],
                            'catatan' => $row['catatan'],
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

    public function layananlist($id_transaksi, $jenis_layanan = null)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $LayananModel = new LayananModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            // Filter layanan berdasarkan jenis_layanan jika parameter diberikan
            if ($jenis_layanan) {
                $LayananModel->where('jenis_layanan', $jenis_layanan);
            }

            $results = $LayananModel
                ->orderBy('layanan.id_layanan', 'ASC')
                ->findAll();

            $options = [];
            foreach ($results as $row) {
                $tarif = (int) $row['tarif'];
                $tarif_terformat = number_format($tarif, 0, ',', '.');

                $isUsed = $DetailTransaksiModel->where('id_layanan', $row['id_layanan'])
                    ->where('id_transaksi', $id_transaksi)
                    ->first();

                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_layanan'],
                        'text' => $row['nama_layanan'] . ' (Rp' . $tarif_terformat . ')'
                    ];
                }
            }

            return $this->response->setJSON($options);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function reseplist($id_transaksi, $nomor_registrasi)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $ResepModel = new ResepModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            $results = $ResepModel
                ->where('nomor_registrasi', $nomor_registrasi)
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

    public function tambahlayanan($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_layanan' => 'required',
                'qty_transaksi' => 'required|numeric|greater_than[0]',
                'diskon_layanan' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $LayananModel = new LayananModel();
            $layanan = $LayananModel->find($this->request->getPost('id_layanan'));

            // Save Data
            $data = [
                'id_resep' => NULL,
                'id_layanan' => $this->request->getPost('id_layanan'),
                'id_transaksi' => $id,
                'jenis_transaksi' => 'Tindakan',
                'qty_transaksi' => $this->request->getPost('qty_transaksi'),
                'harga_transaksi' => $layanan['tarif'],
                'diskon' => $this->request->getPost('diskon_layanan'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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

    public function tambahobatalkes($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_resep' => 'required',
                'diskon_obatalkes' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $ResepModel = new ResepModel();
            $resep = $ResepModel->find($this->request->getPost('id_resep'));

            // Save Data
            $data = [
                'id_resep' => $this->request->getPost('id_resep'),
                'id_layanan' => NULL,
                'id_transaksi' => $id,
                'jenis_transaksi' => 'Obat dan Alkes',
                'qty_transaksi' => 1,
                'harga_transaksi' => $resep['total_biaya'],
                'diskon' => $this->request->getPost('diskon_obatalkes'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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

    public function perbaruilayanan($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'qty_transaksi_edit' => 'required|numeric|greater_than[0]',
                'diskon_layanan_edit' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $detail_transaksi = $this->DetailTransaksiModel->find($this->request->getPost('id_detail_transaksi'));

            // Save Data
            $data = [
                'id_detail_transaksi' => $this->request->getPost('id_detail_transaksi'),
                'id_resep' => NULL,
                'id_layanan' => $detail_transaksi['id_layanan'],
                'id_transaksi' => $id,
                'jenis_transaksi' => $detail_transaksi['jenis_transaksi'],
                'qty_transaksi' => $this->request->getPost('qty_transaksi_edit'),
                'harga_transaksi' => $detail_transaksi['harga_transaksi'],
                'diskon' => $this->request->getPost('diskon_layanan_edit'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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

    public function perbaruiobatalkes($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'diskon_obatalkes_edit' => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $detail_transaksi = $this->DetailTransaksiModel->find($this->request->getPost('id_detail_transaksi'));

            // Save Data
            $data = [
                'id_detail_transaksi' => $this->request->getPost('id_detail_transaksi'),
                'id_resep' => $detail_transaksi['id_resep'],
                'id_layanan' => NULL,
                'id_transaksi' => $id,
                'jenis_transaksi' => $detail_transaksi['jenis_transaksi'],
                'qty_transaksi' => $detail_transaksi['qty_transaksi'],
                'harga_transaksi' => $detail_transaksi['harga_transaksi'],
                'diskon' => $this->request->getPost('diskon_obatalkes_edit'),
            ];
            $this->DetailTransaksiModel->save($data);

            $db = db_connect();

            // Calculate total_pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
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

    public function process($id_transaksi)
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

            $detailtransaksi = $db->table('detail_transaksi');
            $detailtransaksi->where('id_transaksi', $id_transaksi);
            $details = $detailtransaksi->get()->getResultArray(); // Use getResultArray to retrieve all details

            if ($details) {
                foreach ($details as $detail) {
                    if ($detail['id_resep'] !== null) {
                        $resep = $db->table('resep');
                        $resep->where('id_resep', $detail['id_resep']); // Ensure you're matching by id_resep
                        $resep->update([
                            'status' => 1,
                        ]);
                    }
                }
            }

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
                ->find($id);
            $layanan = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('layanan', 'layanan.id_layanan = detail_transaksi.id_layanan', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result_layanan = [];

            // Untuk memetakan setiap transaksi
            foreach ($layanan as $row) {
                // Jika transaksi ini belum ada dalam array $result_layanan, tambahkan
                if (!isset($result_layanan[$row['id_detail_transaksi']])) {
                    $result_layanan[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'layanan' => [
                            'id_layanan' => $row['id_layanan'],
                            'nama_layanan' => $row['nama_layanan'],
                            'jenis_layanan' => $row['jenis_layanan'],
                            'tarif' => $row['tarif'],
                            'keterangan' => $row['keterangan'],
                        ],
                    ];
                }
            }

            $total_layanan = $this->DetailTransaksiModel
                ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                ->get()->getRowArray();

            $obatalkes = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'detail_resep.id_obat = obat.id_obat', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result_obatalkes = [];

            // Untuk memetakan setiap transaksi
            foreach ($obatalkes as $row) {
                $ppn = $row['ppn'];
                $harga_obat = $row['harga_obat'];
                $jumlah_ppn = ($harga_obat * $ppn) / 100;
                $total_harga = $harga_obat + $jumlah_ppn;
                // Jika transaksi ini belum ada dalam array $result_obatalkes, tambahkan
                if (!isset($result_obatalkes[$row['id_detail_transaksi']])) {
                    $result_obatalkes[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
                            'detail_resep' => []
                        ],
                    ];
                }

                // Tambahkan detail_resep ke transaksi
                $result_obatalkes[$row['id_detail_transaksi']]['resep']['detail_resep'][] = [
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
                            'harga_jual' => $total_harga,
                            'signa' => $row['signa'],
                            'catatan' => $row['catatan'],
                            'cara_pakai' => $row['cara_pakai'],
                            'jumlah' => $row['jumlah'],
                            'harga_satuan' => $row['harga_satuan'],
                        ]
                    ],
                ];
            }
            $total_obatalkes = $this->DetailTransaksiModel
                ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                ->get()->getRowArray();

            if (!empty($transaksi) && $transaksi['lunas'] == 1) {
                // dd($total_obatalkes);
                // die;
                $data = [
                    'transaksi' => $transaksi,
                    'layanan' => array_values($result_layanan),
                    'obatalkes' => array_values($result_obatalkes),
                    'total_layanan' => $total_layanan['total_harga'],
                    'total_obatalkes' => $total_obatalkes['total_harga'],
                    'title' => 'Detail Transaksi ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/transaksi/struk', $data);
                // die;
                $dompdf = new Dompdf();
                $html = view('dashboard/transaksi/struk', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('kwitansi-id-' . $transaksi['id_transaksi'] . '-' . $transaksi['no_kwitansi'] . '-' . $transaksi['tgl_transaksi'] . '-' . urlencode($transaksi['nama_pasien']) . '.pdf', [
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
