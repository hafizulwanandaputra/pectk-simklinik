<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\AsesmenModel;
use App\Models\AsesmenMataModel;

class AsesmenMata extends BaseController
{
    protected $RawatJalanModel;
    protected $AsesmenModel;
    protected $AsesmenMataModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->AsesmenModel = new AsesmenModel();
        $this->AsesmenMataModel = new AsesmenMataModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
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
            $asesmen_mata = $db->table('medrec_assesment_mata')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getResultArray();

            return $this->response->setJSON($asesmen_mata);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->AsesmenMataModel->find($id); // Mengambil skrining
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'gambar' => 'uploaded[gambar]|max_size[gambar,8192]|is_image[gambar]',
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
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'keterangan' => $this->request->getPost('keterangan'),
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $this->AsesmenMataModel->save($data);
            $id_eksekusi = $this->AsesmenMataModel->getInsertID();

            // Proses file tanda tangan edukator (jika ada)
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $extension = $gambar->getExtension();
                $gambar_name = 'mata_' . $rawatjalan['nomor_registrasi'] . '_' . $id_eksekusi . '.' . $extension;
                $gambar->move(FCPATH . 'uploads/asesmen_mata', $gambar_name);

                // Update nama file ke database
                $this->AsesmenMataModel->update($id_eksekusi, ['gambar' => $gambar_name]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan fisik berhasil ditambahkan']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'gambar' => 'if_exist|max_size[gambar,8192]|is_image[gambar]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $penunjang_scan = $this->AsesmenMataModel->find($this->request->getPost('id_asesmen_mata'));

            // Simpan data edukasi
            $data = [
                'id_asesmen_mata' => $this->request->getPost('id_asesmen_mata'),
                'nomor_registrasi' => $penunjang_scan['nomor_registrasi'],
                'keterangan' => $this->request->getPost('keterangan'),
                'waktu_dibuat' => $penunjang_scan['waktu_dibuat'],
            ];

            // Unggah gambar
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $extension = $gambar->getExtension();
                $id_asesmen_mata = $this->request->getVar('id_asesmen_mata'); // Pastikan mengambil id yang benar
                if ($penunjang_scan['gambar']) {
                    unlink(FCPATH . 'uploads/asesmen_mata/' . $penunjang_scan['gambar']);
                }
                $gambar_name = 'mata_' . $penunjang_scan['nomor_registrasi'] . '_' . $id_asesmen_mata . '.' . $extension;
                $gambar->move(FCPATH . 'uploads/asesmen_mata', $gambar_name);
                $data['gambar'] = $gambar_name;
            }

            $this->AsesmenMataModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan fisik berhasil diperbarui']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // public function gambar($filename)
    // {
    //     // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
    //     if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
    //         $path = FCPATH . 'uploads/asesmen_mata/' . $filename;

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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $asesmen_mata = $this->AsesmenMataModel->find($id);
            if ($asesmen_mata) {
                // Hapus tanda tangan edukator
                if ($asesmen_mata['gambar']) {
                    unlink(FCPATH . 'uploads/asesmen_mata/' . $asesmen_mata['gambar']);
                }

                // Hapus evaluasi
                $this->AsesmenMataModel->delete($id);
                $db = db_connect();
                // Reset Nilai Auto Increment
                $db->query('ALTER TABLE `medrec_assesment_mata` auto_increment = 1');
                return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan fisik berhasil dihapus']);
            } else {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Pemeriksaan fisik tidak ditemukan']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
