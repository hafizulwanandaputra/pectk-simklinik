<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\SkriningModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Skrining extends BaseController
{
    protected $RawatJalanModel;
    protected $SkriningModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->SkriningModel = new SkriningModel();
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

            // Memeriksa apakah skrining sudah ada
            $skrining = $db->table('medrec_skrining')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$skrining) {
                // Jika skrining tidak ditemukan, buat skrining baru dengan query builder
                $db->table('medrec_skrining')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                $this->notify_clients('update');

                // Setelah skrining dibuat, ambil kembali data skrining menggunakan query builder
                $skrining = $db->table('medrec_skrining')
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

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'skrining' => $skrining,
                'title' => 'Skrining ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Skrining',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/skrining/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->SkriningModel->find($id); // Mengambil skrining
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

            // Memeriksa apakah skrining sudah ada
            $skrining = $db->table('medrec_skrining')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // Memeriksa apakah pasien tidak kosong
            if ($skrining) {
                // === Generate Barcode ===
                $barcodeGenerator = new BarcodeGeneratorPNG();
                $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'skrining' => $skrining,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Skrining ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/skrining/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/rawatjalan/skrining/form', $data);
                $filename = 'output-skrining.pdf';

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            $nyeri_hilang_bila = $this->request->getPost('nyeri_hilang_bila');
            // Set base validation rules
            $validation->setRules([
                'jatuh_sempoyongan' => 'required',
                'jatuh_penopang' => 'required',
                'jatuh_info_dokter' => 'required',
                'status_fungsional' => 'required',
                'nyeri_kategori' => 'required',
                'nyeri_hilang_lainnya' => $nyeri_hilang_bila === 'LAIN-LAIN' ? 'required' : 'permit_empty',
                'nyeri_info_dokter' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $skrining = $this->SkriningModel->find($id);

            // Simpan data skrining
            $data = [
                'id_skrining' => $id,
                'no_rm' => $skrining['no_rm'],
                'nomor_registrasi' => $skrining['nomor_registrasi'],
                'jatuh_sempoyongan' => $this->request->getPost('jatuh_sempoyongan') ?: NULL,
                'jatuh_penopang' => $this->request->getPost('jatuh_penopang') ?: NULL,
                'jatuh_info_dokter' => $this->request->getPost('jatuh_info_dokter') ?: NULL,
                'jatuh_info_pukul' => $this->request->getPost('jatuh_info_pukul') ?: NULL,
                'status_fungsional' => $this->request->getPost('status_fungsional') ?: NULL,
                'status_info_pukul' => $this->request->getPost('status_info_pukul') ?: NULL,
                'nyeri_kategori' => $this->request->getPost('nyeri_kategori') ?: NULL,
                'nyeri_skala' => $this->request->getPost('nyeri_skala') ?: '0',
                'nyeri_lokasi' => $this->request->getPost('nyeri_lokasi') ?: NULL,
                'nyeri_karakteristik' => $this->request->getPost('nyeri_karakteristik') ?: NULL,
                'nyeri_durasi' => $this->request->getPost('nyeri_durasi') ?: NULL,
                'nyeri_frekuensi' => $this->request->getPost('nyeri_frekuensi') ?: NULL,
                'nyeri_hilang_bila' => $this->request->getPost('nyeri_hilang_bila') ?: NULL,
                'nyeri_hilang_lainnya' => $this->request->getPost('nyeri_hilang_lainnya') ?: NULL,
                'nyeri_info_dokter' => $this->request->getPost('nyeri_info_dokter') ?: NULL,
                'nyeri_info_pukul' => $this->request->getPost('nyeri_info_pukul') ?: NULL,
                'waktu_dibuat' => $skrining['waktu_dibuat'],
            ];
            $this->SkriningModel->save($data);
            $this->notify_clients_submit('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Skrining berhasil diperbarui']);
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
