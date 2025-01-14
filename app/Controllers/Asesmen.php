<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\AsesmenModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Asesmen extends BaseController
{
    protected $RawatJalanModel;
    protected $AsesmenModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->AsesmenModel = new AsesmenModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah asesmen sudah ada
            $asesmen = $db->table('medrec_assesment')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$asesmen) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_assesment')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'nama_dokter' => $rawatjalan['dokter'],
                    'tanggal_registrasi' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $asesmen = $db->table('medrec_assesment')
                    ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                    ->get()
                    ->getRowArray();
            }

            // Query untuk item sebelumnya
            $previous = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan <', $id) // Kondisi untuk id sebelumnya
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan >', $id) // Kondisi untuk id berikutnya
                ->orderBy('rawat_jalan.id_rawat_jalan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'asesmen' => $asesmen,
                'title' => 'Asesmen ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Asesmen',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/rawatjalan/asesmen/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data asesmen berdasarkan ID
            $data = $this->AsesmenModel->find($id); // Mengambil asesmen
            return $this->response->setJSON($data); // Mengembalikan data asesmen dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icdx()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
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

    public function icd9()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
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
            $builder = $db->table('icd_9');
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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah asesmen sudah ada
            $asesmen = $db->table('medrec_assesment')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($asesmen) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'asesmen' => $asesmen,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Asesmen ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/asesmen/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/asesmen/form', $data);
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Ambil resep luar
            $asesmen = $this->AsesmenModel->find($id);

            // Simpan data asesmen
            $data = [
                'id_asesmen' => $id,
                'no_rm' => $asesmen['no_rm'],
                'nomor_registrasi' => $asesmen['nomor_registrasi'],
                'keluhan_utama' => $this->request->getPost('keluhan_utama') ?: NULL,
                'riwayat_penyakit_sekarang' => $this->request->getPost('riwayat_penyakit_sekarang') ?: NULL,
                'riwayat_penyakit_dahulu' => $this->request->getPost('riwayat_penyakit_dahulu') ?: NULL,
                'riwayat_penyakit_keluarga' => $this->request->getPost('riwayat_penyakit_keluarga') ?: NULL,
                'riwayat_pengobatan' => $this->request->getPost('riwayat_pengobatan') ?: NULL,
                'riwayat_sosial_pekerjaan' => $this->request->getPost('riwayat_sosial_pekerjaan') ?: NULL,
                'kesadaran' => $this->request->getPost('kesadaran') ?: NULL,
                'tekanan_darah' => $this->request->getPost('tekanan_darah') ?: NULL,
                'nadi' => $this->request->getPost('nadi') ?: NULL,
                'suhu' => $this->request->getPost('suhu') ?: NULL,
                'pernapasan' => $this->request->getPost('pernapasan') ?: NULL,
                'keadaan_umum' => $this->request->getPost('keadaan_umum') ?: NULL,
                'kesadaran_mental' => $this->request->getPost('kesadaran_mental') ?: NULL,
                'alergi' => $this->request->getPost('alergi') ?: NULL,
                'alergi_keterangan' => $this->request->getPost('alergi_keterangan') ?: NULL,
                'sakit_lainnya' => $this->request->getPost('sakit_lainnya') ?: NULL,
                'sakit_lainnya' => $this->request->getPost('sakit_lainnya') ?: NULL,
                'od_ucva' => $this->request->getPost('od_ucva') ?: NULL,
                'od_bcva' => $this->request->getPost('od_bcva') ?: NULL,
                'os_ucva' => $this->request->getPost('os_ucva') ?: NULL,
                'os_bcva' => $this->request->getPost('os_bcva') ?: NULL,
                'diagnosa_medis_1' => $this->request->getPost('diagnosa_medis_1') ?: NULL,
                'icdx_kode_1' => $this->request->getPost('icdx_kode_1') ?: NULL,
                'diagnosa_medis_2' => $this->request->getPost('diagnosa_medis_2') ?: NULL,
                'icdx_kode_2' => $this->request->getPost('icdx_kode_2') ?: NULL,
                'diagnosa_medis_3' => $this->request->getPost('diagnosa_medis_3') ?: NULL,
                'icdx_kode_3' => $this->request->getPost('icdx_kode_3') ?: NULL,
                'diagnosa_medis_4' => $this->request->getPost('diagnosa_medis_4') ?: NULL,
                'icdx_kode_4' => $this->request->getPost('icdx_kode_4') ?: NULL,
                'diagnosa_medis_5' => $this->request->getPost('diagnosa_medis_5') ?: NULL,
                'icdx_kode_5' => $this->request->getPost('icdx_kode_5') ?: NULL,
                'terapi_1' => $this->request->getPost('terapi_1') ?: NULL,
                'icd9_kode_1' => $this->request->getPost('icd9_kode_1') ?: NULL,
                'terapi_2' => $this->request->getPost('terapi_2') ?: NULL,
                'icd9_kode_2' => $this->request->getPost('icd9_kode_2') ?: NULL,
                'terapi_3' => $this->request->getPost('terapi_3') ?: NULL,
                'icd9_kode_3' => $this->request->getPost('icd9_kode_3') ?: NULL,
                'terapi_4' => $this->request->getPost('terapi_4') ?: NULL,
                'icd9_kode_4' => $this->request->getPost('icd9_kode_4') ?: NULL,
                'terapi_5' => $this->request->getPost('terapi_5') ?: NULL,
                'icd9_kode_5' => $this->request->getPost('icd9_kode_5') ?: NULL,
                'persetujuan_dokter' => $asesmen['persetujuan_dokter'],
                'nama_dokter' => $asesmen['nama_dokter'],
                'tanggal_registrasi' => $asesmen['tanggal_registrasi'],
            ];
            $this->AsesmenModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Asesmen berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
