<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\IstirahatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;
use NumberToWords\NumberToWords;

class Istirahat extends BaseController
{
    protected $RawatJalanModel;
    protected $IstirahatModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->IstirahatModel = new IstirahatModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Surat Keterangan Istirahat - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Istirahat',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/istirahat/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function istirahatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $IstirahatModel = $this->IstirahatModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $IstirahatModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $IstirahatModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $IstirahatModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $Istirahat = $IstirahatModel
                ->orderBy('id_keterangan_istirahat', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataIstirahat = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $Istirahat, array_keys($Istirahat));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'istirahat' => $dataIstirahat,
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
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

            // LEFT JOIN ke medrec_keterangan_istirahat
            $builder->join('medrec_keterangan_istirahat', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'left');

            // Hanya ambil data yang belum ada di medrec_keterangan_istirahat
            $builder->where('medrec_keterangan_istirahat.nomor_registrasi IS NULL');

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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
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

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('status', 'DAFTAR')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $IstirahatData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $IstirahatData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$IstirahatData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $IstirahatData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_keterangan_istirahat')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan istirahat berhasil ditambahkan']); // Mengembalikan respon sukses
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

        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (!in_array(session()->get('role'), ['Admin', 'Dokter', 'Perawat', 'Admisi'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Memeriksa validitas parameter side
        if (!in_array($side, ['left', 'right'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $db = db_connect();

        // Inisialisasi rawat jalan
        $istirahat = $this->IstirahatModel
            ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
            ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
            ->find($id);

        if (!$istirahat) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Ambil tabel master_provinsi
        $provinsi = $db->table('master_provinsi');
        $provinsi->select('UPPER(provinsiNama) AS provinsiNama');
        $provinsi->where('provinsiId', $istirahat['provinsi']);

        // Query untuk mendapatkan nama provinsi
        $res_provinsi = $provinsi->get()->getRow();

        if ($res_provinsi) {
            // Ubah ID menjadi nama provinsi
            $istirahat['provinsi'] = $res_provinsi->provinsiNama;
        }

        // Ambil tabel master_kabupaten
        $kabupaten = $db->table('master_kabupaten');
        $kabupaten->select('UPPER(kabupatenNama) AS kabupatenNama');
        $kabupaten->where('kabupatenId', $istirahat['kabupaten']);

        // Query untuk mendapatkan nama kabupaten
        $res_kabupaten = $kabupaten->get()->getRow();

        if ($res_kabupaten) {
            // Ubah ID menjadi nama kabupaten
            $istirahat['kabupaten'] = $res_kabupaten->kabupatenNama;
        }

        // Ambil tabel master_kecamatan
        $kecamatan = $db->table('master_kecamatan');
        $kecamatan->select('UPPER(kecamatanNama) AS kecamatanNama');
        $kecamatan->where('kecamatanId', $istirahat['kecamatan']);

        // Query untuk mendapatkan nama kecamatan
        $res_kecamatan = $kecamatan->get()->getRow();

        if ($res_kecamatan) {
            // Ubah ID menjadi nama kecamatan
            $istirahat['kecamatan'] = $res_kecamatan->kecamatanNama;
        }

        // Ambil tabel master_kelurahan
        $kelurahan = $db->table('master_kelurahan');
        $kelurahan->select('UPPER(kelurahanNama) AS kelurahanNama');
        $kelurahan->where('kelurahanId', $istirahat['kelurahan']);

        // Query untuk mendapatkan nama kelurahan
        $res_kelurahan = $kelurahan->get()->getRow();

        if ($res_kelurahan) {
            // Ubah ID menjadi nama kelurahan
            $istirahat['kelurahan'] = $res_kelurahan->kelurahanNama;
        }

        // === Generate Barcode ===
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $bcNoReg = base64_encode($barcodeGenerator->getBarcode($istirahat['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

        $dateTime2 = new DateTime($istirahat['tanggal_mulai']);
        $dateTime3 = new DateTime($istirahat['tanggal_selesai']);
        $durasi_istirahat = $dateTime3->diff($dateTime2)->d + 1;

        $numberToWords = new NumberToWords();
        $converter = $numberToWords->getNumberTransformer('id'); // 'id' untuk Bahasa Indonesia

        // Konversi durasi istirahat ke teks
        $istirahat['durasi_teks'] = $converter->toWords($durasi_istirahat);

        // Menyiapkan data untuk tampilan
        $data = [
            'istirahat' => $istirahat,
            'durasi_istirahat' => $durasi_istirahat,
            'bcNoReg' => $bcNoReg,
            'title' => 'Surat Keterangan Istirahat ' . $istirahat['nama_pasien'] . ' (' . $istirahat['no_rm'] . ') - ' . $istirahat['nomor_registrasi'] . ' - ' . $this->systemName,
            'headertitle' => 'Surat Keterangan Istirahat',
            'agent' => $this->request->getUserAgent(),
        ];

        // Tentukan tampilan berdasarkan parameter side
        $viewFile = ($side === 'left') ? 'dashboard/istirahat/form-left' : 'dashboard/istirahat/form-right';

        $client = new Client(); // pakai Guzzle langsung
        $html = view($viewFile, $data);
        $filename = 'output-istirahat.pdf';

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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $istirahat = $this->IstirahatModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            if ($istirahat) {
                $db = db_connect();

                // Menghapus istirahat
                $this->IstirahatModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_keterangan_istirahat` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Surat keterangan istirahat berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Surat keterangan istirahat tidak ditemukan', // Pesan jika peran tidak valid
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            $istirahat = $this->IstirahatModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_keterangan_istirahat')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_istirahat.id_keterangan_istirahat <', $id)
                ->orderBy('medrec_keterangan_istirahat.id_keterangan_istirahat', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_keterangan_istirahat')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_istirahat.id_keterangan_istirahat >', $id)
                ->orderBy('medrec_keterangan_istirahat.id_keterangan_istirahat', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_keterangan_istirahat')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $istirahat['no_rm'])
                ->orderBy('id_keterangan_istirahat', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'istirahat' => $istirahat,
                'title' => 'Surat Keterangan Istirahat ' . $istirahat['nama_pasien'] . ' (' . $istirahat['no_rm'] . ') - ' . $istirahat['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Istirahat',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/istirahat/details', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->IstirahatModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_istirahat.nomor_registrasi', 'inner')
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();
            // Melakukan validasi
            $validation = \Config\Services::validation();

            // Menetapkan aturan validasi dasar
            $rules = [
                'tanggal_mulai' => [
                    'rules' => 'required',
                ],
                'tanggal_selesai' => [
                    'rules' => 'required',
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'diagnosis' => $this->request->getPost('diagnosis') ?: null,
                'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: null,
                'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null
            ];
            $db->table('medrec_keterangan_istirahat')->where('id_keterangan_istirahat', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan istirahat berhasil diperbarui']); // Mengembalikan respon sukses
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
