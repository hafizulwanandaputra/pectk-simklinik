<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\SkriningModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Skrining extends BaseController
{
    protected $RawatJalanModel;
    protected $SkriningModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->SkriningModel = new SkriningModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah skrining sudah ada
            $skrining = $db->table('medrec_skrining')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$skrining) {
                // Jika skrining tidak ditemukan, buat skrining baru dengan query builder
                $db->table('medrec_skrining')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah skrining dibuat, ambil kembali data skrining menggunakan query builder
                $skrining = $db->table('medrec_skrining')
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
                'skrining' => $skrining,
                'title' => 'Skrining ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Skrining',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/skrining/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->SkriningModel->find($id); // Mengambil skrining
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->find($id);

            // Memeriksa apakah skrining sudah ada
            $skrining = $db->table('medrec_skrining')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($skrining) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'skrining' => $skrining,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Skrining ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/skrining/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/skrining/form', $data);
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
            $skrining = $this->SkriningModel->find($id);

            // Simpan data skrining
            $data = [
                'id_skrining' => $id,
                'no_rm' => $skrining['no_rm'],
                'nomor_registrasi' => $skrining['nomor_registrasi'],
                'jatuh_sempoyongan' => $this->request->getPost('jatuh_sempoyongan') ?: NULL,
                'jatuh_penopang' => $this->request->getPost('jatuh_penopang') ?: NULL,
                'jatuh_info_dokter' => $this->request->getPost('jatuh_info_dokter') ?: NULL,
                'jatuh_info_pukul' => $this->request->getPost('jatuh_info_pukul') ?: NULL,
                'status_fungsional' => $this->request->getPost('status_fungsional') ?: NULL,
                'status_info_pukul' => $this->request->getPost('status_info_pukul') ?: NULL,
                'nyeri_kategori' => $this->request->getPost('nyeri_kategori') ?: NULL,
                'nyeri_skala' => $this->request->getPost('nyeri_skala') ?: NULL,
                'nyeri_lokasi' => $this->request->getPost('nyeri_lokasi') ?: NULL,
                'nyeri_karakteristik' => $this->request->getPost('nyeri_karakteristik') ?: NULL,
                'nyeri_durasi' => $this->request->getPost('nyeri_durasi') ?: NULL,
                'nyeri_frekuensi' => $this->request->getPost('nyeri_frekuensi') ?: NULL,
                'nyeri_hilang_bila' => $this->request->getPost('nyeri_hilang_bila') ?: NULL,
                'nyeri_hilang_lainnya' => $this->request->getPost('nyeri_hilang_lainnya') ?: NULL,
                'nyeri_info_dokter' => $this->request->getPost('nyeri_info_dokter') ?: NULL,
                'nyeri_info_pukul' => $this->request->getPost('nyeri_info_pukul') ?: NULL,
                'waktu_dibuat' => $skrining['waktu_dibuat'],
            ];
            $this->SkriningModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Skrining berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
