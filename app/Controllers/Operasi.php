<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\SPOperasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Operasi extends BaseController
{
    protected $RawatJalanModel;
    protected $SPOperasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->SPOperasiModel = new SPOperasiModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Pasien Operasi - ' . $this->systemName,
                'headertitle' => 'Pasien Operasi',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/operasi/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function operasilist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $dokter = $this->request->getGet('dokter');
            $status = $this->request->getGet('status');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $SPOperasiModel = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $SPOperasiModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($dokter) {
                $SPOperasiModel
                    ->like('dokter_operator', $dokter);
            }

            if ($status) {
                $SPOperasiModel
                    ->like('status_operasi', $status);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $SPOperasiModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $SPOperasiModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $SPOperasi = $SPOperasiModel
                ->orderBy('id_sp_operasi', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataSPOperasi = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $SPOperasi, array_keys($SPOperasi));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'sp_operasi' => $dataSPOperasi,
                'total' => $total
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function rawatjalanlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
            $today = date('Y-m-d');

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

            // Mengambil nomor_registrasi yang sudah terpakai di medrec_sp_operasi
            $db = \Config\Database::connect();
            $usedNoRegInit = $db->table('medrec_sp_operasi')->select('nomor_registrasi')->get()->getResultArray();
            $usedNoReg = array_column($usedNoRegInit, 'nomor_registrasi');

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
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function dokterlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            $db = db_connect();
            // Mengambil kasir dari tabel user
            $spOperasiData = $this->SPOperasiModel
                ->groupBy('dokter_operator')
                ->orderBy('dokter_operator', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data transaksi luar yang diterima
            foreach ($spOperasiData as $SPOperasi) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $SPOperasi['dokter_operator'], // Nilai untuk opsi
                    'text'  => $SPOperasi['dokter_operator'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data SP$SPOperasi luar dalam format JSON
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

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
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

            $today = date('Y-m-d');
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $spOperasiData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $spOperasiData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$spOperasiData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Mendapatkan tanggal saat ini
            $date = new \DateTime($spOperasiData['tanggal_registrasi']);
            $tanggal = $date->format('d'); // Hari (2 digit)
            $bulan = $date->format('m'); // Bulan (2 digit)
            $tahun = $date->format('y'); // Tahun (2 digit)

            // Mengambil nomor registrasi terakhir untuk di-increment
            $lastNoReg = $this->SPOperasiModel->getLastNoBooking($tahun, $bulan, $tanggal);
            $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -3)) : 0; // Mendapatkan nomor terakhir
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Menyiapkan nomor berikutnya

            // Memformat nomor kwitansi
            $nomor_booking = sprintf('OK%s%s%s%s', $tanggal, $bulan, $tahun, $nextNumber);

            // Menyimpan data transaksi
            $data = [
                'nomor_booking' => $nomor_booking,
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $spOperasiData['no_rm'], // Nomor rekam medis
                'tanggal_operasi' => NULL,
                'jam_operasi' => NULL,
                'diagnosa' => NULL,
                'jenis_tindakan' => NULL,
                'indikasi_operasi' => NULL,
                'jenis_bius' => NULL,
                'tipe_bayar' => NULL,
                'rajal_ranap' => NULL,
                'ruang_operasi' => NULL,
                'dokter_operator' => 'Belum Ada',
                'status_operasi' => 'DIJADWAL',
                'diagnosa_site_marking' => NULL,
                'tindakan_site_marking' => NULL,
                'site_marking' => NULL,
                'nama_pasien_keluarga' => NULL,
                'tanda_tangan_pasien' => NULL,
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $this->SPOperasiModel->save($data); // Menyimpan data transaksi ke database
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Pasien operasi berhasil ditambahkan']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function setstatus()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'status_operasi' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            if ($this->request->getPost('status_operasi') == 'HAPUS') {
                $sp_operasi = $db->table('medrec_sp_operasi')
                    ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                    ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                    ->where('id_sp_operasi', $this->request->getPost('id_sp_operasi'))
                    ->get()->getRowArray();
                $tanggal = date('Y-m-d', strtotime($sp_operasi['tanggal_registrasi']));
                $today = date('Y-m-d');
                $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
                if ($tanggal < $hari_yang_lalu || $tanggal > $today) {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Pasien operasi yang didaftarkan lebih dari 14 hari tidak dapat dihapus']);
                }
                if ($sp_operasi['site_marking']) {
                    @unlink(FCPATH . 'uploads/site_marking/' . $sp_operasi['site_marking']);
                }
                $db->table('medrec_sp_operasi')
                    ->where('id_sp_operasi', $this->request->getPost('id_sp_operasi'))
                    ->delete();
                $db->query('ALTER TABLE medrec_sp_operasi AUTO_INCREMENT = 1');
                $db->query('ALTER TABLE medrec_operasi_pra AUTO_INCREMENT = 1');
                $db->query('ALTER TABLE medrec_operasi_safety_signin AUTO_INCREMENT = 1');
                $db->query('ALTER TABLE medrec_operasi_safety_signout AUTO_INCREMENT = 1');
                $db->query('ALTER TABLE medrec_operasi_safety_timeout AUTO_INCREMENT = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['success' => true, 'message' => 'Pasien operasi berhasil dihapus']);
            } else {
                $db->table('medrec_sp_operasi')
                    ->where('id_sp_operasi', $this->request->getPost('id_sp_operasi'))
                    ->update([
                        'status_operasi' => $this->request->getPost('status_operasi'),
                    ]);
                // Panggil WebSocket untuk update client
                $this->notify_clients('update');
                return $this->response->setJSON(['success' => true, 'message' => 'Status pasien operasi berhasil diperbarui']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
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
