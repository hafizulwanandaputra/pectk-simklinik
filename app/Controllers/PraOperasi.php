<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SPOperasiModel;
use App\Models\PraOperasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

use Picqer\Barcode\BarcodeGeneratorPNG;

class PraOperasi extends BaseController
{
    protected $SPOperasiModel;
    protected $PraOperasiModel;
    public function __construct()
    {
        $this->SPOperasiModel = new SPOperasiModel();
        $this->PraOperasiModel = new PraOperasiModel();
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

            $operasi_pra = $db->table('medrec_operasi_pra')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_pra) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_pra')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_pra = $db->table('medrec_operasi_pra')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $perawat = $db->table('user')
                ->where('role', 'Perawat')
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
                'operasi_pra' => $operasi_pra,
                'perawat' => $perawat,
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

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data pra operasi berdasarkan ID
            $data = $this->PraOperasiModel->find($id); // Mengambil pra operasi
            $data['ctt_riwayat_sakit'] = explode(',', $data['ctt_riwayat_sakit']);
            return $this->response->setJSON($data); // Mengembalikan data pra operasi dalam format JSON
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

            $operasi_pra = $db->table('medrec_operasi_pra')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($sp_operasi['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            $operasi_pra['ctt_riwayat_sakit'] = str_replace(',', ', ', $operasi_pra['ctt_riwayat_sakit']);

            // Memeriksa apakah pasien tidak kosong
            if ($operasi_pra) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'operasi' => $sp_operasi,
                    'operasi_pra' => $operasi_pra,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Pemeriksaan Pra Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                    'headertitle' => 'Pemeriksaan Pra Operasi',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/operasi/praoperasi/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-praoperasi.html';
                file_put_contents($htmlFile, view('dashboard/operasi/praoperasi/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-praoperasi.pdf';

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
                        ->setHeader('Content-Disposition', 'inline; filename="PraOperasi_' . $sp_operasi['nomor_booking'] . '_' . str_replace('-', '', $sp_operasi['no_rm']) . '.pdf')
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

    public function update($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();
            $validation = \Config\Services::validation();

            $operasi_pra = $db->table('medrec_operasi_pra')
                ->where('id_operasi_pra', $id)
                ->get()
                ->getRowArray();
            $sp_operasi = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('nomor_booking', $operasi_pra['nomor_booking'])
                ->get()
                ->getRowArray();
            $ctt_alergi = $this->request->getPost('ctt_alergi');
            $validation->setRules([
                'perawat_praoperasi' => 'required',
                'jenis_operasi' => 'required',
                'ctt_vital_suhu' => 'required',
                'ctt_vital_nadi' => 'required',
                'ctt_vital_rr' => 'required',
                'ctt_vital_td' => 'required',
                'ctt_mental' => 'required',
                'ctt_alergi' => 'required',
                'ctt_alergi_jelaskan' => $ctt_alergi === 'YA' ? 'required' : 'permit_empty',
                'ctt_haid' => $sp_operasi['jenis_kelamin'] === 'P' ? 'required' : 'permit_empty',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $ctt_riwayat_sakit = $this->request->getPost('ctt_riwayat_sakit');
            $ctt_riwayat_sakit_csv = is_array($ctt_riwayat_sakit) ? implode(',', $ctt_riwayat_sakit) : NULL;

            $data = [
                'id_operasi_pra' => $id,
                'nomor_booking' => $operasi_pra['nomor_booking'],
                'nomor_registrasi' => $operasi_pra['nomor_registrasi'],
                'no_rm' => $operasi_pra['no_rm'],
                'perawat_praoperasi' => $this->request->getPost('perawat_praoperasi') ?: NULL,
                'jenis_operasi' => $this->request->getPost('jenis_operasi') ?: NULL,
                // BAGIAN A
                'ctt_vital_suhu' => $this->request->getPost('ctt_vital_suhu') ?: NULL,
                'ctt_vital_nadi' => $this->request->getPost('ctt_vital_nadi') ?: NULL,
                'ctt_vital_rr' => $this->request->getPost('ctt_vital_rr') ?: NULL,
                'ctt_vital_td' => $this->request->getPost('ctt_vital_td') ?: NULL,
                'ctt_vital_nyeri' => $this->request->getPost('ctt_vital_nyeri') ?: NULL,
                'ctt_vital_tb' => $this->request->getPost('ctt_vital_tb') ?: NULL,
                'ctt_vital_bb' => $this->request->getPost('ctt_vital_bb') ?: NULL,
                'ctt_mental' => $this->request->getPost('ctt_mental') ?: NULL,
                'ctt_riwayat_sakit' => $ctt_riwayat_sakit_csv,
                'ctt_riwayat_sakit_lain' => $this->request->getPost('ctt_riwayat_sakit_lain') ?: NULL,
                'ctt_pengobatan_sekarang' => $this->request->getPost('ctt_pengobatan_sekarang') ?: NULL,
                'ctt_alat_bantu' => $this->request->getPost('ctt_alat_bantu') ?: NULL,
                'ctt_operasi_jenis' => $this->request->getPost('ctt_operasi_jenis') ?: NULL,
                'ctt_operasi_tanggal' => $this->request->getPost('ctt_operasi_tanggal') ?: NULL,
                'ctt_operasi_lokasi' => $this->request->getPost('ctt_operasi_lokasi') ?: NULL,
                'ctt_alergi' => $this->request->getPost('ctt_alergi') ?: NULL,
                'ctt_alergi_jelaskan' => $this->request->getPost('ctt_alergi_jelaskan') ?: NULL,
                'ctt_lab_hb' => $this->request->getPost('ctt_lab_hb') ?: NULL,
                'ctt_lab_bt' => $this->request->getPost('ctt_lab_bt') ?: NULL,
                'ctt_lab_ctaptt' => $this->request->getPost('ctt_lab_ctaptt') ?: NULL,
                'ctt_lab_goldarah' => $this->request->getPost('ctt_lab_goldarah') ?: NULL,
                'ctt_lab_urin' => $this->request->getPost('ctt_lab_urin') ?: NULL,
                'ctt_lab_lainnya' => $this->request->getPost('ctt_lab_lainnya') ?: NULL,
                'ctt_haid' => $this->request->getPost('ctt_haid') ?: NULL,
                'ctt_kepercayaan' => $this->request->getPost('ctt_kepercayaan') ?: NULL,
                // BAGIAN B
                'cek_biometri' => $this->request->getPost('cek_biometri') ?: NULL,
                'cek_retinometri' => $this->request->getPost('cek_retinometri') ?: NULL,
                'cek_labor' => $this->request->getPost('cek_labor') ?: NULL,
                'cek_radiologi' => $this->request->getPost('cek_radiologi') ?: NULL,
                'cek_puasa' => $this->request->getPost('cek_puasa') ?: NULL,
                'cek_instruksi' => $this->request->getPost('cek_instruksi') ?: NULL,
                'cek_lensa' => $this->request->getPost('cek_lensa') ?: NULL,
                'cek_rotgen' => $this->request->getPost('cek_rotgen') ?: NULL,
                'cek_rotgen_usia' => $this->request->getPost('cek_rotgen_usia') ?: NULL,
                'cek_rotgen_konsul' => $this->request->getPost('cek_rotgen_konsul') ?: NULL,
                'cek_penyakit' => $this->request->getPost('cek_penyakit') ?: NULL,
                'cek_hepatitis_akhir' => $this->request->getPost('cek_hepatitis_akhir') ?: NULL,
                'cek_penyakit_lainnya' => $this->request->getPost('cek_penyakit_lainnya') ?: NULL,
                'cek_tekanan_darah' => $this->request->getPost('cek_tekanan_darah') ?: NULL,
                'cek_berat_badan' => $this->request->getPost('cek_berat_badan') ?: NULL,
                'cek_foto_fundus' => $this->request->getPost('cek_foto_fundus') ?: NULL,
                'cek_usg' => $this->request->getPost('cek_usg') ?: NULL,
                'cek_perhiasan' => $this->request->getPost('cek_perhiasan') ?: NULL,
                'cek_ttd' => $this->request->getPost('cek_ttd') ?: NULL,
                'cek_cuci' => $this->request->getPost('cek_cuci') ?: NULL,
                'cek_mark' => $this->request->getPost('cek_mark') ?: NULL,
                'cek_tetes_pantocain' => $this->request->getPost('cek_tetes_pantocain') ?: NULL,
                'cek_tetes_efrisel1' => $this->request->getPost('cek_tetes_efrisel1') ?: NULL,
                'cek_tetes_efrisel2' => $this->request->getPost('cek_tetes_efrisel2') ?: NULL,
                'cek_tetes_midriatil1' => $this->request->getPost('cek_tetes_midriatil1') ?: NULL,
                'cek_tetes_midriatil2' => $this->request->getPost('cek_tetes_midriatil2') ?: NULL,
                'cek_tetes_midriatil3' => $this->request->getPost('cek_tetes_midriatil3') ?: NULL,
                'cek_makan' => $this->request->getPost('cek_makan') ?: NULL,
                'cek_obat' => $this->request->getPost('cek_obat') ?: NULL,
                'cek_jenis_obat' => $this->request->getPost('cek_jenis_obat') ?: NULL,
                'waktu_dibuat' => $operasi_pra['waktu_dibuat'],
            ];

            $this->PraOperasiModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan pra operasi berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
        }
    }
}
