<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\EdukasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Edukasi extends BaseController
{
    protected $RawatJalanModel;
    protected $EdukasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->EdukasiModel = new EdukasiModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah edukasi sudah ada
            $edukasi = $db->table('medrec_edukasi')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$edukasi) {
                // Jika edukasi tidak ditemukan, buat edukasi baru dengan query builder
                $db->table('medrec_edukasi')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah edukasi dibuat, ambil kembali data edukasi menggunakan query builder
                $edukasi = $db->table('medrec_edukasi')
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

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $rawatjalan['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Query untuk opsi pendidikan
            $pendidikan = $db->table('master_pendidikan')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'edukasi' => $edukasi,
                'title' => 'Edukasi ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Edukasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan,
                'pendidikan' => $pendidikan,
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/edukasi/index', $data);
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
            $data = $this->EdukasiModel->find($id); // Mengambil skrining
            $data['hambatan'] = explode(',', $data['hambatan']); // Ubah CSV menjadi array
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
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah edukasi sudah ada
            $edukasi = $db->table('medrec_edukasi')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // Ambil tabel master_pendidikan
            $pendidikan = $db->table('master_pendidikan');
            $pendidikan->select('keterangan');
            $pendidikan->where('pendidikan', $edukasi['pendidikan']);

            // Query untuk mendapatkan nama pendidikan
            $res_pendidikan = $pendidikan->get()->getRow();

            if ($res_pendidikan) {
                // Ubah pendidikan menjadi keterangan
                $edukasi['pendidikan'] = $res_pendidikan->keterangan;
            }

            $edukasi['hambatan'] = str_replace(',', ', ', $edukasi['hambatan']);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($edukasi) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'edukasi' => $edukasi,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Skrining ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/edukasi/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/edukasi/form', $data);
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Ambil resep luar
            $edukasi = $this->EdukasiModel->find($id);

            // Proses data hambatan dari select multiple
            $hambatan = $this->request->getPost('hambatan');
            $hambatan_csv = is_array($hambatan) ? implode(',', $hambatan) : NULL;

            // Simpan data edukasi
            $data = [
                'id_edukasi' => $id,
                'no_rm' => $edukasi['no_rm'],
                'nomor_registrasi' => $edukasi['nomor_registrasi'],
                'bahasa' => $this->request->getPost('bahasa') ?: NULL,
                'bahasa_lainnya' => $this->request->getPost('bahasa_lainnya') ?: NULL,
                'penterjemah' => $this->request->getPost('penterjemah') ?: NULL,
                'pendidikan' => $this->request->getPost('pendidikan') ?: NULL,
                'baca_tulis' => $this->request->getPost('baca_tulis') ?: NULL,
                'cara_belajar' => $this->request->getPost('cara_belajar') ?: NULL,
                'budaya' => $this->request->getPost('budaya') ?: NULL,
                'hambatan' => $hambatan_csv,
                'keyakinan' => $this->request->getPost('keyakinan') ?: NULL,
                'keyakinan_khusus' => $this->request->getPost('keyakinan_khusus') ?: NULL,
                'topik_pembelajaran' => $this->request->getPost('topik_pembelajaran') ?: NULL,
                'topik_lainnya' => $this->request->getPost('topik_lainnya') ?: NULL,
                'kesediaan_pasien' => $this->request->getPost('kesediaan_pasien') ?: NULL,
                'waktu_dibuat' => $edukasi['waktu_dibuat'],
            ];
            $this->EdukasiModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Edukasi berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}