<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\FRMSetujuKedokteranModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class FRMSetujuKedokteran extends BaseController
{
    protected $RawatJalanModel;
    protected $FRMSetujuKedokteranModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->FRMSetujuKedokteranModel = new FRMSetujuKedokteranModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Formulir Persetujuan Tindakan Kedokteran - ' . $this->systemName,
                'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/frmsetujukedokteran/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function frmsetujukedokteranlist()
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
            $FRMSetujuKedokteranModel = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($tanggal) {
                $FRMSetujuKedokteranModel
                    ->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $FRMSetujuKedokteranModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $FRMSetujuKedokteranModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $FRMSetuju = $FRMSetujuKedokteranModel
                ->orderBy('id_form_persetujuan_tindakan', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataFRMSetuju = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $FRMSetuju, array_keys($FRMSetuju));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'form_persetujuan_tindakan' => $dataFRMSetuju,
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
            $usedNoRegInit = $db->table('medrec_form_persetujuan_tindakan')->select('nomor_registrasi')->get()->getResultArray();
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
            $LPOperasiData = null;
            foreach ($data as $patient) {
                if ($patient['nomor_registrasi'] == $nomorRegistrasi) {
                    $LPOperasiData = $patient; // Menyimpan data pasien jika ditemukan
                    break;
                }
            }

            // Jika data pasien tidak ditemukan
            if (!$LPOperasiData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data rawat jalan tidak ditemukan', 'errors' => NULL]);
            }

            // Menyimpan data transaksi
            $data = [
                'nomor_registrasi' => $nomorRegistrasi, // Nomor registrasi
                'no_rm' => $LPOperasiData['no_rm'], // Nomor rekam medis
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $db->table('medrec_form_persetujuan_tindakan')->insert($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Formulir persetujuan tindakan kedokteran berhasil ditambahkan']); // Mengembalikan respon sukses
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
            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($form_persetujuan_tindakan['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($form_persetujuan_tindakan) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'form_persetujuan_tindakan' => $form_persetujuan_tindakan,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Formulir Persetujuan Tindakan Kedokteran ' . $form_persetujuan_tindakan['nama_pasien'] . ' (' . $form_persetujuan_tindakan['no_rm'] . ') - ' . $form_persetujuan_tindakan['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/frmsetujukedokteran/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-form-persetujuan-tindakan.html';
                file_put_contents($htmlFile, view('dashboard/frmsetujukedokteran/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-form-persetujuan-tindakan.pdf';

                // Jalankan Puppeteer untuk konversi HTML ke PDF
                // Keterangan: "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile panjang lebar marginAtas margin Kanan marginBawah marginKiri"
                // Silakan lihat puppeteer-pdf.js di folder public untuk keterangan lebih lanjut.
                $command = env('CMD-ENV') . "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile 210mm 297mm 1cm 1cm 1cm 1cm 2>&1";
                $output = shell_exec($command);

                // Hapus file HTML setelah eksekusi
                @unlink($htmlFile);

                // Jika tidak ada output, langsung stream PDF
                if (!$output) {
                    return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="FRMSetuju_' . $form_persetujuan_tindakan['nomor_registrasi'] . '_' . str_replace('-', '', $form_persetujuan_tindakan['no_rm']) . '.pdf')
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
            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            if (date('Y-m-d', strtotime($form_persetujuan_tindakan['tanggal_registrasi'])) != date('Y-m-d')) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Form persetujuan tindakan kedokteran yang bukan hari ini tidak dapat dihapus']);
            }
            if ($form_persetujuan_tindakan) {
                $db = db_connect();

                // Menghapus form_persetujuan_tindakan
                $this->FRMSetujuKedokteranModel->delete($id);

                // Reset auto increment
                $db->query('ALTER TABLE `medrec_form_persetujuan_tindakan` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
                return $this->response->setJSON(['message' => 'Formulir persetujuan tindakan kedokteran berhasil dihapus']); // Mengembalikan respon sukses
            } else {
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Formulir persetujuan tindakan kedokteran tidak ditemukan', // Pesan jika peran tidak valid
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

            $form_persetujuan_tindakan = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan <', $id)
                ->orderBy('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan >', $id)
                ->orderBy('medrec_form_persetujuan_tindakan.id_form_persetujuan_tindakan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('medrec_form_persetujuan_tindakan')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $form_persetujuan_tindakan['no_rm'])
                ->orderBy('id_form_persetujuan_tindakan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'form_persetujuan_tindakan' => $form_persetujuan_tindakan,
                'dokter' => $dokter,
                'title' => 'Formulir Persetujuan Tindakan Kedokteran ' . $form_persetujuan_tindakan['nama_pasien'] . ' (' . $form_persetujuan_tindakan['no_rm'] . ') - ' . $form_persetujuan_tindakan['nomor_registrasi'] . ' - ' . $this->systemName,
                'headertitle' => 'Formulir Persetujuan Tindakan Kedokteran',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/frmsetujukedokteran/details', $data);
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
            $data = $this->FRMSetujuKedokteranModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_form_persetujuan_tindakan.nomor_registrasi', 'inner')
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
                'dokter_pelaksana' => [
                    'rules' => 'required',
                ],
                'pemberi_informasi' => [
                    'rules' => 'required',
                ],
                'penerima_informasi' => [
                    'rules' => 'required',
                ],
                'pererima_tanggal_lahir' => [
                    'rules' => 'required',
                ],
                'penerima_jenis_kelamin' => [
                    'rules' => 'required',
                ],
                'penerima_alamat' => [
                    'rules' => 'required',
                ],
                'penerima_hubungan' => [
                    'rules' => 'required',
                ],
                'keterangan_hubungan' => [
                    'rules' => $this->request->getPost('penerima_hubungan') === 'KELUARGA' ? 'required' : 'permit_empty',
                ],
                'tindakan_kedoteran' => [
                    'rules' => 'required',
                ],
                'tanggal_tindakan' => [
                    'rules' => 'required',
                ],
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data transaksi
            $data = [
                'dokter_pelaksana' => $this->request->getPost('dokter_pelaksana') ?: null,
                'pemberi_informasi' => $this->request->getPost('pemberi_informasi') ?: null,

                'info_diagnosa' => $this->request->getPost('info_diagnosa') ?: null,
                'info_dasar_diagnosis' => $this->request->getPost('info_dasar_diagnosis') ?: null,
                'info_tindakan' => $this->request->getPost('info_tindakan') ?: null,
                'info_indikasi' => $this->request->getPost('info_indikasi') ?: null,
                'info_tatacara' => $this->request->getPost('info_tatacara') ?: null,
                'info_tujuan' => $this->request->getPost('info_tujuan') ?: null,
                'info_resiko' => $this->request->getPost('info_resiko') ?: null,
                'info_komplikasi' => $this->request->getPost('info_komplikasi') ?: null,
                'info_prognosis' => $this->request->getPost('info_prognosis') ?: null,
                'info_alternatif' => $this->request->getPost('info_alternatif') ?: null,
                'info_lainnya' => $this->request->getPost('info_lainnya') ?: null,

                'penerima_informasi' => $this->request->getPost('penerima_informasi') ?: null,
                'pererima_tanggal_lahir' => $this->request->getPost('pererima_tanggal_lahir') ?: null,
                'penerima_jenis_kelamin' => $this->request->getPost('penerima_jenis_kelamin') ?: null,
                'penerima_alamat' => $this->request->getPost('penerima_alamat') ?: null,
                'penerima_hubungan' => $this->request->getPost('penerima_hubungan') ?: null,
                'keterangan_hubungan' => $this->request->getPost('keterangan_hubungan') ?: null,

                'tindakan_kedoteran' => $this->request->getPost('tindakan_kedoteran') ?: null,
                'tanggal_tindakan' => $this->request->getPost('tanggal_tindakan') ?: null,
                'nama_saksi_1' => $this->request->getPost('nama_saksi_1') ?: null,
                'nama_saksi_2' => $this->request->getPost('nama_saksi_2') ?: null,
            ];
            $db->table('medrec_form_persetujuan_tindakan')->where('id_form_persetujuan_tindakan', $id)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Formulir persetujuan tindakan kedokteran berhasil diperbarui']); // Mengembalikan respon sukses
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
