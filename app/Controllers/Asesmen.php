<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\AsesmenModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

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
                    'waktu_dibuat' => date('Y-m-d H:i:s')
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
                'asesmen' => $asesmen,
                'title' => 'Asesmen ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Asesmen',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data asesmen berdasarkan ID
            $data = $this->AsesmenModel->find($id); // Mengambil asesmen
            $data['sakit_lainnya'] = explode(',', $data['sakit_lainnya']); // Ubah CSV menjadi array
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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_x');
            $builder->select('icdKode, icdNamaInggris');

            // Menambahkan filter pencarian
            $builder
                ->like('icdKode', $search)
                ->orLike('icdNamaInggris', $search);

            // Menambahkan limit dan offset untuk lazy loading
            $builder->limit($limit, $offset);

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
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
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

            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('icd_9');
            $builder->select('icdKode, icdNamaInggris');

            // Menambahkan filter pencarian
            $builder
                ->like('icdKode', $search)
                ->orLike('icdNamaInggris', $search);

            // Menambahkan limit dan offset untuk lazy loading
            $builder->limit($limit, $offset);

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

    public function listvisus()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data ICD-X
            $builder = $db->table('master_visus');
            $builder->select('visus_machine');;

            $results = $builder->get()->getResultArray();
            $options = [];
            // Menyiapkan opsi untuk ditampilkan
            foreach ($results as $row) {
                $options[] = [
                    'value' => $row['visus_machine']
                ];
            }

            // Mengembalikan respons JSON dengan data supplier
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah asesmen sudah ada
            $asesmen = $db->table('medrec_assesment')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            $asesmen['sakit_lainnya'] = str_replace(',', ', ', $asesmen['sakit_lainnya']);

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
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-asesmen.html';
                file_put_contents($htmlFile, view('dashboard/rawatjalan/asesmen/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-asesmen.pdf';

                // Jalankan Puppeteer untuk konversi HTML ke PDF
                // Keterangan: "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile panjang lebar marginAtas margin Kanan marginBawah marginKiri"
                // Silakan lihat puppeteer-pdf.js di folder public untuk keterangan lebih lanjut.
                $command = env('CMD-ENV') . "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile 210mm 297mm 1cm 1cm 1cm 1cm";
                shell_exec($command);

                // Hapus file HTML
                @unlink($htmlFile);

                // Kirim PDF ke browser
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="' . str_replace('-', '', $rawatjalan['no_rm']) . '.pdf')
                    ->setBody(file_get_contents($pdfFile));
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', 'Perawat', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            $alergi = $this->request->getPost('alergi');
            // Set base validation rules
            $validation->setRules([
                'keluhan_utama' => 'required',
                'kesadaran' => 'required',
                'keadaan_umum' => 'required',
                'alergi' => 'required',
                'alergi_keterangan' => $alergi === 'YA' ? 'required' : 'permit_empty',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil data asesmen berdasarkan ID menggunakan Query Builder
            $db = db_connect();
            $asesmen = $db->table('medrec_assesment')->where('id_asesmen', $id)->get()->getRowArray();

            if (!$asesmen) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Proses data sakit_lainnya dari select multiple
            $sakit_lainnya = $this->request->getPost('sakit_lainnya');
            $sakit_lainnya_csv = is_array($sakit_lainnya) ? implode(',', $sakit_lainnya) : NULL;

            // Data yang akan disimpan
            if (session()->get('role') == 'Perawat') {
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
                    'tekanan_darah' => $this->request->getPost('tekanan_darah') ?? 0,
                    'nadi' => $this->request->getPost('nadi') ?? 0,
                    'suhu' => $this->request->getPost('suhu') ?? 0,
                    'pernapasan' => $this->request->getPost('pernapasan') ?? 0,
                    'keadaan_umum' => $this->request->getPost('keadaan_umum') ?: NULL,
                    'alergi' => $this->request->getPost('alergi') ?: NULL,
                    'alergi_keterangan' => $this->request->getPost('alergi_keterangan') ?: NULL,
                    'sakit_lainnya' => $sakit_lainnya_csv,
                    'tono_od' => $this->request->getPost('tono_od'),
                    'tono_os' => $this->request->getPost('tono_os'),
                    'od_ucva' => $this->request->getPost('od_ucva'),
                    'od_bcva' => $this->request->getPost('od_bcva'),
                    'os_ucva' => $this->request->getPost('os_ucva'),
                    'os_bcva' => $this->request->getPost('os_bcva'),
                    'waktu_dibuat' => $asesmen['waktu_dibuat'],
                ];
            } else {
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
                    'alergi' => $this->request->getPost('alergi') ?: NULL,
                    'alergi_keterangan' => $this->request->getPost('alergi_keterangan') ?: NULL,
                    'sakit_lainnya' => $sakit_lainnya_csv,
                    'tono_od' => $this->request->getPost('tono_od'),
                    'tono_os' => $this->request->getPost('tono_os'),
                    'od_ucva' => $this->request->getPost('od_ucva'),
                    'od_bcva' => $this->request->getPost('od_bcva'),
                    'os_ucva' => $this->request->getPost('os_ucva'),
                    'os_bcva' => $this->request->getPost('os_bcva'),
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
                    'waktu_dibuat' => $asesmen['waktu_dibuat'],
                ];
            }

            // Perbarui data asesmen
            $db->table('medrec_assesment')->where('id_asesmen', $id)->update($data);

            return $this->response->setJSON(['success' => true, 'message' => 'Asesmen berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
