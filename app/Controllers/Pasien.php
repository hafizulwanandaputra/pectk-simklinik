<?php

namespace App\Controllers;

use App\Models\PasienModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pasien extends BaseController
{
    protected $PasienModel;
    public function __construct()
    {
        $this->PasienModel = new PasienModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Pasien - ' . $this->systemName,
                'headertitle' => 'Pasien',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/pasien/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $PasienModel = $this->PasienModel;

            // Menerapkan filter pencarian berdasarkan nomor rekam medis dan nama pasien, pasien
            if ($search) {
                $PasienModel->groupStart()
                    ->like('no_rm', $search)
                    ->orLike('nama_pasien', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $PasienModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Pasien = $PasienModel->orderBy('id_pasien', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap pasien
            $dataPasien = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data; // Mengembalikan data yang telah ditambahkan nomor urut
            }, $Pasien, array_keys($Pasien));

            // Mengembalikan data pasien dalam format JSON
            return $this->response->setJSON([
                'pasien' => $dataPasien,
                'total' => $total // Mengembalikan total hasil
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Menghasilkan nomor rekam medis baru
            $lastRecord = $this->PasienModel->orderBy('id_pasien', 'DESC')->first(); // Dapatkan data terakhir berdasarkan ID
            $lastNoRm = $lastRecord ? str_replace('-', '', $lastRecord['no_rm']) : '000000'; // Nomor default jika tidak ada data

            $newNoRmNumeric = (int)$lastNoRm + 1; // Auto increment
            $newNoRm = str_pad($newNoRmNumeric, 6, '0', STR_PAD_LEFT); // Pastikan panjangnya 6 digit
            $formattedNoRm = substr($newNoRm, 0, 2) . '-' . substr($newNoRm, 2, 2) . '-' . substr($newNoRm, 4, 2); // Format xx-xx-xx

            // Simpan data pasien
            $data = [
                'no_rm' => $formattedNoRm,
                'nama_pasien' => NULL,
                'nik' => NULL,
                'no_bpjs' => NULL,
                'tempat_lahir' => NULL,
                'tanggal_lahir' => NULL,
                'jenis_kelamin' => NULL,
                'alamat' => NULL,
                'provinsi' => NULL,
                'kabupaten' => NULL,
                'kecamatan' => NULL,
                'kelurahan' => NULL,
                'rt' => NULL,
                'rw' => NULL,
                'telpon' => NULL,
                'kewarganegaraan' => NULL,
                'agama' => NULL,
                'status_nikah' => NULL,
                'pekerjaan' => NULL,
            ];
            $this->PasienModel->insert($data);

            // Dapatkan ID dari data yang baru disimpan
            $newId = $this->PasienModel->insertID();

            // Redirect ke halaman detail pasien
            return redirect()->to(base_url('pasien/detailpasien/' . $newId));
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', dan 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Menghubungkan ke database
            $db = db_connect();

            // ambil pasien berdasarkan ID
            $pasien = $this->PasienModel
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('pasien')
                ->orderBy('pasien.id_pasien', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('pasien')
                ->orderBy('pasien.id_pasien', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Memeriksa apakah pasien tidak kosong
            if (!empty($pasien)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'pasien' => $pasien,
                    'title' => 'Detail Pasien ' . $pasien['nama_pasien'] . ' (ID ' . $pasien['no_rm'] . ') - ' . $this->systemName,
                    'systemname' => $this->systemName,
                    'headertitle' => 'Detail Pasien',
                    'agent' => $this->request->getUserAgent(), // Menyimpan informasi tentang user agent
                    'previous' => $previous,
                    'next' => $next
                ];
                // Mengembalikan tampilan detail pasien
                return view('dashboard/pasien/details', $data);
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasien($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Mengambil data pasien berdasarkan ID
            $data = $this->PasienModel->find($id); // Mengambil pasien
            return $this->response->setJSON($data); // Mengembalikan data pasien dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function provinsi()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data provinsi
            $builder = $db->table('reg_provinces');
            $result = $builder->select('id, name')->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function kabupaten($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kabupaten
            $builder = $db->table('reg_regencies');
            $result = $builder->select('id, name')->where('province_id', $id)->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function kecamatan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kecamatan
            $builder = $db->table('reg_districts');
            $result = $builder->select('id, name')->where('regency_id', $id)->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function kelurahan($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Rekam Medis' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Membuat koneksi ke database
            $db = db_connect();

            // Menggunakan Query Builder untuk mengambil data kecamatan
            $builder = $db->table('reg_villages');
            $result = $builder->select('id, name')->where('district_id', $id)->get()->getResultArray();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
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
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Rekam Medis') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_pasien' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'kelurahan' => 'required',
                'kewarganegaraan' => 'required',
                'agama' => 'required',
                'status_nikah' => 'required',
                'pekerjaan' => 'required',
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $pasien = $this->PasienModel->find($id);

            // Simpan data pasien
            $data = [
                'id_pasien' => $id,
                'no_rm' => $pasien['no_rm'],
                'nama_pasien' => $this->request->getPost('nama_pasien'),
                'nik' => $this->request->getPost('nik') ?: NULL,
                'no_bpjs' => $this->request->getPost('no_bpjs') ?: NULL,
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'alamat' => $this->request->getPost('alamat'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kabupaten' => $this->request->getPost('kabupaten'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'kelurahan' => $this->request->getPost('kelurahan'),
                'rt' => $this->request->getPost('rt') ?: NULL,
                'rw' => $this->request->getPost('rw') ?: NULL,
                'telpon' => $this->request->getPost('telpon'),
                'kewarganegaraan' => $this->request->getPost('kewarganegaraan'),
                'agama' => $this->request->getPost('agama'),
                'status_nikah' => $this->request->getPost('status_nikah'),
                'pekerjaan' => $this->request->getPost('pekerjaan'),
            ];
            $this->PasienModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Data pasien berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
