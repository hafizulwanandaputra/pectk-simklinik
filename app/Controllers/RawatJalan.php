<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RawatJalanModel;
use App\Models\PoliklinikModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;

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

    public function rawatjalanlisttanggal($tanggal)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Admisi') {
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->like('tanggal_registrasi', $tanggal)
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

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
            foreach ($rawatjalan as &$rajal) {
                if (isset($jaminanMap[$rajal['jaminan']])) {
                    $rajal['jaminan'] = $jaminanMap[$rajal['jaminan']];
                } else {
                    $rajal['jaminan'] = 'Tidak Diketahui'; // Default jika kode jaminan tidak ditemukan
                }
            }

            // Mengembalikan respons JSON dengan data pasien
            return $this->response->setJSON([
                'data' => $rawatjalan,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function rawatjalanlistrm($no_rm)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat'  || session()->get('role') == 'Admisi') {
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('pasien.no_rm', $no_rm)
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

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
            foreach ($rawatjalan as &$rajal) {
                if (isset($jaminanMap[$rajal['jaminan']])) {
                    $rajal['jaminan'] = $jaminanMap[$rajal['jaminan']];
                } else {
                    $rajal['jaminan'] = 'Tidak Diketahui'; // Default jika kode jaminan tidak ditemukan
                }
            }

            // Mengembalikan respons JSON dengan data pasien
            return $this->response->setJSON([
                'data' => $rawatjalan,
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

            if ($this->request->getPost('alasan_batal') == 'Kesalahan dalam Memasukkan Data') {
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
                }

                // Hapus data rawat jalan
                $db->table('rawat_jalan')->where('id_rawat_jalan', $id)->delete();

                // Reset auto_increment menjadi 1
                $db->query('ALTER TABLE rawat_jalan AUTO_INCREMENT = 1');
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
            return $this->response->setJSON(['success' => true, 'message' => 'Rawat jalan berhasil dibatalkan']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
