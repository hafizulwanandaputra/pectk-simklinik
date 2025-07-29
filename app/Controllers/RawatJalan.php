<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RawatJalanModel;
use App\Models\PoliklinikModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;


class RawatJalan extends BaseController
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Rawat Jalan - ' . $this->systemName,
                'headertitle' => 'Rawat Jalan',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/rawatjalan/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function rawatjalanlisttanggal()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Admisi') {
            $db = db_connect();

            $tanggal = $this->request->getGet('tanggal');
            $limit = (int) $this->request->getGet('limit');
            $offset = (int) $this->request->getGet('offset');

            if (!$tanggal && !$limit && !$offset) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Parameter tidak lengkap',
                ]);
            }

            // Gunakan builder untuk fleksibilitas lebih
            $builder = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');

            if ($tanggal) {
                $builder->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Clone builder untuk total dan status
            $totalBuilder = clone $builder;
            $didaftarkanBuilder = clone $builder;
            $dibatalkanBuilder = clone $builder;

            $total = $totalBuilder->countAllResults(false);
            $didaftarkan = $didaftarkanBuilder->where('rawat_jalan.status', 'DAFTAR')->countAllResults(false);
            $dibatalkan = $dibatalkanBuilder->where('rawat_jalan.status', 'BATAL')->countAllResults(false);

            $Pasien = $builder->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            $startNumber = $offset + 1;

            // Ambil master jaminan
            $jaminanList = $db->table('master_jaminan')
                ->select('jaminanKode, jaminanNama')
                ->get()
                ->getResultArray();

            $jaminanMap = [];
            foreach ($jaminanList as $jaminan) {
                $jaminanMap[$jaminan['jaminanKode']] = $jaminan['jaminanNama'];
            }

            $totalPasien = count($Pasien);

            // Mapping data pasien
            foreach ($Pasien as $i => &$rajal) {
                $rajal['number'] = $startNumber + ($totalPasien - 1 - $i); // urutan angka dibalik
                $rajal['jaminan'] = isset($jaminanMap[$rajal['jaminan']]) ? $jaminanMap[$rajal['jaminan']] : 'Tidak Diketahui';
            }
            unset($rajal); // putuskan referensi

            return $this->response->setJSON([
                'data' => $Pasien,
                'total' => (int) $total,
                'didaftarkan' => (int) $didaftarkan,
                'dibatalkan' => (int) $dibatalkan,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function rawatjalanlistrm()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Admisi') {
            $db = db_connect();

            $no_rm = $this->request->getGet('no_rm');
            $limit = (int) $this->request->getGet('limit');
            $offset = (int) $this->request->getGet('offset');

            if (!$no_rm && !$limit && !$offset) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Parameter tidak lengkap',
                ]);
            }

            // Gunakan query builder
            $builder = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');

            if ($no_rm) {
                $builder->like('pasien.no_rm', $no_rm);
            }

            // Duplikat builder untuk menghitung jumlah
            $totalBuilder = clone $builder;
            $didaftarkanBuilder = clone $builder;
            $dibatalkanBuilder = clone $builder;

            $total = $totalBuilder->countAllResults(false);
            $didaftarkan = $didaftarkanBuilder->where('rawat_jalan.status', 'DAFTAR')->countAllResults(false);
            $dibatalkan = $dibatalkanBuilder->where('rawat_jalan.status', 'BATAL')->countAllResults(false);

            $Pasien = $builder->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            $startNumber = $offset + 1;

            // Ambil data jaminan
            $jaminanList = $db->table('master_jaminan')
                ->select('jaminanKode, jaminanNama')
                ->get()
                ->getResultArray();

            $jaminanMap = [];
            foreach ($jaminanList as $jaminan) {
                $jaminanMap[$jaminan['jaminanKode']] = $jaminan['jaminanNama'];
            }

            $totalPasien = count($Pasien);

            // Mapping data pasien
            foreach ($Pasien as $i => &$rajal) {
                $rajal['number'] = $startNumber + ($totalPasien - 1 - $i); // urutan angka dibalik
                $rajal['jaminan'] = isset($jaminanMap[$rajal['jaminan']]) ? $jaminanMap[$rajal['jaminan']] : 'Tidak Diketahui';
            }
            unset($rajal); // putuskan referensi

            return $this->response->setJSON([
                'data' => $Pasien,
                'total' => (int) $total,
                'didaftarkan' => (int) $didaftarkan,
                'dibatalkan' => (int) $dibatalkan,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function rawatjalanlistnama()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Admisi') {
            $db = db_connect();

            $nama = $this->request->getGet('nama');
            $limit = (int) $this->request->getGet('limit');
            $offset = (int) $this->request->getGet('offset');

            if (!$nama && !$limit && !$offset) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Parameter tidak lengkap',
                ]);
            }

            $builder = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');

            if ($nama) {
                $builder->like('pasien.nama_pasien', $nama); // pastikan field-nya sesuai di DB
            }

            // Clone builder untuk hitung total dan status
            $totalBuilder = clone $builder;
            $didaftarkanBuilder = clone $builder;
            $dibatalkanBuilder = clone $builder;

            $total = $totalBuilder->countAllResults(false);
            $didaftarkan = $didaftarkanBuilder->where('rawat_jalan.status', 'DAFTAR')->countAllResults(false);
            $dibatalkan = $dibatalkanBuilder->where('rawat_jalan.status', 'BATAL')->countAllResults(false);

            $Pasien = $builder->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            $startNumber = $offset + 1;

            // Ambil data jaminan dari master
            $jaminanList = $db->table('master_jaminan')
                ->select('jaminanKode, jaminanNama')
                ->get()
                ->getResultArray();

            $jaminanMap = [];
            foreach ($jaminanList as $jaminan) {
                $jaminanMap[$jaminan['jaminanKode']] = $jaminan['jaminanNama'];
            }

            $totalPasien = count($Pasien);

            // Mapping data pasien
            foreach ($Pasien as $i => &$rajal) {
                $rajal['number'] = $startNumber + ($totalPasien - 1 - $i); // urutan angka dibalik
                $rajal['jaminan'] = isset($jaminanMap[$rajal['jaminan']]) ? $jaminanMap[$rajal['jaminan']] : 'Tidak Diketahui';
            }
            unset($rajal); // putuskan referensi

            return $this->response->setJSON([
                'data' => $Pasien,
                'total' => (int) $total,
                'didaftarkan' => (int) $didaftarkan,
                'dibatalkan' => (int) $dibatalkan,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'jaminan' => 'required',
                'ruangan' => 'required',
                'dokter' => 'required',
                'keluhan' => 'required',
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            // Ambil data jaminan
            $jaminan = $this->request->getPost('jaminan');
            $jaminanData = $db->table('master_jaminan')->where('jaminanKode', $jaminan)->get()->getRow();

            if (!$jaminanData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jaminan tidak ditemukan']);
            }

            $jaminanKode = $jaminanData->jaminanKode;
            $jaminanAntrian = $jaminanData->jaminanAntrian;

            // Generate nomor_registrasi dengan format RJ[jaminanKode]ddmmyy-xxx
            $tanggal = date('dmy');
            $lastReg = $db->table('rawat_jalan')
                ->select('RIGHT(nomor_registrasi, 3) AS last_number')
                ->where('jaminan', $jaminan)
                ->where('tanggal_registrasi >=', date('Y-m-d 00:00:00'))
                ->where('tanggal_registrasi <=', date('Y-m-d 23:59:59'))
                ->orderBy('nomor_registrasi', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();

            $increment = $lastReg ? intval($lastReg->last_number) + 1 : 1;
            $nomor_registrasi = 'RJ' . $jaminanKode . $tanggal . '-' . str_pad($increment, 3, '0', STR_PAD_LEFT);

            // Ambil kode dokter
            $dokter = $this->request->getPost('dokter');
            $dokterData = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->where('fullname', $dokter)
                ->get()
                ->getRow();

            if (!$dokterData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Dokter tidak ditemukan']);
            }

            $kodeDokter = $dokterData->kode_antrian;

            // Query untuk mengambil nomor antrian terakhir berdasarkan dokter dan jaminan
            $lastQueue = $db->table('rawat_jalan')
                ->select('RIGHT(no_antrian, 3) AS last_number')
                ->where('dokter', $dokter)
                ->where('jaminan', $jaminan)
                ->where('tanggal_registrasi >=', date('Y-m-d 00:00:00'))
                ->where('tanggal_registrasi <=', date('Y-m-d 23:59:59'))
                ->orderBy('no_antrian', 'DESC')
                ->limit(1)
                ->get()
                ->getRow();

            // Jika ada data sebelumnya, ambil angka terakhir, jika tidak mulai dari 1
            $queueIncrement = $lastQueue ? intval($lastQueue->last_number) + 1 : 1;

            // Formatkan kode antrian dengan memastikan angka 3 digit
            $kode_antrian = $kodeDokter . $jaminanAntrian;
            $no_antrian = str_pad($queueIncrement, 3, '0', STR_PAD_LEFT);

            $totalrawatjalan = $db->table('rawat_jalan')
                ->where('no_rm', $no_rm)
                ->where('status', 'DAFTAR')
                ->countAllResults(false);

            // Kondisikan status kunjungan
            if ($totalrawatjalan > 0) {
                $status_kunjungan = 'LAMA';
            } else {
                $status_kunjungan = 'BARU';
            }

            // Simpan data pasien
            $data = [
                'no_rm' => $no_rm,
                'nomor_registrasi' => $nomor_registrasi,
                'tanggal_registrasi' => date('Y-m-d H:i:s'),
                'jenis_kunjungan' => NULL,
                'status_kunjungan' => $status_kunjungan,
                'jaminan' => $this->request->getPost('jaminan'),
                'ruangan' => $this->request->getPost('ruangan'),
                'dokter' => $this->request->getPost('dokter'),
                'keluhan' => $this->request->getPost('keluhan'),
                'alasan_batal' => NULL,
                'pembatal' => NULL,
                'kode_antrian' => $kode_antrian,
                'no_antrian' => $no_antrian,
                'pendaftar' => session()->get('fullname'),
                'status' => 'DAFTAR',
                'transaksi' => 0,
            ];
            $this->RawatJalanModel->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil diregistrasi']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function rawatjalan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi', 'Perawat', atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Perawat' || session()->get('role') == 'Dokter') {
            // Mengambil data pasien berdasarkan ID
            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id); // Mengambil pasien
            return $this->response->setJSON($data); // Mengembalikan data pasien dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function edit($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'edit_ruangan' => 'required',
                'edit_dokter' => 'required',
                'edit_keluhan' => 'required'
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            // Ambil rawat jalan
            $rajal = $db->table('rawat_jalan')
                ->where('id_rawat_jalan', $id)
                ->get()->getRowArray();

            if ($rajal['transaksi'] == 1) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Rawat jalan tidak dapat diedit karena transaksi sudah diproses']);
            }
            if ($rajal['status'] == 'BATAL') {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Rawat jalan tidak dapat diedit karena rawat jalan ini sudah dibatalkan']);
            }

            // Cek apakah nomor_registrasi digunakan di tabel resep atau transaksi
            $nomorReg = $rajal['nomor_registrasi'];

            $digunakanDiResepObat = $db->table('resep')->where('nomor_registrasi', $nomorReg)->countAllResults();
            $digunakanDiResepKacamata = $db->table('medrec_optik')->where('nomor_registrasi', $nomorReg)->countAllResults();
            $digunakanDiTransaksi = $db->table('transaksi')->where('nomor_registrasi', $nomorReg)->countAllResults();

            if ($digunakanDiResepObat > 0 || $digunakanDiResepKacamata > 0 || $digunakanDiTransaksi > 0) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Rawat jalan tidak dapat diedit karena rawat jalan ini sudah memiliki resep obat, resep kacamata, atau transaksi'
                ]);
            }

            // Simpan data pasien
            $data = [
                'ruangan' => $this->request->getPost('edit_ruangan'),
                'dokter' => $this->request->getPost('edit_dokter'),
                'keluhan' => $this->request->getPost('edit_keluhan'),
            ];
            // Update data menggunakan Query Builder
            $db->table('rawat_jalan')->where('id_rawat_jalan', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil diedit']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function editlembarisianoperasi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'tindakan_operasi_rajal' => 'required',
                'tanggal_operasi_rajal' => 'required'
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            // Ambil rawat jalan
            $rajal = $db->table('rawat_jalan')
                ->where('id_rawat_jalan', $id)
                ->get()->getRowArray();

            if ($rajal['transaksi'] == 1) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Lembar isian operasi tidak dapat diedit karena transaksi sudah diproses']);
            }
            if ($rajal['status'] == 'BATAL') {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Lembar isian operasi tidak dapat diedit karena rawat jalan ini sudah dibatalkan']);
            }

            // Simpan data pasien
            $data = [
                'tindakan_operasi_rajal' => $this->request->getPost('tindakan_operasi_rajal'),
                'tanggal_operasi_rajal' => $this->request->getPost('tanggal_operasi_rajal'),
                'jam_operasi_rajal' => $this->request->getPost('jam_operasi_rajal') ?: NULL,
            ];
            // Update data menggunakan Query Builder
            $db->table('rawat_jalan')->where('id_rawat_jalan', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Lembar isian operasi berhasil diedit']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function lembarisianoperasi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // ambil rajal berdasarkan ID
            $rajal = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('ruangan', 'Kamar Operasi')
                ->find($id);

            // Memeriksa apakah pasien tidak kosong
            if (!empty($rajal)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rajal' => $rajal,
                    'title' => 'Lembar Isian Operasi ' . $rajal['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                return view('dashboard/rawatjalan/lembarisianoperasi', $data);
                die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-lembarisianoperasi.html';
                file_put_contents($htmlFile, view('dashboard/rawatjalan/lembarisianoperasi', $data));

                $client = \Config\Services::curlrequest();
                $html = view('dashboard/rawatjalan/lembarisianoperasi', $data);
                $filename = 'output-lembarisianoperasi.pdf';

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

    public function struk($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // ambil rajal berdasarkan ID
            $rajal = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah pasien tidak kosong
            if (!empty($rajal)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rajal' => $rajal,
                    'title' => 'Struk ' . $rajal['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/struk', $data);
                // die;
                $client = \Config\Services::curlrequest();
                $html = view('dashboard/rawatjalan/struk', $data);
                $filename = 'output-struk.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'width' => '80mm',
                                'height' => '100mm',
                                'margin' => [
                                    'top' => '0.1cm',
                                    'right' => '0.82cm',
                                    'bottom' => '0.1cm',
                                    'left' => '0.82cm'
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

    public function cancel($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'alasan_batal' => 'required',
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            // Ambil rawat jalan
            $rajal = $db->table('rawat_jalan')
                ->where('id_rawat_jalan', $id)
                ->get()->getRowArray();

            if ($rajal['transaksi'] == 1) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Rawat jalan ini tidak dapat dibatalkan karena transaksi sudah diproses']);
            }
            if ($rajal['status'] == 'BATAL') {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Rawat jalan ini sudah dibatalkan sebelumnya']);
            }

            $rajalq = $db->table('rawat_jalan')
                ->where('id_rawat_jalan', $id);

            // Ambil nomor_registrasi dari data rawat_jalan sebelum dihapus
            $rawatJalanData = $rajalq->get()->getRow();
            $nomorRegistrasi = $rawatJalanData->nomor_registrasi ?? null;

            if ($this->request->getPost('alasan_batal') == 'HAPUS') {
                if ($nomorRegistrasi) {
                    // Hapus file terkait sebelum menghapus data
                    $asesmen_mata = $db->table('medrec_assesment_mata')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($asesmen_mata as $mata) {
                        if (!empty($mata['gambar']) && file_exists(FCPATH . 'uploads/asesmen_mata/' . $mata['gambar'])) {
                            @unlink(FCPATH . 'uploads/asesmen_mata/' . $mata['gambar']);
                        }
                    }

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

                    $penunjang_scan = $db->table('medrec_permintaan_penunjang_scan')
                        ->where('nomor_registrasi', $nomorRegistrasi)
                        ->get()
                        ->getResultArray();
                    foreach ($penunjang_scan as $scan) {
                        if (!empty($scan['gambar']) && file_exists(FCPATH . 'uploads/scan_penunjang/' . $scan['gambar'])) {
                            @unlink(FCPATH . 'uploads/scan_penunjang/' . $scan['gambar']);
                        }
                    }

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

                // Hapus data rawat jalan
                $db->table('rawat_jalan')->where('id_rawat_jalan', $id)->delete();

                // Reset auto_increment menjadi 1
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

                $this->notify_clients('delete');
                return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil dihapus karena kesalahan data']);
            }

            // Mengupdate status rawat jalan menjadi 'BATAL'
            $rajalq
                ->where('id_rawat_jalan', $id)->update([
                    'id_rawat_jalan' => $id,
                    'status' => 'BATAL',
                    'alasan_batal' => $this->request->getPost('alasan_batal'),
                    'pembatal' => session()->get('fullname')
                ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil dibatalkan']);
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

        return $this->response->setJSON([
            'status' => ucfirst($action) . ' notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
