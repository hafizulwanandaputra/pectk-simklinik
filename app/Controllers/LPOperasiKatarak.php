<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\LPOperasiKatarakModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class LPOperasiKatarak extends BaseController
{
    protected $RawatJalanModel;
    protected $LPOperasiKatarakModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->LPOperasiKatarakModel = new LPOperasiKatarakModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Laporan Operasi Katarak - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi Katarak',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasikatarak/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function lpoperasikataraklist()
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
            $LPOperasiKatarakModel = $this->LPOperasiKatarakModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $LPOperasiKatarakModel
                    ->like('tanggal', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $LPOperasiKatarakModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $LPOperasiKatarakModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $LpOperasiKatarak = $LPOperasiKatarakModel
                ->orderBy('id_lp_operasi_katarak', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataLpOperasiKatarak = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $LpOperasiKatarak, array_keys($LpOperasiKatarak));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'lp_operasi_katarak' => $dataLpOperasiKatarak,
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
                ->where('ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->findAll();

            // Mengambil nomor_registrasi yang sudah terpakai di medrec_sp_operasi
            $db = \Config\Database::connect();
            $usedNoReg = $db->table('medrec_sp_operasi')->select('nomor_registrasi')->get()->getResultArray();
            $usedNoReg = array_column($usedNoReg, 'nomor_registrasi');

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
                ->where('ruangan', 'Kamar Operasi')
                ->findAll();

            // Memeriksa apakah data mengandung nomor registrasi yang diminta
            $LPOperasiKatarakData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $LPOperasiKatarakData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$LPOperasiKatarakData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $LPOperasiKatarakData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_lp_operasi_katarak')->insert($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi katarak berhasil ditambahkan']); // Mengembalikan respon sukses
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
            $lp_operasi_katarak = $this->LPOperasiKatarakModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($lp_operasi_katarak['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($lp_operasi_katarak) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'lp_operasi_katarak' => $lp_operasi_katarak,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Laporan Operasi Katarak ' . $lp_operasi_katarak['nama_pasien'] . ' (' . $lp_operasi_katarak['no_rm'] . ') - ' . $lp_operasi_katarak['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Laporan Operasi Katarak',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/lpoperasikatarak/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-lp-operasi-katarak.html';
                file_put_contents($htmlFile, view('dashboard/lpoperasikatarak/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-lp-operasi-katarak.pdf';

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
                    ->setHeader('Content-Disposition', 'inline; filename="LPOperasiKatarak_' . $lp_operasi_katarak['nomor_registrasi'] . '_' . str_replace('-', '', $lp_operasi_katarak['no_rm']) . '.pdf')
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
            $transaksi = $this->LPOperasiKatarakModel->find($id);
            if ($transaksi) {
                $db = db_connect();

                // Menghapus transaksi
                $this->LPOperasiKatarakModel->delete($id);

                // Reset auto increment untuk tabel transaksi dan detail_transaksi
                $db->query('ALTER TABLE `medrec_lp_operasi_katarak` auto_increment = 1');

                return $this->response->setJSON(['message' => 'Laporan operasi katarak berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Laporan operasi katarak tidak ditemukan', // Pesan jika peran tidak valid
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

            $lp_operasi_katarak = $this->LPOperasiKatarakModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
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
            $previous = $db->table('medrec_lp_operasi_katarak')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi_katarak.id_lp_operasi_katarak <', $id)
                ->orderBy('medrec_lp_operasi_katarak.id_lp_operasi_katarak', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_lp_operasi_katarak')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_lp_operasi_katarak.id_lp_operasi_katarak >', $id)
                ->orderBy('medrec_lp_operasi_katarak.id_lp_operasi_katarak', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->join('medrec_lp_operasi_katarak', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
                ->where('rawat_jalan.no_rm', $lp_operasi_katarak['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->where('rawat_jalan.ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'lp_operasi_katarak' => $lp_operasi_katarak,
                'dokter' => $dokter,
                'asisten' => $asisten,
                'title' => 'Laporan Operasi Katarak ' . $lp_operasi_katarak['nama_pasien'] . ' (' . $lp_operasi_katarak['no_rm'] . ') - ' . $lp_operasi_katarak['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Laporan Operasi Katarak',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/lpoperasikatarak/details', $data);
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
            $data = $this->LPOperasiKatarakModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_lp_operasi_katarak.nomor_registrasi', 'inner')
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

                'anastesi_retrobulbar' => $this->request->getPost('anastesi_retrobulbar') ?: null,
                'anastesi_peribulber' => $this->request->getPost('anastesi_peribulber') ?: null,
                'anastesi_topikal' => $this->request->getPost('anastesi_topikal') ?: null,
                'anastesi_subtenom' => $this->request->getPost('anastesi_subtenom') ?: null,
                'anastesi_lidocain_2' => $this->request->getPost('anastesi_lidocain_2') ?: null,
                'anastesi_marcain_05' => $this->request->getPost('anastesi_marcain_05') ?: null,
                'anastesi_lainnya' => $this->request->getPost('anastesi_lainnya') ?: null,

                'peritomi_basis_forniks' => $this->request->getPost('peritomi_basis_forniks') ?: null,
                'peritomi_basis_limbus' => $this->request->getPost('peritomi_basis_limbus') ?: null,

                'lokasi_superonasal' => $this->request->getPost('lokasi_superonasal') ?: null,
                'lokasi_superior' => $this->request->getPost('lokasi_superior') ?: null,
                'lokasi_supertemporal' => $this->request->getPost('lokasi_supertemporal') ?: null,
                'lokasi_lainnya' => $this->request->getPost('lokasi_lainnya') ?: null,

                'lokasi_insisi_kornea' => $this->request->getPost('lokasi_insisi_kornea') ?: null,
                'lokasi_insisi_limbus' => $this->request->getPost('lokasi_insisi_limbus') ?: null,
                'lokasi_insisi_skelera' => $this->request->getPost('lokasi_insisi_skelera') ?: null,
                'lokasi_insisi_skeleratunnel' => $this->request->getPost('lokasi_insisi_skeleratunnel') ?: null,
                'lokasi_insisi_sideport' => $this->request->getPost('lokasi_insisi_sideport') ?: null,

                'ukuran_inisiasi' => $this->request->getPost('ukuran_inisiasi') ?: null,

                'alat_insisi_jarum' => $this->request->getPost('alat_insisi_jarum') ?: null,
                'alat_insisi_crescent' => $this->request->getPost('alat_insisi_crescent') ?: null,
                'alat_insisi_diamond' => $this->request->getPost('alat_insisi_diamond') ?: null,

                'capsulectomy_canopener' => $this->request->getPost('capsulectomy_canopener') ?: null,
                'capsulectomy_envelope' => $this->request->getPost('capsulectomy_envelope') ?: null,
                'capsulectomy_ccc' => $this->request->getPost('capsulectomy_ccc') ?: null,

                'ekstraksi_lenca_icce' => $this->request->getPost('ekstraksi_lenca_icce') ?: null,
                'ekstraksi_lenca_ecce' => $this->request->getPost('ekstraksi_lenca_ecce') ?: null,
                'ekstraksi_lenca_sucea' => $this->request->getPost('ekstraksi_lenca_sucea') ?: null,
                'ekstraksi_lenca_phaco' => $this->request->getPost('ekstraksi_lenca_phaco') ?: null,
                'ekstraksi_lenca_cle' => $this->request->getPost('ekstraksi_lenca_cle') ?: null,
                'ekstraksi_lenca_ai' => $this->request->getPost('ekstraksi_lenca_ai') ?: null,

                'tindakan_sphincter' => $this->request->getPost('tindakan_sphincter') ?: null,
                'tindakan_jahitan_iris' => $this->request->getPost('tindakan_jahitan_iris') ?: null,
                'tindakan_virektomi' => $this->request->getPost('tindakan_virektomi') ?: null,
                'tindakan_kapsulotomi_post' => $this->request->getPost('tindakan_kapsulotomi_post') ?: null,
                'tindakan_sinechiolyssis' => $this->request->getPost('tindakan_sinechiolyssis') ?: null,

                'cairan_irigasi_ri' => $this->request->getPost('cairan_irigasi_ri') ?: null,
                'cairan_irigasi_bss' => $this->request->getPost('cairan_irigasi_bss') ?: null,
                'cairan_irigasi_lainnya' => $this->request->getPost('cairan_irigasi_lainnya') ?: null,

                'fiksasi_bmb' => $this->request->getPost('fiksasi_bmb') ?: null,
                'fiksasi_bmd' => $this->request->getPost('fiksasi_bmd') ?: null,
                'fiksasi_sulkus_siliaris' => $this->request->getPost('fiksasi_sulkus_siliaris') ?: null,
                'fiksasi_sklera' => $this->request->getPost('fiksasi_sklera') ?: null,

                'penanaman_diputar' => $this->request->getPost('penanaman_diputar') ?: null,
                'penanaman_tidak_diputar' => $this->request->getPost('penanaman_tidak_diputar') ?: null,

                'jenis_dilipat' => $this->request->getPost('jenis_dilipat') ?: null,
                'jenis_tidak_dilipat' => $this->request->getPost('jenis_tidak_dilipat') ?: null,

                'posisi_vertikal' => $this->request->getPost('posisi_vertikal') ?: null,
                'posisi_horizontal' => $this->request->getPost('posisi_horizontal') ?: null,
                'posisi_miring' => $this->request->getPost('posisi_miring') ?: null,

                'cairan_viscoelastik_healon' => $this->request->getPost('cairan_viscoelastik_healon') ?: null,
                'cairan_viscoelastik_viscoat' => $this->request->getPost('cairan_viscoelastik_viscoat') ?: null,
                'cairan_viscoelastik_amvisca' => $this->request->getPost('cairan_viscoelastik_amvisca') ?: null,
                'cairan_viscoelastik_healon_5' => $this->request->getPost('cairan_viscoelastik_healon_5') ?: null,
                'cairan_viscoelastik_rohtovisc' => $this->request->getPost('cairan_viscoelastik_rohtovisc') ?: null,

                'benang_vicryl_8_0' => $this->request->getPost('benang_vicryl_8_0') ?: null,
                'benang_ethylon_10_0' => $this->request->getPost('benang_ethylon_10_0') ?: null,

                'jumlah_jahitan' => $this->request->getPost('jumlah_jahitan') ?: null,
                'prabedah_od' => $this->request->getPost('prabedah_od') ?: null,
                'prabedah_os' => $this->request->getPost('prabedah_os') ?: null,

                'komplikasi_tidak_ada' => $this->request->getPost('komplikasi_tidak_ada') ?: null,
                'komplikasi_ada' => $this->request->getPost('komplikasi_ada') ?: null,
                'komplikasi_prolaps' => $this->request->getPost('komplikasi_prolaps') ?: null,
                'komplikasi_pendarahan' => $this->request->getPost('komplikasi_pendarahan') ?: null,
                'komplikasi_lainnya' => $this->request->getPost('komplikasi_lainnya') ?: null,

                'tindakan_operasi' => $this->request->getPost('tindakan_operasi') ?: null,
                'terapi_pascabedah' => $this->request->getPost('terapi_pascabedah') ?: null,
            ];
            $db->table('medrec_lp_operasi_katarak')->where('id_lp_operasi_katarak', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Laporan operasi katarak berhasil diperbarui']); // Mengembalikan respon sukses
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
            ]);
        }
    }
}
