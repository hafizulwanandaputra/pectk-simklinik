<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\LPOperasiPterigiumModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class LPOperasiPterigium extends BaseController
{
    protected $RawatJalanModel;
    protected $LPOperasiPterigiumModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->LPOperasiPterigiumModel = new LPOperasiPterigiumModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Laporan Operasi Pterigium - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi Pterigium',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasipterigium/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function lpoperasipterigiumlist()
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
            $LPOperasiPterigiumModel = $this->LPOperasiPterigiumModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $LPOperasiPterigiumModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $LPOperasiPterigiumModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $LPOperasiPterigiumModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $LpOperasiPterigium = $LPOperasiPterigiumModel
                ->orderBy('id_lp_operasi_pterigium', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataLpOperasiPterigium = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $LpOperasiPterigium, array_keys($LpOperasiPterigium));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'lp_operasi_pterigium' => $dataLpOperasiPterigium,
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
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
            $today = date('Y-m-d');

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

            // Mengambil nomor_registrasi yang sudah terpakai di rawat_jalan
            $db = \Config\Database::connect();
            $usedNoRegInit = $db->table('medrec_lp_operasi_pterigium')->select('nomor_registrasi')->get()->getResultArray();
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

            $today = date('Y-m-d');
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini

            $data = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where("DATE(tanggal_registrasi) >=", $hari_yang_lalu)
                ->where("DATE(tanggal_registrasi) <=", $today)
                ->where('status', 'DAFTAR')
                ->where('ruangan', 'Kamar Operasi')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $LPOperasiPterigiumData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $LPOperasiPterigiumData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$LPOperasiPterigiumData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $LPOperasiPterigiumData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_lp_operasi_pterigium')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi pterigium berhasil ditambahkan']); // Mengembalikan respon sukses
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
            $lp_operasi_pterigium = $this->LPOperasiPterigiumModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($lp_operasi_pterigium['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($lp_operasi_pterigium) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'lp_operasi_pterigium' => $lp_operasi_pterigium,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Laporan Operasi Pterigium ' . $lp_operasi_pterigium['nama_pasien'] . ' (' . $lp_operasi_pterigium['no_rm'] . ') - ' . $lp_operasi_pterigium['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Laporan Operasi Pterigium',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/lpoperasipterigium/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-lp-operasi-pterigium.html';
                file_put_contents($htmlFile, view('dashboard/lpoperasipterigium/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-lp-operasi-pterigium.pdf';

                // Jalankan Puppeteer untuk konversi HTML ke PDF
                // Keterangan: "node " . ROOTPATH . "puppeteer-pdf.js $htmlFile $pdfFile panjang lebar marginAtas margin Kanan marginBawah marginKiri"
                // Silakan lihat puppeteer-pdf.js di root projectt untuk keterangan lebih lanjut.
                $command = env('CMD-ENV') . "node " . ROOTPATH . "puppeteer-pdf.js $htmlFile $pdfFile 210mm 297mm 1cm 1cm 1cm 1cm 2>&1";
                $output = shell_exec($command);

                // Hapus file HTML setelah eksekusi
                @unlink($htmlFile);

                // Jika tidak ada output, langsung stream PDF
                if (!$output) {
                    return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="LPOperasiPterigium_' . $lp_operasi_pterigium['nomor_registrasi'] . '_' . str_replace('-', '', $lp_operasi_pterigium['no_rm']) . '.pdf')
                        ->setBody(file_get_contents($pdfFile));
                }

                // Jika ada output (kemungkinan error), kembalikan HTTP 500 dengan <pre>
                return $this->response
                    ->setStatusCode(500)
                    ->setHeader('Content-Type', 'text/html')
                    ->setBody('<pre>' . htmlspecialchars($output) . '</pre>');
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
            $lp_operasi_pterigium = $this->LPOperasiPterigiumModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            $tanggal = date('Y-m-d', strtotime($lp_operasi_pterigium['tanggal_registrasi']));
            $today = date('Y-m-d');
            $hari_yang_lalu = date('Y-m-d', strtotime('-13 days')); // Termasuk hari ini
            if ($tanggal < $hari_yang_lalu || $tanggal > $today) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Pasien operasi katarak yang didaftarkan lebih dari 14 hari tidak dapat dihapus']);
            }
            if ($lp_operasi_pterigium) {
                $db = db_connect();

                // Menghapus lp_operasi_pterigium
                $this->LPOperasiPterigiumModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_lp_operasi_pterigium` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Laporan operasi pterigium berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Laporan operasi pterigium tidak ditemukan', // Pesan jika peran tidak valid
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

            $lp_operasi_pterigium = $this->LPOperasiPterigiumModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            $asisten = $db->table('user')
                ->groupStart()
                ->where('role', 'Dokter')
                ->orWhere('role', 'Perawat')
                ->groupEnd()
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_lp_operasi_pterigium')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi_pterigium.id_lp_operasi_pterigium <', $id)
                ->orderBy('medrec_lp_operasi_pterigium.id_lp_operasi_pterigium', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_lp_operasi_pterigium')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi_pterigium.id_lp_operasi_pterigium >', $id)
                ->orderBy('medrec_lp_operasi_pterigium.id_lp_operasi_pterigium', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_lp_operasi_pterigium')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $lp_operasi_pterigium['no_rm'])
                ->orderBy('id_lp_operasi_pterigium', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'lp_operasi_pterigium' => $lp_operasi_pterigium,
                'dokter' => $dokter,
                'asisten' => $asisten,
                'title' => 'Laporan Operasi Pterigium ' . $lp_operasi_pterigium['nama_pasien'] . ' (' . $lp_operasi_pterigium['no_rm'] . ') - ' . $lp_operasi_pterigium['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi Pterigium',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasipterigium/details', $data);
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
            $data = $this->LPOperasiPterigiumModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_pterigium.nomor_registrasi', 'inner')
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
                'mata' => [
                    'rules' => 'required',
                ],
                'operator' => [
                    'rules' => 'required',
                ],
                'tanggal_operasi' => [
                    'rules' => 'required',
                ],
                'jam_operasi' => [
                    'rules' => 'required',
                ],
                'lama_operasi' => [
                    'rules' => 'required',
                ],
                'diagnosis' => [
                    'rules' => 'required',
                ],
                'asisten' => [
                    'rules' => 'required',
                ],
                'jenis_operasi' => [
                    'rules' => 'required',
                ],
                'jenis_anastesi' => [
                    'rules' => 'required',
                ],
                'dokter_anastesi' => [
                    'rules' => 'required',
                ],
                'antiseptic' => [
                    'rules' => 'required',
                ],
                'antiseptic_lainnya' => [
                    'rules' => $this->request->getPost('antiseptic') === 'LAINNYA' ? 'required' : 'permit_empty',
                ],
                'spekulum' => [
                    'rules' => 'required',
                ],
                'spekulum_lainnya' => [
                    'rules' => $this->request->getPost('spekulum') === 'LAINNYA' ? 'required' : 'permit_empty',
                ],
                'kendala_rektus_superior' => [
                    'rules' => 'required',
                ],
                'cangkok_konjungtiva' => [
                    'rules' => 'required',
                ],
                'ukuran_cangkok' => [
                    'rules' => $this->request->getPost('cangkok_konjungtiva') === 'YA' ? 'required' : 'permit_empty',
                ],
                'cangkang_membrane_amnio' => [
                    'rules' => 'required',
                ],
                'ukuran_cangkang' => [
                    'rules' => $this->request->getPost('cangkang_membrane_amnio') === 'YA' ? 'required' : 'permit_empty',
                ],
                'bare_sclera' => [
                    'rules' => 'required',
                ],
                'mytomicyn_c' => [
                    'rules' => 'required',
                ],
                'penjahitan' => [
                    'rules' => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'mata' => $this->request->getPost('mata') ?: null,
                'operator' => $this->request->getPost('operator') ?: null,
                'tanggal_operasi' => $this->request->getPost('tanggal_operasi') ?: null,
                'jam_operasi' => $this->request->getPost('jam_operasi') ?: null,
                'lama_operasi' => $this->request->getPost('lama_operasi') ?: null,
                'diagnosis' => $this->request->getPost('diagnosis') ?: null,
                'asisten' => $this->request->getPost('asisten') ?: null,
                'jenis_operasi' => $this->request->getPost('jenis_operasi') ?: null,
                'jenis_anastesi' => $this->request->getPost('jenis_anastesi') ?: null,
                'dokter_anastesi' => $this->request->getPost('dokter_anastesi') ?: null,

                'antiseptic' => $this->request->getPost('antiseptic') ?: null,
                'antiseptic_lainnya' => $this->request->getPost('antiseptic_lainnya') ?: null,
                'spekulum' => $this->request->getPost('spekulum') ?: null,
                'spekulum_lainnya' => $this->request->getPost('spekulum_lainnya') ?: null,
                'kendala_rektus_superior' => $this->request->getPost('kendala_rektus_superior') ?: null,
                'cangkok_konjungtiva' => $this->request->getPost('cangkok_konjungtiva') ?: null,
                'ukuran_cangkok' => $this->request->getPost('ukuran_cangkok') ?: null,
                'cangkang_membrane_amnio' => $this->request->getPost('cangkang_membrane_amnio') ?: null,
                'ukuran_cangkang' => $this->request->getPost('ukuran_cangkang') ?: null,
                'bare_sclera' => $this->request->getPost('bare_sclera') ?: null,
                'mytomicyn_c' => $this->request->getPost('mytomicyn_c') ?: null,
                'penjahitan' => $this->request->getPost('penjahitan') ?: null,

                'keterangan_tambahan' => $this->request->getPost('keterangan_tambahan') ?: null,
                'terapi_pasca_bedah' => $this->request->getPost('terapi_pasca_bedah') ?: null,
            ];
            $db->table('medrec_lp_operasi_pterigium')->where('id_lp_operasi_pterigium', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi pterigium berhasil diperbarui']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }

    public function notify_clients($action)
    {
        if (!in_array($action, ['update', 'delete'])) {
            return $this->response->setJSON([
                'status' => 'Invalid action',
                'error' => 'Action must be either "update" or "delete"'
            ])->setStatusCode(400);
        }

        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => ['action' => $action]
        ]);

        return $this->response->setJSON([
            'status' => ucfirst($action) . ' notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
