<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\AsesmenModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Picqer\Barcode\BarcodeGeneratorPNG;

class FRMPeriksaPasien extends BaseController
{
    protected $RawatJalanModel;
    protected $AsesmenModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->AsesmenModel = new AsesmenModel();
    }
    public function index()
    {
        if (session()->get('role') == 'Admisi' || session()->get('role') == 'Kasir') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Formulir Pemeriksaan Pasien - ' . $this->systemName,
                'headertitle' => 'Formulir Pemeriksaan Pasien',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/frmperiksapasien/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function frmperiksapasienlist()
    {
        if (session()->get('role') == 'Admisi' || session()->get('role') == 'Kasir') {

            $db = db_connect();

            $tanggal = $this->request->getGet('tanggal');
            $search  = $this->request->getGet('search');
            $limit   = $this->request->getGet('limit');
            $offset  = $this->request->getGet('offset');

            $limit  = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // 🔥 SELECT EXPLISIT (hindari SELECT *)
            $AsesmenModel = $this->AsesmenModel
                ->select('
                    medrec_assesment.*,
                    rawat_jalan.*,
                    pasien.*,
                    rawat_jalan.jaminan AS jaminan_kode
                ')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_assesment.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner');

            // Filter tanggal
            if ($tanggal) {
                $AsesmenModel->like('rawat_jalan.tanggal_registrasi', $tanggal);
            }

            // Filter search
            if ($search) {
                $AsesmenModel->groupStart()
                    ->like('pasien.no_rm', $search)
                    ->orLike('pasien.nama_pasien', $search)
                    ->groupEnd();
            }

            // Total data
            $total = $AsesmenModel->countAllResults(false);

            // Data
            $Pasien = $AsesmenModel
                ->orderBy('id_asesmen', 'DESC')
                ->findAll($limit, $offset);

            // =========================
            // 🔥 MAPPING JAMINAN
            // =========================
            $jaminanList = $db->table('master_jaminan')
                ->select('jaminanKode, jaminanNama')
                ->get()
                ->getResultArray();

            $jaminanMap = [];
            foreach ($jaminanList as $jaminan) {
                $jaminanMap[$jaminan['jaminanKode']] = $jaminan['jaminanNama'];
            }

            // =========================
            // 🔥 NOMOR DESCENDING
            // =========================
            foreach ($Pasien as $i => &$rajal) {

                // 🔥 PAKSA mulai dari 1
                $rajal['number'] = $i + 1;

                $kodeJaminan = trim((string) ($rajal['jaminan_kode'] ?? ''));

                unset($rajal['jaminan']);
                $rajal['jaminan'] = $jaminanMap[$kodeJaminan] ?? 'Tidak Diketahui';
            }
            unset($rajal);

            return $this->response->setJSON([
                'form_pemeriksaan_pasien' => $Pasien,
                'total' => $total
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function export($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Admisi' || session()->get('role') == 'Kasir' || session()->get('role') == 'Manajer') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $form_pemeriksaan_pasien = $this->AsesmenModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_assesment.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            // === Ambil data resep ===
            $resep = $db->table('resep')
                ->where('nomor_registrasi', $form_pemeriksaan_pasien['nomor_registrasi'])
                ->get()
                ->getResultArray();

            // === Ambil detail resep per resep ===
            foreach ($resep as &$r) {
                $r['detail_resep'] = $db->table('detail_resep')
                    ->where('id_resep', $r['id_resep'])
                    ->get()
                    ->getResultArray();
            }

            // Ambil tabel master_provinsi
            $provinsi = $db->table('master_provinsi');
            $provinsi->select('UPPER(provinsiNama) AS provinsiNama');
            $provinsi->where('provinsiId', $form_pemeriksaan_pasien['provinsi']);

            // Query untuk mendapatkan nama provinsi
            $res_provinsi = $provinsi->get()->getRow();

            if ($res_provinsi) {
                // Ubah ID menjadi nama provinsi
                $form_pemeriksaan_pasien['provinsi'] = $res_provinsi->provinsiNama;
            }

            // Ambil tabel master_kabupaten
            $kabupaten = $db->table('master_kabupaten');
            $kabupaten->select('UPPER(kabupatenNama) AS kabupatenNama');
            $kabupaten->where('kabupatenId', $form_pemeriksaan_pasien['kabupaten']);

            // Query untuk mendapatkan nama kabupaten
            $res_kabupaten = $kabupaten->get()->getRow();

            if ($res_kabupaten) {
                // Ubah ID menjadi nama kabupaten
                $form_pemeriksaan_pasien['kabupaten'] = $res_kabupaten->kabupatenNama;
            }

            // Ambil tabel master_kecamatan
            $kecamatan = $db->table('master_kecamatan');
            $kecamatan->select('UPPER(kecamatanNama) AS kecamatanNama');
            $kecamatan->where('kecamatanId', $form_pemeriksaan_pasien['kecamatan']);

            // Query untuk mendapatkan nama kecamatan
            $res_kecamatan = $kecamatan->get()->getRow();

            if ($res_kecamatan) {
                // Ubah ID menjadi nama kecamatan
                $form_pemeriksaan_pasien['kecamatan'] = $res_kecamatan->kecamatanNama;
            }

            // Ambil tabel master_kelurahan
            $kelurahan = $db->table('master_kelurahan');
            $kelurahan->select('UPPER(kelurahanNama) AS kelurahanNama');
            $kelurahan->where('kelurahanId', $form_pemeriksaan_pasien['kelurahan']);

            // Query untuk mendapatkan nama kelurahan
            $res_kelurahan = $kelurahan->get()->getRow();

            if ($res_kelurahan) {
                // Ubah ID menjadi nama kelurahan
                $form_pemeriksaan_pasien['kelurahan'] = $res_kelurahan->kelurahanNama;
            }

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($form_pemeriksaan_pasien['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            $jaminanRow = $db->table('master_jaminan')
                ->select('jaminanNama')
                ->where('jaminanKode', $form_pemeriksaan_pasien['jaminan'])
                ->get()
                ->getRow();

            if ($jaminanRow) {
                // TIMPA: kode -> nama
                $form_pemeriksaan_pasien['jaminan'] = $jaminanRow->jaminanNama;
            }

            // Memeriksa apakah pasien tidak kosong
            if ($form_pemeriksaan_pasien) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'form_pemeriksaan_pasien' => $form_pemeriksaan_pasien,
                    'resep' => $resep,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Formulir Pemeriksaan Pasien ' . $form_pemeriksaan_pasien['nama_pasien'] . ' (' . $form_pemeriksaan_pasien['no_rm'] . ') - ' . $form_pemeriksaan_pasien['nomor_registrasi'] . ' - ' . $this->systemName,
                    'headertitle' => 'Formulir Pemeriksaan Pasien',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/frmperiksapasien/form', $data);
                // die;
                $client = new Client(); // pakai Guzzle langsung
                $html = view('dashboard/frmperiksapasien/form', $data);
                $filename = 'output-form-pemeriksaan-pasien.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'format' => 'A4',
                                'margin' => [
                                    'top' => '1cm',
                                    'right' => '1cm',
                                    'bottom' => '1cm',
                                    'left' => '1cm'
                                ]
                            ]
                        ]
                    ]);

                    $rawBody = $response->getBody()->getContents();
                    $result = json_decode($rawBody, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return $this->response
                            ->setStatusCode($response->getStatusCode())
                            ->setBody("Gagal membuat PDF. Respons worker:\n\n" . esc($rawBody));
                    }

                    if (!empty($result['success']) && $result['success']) {
                        $path = WRITEPATH . 'temp/' . $result['file'];

                        if (!is_file($path)) {
                            return $this->response
                                ->setStatusCode(500)
                                ->setBody("PDF berhasil dibuat tapi file tidak ditemukan: $path");
                        }

                        return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                            ->setBody(file_get_contents($path));
                    } else {
                        $errorMessage = $result['error'] ?? 'Tidak diketahui';
                        $errorDetails = $result['details'] ?? '';

                        return $this->response
                            ->setStatusCode(500)
                            ->setBody(
                                "Gagal membuat PDF: " . esc($errorMessage) .
                                    (!empty($errorDetails) ? "\n\nDetail:\n" . esc($errorDetails) : '')
                            );
                    }
                } catch (RequestException $e) {
                    // Ambil pesan default
                    $errorMessage = "Kesalahan saat request ke PDF worker: " . $e->getMessage();

                    // Kalau ada response dari worker
                    if ($e->hasResponse()) {
                        $errorBody = (string) $e->getResponse()->getBody();

                        $json = json_decode($errorBody, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($json['error'])) {
                            $errorMessage .= "\n\nPesan dari worker: " . esc($json['error']);
                            if (!empty($json['details'])) {
                                $errorMessage .= "\n\nDetail:\n" . esc($json['details']);
                            }
                        } else {
                            $errorMessage .= "\n\nRespons worker:\n" . esc($errorBody);
                        }
                    }

                    return $this->response
                        ->setStatusCode(500)
                        ->setBody($errorMessage);
                }
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
