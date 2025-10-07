<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\EdukasiModel;
use App\Models\EdukasiEvaluasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Edukasi extends BaseController
{
    protected $RawatJalanModel;
    protected $EdukasiModel;
    protected $EdukasiEvaluasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->EdukasiModel = new EdukasiModel();
        $this->EdukasiEvaluasiModel = new EdukasiEvaluasiModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah edukasi sudah ada
            $edukasi = $db->table('medrec_edukasi')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$edukasi) {
                // Jika edukasi tidak ditemukan, buat edukasi baru dengan query builder
                $db->table('medrec_edukasi')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                $this->notify_clients('update');

                // Setelah edukasi dibuat, ambil kembali data edukasi menggunakan query builder
                $edukasi = $db->table('medrec_edukasi')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getRowArray();
            }

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

            // Query untuk opsi pendidikan
            $pendidikan = $db->table('master_pendidikan')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'edukasi' => $edukasi,
                'title' => 'Edukasi ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Edukasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan,
                'pendidikan' => $pendidikan,
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/edukasi/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->EdukasiModel->find($id); // Mengambil skrining
            $data['hambatan'] = explode(',', $data['hambatan']); // Ubah CSV menjadi array
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah edukasi sudah ada
            $edukasi = $db->table('medrec_edukasi')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // Memeriksa apakah pasien tidak kosong
            if ($edukasi) {
                // Ambil tabel master_pendidikan
                $pendidikan = $db->table('master_pendidikan');
                $pendidikan->select('keterangan');
                $pendidikan->where('pendidikan', $edukasi['pendidikan']);

                // Query untuk mendapatkan nama pendidikan
                $res_pendidikan = $pendidikan->get()->getRow();

                if ($res_pendidikan) {
                    // Ubah pendidikan menjadi keterangan
                    $edukasi['pendidikan'] = $res_pendidikan->keterangan;
                }

                $edukasi['hambatan'] = str_replace(',', ', ', $edukasi['hambatan']);

                // Memeriksa apakah evaluasi edukasi sudah ada
                $edukasi_evaluasi = $db->table('medrec_edukasi_evaluasi')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getResultArray();

                // === Generate Barcode ===
                $barcodeGenerator = new BarcodeGeneratorPNG();
                $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'edukasi' => $edukasi,
                    'edukasi_evaluasi' => $edukasi_evaluasi,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Edukasi ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/edukasi/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/rawatjalan/edukasi/form', $data);
                $filename = 'output-edukasi.pdf';

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            $bahasa = $this->request->getPost('bahasa');
            $keyakinan = $this->request->getPost('keyakinan');
            $topik_pembelajaran = $this->request->getPost('topik_pembelajaran');
            // Set base validation rules
            $validation->setRules([
                'bahasa' => 'required',
                'bahasa_lainnya' => $bahasa === 'LAINNYA' ? 'required' : 'permit_empty',
                'penterjemah' => 'required',
                'baca_tulis' => 'required',
                'cara_belajar' => 'required',
                'keyakinan' => 'required',
                'keyakinan_khusus' => $keyakinan === 'KHUSUS' ? 'required' : 'permit_empty',
                'topik_pembelajaran' => 'required',
                'topik_lainnya' => $topik_pembelajaran === 'Lainnya' ? 'required' : 'permit_empty',
                'kesediaan_pasien' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $edukasi = $this->EdukasiModel->find($id);

            // Proses data hambatan dari select multiple
            $hambatan = $this->request->getPost('hambatan');
            $hambatan_csv = is_array($hambatan) ? implode(',', $hambatan) : NULL;

            // Simpan data edukasi
            $data = [
                'id_edukasi' => $id,
                'no_rm' => $edukasi['no_rm'],
                'nomor_registrasi' => $edukasi['nomor_registrasi'],
                'bahasa' => $this->request->getPost('bahasa') ?: NULL,
                'bahasa_lainnya' => $this->request->getPost('bahasa_lainnya') ?: NULL,
                'penterjemah' => $this->request->getPost('penterjemah') ?: NULL,
                'pendidikan' => $this->request->getPost('pendidikan') ?: NULL,
                'baca_tulis' => $this->request->getPost('baca_tulis') ?: NULL,
                'cara_belajar' => $this->request->getPost('cara_belajar') ?: NULL,
                'budaya' => $this->request->getPost('budaya') ?: NULL,
                'hambatan' => $hambatan_csv,
                'keyakinan' => $this->request->getPost('keyakinan') ?: NULL,
                'keyakinan_khusus' => $this->request->getPost('keyakinan_khusus') ?: NULL,
                'topik_pembelajaran' => $this->request->getPost('topik_pembelajaran') ?: NULL,
                'topik_lainnya' => $this->request->getPost('topik_lainnya') ?: NULL,
                'kesediaan_pasien' => $this->request->getPost('kesediaan_pasien') ?: NULL,
                'waktu_dibuat' => $edukasi['waktu_dibuat'],
            ];
            $this->EdukasiModel->save($data);
            $this->notify_clients_submit('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Edukasi berhasil diperbarui']);
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
