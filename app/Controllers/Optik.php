<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\OptikModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Optik extends BaseController
{
    protected $RawatJalanModel;
    protected $OptikModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->OptikModel = new OptikModel();
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

            $title = 'Resep Kacamata ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName;
            $headertitle = 'Resep Kacamata';

            // Memeriksa apakah optik sudah ada
            $optik = $db->table('medrec_optik')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

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

            if (!$optik) {
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'title' => $title,
                    'headertitle' => $headertitle, // Judul header
                    'agent' => $this->request->getUserAgent(), // Mengambil user agent
                    'previous' => $previous,
                    'next' => $next,
                    'listRawatJalan' => $listRawatJalan
                ];
                return view('dashboard/rawatjalan/optik/empty', $data);
            }

            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'optik' => $optik,
                'title' => $title,
                'headertitle' => $headertitle, // Judul header
                'agent' => $this->request->getUserAgent(), // Mengambil user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/optik/index', $data);
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
            $data = $this->OptikModel->find($id); // Mengambil skrining
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if ($rawatjalan['transaksi'] == 1) {
                session()->setFlashdata('error', 'Resep kacamata tidak dapat ditambahkan pada rawat jalan yang transaksisnya sudah diproses');
                return redirect()->back();
            }

            // Buat resep kacamata baru dengan query builder
            $db->table('medrec_optik')->insert([
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'no_rm' => $rawatjalan['no_rm'],
                'waktu_dibuat' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back();
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
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

            // Memeriksa apakah optik sudah ada
            $optik = $db->table('medrec_optik')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($optik) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'optik' => $optik,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Resep Kacamata ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/optik/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/optik/form', $data);
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
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'tipe_lensa' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $optik = $this->OptikModel->find($id);

            // Simpan data optik
            $data = [
                'id_optik' => $id,
                'no_rm' => $optik['no_rm'],
                'nomor_registrasi' => $optik['nomor_registrasi'],
                'tipe_lensa' => $this->request->getPost('tipe_lensa') ?: NULL,

                'od_login_spher' => $this->request->getPost('od_login_spher'),
                'od_login_cyldr' => $this->request->getPost('od_login_cyldr'),
                'od_login_axis' => $this->request->getPost('od_login_axis'),
                'od_login_prisma' => $this->request->getPost('od_login_prisma'),
                'od_login_basis' => $this->request->getPost('od_login_basis'),

                'od_domo_spher' => $this->request->getPost('od_domo_spher'),
                'od_domo_cyldr' => $this->request->getPost('od_domo_cyldr'),
                'od_domo_axis' => $this->request->getPost('od_domo_axis'),
                'od_domo_prisma' => $this->request->getPost('od_domo_prisma'),
                'od_domo_basis' => $this->request->getPost('od_quitat_basis'),

                'od_quitat_spher' => $this->request->getPost('od_quitat_spher'),
                'od_quitat_cyldr' => $this->request->getPost('od_quitat_cyldr'),
                'od_quitat_axis' => $this->request->getPost('od_quitat_axis'),
                'od_quitat_prisma' => $this->request->getPost('od_quitat_prisma'),
                'od_quitat_basis' => $this->request->getPost('od_quitat_basis'),

                'os_login_spher' => $this->request->getPost('os_login_spher'),
                'os_login_cyldr' => $this->request->getPost('os_login_cyldr'),
                'os_login_axis' => $this->request->getPost('os_login_axis'),
                'os_login_prisma' => $this->request->getPost('os_login_prisma'),
                'os_login_basis' => $this->request->getPost('os_login_basis'),
                'os_login_vitror' => $this->request->getPost('os_login_vitror'),
                'os_login_pupil' => $this->request->getPost('os_login_pupil'),

                'os_domo_spher' => $this->request->getPost('os_domo_spher'),
                'os_domo_cyldr' => $this->request->getPost('os_domo_cyldr'),
                'os_domo_axis' => $this->request->getPost('os_domo_axis'),
                'os_domo_prisma' => $this->request->getPost('os_domo_prisma'),
                'os_domo_basis' => $this->request->getPost('os_quitat_basis'),
                'os_domo_vitror' => $this->request->getPost('os_quitat_vitror'),
                'os_domo_pupil' => $this->request->getPost('os_quitat_pupil'),

                'os_quitat_spher' => $this->request->getPost('os_quitat_spher'),
                'os_quitat_cyldr' => $this->request->getPost('os_quitat_cyldr'),
                'os_quitat_axis' => $this->request->getPost('os_quitat_axis'),
                'os_quitat_prisma' => $this->request->getPost('os_quitat_prisma'),
                'os_quitat_basis' => $this->request->getPost('os_quitat_basis'),
                'os_quitat_vitror' => $this->request->getPost('os_quitat_vitror'),
                'os_quitat_pupil' => $this->request->getPost('os_quitat_pupil'),

                'waktu_dibuat' => $optik['waktu_dibuat'],
            ];
            $this->OptikModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Optik berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
