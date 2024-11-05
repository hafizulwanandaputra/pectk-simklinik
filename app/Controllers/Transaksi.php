<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\LayananModel;
use App\Models\ResepModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use Dompdf\Dompdf;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Transaksi extends BaseController
{
    protected $TransaksiModel;
    protected $DetailTransaksiModel;
    public function __construct()
    {
        $this->TransaksiModel = new TransaksiModel();
        $this->DetailTransaksiModel = new DetailTransaksiModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Menyiapkan data untuk tampilan halaman kasir
            $data = [
                'title' => 'Kasir - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Kasir', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            return view('dashboard/transaksi/index', $data); // Mengembalikan tampilan halaman kasir
        } else {
            throw PageNotFoundException::forPageNotFound(); // Menampilkan halaman tidak ditemukan jika peran tidak valid
        }
    }

    public function listtransaksi()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil parameter dari permintaan GET
            $search = $this->request->getGet('search'); // Nilai pencarian
            $limit = $this->request->getGet('limit'); // Batas jumlah hasil
            $offset = $this->request->getGet('offset'); // Offset untuk pagination
            $status = $this->request->getGet('status'); // Status transaksi
            $jenis = $this->request->getGet('jenis'); // Status transaksi
            $names = $this->request->getGet('names');
            $kasir = $this->request->getGet('kasir'); // Status transaksi

            // Mengubah limit dan offset menjadi integer, jika tidak ada, set ke 0
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Mengambil model transaksi
            $TransaksiModel = $this->TransaksiModel;

            // Memilih semua kolom dari tabel transaksi
            $TransaksiModel->select('transaksi.*');

            // Menerapkan filter status jika ada
            if ($status === '1') {
                $TransaksiModel->where('lunas', 1); // Status lunas
            } elseif ($status === '0') {
                $TransaksiModel->where('lunas', 0); // Status belum lunas
            }

            // Menerapkan filter jenis jika ada
            if ($jenis === 'Resep Luar') {
                $TransaksiModel->where('dokter', 'Resep Luar'); // Resep Luar
            } elseif ($jenis === 'Resep Dokter') {
                $TransaksiModel->where('dokter !=', 'Resep Luar'); // Resep Dokter
            }

            // Menerapkan filter names jika disediakan
            if ($names === '1') {
                $TransaksiModel->where('nama_pasien IS NOT NULL'); // Pasien dengan nama
            } elseif ($names === '0') {
                $TransaksiModel->where('nama_pasien', NULL); // Pasien anonim
            }

            // Menerapkan filter pencarian berdasarkan nama pasien, kasir, atau tanggal transaksi
            if ($search) {
                $TransaksiModel
                    ->groupStart()
                    ->like('nama_pasien', $search) // Pencarian berdasarkan nama pasien
                    ->orLike('tgl_transaksi', $search) // Pencarian berdasarkan tanggal transaksi
                    ->groupEnd();
            }

            // Menerapkan filter untuk kasir jika disediakan
            if ($kasir) {
                $TransaksiModel->where('kasir', $kasir); // Menambahkan filter berdasarkan kasir
            }

            // Menghitung total hasil tanpa filter
            $total = $TransaksiModel->countAllResults(false);

            // Mengambil hasil transaksi dengan pagination
            $Transaksi = $TransaksiModel
                ->orderBy('id_transaksi', 'DESC') // Mengurutkan berdasarkan id_transaksi secara menurun
                ->findAll($limit, $offset); // Mengambil data dengan batas dan offset

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Mengolah setiap transaksi dan menghitung total_pembayaran
            $dataTransaksi = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menambahkan nomor urut
                $db = db_connect(); // Menghubungkan ke database

                // Menghitung total pembayaran dari detail_transaksi
                $builder = $db->table('detail_transaksi');
                $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
                $builder->where('id_transaksi', $data['id_transaksi']);
                $result = $builder->get()->getRow(); // Mengambil hasil dari query

                $total_pembayaran = $result->total_pembayaran; // Mengambil total pembayaran

                // Memperbarui tabel transaksi dengan total_pembayaran
                $transaksiBuilder = $db->table('transaksi');
                $transaksiBuilder->where('id_transaksi', $data['id_transaksi']);
                $transaksiBuilder->update([
                    'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran
                ]);
                return $data; // Mengembalikan data transaksi yang telah diproses
            }, $Transaksi, array_keys($Transaksi));

            // Mengembalikan respon JSON dengan data transaksi dan total hasil
            return $this->response->setJSON([
                'transaksi' => $dataTransaksi, // Data transaksi
                'total' => $total // Total hasil
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function kasirlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil kasir dari tabel transaksi
            $transaksiData = $this->TransaksiModel
                ->groupBy('kasir')
                ->orderBy('kasir', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data transaksi luar yang diterima
            foreach ($transaksiData as $transaksi) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $transaksi['kasir'], // Nilai untuk opsi
                    'text'  => $transaksi['kasir'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data transaksi luar dalam format JSON
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

    public function pasienlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $client = new Client(); // Membuat klien HTTP Guzzle baru

            try {
                // Mengirim permintaan GET ke API
                $response = $client->request('GET', env('API-URL') . date('Y-m-d'), [
                    'headers' => [
                        'Accept' => 'application/json',
                        'x-key' => env('X-KEY') // Mengatur header API
                    ],
                ]);

                // Mendekode JSON dan menangani potensi error
                $data = json_decode($response->getBody()->getContents(), true);

                // Mengambil nomor_registrasi yang sudah terpakai di transaksi
                $db = \Config\Database::connect();
                $usedNoReg = $db->table('transaksi')->select('nomor_registrasi')->get()->getResultArray();
                $usedNoReg = array_column($usedNoReg, 'nomor_registrasi');

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
            } catch (RequestException $e) {
                // Menangani error saat permintaan API
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Gagal mengambil data pasien: ' . $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function pasienlistexternal()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data dari tabel resep dengan status = 0 dan mengurutkan berdasarkan nomor_registrasi
            $ResepModel = new ResepModel();
            $resepData = $ResepModel
                ->groupStart()
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->groupEnd()
                ->orderBy('id_resep', 'DESC')
                ->findAll();

            // Mengambil id_resep yang sudah terpakai di transaksi
            $db = \Config\Database::connect();
            $usedResepIds = $db->table('transaksi')->select('id_resep')->get()->getResultArray();
            $usedResepIds = array_column($usedResepIds, 'id_resep');

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data resep luar yang diterima
            foreach ($resepData as $resep) {
                // Memeriksa apakah id_resep ada dalam daftar id yang terpakai
                if (in_array($resep['id_resep'], $usedResepIds)) {
                    continue; // Lewati resep yang sudah terpakai
                }

                if ($resep['nama_pasien'] == NULL) {
                    $nama_pasien = 'Anonim';
                } else {
                    $nama_pasien = $resep['nama_pasien'];
                }

                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $resep['id_resep'], // Nilai untuk opsi
                    'text'  => $nama_pasien . ' (ID ' . $resep['id_resep'] . ' - '  . $resep['tanggal_lahir'] . ')' // Teks untuk opsi
                ];
            }

            // Mengembalikan data resep luar dalam format JSON
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

    public function transaksi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data transaksi berdasarkan id
            $data = $this->TransaksiModel->find($id);
            return $this->response->setJSON($data); // Mengembalikan data dalam format JSON
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
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

            // Mengambil data dari API eksternal menggunakan Guzzle
            $client = new Client();
            try {
                // Mengirim permintaan GET ke API
                $response = $client->request('GET', env('API-URL') . date('Y-m-d'), [
                    'headers' => [
                        'x-key' => env('X-KEY'), // Mengatur header API
                    ],
                ]);

                // Mendekode JSON yang diterima dari API
                $dataFromApi = json_decode($response->getBody(), true);

                // Memeriksa apakah data mengandung nomor registrasi yang diminta
                $resepData = null;
                foreach ($dataFromApi as $patient) {
                    if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                        $resepData = $patient; // Menyimpan data pasien jika ditemukan
                        break;
                    }
                }

                // Jika data pasien tidak ditemukan
                if (!$resepData) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Data pasien tidak ditemukan', 'errors' => NULL]);
                }

                // Mendapatkan tanggal saat ini
                $date = new \DateTime();
                $tanggal = $date->format('d'); // Hari (2 digit)
                $bulan = $date->format('m'); // Bulan (2 digit)
                $tahun = $date->format('y'); // Tahun (2 digit)

                // Mengambil nomor registrasi terakhir untuk di-increment
                $lastNoReg = $this->TransaksiModel->getLastNoReg($tahun, $bulan, $tanggal);
                $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -4)) : 0; // Mendapatkan nomor terakhir
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Menyiapkan nomor berikutnya

                // Memformat nomor kwitansi
                $no_kwitansi = sprintf('TRJ%s%s%s-%s', $tanggal, $bulan, $tahun, $nextNumber);

                // Menyimpan data transaksi
                $data = [
                    'id_resep' => NULL, // ID resep diatur ke NULL
                    'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                    'no_rm' => $resepData['no_rm'], // Nomor rekam medis
                    'nama_pasien' => $resepData['nama_pasien'], // Nama pasien
                    'alamat' => $resepData['alamat'], // Alamat pasien
                    'telpon' => $resepData['telpon'], // Nomor telepon pasien
                    'jenis_kelamin' => $resepData['jenis_kelamin'], // Jenis kelamin pasien
                    'tempat_lahir' => $resepData['tempat_lahir'], // Tempat lahir pasien
                    'tanggal_lahir' => $resepData['tanggal_lahir'], // Tanggal lahir pasien
                    'dokter' => $resepData['dokter'], // Tanggal lahir pasien
                    'kasir' => session()->get('fullname'), // Nama kasir dari session
                    'no_kwitansi' => $no_kwitansi, // Nomor kwitansi
                    'tgl_transaksi' => date('Y-m-d H:i:s'), // Tanggal dan waktu transaksi
                    'total_pembayaran' => 0, // Total pembayaran awal
                    'metode_pembayaran' => '', // Metode pembayaran (kosong pada awalnya)
                    'lunas' => 0, // Status lunas (0 berarti belum lunas)
                ];
                $this->TransaksiModel->save($data); // Menyimpan data transaksi ke database
                return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil ditambahkan']); // Mengembalikan respon sukses
            } catch (\Exception $e) {
                // Menangani kesalahan saat mengambil data
                return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(), 'errors' => NULL]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function createexternal()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Melakukan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_resep' => 'required', // Nomor registrasi wajib diisi
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]); // Mengembalikan kesalahan validasi
            }

            // Mengambil nomor registrasi dari permintaan POST
            $id_resep = $this->request->getPost('id_resep');

            // Mengambil data dari tabel resep berdasarkan nomor registrasi
            $ResepModel = new ResepModel();
            $resepData = $ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->where('status', 0)
                ->where('id_resep', $id_resep)
                ->get()->getRowArray();

            // Jika data pasien tidak ditemukan
            if (!$resepData) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data resep tidak ditemukan', 'errors' => NULL]);
            }

            // Mendapatkan tanggal saat ini
            $date = new \DateTime();
            $tanggal = $date->format('d'); // Hari (2 digit)
            $bulan = $date->format('m'); // Bulan (2 digit)
            $tahun = $date->format('y'); // Tahun (2 digit)

            // Mengambil nomor registrasi terakhir untuk di-increment
            $lastNoReg = $this->TransaksiModel->getLastNoReg($tahun, $bulan, $tanggal);
            $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -4)) : 0; // Mendapatkan nomor terakhir
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Menyiapkan nomor berikutnya

            // Memformat nomor kwitansi
            $no_kwitansi = sprintf('TRJ%s%s%s-%s', $tanggal, $bulan, $tahun, $nextNumber);

            // Menyimpan data transaksi
            $data = [
                'id_resep' => $id_resep, // ID resep
                'nomor_registrasi' => $resepData['nomor_registrasi'], // Nomor registrasi
                'no_rm' => $resepData['no_rm'], // Nomor rekam medis
                'nama_pasien' => $resepData['nama_pasien'], // Nama pasien
                'alamat' => $resepData['alamat'], // Alamat pasien
                'telpon' => $resepData['telpon'], // Nomor telepon pasien
                'jenis_kelamin' => $resepData['jenis_kelamin'], // Jenis kelamin pasien
                'tempat_lahir' => $resepData['tempat_lahir'], // Tempat lahir pasien
                'tanggal_lahir' => $resepData['tanggal_lahir'], // Tanggal lahir pasien
                'dokter' => 'Resep Luar', // Tanggal lahir pasien
                'kasir' => session()->get('fullname'), // Nama kasir dari session
                'no_kwitansi' => $no_kwitansi, // Nomor kwitansi
                'tgl_transaksi' => date('Y-m-d H:i:s'), // Tanggal dan waktu transaksi
                'total_pembayaran' => 0, // Total pembayaran awal
                'metode_pembayaran' => '', // Metode pembayaran (kosong pada awalnya)
                'lunas' => 0, // Status lunas (0 berarti belum lunas)
            ];
            $this->TransaksiModel->save($data); // Menyimpan data transaksi ke database
            return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil ditambahkan']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $transaksi = $this->TransaksiModel->find($id);
            if ($transaksi['lunas'] == 0) {
                $db = db_connect();
                // Mencari semua `id_resep` yang terkait dengan transaksi yang akan dihapus
                $query = $db->query('SELECT DISTINCT id_resep FROM detail_transaksi WHERE id_transaksi = ?', [$id]);
                $results = $query->getResult(); // Mengambil hasil query

                if (!empty($results)) {
                    // Loop melalui setiap `id_resep` terkait dan memperbarui statusnya menjadi 0
                    foreach ($results as $row) {
                        $db->query('UPDATE resep SET status = 0 WHERE id_resep = ?', [$row->id_resep]);
                    }
                }

                // Menghapus transaksi
                $this->TransaksiModel->delete($id);

                // Reset auto increment untuk tabel transaksi dan detail_transaksi
                $db->query('ALTER TABLE `transaksi` auto_increment = 1');
                $db->query('ALTER TABLE `detail_transaksi` auto_increment = 1');

                return $this->response->setJSON(['message' => 'Transaksi berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(401)->setJSON(['message' => 'Transaksi yang sudah lunas tidak bisa dihapus. Batalkan transaksi terlebih dahulu sebelum menghapus transaksi ini.']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    // DETAIL TRANSAKSI
    public function detailtransaksi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data transaksi berdasarkan ID
            $transaksi = $this->TransaksiModel->find($id);
            $LayananModel = new LayananModel();
            // Mengambil jenis layanan yang dikelompokkan
            $layanan = $LayananModel->select('jenis_layanan')->groupBy('jenis_layanan')->findAll();

            // Memeriksa apakah transaksi ditemukan
            if (!empty($transaksi)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'transaksi' => $transaksi,
                    'layanan' => $layanan,
                    'title' => 'Detail Transaksi ' . $transaksi['no_kwitansi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Detail Transaksi',
                    'agent' => $this->request->getUserAgent()
                ];
                // Mengembalikan tampilan detail transaksi
                return view('dashboard/transaksi/details', $data);
            } else {
                // Jika transaksi tidak ditemukan, lempar pengecualian
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak valid, lempar pengecualian
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detaillayananlist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil daftar layanan berdasarkan ID transaksi
            $layanan = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Memetakan setiap transaksi
            foreach ($layanan as $row) {
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'nama_layanan' => $row['nama_layanan'],
                    ];
                }
            }

            // Mengembalikan hasil dalam bentuk JSON
            return $this->response->setJSON(array_values($result));
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailobatalkeslist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil daftar obat dan alkes berdasarkan ID transaksi
            $obatalkes = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur
            $result = [];

            // Memetakan setiap transaksi
            foreach ($obatalkes as $row) {
                // Jika transaksi ini belum ada dalam array $result, tambahkan
                if (!isset($result[$row['id_detail_transaksi']])) {
                    $result[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
                            'detail_resep' => []
                        ],
                    ];
                }

                // Tambahkan detail_resep ke transaksi
                $result[$row['id_detail_transaksi']]['resep']['detail_resep'][] = [
                    'id_detail_resep' => $row['id_detail_resep'],
                    'id_resep' => $row['id_resep'],
                    'id_obat' => $row['id_obat'],
                    'nama_obat' => $row['nama_obat'],
                    'kategori_obat' => $row['kategori_obat'],
                    'bentuk_obat' => $row['bentuk_obat'],
                    'signa' => $row['signa'],
                    'catatan' => $row['catatan'],
                    'cara_pakai' => $row['cara_pakai'],
                    'jumlah' => $row['jumlah'],
                    'harga_satuan' => $row['harga_satuan']
                ];
            }

            // Mengembalikan hasil dalam bentuk JSON
            return $this->response->setJSON(array_values($result));
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailtransaksiitem($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data detail transaksi berdasarkan ID
            $data = $this->DetailTransaksiModel
                ->where('id_detail_transaksi', $id)
                ->orderBy('id_detail_transaksi', 'ASC')
                ->find($id);

            // Mengembalikan data dalam bentuk JSON
            return $this->response->setJSON($data);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function layananlist($id_transaksi, $jenis_layanan = null)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $LayananModel = new LayananModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            // Filter layanan berdasarkan jenis_layanan jika parameter diberikan
            if ($jenis_layanan) {
                $LayananModel->where('jenis_layanan', $jenis_layanan);
            }

            // Mengambil semua layanan yang telah difilter
            $results = $LayananModel
                ->orderBy('layanan.id_layanan', 'ASC')
                ->findAll();

            $options = [];
            // Memetakan hasil layanan ke dalam format yang diinginkan
            foreach ($results as $row) {
                $tarif = (int) $row['tarif']; // Mengonversi tarif ke integer
                $tarif_terformat = number_format($tarif, 0, ',', '.'); // Memformat tarif

                // Memeriksa apakah layanan sudah digunakan dalam transaksi
                $isUsed = $DetailTransaksiModel->where('id_layanan', $row['id_layanan'])
                    ->where('id_transaksi', $id_transaksi)
                    ->first();

                // Jika layanan belum digunakan, tambahkan ke opsi
                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_layanan'], // ID layanan
                        'text' => $row['nama_layanan'] . ' (Rp' . $tarif_terformat . ')' // Nama layanan dengan tarif terformat
                    ];
                }
            }

            // Mengembalikan opsi layanan dalam bentuk JSON
            return $this->response->setJSON($options);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function reseplist($id_transaksi, $nomor_registrasi)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $ResepModel = new ResepModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            // Mengambil resep berdasarkan nomor registrasi dengan kondisi tertentu
            $results = $ResepModel
                ->where('nomor_registrasi', $nomor_registrasi)
                ->where('confirmed', 1) // Mengambil resep yang sudah dikonfirmasi
                ->where('status', 0) // Mengambil resep yang statusnya 0
                ->where('total_biaya >', 0) // Mengambil resep dengan total biaya lebih dari 0
                ->orderBy('resep.id_resep', 'DESC')->findAll();

            $options = [];
            // Memetakan hasil resep ke dalam format yang diinginkan
            foreach ($results as $row) {
                $total_biaya = (int) $row['total_biaya']; // Mengonversi total biaya ke integer
                $total_biaya_terformat = number_format($total_biaya, 0, ',', '.'); // Memformat total biaya

                // Memeriksa apakah resep sudah digunakan dalam transaksi
                $isUsed = $DetailTransaksiModel->where('id_resep', $row['id_resep'])
                    ->where('id_transaksi', $id_transaksi)
                    ->first();

                // Jika resep belum digunakan, tambahkan ke opsi
                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_resep'], // ID resep
                        'text' => $row['tanggal_resep'] . ' (Rp' . $total_biaya_terformat . ')' // Tanggal resep dengan total biaya terformat
                    ];
                }
            }

            // Mengembalikan opsi resep dalam bentuk JSON
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function reseplistexternal($id_transaksi, $id_resep)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $ResepModel = new ResepModel();
            $DetailTransaksiModel = new DetailTransaksiModel();

            // Mengambil resep berdasarkan nomor registrasi dengan kondisi tertentu
            $results = $ResepModel
                ->where('id_resep', $id_resep)
                ->where('confirmed', null)
                ->where('status', 0) // Mengambil resep yang statusnya 0
                ->where('total_biaya >', 0) // Mengambil resep dengan total biaya lebih dari 0
                ->orderBy('resep.id_resep', 'DESC')->findAll();

            $options = [];
            // Memetakan hasil resep ke dalam format yang diinginkan
            foreach ($results as $row) {
                $total_biaya = (int) $row['total_biaya']; // Mengonversi total biaya ke integer
                $total_biaya_terformat = number_format($total_biaya, 0, ',', '.'); // Memformat total biaya

                // Memeriksa apakah resep sudah digunakan dalam transaksi
                $isUsed = $DetailTransaksiModel->where('id_resep', $id_resep)
                    ->where('id_transaksi', $id_transaksi)
                    ->first();

                // Jika resep belum digunakan, tambahkan ke opsi
                if (!$isUsed) {
                    $options[] = [
                        'value' => $id_resep, // ID resep
                        'text' => $row['tanggal_resep'] . ' (Rp' . $total_biaya_terformat . ')' // Tanggal resep dengan total biaya terformat
                    ];
                }
            }

            // Mengembalikan opsi resep dalam bentuk JSON
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahlayanan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_layanan' => 'required', // ID layanan harus diisi
                'qty_transaksi' => 'required|numeric|greater_than[0]', // Kuantitas harus diisi, berupa angka, dan lebih dari 0
                'diskon_layanan' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]', // Diskon harus diisi, berupa angka, antara 0 dan 100
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $transaksib = $db->table('transaksi');
            $transaksib->where('id_transaksi', $id);
            $transaksi = $transaksib->get()->getRowArray();

            if ($transaksi['lunas'] == 1) {
                // Gagalkan jika transaksi lunas
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Batalkan transaksi terlebih dahulu.']);
            }

            $LayananModel = new LayananModel();
            // Mengambil data layanan berdasarkan ID yang diberikan
            $layanan = $LayananModel->find($this->request->getPost('id_layanan'));

            // Menyimpan data transaksi layanan
            $data = [
                'id_resep' => NULL,
                'id_layanan' => $this->request->getPost('id_layanan'),
                'id_transaksi' => $id,
                'nama_layanan' => $layanan['nama_layanan'],
                'jenis_transaksi' => 'Tindakan',
                'qty_transaksi' => $this->request->getPost('qty_transaksi'),
                'harga_transaksi' => $layanan['tarif'], // Menggunakan tarif dari layanan
                'diskon' => $this->request->getPost('diskon_layanan'),
            ];
            // Menyimpan data ke DetailTransaksiModel
            $this->DetailTransaksiModel->save($data);

            // Menghitung total pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran; // Total pembayaran yang dihitung

            // Memperbarui tabel transaksi
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran di tabel transaksi
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil ditambahkan']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahobatalkes($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_resep' => 'required', // ID resep harus diisi
                'diskon_obatalkes' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]', // Diskon harus diisi, berupa angka, antara 0 dan 100
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $transaksib = $db->table('transaksi');
            $transaksib->where('id_transaksi', $id);
            $transaksi = $transaksib->get()->getRowArray();

            if ($transaksi['lunas'] == 1) {
                // Gagalkan jika transaksi lunas
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Batalkan transaksi terlebih dahulu.']);
            }

            $ResepModel = new ResepModel();
            // Mengambil data resep berdasarkan ID yang diberikan
            $resep = $ResepModel->find($this->request->getPost('id_resep'));

            // Menyimpan data transaksi obat dan alkes
            $data = [
                'id_resep' => $this->request->getPost('id_resep'),
                'id_layanan' => NULL,
                'id_transaksi' => $id,
                'nama_layanan' => NULL,
                'jenis_transaksi' => 'Obat dan Alkes',
                'qty_transaksi' => 1, // Kuantitas untuk obat dan alkes ditetapkan 1
                'harga_transaksi' => $resep['total_biaya'], // Menggunakan total biaya dari resep
                'diskon' => $this->request->getPost('diskon_obatalkes'),
            ];
            // Menyimpan data ke DetailTransaksiModel
            $this->DetailTransaksiModel->save($data);

            // Menghitung total pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran; // Total pembayaran yang dihitung

            // Memperbarui tabel transaksi
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran di tabel transaksi
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil ditambahkan']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruilayanan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'qty_transaksi_edit' => 'required|numeric|greater_than[0]', // Kuantitas harus diisi, berupa angka, dan lebih dari 0
                'diskon_layanan_edit' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]', // Diskon harus diisi, berupa angka, antara 0 dan 100
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $transaksib = $db->table('transaksi');
            $transaksib->where('id_transaksi', $id);
            $transaksi = $transaksib->get()->getRowArray();

            if ($transaksi['lunas'] == 1) {
                // Gagalkan jika transaksi lunas
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Batalkan transaksi terlebih dahulu.']);
            }

            // Mengambil detail transaksi berdasarkan ID yang diberikan
            $detail_transaksi = $this->DetailTransaksiModel->find($this->request->getPost('id_detail_transaksi'));

            // Menyimpan data yang diperbarui
            $data = [
                'id_detail_transaksi' => $this->request->getPost('id_detail_transaksi'),
                'id_resep' => NULL,
                'id_layanan' => $detail_transaksi['id_layanan'], // Menggunakan ID layanan yang ada
                'id_transaksi' => $id,
                'nama_layanan' => $detail_transaksi['nama_layanan'],
                'jenis_transaksi' => $detail_transaksi['jenis_transaksi'],
                'qty_transaksi' => $this->request->getPost('qty_transaksi_edit'), // Menggunakan kuantitas yang diperbarui
                'harga_transaksi' => $detail_transaksi['harga_transaksi'],
                'diskon' => $this->request->getPost('diskon_layanan_edit'), // Menggunakan diskon yang diperbarui
            ];
            // Menyimpan data ke DetailTransaksiModel
            $this->DetailTransaksiModel->save($data);

            // Menghitung total pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran; // Total pembayaran yang dihitung

            // Memperbarui tabel transaksi
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran di tabel transaksi
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil diperbarui']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruiobatalkes($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'diskon_obatalkes_edit' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]', // Diskon harus diisi, berupa angka, antara 0 dan 100
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $transaksib = $db->table('transaksi');
            $transaksib->where('id_transaksi', $id);
            $transaksi = $transaksib->get()->getRowArray();

            if ($transaksi['lunas'] == 1) {
                // Gagalkan jika transaksi lunas
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Batalkan transaksi terlebih dahulu.']);
            }

            // Mengambil detail transaksi berdasarkan ID yang diberikan
            $detail_transaksi = $this->DetailTransaksiModel->find($this->request->getPost('id_detail_transaksi'));

            // Menyimpan data yang diperbarui
            $data = [
                'id_detail_transaksi' => $this->request->getPost('id_detail_transaksi'),
                'id_resep' => $detail_transaksi['id_resep'], // Menggunakan ID resep yang ada
                'id_layanan' => NULL,
                'id_transaksi' => $id,
                'nama_layanan' => NULL,
                'jenis_transaksi' => $detail_transaksi['jenis_transaksi'],
                'qty_transaksi' => $detail_transaksi['qty_transaksi'], // Menggunakan kuantitas yang ada
                'harga_transaksi' => $detail_transaksi['harga_transaksi'],
                'diskon' => $this->request->getPost('diskon_obatalkes_edit'), // Menggunakan diskon yang diperbarui
            ];
            // Menyimpan data ke DetailTransaksiModel
            $this->DetailTransaksiModel->save($data);

            // Menghitung total pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran; // Total pembayaran yang dihitung

            // Memperbarui tabel transaksi
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran di tabel transaksi
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item transaksi berhasil diperbarui']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailtransaksi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $db = db_connect();

            // Mencari detail pembelian obat sebelum penghapusan untuk mendapatkan id_transaksi
            $detail = $this->DetailTransaksiModel->find($id);

            $id_transaksi = $detail['id_transaksi']; // Mengambil id_transaksi dari detail yang ditemukan

            $transaksib = $db->table('transaksi');
            $transaksib->where('id_transaksi', $id_transaksi);
            $transaksi = $transaksib->get()->getRowArray();

            if ($transaksi['lunas'] == 1) {
                // Gagalkan jika transaksi lunas
                return $this->response->setStatusCode(401)->setJSON(['message' => 'Tidak bisa dilakukan. Batalkan transaksi terlebih dahulu.']);
            }

            // Menghapus detail pembelian obat
            $this->DetailTransaksiModel->delete($id);

            // Reset auto_increment (opsional, tergantung kebutuhan)
            $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

            // Menghitung total pembayaran
            $builder = $db->table('detail_transaksi');
            $builder->select('SUM((harga_transaksi * qty_transaksi) * (1 - (diskon / 100))) as total_pembayaran');
            $builder->where('id_transaksi', $id_transaksi);
            $result = $builder->get()->getRow();

            $total_pembayaran = $result->total_pembayaran; // Total pembayaran yang dihitung

            // Memperbarui tabel transaksi
            $transaksiBuilder = $db->table('transaksi');
            $transaksiBuilder->where('id_transaksi', $id_transaksi);
            $transaksiBuilder->update([
                'total_pembayaran' => $total_pembayaran, // Memperbarui total pembayaran di tabel transaksi
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['message' => 'Item transaksi berhasil dihapus']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function process($id_transaksi)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $rules = [
                'terima_uang' => 'required|numeric|greater_than_equal_to[0]', // Uang yang diterima harus diisi, berupa angka, dan lebih dari 0
                'metode_pembayaran' => 'required', // Metode pembayaran harus diisi
            ];

            // Cek jika metode pembayaran adalah 'QRIS/Transfer Bank'
            if ($this->request->getPost('metode_pembayaran') == 'QRIS/Transfer Bank') {
                // Tambahkan aturan tambahan jika diperlukan (misalnya validasi terkait bank)
                $rules['bank'] = 'required'; // Nama bank harus diisi
            }

            // Terapkan aturan validasi
            $validation->setRules($rules);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $terima_uang = $this->request->getPost('terima_uang'); // Mengambil jumlah uang yang diterima

            $db = db_connect();
            $db->transBegin();  // Memulai transaksi

            // Mengambil total pembayaran dari tabel transaksi
            $transaksi = $db->table('transaksi')
                ->select('total_pembayaran')
                ->where('id_transaksi', $id_transaksi)
                ->get()
                ->getRow();

            $total_pembayaran = $transaksi->total_pembayaran; // Total pembayaran yang diambil

            if ($this->request->getPost('metode_pembayaran') == 'QRIS/Transfer Bank') {
                // Memeriksa apakah uang yang diterima sama dengan total pembayaran
                if ($terima_uang != $total_pembayaran) {
                    return $this->response->setStatusCode(402)->setJSON(['success' => false, 'message' => 'Uang yang diterima harus sama dengan total pembayaran pada metode QRIS/Transfer Bank', 'errors' => NULL]);
                }
            } else {
                // Memeriksa apakah uang yang diterima kurang dari total pembayaran
                if ($terima_uang < $total_pembayaran) {
                    return $this->response->setStatusCode(402)->setJSON(['success' => false, 'message' => 'Uang yang diterima kurang dari total pembayaran', 'errors' => NULL]);
                }
            }

            // Menghitung uang kembali jika uang yang diterima lebih besar dari total pembayaran
            $uang_kembali = $terima_uang > $total_pembayaran ? $terima_uang - $total_pembayaran : 0;


            // Jika bank kosong, atur ke NULL
            if ($this->request->getPost('bank') == '') {
                $bank = NULL;
            } else {
                $bank = $this->request->getPost('bank');
            }

            // Memperbarui transaksi
            $transaksi = $db->table('transaksi');
            $transaksi->where('id_transaksi', $id_transaksi);
            $transaksi->update([
                'terima_uang' => $terima_uang, // Memperbarui jumlah uang yang diterima
                'metode_pembayaran' => $this->request->getPost('metode_pembayaran'), // Memperbarui metode pembayaran
                'bank' => $bank, // Menambahkan bank
                'uang_kembali' => $uang_kembali, // Memperbarui uang kembali
                'lunas' => 1, // Menandai transaksi sebagai lunas
            ]);

            // Mengambil detail transaksi
            $detailtransaksi = $db->table('detail_transaksi');
            $detailtransaksi->where('id_transaksi', $id_transaksi);
            $details = $detailtransaksi->get()->getResultArray(); // Mengambil semua detail transaksi

            // Memperbarui status resep jika ada
            if ($details) {
                foreach ($details as $detail) {
                    if ($detail['id_resep'] !== null) { // Memeriksa apakah ada ID resep
                        $resep = $db->table('resep');
                        $resep->where('id_resep', $detail['id_resep']); // Memastikan mencocokkan berdasarkan id_resep
                        $resep->update([
                            'status' => 1, // Memperbarui status resep menjadi selesai
                        ]);
                    }
                }
            }

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();  // Rollback jika ada masalah
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal memproses transaksi', 'errors' => NULL]);
            } else {
                $db->transCommit();  // Commit transaksi jika semuanya baik-baik saja
                return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil diproses. Silakan cetak struk transaksi.']);
            }
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function cancel($id_transaksi)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $rules = [
                'password' => 'required', // Kata sandi pembatalan transaksi
            ];

            // Terapkan aturan validasi
            $validation->setRules($rules);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan kesalahan validasi jika tidak valid
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $password = $this->request->getPost('password'); // Mengambil kata sandi yang dimasukkan

            $db = db_connect();
            // Ambil data kata sandi transaksi dengan ID 1
            $query_pwd_transaksi = $db->table('pwd_transaksi')->getWhere(['id' => 1]);

            $pwd_transaksi = $query_pwd_transaksi->getRowArray(); // Ambil satu baris data

            if (password_verify($password, $pwd_transaksi['pwd_transaksi'])) {
                // Membatalkan transaksi
                $transaksi = $db->table('transaksi');
                $transaksi->where('id_transaksi', $id_transaksi);
                $transaksi->update([
                    'terima_uang' => 0, // Mengosongkan uang yang terima
                    'metode_pembayaran' => '', // Mengosongkan metode pembayaran
                    'bank' => '', // Mengosongkan bank
                    'uang_kembali' => 0, // Mengosongkan uang kembali
                    'lunas' => 0, // Menandai transaksi sebagai belum lunas
                ]);

                // Mengambil detail transaksi
                $detailtransaksi = $db->table('detail_transaksi');
                $detailtransaksi->where('id_transaksi', $id_transaksi);
                $details = $detailtransaksi->get()->getResultArray(); // Mengambil semua detail transaksi

                // Memperbarui status resep jika ada
                if ($details) {
                    foreach ($details as $detail) {
                        if ($detail['id_resep'] !== null) { // Memeriksa apakah ada ID resep
                            $resep = $db->table('resep');
                            $resep->where('id_resep', $detail['id_resep']); // Memastikan mencocokkan berdasarkan id_resep
                            $resep->update([
                                'status' => 0, // Memperbarui status resep menjadi belum selesai
                            ]);
                        }
                    }
                }

                // Kirim pesan pembatalan berhasil jika kata sandi yang dimasukkan benar
                return $this->response->setJSON(['success' => true, 'message' => 'Transaksi berhasil dibatalkan.']);
            } else {
                // Kirim pesan kesalahan HTTP 401 jika kata sandi yang dimasukkan salah
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Kata sandi transaksi salah', 'errors' => NULL]);
            }
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function struk($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil data transaksi berdasarkan ID
            $transaksi = $this->TransaksiModel->find($id);
            // Mengambil detail layanan dari transaksi
            $layanan = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan') // Mengambil hanya jenis transaksi 'Tindakan'
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('layanan', 'layanan.id_layanan = detail_transaksi.id_layanan', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur layanan
            $result_layanan = [];

            // Memetakan setiap transaksi layanan
            foreach ($layanan as $row) {
                if (!isset($result_layanan[$row['id_detail_transaksi']])) {
                    // Menyimpan detail layanan ke array jika belum ada
                    $result_layanan[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'layanan' => [
                            'id_layanan' => $row['id_layanan'],
                            'nama_layanan' => $row['nama_layanan'],
                            'jenis_layanan' => $row['jenis_layanan'],
                            'tarif' => $row['tarif'],
                            'keterangan' => $row['keterangan'],
                        ],
                    ];
                }
            }

            // Menghitung total harga layanan
            $total_layanan = $this->DetailTransaksiModel
                ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                ->get()->getRowArray();

            // Mengambil detail obat dan alat kesehatan
            $obatalkes = $this->DetailTransaksiModel
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes') // Mengambil hanya jenis transaksi 'Obat dan Alkes'
                ->join('transaksi', 'transaksi.id_transaksi = detail_transaksi.id_transaksi', 'inner')
                ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                ->join('detail_resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->join('obat', 'detail_resep.id_obat = obat.id_obat', 'inner')
                ->orderBy('id_detail_transaksi', 'ASC')
                ->findAll();

            // Array untuk menyimpan hasil terstruktur obat dan alkes
            $result_obatalkes = [];

            // Memetakan setiap transaksi obat dan alkes
            foreach ($obatalkes as $row) {

                if (!isset($result_obatalkes[$row['id_detail_transaksi']])) {
                    // Menyimpan detail obat ke array jika belum ada
                    $result_obatalkes[$row['id_detail_transaksi']] = [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'lunas' => $row['lunas'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
                            'detail_resep' => [] // Menyimpan detail resep
                        ],
                    ];
                }

                // Menambahkan detail_resep ke transaksi
                $result_obatalkes[$row['id_detail_transaksi']]['resep']['detail_resep'][] = [
                    'id_detail_resep' => $row['id_detail_resep'],
                    'id_resep' => $row['id_resep'],
                    'id_obat' => $row['id_obat'],
                    'nama_obat' => $row['nama_obat'],
                    'kategori_obat' => $row['kategori_obat'],
                    'bentuk_obat' => $row['bentuk_obat'],
                    'signa' => $row['signa'],
                    'catatan' => $row['catatan'],
                    'cara_pakai' => $row['cara_pakai'],
                    'jumlah' => $row['jumlah'],
                    'harga_satuan' => $row['harga_satuan']
                ];
            }

            // Menghitung total harga obat dan alkes
            $total_obatalkes = $this->DetailTransaksiModel
                ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                ->where('detail_transaksi.id_transaksi', $id)
                ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                ->get()->getRowArray();

            // Memeriksa apakah transaksi valid dan lunas
            if (!empty($transaksi) && $transaksi['lunas'] == 1) {
                // Menyiapkan data untuk ditampilkan
                $data = [
                    'transaksi' => $transaksi,
                    'layanan' => array_values($result_layanan), // Mengubah array hasil layanan menjadi indexed array
                    'obatalkes' => array_values($result_obatalkes), // Mengubah array hasil obat menjadi indexed array
                    'total_layanan' => $total_layanan['total_harga'], // Total harga layanan
                    'total_obatalkes' => $total_obatalkes['total_harga'], // Total harga obat
                    'title' => 'Detail Transaksi ' . $id . ' - ' . $this->systemName // Judul halaman
                ];

                // Menghasilkan dan menampilkan struk transaksi dalam format PDF
                $dompdf = new Dompdf();
                $html = view('dashboard/transaksi/struk', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('kwitansi-id-' . $transaksi['id_transaksi'] . '-' . $transaksi['no_kwitansi'] . '-' . $transaksi['tgl_transaksi'] . '-' . urlencode($transaksi['nama_pasien']) . '.pdf', [
                    'Attachment' => FALSE // Mengunduh PDF atau membuka di browser
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound(); // Jika transaksi tidak valid, lempar exception
            }
        } else {
            throw PageNotFoundException::forPageNotFound(); // Jika peran tidak valid, lempar exception
        }
    }

    // LAPORAN TRANSAKSI
    public function reportinit()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Menyiapkan data untuk tampilan halaman laporan
            $data = [
                'title' => 'Laporan Transaksi Harian - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Laporan Transaksi Harian', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            return view('dashboard/transaksi/report', $data); // Mengembalikan tampilan halaman laporan
        } else {
            throw PageNotFoundException::forPageNotFound(); // Menampilkan halaman tidak ditemukan jika peran tidak valid
        }
    }

    public function report($tgl_transaksi)
    {
        // Memeriksa peran pengguna, hanya 'Admin' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            // Mengambil tanggal dari query string
            $tanggal = $tgl_transaksi;

            if (!$tanggal) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Tanggal harus diisi',
                ]);
            }

            $transaksi = $this->TransaksiModel->where('lunas', 1)
                ->like('tgl_transaksi', $tgl_transaksi)
                ->findAll();

            $result = []; // Untuk menyimpan transaksi terstruktur

            foreach ($transaksi as $item) {
                $dokter = $item['dokter'];

                $layanan = $this->DetailTransaksiModel
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                    ->orderBy('id_detail_transaksi', 'ASC')
                    ->findAll();

                $result_layanan = array_map(function ($row) {
                    return [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'nama_layanan' => $row['nama_layanan'],
                    ];
                }, $layanan);

                $obatalkes = $this->DetailTransaksiModel
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                    ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                    ->orderBy('id_detail_transaksi', 'ASC')
                    ->findAll();

                $result_obatalkes = array_map(function ($row) {
                    return [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
                        ]
                    ];
                }, $obatalkes);

                // Menghitung total harga obat dan alkes
                $total_obatalkes_awal = $this->DetailTransaksiModel
                    ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                    ->get()->getRowArray();

                // Memastikan nilai total_harga ada
                $total_harga = isset($total_obatalkes_awal['total_harga']) ? $total_obatalkes_awal['total_harga'] : 0;

                // Membulatkan hasil total_harga (misalnya 2 angka di belakang koma)
                $total_obatalkes = round($total_harga, 0);

                $result[] = [
                    'id_transaksi' => $item['id_transaksi'],
                    'no_kwitansi' => $item['no_kwitansi'],
                    'kasir' => $item['kasir'],
                    'no_rm' => $item['no_rm'],
                    'nama_pasien' => $item['nama_pasien'],
                    'metode_pembayaran' => $item['metode_pembayaran'],
                    'bank' => $item['bank'],
                    'total_pembayaran' => $item['total_pembayaran'],
                    'dokter' => $dokter,
                    'detail' => [
                        'layanan' => $result_layanan,
                        'obatalkes' => $total_obatalkes
                    ]
                ];
            }


            // Menghitung Total Pemasukan
            $total_all = $this->TransaksiModel
                ->where('lunas', 1)
                ->like('tgl_transaksi', $tgl_transaksi)
                ->selectSum('total_pembayaran')
                ->get()
                ->getRow()
                ->total_pembayaran;

            // Mengembalikan respons JSON dengan data pasien
            return $this->response->setJSON([
                'data' => $result,
                'total_all' => $total_all
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function reportexcel($tgl_transaksi)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Kasir' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Kasir') {
            $transaksi = $this->TransaksiModel->where('lunas', 1)
                ->like('tgl_transaksi', $tgl_transaksi)
                ->findAll();

            $result = []; // Untuk menyimpan transaksi terstruktur

            foreach ($transaksi as $item) {
                $dokter = $item['dokter'];
                $layanan = $this->DetailTransaksiModel
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Tindakan')
                    ->orderBy('id_detail_transaksi', 'ASC')
                    ->findAll();

                $result_layanan = array_map(function ($row) {
                    return [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_layanan' => $row['id_layanan'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'nama_layanan' => $row['nama_layanan'],
                    ];
                }, $layanan);

                $obatalkes = $this->DetailTransaksiModel
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                    ->join('resep', 'resep.id_resep = detail_transaksi.id_resep', 'inner')
                    ->orderBy('id_detail_transaksi', 'ASC')
                    ->findAll();

                $result_obatalkes = array_map(function ($row) {
                    return [
                        'id_detail_transaksi' => $row['id_detail_transaksi'],
                        'id_resep' => $row['id_resep'],
                        'id_transaksi' => $row['id_transaksi'],
                        'qty_transaksi' => $row['qty_transaksi'],
                        'harga_transaksi' => $row['harga_transaksi'],
                        'diskon' => $row['diskon'],
                        'resep' => [
                            'id_resep' => $row['id_resep'],
                            'dokter' => $row['dokter'],
                            'tanggal_resep' => $row['tanggal_resep'],
                            'jumlah_resep' => $row['jumlah_resep'],
                            'total_biaya' => $row['total_biaya'],
                            'status' => $row['status'],
                        ]
                    ];
                }, $obatalkes);

                // Menghitung total harga obat dan alkes
                $total_obatalkes_awal = $this->DetailTransaksiModel
                    ->selectSum('((harga_transaksi * qty_transaksi) - ((harga_transaksi * qty_transaksi) * diskon / 100))', 'total_harga')
                    ->where('detail_transaksi.id_transaksi', $item['id_transaksi'])
                    ->where('detail_transaksi.jenis_transaksi', 'Obat dan Alkes')
                    ->get()->getRowArray();

                // Memastikan nilai total_harga ada
                $total_harga = isset($total_obatalkes_awal['total_harga']) ? $total_obatalkes_awal['total_harga'] : 0;

                // Membulatkan hasil total_harga (misalnya 2 angka di belakang koma)
                $total_obatalkes = round($total_harga, 0);

                $result[] = [
                    'id_transaksi' => $item['id_transaksi'],
                    'no_kwitansi' => $item['no_kwitansi'],
                    'kasir' => $item['kasir'],
                    'no_rm' => $item['no_rm'],
                    'nama_pasien' => $item['nama_pasien'],
                    'metode_pembayaran' => $item['metode_pembayaran'],
                    'bank' => $item['bank'],
                    'total_pembayaran' => $item['total_pembayaran'],
                    'dokter' => $dokter,
                    'detail' => [
                        'layanan' => $result_layanan,
                        'obatalkes' => $total_obatalkes
                    ]
                ];
            }

            // Menghitung Total Pemasukan
            $total_all = $this->TransaksiModel
                ->where('lunas', 1)
                ->like('tgl_transaksi', $tgl_transaksi)
                ->selectSum('total_pembayaran')
                ->get()
                ->getRow()
                ->total_pembayaran;

            // Memeriksa apakah detail pembelian obat kosong
            if (empty($transaksi)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Membuat nama file berdasarkan tanggal pembelian
                $filename = $tgl_transaksi . '-transaksi';
                $tanggal = new DateTime($tgl_transaksi);
                // Buat formatter untuk tanggal dan waktu
                $formatter = new IntlDateFormatter(
                    'id_ID', // Locale untuk bahasa Indonesia
                    IntlDateFormatter::LONG, // Format untuk tanggal
                    IntlDateFormatter::NONE, // Tidak ada waktu
                    'Asia/Jakarta', // Timezone
                    IntlDateFormatter::GREGORIAN, // Calendar
                    'EEEE, d MMMM yyyy' // Format tanggal lengkap dengan nama hari
                );

                // Format tanggal
                $tanggalFormat = $formatter->format($tanggal);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Menambahkan informasi header di spreadsheet
                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'LAPORAN TRANSAKSI HARIAN');

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

                // Menambahkan informasi tanggal dan supplier
                $sheet->setCellValue('A4', 'Hari dan Tanggal:');
                $sheet->setCellValue('C4', $tanggalFormat);

                // Menambahkan header tabel detail pembelian
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Nomor Kwitansi');
                $sheet->setCellValue('C5', 'Kasir');
                $sheet->setCellValue('D5', 'Nomor RM');
                $sheet->setCellValue('E5', 'Nama Pasien');
                $sheet->setCellValue('F5', 'Metode Pembayaran');
                $sheet->setCellValue('G5', 'Dokter');
                $sheet->setCellValue('H5', 'Tindakan');
                $sheet->setCellValue('I5', 'Kas');

                // Mengatur tata letak dan gaya untuk header
                $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:I3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                // Mengisi data detail pembelian obat ke dalam spreadsheet
                $column = 6; // Baris awal data
                $nomor = 1;  // Nomor urut transaksi

                foreach ($result as $list) {
                    // Tentukan posisi awal untuk setiap transaksi
                    $startRow = $column; // Simpan posisi awal transaksi

                    // Kondisikan jika ada resep luar
                    $no_rm = ($list['no_rm'] == NULL) ? '-' : $list['no_rm'];

                    // Kondisikan jika nama pasien adalah anonim atau bukan
                    $nama_pasien = ($list['nama_pasien'] == NULL) ? '-' : $list['nama_pasien'];

                    // Isi data transaksi utama
                    $sheet->setCellValue('A' . $column, $nomor++);
                    $sheet->setCellValue('B' . $column, $list['no_kwitansi']);
                    $sheet->setCellValue('C' . $column, $list['kasir']);
                    $sheet->setCellValue('D' . $column, $no_rm);
                    $sheet->setCellValue('E' . $column, $nama_pasien);
                    $sheet->setCellValue('F' . $column, $list['metode_pembayaran']);
                    $sheet->setCellValue('G' . $column, $list['dokter']);

                    // Atur ke bold
                    $sheet->getStyle("A{$column}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Loop layanan dan pastikan harga terformat
                    foreach ($list['detail']['layanan'] as $layanan) {
                        $sheet->setCellValue('H' . $column, $layanan['nama_layanan']);

                        // Terapkan format angka sebelum isi nilai
                        $sheet->getStyle("I{$column}")->getNumberFormat()->setFormatCode(
                            '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                        );
                        $sheet->setCellValue('I' . $column, $layanan['harga_transaksi']);
                        $column++;
                    }

                    // Baris obat
                    $sheet->setCellValue('H' . $column, 'Obat');

                    // Terapkan format angka sebelum isi nilai
                    $sheet->getStyle("I{$column}")->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('I' . $column, $list['detail']['obatalkes']);

                    // Tambahkan baris pemisah antar transaksi
                    $column++;

                    // Baris total pembayaran
                    $sheet->setCellValue('H' . $column, 'Total');
                    $sheet->getStyle("I{$column}")->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('I' . $column, $list['total_pembayaran']);
                    // Atur ke bold
                    $sheet->getStyle("H{$column}")->getFont()->setBold(TRUE);
                    $sheet->getStyle("I{$column}")->getFont()->setBold(TRUE);

                    // Tambahkan baris pemisah antar transaksi
                    $column++;

                    // Kondisikan jika tidak ada bank
                    $bank = ($list['bank'] == NULL) ? '-' : $list['bank'];

                    // Baris bank
                    $sheet->setCellValue('H' . $column, 'Bank/E-wallet');
                    $sheet->setCellValue('I' . $column, $bank); // Menambahkan data bank
                    // Atur ke bold
                    $sheet->getStyle("H{$column}")->getFont()->setBold(TRUE);
                    $sheet->getStyle("I{$column}")->getFont()->setBold(TRUE);
                    // Atur rata kanan untuk bank
                    $sheet->getStyle("I{$column}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    // Menggabungkan sel dari A hingga G
                    $sheet->mergeCells("A{$startRow}:A{$column}");
                    $sheet->mergeCells("B{$startRow}:B{$column}");
                    $sheet->mergeCells("C{$startRow}:C{$column}");
                    $sheet->mergeCells("D{$startRow}:D{$column}");
                    $sheet->mergeCells("E{$startRow}:E{$column}");
                    $sheet->mergeCells("F{$startRow}:F{$column}");
                    $sheet->mergeCells("G{$startRow}:G{$column}");

                    // Tambahkan baris pemisah antar transaksi
                    $column++;
                }

                // Menambahkan total pemasukan di bawah tabel
                $sheet->setCellValue('A' . ($column), 'Total Pemasukan');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':H' . ($column));
                $sheet->setCellValue('I' . ($column), $total_all);
                // Mengatur format untuk total pemasukan
                $sheet->getStyle('I' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');

                // Mengatur gaya teks untuk header dan total
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setSize(10);
                $sheet->getStyle('C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . ($column + 2) . ':D' . ($column + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Mengatur gaya font untuk header dan total
                $sheet->getStyle('A1:A4')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:I5')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:I5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(TRUE);

                // Menambahkan border untuk header dan tabel
                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:I2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A5:I' . ($column))->applyFromArray($tableBorder);
                $sheet->getStyle('A5:I' . ($column))->getAlignment()->setWrapText(true);
                $sheet->getStyle('A6:I' . ($column + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(30, 'px');
                $sheet->getColumnDimension('B')->setWidth(150, 'px');
                $sheet->getColumnDimension('C')->setWidth(150, 'px');
                $sheet->getColumnDimension('D')->setWidth(75, 'px');
                $sheet->getColumnDimension('E')->setWidth(150, 'px');
                $sheet->getColumnDimension('F')->setWidth(100, 'px');
                $sheet->getColumnDimension('G')->setWidth(150, 'px');
                $sheet->getColumnDimension('H')->setWidth(175, 'px');
                $sheet->getColumnDimension('I')->setWidth(150, 'px');

                // Menyimpan file spreadsheet dan mengirimkan ke browser
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
