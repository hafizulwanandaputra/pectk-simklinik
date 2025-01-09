<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RawatJalanModel;
use App\Models\PoliklinikModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Generator;

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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
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

    public function rawatjalanlist($tanggal)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Rekam Medis') {
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
                ->where('tanggal', $tanggal)
                ->groupEnd()
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner');

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

    public function create($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'jenis_kunjungan' => 'required',
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

            // Kondisikan status kunjungan
            if ($this->request->getPost('jenis_kunjungan') == 'BARU') {
                $status_kunjungan = 'BARU';
            } else {
                $status_kunjungan = 'LAMA';
            }

            // Simpan data pasien
            $data = [
                'no_rm' => $no_rm,
                'nomor_registrasi' => $nomor_registrasi,
                'tanggal_registrasi' => date('Y-m-d H:i:s'),
                'jenis_kunjungan' => $this->request->getPost('jenis_kunjungan'),
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

            return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil diregistrasi']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function struk($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/struk', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream($rajal['nomor_registrasi'] . '.pdf', [
                    'Attachment' => FALSE // Menghasilkan PDF tanpa mengunduh
                ]);
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function rawatjalan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
            // Mengambil data pasien berdasarkan ID
            $data = $this->RawatJalanModel->find($id); // Mengambil pasien
            return $this->response->setJSON($data); // Mengembalikan data pasien dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function kunjunganoptions()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
            // Mengambil jenis kunjungan dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
            // Mengambil jaminan dari tabel master jaminan
            $db = db_connect();
            $masterjaminan = $db->table('master_jaminan')
                ->where('jaminanStatus', 'AKTIF')
                ->get()->getRowArray();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data pengguna yang diterima
            foreach ($masterjaminan as $jaminan) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $jaminan['jaminanNama'], // Nilai untuk opsi
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Rekam Medis') {
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

    public function cancel($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Rawat jalan tidak dapat dibatalkan karena transaksi sudah diproses']);
            } else {
                $rajalq = $db->table('rawat_jalan')
                    ->where('id_rawat_jalan', $id);
                // Mengupdate status rawat jalan menjadi 'BATAL'
                $rajalq->update([
                    'status' => 'BATAL',
                    'alasan_batal' => $this->request->getPost('alasan_batal'),
                    'pembatal' => session()->get('fullname')
                ]);
                return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil dibatalkan']);
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
