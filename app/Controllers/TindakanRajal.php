<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\TindakanRajalModel;
use App\Models\AuthModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TindakanRajal extends BaseController
{
    protected $RawatJalanModel;
    protected $TindakanRajalModel;
    protected $AuthModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->TindakanRajalModel = new TindakanRajalModel();
        $this->AuthModel = new AuthModel();
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


            // Memeriksa apakah lp_tindakan_rajal sudah ada
            $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$laporanrajal) {
                // Jika lp_tindakan_rajal tidak ditemukan, buat lp_tindakan_rajal baru dengan query builder
                $db->table('medrec_lp_tindakan_rajal')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah lp_tindakan_rajal dibuat, ambil kembali data lp_tindakan_rajal menggunakan query builder
                $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getRowArray();
            }

            // Periksa apakah nomor_registrasi ada
            $db->table('medrec_lp_tindakan_rajal')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->update([
                    'nama_dokter_dpjp' => $rawatjalan['dokter'],
                ]);

            // Inisialisasi rawat jalan
            $nama_perawat = $this->AuthModel
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->findAll();

            // Inisialisasi rawat jalan
            $nama_perawat_count = $this->AuthModel
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->countAllResults();

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

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'laporanrajal' => $laporanrajal,
                'nama_perawat' => $nama_perawat,
                'nama_perawat_count' => $nama_perawat_count,
                'title' => 'Laporan Tindakan Rawat Jalan ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Laporan Tindakan Rawat Jalan',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman tindakan rawat jalan
            return view('dashboard/rawatjalan/laporanrajal/index', $data);
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
            $data = $this->TindakanRajalModel->find($id); // Mengambil skrining
            $data['nama_perawat'] = explode(';', $data['nama_perawat']); // Ubah CSV menjadi array
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icdx()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Mendapatkan parameter pencarian dari permintaan GET
            $search = $this->request->getGet('search');

            // Jika parameter pencarian kosong, kembalikan data kosong
            if (empty($search)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [] // Data kosong
                ]);
            }

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_x');
            $builder->select('icdKode, icdNamaIndonesia');

            // Menambahkan filter pencarian
            $builder->like('icdKode', $search);

            $result = $builder->get()->getResultArray();

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

            // Memeriksa apakah lp_tindakan_rajal sudah ada
            $laporanrajal = $db->table('medrec_lp_tindakan_rajal')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            $laporanrajal['nama_perawat'] = str_replace(';', ', ', $laporanrajal['nama_perawat']);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($laporanrajal) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'laporanrajal' => $laporanrajal,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Laporan Tindakan Rawat Jalan ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/laporanrajal/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/laporanrajal/form', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream(str_replace('-', '', $rawatjalan['no_rm']) . '.pdf', [
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

    public function update($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Ambil resep luar
            $laporanrajal = $this->TindakanRajalModel->find($id);

            // Proses data perawat dari select multiple
            $perawat = $this->request->getPost('nama_perawat');
            $perawat_csv = is_array($perawat) ? implode(';', $perawat) : NULL;

            // Simpan data laporanrajal
            $data = [
                'id_lp_tindakan_rajal' => $id,
                'no_rm' => $laporanrajal['no_rm'],
                'nomor_registrasi' => $laporanrajal['nomor_registrasi'],
                'nama_dokter_dpjp' => $laporanrajal['nama_dokter_dpjp'],
                'nama_perawat' => $perawat_csv,
                'diagnosa' => $this->request->getPost('diagnosa') ?: NULL,
                'kode_icd_x' => $this->request->getPost('kode_icd_x') ?: NULL,
                'lokasi_mata' => $this->request->getPost('lokasi_mata') ?: NULL,
                'isi_laporan' => $this->request->getPost('isi_laporan') ?: NULL,
                'waktu_dibuat' => $laporanrajal['waktu_dibuat'],
            ];
            $this->TindakanRajalModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan tindakan rawat jalan berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}