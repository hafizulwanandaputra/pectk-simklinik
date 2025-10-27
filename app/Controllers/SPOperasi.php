<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SPOperasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SPOperasi extends BaseController
{
    protected $SPOperasiModel;
    public function __construct()
    {
        $this->SPOperasiModel = new SPOperasiModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $master_tindakan_operasi = $db->table('master_tindakan_operasi')
                ->orderBy('nama_tindakan', 'ASC')
                ->get()->getResultArray();

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi <', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi >', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->join('medrec_sp_operasi', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->where('rawat_jalan.no_rm', $sp_operasi['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->where('rawat_jalan.ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'operasi' => $sp_operasi,
                'master_tindakan_operasi' => $master_tindakan_operasi,
                'dokter' => $dokter,
                'title' => 'Surat Perintah Kamar Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Perintah Kamar Operasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/operasi/spko/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id); // Mengambil skrining
            $data['jenis_tindakan'] = explode(',', $data['jenis_tindakan']); // Ubah CSV menjadi array
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $sp_operasi['jenis_tindakan'] = str_replace(',', '<br>', $sp_operasi['jenis_tindakan']);


            // Memeriksa apakah pasien tidak kosong
            if ($sp_operasi) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'operasi' => $sp_operasi,
                    'title' => 'Surat Perintah Kamar Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                    'headertitle' => 'Surat Perintah Kamar Operasi',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/operasi/spko/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/operasi/spko/form', $data);
                $filename = 'output-spko.pdf';

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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $validation = \Config\Services::validation();
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            $validation->setRules([
                'jam_operasi' => 'required',
                'jenis_tindakan' => 'required',
                'jenis_bius' => 'required',
                'rajal_ranap' => 'required',
                'ruang_operasi' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $jenis_tindakan = $this->request->getPost('jenis_tindakan');
            $jenis_tindakan_csv = is_array($jenis_tindakan) ? implode(',', $jenis_tindakan) : NULL;

            $data = [
                'id_sp_operasi' => $id,
                'nomor_booking' => $sp_operasi['nomor_booking'],
                'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                'no_rm' => $sp_operasi['no_rm'],
                'tanggal_operasi' => $sp_operasi['tanggal_operasi'],
                'jam_operasi' => $this->request->getPost('jam_operasi') ?: NULL,
                'diagnosa' => $this->request->getPost('diagnosa') ?: NULL,
                'jenis_tindakan' => $jenis_tindakan_csv,
                'indikasi_operasi' => $this->request->getPost('indikasi_operasi') ?: NULL,
                'jenis_bius' => $this->request->getPost('jenis_bius') ?: NULL,
                'tipe_bayar' => $this->request->getPost('tipe_bayar') ?: NULL,
                'rajal_ranap' => $this->request->getPost('rajal_ranap') ?: NULL,
                'ruang_operasi' => $this->request->getPost('ruang_operasi') ?: NULL,
                'dokter_operator' => $sp_operasi['dokter_operator'],
                'status_operasi' => $sp_operasi['status_operasi'],
                'diagnosa_site_marking' => $this->request->getPost('diagnosa_site_marking') ?: NULL,
                'tindakan_site_marking' => $this->request->getPost('tindakan_site_marking') ?: NULL,
                'nama_pasien_keluarga' => $this->request->getPost('nama_pasien_keluarga') ?: NULL,
                'tanda_tangan_pasien' => $sp_operasi['tanda_tangan_pasien'],
                'waktu_dibuat' => $sp_operasi['waktu_dibuat'],
                'site_marking' => $sp_operasi['site_marking'] // Gunakan nilai lama sebagai default
            ];

            $site_marking_base64 = $this->request->getPost('site_marking');

            if ($site_marking_base64 != $sp_operasi['site_marking']) {
                $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $site_marking_base64));
                $extension = 'png';
                $site_marking_name = $sp_operasi['no_rm'] . '_' . $sp_operasi['nomor_booking'] . '.' . $extension;

                if ($sp_operasi['site_marking']) {
                    @unlink(FCPATH . 'uploads/site_marking/' . $sp_operasi['site_marking']);
                }

                file_put_contents(FCPATH . 'uploads/site_marking/' . $site_marking_name, $image_data);
                $data['site_marking'] = $site_marking_name; // Perbarui hanya jika ada data baru
            }

            $this->SPOperasiModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return $this->response->setJSON(['success' => true, 'message' => 'SPKO berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
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
