<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SPOperasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;

class SPOperasi extends BaseController
{
    protected $SPOperasiModel;
    public function __construct()
    {
        $this->SPOperasiModel = new SPOperasiModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
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

            $master_tindakan_operasi = $db->table('master_tindakan_operasi')
                ->orderBy('nama_tindakan', 'ASC')
                ->get()->getResultArray();

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
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
                'master_tindakan_operasi' => $master_tindakan_operasi,
                'dokter' => $dokter,
                'title' => 'Surat Perintah Kamar Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                'headertitle' => 'Surat Perintah Kamar Operasi',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman skrining
            return view('dashboard/operasi/spko/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id); // Mengambil skrining
            $data['jenis_tindakan'] = explode(',', $data['jenis_tindakan']); // Ubah CSV menjadi array
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

            $sp_operasi['jenis_tindakan'] = str_replace(',', '<br>', $sp_operasi['jenis_tindakan']);


            // Memeriksa apakah pasien tidak kosong
            if ($sp_operasi) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'operasi' => $sp_operasi,
                    'title' => 'Surat Perintah Kamar Operasi ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                    'headertitle' => 'Surat Perintah Kamar Operasi',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/operasi/spko/form', $data);
                // die;
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/operasi/spko/form', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('SPKO_' . $sp_operasi['nomor_booking'] . '_' . str_replace('-', '', $sp_operasi['no_rm']) . '.pdf', [
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            // Ambil SPKO
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);
            // Set base validation rules
            $site_marking_validation = ($sp_operasi['site_marking'] == NULL) ? 'uploaded[site_marking]|' : '';
            $validation->setRules([
                'tanggal_operasi' => 'required',
                'jam_operasi' => 'required',
                'jenis_tindakan' => 'required',
                'jenis_bius' => 'required',
                'rajal_ranap' => 'required',
                'ruang_operasi' => 'required',
                'dokter_operator' => 'required',
                'site_marking' => $site_marking_validation . 'max_size[site_marking,8192]|is_image[site_marking]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Proses data jenis_tindakan dari select multiple
            $jenis_tindakan = $this->request->getPost('jenis_tindakan');
            $jenis_tindakan_csv = is_array($jenis_tindakan) ? implode(',', $jenis_tindakan) : NULL;

            // Simpan data edukasi
            $data = [
                'id_sp_operasi' => $id,
                'nomor_booking' => $sp_operasi['nomor_booking'],
                'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                'no_rm' => $sp_operasi['no_rm'],
                'tanggal_operasi' => $this->request->getPost('tanggal_operasi') ?: NULL,
                'jam_operasi' => $this->request->getPost('jam_operasi') ?: NULL,
                'diagnosa' => $this->request->getPost('diagnosa') ?: NULL,
                'jenis_tindakan' => $jenis_tindakan_csv,
                'indikasi_operasi' => $this->request->getPost('indikasi_operasi') ?: NULL,
                'jenis_bius' => $this->request->getPost('jenis_bius') ?: NULL,
                'tipe_bayar' => $this->request->getPost('tipe_bayar') ?: NULL,
                'rajal_ranap' => $this->request->getPost('rajal_ranap') ?: NULL,
                'ruang_operasi' => $this->request->getPost('ruang_operasi') ?: NULL,
                'dokter_operator' => $this->request->getPost('dokter_operator') ?: NULL,
                'status_operasi' => $sp_operasi['status_operasi'],
                'diagnosa_site_marking' => $this->request->getPost('diagnosa_site_marking') ?: NULL,
                'tindakan_site_marking' => $this->request->getPost('tindakan_site_marking') ?: NULL,
                'nama_pasien_keluarga' => $this->request->getPost('nama_pasien_keluarga') ?: NULL,
                'tanda_tangan_pasien' => $sp_operasi['tanda_tangan_pasien'],
                'waktu_dibuat' => $sp_operasi['waktu_dibuat'],
            ];

            // Unggah site marking
            $site_marking = $this->request->getFile('site_marking');
            if ($site_marking && $site_marking->isValid() && !$site_marking->hasMoved()) {
                $extension = $site_marking->getExtension();
                $id_penunjang_scan = $this->request->getVar('id_penunjang_scan'); // Pastikan mengambil id yang benar
                if ($sp_operasi['site_marking']) {
                    @unlink(FCPATH . 'uploads/site_marking/' . $sp_operasi['site_marking']);
                }
                $site_marking_name = $sp_operasi['no_rm'] . '_' . $sp_operasi['nomor_booking'] . '.' . $extension;
                $site_marking->move(FCPATH . 'uploads/site_marking', $site_marking_name);
                $data['site_marking'] = $site_marking_name;
            }

            $this->SPOperasiModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'SPKO berhasil diperbarui']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
