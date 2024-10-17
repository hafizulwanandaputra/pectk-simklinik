<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\ObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $status = $this->request->getGet('status');

            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $ResepModel = $this->ResepModel;

            // Join tables before applying search filter
            if (session()->get('role') != 'Dokter') {
                $ResepModel
                    ->select('resep.*');
            } else {
                $ResepModel
                    ->select('resep.*')
                    ->where('resep.dokter', session()->get('fullname'));
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
                    ->like('nama_pasien', $search)
                    ->orLike('dokter', $search)
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
            $client = new Client(); // Create a new Guzzle HTTP client
            $apiUrl = 'https://pectk.padangeyecenter.com/klinik/api/registrasi/rajal/all/' . date('Y-m-d');
            //  . date('Y-m-d')

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

                $options = [];
                foreach ($data as $row) {
                    $options[] = [
                        'value' => $row['nomor_registrasi'],
                        'text' => $row['nama_pasien'] . ' (' . $row['no_rm'] . ' - ' . $row['nomor_registrasi'] . ')'
                    ];
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

    public function resep($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
            if (session()->get('role') != 'Dokter') {
                $data = $this->ResepModel
                    ->find($id);
            } else {
                $data = $this->ResepModel
                    ->where('resep.dokter', session()->get('fullname'))
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

                // Prepare data to save
                $data = [
                    'nomor_registrasi' => $nomorRegistrasi,
                    'no_rm' => $patientData['no_rm'],
                    'nama_pasien' => $patientData['nama_pasien'],
                    'alamat' => $patientData['alamat'],
                    'telpon' => $patientData['telpon'],
                    'jenis_kelamin' => $patientData['jenis_kelamin'],
                    'tempat_lahir' => $patientData['tempat_lahir'],
                    'tanggal_lahir' => $patientData['tanggal_lahir'],
                    'dokter' => session()->get('fullname'),
                    'tanggal_resep' => date('Y-m-d H:i:s'),
                    'jumlah_resep' => 0,
                    'total_biaya' => 0,
                    'status' => 0,
                ];

                // Save Data
                $this->ResepModel->save($data);
                return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil ditambahkan']);
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
            if (session()->get('role') != 'Dokter') {
                $resep = $this->ResepModel
                    ->find($id);
            } else {
                $resep = $this->ResepModel
                    ->where('resep.dokter', session()->get('fullname'))
                    ->find($id);
            }
            if (!empty($resep)) {
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Apoteker') {
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

    public function obatlist($id_resep)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $ObatModel = new ObatModel();
            $DetailResepModel = new DetailResepModel(); // Model untuk tabel detail_resep

            // Ambil semua obat berdasarkan id_supplier
            $results = $ObatModel->orderBy('nama_obat', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $ppn = (int) $row['ppn'];
                $mark_up = (int) $row['mark_up'];
                $harga_obat = (int) $row['harga_obat'];

                // Hitung PPN terlebih dahulu
                $jumlah_ppn = ($harga_obat * $ppn) / 100;
                $total_harga_ppn = $harga_obat + $jumlah_ppn;

                // Setelah itu, terapkan mark-up
                $jumlah_mark_up = ($total_harga_ppn * $mark_up) / 100;
                $total_harga = $total_harga_ppn + $jumlah_mark_up;
                $harga_obat_terformat = number_format($total_harga, 0, ',', '.');
                // Cek apakah id_resep sudah ada di tabel detail_resep dengan id_resep yang sama
                $isUsed = $DetailResepModel->where('id_obat', $row['id_obat'])
                    ->where('id_resep', $id_resep) // Pastikan sesuai dengan id_resep yang sedang digunakan
                    ->first();

                // Jika belum ada pada pembelian yang sama, tambahkan ke options
                if (($row['jumlah_masuk'] - $row['jumlah_keluar']) > 0 && !$isUsed) {
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
                'signa' => 'required',
                'catatan' => 'required',
                'cara_pakai' => 'required',
                'jumlah' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();
            $db->transBegin();

            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $this->request->getPost('id_obat'))->get()->getRowArray();

            $ppn = $obat['ppn'];
            $mark_up = $obat['mark_up'];
            $harga_obat = $obat['harga_obat'];

            // Hitung PPN terlebih dahulu
            $jumlah_ppn = ($harga_obat * $ppn) / 100;
            $total_harga_ppn = $harga_obat + $jumlah_ppn;

            // Setelah itu, terapkan mark-up
            $jumlah_mark_up = ($total_harga_ppn * $mark_up) / 100;
            $total_harga = $total_harga_ppn + $jumlah_mark_up;

            // Save Data
            $data = [
                'id_resep' => $id,
                'id_obat' => $this->request->getPost('id_obat'),
                'signa' => $this->request->getPost('signa'),
                'catatan' => $this->request->getPost('catatan'),
                'cara_pakai' => $this->request->getPost('cara_pakai'),
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_satuan' => $total_harga,
            ];
            $this->DetailResepModel->save($data);

            $new_jumlah_keluar = $obat['jumlah_keluar'] + $this->request->getPost('jumlah');
            $builderObat->where('id_obat', $this->request->getPost('id_obat'))->update(['jumlah_keluar' => $new_jumlah_keluar]);

            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
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

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian resep', 'errors' => NULL]);
            } else {
                $db->transCommit();
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
                'signa_edit' => 'required',
                'catatan_edit' => 'required',
                'cara_pakai_edit' => 'required',
                'jumlah_edit' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();
            $db->transBegin();

            $detail_resep = $this->DetailResepModel->find($this->request->getPost('id_detail_resep'));
            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $detail_resep['id_obat'])->get()->getRowArray();

            // Save Data
            $data = [
                'id_detail_resep' => $this->request->getPost('id_detail_resep'),
                'id_resep' => $id,
                'id_obat' => $detail_resep['id_obat'],
                'signa' => $this->request->getPost('signa_edit'),
                'catatan' => $this->request->getPost('catatan_edit'),
                'cara_pakai' => $this->request->getPost('cara_pakai_edit'),
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'harga_satuan' => $detail_resep['harga_satuan'],
            ];
            $this->DetailResepModel->save($data);

            $new_jumlah_keluar = $obat['jumlah_keluar'] - $detail_resep['jumlah']  + $this->request->getPost('jumlah_edit');
            $builderObat->where('id_obat', $detail_resep['id_obat'])->update(['jumlah_keluar' => $new_jumlah_keluar]);

            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
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

            // Update detail_transaksi with new harga_transaksi
            $harga_transaksi = $detail_resep['jumlah'] * $detail_resep['harga_satuan'];

            $detailTransaksiBuilder = $db->table('detail_transaksi');
            $detailTransaksiBuilder->where('id_resep', $id);
            $detailTransaksiBuilder->update([
                'harga_transaksi' => $harga_transaksi
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian resep', 'errors' => NULL]);
            } else {
                $db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil diperbarui']);
            }
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

    public function etiketdalam($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $resep = $this->ResepModel
                ->where('status', 0)
                ->find($id);
            $detail_resep = $this->DetailResepModel
                ->where('detail_resep.id_resep', $id)
                ->groupStart()
                ->where('obat.bentuk_obat', 'Tablet/Kapsul')
                ->orWhere('obat.bentuk_obat', 'Sirup')
                ->groupEnd()
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'obat.id_obat = detail_resep.id_obat', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->findAll();
            // dd($detail_resep);
            // die;
            if (!empty($detail_resep) && $resep['status'] == 0) {
                // dd($total_obatalkes);
                // die;
                $data = [
                    'resep' => $resep,
                    'detail_resep' => $detail_resep,
                    'title' => 'Etiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/resep/etiket', $data);
                // die;
                $dompdf = new Dompdf();
                $html = view('dashboard/resep/etiket', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('resep-obat-dalam-id-' . $resep['nomor_registrasi'] . '-' . urlencode($resep['nama_pasien']) . '-' . urlencode($resep['dokter']) . '-' . $resep['tanggal_resep'] . '.pdf', [
                    'Attachment' => FALSE
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function etiketluar($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $resep = $this->ResepModel
                ->where('status', 0)
                ->find($id);
            $detail_resep = $this->DetailResepModel
                ->where('detail_resep.id_resep', $id)
                ->groupStart()
                ->where('obat.bentuk_obat', 'Tetes')
                ->orWhere('obat.bentuk_obat', 'Salep')
                ->groupEnd()
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'obat.id_obat = detail_resep.id_obat', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->findAll();
            // dd($detail_resep);
            // die;
            if (!empty($detail_resep) && $resep['status'] == 0) {
                // dd($total_obatalkes);
                // die;
                $data = [
                    'resep' => $resep,
                    'detail_resep' => $detail_resep,
                    'title' => 'Etiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/resep/etiket', $data);
                // die;
                $dompdf = new Dompdf();
                $html = view('dashboard/resep/etiket', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('resep-obat-luar-id-' . $resep['nomor_registrasi'] . '-' . urlencode($resep['nama_pasien']) . '-' . urlencode($resep['dokter']) . '-' . $resep['tanggal_resep'] . '.pdf', [
                    'Attachment' => FALSE
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
