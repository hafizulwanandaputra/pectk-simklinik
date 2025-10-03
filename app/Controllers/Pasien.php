<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RawatJalanModel;
use App\Models\PoliklinikModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Generator;

class Pasien extends BaseController
{
    protected $PasienModel;
    protected $RawatJalanModel;
    protected $PoliklinikModel;
    protected $AuthModel;
    public function __construct()
    {
        $this->PasienModel = new PasienModel();
        $this->RawatJalanModel = new RawatJalanModel();
        $this->PoliklinikModel = new PoliklinikModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Pasien - ' . $this->systemName,
                'headertitle' => 'Pasien',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/pasien/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $tanggal_lahir = $this->request->getGet('tanggal_lahir');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $PasienModel = $this->PasienModel;

            // Menerapkan filter pencarian berdasarkan nomor rekam medis dan nama pasien, pasien
            if ($search) {
                // Terapkan filter pencarian
                $PasienModel->groupStart()
                    ->like('no_rm', $search)
                    ->orLike('nama_pasien', $search)
                    ->orLike('nik', $search)
                    ->orLike('no_bpjs', $search)
                    ->groupEnd();
            }

            // Menerapkan filter pencarian berdasarkan tanggal_lahir
            if ($tanggal_lahir) {
                // Terapkan filter pencarian
                $PasienModel->groupStart()
                    ->like('tanggal_lahir', $tanggal_lahir)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $PasienModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $PasienModel->orderBy('id_pasien', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            $db = db_connect();

            // Tambahkan nomor urut + jumlah rawat jalan ke setiap pasien
            $dataPasien = array_map(function ($data, $index) use ($startNumber, $db) {
                $data['number'] = $startNumber + $index;

                // Hitung jumlah rawat jalan keseluruhan
                $countAll = $db->table('rawat_jalan')
                    ->where('no_rm', $data['no_rm'])
                    ->countAllResults();

                // Hitung jumlah rawat jalan dengan status DAFTAR
                $countDaftar = $db->table('rawat_jalan')
                    ->where('no_rm', $data['no_rm'])
                    ->where('status', 'DAFTAR')
                    ->countAllResults();

                // Hitung jumlah rawat jalan dengan status BATAL
                $countBatal = $db->table('rawat_jalan')
                    ->where('no_rm', $data['no_rm'])
                    ->where('status', 'BATAL')
                    ->countAllResults();

                $data['jumlah_rawat_jalan'] = $countAll;
                $data['jumlah_rawat_jalan_daftar'] = $countDaftar;
                $data['jumlah_rawat_jalan_batal']  = $countBatal;

                return $data;
            }, $Pasien, array_keys($Pasien));

            // Mengembalikan data pasien dalam format JSON
            return $this->response->setJSON([
                'pasien' => $dataPasien,
                'total' => $total // Mengembalikan total hasil
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function cekkososng()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            $pasien = $db->table('pasien')
                ->where('nama_pasien', null);

            $total_pasien_kosong = $pasien->countAllResults();
            $no_rm_kosong = $pasien->select('no_rm')->where('nama_pasien', null)->get()->getResultArray();

            if ($total_pasien_kosong > 0) {
                return $this->response->setJSON([
                    'cekkosong' => true,
                    'message' => 'Ada ' . number_format($total_pasien_kosong, 0, ',', '.') . ' pasien dengan data kosong. Silahkan lengkapi data pasien dengan nomor rekam medis berikut:',
                    'no_rm' => array_column($no_rm_kosong, 'no_rm')
                ]);
            } else {
                return $this->response->setJSON([
                    'cekkosong' => false,
                    'message' => 'Tidak ada pasien dengan data kosong.',
                    'no_rm' => NULL
                ]);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menghasilkan nomor rekam medis baru
            $lastRecord = $this->PasienModel->orderBy('id_pasien', 'DESC')->first(); // Dapatkan data terakhir berdasarkan ID
            $lastNoRm = $lastRecord ? str_replace('-', '', $lastRecord['no_rm']) : '000000'; // Nomor default jika tidak ada data

            $newNoRmNumeric = (int)$lastNoRm + 1; // Auto increment
            $newNoRm = str_pad($newNoRmNumeric, 6, '0', STR_PAD_LEFT); // Pastikan panjangnya 6 digit
            $formattedNoRm = substr($newNoRm, 0, 2) . '-' . substr($newNoRm, 2, 2) . '-' . substr($newNoRm, 4, 2); // Format xx-xx-xx

            // Simpan data pasien
            $data = [
                'no_rm' => $formattedNoRm,
                'nama_pasien' => NULL,
                'nik' => NULL,
                'no_bpjs' => NULL,
                'tempat_lahir' => NULL,
                'tanggal_lahir' => NULL,
                'jenis_kelamin' => NULL,
                'alamat' => NULL,
                'provinsi' => '',
                'kabupaten' => '',
                'kecamatan' => '',
                'kelurahan' => '',
                'rt' => NULL,
                'rw' => NULL,
                'telpon' => NULL,
                'kewarganegaraan' => '',
                'agama' => '',
                'status_nikah' => '',
                'pekerjaan' => '',
                'tanggal_daftar' => date("Y-m-d H:i:s"),
            ];
            $this->PasienModel->insert($data);

            // Dapatkan ID dari data yang baru disimpan
            $newId = $this->PasienModel->insertID();

            // Panggil WebSocket untuk update client
            $this->notify_clients('update');

            // Redirect ke halaman detail pasien
            return redirect()->to(base_url('pasien/detailpasien/' . $newId));
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menghubungkan ke database
            $db = db_connect();

            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

            // Agama
            $agama = $db->table('master_agama')->get()->getResultArray();

            // Kewarganegaraan
            $kebangsaan = $db->table('master_kebangsaan')->get()->getResultArray();

            // Status Pernikahan
            $status_pernikahan = $db->table('master_status_pernikahan')->get()->getResultArray();

            // Pekerjaan
            $pekerjaan = $db->table('master_pekerjaan')->where('pekerjaanId !=', 1)->orderBy('pekerjaanNama', 'ASC')->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('pasien')
                ->where('pasien.id_pasien <', $id) // Kondisi untuk id sebelumnya
                ->orderBy('pasien.id_pasien', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('pasien')
                ->where('pasien.id_pasien >', $id) // Kondisi untuk id berikutnya
                ->orderBy('pasien.id_pasien', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Memeriksa apakah pasien tidak kosong
            if (!empty($pasien)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'pasien' => $pasien,
                    'title' => 'Detail Pasien ' . $pasien['nama_pasien'] . ' (' . $pasien['no_rm'] . ') - ' . $this->systemName,
                    'agama' => $agama,
                    'kebangsaan' => $kebangsaan,
                    'status_pernikahan' => $status_pernikahan,
                    'pekerjaan' => $pekerjaan,
                    'systemname' => $this->systemName,
                    'headertitle' => 'Detail Pasien',
                    'agent' => $this->request->getUserAgent(), // Menyimpan informasi tentang user agent
                    'previous' => $previous,
                    'next' => $next
                ];
                // Mengembalikan tampilan detail pasien
                return view('dashboard/pasien/details', $data);
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function identitas($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

            // Ambil tabel master_provinsi
            $provinsi = $db->table('master_provinsi');
            $provinsi->select('UPPER(provinsiNama) AS provinsiNama');
            $provinsi->where('provinsiId', $pasien['provinsi']);

            // Query untuk mendapatkan nama provinsi
            $res_provinsi = $provinsi->get()->getRow();

            if ($res_provinsi) {
                // Ubah ID menjadi nama provinsi
                $pasien['provinsi'] = $res_provinsi->provinsiNama;
            }

            // Ambil tabel master_kabupaten
            $kabupaten = $db->table('master_kabupaten');
            $kabupaten->select('UPPER(kabupatenNama) AS kabupatenNama');
            $kabupaten->where('kabupatenId', $pasien['kabupaten']);

            // Query untuk mendapatkan nama kabupaten
            $res_kabupaten = $kabupaten->get()->getRow();

            if ($res_kabupaten) {
                // Ubah ID menjadi nama kabupaten
                $pasien['kabupaten'] = $res_kabupaten->kabupatenNama;
            }

            // Ambil tabel master_kecamatan
            $kecamatan = $db->table('master_kecamatan');
            $kecamatan->select('UPPER(kecamatanNama) AS kecamatanNama');
            $kecamatan->where('kecamatanId', $pasien['kecamatan']);

            // Query untuk mendapatkan nama kecamatan
            $res_kecamatan = $kecamatan->get()->getRow();

            if ($res_kecamatan) {
                // Ubah ID menjadi nama kecamatan
                $pasien['kecamatan'] = $res_kecamatan->kecamatanNama;
            }

            // Ambil tabel master_kelurahan
            $kelurahan = $db->table('master_kelurahan');
            $kelurahan->select('UPPER(kelurahanNama) AS kelurahanNama');
            $kelurahan->where('kelurahanId', $pasien['kelurahan']);

            // Query untuk mendapatkan nama kelurahan
            $res_kelurahan = $kelurahan->get()->getRow();

            if ($res_kelurahan) {
                // Ubah ID menjadi nama kelurahan
                $pasien['kelurahan'] = $res_kelurahan->kelurahanNama;
            }

            // Ambil tabel master_agama
            $agama = $db->table('master_agama');
            $agama->select('agamaNama');
            $agama->where('agamaId', $pasien['agama']);

            // Query untuk mendapatkan nama agama
            $res_agama = $agama->get()->getRow();

            if ($res_agama) {
                // Ubah ID menjadi nama agama
                $pasien['agama'] = $res_agama->agamaNama;
            }

            // Ambil tabel master_pekerjaan
            $pekerjaan = $db->table('master_pekerjaan');
            $pekerjaan->select('pekerjaanNama');
            $pekerjaan->where('pekerjaanId', $pasien['pekerjaan']);

            // Query untuk mendapatkan nama pekerjaan
            $res_pekerjaan = $pekerjaan->get()->getRow();

            if ($res_pekerjaan) {
                // Ubah ID menjadi nama pekerjaan
                $pasien['pekerjaan'] = $res_pekerjaan->pekerjaanNama;
            }

            // Ambil tabel master_status_pernikahan
            $pernikahan = $db->table('master_status_pernikahan');
            $pernikahan->select('pernikahanStatus');
            $pernikahan->where('pernikahanId', $pasien['status_nikah']);

            // Query untuk mendapatkan nama pernikahan
            $res_pernikahan = $pernikahan->get()->getRow();

            if ($res_pernikahan) {
                // Ubah ID menjadi nama pernikahan
                $pasien['status_nikah'] = $res_pernikahan->pernikahanStatus;
            }

            $qrcode = new Generator;
            $qrNoRMSVG = $qrcode->size(64)->generate($pasien['no_rm']);
            $qrNoRM = base64_encode($qrNoRMSVG);

            // Memeriksa apakah pasien tidak kosong
            if (!empty($pasien)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'pasien' => $pasien,
                    'qrNoRM' => $qrNoRM,
                    'title' => 'Identitas Pasien ' . $pasien['nama_pasien'] . ' (' . $pasien['no_rm'] . ') - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/pasien/identitas', $data);
                // die;
                $client = new Client();
                $html = view('dashboard/pasien/identitas', $data);
                $filename = 'output-identitas.pdf';

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

    public function barcode($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoRM = base64_encode($barcodeGenerator->getBarcode($pasien['no_rm'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if (!empty($pasien)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'pasien' => $pasien,
                    'bcNoRM' => $bcNoRM,
                    'title' => 'Barcode ' . $pasien['nama_pasien'] . ' (' . $pasien['no_rm'] . ') - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/pasien/barcode', $data);
                // die;
                $client = new Client();
                $html = view('dashboard/pasien/barcode', $data);
                $filename = 'output-barcode.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'width' => '50mm',
                                'height' => '20.15mm',
                                'margin' => [
                                    'top' => '0.025cm',
                                    'right' => '0.05cm',
                                    'bottom' => '0.025cm',
                                    'left' => '0.05cm'
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

    public function pasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil data pasien berdasarkan ID
            $data = $this->PasienModel->find($id); // Mengambil pasien
            return $this->response->setJSON($data); // Mengembalikan data pasien dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function provinsi()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data provinsi
            $builder = $db->table('master_provinsi');
            $result = $builder->select('provinsiId, UPPER(provinsiNama) AS provinsiNama')->get()->getResultArray();

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

    public function kabupaten($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kabupaten
            $builder = $db->table('master_kabupaten');
            $result = $builder->select('kabupatenId, UPPER(kabupatenNama) AS kabupatenNama')->where('provinsiId', $id)->get()->getResultArray();

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

    public function kecamatan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kecamatan
            $builder = $db->table('master_kecamatan');
            $result = $builder->select('kecamatanId, UPPER(kecamatanNama) AS kecamatanNama')->where('kabupatenId', $id)->get()->getResultArray();

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

    public function kelurahan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kelurahan
            $builder = $db->table('master_kelurahan');
            $result = $builder->select('kelurahanId, UPPER(kelurahanNama) AS kelurahanNama')->where('kecamatanId', $id)->get()->getResultArray();

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

    public function rawatjalanlist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $tanggal = $this->request->getGet('tanggal');
            $jenis_kunjungan = $this->request->getGet('jenis_kunjungan');
            $jaminan = $this->request->getGet('jaminan');
            $ruangan = $this->request->getGet('ruangan');
            $dokter = $this->request->getGet('dokter');
            $pendaftar = $this->request->getGet('pendaftar');
            $status = $this->request->getGet('status');
            $transaksi = $this->request->getGet('transaksi');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $RawatJalanModel = $this->RawatJalanModel;

            $RawatJalanModel->select('rawat_jalan.*'); // Mengambil semua kolom dari tabel resep

            // Mengaplikasikan filter tanggal jika diberikan
            if ($tanggal) {
                $RawatJalanModel->like('tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter untuk kunjungan
            if ($jenis_kunjungan) {
                $RawatJalanModel->where('jenis_kunjungan', $jenis_kunjungan); // Menambahkan filter berdasarkan kunjungan
            }

            // Menerapkan filter untuk jaminan
            if ($jaminan) {
                $RawatJalanModel->where('jaminan', $jaminan); // Menambahkan filter berdasarkan jaminan
            }

            // Menerapkan filter untuk ruangan
            if ($ruangan) {
                $RawatJalanModel->where('ruangan', $ruangan); // Menambahkan filter berdasarkan ruangan
            }

            // Menerapkan filter untuk dokter
            if ($dokter) {
                $RawatJalanModel->where('dokter', $dokter); // Menambahkan filter berdasarkan dokter
            }

            // Menerapkan filter untuk pendaftar
            if ($pendaftar) {
                $RawatJalanModel->where('pendaftar', $pendaftar); // Menambahkan filter berdasarkan pendaftar
            }

            // Menerapkan filter untuk status
            if ($status) {
                $RawatJalanModel->where('status', $status); // Menambahkan filter berdasarkan status
            }

            // Menerapkan filter transaksi
            if ($transaksi === '1') {
                $RawatJalanModel->where('transaksi', 1); // Mengambil rawat jalan yang sudah ditransaksikan
            } elseif ($transaksi === '0') {
                $RawatJalanModel->where('transaksi', 0); // Mengambil rawat jalan yang belum ditransaksikan
            }

            // Menambahkan filter untuk rawat jalan agar hanya menampilkan rawat jalan dari salah satu pasien
            $RawatJalanModel->groupStart()
                ->where('no_rm', $id)
                ->groupEnd();

            // Menghitung total hasil pencarian
            $total = $RawatJalanModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $RawatJalanModel->orderBy('id_rawat_jalan', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap pasien
            $dataRajal = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data; // Mengembalikan data yang telah ditambahkan nomor urut
            }, $Pasien, array_keys($Pasien));

            $db = db_connect();

            // Ambil semua data dari master_jaminan untuk di-cache
            $jaminanList = $db->table('master_jaminan')
                ->select('jaminanKode, jaminanNama')
                ->get()
                ->getResultArray();

            // Ubah data menjadi array dengan key sebagai jaminanKode untuk akses cepat
            $jaminanMap = [];
            foreach ($jaminanList as $jaminan) {
                $jaminanMap[$jaminan['jaminanKode']] = $jaminan['jaminanNama'];
            }

            // Loop untuk mengganti nilai 'jaminan' di $rawatjalan
            foreach ($dataRajal as &$rajal) {
                if (isset($jaminanMap[$rajal['jaminan']])) {
                    $rajal['jaminan'] = $jaminanMap[$rajal['jaminan']];
                } else {
                    $rajal['jaminan'] = 'Tidak Diketahui'; // Default jika kode jaminan tidak ditemukan
                }
                // Tambahkan field resep_obat_digunakan
                $resepObatCount = $db->table('resep')
                    ->where('nomor_registrasi', $rajal['nomor_registrasi'])
                    ->countAllResults();
                $rajal['resep_obat_digunakan'] = (int) ($resepObatCount > 0);

                // Tambahkan field resep_kacamata_digunakan
                $resepKacamataCount = $db->table('medrec_optik')
                    ->where('nomor_registrasi', $rajal['nomor_registrasi'])
                    ->countAllResults();
                $rajal['resep_kacamata_digunakan'] = (int) ($resepKacamataCount > 0);

                // Tambahkan field transaksi_digunakan
                $transaksiCount = $db->table('transaksi')
                    ->where('nomor_registrasi', $rajal['nomor_registrasi'])
                    ->countAllResults();
                $rajal['transaksi_digunakan'] = (int) ($transaksiCount > 0);
            }

            // Mengembalikan data pasien dalam format JSON
            return $this->response->setJSON([
                'rajal' => $dataRajal,
                'total' => $total // Mengembalikan total hasil
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function kunjunganoptions($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil jenis kunjungan dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('no_rm', $no_rm)
                ->groupBy('jenis_kunjungan')
                ->orderBy('jenis_kunjungan', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data rawat jalan yang diterima
            foreach ($rawatJalan as $kunjungan) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $kunjungan['jenis_kunjungan'], // Nilai untuk opsi
                    'text'  => $kunjungan['jenis_kunjungan'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data rawat jalan dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function jaminanoptions()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil jaminan dari tabel master jaminan
            $db = db_connect();
            $masterjaminan = $db->table('master_jaminan')
                ->where('jaminanStatus', 'AKTIF')
                ->get()->getResultArray();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data pengguna yang diterima
            foreach ($masterjaminan as $jaminan) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value'  => $jaminan['jaminanKode'], // Teks untuk opsi
                    'text'  => $jaminan['jaminanNama'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data rawat jalan dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function ruanganoptions()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil ruangan dari tabel poliklinik
            $poliklinik = $this->PoliklinikModel
                ->where('status', 1)
                ->orderBy('id_poli', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data poliklinik yang diterima
            foreach ($poliklinik as $ruangan) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $ruangan['nama_poli'], // Nilai untuk opsi
                    'text'  => $ruangan['nama_poli'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data poliklinik dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function dokteroptions()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil ruangan dari tabel pengguna
            $auth = $this->AuthModel
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data pengguna yang diterima
            foreach ($auth as $dokter) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $dokter['fullname'], // Nilai untuk opsi
                    'text'  => $dokter['fullname'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data pengguna dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function pendaftaroptions($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil pendaftar dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('no_rm', $no_rm)
                ->groupBy('pendaftar')
                ->orderBy('pendaftar', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data rawat jalan yang diterima
            foreach ($rawatJalan as $pendaftar) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $pendaftar['pendaftar'], // Nilai untuk opsi
                    'text'  => $pendaftar['pendaftar'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data rawat jalan dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function statusoptions($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Mengambil status dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('no_rm', $no_rm)
                ->groupBy('status')
                ->orderBy('status', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data rawat jalan yang diterima
            foreach ($rawatJalan as $status) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $status['status'], // Nilai untuk opsi
                    'text'  => $status['status'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data rawat jalan dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function exportexcel()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Ambil semua data pasien
            $pasien = $this->PasienModel->findAll();

            // Membuat nama file
            $filename = 'DATA PASIEN PECTK.xlsx';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Menambahkan informasi header di spreadsheet
            $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
            $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
            $sheet->setCellValue('A3', 'DATA PASIEN RAWAT JALAN');

            // Path gambar yang ingin ditambahkan
            $gambarPath = FCPATH . 'assets/images/logo_pec.png'; // Ganti dengan path gambar Anda

            // Membuat objek Drawing
            $drawing = new Drawing();
            $drawing->setName('Logo PEC-TK'); // Nama gambar
            $drawing->setDescription('Logo PEC-TK'); // Deskripsi gambar
            $drawing->setPath($gambarPath); // Path ke gambar
            $drawing->setCoordinates('A1'); // Koordinat sel tempat gambar akan ditambahkan
            $drawing->setHeight(36); // Tinggi gambar dalam piksel (opsional)
            $drawing->setWorksheet($sheet); // Menambahkan gambar ke worksheet

            // Menambahkan header tabel detail laporan resep
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'Tanggal dan Waktu');
            $sheet->setCellValue('C4', 'Nama');
            $sheet->setCellValue('D4', 'Tempat dan Tanggal Lahir');
            $sheet->setCellValue('E4', 'NIK');
            $sheet->setCellValue('F4', 'Alamat');
            $sheet->setCellValue('G4', 'L/P');
            $sheet->setCellValue('H4', 'Agama');
            $sheet->setCellValue('I4', 'Nomor HP');
            $sheet->setCellValue('J4', 'Provinsi');
            $sheet->setCellValue('K4', 'Kab/Kota');
            $sheet->setCellValue('L4', 'Kecamatan');
            $sheet->setCellValue('M4', 'Desa/Kelurahan');
            $sheet->setCellValue('N4', 'No RM');
            $sheet->setCellValue('O4', 'Status Nikah');
            $sheet->setCellValue('P4', 'Pekerjaan');

            // Mengatur tata letak dan gaya untuk header
            $spreadsheet->getActiveSheet()->mergeCells('A1:P1');
            $spreadsheet->getActiveSheet()->mergeCells('A2:P2');
            $spreadsheet->getActiveSheet()->mergeCells('A3:P3');
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

            $column = 5; // Baris awal data
            $nomor = 1;  // Nomor urut resep

            foreach ($pasien as $list) {
                $db = db_connect();

                // Ambil tabel master_provinsi
                $provinsi = $db->table('master_provinsi');
                $provinsi->select('provinsiNama');
                $provinsi->where('provinsiId', $list['provinsi']);

                // Query untuk mendapatkan nama provinsi
                $res_provinsi = $provinsi->get()->getRow();

                if ($res_provinsi) {
                    // Ubah ID menjadi nama provinsi
                    $list['provinsi'] = $res_provinsi->provinsiNama;
                }

                // Ambil tabel master_kabupaten
                $kabupaten = $db->table('master_kabupaten');
                $kabupaten->select('kabupatenNama');
                $kabupaten->where('kabupatenId', $list['kabupaten']);

                // Query untuk mendapatkan nama kabupaten
                $res_kabupaten = $kabupaten->get()->getRow();

                if ($res_kabupaten) {
                    // Ubah ID menjadi nama kabupaten
                    $list['kabupaten'] = $res_kabupaten->kabupatenNama;
                }

                // Ambil tabel master_kecamatan
                $kecamatan = $db->table('master_kecamatan');
                $kecamatan->select('kecamatanNama');
                $kecamatan->where('kecamatanId', $list['kecamatan']);

                // Query untuk mendapatkan nama kecamatan
                $res_kecamatan = $kecamatan->get()->getRow();

                if ($res_kecamatan) {
                    // Ubah ID menjadi nama kecamatan
                    $list['kecamatan'] = $res_kecamatan->kecamatanNama;
                }

                // Ambil tabel master_kelurahan
                $kelurahan = $db->table('master_kelurahan');
                $kelurahan->select('kelurahanNama');
                $kelurahan->where('kelurahanId', $list['kelurahan']);

                // Query untuk mendapatkan nama kelurahan
                $res_kelurahan = $kelurahan->get()->getRow();

                if ($res_kelurahan) {
                    // Ubah ID menjadi nama kelurahan
                    $list['kelurahan'] = $res_kelurahan->kelurahanNama;
                }

                // Ambil tabel master_agama
                $agama = $db->table('master_agama');
                $agama->select('agamaNama');
                $agama->where('agamaId', $list['agama']);

                // Query untuk mendapatkan nama agama
                $res_agama = $agama->get()->getRow();

                if ($res_agama) {
                    // Ubah ID menjadi nama agama
                    $list['agama'] = $res_agama->agamaNama;
                }

                // Ambil tabel master_pekerjaan
                $pekerjaan = $db->table('master_pekerjaan');
                $pekerjaan->select('pekerjaanNama');
                $pekerjaan->where('pekerjaanId', $list['pekerjaan']);

                // Query untuk mendapatkan nama pekerjaan
                $res_pekerjaan = $pekerjaan->get()->getRow();

                if ($res_pekerjaan) {
                    // Ubah ID menjadi nama pekerjaan
                    $list['pekerjaan'] = $res_pekerjaan->pekerjaanNama;
                }

                // Ambil tabel master_status_pernikahan
                $pernikahan = $db->table('master_status_pernikahan');
                $pernikahan->select('pernikahanStatus');
                $pernikahan->where('pernikahanId', $list['status_nikah']);

                // Query untuk mendapatkan nama pernikahan
                $res_pernikahan = $pernikahan->get()->getRow();

                if ($res_pernikahan) {
                    // Ubah ID menjadi nama pernikahan
                    $list['status_nikah'] = $res_pernikahan->pernikahanStatus;
                }

                // Isi data resep
                $sheet->setCellValue('A' . $column, $nomor++);
                $sheet->setCellValue('B' . $column, $list['tanggal_daftar']);
                $sheet->setCellValue('C' . $column, $list['nama_pasien']);
                $sheet->setCellValue('D' . $column, $list['tempat_lahir'] . ', ' . $list['tanggal_lahir']);
                $sheet->setCellValueExplicit('E' . $column, $list['nik'], DataType::TYPE_STRING);
                $sheet->setCellValue('F' . $column, $list['alamat']);
                $sheet->setCellValue('G' . $column, $list['jenis_kelamin']);
                $sheet->setCellValue('H' . $column, $list['agama']);
                $sheet->setCellValueExplicit('I' . $column, $list['telpon'], DataType::TYPE_STRING);
                $sheet->setCellValue('J' . $column, $list['provinsi']);
                $sheet->setCellValue('K' . $column, $list['kabupaten']);
                $sheet->setCellValue('L' . $column, $list['kecamatan']);
                $sheet->setCellValue('M' . $column, $list['kelurahan']);
                $sheet->setCellValue('N' . $column, $list['no_rm']);
                $sheet->setCellValue('O' . $column, $list['status_nikah']);
                $sheet->setCellValue('P' . $column, $list['pekerjaan']);

                // Atur nomor ke rata tengah
                $sheet->getStyle("A{$column}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tambahkan baris pemisah antar transaksi
                $column++;
            }

            // Mengatur gaya teks untuk header dan total
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getFont()->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2')->getFont()->setSize(8);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3')->getFont()->setSize(12);
            $sheet->getStyle('A4:P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Mengatur gaya font untuk header dan total
            $sheet->getStyle('A1:A4')->getFont()->setBold(TRUE);
            $sheet->getStyle('A4:P4')->getFont()->setBold(TRUE);
            $sheet->getStyle('A4:P4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . ($column) . ':P' . ($column))->getFont()->setBold(TRUE);

            // Menambahkan border untuk header dan tabel
            $headerBorder1 = [
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000']
                    ]
                ]
            ];
            $sheet->getStyle('A2:P2')->applyFromArray($headerBorder1);
            $tableBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000']
                    ]
                ]
            ];
            $sheet->getStyle('A4:P' . ($column - 1))->applyFromArray($tableBorder);
            $sheet->getStyle('A4:P' . ($column - 1))->getAlignment()->setWrapText(true);
            $sheet->getStyle('A5:P' . ($column + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

            // Mengatur lebar kolom
            $sheet->getColumnDimension('A')->setWidth(50, 'px');
            $sheet->getColumnDimension('B')->setWidth(135, 'px');
            $sheet->getColumnDimension('C')->setWidth(200, 'px');
            $sheet->getColumnDimension('D')->setWidth(200, 'px');
            $sheet->getColumnDimension('E')->setWidth(135, 'px');
            $sheet->getColumnDimension('F')->setWidth(135, 'px');
            $sheet->getColumnDimension('G')->setWidth(30, 'px');
            $sheet->getColumnDimension('H')->setWidth(135, 'px');
            $sheet->getColumnDimension('I')->setWidth(135, 'px');
            $sheet->getColumnDimension('J')->setWidth(135, 'px');
            $sheet->getColumnDimension('K')->setWidth(135, 'px');
            $sheet->getColumnDimension('L')->setWidth(135, 'px');
            $sheet->getColumnDimension('M')->setWidth(135, 'px');
            $sheet->getColumnDimension('N')->setWidth(75, 'px');
            $sheet->getColumnDimension('O')->setWidth(135, 'px');
            $sheet->getColumnDimension('P')->setWidth(135, 'px');

            // Menyimpan file spreadsheet dan mengirimkan ke browser
            $writer = new Xlsx($spreadsheet);
            // Simpan ke file sementara
            $temp_file = WRITEPATH . 'exports/' . $filename;
            $writer->save($temp_file);

            // Kirimkan file dalam mode streaming agar bisa dipantau progresnya
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($temp_file));

            readfile($temp_file);
            unlink($temp_file); // Hapus setelah dikirim
            exit();
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function update($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_pasien' => 'required',
                'nik' => 'numeric|permit_empty',
                'no_bpjs' => 'numeric|permit_empty',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'rt' => 'numeric|permit_empty',
                'rw' => 'numeric|permit_empty',
                'telpon' => 'numeric|permit_empty',
                'kewarganegaraan' => 'required',
                'agama' => 'required',
                'status_nikah' => 'required',
                'pekerjaan' => 'required',
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $pasien = $this->PasienModel->find($id);

            $rt_input = $this->request->getPost('rt');
            $rw_input = $this->request->getPost('rw');

            // Simpan data pasien
            $data = [
                'id_pasien' => $id,
                'no_rm' => $pasien['no_rm'],
                'nama_pasien' => $this->request->getPost('nama_pasien'),
                'nik' => $this->request->getPost('nik') ?: NULL,
                'no_bpjs' => $this->request->getPost('no_bpjs') ?: NULL,
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'alamat' => $this->request->getPost('alamat'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kabupaten' => $this->request->getPost('kabupaten'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'kelurahan' => $this->request->getPost('kelurahan'),
                'rt' => ($rt_input !== '' && !preg_match('/^0+$/', $rt_input)) ? str_pad($rt_input, 3, '0', STR_PAD_LEFT) : NULL,
                'rw' => ($rw_input !== '' && !preg_match('/^0+$/', $rw_input)) ? str_pad($rw_input, 3, '0', STR_PAD_LEFT) : NULL,
                'telpon' => $this->request->getPost('telpon'),
                'kewarganegaraan' => $this->request->getPost('kewarganegaraan'),
                'agama' => $this->request->getPost('agama'),
                'status_nikah' => $this->request->getPost('status_nikah'),
                'pekerjaan' => $this->request->getPost('pekerjaan'),
                'tanggal_daftar' => $pasien['tanggal_daftar'],
            ];
            $this->PasienModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Data pasien berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // Cari pasien dengan ID terbesar (terakhir dimasukkan)
            $lastPasien = $db->table('pasien')
                ->select('id_pasien, no_rm')
                ->orderBy('id_pasien', 'DESC')
                ->get()
                ->getRow();

            $lastId = $lastPasien->id_pasien;
            $lastNoRM = $lastPasien->no_rm;

            // Jika ID yang mau dihapus bukan ID terakhir → gagalkan
            if ($id != $lastId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pasien dengan nomor rekam medis terakhir yang dapat dihapus. '
                        . 'Hapus pasien ' . $lastNoRM . ' terlebih dahulu.',
                ]);
            }

            // Ambil no_rm dari pasien
            $pasien = $db->table('pasien')->select('no_rm')->where('id_pasien', $id)->get()->getRow();

            if (!$pasien) {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Data pasien tidak ditemukan',
                ]);
            }

            // Cek apakah no_rm dipakai di tabel rawat_jalan
            $count = $db->table('rawat_jalan')
                ->where('no_rm', $pasien->no_rm)
                ->where('status', 'DAFTAR')
                ->countAllResults();

            if ($count > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data pasien tidak dapat dihapus karena masih memiliki riwayat rawat jalan terdaftar',
                ]);
            }

            // Ambil semua nomor_registrasi dari pasien ini
            $rawatJalanData = $db->table('rawat_jalan')
                ->where('no_rm', $pasien->no_rm)
                ->where('status', 'BATAL')
                ->get()
                ->getResultArray();

            if (!empty($rawatJalanData)) {
                foreach ($rawatJalanData as $rj) {
                    $nomorRegistrasi = $rj['nomor_registrasi'];

                    // --- Hapus file asesmen mata ---
                    $asesmen_mata = $db->table('medrec_assesment_mata')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($asesmen_mata as $mata) {
                        if (!empty($mata['gambar']) && file_exists(FCPATH . 'uploads/asesmen_mata/' . $mata['gambar'])) {
                            @unlink(FCPATH . 'uploads/asesmen_mata/' . $mata['gambar']);
                        }
                    }

                    // --- Hapus file edukasi evaluasi ---
                    $edukasi_evaluasi = $db->table('medrec_edukasi_evaluasi')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($edukasi_evaluasi as $edukasi) {
                        if (!empty($edukasi['tanda_tangan_edukator']) && file_exists(FCPATH . 'uploads/ttd_edukator_evaluasi/' . $edukasi['tanda_tangan_edukator'])) {
                            @unlink(FCPATH . 'uploads/ttd_edukator_evaluasi/' . $edukasi['tanda_tangan_edukator']);
                        }
                        if (!empty($edukasi['tanda_tangan_pasien']) && file_exists(FCPATH . 'uploads/ttd_pasien_evaluasi/' . $edukasi['tanda_tangan_pasien'])) {
                            @unlink(FCPATH . 'uploads/ttd_pasien_evaluasi/' . $edukasi['tanda_tangan_pasien']);
                        }
                    }

                    // --- Hapus file penunjang scan ---
                    $penunjang_scan = $db->table('medrec_permintaan_penunjang_scan')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($penunjang_scan as $scan) {
                        if (!empty($scan['gambar']) && file_exists(FCPATH . 'uploads/scan_penunjang/' . $scan['gambar'])) {
                            @unlink(FCPATH . 'uploads/scan_penunjang/' . $scan['gambar']);
                        }
                    }

                    // --- Hapus file site marking operasi ---
                    $sp_operasi = $db->table('medrec_sp_operasi')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($sp_operasi as $scan) {
                        if (!empty($scan['site_marking']) && file_exists(FCPATH . 'uploads/site_marking/' . $scan['site_marking'])) {
                            @unlink(FCPATH . 'uploads/site_marking/' . $scan['site_marking']);
                        }
                    }
                }
            }

            // Menghapus pasien
            $db->table('pasien')
                ->where('id_pasien', $id)
                ->delete();

            // Reset auto increment untuk tabel pasien
            $db->query('ALTER TABLE pasien auto_increment = 1');
            $db->query('ALTER TABLE rawat_jalan AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_assesment AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_assesment_mata AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_edukasi AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_edukasi_evaluasi AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_form_persetujuan_tindakan AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_form_persetujuan_tindakan_anestesi AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_lp_operasi AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_lp_operasi_katarak AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_lp_operasi_pterigium AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_operasi_pra AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_operasi_safety_signin AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_operasi_safety_signout AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_operasi_safety_timeout AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_optik AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_permintaan_penunjang AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_permintaan_penunjang_scan AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_skrining AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_sp_operasi AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_rujukan AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_keterangan_buta_warna AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_keterangan_sakit_mata AUTO_INCREMENT = 1');
            $db->query('ALTER TABLE medrec_keterangan_istirahat AUTO_INCREMENT = 1');

            // Panggil WebSocket untuk update client
            $this->notify_clients('delete');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data pasien berhasil dihapus',
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
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
