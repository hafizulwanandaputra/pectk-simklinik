<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\SakitMataModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SakitMata extends BaseController
{
    protected $RawatJalanModel;
    protected $SakitMataModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->SakitMataModel = new SakitMataModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Surat Keterangan Sakit Mata - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Sakit Mata',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/sakitmata/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function sakitmatalist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $biasa = $this->request->getGet('biasa');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $SakitMataModel = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter biasa jika ada
            if ($biasa === '1') {
                $SakitMataModel->where('biasa', 1); // Surat Keterangan Biasa
            } elseif ($biasa === '0') {
                $SakitMataModel->where('biasa', 0); // Surat Keterangan Sakit
            }

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $SakitMataModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $SakitMataModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $SakitMataModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $SakitMata = $SakitMataModel
                ->orderBy('id_keterangan_sakit_mata', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataSakitMata = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $SakitMata, array_keys($SakitMata));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'sakitmata' => $dataSakitMata,
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

            // LEFT JOIN ke medrec_keterangan_sakit_mata
            $builder->join('medrec_keterangan_sakit_mata', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'left');

            // Hanya ambil data yang belum ada di medrec_keterangan_sakit_mata
            $builder->where('medrec_keterangan_sakit_mata.nomor_registrasi IS NULL');

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

            // Ambil data rawat_jalan yang belum ada di medrec_keterangan_sakit_mata
            $builder = $db->table('rawat_jalan');
            $builder->select('rawat_jalan.nomor_registrasi, rawat_jalan.no_rm');
            $builder->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');
            $builder->join('medrec_keterangan_sakit_mata', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'left');
            $builder->where('rawat_jalan.nomor_registrasi', $nomorRegistrasi);
            $builder->where('rawat_jalan.status', 'DAFTAR');
            $builder->where('medrec_keterangan_sakit_mata.nomor_registrasi IS NULL');
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
            $db->table('medrec_keterangan_sakit_mata')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan sakit mata berhasil ditambahkan']); // Mengembalikan respon sukses
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

        // Inisialisasi rawat jalan
        $sakitmata = $this->SakitMataModel
            ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
            ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
            ->find($id);

        if (!$sakitmata) {
            throw PageNotFoundException::forPageNotFound();
        }

        // === Generate Barcode ===
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $bcNoReg = base64_encode($barcodeGenerator->getBarcode($sakitmata['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

        // Menyiapkan data untuk tampilan
        $data = [
            'sakitmata' => $sakitmata,
            'bcNoReg' => $bcNoReg,
            'title' => 'Surat Keterangan Sakit Mata ' . $sakitmata['nama_pasien'] . ' (' . $sakitmata['no_rm'] . ') - ' . $sakitmata['nomor_registrasi'] . ' - ' . $this->systemName,
            'headertitle' => 'Surat Keterangan Sakit Mata',
            'agent' => $this->request->getUserAgent(),
        ];

        // Tentukan tampilan berdasarkan parameter side
        $viewFile = ($side === 'left') ? 'dashboard/sakitmata/form-left' : 'dashboard/sakitmata/form-right';

        $client = \Config\Services::curlrequest();
        $html = view($viewFile, $data);
        $filename = 'output-sakitmata.pdf';

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
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $sakitmata = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if ($sakitmata) {
                $db = db_connect();

                // Menghapus sakitmata
                $this->SakitMataModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_keterangan_sakit_mata` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Surat keterangan sakit mata berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Surat keterangan sakit mata tidak ditemukan', // Pesan jika peran tidak valid
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

            $sakitmata = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata <', $id)
                ->orderBy('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata >', $id)
                ->orderBy('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $sakitmata['no_rm'])
                ->orderBy('id_keterangan_sakit_mata', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'sakitmata' => $sakitmata,
                'title' => 'Surat Keterangan Sakit Mata ' . $sakitmata['nama_pasien'] . ' (' . $sakitmata['no_rm'] . ') - ' . $sakitmata['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Sakit Mata',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/sakitmata/details', $data);
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
            $data = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
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
                'keterangan' => [
                    'rules' => 'required',
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'biasa' => $this->request->getPost('biasa'),
                'keterangan' => $this->request->getPost('keterangan') ?: null
            ];
            $db->table('medrec_keterangan_sakit_mata')->where('id_keterangan_sakit_mata', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan sakit mata berhasil diperbarui']); // Mengembalikan respon sukses
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
