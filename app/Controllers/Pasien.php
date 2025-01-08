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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $PasienModel = $this->PasienModel;

            // Menerapkan filter pencarian berdasarkan nomor rekam medis dan nama pasien, pasien
            if ($search) {
                $PasienModel->groupStart()
                    ->like('no_rm', $search)
                    ->orLike('nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $PasienModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $PasienModel->orderBy('id_pasien', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap pasien
            $dataPasien = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data; // Mengembalikan data yang telah ditambahkan nomor urut
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

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
                'kewarganegaraan' => NULL,
                'agama' => NULL,
                'status_nikah' => NULL,
                'pekerjaan' => NULL,
                'tanggal_daftar' => date("Y-m-d H:i:s"),
            ];
            $this->PasienModel->insert($data);

            // Dapatkan ID dari data yang baru disimpan
            $newId = $this->PasienModel->insertID();

            // Redirect ke halaman detail pasien
            return redirect()->to(base_url('pasien/detailpasien/' . $newId));
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Menghubungkan ke database
            $db = db_connect();

            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

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

    public function kiup($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            $db = db_connect();

            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

            // Ambil tabel reg_provinces
            $provinsi = $db->table('reg_provinces');
            $provinsi->select('name');
            $provinsi->where('id', $pasien['provinsi']);

            // Query untuk mendapatkan nama provinsi
            $res_provinsi = $provinsi->get()->getRow();

            if ($res_provinsi) {
                // Ubah ID menjadi nama provinsi
                $pasien['provinsi'] = $res_provinsi->name;
            }

            // Ambil tabel reg_regencies
            $kabupaten = $db->table('reg_regencies');
            $kabupaten->select('name');
            $kabupaten->where('id', $pasien['kabupaten']);

            // Query untuk mendapatkan nama kabupaten
            $res_kabupaten = $kabupaten->get()->getRow();

            if ($res_kabupaten) {
                // Ubah ID menjadi nama kabupaten
                $pasien['kabupaten'] = $res_kabupaten->name;
            }

            // Ambil tabel reg_districts
            $kecamatan = $db->table('reg_districts');
            $kecamatan->select('name');
            $kecamatan->where('id', $pasien['kecamatan']);

            // Query untuk mendapatkan nama kecamatan
            $res_kecamatan = $kecamatan->get()->getRow();

            if ($res_kecamatan) {
                // Ubah ID menjadi nama kecamatan
                $pasien['kecamatan'] = $res_kecamatan->name;
            }

            // Ambil tabel reg_villages
            $kelurahan = $db->table('reg_villages');
            $kelurahan->select('name');
            $kelurahan->where('id', $pasien['kelurahan']);

            // Query untuk mendapatkan nama kelurahan
            $res_kelurahan = $kelurahan->get()->getRow();

            if ($res_kelurahan) {
                // Ubah ID menjadi nama kelurahan
                $pasien['kelurahan'] = $res_kelurahan->name;
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
                    'title' => 'KIUP ' . $pasien['nama_pasien'] . ' (' . $pasien['no_rm'] . ') - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/pasien/kiup', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/pasien/kiup', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('kiup-' . $pasien['no_rm'] . '-' . urlencode($pasien['nama_pasien']) . '.pdf', [
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

    public function barcode($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/pasien/barcode', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('barcode-' . $pasien['no_rm'] . '-' . urlencode($pasien['nama_pasien']) . '.pdf', [
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

    public function pasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data provinsi
            $builder = $db->table('reg_provinces');
            $result = $builder->select('id, name')->get()->getResultArray();

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kabupaten
            $builder = $db->table('reg_regencies');
            $result = $builder->select('id, name')->where('province_id', $id)->get()->getResultArray();

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kecamatan
            $builder = $db->table('reg_districts');
            $result = $builder->select('id, name')->where('regency_id', $id)->get()->getResultArray();

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kecamatan
            $builder = $db->table('reg_villages');
            $result = $builder->select('id, name')->where('district_id', $id)->get()->getResultArray();

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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
                ->where('id_pasien', $id)
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

    public function kunjunganoptions($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil jenis kunjungan dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('id_pasien', $id)
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

    public function jaminanoptions($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil jaminan dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('id_pasien', $id)
                ->groupBy('jaminan')
                ->orderBy('jaminan', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data rawat jalan yang diterima
            foreach ($rawatJalan as $jaminan) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $jaminan['jaminan'], // Nilai untuk opsi
                    'text'  => $jaminan['jaminan'] // Teks untuk opsi
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil ruangan dari tabel pengguna
            $auth = $this->AuthModel
                ->where('role', 'Dokter')
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

    public function pendaftaroptions($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil pendaftar dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('id_pasien', $id)
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

    public function statusoptions($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Mengambil status dari tabel rawat jalan
            $rawatJalan = $this->RawatJalanModel
                ->where('id_pasien', $id)
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

    public function update($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Rekam Medis') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_pasien' => 'required',
                'nik' => 'max_length[16]',
                'no_bpjs' => 'max_length[16]',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
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
                'rt' => $this->request->getPost('rt') ?: NULL,
                'rw' => $this->request->getPost('rw') ?: NULL,
                'telpon' => $this->request->getPost('telpon'),
                'kewarganegaraan' => $this->request->getPost('kewarganegaraan'),
                'agama' => $this->request->getPost('agama'),
                'status_nikah' => $this->request->getPost('status_nikah'),
                'pekerjaan' => $this->request->getPost('pekerjaan'),
                'tanggal_daftar' => $pasien['tanggal_daftar'],
            ];
            $this->PasienModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Data pasien berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
