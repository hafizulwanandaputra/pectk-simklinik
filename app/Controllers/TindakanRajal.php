<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\TindakanRajalModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;

use Picqer\Barcode\BarcodeGeneratorPNG;

class TindakanRajal extends BaseController
{
    protected $RawatJalanModel;
    protected $TindakanRajalModel;
    protected $AuthModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->TindakanRajalModel = new TindakanRajalModel();
        $this->AuthModel = new AuthModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }


            // Memeriksa apakah lp_tindakan_rajal sudah ada
            $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$laporanrajal) {
                // Jika lp_tindakan_rajal tidak ditemukan, buat lp_tindakan_rajal baru dengan query builder
                $db->table('medrec_lp_tindakan_rajal')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                $this->notify_clients('update');

                // Setelah lp_tindakan_rajal dibuat, ambil kembali data lp_tindakan_rajal menggunakan query builder
                $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getRowArray();
            }

            // Inisialisasi rawat jalan
            $nama_perawat = $this->AuthModel
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->findAll();

            // Inisialisasi rawat jalan
            $nama_perawat_count = $this->AuthModel
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->countAllResults();

            // Query untuk item sebelumnya
            $previous = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan <', $id) // Kondisi untuk id sebelumnya
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan >', $id) // Kondisi untuk id berikutnya
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $rawatjalan['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'laporanrajal' => $laporanrajal,
                'nama_perawat' => $nama_perawat,
                'nama_perawat_count' => $nama_perawat_count,
                'title' => 'Tindakan Rawat Jalan ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Tindakan Rawat Jalan',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman tindakan rawat jalan
            return view('dashboard/rawatjalan/laporanrajal/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->TindakanRajalModel->find($id); // Mengambil skrining
            $data['nama_perawat'] = explode(';', $data['nama_perawat']); // Ubah CSV menjadi array
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icdx()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Mendapatkan parameter pencarian dari permintaan GET
            $search = $this->request->getGet('search');
            $offset = (int) $this->request->getGet('offset') ?? 0; // Default 0 jika tidak ada
            $limit = (int) $this->request->getGet('limit') ?? 50; // Default 50 jika tidak ada

            // Jika parameter pencarian kosong, kembalikan data kosong
            if (empty($search)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [] // Data kosong
                ]);
            }

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_x');
            $builder->select('icdKode, icdNamaInggris');

            // Menambahkan filter pencarian
            $builder
                ->like('icdKode', $search)
                ->orLike('icdNamaInggris', $search);

            // Menambahkan limit dan offset untuk lazy loading
            $builder->limit($limit, $offset);

            $result = $builder->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah lp_tindakan_rajal sudah ada
            $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            $laporanrajal['nama_perawat'] = str_replace(';', ', ', $laporanrajal['nama_perawat']);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($laporanrajal) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'laporanrajal' => $laporanrajal,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Laporan Tindakan Rawat Jalan ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/laporanrajal/form', $data);
                // die;
                $client = \Config\Services::curlrequest();
                $html = view('dashboard/rawatjalan/laporanrajal/form', $data);
                $filename = 'output-laporanrajal.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'format' => 'A4',
                                'margin' => [
                                    'top' => '1cm',
                                    'right' => '1cm',
                                    'bottom' => '1cm',
                                    'left' => '1cm'
                                ]
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['success']) && $result['success']) {
                        $path = WRITEPATH . 'temp/' . $result['file'];

                        if (!is_file($path)) {
                            return $this->response
                                ->setStatusCode(500)
                                ->setBody("PDF berhasil dibuat tapi file tidak ditemukan: $path");
                        }

                        return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                            ->setBody(file_get_contents($path));
                    } else {
                        $errorMessage = $result['error'] ?? 'Tidak diketahui';
                        return $this->response
                            ->setStatusCode(500)
                            ->setBody("Gagal membuat PDF: " . esc($errorMessage));
                    }
                } catch (\Exception $e) {
                    return $this->response
                        ->setStatusCode(500)
                        ->setBody("Kesalahan saat request ke PDF worker: " . esc($e->getMessage()));
                }
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'nama_perawat' => 'required',
                'lokasi_mata' => 'required',
                'isi_laporan' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $laporanrajal = $this->TindakanRajalModel->find($id);

            // Proses data perawat dari select multiple
            $perawat = $this->request->getPost('nama_perawat');
            $perawat_csv = is_array($perawat) ? implode(';', $perawat) : NULL;

            // Simpan data laporanrajal
            $data = [
                'id_lp_tindakan_rajal' => $id,
                'no_rm' => $laporanrajal['no_rm'],
                'nomor_registrasi' => $laporanrajal['nomor_registrasi'],
                'nama_perawat' => $perawat_csv,
                'diagnosa' => $this->request->getPost('diagnosa') ?: NULL,
                'kode_icd_x' => $this->request->getPost('kode_icd_x') ?: NULL,
                'lokasi_mata' => $this->request->getPost('lokasi_mata') ?: NULL,
                'isi_laporan' => $this->request->getPost('isi_laporan') ?: NULL,
                'waktu_dibuat' => $laporanrajal['waktu_dibuat'],
            ];
            $this->TindakanRajalModel->save($data);
            $this->notify_clients_submit('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Tindakan rawat jalan berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function notify_clients($action)
    {
        if (!in_array($action, ['update', 'delete'])) {
            return $this->response->setJSON([
                'status' => 'Invalid action',
                'error' => 'Action must be either "update" or "delete"'
            ])->setStatusCode(400);
        }

        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => ['action' => $action]
        ]);
    }

    public function notify_clients_submit($action)
    {
        if (!in_array($action, ['update', 'delete'])) {
            return $this->response->setJSON([
                'status' => 'Invalid action',
                'error' => 'Action must be either "update" or "delete"'
            ])->setStatusCode(400);
        }

        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => ['action' => $action]
        ]);

        return $this->response->setJSON([
            'status' => ucfirst($action) . ' notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
