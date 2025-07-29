<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\LPOperasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class LPOperasi extends BaseController
{
    protected $RawatJalanModel;
    protected $LPOperasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->LPOperasiModel = new LPOperasiModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Laporan Operasi - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasi/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function lpoperasilist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $LPOperasiModel = $this->LPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $LPOperasiModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $LPOperasiModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $LPOperasiModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $LpOperasiPterigium = $LPOperasiModel
                ->orderBy('id_lp_operasi', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataLpOperasiPterigium = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $LpOperasiPterigium, array_keys($LpOperasiPterigium));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'lp_operasi' => $dataLpOperasiPterigium,
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
            $today = date('Y-m-d');

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

            // Mengambil nomor_registrasi yang sudah terpakai di rawat_jalan
            $db = \Config\Database::connect();
            $usedNoRegInit = $db->table('medrec_lp_operasi')->select('nomor_registrasi')->get()->getResultArray();
            $usedNoReg = array_column($usedNoRegInit, 'nomor_registrasi');

            $options = [];
            // Menyusun opsi dari data rawat jalan yang diterima
            foreach ($data as $row) {
                // Memeriksa apakah nomor_registrasi ada dalam daftar nomor_registrasi yang terpakai
                if (in_array($row['nomor_registrasi'], $usedNoReg)) {
                    continue; // Lewati rawat jalan yang sudah terpakai
                }

                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $row['nomor_registrasi'], // Nilai untuk opsi
                    'text' => $row['nama_pasien'] . ' (' . $row['nomor_registrasi'] . ' - ' . $row['no_rm'] . ' - ' . $row['tanggal_lahir'] . ')' // Teks untuk opsi
                ];
            }

            // Mengembalikan data rawat jalan dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data' => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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

            $today = date('Y-m-d');
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $LPOperasiData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $LPOperasiData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$LPOperasiData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $LPOperasiData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_lp_operasi')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi berhasil ditambahkan']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Inisialisasi rawat jalan
            $lp_operasi = $this->LPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($lp_operasi['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($lp_operasi) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'lp_operasi' => $lp_operasi,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Laporan Operasi ' . $lp_operasi['nama_pasien'] . ' (' . $lp_operasi['no_rm'] . ') - ' . $lp_operasi['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Laporan Operasi',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/lpoperasi/form', $data);
                // die;
                $client = \Config\Services::curlrequest();
                $html = view('dashboard/lpoperasi/form', $data);
                $filename = 'output-lp-operasi.pdf';

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

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $lp_operasi = $this->LPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            $tanggal = date('Y-m-d', strtotime($lp_operasi['tanggal_registrasi']));
            $today = date('Y-m-d');
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
            if ($tanggal < $hari_yang_lalu || $tanggal > $today) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Pasien operasi yang didaftarkan lebih dari 14 hari tidak dapat dihapus']);
            }
            if ($lp_operasi) {
                $db = db_connect();

                // Menghapus lp_operasi
                $this->LPOperasiModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_lp_operasi` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Laporan operasi berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Laporan operasi tidak ditemukan', // Pesan jika peran tidak valid
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            $lp_operasi = $this->LPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            $asisten = $db->table('user')
                ->groupStart()
                ->where('role', 'Dokter')
                ->orWhere('role', 'Perawat')
                ->groupEnd()
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_lp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi.id_lp_operasi <', $id)
                ->orderBy('medrec_lp_operasi.id_lp_operasi', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_lp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi.id_lp_operasi >', $id)
                ->orderBy('medrec_lp_operasi.id_lp_operasi', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_lp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $lp_operasi['no_rm'])
                ->orderBy('id_lp_operasi', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'lp_operasi' => $lp_operasi,
                'dokter' => $dokter,
                'asisten' => $asisten,
                'title' => 'Laporan Operasi ' . $lp_operasi['nama_pasien'] . ' (' . $lp_operasi['no_rm'] . ') - ' . $lp_operasi['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasi/details', $data);
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
            $data = $this->LPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi.nomor_registrasi', 'inner')
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Melakukan validasi
            $validation = \Config\Services::validation();

            // Menetapkan aturan validasi dasar
            $rules = [
                'dokter_bedah' => [
                    'rules' => 'required',
                ],
                'asisten_dokter_bedah' => [
                    'rules' => 'required',
                ],
                'dokter_anastesi' => [
                    'rules' => 'required',
                ],
                'jenis_anastesi' => [
                    'rules' => 'required',
                ],
                'jenis_operasi' => [
                    'rules' => 'required',
                ],
                'pemeriksaan_pa' => [
                    'rules' => 'required',
                ],
                'tanggal_operasi' => [
                    'rules' => 'required',
                ],
                'jam_operasi' => [
                    'rules' => 'required',
                ],
                'lama_operasi' => [
                    'rules' => 'required',
                ],
                'laporan_operasi' => [
                    'rules' => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'dokter_bedah' => $this->request->getPost('dokter_bedah') ?: null,
                'asisten_dokter_bedah' => $this->request->getPost('asisten_dokter_bedah') ?: null,
                'dokter_anastesi' => $this->request->getPost('dokter_anastesi') ?: null,
                'jenis_anastesi' => $this->request->getPost('jenis_anastesi') ?: null,
                'jenis_operasi' => $this->request->getPost('jenis_operasi') ?: null,
                'diagnosis_pra_bedah' => $this->request->getPost('diagnosis_pra_bedah') ?: null,
                'diagnosis_pasca_bedah' => $this->request->getPost('diagnosis_pasca_bedah') ?: null,
                'indikasi_operasi' => $this->request->getPost('indikasi_operasi') ?: null,
                'nama_operasi' => $this->request->getPost('nama_operasi') ?: null,
                'jaringan_eksisi' => $this->request->getPost('jaringan_eksisi') ?: null,
                'pemeriksaan_pa' => $this->request->getPost('pemeriksaan_pa') ?: null,
                'tanggal_operasi' => $this->request->getPost('tanggal_operasi') ?: null,
                'jam_operasi' => $this->request->getPost('jam_operasi') ?: null,
                'lama_operasi' => $this->request->getPost('lama_operasi') ?: null,
                'laporan_operasi' => $this->request->getPost('laporan_operasi') ?: null,
            ];
            $db->table('medrec_lp_operasi')->where('id_lp_operasi', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi berhasil diperbarui']); // Mengembalikan respon sukses
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
