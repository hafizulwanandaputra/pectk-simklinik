<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\RujukanModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Rujukan extends BaseController
{
    protected $RawatJalanModel;
    protected $RujukanModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->RujukanModel = new RujukanModel();
    }
    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi' || session()->get('role') == 'Manajer') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Surat Rujukan - ' . $this->systemName,
                'headertitle' => 'Surat Rujukan',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/rujukan/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function rujukanlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi' || session()->get('role') == 'Manajer') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $RujukanModel = $this->RujukanModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $RujukanModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $RujukanModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $RujukanModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $Rujukan = $RujukanModel
                ->orderBy('id_rujukan', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataRujukan = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $Rujukan, array_keys($Rujukan));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'rujukan' => $dataRujukan,
                'total' => $total
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function pasienlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi' || session()->get('role') == 'Manajer') {
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

            $db = db_connect();
            $builder = $db->table('rawat_jalan');
            $builder->select([
                'rawat_jalan.nomor_registrasi',
                'pasien.nama_pasien',
                'rawat_jalan.tanggal_registrasi',
                'pasien.no_rm',
                'pasien.tanggal_lahir'
            ]);
            $builder->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');

            // LEFT JOIN ke medrec_rujukan
            $builder->join('medrec_rujukan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'left');

            // Hanya ambil data yang belum ada di medrec_rujukan
            $builder->where('medrec_rujukan.nomor_registrasi IS NULL');

            // Tambahkan filter pencarian
            $builder->groupStart()
                ->like('pasien.nama_pasien', $search)
                ->orLike('rawat_jalan.tanggal_registrasi', $search)
                ->orLike('pasien.no_rm', $search)
                ->orLike('pasien.tanggal_lahir', $search)
                ->groupEnd();

            // Filter status DAFTAR
            $builder->where('rawat_jalan.status', 'DAFTAR');

            // Sorting dan limit
            $builder->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->limit($limit, $offset);

            $result = $builder->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Manajer') {
            $db = db_connect();
            // Melakukan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nomor_registrasi' => 'required', // Nomor registrasi wajib diisi
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]); // Mengembalikan kesalahan validasi
            }

            // Mengambil nomor registrasi dari permintaan POST
            $nomorRegistrasi = $this->request->getPost('nomor_registrasi');

            // Ambil data rawat_jalan yang belum ada di medrec_rujukan
            $builder = $db->table('rawat_jalan');
            $builder->select('rawat_jalan.nomor_registrasi, rawat_jalan.no_rm');
            $builder->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');
            $builder->join('medrec_rujukan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'left');
            $builder->where('rawat_jalan.nomor_registrasi', $nomorRegistrasi);
            $builder->where('rawat_jalan.status', 'DAFTAR');
            $builder->where('medrec_rujukan.nomor_registrasi IS NULL');
            $patient = $builder->get()->getRowArray();

            if (!$patient) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Data rawat jalan tidak ditemukan atau sudah pernah dibuat sebelumnya',
                    'errors' => null
                ]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $patient['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_rujukan')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat rujukan berhasil ditambahkan']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function export($id)
    {
        // Ambil parameter 'side' dari URL
        $side = $this->request->getGet('side');

        if (!in_array(session()->get('role'), ['Admin', 'Dokter', 'Admisi'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Memeriksa validitas parameter side
        if (!in_array($side, ['left', 'right'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $db = db_connect();

        // Inisialisasi rawat jalan
        $rujukan = $this->RujukanModel
            ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
            ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
            ->find($id);

        if (!$rujukan) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Ambil tabel master_provinsi
        $provinsi = $db->table('master_provinsi');
        $provinsi->select('UPPER(provinsiNama) AS provinsiNama');
        $provinsi->where('provinsiId', $rujukan['provinsi']);

        // Query untuk mendapatkan nama provinsi
        $res_provinsi = $provinsi->get()->getRow();

        if ($res_provinsi) {
            // Ubah ID menjadi nama provinsi
            $rujukan['provinsi'] = $res_provinsi->provinsiNama;
        }

        // Ambil tabel master_kabupaten
        $kabupaten = $db->table('master_kabupaten');
        $kabupaten->select('UPPER(kabupatenNama) AS kabupatenNama');
        $kabupaten->where('kabupatenId', $rujukan['kabupaten']);

        // Query untuk mendapatkan nama kabupaten
        $res_kabupaten = $kabupaten->get()->getRow();

        if ($res_kabupaten) {
            // Ubah ID menjadi nama kabupaten
            $rujukan['kabupaten'] = $res_kabupaten->kabupatenNama;
        }

        // Ambil tabel master_kecamatan
        $kecamatan = $db->table('master_kecamatan');
        $kecamatan->select('UPPER(kecamatanNama) AS kecamatanNama');
        $kecamatan->where('kecamatanId', $rujukan['kecamatan']);

        // Query untuk mendapatkan nama kecamatan
        $res_kecamatan = $kecamatan->get()->getRow();

        if ($res_kecamatan) {
            // Ubah ID menjadi nama kecamatan
            $rujukan['kecamatan'] = $res_kecamatan->kecamatanNama;
        }

        // Ambil tabel master_kelurahan
        $kelurahan = $db->table('master_kelurahan');
        $kelurahan->select('UPPER(kelurahanNama) AS kelurahanNama');
        $kelurahan->where('kelurahanId', $rujukan['kelurahan']);

        // Query untuk mendapatkan nama kelurahan
        $res_kelurahan = $kelurahan->get()->getRow();

        if ($res_kelurahan) {
            // Ubah ID menjadi nama kelurahan
            $rujukan['kelurahan'] = $res_kelurahan->kelurahanNama;
        }

        // === Generate Barcode ===
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rujukan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

        // Menyiapkan data untuk tampilan
        $data = [
            'rujukan' => $rujukan,
            'bcNoReg' => $bcNoReg,
            'title' => 'Surat Rujukan ' . $rujukan['nama_pasien'] . ' (' . $rujukan['no_rm'] . ') - ' . $rujukan['nomor_registrasi'] . ' - ' . $this->systemName,
            'headertitle' => 'Surat Rujukan',
            'agent' => $this->request->getUserAgent(),
        ];

        // Tentukan tampilan berdasarkan parameter side
        $viewFile = ($side === 'left') ? 'dashboard/rujukan/form-left' : 'dashboard/rujukan/form-right';

        $client = new Client(); // pakai Guzzle langsung
        $html = view($viewFile, $data);
        $filename = 'output-rujukan.pdf';

        try {
            $response = $client->post(env('PDF-URL'), [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'html' => $html,
                    'filename' => $filename,
                    'paper' => [
                        'format' => 'A4',
                        'landscape' => true,
                        'margin' => [
                            'top' => '0cm',
                            'right' => '0cm',
                            'bottom' => '0cm',
                            'left' => '0cm'
                        ]
                    ]
                ]
            ]);

            $rawBody = $response->getBody()->getContents();
            $result = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->response
                    ->setStatusCode($response->getStatusCode())
                    ->setBody("Gagal membuat PDF. Respons worker:\n\n" . esc($rawBody));
            }

            if (!empty($result['success']) && $result['success']) {
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
                $errorDetails = $result['details'] ?? '';

                return $this->response
                    ->setStatusCode(500)
                    ->setBody(
                        "Gagal membuat PDF: " . esc($errorMessage) .
                            (!empty($errorDetails) ? "\n\nDetail:\n" . esc($errorDetails) : '')
                    );
            }
        } catch (RequestException $e) {
            // Ambil pesan default
            $errorMessage = "Kesalahan saat request ke PDF worker: " . $e->getMessage();

            // Kalau ada response dari worker
            if ($e->hasResponse()) {
                $errorBody = (string) $e->getResponse()->getBody();

                $json = json_decode($errorBody, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($json['error'])) {
                    $errorMessage .= "\n\nPesan dari worker: " . esc($json['error']);
                    if (!empty($json['details'])) {
                        $errorMessage .= "\n\nDetail:\n" . esc($json['details']);
                    }
                } else {
                    $errorMessage .= "\n\nRespons worker:\n" . esc($errorBody);
                }
            }

            return $this->response
                ->setStatusCode(500)
                ->setBody($errorMessage);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Manajer') {
            $rujukan = $this->RujukanModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            if ($rujukan) {
                $db = db_connect();

                // Menghapus rujukan
                $this->RujukanModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_rujukan` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Surat rujukan berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Surat rujukan tidak ditemukan', // Pesan jika peran tidak valid
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function details($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Manajer') {
            $db = db_connect();

            $rujukan = $this->RujukanModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_rujukan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_rujukan.id_rujukan <', $id)
                ->orderBy('medrec_rujukan.id_rujukan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_rujukan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_rujukan.id_rujukan >', $id)
                ->orderBy('medrec_rujukan.id_rujukan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_rujukan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $rujukan['no_rm'])
                ->orderBy('id_rujukan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rujukan' => $rujukan,
                'title' => 'Surat Rujukan ' . $rujukan['nama_pasien'] . ' (' . $rujukan['no_rm'] . ') - ' . $rujukan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Rujukan',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/rujukan/details', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Manajer') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->RujukanModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_rujukan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Manajer') {
            $db = db_connect();
            // Melakukan validasi
            $validation = \Config\Services::validation();

            // Menetapkan aturan validasi dasar
            $rules = [
                'dokter_rujukan' => [
                    'rules' => 'required',
                ],
                'alamat_dokter_rujukan' => [
                    'rules' => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'diagnosis' => $this->request->getPost('diagnosis') ?: null,
                'diagnosis_diferensial' => $this->request->getPost('diagnosis_diferensial') ?: null,
                'terapi' => $this->request->getPost('terapi') ?: null,
                'dokter_rujukan' => $this->request->getPost('dokter_rujukan') ?: null,
                'alamat_dokter_rujukan' => $this->request->getPost('alamat_dokter_rujukan') ?: null,
            ];
            $db->table('medrec_rujukan')->where('id_rujukan', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat rujukan berhasil diperbarui']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
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

        return $this->response->setJSON([
            'status' => ucfirst($action) . ' notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
