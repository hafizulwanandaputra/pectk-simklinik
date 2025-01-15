<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RawatJalanModel;
use App\Models\PenunjangModel;
use App\Models\PenunjangScanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PenunjangScan extends BaseController
{
    protected $RawatJalanModel;
    protected $PenunjangModel;
    protected $PenunjangScanModel;
    public function __construct()
    {
        $this->RawatJalanModel = new RawatJalanModel();
        $this->PenunjangModel = new PenunjangModel();
        $this->PenunjangScanModel = new PenunjangScanModel();
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
                // Mengembalikan status 404 jika peran tidak diizinkan
                return $this->response->setStatusCode(404)->setJSON([
                    'error' => 'Halaman tidak ditemukan',
                ]);
            }

            // Memeriksa apakah evaluasi edukasi sudah ada
            $penunjang_scan = $db->table('medrec_permintaan_penunjang_scan')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getResultArray();

            return $this->response->setJSON($penunjang_scan);
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Mengambil data skrining berdasarkan ID
            $data = $this->PenunjangScanModel->find($id); // Mengambil skrining
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
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'pemeriksaan' => 'required',
                'gambar' => 'max_size[gambar,8192]|is_image[gambar]',
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
                'pemeriksaan' => $this->request->getPost('pemeriksaan'),
                'keterangan' => $this->request->getPost('keterangan'),
                'waktu_dibuat' => date('Y-m-d H:i:s'),
            ];
            $this->PenunjangScanModel->save($data);
            $id_eksekusi = $this->PenunjangScanModel->getInsertID();

            // Proses file tanda tangan edukator (jika ada)
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $extension = $gambar->getExtension();
                $gambar_name = $this->request->getPost('pemeriksaan') . '_' . $rawatjalan['nomor_registrasi'] . '_' . $id_eksekusi . '.' . $extension;
                $gambar->move(WRITEPATH . 'uploads/scan_penunjang', $gambar_name);

                // Update nama file ke database
                $this->PenunjangScanModel->update($id_eksekusi, ['gambar' => $gambar_name]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Pemindaian berhasil ditambahkan']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'pemeriksaan' => 'required',
                'gambar' => 'if_exist|max_size[gambar,8192]|is_image[gambar]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $penunjang_scan = $this->PenunjangScanModel->find($this->request->getPost('id_penunjang_scan'));

            // Simpan data edukasi
            $data = [
                'id_penunjang_scan' => $this->request->getPost('id_penunjang_scan'),
                'nomor_registrasi' => $penunjang_scan['nomor_registrasi'],
                'pemeriksaan' => $this->request->getPost('pemeriksaan'),
                'keterangan' => $this->request->getPost('keterangan'),
                'waktu_dibuat' => $penunjang_scan['waktu_dibuat'],
            ];

            // Unggah gambar
            $gambar = $this->request->getFile('gambar');
            if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
                $extension = $gambar->getExtension();
                $id_penunjang_scan = $this->request->getVar('id_penunjang_scan'); // Pastikan mengambil id yang benar
                if ($penunjang_scan['gambar']) {
                    unlink(WRITEPATH . 'uploads/scan_penunjang/' . $penunjang_scan['gambar']);
                }
                $gambar_name = $this->request->getPost('pemeriksaan') . '_' . $penunjang_scan['nomor_registrasi'] . '_' . $id_penunjang_scan . '.' . $extension;
                $gambar->move(WRITEPATH . 'uploads/scan_penunjang', $gambar_name);
                $data['gambar'] = $gambar_name;
            }

            $this->PenunjangScanModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemindaian berhasil diperbarui']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function gambar($filename)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            $path = WRITEPATH . 'uploads/scan_penunjang/' . $filename;

            if (is_file($path)) {
                $mime = mime_content_type($path);
                header('Content-Type: ' . $mime);
                readfile($path);
                exit;
            }

            throw PageNotFoundException::forPageNotFound();
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter'  || session()->get('role') == 'Perawat') {
            $penunjang_scan = $this->PenunjangScanModel->find($id);
            if ($penunjang_scan) {
                // Hapus tanda tangan edukator
                if ($penunjang_scan['gambar']) {
                    unlink(WRITEPATH . 'uploads/scan_penunjang/' . $penunjang_scan['gambar']);
                }

                // Hapus evaluasi
                $this->PenunjangScanModel->delete($id);
                $db = db_connect();
                // Reset Nilai Auto Increment
                $db->query('ALTER TABLE `medrec_permintaan_penunjang_scan` auto_increment = 1');
                return $this->response->setJSON(['success' => true, 'message' => 'Pemindaian berhasil dihapus']);
            } else {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Pemindaian tidak ditemukan']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
