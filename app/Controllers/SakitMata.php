<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\SakitMataModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SakitMata extends BaseController
{
    protected $RawatJalanModel;
    protected $SakitMataModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->SakitMataModel = new SakitMataModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Surat Keterangan Sakit Mata - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Sakit Mata',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/sakitmata/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function sakitmatalist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $tanggal = $this->request->getGet('tanggal');
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $SakitMataModel = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $SakitMataModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $SakitMataModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $SakitMataModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $SakitMata = $SakitMataModel
                ->orderBy('id_keterangan_sakit_mata', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataSakitMata = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $SakitMata, array_keys($SakitMata));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'sakitmata' => $dataSakitMata,
                'total' => $total
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function pasienlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->like('tanggal_registrasi', date('Y-m-d'))
                ->where('status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

            // Mengambil nomor_registrasi yang sudah terpakai di rawat_jalan
            $db = \Config\Database::connect();
            $usedNoRegInit = $db->table('medrec_keterangan_sakit_mata')->select('nomor_registrasi')->get()->getResultArray();
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

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
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

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->like('tanggal_registrasi', date('Y-m-d'))
                ->where('status', 'DAFTAR')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $SakitMataData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $SakitMataData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$SakitMataData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $SakitMataData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_keterangan_sakit_mata')->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan sakit mata berhasil ditambahkan']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Inisialisasi rawat jalan
            $sakitmata = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($sakitmata['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($sakitmata) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'sakitmata' => $sakitmata,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Surat Keterangan Sakit Mata ' . $sakitmata['nama_pasien'] . ' (' . $sakitmata['no_rm'] . ') - ' . $sakitmata['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Surat Keterangan Sakit Mata',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/sakitmata/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-sakitmata.html';
                file_put_contents($htmlFile, view('dashboard/sakitmata/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-sakitmata.pdf';

                // Jalankan Puppeteer untuk konversi HTML ke PDF
                // Keterangan: "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile panjang lebar marginAtas margin Kanan marginBawah marginKiri"
                // Silakan lihat puppeteer-pdf.js di folder public untuk keterangan lebih lanjut.
                $command = env('CMD-ENV') . "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile 297mm 210mm 0cm 0cm 0cm 0cm";
                shell_exec($command);

                // Hapus file HTML
                @unlink($htmlFile);

                // Kirim PDF ke browser
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="SakitMata_' . $sakitmata['nomor_registrasi'] . '_' . str_replace('-', '', $sakitmata['no_rm']) . '.pdf')
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

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $sakitmata = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            if (date('Y-m-d', strtotime($sakitmata['tanggal_registrasi'])) != date('Y-m-d')) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Surat keterangan sakit mata yang bukan hari ini tidak dapat dihapus']);
            }
            if ($sakitmata) {
                $db = db_connect();

                // Menghapus sakitmata
                $this->SakitMataModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_keterangan_sakit_mata` auto_increment = 1');

                return $this->response->setJSON(['message' => 'Surat keterangan sakit mata berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Surat keterangan sakit mata tidak ditemukan', // Pesan jika peran tidak valid
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function details($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            $sakitmata = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata <', $id)
                ->orderBy('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata >', $id)
                ->orderBy('medrec_keterangan_sakit_mata.id_keterangan_sakit_mata', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_keterangan_sakit_mata')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $sakitmata['no_rm'])
                ->orderBy('id_keterangan_sakit_mata', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'sakitmata' => $sakitmata,
                'title' => 'Surat Keterangan Sakit Mata ' . $sakitmata['nama_pasien'] . ' (' . $sakitmata['no_rm'] . ') - ' . $sakitmata['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Keterangan Sakit Mata',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/sakitmata/details', $data);
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
            $data = $this->SakitMataModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_keterangan_sakit_mata.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Melakukan validasi
            $validation = \Config\Services::validation();

            // Menetapkan aturan validasi dasar
            $rules = [
                'keterangan' => [
                    'rules' => 'required',
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'keterangan' => $this->request->getPost('keterangan') ?: null
            ];
            $db->table('medrec_keterangan_sakit_mata')->where('id_keterangan_sakit_mata', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Surat keterangan sakit mata berhasil diperbarui']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }
}
