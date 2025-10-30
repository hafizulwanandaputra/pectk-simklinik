<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\BatchObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
            $gender = $this->request->getGet('gender');
            $names = $this->request->getGet('names');
            $apoteker = $this->request->getGet('apoteker');
            $tanggal = $this->request->getGet('tanggal');

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

            // Menerapkan filter gender jika disediakan
            if ($gender === 'L') {
                $ResepModel->where('jenis_kelamin', 'L'); // Mengambil resep dari pasien laki-laki
            } elseif ($gender === 'P') {
                $ResepModel->where('jenis_kelamin', 'P'); // Mengambil resep dari pasien perempuan
            }

            // Menerapkan filter names jika disediakan
            if ($names === '1') {
                $ResepModel->select('resep.*')->where('nama_pasien IS NOT NULL'); // Pasien dengan nama
            } elseif ($names === '0') {
                $ResepModel->select('resep.*')->where('nama_pasien', NULL); // Pasien anonim
            }

            // Mengaplikasikan filter apoteker jika diberikan
            if ($apoteker) {
                $ResepModel->like('apoteker', $apoteker);
            }

            // Mengaplikasikan filter tanggal jika diberikan
            if ($tanggal) {
                $ResepModel->like('tanggal_resep', $tanggal);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal resep
            if ($search) {
                $ResepModel->groupStart()
                    ->like('nama_pasien', $search)
                    ->groupEnd();
            }

            // Menambahkan filter untuk resep di mana nomor_registrasi, no_rm, dan dokter adalah NULL
            $ResepModel->groupStart()
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

    public function apotekerlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil apoteker dari tabel resep luar
            $resepData = $this->ResepModel
                ->where('apoteker IS NOT NULL')
                ->groupBy('apoteker')
                ->orderBy('apoteker', 'ASC')
                ->findAll();

            // Menyiapkan array opsi untuk dikirim dalam respon
            $options = [];
            // Menyusun opsi dari data resep luar yang diterima
            foreach ($resepData as $resep) {
                // Menambahkan opsi ke dalam array
                $options[] = [
                    'value' => $resep['apoteker'], // Nilai untuk opsi
                    'text'  => $resep['apoteker'] // Teks untuk opsi
                ];
            }

            // Mengembalikan data resep luar dalam format JSON
            return $this->response->setJSON([
                'success' => true, // Indikator sukses
                'data'    => $options, // Data opsi
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan jika peran tidak valid
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
                'jenis_kelamin' => 'required', // Jenis kelamin
            ]);

            // Memeriksa validasi
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Menyiapkan data untuk disimpan
            $data = [
                'nomor_registrasi' => NULL,
                'no_rm' => NULL,
                'nama_pasien' => $this->request->getPost('nama_pasien') ?: null,
                'alamat' => $this->request->getPost('alamat') ?: null,
                'telpon' => NULL,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => NULL,
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                'dokter' => 'Resep Luar',
                'apoteker' => session()->get('fullname'),
                'tanggal_resep' => date('Y-m-d H:i:s'), // Menyimpan tanggal resep saat ini
                'jumlah_resep' => 0,
                'total_biaya' => 0,
                'confirmed' => NULL,
                'status' => 0,
            ];

            // Menyimpan data resep ke dalam model
            $this->ResepModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
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
                'jenis_kelamin' => 'required', // Jenis kelamin
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
                    'nama_pasien' => $this->request->getPost('nama_pasien') ?: null,
                    'alamat' => $this->request->getPost('alamat') ?: null,
                    'telpon' => NULL,
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                    'tempat_lahir' => NULL,
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                    'dokter' => 'Resep Luar',
                    'apoteker' => $resep['apoteker'],
                    'tanggal_resep' => $resep['tanggal_resep'],
                    'jumlah_resep' => $resep['jumlah_resep'],
                    'total_biaya' => $resep['total_biaya'],
                    'confirmed' => NULL,
                    'status' => $resep['status'],
                ];

                // Menyimpan data resep ke dalam model
                $this->ResepModel->save($data);
                // Panggil WebSocket untuk update client
                $this->notify_clients('update');
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
                $detailResep = $db->query("SELECT id_batch_obat, jumlah FROM detail_resep WHERE id_resep = ?", [$id])->getResultArray();

                // Mengurangi jumlah_keluar pada tabel obat
                foreach ($detailResep as $detail) {
                    $id_batch_obat = $detail['id_batch_obat'];
                    $jumlah = $detail['jumlah'];

                    // Mengambil jumlah_keluar dari tabel obat
                    $obat = $db->query("SELECT jumlah_keluar FROM batch_obat WHERE id_batch_obat = ?", [$id_batch_obat])->getRowArray();

                    if ($obat) {
                        // Mengurangi jumlah_keluar
                        $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah;

                        // Memastikan jumlah_keluar tidak negatif
                        if ($new_jumlah_keluar < 0) {
                            $new_jumlah_keluar = 0;
                        }

                        // Memperbarui jumlah_keluar di tabel obat
                        $db->query("UPDATE batch_obat SET jumlah_keluar = ? WHERE id_batch_obat = ?", [$new_jumlah_keluar, $id_batch_obat]);
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
                // Panggil WebSocket untuk update client
                $this->notify_clients('delete');
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
            // Menghubungkan ke database
            $db = db_connect();

            // Ambil resep luar berdasarkan ID
            $resep = $this->ResepModel
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('resep')
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->where('resep.id_resep <', $id) // Kondisi untuk id sebelumnya
                ->orderBy('resep.id_resep', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('resep')
                ->where('nomor_registrasi', null)
                ->where('no_rm', null)
                ->where('telpon', null)
                ->where('tempat_lahir', null)
                ->where('dokter', 'Resep Luar')
                ->where('resep.id_resep >', $id) // Kondisi untuk id berikutnya
                ->orderBy('resep.id_resep', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Memeriksa apakah resep tidak kosong
            if (!empty($resep)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'resep' => $resep,
                    'title' => 'Detail Resep Luar ' . $resep['nama_pasien'] . ' (ID ' . $id . ') - ' . $this->systemName,
                    'headertitle' => 'Detail Resep Luar',
                    'agent' => $this->request->getUserAgent(), // Menyimpan informasi tentang user agent
                    'previous' => $previous,
                    'next' => $next
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
            $BatchObatModel = new BatchObatModel(); // Membuat instance model Obat
            $DetailResepModel = new DetailResepModel(); // Membuat instance model DetailResep

            // Mengambil semua obat dari tabel obat dan mengurutkannya
            $results = $BatchObatModel
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->where('batch_obat.tgl_kedaluwarsa >', date('Y-m-d'))
                ->orderBy('tgl_kedaluwarsa', 'ASC')
                ->findAll();

            $options = []; // Menyiapkan array untuk opsi obat
            foreach ($results as $row) {
                $ppn = (float) $row['ppn'];
                $mark_up = (float) $row['mark_up'];
                $diskon = (float) $row['diskon'];
                $harga_obat = (float) $row['harga_obat'];
                $penyesuaian_harga = (float) $row['penyesuaian_harga'];

                // 1. Harga setelah PPN
                $harga_setelah_ppn = $harga_obat + ($harga_obat * $ppn / 100);

                // 2. Terapkan mark-up
                $harga_setelah_markup = $harga_setelah_ppn + ($harga_setelah_ppn * $mark_up / 100);

                // 3. Terapkan diskon SEBELUM pembulatan
                $harga_setelah_diskon = $harga_setelah_markup * (1 - ($diskon / 100));

                // 4. Bulatkan ke ratusan atas
                $harga_bulat = ceil($harga_setelah_diskon / 100) * 100;

                // 5. Tambahkan penyesuaian harga
                $harga_final = $harga_bulat + $penyesuaian_harga;

                // 6. Format harga
                $harga_obat_terformat = number_format($harga_final, 0, ',', '.');

                // 7. Cek apakah sudah digunakan
                $isUsed = $DetailResepModel
                    ->where('id_batch_obat', $row['id_batch_obat'])
                    ->where('id_resep', $id_resep)
                    ->first();

                // 8. Tambahkan hanya jika stok tersedia
                $stok_tersisa = $row['jumlah_masuk'] - $row['jumlah_keluar'];

                if ($stok_tersisa > 0 && !$isUsed) {
                    $options[] = [
                        'value' => $row['id_batch_obat'],
                        'text' => $row['nama_obat'] .
                            ' (' . (!empty($row['isi_obat']) ? $row['isi_obat'] : 'Tanpa isi obat') .
                            ' • ' . $row['kategori_obat'] .
                            ' • ' . $row['bentuk_obat'] .
                            ' • Rp' . $harga_obat_terformat .
                            ' • ' . (!empty($row['nama_batch']) ? $row['nama_batch'] : 'Tanpa nama batch') .
                            ' • EXP ' . $row['tgl_kedaluwarsa'] .
                            ' • ' . $stok_tersisa . ')'
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

    public function obatkedaluwarsa()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $BatchObatModel = new BatchObatModel();

            $hari_ini = date('Y-m-d');
            $enam_bulan_ke_depan = date('Y-m-d', strtotime('+6 months'));

            $results = $BatchObatModel
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->where('batch_obat.tgl_kedaluwarsa >=', $hari_ini)
                ->where('batch_obat.tgl_kedaluwarsa <=', $enam_bulan_ke_depan)
                ->orderBy('tgl_kedaluwarsa', 'ASC')
                ->findAll();

            $options = [];
            foreach ($results as $row) {
                $ppn = (float) $row['ppn'];
                $mark_up = (float) $row['mark_up'];
                $diskon = (float) $row['diskon'];
                $harga_obat = (float) $row['harga_obat'];
                $penyesuaian_harga = (float) $row['penyesuaian_harga'];

                // 1. Harga setelah PPN
                $harga_setelah_ppn = $harga_obat + ($harga_obat * $ppn / 100);

                // 2. Terapkan mark-up
                $harga_setelah_markup = $harga_setelah_ppn + ($harga_setelah_ppn * $mark_up / 100);

                // 3. Terapkan diskon SEBELUM pembulatan
                $harga_setelah_diskon = $harga_setelah_markup * (1 - ($diskon / 100));

                // 4. Bulatkan ke ratusan atas
                $harga_bulat = ceil($harga_setelah_diskon / 100) * 100;

                // 5. Tambahkan penyesuaian harga
                $harga_final = $harga_bulat + $penyesuaian_harga;

                // 6. Format harga
                $harga_obat_terformat = number_format($harga_final, 0, ',', '.');

                // 7. Tambahkan hanya jika stok tersedia
                $stok_tersisa = $row['jumlah_masuk'] - $row['jumlah_keluar'];

                $options[] = [
                    'nama_obat' => $row['nama_obat'],
                    'isi_obat' => $row['isi_obat'],
                    'kategori_obat' => $row['kategori_obat'],
                    'bentuk_obat' => $row['bentuk_obat'],
                    'nama_batch' => $row['nama_batch'],
                    'stok_tersisa' => $stok_tersisa,
                    'harga' => $harga_obat_terformat,
                    'tgl_kedaluwarsa' => $row['tgl_kedaluwarsa']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'jumlah' => intval(count($options)), // ← ini menambahkan panjang data sebagai int
                'data' => $options,
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahdetailresep($id)
    {
        // Hanya Admin atau Apoteker yang boleh menambah detail resep
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'id_batch_obat' => 'required',
                'signa' => 'required',
                'catatan' => 'required',
                'cara_pakai' => 'required',
                'jumlah' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();
            $db->transBegin();

            $builderObat = $db->table('batch_obat');
            $obat = $builderObat
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->where('batch_obat.id_batch_obat', $this->request->getPost('id_batch_obat'))
                ->where('batch_obat.tgl_kedaluwarsa >', date('Y-m-d'))
                ->get()->getRowArray();

            if (!$obat) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data obat tidak ditemukan']);
            }

            $ppn = (float) $obat['ppn'];
            $mark_up = (float) $obat['mark_up'];
            $diskon = (float) $obat['diskon'];
            $harga_obat = (float) $obat['harga_obat'];
            $penyesuaian_harga = (float) $obat['penyesuaian_harga'];

            // 1. Harga setelah PPN
            $harga_setelah_ppn = $harga_obat + ($harga_obat * $ppn / 100);

            // 2. Terapkan mark-up
            $harga_setelah_markup = $harga_setelah_ppn + ($harga_setelah_ppn * $mark_up / 100);

            // 3. Terapkan diskon SEBELUM pembulatan
            $harga_setelah_diskon = $harga_setelah_markup * (1 - ($diskon / 100));

            // 4. Bulatkan ke ratusan atas
            $harga_bulat = ceil($harga_setelah_diskon / 100) * 100;

            // 5. Tambahkan penyesuaian harga
            $harga_final = $harga_bulat + $penyesuaian_harga;

            // 6. Format harga
            $harga_obat_terformat = number_format($harga_final, 0, ',', '.');

            // Cek input signa & catatan
            $signa = $this->request->getPost('signa');
            if ($signa == '-' || $signa == '0') {
                $signa = NULL;
            }

            $catatan = $this->request->getPost('catatan');
            if ($catatan == '-') {
                $catatan = NULL;
            }

            // Simpan ke detail resep
            $data = [
                'id_resep' => $id,
                'id_obat' => $obat['id_obat'],
                'id_batch_obat' => $this->request->getPost('id_batch_obat'),
                'nama_obat' => $obat['nama_obat'],
                'kategori_obat' => $obat['kategori_obat'],
                'bentuk_obat' => $obat['bentuk_obat'],
                'nama_batch' => $obat['nama_batch'],
                'signa' => $signa,
                'catatan' => $catatan,
                'cara_pakai' => $this->request->getPost('cara_pakai'),
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_satuan' => $harga_final,
            ];
            $this->DetailResepModel->save($data);

            // Periksa status resep
            $resepb = $db->table('resep')
                ->where('id_resep', $id)
                ->where('dokter', 'Resep Luar');
            $resep = $resepb->get()->getRowArray();

            if ($resep && $resep['status'] == 1) {
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi sudah diproses', 'errors' => NULL]);
            }

            // Update stok obat
            $new_jumlah_keluar = $obat['jumlah_keluar'] + $this->request->getPost('jumlah');
            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Jumlah obat melebihi stok<br>Maksimum: ' . ($obat['jumlah_masuk'] - $obat['jumlah_keluar']),
                    'errors' => NULL
                ]);
            }

            $builderObat->where('id_batch_obat', $this->request->getPost('id_batch_obat'))->update([
                'jumlah_keluar' => $new_jumlah_keluar,
                'diperbarui' => date('Y-m-d H:i:s')
            ]);

            // Hitung ulang total resep
            $builder = $db->table('detail_resep');
            $builder->select('SUM(jumlah) as jumlah_resep, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_resep', $id);
            $result = $builder->get()->getRow();

            $jumlah_resep = $result->jumlah_resep;
            $total_biaya = $result->total_biaya;

            $db->table('resep')
                ->where('id_resep', $id)
                ->update([
                    'jumlah_resep' => $jumlah_resep,
                    'total_biaya' => $total_biaya,
                ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian resep', 'errors' => NULL]);
            } else {
                $db->transCommit();
                $this->notify_clients('update_resep');
                return $this->response->setJSON(['success' => true, 'message' => 'Item resep berhasil ditambahkan']);
            }
        } else {
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
            $builderObat = $db->table('batch_obat');
            $obat = $builderObat
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->where('id_batch_obat', $detail_resep['id_batch_obat'])
                ->get()->getRowArray();

            // Memeriksa apakah id_obat ditemukan
            if (!$obat) {
                $db->transRollback();
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Obat tidak ditemukan atau sudah dihapus', 'errors' => NULL]);
            }

            if ($this->request->getPost('signa_edit') == '-' || $this->request->getPost('signa_edit') == '0') {
                $signa_edit = NULL;
            } else {
                $signa_edit = $this->request->getPost('signa_edit');
            }

            if ($this->request->getPost('catatan_edit') == '-') {
                $catatan_edit = NULL;
            } else {
                $catatan_edit = $this->request->getPost('catatan_edit');
            }

            // Simpan data detail resep yang diperbarui
            $data = [
                'signa' => $signa_edit,
                'catatan' => $catatan_edit,
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
                ->where('dokter', 'Resep Luar');
            $resep = $resepb->get()->getRowArray();

            // Jika status resep adalah transaksi sudah diproses, gagalkan operasi
            if ($resep['status'] == 1) {
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan resep ini sudah diproses', 'errors' => NULL]);
            }

            // Mengupdate jumlah keluar obat
            $new_jumlah_keluar = $obat['jumlah_keluar'] - $detail_resep['jumlah'] + $this->request->getPost('jumlah_edit');
            $builderObat->where('id_batch_obat', $detail_resep['id_batch_obat'])->update([
                'jumlah_keluar' => $new_jumlah_keluar,
                'diperbarui' => date('Y-m-d H:i:s')
            ]);

            // Memeriksa apakah jumlah keluar melebihi stok
            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok<br>Maksimum: ' . ($obat['jumlah_masuk'] - $obat['jumlah_keluar'] + $detail_resep['jumlah']), 'errors' => NULL]);
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
                // Panggil WebSocket untuk update client
                $this->notify_clients('update_resep');
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
                $id_resep = $detail['id_resep'];
                $jumlah_obat = $detail['jumlah'];
                $id_batch_obat = $detail['id_batch_obat']; // ← ini penting

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

                // Ambil batch yang sesuai
                $builderObat = $db->table('batch_obat');
                $obat = $builderObat->where('id_batch_obat', $id_batch_obat)->get()->getRowArray();

                if ($obat) {
                    $new_jumlah_keluar = $obat['jumlah_keluar'] - $jumlah_obat;
                    if ($new_jumlah_keluar < 0) {
                        $new_jumlah_keluar = 0;
                    }

                    $builderObat->where('id_batch_obat', $id_batch_obat)->update([
                        'jumlah_keluar' => $new_jumlah_keluar,
                        'diperbarui' => date('Y-m-d H:i:s')
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
                // Panggil WebSocket untuk update client
                $this->notify_clients('update_resep');
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
                ->where('dokter', 'Resep Luar')
                ->find($id);

            if (empty($resep)) {
                throw PageNotFoundException::forPageNotFound();
            }

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
                    'title' => 'E-Tiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/resepluar/etiket', $data);
                // die;
                $client = new Client();
                $html = view('dashboard/resepluar/etiket', $data);
                $filename = 'output-resepluar-dalam.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'width' => '5.5cm',
                                'height' => '3.75cm',
                                'margin' => [
                                    'top' => '0.15cm',
                                    'right' => '0.65cm',
                                    'bottom' => '0.5cm',
                                    'left' => '0.65cm'
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

            if (empty($resep)) {
                throw PageNotFoundException::forPageNotFound();
            }

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
                    'title' => 'E-Tiket Resep ' . $id . ' - ' . $this->systemName
                ];
                // return view('dashboard/resepluar/etiket', $data);
                // die;
                $client = new Client();
                $html = view('dashboard/resepluar/etiket', $data);
                $filename = 'output-resepluar-luar.pdf';

                try {
                    $response = $client->post(env('PDF-URL'), [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'html' => $html,
                            'filename' => $filename,
                            'paper' => [
                                'width' => '5.5cm',
                                'height' => '3.75cm',
                                'margin' => [
                                    'top' => '0.15cm',
                                    'right' => '0.65cm',
                                    'bottom' => '0.5cm',
                                    'left' => '0.65cm'
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
                throw PageNotFoundException::forPageNotFound(); // Jika detail resep kosong atau status tidak sesuai
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function notify_clients($action)
    {
        if (!in_array($action, ['update', 'update_resep', 'delete'])) {
            return $this->response->setJSON([
                'status' => 'Invalid action',
                'error' => 'Action must be either "update", "update_resep", or "delete"'
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
