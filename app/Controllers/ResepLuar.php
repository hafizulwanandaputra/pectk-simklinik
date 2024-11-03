<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\ObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;

class ResepLuar extends BaseController
{
    protected $ResepModel;
    protected $DetailResepModel;
    public function __construct()
    {
        $this->ResepModel = new ResepModel();
        $this->DetailResepModel = new DetailResepModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'title' => 'Resep Luar - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Resep Luar', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent
            ];
            return view('dashboard/resepluar/index', $data); // Mengembalikan tampilan resep
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function listresep()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $status = $this->request->getGet('status');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $ResepModel = $this->ResepModel;

            // Menerapkan filter status jika disediakan
            if ($status === '1') {
                $ResepModel->select('resep.*')->where('status', 1); // Resep aktif
            } elseif ($status === '0') {
                $ResepModel->select('resep.*')->where('status', 0); // Resep non-aktif
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $ResepModel->groupStart()
                    ->like('nama_pasien', $search)
                    ->orLike('tanggal_resep', $search)
                    ->groupEnd();
            }

            // Menambahkan filter untuk resep di mana nomor_registrasi, no_rm, dan dokter adalah NULL
            $ResepModel->groupStart()
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->groupEnd();

            // Menghitung total hasil pencarian
            $total = $ResepModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $Resep = $ResepModel->orderBy('id_resep', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap resep
            $dataResep = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data;
            }, $Resep, array_keys($Resep));

            // Mengembalikan data resep dalam format JSON
            return $this->response->setJSON([
                'resep' => $dataResep,
                'total' => $total
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function resep($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id); // Mengambil resep 
            return $this->response->setJSON($data); // Mengembalikan data resep dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_pasien' => 'required', // Nama pasien
                'jenis_kelamin' => 'required', // Jenis kelamin
                'tanggal_lahir' => 'required', // Jenis kelamin
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Menyiapkan data untuk disimpan
            $data = [
                'nomor_registrasi' => NULL,
                'no_rm' => NULL,
                'nama_pasien' => $this->request->getPost('nama_pasien'),
                'alamat' => $this->request->getPost('alamat'),
                'telpon' => NULL,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => NULL,
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'dokter' => 'Resep Luar',
                'tanggal_resep' => date('Y-m-d H:i:s'), // Menyimpan tanggal resep saat ini
                'jumlah_resep' => 0,
                'total_biaya' => 0,
                'status' => 0,
            ];

            // Menyimpan data resep ke dalam model
            $this->ResepModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil ditambahkan']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function update()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'nama_pasien' => 'required', // Nama pasien
                'jenis_kelamin' => 'required', // Jenis kelamin
                'tanggal_lahir' => 'required', // Jenis kelamin
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Ambil resep luar
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($this->request->getPost('id_resep'));

            if ($resep['status'] == 0) {
                // Menyiapkan data untuk disimpan
                $data = [
                    'id_resep' => $this->request->getPost('id_resep'),
                    'nomor_registrasi' => NULL,
                    'no_rm' => NULL,
                    'nama_pasien' => $this->request->getPost('nama_pasien'),
                    'alamat' => $this->request->getPost('alamat'),
                    'telpon' => NULL,
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                    'tempat_lahir' => NULL,
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                    'dokter' => 'Resep Luar',
                    'tanggal_resep' => date('Y-m-d H:i:s'), // Menyimpan tanggal resep saat ini
                    'jumlah_resep' => 0,
                    'total_biaya' => 0,
                    'status' => 0,
                ];

                // Menyimpan data resep ke dalam model
                $this->ResepModel->save($data);
                return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil diperbarui']);
            } else {
                // Mengembalikan status 401 jika status adalah sudah ditransaksikan
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Resep ini tidak bisa diedit karena sudah ditransaksikan',
                ]);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect(); // Menghubungkan ke database

            // Mengambil resep
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id);

            if ($resep['status'] == 0) {
                // Mengambil semua id_obat dan jumlah dari detail_resep yang terkait dengan resep yang dihapus
                $detailResep = $db->query("SELECT id_obat, jumlah FROM detail_resep WHERE id_resep = ?", [$id])->getResultArray();

                // Mengurangi jumlah_keluar pada tabel obat
                foreach ($detailResep as $detail) {
                    $id_obat = $detail['id_obat'];
                    $jumlah = $detail['jumlah'];

                    // Mengambil jumlah_keluar dari tabel obat
                    $obat = $db->query("SELECT jumlah_keluar FROM obat WHERE id_obat = ?", [$id_obat])->getRowArray();

                    if ($obat) {
                        // Mengurangi jumlah_keluar
                        $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah;

                        // Memastikan jumlah_keluar tidak negatif
                        if ($new_jumlah_keluar < 0) {
                            $new_jumlah_keluar = 0;
                        }

                        // Memperbarui jumlah_keluar di tabel obat
                        $db->query("UPDATE obat SET jumlah_keluar = ? WHERE id_obat = ?", [$new_jumlah_keluar, $id_obat]);
                    }
                }

                // Melanjutkan penghapusan resep
                $transaksiDetail = $db->query("SELECT id_transaksi FROM detail_transaksi WHERE id_resep = ?", [$id])->getRow();

                // Menghapus resep dan detail terkait
                $this->ResepModel->where('status', 0)->delete($id);
                $db->query('ALTER TABLE `resep` auto_increment = 1'); // Mengatur ulang auto increment pada tabel resep
                $db->query('ALTER TABLE `detail_resep` auto_increment = 1'); // Mengatur ulang auto increment pada tabel detail resep

                // Jika ada transaksi terkait, hitung ulang total_pembayaran
                if ($transaksiDetail) {
                    $id_transaksi = $transaksiDetail->id_transaksi;

                    // Hitung ulang total_pembayaran berdasarkan detail transaksi yang tersisa
                    $result = $db->query("
                SELECT SUM(harga_satuan) as total_pembayaran 
                FROM detail_transaksi 
                WHERE id_transaksi = ?", [$id_transaksi])->getRow();

                    $total_pembayaran = $result->total_pembayaran ?? 0;

                    // Memperbarui tabel transaksi dengan total_pembayaran yang baru
                    $db->query("
                UPDATE transaksi 
                SET total_pembayaran = ? 
                WHERE id_transaksi = ?", [$total_pembayaran, $id_transaksi]);
                }

                return $this->response->setJSON(['message' => 'Resep berhasil dihapus']); // Mengembalikan pesan sukses
            } else {
                return $this->response->setStatusCode(422)->setJSON(['message' => 'Resep ini tidak bisa dihapus karena sudah ditransaksikan']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL RESEP LUAR
    public function detailresep($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Ambil resep luar berdasarkan ID
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id);

            // Memeriksa apakah resep tidak kosong
            if (!empty($resep)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'resep' => $resep,
                    'title' => 'Detail Resep Luar ' . $resep['nama_pasien'] . ' (' . $id . ') - ' . $this->systemName,
                    'headertitle' => 'Detail Resep Luar',
                    'agent' => $this->request->getUserAgent() // Menyimpan informasi tentang user agent
                ];
                // Mengembalikan tampilan detail resep
                return view('dashboard/resepluar/details', $data);
            } else {
                // Menampilkan halaman tidak ditemukan jika resep tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Menampilkan halaman tidak ditemukan jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailreseplist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail resep berdasarkan id_resep yang diberikan
            $data = $this->DetailResepModel
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner') // Bergabung dengan tabel resep
                ->where('detail_resep.id_resep', $id)
                ->where('resep.nomor_registrasi', null)
                ->where('resep.no_rm', null)
                ->where('resep.telpon', null)
                ->where('resep.tempat_lahir', null)
                ->where('resep.dokter', 'Resep Luar')
                ->orderBy('id_detail_resep', 'ASC') // Mengurutkan berdasarkan id_detail_resep
                ->findAll();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON($data);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailresepitem($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail resep berdasarkan id_detail_resep yang diberikan
            $data = $this->DetailResepModel
                ->where('id_detail_resep', $id)
                ->orderBy('id_detail_resep', 'ASC') // Mengurutkan berdasarkan id_detail_resep
                ->find($id); // Mengambil data berdasarkan id

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON($data);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist($id_resep)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $ObatModel = new ObatModel(); // Membuat instance model Obat
            $DetailResepModel = new DetailResepModel(); // Membuat instance model DetailResep

            // Mengambil semua obat dari tabel obat dan mengurutkannya
            $results = $ObatModel->orderBy('nama_obat', 'ASC')->findAll();

            $options = []; // Menyiapkan array untuk opsi obat
            foreach ($results as $row) {
                $ppn = (float) $row['ppn']; // Mengambil nilai PPN
                $mark_up = (float) $row['mark_up']; // Mengambil nilai mark-up
                $harga_obat = (float) $row['harga_obat']; // Mengambil harga obat
                $penyesuaian_harga = (float) $row['penyesuaian_harga']; // Mengambil penyesuaian harga

                // 1. Hitung PPN
                $jumlah_ppn = ($harga_obat * $ppn) / 100;
                $total_harga_ppn = $harga_obat + $jumlah_ppn;

                // 2. Terapkan mark-up
                $jumlah_mark_up = ($total_harga_ppn * $mark_up) / 100;
                $total_harga = $total_harga_ppn + $jumlah_mark_up;

                // 3. Bulatkan harga ke ratusan terdekat ke atas dan tambahkan penyesuaian
                $harga_bulat = ceil($total_harga / 100) * 100 + $penyesuaian_harga;

                // 4. Format harga dengan pemisah ribuan
                $harga_obat_terformat = number_format($harga_bulat, 0, ',', '.');

                // 5. Cek apakah obat sudah digunakan dalam resep yang sama
                $isUsed = $DetailResepModel
                    ->where('id_obat', $row['id_obat'])
                    ->where('id_resep', $id_resep)
                    ->first();

                // 6. Jika stok tersedia dan obat belum digunakan, tambahkan ke options
                $stok_tersisa = $row['jumlah_masuk'] - $row['jumlah_keluar'];
                if ($stok_tersisa > 0 && !$isUsed) {
                    $options[] = [
                        'value' => $row['id_obat'], // Menyimpan id_obat
                        'text' => $row['nama_obat'] .
                            ' (' . $row['kategori_obat'] .
                            ' • ' . $row['bentuk_obat'] .
                            ' • Rp' . $harga_obat_terformat .
                            ' • ' . $stok_tersisa . ')' // Menyimpan informasi obat
                    ];
                }
            }

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahdetailresep($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_obat' => 'required', // id_obat harus diisi
                'signa' => 'required', // signa harus diisi
                'catatan' => 'required', // catatan harus diisi
                'cara_pakai' => 'required', // cara pakai harus diisi
                'jumlah' => 'required|numeric|greater_than[0]', // jumlah harus diisi, numerik, dan lebih besar dari 0
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Memulai transaksi database
            $db = db_connect();
            $db->transBegin();

            // Mengambil data obat berdasarkan id_obat yang diberikan
            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $this->request->getPost('id_obat'))->get()->getRowArray();

            // Mengambil data obat berdasarkan id_obat yang diberikan
            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $this->request->getPost('id_obat'))->get()->getRowArray();

            $ppn = (float) $obat['ppn']; // Mengambil nilai PPN
            $mark_up = (float) $obat['mark_up']; // Mengambil nilai mark-up
            $harga_obat = (float) $obat['harga_obat']; // Mengambil harga obat
            $penyesuaian_harga = (float) $obat['penyesuaian_harga']; // Mengambil penyesuaian harga

            // 1. Hitung PPN
            $jumlah_ppn = ($harga_obat * $ppn) / 100;
            $total_harga_ppn = $harga_obat + $jumlah_ppn;

            // 2. Terapkan mark-up
            $jumlah_mark_up = ($total_harga_ppn * $mark_up) / 100;
            $total_harga = $total_harga_ppn + $jumlah_mark_up;

            // 3. Bulatkan harga ke ratusan terdekat ke atas dan tambahkan penyesuaian
            $harga_bulat = ceil($total_harga / 100) * 100 + $penyesuaian_harga;

            // Simpan data detail resep
            $data = [
                'id_resep' => $id,
                'id_obat' => $this->request->getPost('id_obat'),
                'nama_obat' => $obat['nama_obat'],
                'kategori_obat' => $obat['kategori_obat'],
                'bentuk_obat' => $obat['bentuk_obat'],
                'signa' => $this->request->getPost('signa'),
                'catatan' => $this->request->getPost('catatan'),
                'cara_pakai' => $this->request->getPost('cara_pakai'),
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_satuan' => $harga_bulat,
            ];
            $this->DetailResepModel->save($data);

            // Mengambil data resep
            $resepb = $db->table('resep');
            $resepb
                ->where('id_resep', $id)
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar');
            $resep = $resepb->get()->getRowArray();

            // Jika status resep adalah transaksi sudah diproses, gagalkan operasi
            if ($resep['status'] == 1) {
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan resep ini sudah diproses', 'errors' => NULL]);
            }

            // Mengupdate jumlah keluar obat
            $new_jumlah_keluar = $obat['jumlah_keluar'] + $this->request->getPost('jumlah');
            $builderObat->where('id_obat', $this->request->getPost('id_obat'))->update([
                'jumlah_keluar' => $new_jumlah_keluar,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Memeriksa apakah jumlah keluar melebihi stok
            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
            }

            // Menghitung jumlah resep
            $builder = $db->table('detail_resep');
            $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_resep', $id);
            $result = $builder->get()->getRow();

            $jumlah_resep = $result->jumlah_resep; // Mengambil jumlah resep
            $total_biaya = $result->total_biaya; // Mengambil total biaya

            // Memperbarui tabel resep
            $resepBuilder = $db->table('resep');
            $resepBuilder->where('id_resep', $id);
            $resepBuilder->update([
                'jumlah_resep' => $jumlah_resep,
                'total_biaya' => $total_biaya,
            ]);

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian resep', 'errors' => NULL]);
            } else {
                $db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil ditambahkan']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailresep($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'signa_edit' => 'required', // signa_edit harus diisi
                'catatan_edit' => 'required', // catatan_edit harus diisi
                'cara_pakai_edit' => 'required', // cara_pakai_edit harus diisi
                'jumlah_edit' => 'required|numeric|greater_than[0]', // jumlah_edit harus diisi, numerik, dan lebih besar dari 0
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Memulai transaksi database
            $db = db_connect();
            $db->transBegin();

            // Mengambil detail resep berdasarkan id_detail_resep yang diberikan
            $detail_resep = $this->DetailResepModel->find($this->request->getPost('id_detail_resep'));
            $builderObat = $db->table('obat');
            $obat = $builderObat->where('id_obat', $detail_resep['id_obat'])->get()->getRowArray();

            // Memeriksa apakah id_obat ditemukan
            if (!$obat) {
                $db->transRollback();
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Obat tidak ditemukan atau sudah dihapus', 'errors' => NULL]);
            }

            // Simpan data detail resep yang diperbarui
            $data = [
                'signa' => $this->request->getPost('signa_edit'),
                'catatan' => $this->request->getPost('catatan_edit'),
                'cara_pakai' => $this->request->getPost('cara_pakai_edit'),
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'harga_satuan' => $detail_resep['harga_satuan'],
            ];

            $db->table('detail_resep')
                ->where('id_detail_resep', $this->request->getPost('id_detail_resep'))
                ->where('id_resep', $id)
                ->where('id_obat', $detail_resep['id_obat'])
                ->update($data);

            // Mengambil data resep
            $resepb = $db->table('resep');
            $resepb
                ->where('id_resep', $id)
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar');
            $resep = $resepb->get()->getRowArray();

            // Jika status resep adalah transaksi sudah diproses, gagalkan operasi
            if ($resep['status'] == 1) {
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan resep ini sudah diproses', 'errors' => NULL]);
            }

            // Mengupdate jumlah keluar obat
            $new_jumlah_keluar = $obat['jumlah_keluar'] - $detail_resep['jumlah'] + $this->request->getPost('jumlah_edit');
            $builderObat->where('id_obat', $detail_resep['id_obat'])->update([
                'jumlah_keluar' => $new_jumlah_keluar,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Memeriksa apakah jumlah keluar melebihi stok
            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok', 'errors' => NULL]);
            }

            // Menghitung jumlah resep
            $builder = $db->table('detail_resep');
            $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_resep', $id);
            $result = $builder->get()->getRow();

            $jumlah_resep = $result->jumlah_resep; // Mengambil jumlah resep
            $total_biaya = $result->total_biaya; // Mengambil total biaya

            // Memperbarui tabel resep
            $resepBuilder = $db->table('resep');
            $resepBuilder->where('id_resep', $id);
            $resepBuilder->update([
                'jumlah_resep' => $jumlah_resep,
                'total_biaya' => $total_biaya,
            ]);

            // Memperbarui detail_transaksi dengan harga_transaksi yang baru
            $harga_transaksi = $detail_resep['jumlah'] * $detail_resep['harga_satuan'];

            $detailTransaksiBuilder = $db->table('detail_transaksi');
            $detailTransaksiBuilder->where('id_resep', $id);
            $detailTransaksiBuilder->update([
                'harga_transaksi' => $harga_transaksi
            ]);

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian resep', 'errors' => NULL]);
            } else {
                $db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil diperbarui']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailresep($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menghubungkan ke database
            $db = db_connect();

            // Mengambil detail resep berdasarkan id_detail_resep yang diberikan
            $builderDetail = $db->table('detail_resep');
            $detail = $builderDetail->where('id_detail_resep', $id)->get()->getRowArray();

            if ($detail) {
                $id_resep = $detail['id_resep']; // Mengambil id resep dari detail
                $id_obat = $detail['id_obat']; // Mengambil id obat dari detail
                $jumlah_obat = $detail['jumlah']; // Mengambil jumlah obat dari detail

                // Mengambil data resep
                $resepb = $db->table('resep');
                $resepb
                    ->where('id_resep', $id_resep)
                    ->where('nomor_registrasi', null)
                    ->where('no_rm', null)
                    ->where('telpon', null)
                    ->where('tempat_lahir', null)
                    ->where('dokter', 'Resep Luar');
                $resep = $resepb->get()->getRowArray();

                // Jika status resep adalah transaksi sudah diproses, gagalkan operasi
                if ($resep['status'] == 1) {
                    return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan resep ini sudah diproses', 'errors' => NULL]);
                }

                // Mengambil jumlah_keluar saat ini dari tabel obat
                $builderObat = $db->table('obat');
                $obat = $builderObat->where('id_obat', $id_obat)->get()->getRowArray();

                if ($obat) {
                    // Memperbarui jumlah_keluar di tabel obat (mengurangi stok berdasarkan detail yang dihapus)
                    $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah_obat;
                    if ($new_jumlah_keluar < 0) {
                        $new_jumlah_keluar = 0; // Jika jumlah keluar negatif, set menjadi 0
                    }
                    $builderObat->where('id_obat', $id_obat)->update([
                        'jumlah_keluar' => $new_jumlah_keluar,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Menghapus detail resep
                $builderDetail->where('id_detail_resep', $id)->delete();

                // Mengatur ulang auto_increment (opsional, tidak biasanya direkomendasikan di produksi)
                $db->query('ALTER TABLE `detail_resep` auto_increment = 1');

                // Menghitung jumlah_resep dan total_biaya untuk resep
                $builder = $db->table('detail_resep');
                $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
                $builder->where('id_resep', $id_resep);
                $result = $builder->get()->getRow();

                $jumlah_resep = $result->jumlah_resep ?? 0;  // Menangani null jika tidak ada baris yang tersisa
                $total_biaya = $result->total_biaya ?? 0;

                // Memperbarui tabel resep
                $resepBuilder = $db->table('resep');
                $resepBuilder->where('id_resep', $id_resep);
                $resepBuilder->update([
                    'jumlah_resep' => $jumlah_resep,
                    'total_biaya' => $total_biaya,
                ]);

                // Menghapus catatan detail_transaksi yang terkait
                $builderTransaksiDetail = $db->table('detail_transaksi');
                $builderTransaksiDetail->where('id_resep', $id_resep)->delete();

                return $this->response->setJSON(['message' => 'Item resep berhasil dihapus']);
            }

            return $this->response->setJSON(['message' => 'Detail resep tidak ditemukan'], 404);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function etiketdalam($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data resep berdasarkan id dan status
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id);
            // Mengambil detail resep yang berkaitan dengan bentuk obat Tablet/Kapsul dan Sirup
            $detail_resep = $this->DetailResepModel
                ->where('detail_resep.id_resep', $id)
                ->groupStart()
                ->where('bentuk_obat', 'Tablet/Kapsul')
                ->orWhere('bentuk_obat', 'Sirup')
                ->groupEnd()
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->findAll();

            // Memeriksa apakah detail resep tidak kosong dan status resep sama dengan 0
            if (!empty($detail_resep)) {
                // Menyiapkan data untuk cetakan
                $data = [
                    'resep' => $resep,
                    'detail_resep' => $detail_resep,
                    'title' => 'Etiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/resepluar/etiket', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('resep-obat-dalam-id-' . $resep['nomor_registrasi'] . '-' . urlencode($resep['nama_pasien']) . '-' . urlencode($resep['dokter']) . '-' . $resep['tanggal_resep'] . '.pdf', [
                    'Attachment' => FALSE // Menghasilkan PDF tanpa mengunduh
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound(); // Jika detail resep kosong atau status tidak sesuai
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function etiketluar($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data resep berdasarkan id dan status
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id);
            // Mengambil detail resep yang berkaitan dengan bentuk obat Tetes dan Salep
            $detail_resep = $this->DetailResepModel
                ->where('detail_resep.id_resep', $id)
                ->groupStart()
                ->where('bentuk_obat', 'Tetes')
                ->orWhere('bentuk_obat', 'Salep')
                ->groupEnd()
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner')
                ->orderBy('id_detail_resep', 'ASC')
                ->findAll();

            // Memeriksa apakah detail resep tidak kosong dan status resep sama dengan 0
            if (!empty($detail_resep)) {
                // Menyiapkan data untuk cetakan
                $data = [
                    'resep' => $resep,
                    'detail_resep' => $detail_resep,
                    'title' => 'Etiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // Menghasilkan PDF menggunakan Dompdf
                $dompdf = new Dompdf();
                $html = view('dashboard/resepluar/etiket', $data);
                $dompdf->loadHtml($html);
                $dompdf->render();
                $dompdf->stream('resep-obat-luar-id-' . $resep['nomor_registrasi'] . '-' . urlencode($resep['nama_pasien']) . '-' . urlencode($resep['dokter']) . '-' . $resep['tanggal_resep'] . '.pdf', [
                    'Attachment' => FALSE // Menghasilkan PDF tanpa mengunduh
                ]);
            } else {
                throw PageNotFoundException::forPageNotFound(); // Jika detail resep kosong atau status tidak sesuai
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
