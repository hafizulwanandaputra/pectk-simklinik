<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\FRMSetujuKedokteranModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class FRMSetujuKedokteran extends BaseController
{
    protected $RawatJalanModel;
    protected $FRMSetujuKedokteranModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->FRMSetujuKedokteranModel = new FRMSetujuKedokteranModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Formulir Persetujuan Tindakan Kedokteran - ' . $this->systemName,
                'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/frmsetujukedokteran/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function frmsetujukedokteranlist()
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
            $FRMSetujuKedokteranModel = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $FRMSetujuKedokteranModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $FRMSetujuKedokteranModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $FRMSetujuKedokteranModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $FRMSetuju = $FRMSetujuKedokteranModel
                ->orderBy('id_form_persetujuan_tindakan', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataFRMSetuju = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $FRMSetuju, array_keys($FRMSetuju));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'form_persetujuan_tindakan' => $dataFRMSetuju,
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

            // LEFT JOIN ke medrec_form_persetujuan_tindakan
            $builder->join('medrec_form_persetujuan_tindakan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'left');

            // Hanya ambil data yang belum ada di medrec_form_persetujuan_tindakan
            $builder->where('medrec_form_persetujuan_tindakan.nomor_registrasi IS NULL');

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

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('status', 'DAFTAR')
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
            $db->table('medrec_form_persetujuan_tindakan')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Formulir persetujuan tindakan kedokteran berhasil ditambahkan']); // Mengembalikan respon sukses
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
            $db = db_connect();

            // Inisialisasi rawat jalan
            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // Ambil tabel master_provinsi
            $provinsi = $db->table('master_provinsi');
            $provinsi->select('UPPER(provinsiNama) AS provinsiNama');
            $provinsi->where('provinsiId', $form_persetujuan_tindakan['provinsi']);

            // Query untuk mendapatkan nama provinsi
            $res_provinsi = $provinsi->get()->getRow();

            if ($res_provinsi) {
                // Ubah ID menjadi nama provinsi
                $form_persetujuan_tindakan['provinsi'] = $res_provinsi->provinsiNama;
            }

            // Ambil tabel master_kabupaten
            $kabupaten = $db->table('master_kabupaten');
            $kabupaten->select('UPPER(kabupatenNama) AS kabupatenNama');
            $kabupaten->where('kabupatenId', $form_persetujuan_tindakan['kabupaten']);

            // Query untuk mendapatkan nama kabupaten
            $res_kabupaten = $kabupaten->get()->getRow();

            if ($res_kabupaten) {
                // Ubah ID menjadi nama kabupaten
                $form_persetujuan_tindakan['kabupaten'] = $res_kabupaten->kabupatenNama;
            }

            // Ambil tabel master_kecamatan
            $kecamatan = $db->table('master_kecamatan');
            $kecamatan->select('UPPER(kecamatanNama) AS kecamatanNama');
            $kecamatan->where('kecamatanId', $form_persetujuan_tindakan['kecamatan']);

            // Query untuk mendapatkan nama kecamatan
            $res_kecamatan = $kecamatan->get()->getRow();

            if ($res_kecamatan) {
                // Ubah ID menjadi nama kecamatan
                $form_persetujuan_tindakan['kecamatan'] = $res_kecamatan->kecamatanNama;
            }

            // Ambil tabel master_kelurahan
            $kelurahan = $db->table('master_kelurahan');
            $kelurahan->select('UPPER(kelurahanNama) AS kelurahanNama');
            $kelurahan->where('kelurahanId', $form_persetujuan_tindakan['kelurahan']);

            // Query untuk mendapatkan nama kelurahan
            $res_kelurahan = $kelurahan->get()->getRow();

            if ($res_kelurahan) {
                // Ubah ID menjadi nama kelurahan
                $form_persetujuan_tindakan['kelurahan'] = $res_kelurahan->kelurahanNama;
            }

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($form_persetujuan_tindakan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($form_persetujuan_tindakan) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'form_persetujuan_tindakan' => $form_persetujuan_tindakan,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Formulir Persetujuan Tindakan Kedokteran ' . $form_persetujuan_tindakan['nama_pasien'] . ' (' . $form_persetujuan_tindakan['no_rm'] . ') - ' . $form_persetujuan_tindakan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/frmsetujukedokteran/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/frmsetujukedokteran/form', $data);
                $filename = 'output-form-persetujuan-tindakan.pdf';

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

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            if ($form_persetujuan_tindakan) {
                $db = db_connect();

                // Menghapus form_persetujuan_tindakan
                $this->FRMSetujuKedokteranModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_form_persetujuan_tindakan` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Formulir persetujuan tindakan kedokteran berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Formulir persetujuan tindakan kedokteran tidak ditemukan', // Pesan jika peran tidak valid
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

            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan <', $id)
                ->orderBy('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan >', $id)
                ->orderBy('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $form_persetujuan_tindakan['no_rm'])
                ->orderBy('id_form_persetujuan_tindakan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'form_persetujuan_tindakan' => $form_persetujuan_tindakan,
                'dokter' => $dokter,
                'title' => 'Formulir Persetujuan Tindakan Kedokteran ' . $form_persetujuan_tindakan['nama_pasien'] . ' (' . $form_persetujuan_tindakan['no_rm'] . ') - ' . $form_persetujuan_tindakan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/frmsetujukedokteran/details', $data);
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
            $data = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
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
                'dokter_pelaksana' => [
                    'rules' => 'required',
                ],
                'pemberi_informasi' => [
                    'rules' => 'required',
                ],
                'penerima_informasi' => [
                    'rules' => 'required',
                ],
                'pererima_tanggal_lahir' => [
                    'rules' => 'required',
                ],
                'penerima_jenis_kelamin' => [
                    'rules' => 'required',
                ],
                'penerima_alamat' => [
                    'rules' => 'required',
                ],
                'penerima_hubungan' => [
                    'rules' => 'required',
                ],
                'keterangan_hubungan' => [
                    'rules' => $this->request->getPost('penerima_hubungan') === 'KELUARGA' ? 'required' : 'permit_empty',
                ],
                'tindakan_kedoteran' => [
                    'rules' => 'required',
                ],
                'tanggal_tindakan' => [
                    'rules' => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'dokter_pelaksana' => $this->request->getPost('dokter_pelaksana') ?: null,
                'pemberi_informasi' => $this->request->getPost('pemberi_informasi') ?: null,

                'info_diagnosa' => $this->request->getPost('info_diagnosa') ?: null,
                'info_dasar_diagnosis' => $this->request->getPost('info_dasar_diagnosis') ?: null,
                'info_tindakan' => $this->request->getPost('info_tindakan') ?: null,
                'info_indikasi' => $this->request->getPost('info_indikasi') ?: null,
                'info_tatacara' => $this->request->getPost('info_tatacara') ?: null,
                'info_tujuan' => $this->request->getPost('info_tujuan') ?: null,
                'info_resiko' => $this->request->getPost('info_resiko') ?: null,
                'info_komplikasi' => $this->request->getPost('info_komplikasi') ?: null,
                'info_prognosis' => $this->request->getPost('info_prognosis') ?: null,
                'info_alternatif' => $this->request->getPost('info_alternatif') ?: null,
                'info_lainnya' => $this->request->getPost('info_lainnya') ?: null,

                'penerima_informasi' => $this->request->getPost('penerima_informasi') ?: null,
                'pererima_tanggal_lahir' => $this->request->getPost('pererima_tanggal_lahir') ?: null,
                'penerima_jenis_kelamin' => $this->request->getPost('penerima_jenis_kelamin') ?: null,
                'penerima_alamat' => $this->request->getPost('penerima_alamat') ?: null,
                'penerima_hubungan' => $this->request->getPost('penerima_hubungan') ?: null,
                'keterangan_hubungan' => $this->request->getPost('keterangan_hubungan') ?: null,

                'tindakan_kedoteran' => $this->request->getPost('tindakan_kedoteran') ?: null,
                'tanggal_tindakan' => $this->request->getPost('tanggal_tindakan') ?: null,
                'nama_saksi_1' => $this->request->getPost('nama_saksi_1') ?: null,
                'nama_saksi_2' => $this->request->getPost('nama_saksi_2') ?: null,
            ];
            $db->table('medrec_form_persetujuan_tindakan')->where('id_form_persetujuan_tindakan', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Formulir persetujuan tindakan kedokteran berhasil diperbarui']); // Mengembalikan respon sukses
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
