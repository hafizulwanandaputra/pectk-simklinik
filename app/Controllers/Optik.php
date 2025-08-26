<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\OptikModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Optik extends BaseController
{
    protected $RawatJalanModel;
    protected $OptikModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->OptikModel = new OptikModel();
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

            $title = 'Resep Kacamata ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName;
            $headertitle = 'Resep Kacamata';

            // Memeriksa apakah optik sudah ada
            $optik = $db->table('medrec_optik')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

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

            if (!$optik) {
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'title' => $title,
                    'headertitle' => $headertitle, // Judul header
                    'agent' => $this->request->getUserAgent(), // Mengambil user agent
                    'previous' => $previous,
                    'next' => $next,
                    'listRawatJalan' => $listRawatJalan
                ];
                return view('dashboard/rawatjalan/optik/empty', $data);
            }

            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'optik' => $optik,
                'title' => $title,
                'headertitle' => $headertitle, // Judul header
                'agent' => $this->request->getUserAgent(), // Mengambil user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/optik/index', $data);
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
            $data = $this->OptikModel
                ->join('rawat_jalan', 'medrec_optik.nomor_registrasi = rawat_jalan.nomor_registrasi', 'inner')
                ->find($id); // Mengambil skrining
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if ($rawatjalan['transaksi'] == 1) {
                session()->setFlashdata('error', 'Resep kacamata tidak dapat ditambahkan pada rawat jalan yang transaksisnya sudah diproses');
                return redirect()->back();
            }

            if (session()->get('role') == 'Dokter') {
                if ($rawatjalan['dokter'] != session()->get('fullname')) {
                    session()->setFlashdata('error', 'Resep kacamata ini hanya bisa ditambahkan oleh ' . $rawatjalan['dokter']);
                    return redirect()->back();
                }
            }

            // Buat resep kacamata baru dengan query builder
            $db->table('medrec_optik')->insert([
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'no_rm' => $rawatjalan['no_rm'],
                'waktu_dibuat' => date('Y-m-d H:i:s')
            ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return redirect()->back();
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
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

            // Memeriksa apakah optik sudah ada
            $optik = $db->table('medrec_optik')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($optik) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'optik' => $optik,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Resep Kacamata ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/optik/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/rawatjalan/optik/form', $data);
                $filename = 'output-optik.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'format' => 'A4',
                                'margin' => [
                                    'top' => '0.25cm',
                                    'right' => '0.25cm',
                                    'bottom' => '0.25cm',
                                    'left' => '0.25cm'
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'tipe_lensa' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $optik = $this->OptikModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_optik.nomor_registrasi')
                ->find($id);

            if (session()->get('role') == 'Dokter') {
                if ($optik['dokter'] != session()->get('fullname')) {
                    return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Resep kacamata ini hanya bisa diisi oleh ' . $optik['dokter']]);
                }
            }

            // Simpan data optik
            $data = [
                'id_optik' => $id,
                'no_rm' => $optik['no_rm'],
                'nomor_registrasi' => $optik['nomor_registrasi'],
                'tipe_lensa' => $this->request->getPost('tipe_lensa'),

                'od_login_spher' => $this->request->getPost('od_login_spher'),
                'od_login_cyldr' => $this->request->getPost('od_login_cyldr'),
                'od_login_axis' => $this->request->getPost('od_login_axis'),
                'od_login_prisma' => $this->request->getPost('od_login_prisma'),
                'od_login_basis' => $this->request->getPost('od_login_basis'),

                'od_domo_spher' => $this->request->getPost('od_domo_spher'),
                'od_domo_cyldr' => $this->request->getPost('od_domo_cyldr'),
                'od_domo_axis' => $this->request->getPost('od_domo_axis'),
                'od_domo_prisma' => $this->request->getPost('od_domo_prisma'),
                'od_domo_basis' => $this->request->getPost('od_quitat_basis'),

                'od_quitat_spher' => $this->request->getPost('od_quitat_spher'),
                'od_quitat_cyldr' => $this->request->getPost('od_quitat_cyldr'),
                'od_quitat_axis' => $this->request->getPost('od_quitat_axis'),
                'od_quitat_prisma' => $this->request->getPost('od_quitat_prisma'),
                'od_quitat_basis' => $this->request->getPost('od_quitat_basis'),

                'os_login_spher' => $this->request->getPost('os_login_spher'),
                'os_login_cyldr' => $this->request->getPost('os_login_cyldr'),
                'os_login_axis' => $this->request->getPost('os_login_axis'),
                'os_login_prisma' => $this->request->getPost('os_login_prisma'),
                'os_login_basis' => $this->request->getPost('os_login_basis'),
                'os_login_vitror' => $this->request->getPost('os_login_vitror'),
                'os_login_pupil' => $this->request->getPost('os_login_pupil'),

                'os_domo_spher' => $this->request->getPost('os_domo_spher'),
                'os_domo_cyldr' => $this->request->getPost('os_domo_cyldr'),
                'os_domo_axis' => $this->request->getPost('os_domo_axis'),
                'os_domo_prisma' => $this->request->getPost('os_domo_prisma'),
                'os_domo_basis' => $this->request->getPost('os_quitat_basis'),
                'os_domo_vitror' => $this->request->getPost('os_quitat_vitror'),
                'os_domo_pupil' => $this->request->getPost('os_quitat_pupil'),

                'os_quitat_spher' => $this->request->getPost('os_quitat_spher'),
                'os_quitat_cyldr' => $this->request->getPost('os_quitat_cyldr'),
                'os_quitat_axis' => $this->request->getPost('os_quitat_axis'),
                'os_quitat_prisma' => $this->request->getPost('os_quitat_prisma'),
                'os_quitat_basis' => $this->request->getPost('os_quitat_basis'),
                'os_quitat_vitror' => $this->request->getPost('os_quitat_vitror'),
                'os_quitat_pupil' => $this->request->getPost('os_quitat_pupil'),

                'waktu_dibuat' => $optik['waktu_dibuat'],
            ];
            $this->OptikModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return $this->response->setJSON(['success' => true, 'message' => 'Resep kacamata berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function notify_clients()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => []
        ]);

        return $this->response->setJSON([
            'status' => 'Notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
