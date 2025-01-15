<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\EdukasiModel;
use App\Models\EdukasiEvaluasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EdukasiEvaluasi extends BaseController
{
    protected $RawatJalanModel;
    protected $EdukasiModel;
    protected $EdukasiEvaluasiModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->EdukasiModel = new EdukasiModel();
        $this->EdukasiEvaluasiModel = new EdukasiEvaluasiModel();
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
                // Mengembalikan status 404 jika peran tidak diizinkan
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Halaman tidak ditemukan',
                ]);
            }

            // Memeriksa apakah evaluasi edukasi sudah ada
            $edukasi_evaluasi = $db->table('medrec_edukasi_evaluasi')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getResultArray();

            return $this->response->setJSON($edukasi_evaluasi);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->EdukasiEvaluasiModel->find($id); // Mengambil skrining
            return $this->response->setJSON($data); // Mengembalikan data skrining dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'unit' => 'required',
                'informasi_edukasi' => 'required',
                'nama_edukator' => 'required',
                'profesi_edukator' => 'required',
                'tanda_tangan_edukator' => 'max_size[tanda_tangan_edukator,8192]|is_image[tanda_tangan_edukator]',
                'nama_pasien_keluarga' => 'required',
                'tanda_tangan_pasien' => 'max_size[tanda_tangan_pasien,8192]|is_image[tanda_tangan_pasien]',
                'evaluasi' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                // Mengembalikan status 404 jika peran tidak diizinkan
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Halaman tidak ditemukan',
                ]);
            }
            // Simpan data edukasi
            $data = [
                'no_rm' => $rawatjalan['no_rm'],
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'unit' => $this->request->getPost('unit'),
                'informasi_edukasi' => $this->request->getPost('informasi_edukasi'),
                'nama_edukator' => $this->request->getPost('nama_edukator'),
                'profesi_edukator' => $this->request->getPost('profesi_edukator'),
                'nama_pasien_keluarga' => $this->request->getPost('nama_pasien_keluarga'),
                'evaluasi' => $this->request->getPost('evaluasi'),
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $this->EdukasiEvaluasiModel->save($data);
            $id_eksekusi = $this->EdukasiEvaluasiModel->getInsertID();

            // Proses file tanda tangan edukator (jika ada)
            $tanda_tangan_edukator = $this->request->getFile('tanda_tangan_edukator');
            if ($tanda_tangan_edukator && $tanda_tangan_edukator->isValid() && !$tanda_tangan_edukator->hasMoved()) {
                $extension = $tanda_tangan_edukator->getExtension();
                $tanda_tangan_edukator_name = 'ttd_edukator_' . $rawatjalan['nomor_registrasi'] . '_' . $id_eksekusi . '.' . $extension;
                $tanda_tangan_edukator->move(FCPATH . 'uploads/ttd_edukator_evaluasi', $tanda_tangan_edukator_name);

                // Update nama file ke database
                $this->EdukasiEvaluasiModel->update($id_eksekusi, ['tanda_tangan_edukator' => $tanda_tangan_edukator_name]);
            }

            // Proses file tanda tangan pasien (jika ada)
            $tanda_tangan_pasien = $this->request->getFile('tanda_tangan_pasien');
            if ($tanda_tangan_pasien && $tanda_tangan_pasien->isValid() && !$tanda_tangan_pasien->hasMoved()) {
                $extension = $tanda_tangan_pasien->getExtension();
                $tanda_tangan_pasien_name = 'ttd_pasien_' . $rawatjalan['nomor_registrasi'] . '_' . $id_eksekusi . '.' . $extension;
                $tanda_tangan_pasien->move(FCPATH . 'uploads/ttd_pasien_evaluasi', $tanda_tangan_pasien_name);

                // Update nama file ke database
                $this->EdukasiEvaluasiModel->update($id_eksekusi, ['tanda_tangan_pasien' => $tanda_tangan_pasien_name]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Evaluasi berhasil ditambahkan']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'unit' => 'required',
                'informasi_edukasi' => 'required',
                'nama_edukator' => 'required',
                'profesi_edukator' => 'required',
                'tanda_tangan_edukator' => 'if_exist|max_size[tanda_tangan_edukator,8192]|is_image[tanda_tangan_edukator]',
                'nama_pasien_keluarga' => 'required',
                'tanda_tangan_pasien' => 'if_exist|max_size[tanda_tangan_pasien,8192]|is_image[tanda_tangan_pasien]',
                'evaluasi' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $edukasi_evaluasi = $this->EdukasiEvaluasiModel->find($this->request->getPost('id_edukasi_evaluasi'));

            // Simpan data edukasi
            $data = [
                'id_edukasi_evaluasi' => $this->request->getPost('id_edukasi_evaluasi'),
                'no_rm' => $edukasi_evaluasi['no_rm'],
                'nomor_registrasi' => $edukasi_evaluasi['nomor_registrasi'],
                'unit' => $this->request->getPost('unit'),
                'informasi_edukasi' => $this->request->getPost('informasi_edukasi'),
                'nama_edukator' => $this->request->getPost('nama_edukator'),
                'profesi_edukator' => $this->request->getPost('profesi_edukator'),
                'nama_pasien_keluarga' => $this->request->getPost('nama_pasien_keluarga'),
                'evaluasi' => $this->request->getPost('evaluasi'),
                'waktu_dibuat' => $edukasi_evaluasi['waktu_dibuat'],
            ];

            // Unggah tanda-tangan edukator
            $tanda_tangan_edukator = $this->request->getFile('tanda_tangan_edukator');
            if ($tanda_tangan_edukator && $tanda_tangan_edukator->isValid() && !$tanda_tangan_edukator->hasMoved()) {
                $extension = $tanda_tangan_edukator->getExtension();
                $id_edukasi_evaluasi = $this->request->getVar('id_edukasi_evaluasi'); // Pastikan mengambil id yang benar
                if ($edukasi_evaluasi['tanda_tangan_edukator']) {
                    unlink(FCPATH . 'uploads/ttd_edukator_evaluasi/' . $edukasi_evaluasi['tanda_tangan_edukator']);
                }
                $tanda_tangan_edukator_name = 'ttd_edukator_' . $edukasi_evaluasi['nomor_registrasi'] . '_' . $id_edukasi_evaluasi . '.' . $extension;
                $tanda_tangan_edukator->move(FCPATH . 'uploads/ttd_edukator_evaluasi', $tanda_tangan_edukator_name);
                $data['tanda_tangan_edukator'] = $tanda_tangan_edukator_name;
            }

            // Unggah tanda-tangan pasien
            $tanda_tangan_pasien = $this->request->getFile('tanda_tangan_pasien');
            if ($tanda_tangan_pasien && $tanda_tangan_pasien->isValid() && !$tanda_tangan_pasien->hasMoved()) {
                $extension = $tanda_tangan_pasien->getExtension();
                $id_edukasi_evaluasi = $this->request->getVar('id_edukasi_evaluasi'); // Ambil id yang benar
                if ($edukasi_evaluasi['tanda_tangan_pasien']) {
                    unlink(FCPATH . 'uploads/ttd_pasien_evaluasi/' . $edukasi_evaluasi['tanda_tangan_pasien']);
                }
                $tanda_tangan_pasien_name = 'ttd_pasien_' . $edukasi_evaluasi['nomor_registrasi'] . '_' . $id_edukasi_evaluasi . '.' . $extension;
                $tanda_tangan_pasien->move(FCPATH . 'uploads/ttd_pasien_evaluasi', $tanda_tangan_pasien_name);
                $data['tanda_tangan_pasien'] = $tanda_tangan_pasien_name;
            }

            $this->EdukasiEvaluasiModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Evaluasi berhasil diperbarui']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // public function tandatanganedukator($filename)
    // {
    //     // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
    //     if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
    //         $path = FCPATH . 'uploads/ttd_edukator_evaluasi/' . $filename;

    //         if (is_file($path)) {
    //             $mime = mime_content_type($path);
    //             header('Content-Type: ' . $mime);
    //             readfile($path);
    //             exit;
    //         }

    //         throw PageNotFoundException::forPageNotFound();
    //     } else {
    //         // Jika peran tidak dikenali, lemparkan pengecualian 404
    //         throw PageNotFoundException::forPageNotFound();
    //     }
    // }

    // public function tandatanganpasien($filename)
    // {
    //     // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
    //     if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
    //         $path = FCPATH . 'uploads/ttd_pasien_evaluasi/' . $filename;

    //         if (is_file($path)) {
    //             $mime = mime_content_type($path);
    //             header('Content-Type: ' . $mime);
    //             readfile($path);
    //             exit;
    //         }

    //         throw PageNotFoundException::forPageNotFound();
    //     } else {
    //         // Jika peran tidak dikenali, lemparkan pengecualian 404
    //         throw PageNotFoundException::forPageNotFound();
    //     }
    // }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $edukasi_evaluasi = $this->EdukasiEvaluasiModel->find($id);
            if ($edukasi_evaluasi) {
                // Hapus tanda tangan edukator
                if ($edukasi_evaluasi['tanda_tangan_edukator']) {
                    unlink(FCPATH . 'uploads/ttd_edukator_evaluasi/' . $edukasi_evaluasi['tanda_tangan_edukator']);
                }

                // Hapus tanda tangan pasien
                if ($edukasi_evaluasi['tanda_tangan_pasien']) {
                    unlink(FCPATH . 'uploads/ttd_pasien_evaluasi/' . $edukasi_evaluasi['tanda_tangan_pasien']);
                }

                // Hapus evaluasi
                $this->EdukasiEvaluasiModel->delete($id);
                $db = db_connect();
                // Reset Nilai Auto Increment
                $db->query('ALTER TABLE `medrec_edukasi_evaluasi` auto_increment = 1');
                return $this->response->setJSON(['success' => true, 'message' => 'Evaluasi berhasil dihapus']);
            } else {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Evaluasi tidak ditemukan']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
