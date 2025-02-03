<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SPOperasiModel;
use App\Models\SafetySignInModel;
use App\Models\SafetySignOutModel;
use App\Models\SafetyTimeOutModel;
use CodeIgniter\Exceptions\PageNotFoundException;

use Picqer\Barcode\BarcodeGeneratorPNG;

class SafetyOperasi extends BaseController
{
    protected $SPOperasiModel;
    protected $SafetySignInModel;
    protected $SafetySignOutModel;
    protected $SafetyTimeOutModel;
    public function __construct()
    {
        $this->SPOperasiModel = new SPOperasiModel();
        $this->SafetySignInModel = new SafetySignInModel();
        $this->SafetySignOutModel = new SafetySignOutModel();
        $this->SafetyTimeOutModel = new SafetyTimeOutModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_signin) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_signin')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_signout) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_signout')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_signout = $db->table('medrec_operasi_safety_signin')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_timeout) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_timeout')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_timeout = $db->table('medrec_operasi_safety_signin')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $perawat = $db->table('user')
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->get()->getResultArray();

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi <', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi >', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->join('medrec_sp_operasi', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->where('rawat_jalan.no_rm', $sp_operasi['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->where('rawat_jalan.ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'operasi' => $sp_operasi,
                'operasi_safety_signin' => $operasi_safety_signin,
                'operasi_safety_signout' => $operasi_safety_signout,
                'operasi_safety_timeout' => $operasi_safety_timeout,
                'perawat' => $perawat,
                'dokter' => $dokter,
                'title' => 'Pemeriksaan Pra Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                'headertitle' => 'Pemeriksaan Pra Operasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pra operasi
            return view('dashboard/operasi/praoperasi/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view_signin($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data safety sign in berdasarkan ID
            $data = $this->SafetySignInModel->find($id); // Mengambil safety sign in
            return $this->response->setJSON($data); // Mengembalikan data safety sign in dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view_signout($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Mengambil data safety sign out berdasarkan ID
            $data = $this->SafetySignOutModel->find($id); // Mengambil safety sign out
            return $this->response->setJSON($data); // Mengembalikan data safety sign out dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view_timeout($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Mengambil data safety time out berdasarkan ID
            $data = $this->SafetyTimeOutModel->find($id); // Mengambil safety time out
            return $this->response->setJSON($data); // Mengembalikan data safety time out dalam format JSON
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($sp_operasi['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($operasi_safety_signin && $operasi_safety_signout && $operasi_safety_timeout) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'operasi' => $sp_operasi,
                    'operasi_safety_signin' => $operasi_safety_signin,
                    'operasi_safety_signout' => $operasi_safety_signout,
                    'operasi_safety_timeout' => $operasi_safety_timeout,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Pemeriksaan Pra Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                    'headertitle' => 'Pemeriksaan Pra Operasi',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/operasi/safety/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-safety.html';
                file_put_contents($htmlFile, view('dashboard/operasi/safety/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-safety.pdf';

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
                    ->setHeader('Content-Disposition', 'inline; filename="Safety_' . $sp_operasi['nomor_booking'] . '_' . str_replace('-', '', $sp_operasi['no_rm']) . '.pdf')
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

    public function update_signin($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();
            // $validation = \Config\Services::validation();

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('id_signin', $id)
                ->get()
                ->getRowArray();

            // $validation->setRules([
            //     'perawat_praoperasi' => 'required',
            //     'jenis_operasi' => 'required',
            //     'ctt_vital_suhu' => 'required',
            //     'ctt_vital_nadi' => 'required',
            //     'ctt_vital_rr' => 'required',
            //     'ctt_vital_td' => 'required',
            //     'ctt_vital_nyeri' => 'required',
            //     'ctt_vital_tb' => 'required',
            //     'ctt_vital_bb' => 'required',
            //     'ctt_mental' => 'required',
            //     'ctt_alergi' => 'required',
            //     'ctt_alergi_jelaskan' => $ctt_alergi === 'YA' ? 'required' : 'permit_empty',
            //     'ctt_haid' => $sp_operasi['jenis_kelamin'] === 'P' ? 'required' : 'permit_empty',
            //     'ctt_kepercayaan' => 'required',
            // ]);

            // if (!$this->validate($validation->getRules())) {
            //     return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            // }

            if (session()->get('role') == 'Perawat') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'ns_konfirmasi_identitas' => $this->request->getPost('ns_konfirmasi_identitas') ?: NULL,
                    'ns_masker_operasi' => $this->request->getPost('ns_masker_operasi') ?: NULL,
                    'ns_inform_consent_sesuai' => $this->request->getPost('ns_inform_consent_sesuai') ?: NULL,
                    'ns_identifikasi_alergi' => $this->request->getPost('ns_identifikasi_alergi') ?: NULL,
                    'ns_puasa' => $this->request->getPost('ns_puasa') ?: NULL,
                    'ns_cek_lensa_intrakuler' => $this->request->getPost('ns_cek_lensa_intrakuler') ?: NULL,
                    'ns_konfirmasi_lensa' => $this->request->getPost('ns_konfirmasi_lensa') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            } else if (session()->get('role') == 'Dokter') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'dr_konfirmasi_identitas' => $this->request->getPost('dr_konfirmasi_identitas') ?: NULL,
                    'dr_masker_operasi' => $this->request->getPost('dr_masker_operasi') ?: NULL,
                    'dr_inform_consent_sesuai' => $this->request->getPost('dr_inform_consent_sesuai') ?: NULL,
                    'dr_identifikasi_alergi' => $this->request->getPost('dr_identifikasi_alergi') ?: NULL,
                    'dr_puasa' => $this->request->getPost('dr_puasa') ?: NULL,
                    'dr_cek_anestesi_khusus' => $this->request->getPost('dr_cek_anestesi_khusus') ?: NULL,
                    'dr_cek_konfirmasi_anestersi' => $this->request->getPost('dr_cek_konfirmasi_anestersi') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            } else if (session()->get('role') == 'Admin') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'ns_konfirmasi_identitas' => $this->request->getPost('ns_konfirmasi_identitas') ?: NULL,
                    'ns_masker_operasi' => $this->request->getPost('ns_masker_operasi') ?: NULL,
                    'ns_inform_consent_sesuai' => $this->request->getPost('ns_inform_consent_sesuai') ?: NULL,
                    'ns_identifikasi_alergi' => $this->request->getPost('ns_identifikasi_alergi') ?: NULL,
                    'ns_puasa' => $this->request->getPost('ns_puasa') ?: NULL,
                    'ns_cek_lensa_intrakuler' => $this->request->getPost('ns_cek_lensa_intrakuler') ?: NULL,
                    'ns_konfirmasi_lensa' => $this->request->getPost('ns_konfirmasi_lensa') ?: NULL,
                    'dr_konfirmasi_identitas' => $this->request->getPost('dr_konfirmasi_identitas') ?: NULL,
                    'dr_masker_operasi' => $this->request->getPost('dr_masker_operasi') ?: NULL,
                    'dr_inform_consent_sesuai' => $this->request->getPost('dr_inform_consent_sesuai') ?: NULL,
                    'dr_identifikasi_alergi' => $this->request->getPost('dr_identifikasi_alergi') ?: NULL,
                    'dr_puasa' => $this->request->getPost('dr_puasa') ?: NULL,
                    'dr_cek_anestesi_khusus' => $this->request->getPost('dr_cek_anestesi_khusus') ?: NULL,
                    'dr_cek_konfirmasi_anestersi' => $this->request->getPost('dr_cek_konfirmasi_anestersi') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            }

            $db->table('medrec_operasi_safety_signin')->where('id_signin', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan pra operasi berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
        }
    }
}
