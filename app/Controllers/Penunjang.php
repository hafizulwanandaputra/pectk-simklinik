<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\PenunjangModel;
use App\Models\PoliklinikModel;
use App\Models\AuthModel;
use App\Models\EdukasiEvaluasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Penunjang extends BaseController
{
    protected $RawatJalanModel;
    protected $PenunjangModel;
    protected $PoliklinikModel;
    protected $AuthModel;
    protected $EdukasiEvaluasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->PenunjangModel = new PenunjangModel();
        $this->PoliklinikModel = new PoliklinikModel();
        $this->AuthModel = new AuthModel();
        $this->EdukasiEvaluasiModel = new EdukasiEvaluasiModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Memeriksa apakah penunjang sudah ada
            $penunjang = $db->table('medrec_permintaan_penunjang')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            if (!$penunjang) {
                // Jika penunjang tidak ditemukan, buat penunjang baru dengan query builder
                $db->table('medrec_permintaan_penunjang')->insert([
                    'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                    'no_rm' => $rawatjalan['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah penunjang dibuat, ambil kembali data penunjang menggunakan query builder
                $penunjang = $db->table('medrec_permintaan_penunjang')
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

            // Menyiapkan data untuk tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'penunjang' => $penunjang,
                'title' => 'Pemeriksaan Penunjang ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Pemeriksaan Penunjang',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan,
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/rawatjalan/penunjang/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->PenunjangModel->find($id); // Mengambil skrining
            $data['pemeriksaan'] = explode(',', $data['pemeriksaan']); // Ubah CSV menjadi array
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function ruanganoptions()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Mengambil ruangan dari tabel pengguna
            $auth = $this->AuthModel
                ->where('role', 'Dokter')
                ->where('active', 1)
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

            // Memeriksa apakah penunjang sudah ada
            $penunjang = $db->table('medrec_permintaan_penunjang')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            $penunjang['pemeriksaan'] = str_replace(',', ', ', $penunjang['pemeriksaan']);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($rawatjalan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($penunjang) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'penunjang' => $penunjang,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Pemeriksaan Penunjang ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'agent' => $this->request->getUserAgent()
                ];
                // return view('dashboard/rawatjalan/penunjang/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/rawatjalan/penunjang/form', $data);
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Ambil resep luar
            $penunjang = $this->PenunjangModel->find($id);

            // Proses data pemeriksaan dari select multiple
            $pemeriksaan = $this->request->getPost('pemeriksaan');
            $pemeriksaan_csv = is_array($pemeriksaan) ? implode(',', $pemeriksaan) : NULL;

            // Simpan data penunjang
            $data = [
                'id_penunjang' => $id,
                'no_rm' => $penunjang['no_rm'],
                'nomor_registrasi' => $penunjang['nomor_registrasi'],
                'diagnosa' => $this->request->getPost('diagnosa') ?: NULL,
                'dokter_pengirim' => $this->request->getPost('dokter_pengirim') ?: NULL,
                'rujukan_dari' => $this->request->getPost('rujukan_dari') ?: NULL,
                'pemeriksaan' => $pemeriksaan_csv,
                'pemeriksaan_lainnya' => $this->request->getPost('pemeriksaan_lainnya') ?: NULL,
                'lokasi_pemeriksaan' => $this->request->getPost('lokasi_pemeriksaan') ?: NULL,
                'hasil_pemeriksaan' => $this->request->getPost('hasil_pemeriksaan') ?: NULL,
                'waktu_dibuat' => $penunjang['waktu_dibuat'],
            ];
            $this->PenunjangModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Edukasi berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
