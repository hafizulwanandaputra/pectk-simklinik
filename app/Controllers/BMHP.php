<?php

namespace App\Controllers;

use App\Models\BMHPModel;
use App\Models\DetailBMHPModel;
use App\Models\BatchObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BMHP extends BaseController
{
    protected $BMHPModel;
    protected $DetailBMHPModel;
    public function __construct()
    {
        $this->BMHPModel = new BMHPModel();
        $this->DetailBMHPModel = new DetailBMHPModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'title' => 'Barang Medis Habis Pakai - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Barang Medis Habis Pakai', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent
            ];
            return view('dashboard/bmhp/index', $data); // Mengembalikan tampilan resep
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function listbmhp()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $konfirmasiKasir = $this->request->getGet('konfirmasiKasir');
            $apoteker = $this->request->getGet('apoteker');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $BMHPModel = $this->BMHPModel;

            // Menerapkan filter status jika disediakan
            if ($konfirmasiKasir === '1') {
                $BMHPModel->select('bmhp.*')->where('konfirmasi_kasir', 1); // BMHP yang dikonfirmasi kasir
            } elseif ($konfirmasiKasir === '0') {
                $BMHPModel->select('bmhp.*')->where('konfirmasi_kasir', 0); // BMHP yang tidak dikonfirmasi kasir
            }

            // Mengaplikasikan filter apoteker jika diberikan
            if ($apoteker) {
                $BMHPModel->like('apoteker', $apoteker);
            }

            // Menerapkan filter pencarian berdasarkan nama pasien atau tanggal bmhp
            if ($search) {
                $BMHPModel->groupStart()
                    ->like('tanggal_bmhp', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil pencarian
            $total = $BMHPModel->countAllResults(false);

            // Mendapatkan hasil yang sudah dipaginasi
            $BMHP = $BMHPModel->orderBy('id_bmhp', 'DESC')->findAll($limit, $offset);

            // Menghitung nomor urut untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke setiap resep
            $dataBMHP = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index; // Menetapkan nomor urut
                return $data;
            }, $BMHP, array_keys($BMHP));

            // Mengembalikan data resep dalam format JSON
            return $this->response->setJSON([
                'bmhp' => $dataBMHP,
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
            $resepData = $this->BMHPModel
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

    public function bmhp($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->BMHPModel
                ->find($id); // Mengambil BMHP 
            return $this->response->setJSON($data); // Mengembalikan data BMHP dalam format JSON
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
            // Menyiapkan data untuk disimpan
            $data = [
                'tanggal_bmhp' => date('Y-m-d H:i:s'), // Menyimpan tanggal BMHP saat ini
                'apoteker' => session()->get('fullname')
            ];

            // Menyimpan data BMHP ke dalam model
            $this->BMHPModel->save($data);

            // Dapatkan ID dari data yang baru disimpan
            $newId = $this->BMHPModel->insertID();

            // Panggil WebSocket untuk update client
            $this->notify_clients('update');

            // Redirect ke halaman detail bmhp
            return redirect()->to(base_url('bmhp/detailbmhp/' . $newId));
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function konfirmasi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
            // Mengambil bmhp
            $bmhp = $db->table('bmhp');
            $bmhp->where('id_bmhp', $id);
            $bmhp->where('konfirmasi_kasir', 0);
            $bmhp->update([
                'konfirmasi_kasir' => 1, // Atur sebagai diarsipkan
            ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Barang medis habis pakai ini telah dikonfirmasi']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function batalkonfirmasi($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
            // Mengambil bmhp
            $bmhp = $db->table('bmhp');
            $bmhp->where('id_bmhp', $id);
            $bmhp->where('konfirmasi_kasir', 1);
            $bmhp->update([
                'konfirmasi_kasir' => 0, // Atur sebagai diarsipkan
            ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients('update');
            return $this->response->setJSON(['success' => true, 'message' => 'Konfirmasi barang medis habis pakai ini telah dibatalkan']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
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

            // Mengambil bmhp
            $bmhp = $this->BMHPModel
                ->find($id);

            if ($bmhp['konfirmasi_kasir'] == 0) {
                // Mengambil semua id_obat dan jumlah dari detail_bmhp yang terkait dengan bmhp yang dihapus
                $detailBMHP = $db->query("SELECT id_batch_obat, jumlah FROM detail_bmhp WHERE id_bmhp = ?", [$id])->getResultArray();

                // Mengurangi jumlah_keluar pada tabel obat
                foreach ($detailBMHP as $detail) {
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

                // Melanjutkan penghapusan bmhp
                $transaksiDetail = $db->query("SELECT id_transaksi FROM detail_transaksi WHERE id_bmhp = ?", [$id])->getRow();

                // Menghapus bmhp dan detail terkait
                $this->BMHPModel->where('status', 0)->delete($id);
                $db->query('ALTER TABLE `bmhp` auto_increment = 1'); // Mengatur ulang auto increment pada tabel bmhp
                $db->query('ALTER TABLE `detail_bmhp` auto_increment = 1'); // Mengatur ulang auto increment pada tabel detail bmhp

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
                return $this->response->setJSON(['message' => 'Barang medis habis pakai berhasil dihapus']); // Mengembalikan pesan sukses
            } else {
                return $this->response->setStatusCode(422)->setJSON(['message' => 'Barang medis habis pakai ini tidak bisa dihapus karena sudah dikonfirmasikan ke kasir']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL BARANG MEDIS HABIS PAKAI
    public function detailbmhp($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menghubungkan ke database
            $db = db_connect();

            // Ambil bmhp luar berdasarkan ID
            $bmhp = $this->BMHPModel
                ->find($id);

            // Query untuk item sebelumnya
            $previous = $db->table('bmhp')
                ->where('bmhp.id_bmhp <', $id) // Kondisi untuk id sebelumnya
                ->orderBy('bmhp.id_bmhp', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('bmhp')
                ->where('bmhp.id_bmhp >', $id) // Kondisi untuk id berikutnya
                ->orderBy('bmhp.id_bmhp', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Memeriksa apakah bmhp tidak kosong
            if (!empty($bmhp)) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'bmhp' => $bmhp,
                    'title' => 'Detail BMHP ' . $bmhp['tanggal_bmhp'] . ') - ' . $this->systemName,
                    'headertitle' => 'Detail BMHP',
                    'agent' => $this->request->getUserAgent(), // Menyimpan informasi tentang user agent
                    'previous' => $previous,
                    'next' => $next
                ];
                // Mengembalikan tampilan detail resep
                return view('dashboard/bmhp/details', $data);
            } else {
                // Menampilkan halaman tidak ditemukan jika resep tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Menampilkan halaman tidak ditemukan jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailbmhplist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail bmhp berdasarkan id_bmhp yang diberikan
            $data = $this->DetailBMHPModel
                ->join('bmhp', 'bmhp.id_bmhp = detail_bmhp.id_bmhp', 'inner') // Bergabung dengan tabel bmhp
                ->where('detail_bmhp.id_bmhp', $id)
                ->orderBy('id_detail_bmhp', 'ASC') // Mengurutkan berdasarkan id_detail_bmhp
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

    public function detailbmhpitem($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail bmhp berdasarkan id_detail_bmhp yang diberikan
            $data = $this->DetailBMHPModel
                ->where('id_detail_bmhp', $id)
                ->orderBy('id_detail_bmhp', 'ASC') // Mengurutkan berdasarkan id_detail_bmhp
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

    public function obatlist($id_bmhp)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $BatchObatModel = new BatchObatModel(); // Membuat instance model Obat
            $DetailBMHPModel = new DetailBMHPModel(); // Membuat instance model DetailResep

            // Mengambil semua obat dari tabel obat dan mengurutkannya
            $results = $BatchObatModel
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->groupStart()
                ->where('batch_obat.tgl_kedaluwarsa >', date('Y-m-d'))
                ->groupEnd()
                ->where('obat.bentuk_obat', "Alat Kesehatan")
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
                $isUsed = $DetailBMHPModel
                    ->where('id_batch_obat', $row['id_batch_obat'])
                    ->where('id_bmhp', $id_bmhp)
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
                ->groupStart()
                ->where('batch_obat.tgl_kedaluwarsa >=', $hari_ini)
                ->where('batch_obat.tgl_kedaluwarsa <=', $enam_bulan_ke_depan)
                ->groupEnd()
                ->where('obat.bentuk_obat', "Alat Kesehatan")
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

    public function tambahdetailbmhp($id)
    {
        // Hanya Admin atau Apoteker yang boleh menambah detail bmhp
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

            // Cek input signa & catatan
            $signa = $this->request->getPost('signa');
            if ($signa == '-' || $signa == '0') {
                $signa = NULL;
            }

            $catatan = $this->request->getPost('catatan');
            if ($catatan == '-') {
                $catatan = NULL;
            }

            // Simpan ke detail bmhp
            $data = [
                'id_bmhp' => $id,
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
            $this->DetailBMHPModel->save($data);

            // Periksa status bmhp
            $bmhpb = $db->table('bmhp')
                ->where('id_bmhp', $id);
            $bmhp = $bmhpb->get()->getRowArray();

            if ($bmhp && $bmhp['status'] == 1) {
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

            // Hitung ulang total bmhp
            $builder = $db->table('detail_bmhp');
            $builder->select('SUM(jumlah) as jumlah_bmhp, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_bmhp', $id);
            $result = $builder->get()->getRow();

            $jumlah_bmhp = $result->jumlah_bmhp;
            $total_biaya = $result->total_biaya;

            $db->table('bmhp')
                ->where('id_bmhp', $id)
                ->update([
                    'jumlah_bmhp' => $jumlah_bmhp,
                    'total_biaya' => $total_biaya,
                ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian BMHP', 'errors' => NULL]);
            } else {
                $db->transCommit();
                $this->notify_clients('update_bmhp');
                return $this->response->setJSON(['success' => true, 'message' => 'Item BMHP berhasil ditambahkan']);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailbmhp($id)
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

            // Mengambil detail bmhp berdasarkan id_detail_bmhp yang diberikan
            $detail_bmhp = $this->DetailBMHPModel->find($this->request->getPost('id_detail_bmhp'));
            $builderObat = $db->table('batch_obat');
            $obat = $builderObat
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->where('id_batch_obat', $detail_bmhp['id_batch_obat'])
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

            // Simpan data detail bmhp yang diperbarui
            $data = [
                'signa' => $signa_edit,
                'catatan' => $catatan_edit,
                'cara_pakai' => $this->request->getPost('cara_pakai_edit'),
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'harga_satuan' => $detail_bmhp['harga_satuan'],
            ];

            $db->table('detail_bmhp')
                ->where('id_detail_bmhp', $this->request->getPost('id_detail_bmhp'))
                ->where('id_bmhp', $id)
                ->where('id_obat', $detail_bmhp['id_obat'])
                ->update($data);

            // Mengambil data bmhp
            $bmhpb = $db->table('bmhp');
            $bmhpb
                ->where('id_bmhp', $id);
            $bmhp = $bmhpb->get()->getRowArray();

            // Jika status bmhp adalah transaksi sudah diproses, gagalkan operasi
            if ($bmhp['status'] == 1) {
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan BMHP ini sudah diproses', 'errors' => NULL]);
            }

            // Mengupdate jumlah keluar obat
            $new_jumlah_keluar = $obat['jumlah_keluar'] - $detail_bmhp['jumlah'] + $this->request->getPost('jumlah_edit');
            $builderObat->where('id_batch_obat', $detail_bmhp['id_batch_obat'])->update([
                'jumlah_keluar' => $new_jumlah_keluar,
                'diperbarui' => date('Y-m-d H:i:s')
            ]);

            // Memeriksa apakah jumlah keluar melebihi stok
            if ($new_jumlah_keluar > $obat['jumlah_masuk']) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Jumlah obat melebihi stok<br>Maksimum: ' . ($obat['jumlah_masuk'] - $obat['jumlah_keluar'] + $detail_bmhp['jumlah']), 'errors' => NULL]);
            }

            // Menghitung jumlah bmhp
            $builder = $db->table('detail_bmhp');
            $builder->select('SUM(jumlah) as jumlah_bmhp, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_bmhp', $id);
            $result = $builder->get()->getRow();

            $jumlah_bmhp = $result->jumlah_bmhp; // Mengambil jumlah bmhp
            $total_biaya = $result->total_biaya; // Mengambil total biaya

            // Memperbarui tabel bmhp
            $bmhpBuilder = $db->table('bmhp');
            $bmhpBuilder->where('id_bmhp', $id);
            $bmhpBuilder->update([
                'jumlah_bmhp' => $jumlah_bmhp,
                'total_biaya' => $total_biaya,
            ]);

            // Memperbarui detail_transaksi dengan harga_transaksi yang baru
            $harga_transaksi = $detail_bmhp['jumlah'] * $detail_bmhp['harga_satuan'];

            $detailTransaksiBuilder = $db->table('detail_transaksi');
            $detailTransaksiBuilder->where('id_bmhp', $id);
            $detailTransaksiBuilder->update([
                'harga_transaksi' => $harga_transaksi
            ]);

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pemberian BMHP', 'errors' => NULL]);
            } else {
                $db->transCommit();
                // Panggil WebSocket untuk update client
                $this->notify_clients('update_bmhp');
                return $this->response->setJSON(['success' => true, 'message' => 'Item BMHP berhasil diperbarui']);
            }
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailbmhp($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menghubungkan ke database
            $db = db_connect();

            // Mengambil detail bmhp berdasarkan id_detail_bmhp yang diberikan
            $builderDetail = $db->table('detail_bmhp');
            $detail = $builderDetail->where('id_detail_bmhp', $id)->get()->getRowArray();

            if ($detail) {
                $id_bmhp = $detail['id_bmhp'];
                $jumlah_obat = $detail['jumlah'];
                $id_batch_obat = $detail['id_batch_obat']; // ← ini penting

                // Mengambil data bmhp
                $bmhpb = $db->table('bmhp');
                $bmhpb
                    ->where('id_bmhp', $id_bmhp);
                $bmhp = $bmhpb->get()->getRowArray();

                // Jika status bmhp adalah transaksi sudah diproses, gagalkan operasi
                if ($bmhp['status'] == 1) {
                    return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan karena transaksi yang menggunakan BMHP ini sudah diproses', 'errors' => NULL]);
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

                // Menghapus detail bmhp
                $builderDetail->where('id_detail_bmhp', $id)->delete();

                // Mengatur ulang auto_increment (opsional, tidak biasanya direkomendasikan di produksi)
                $db->query('ALTER TABLE `detail_bmhp` auto_increment = 1');

                // Menghitung jumlah_bmhp dan total_biaya untuk bmhp
                $builder = $db->table('detail_bmhp');
                $builder->select('SUM(jumlah) as jumlah_bmhp, SUM(jumlah * harga_satuan) as total_biaya');
                $builder->where('id_bmhp', $id_bmhp);
                $result = $builder->get()->getRow();

                $jumlah_bmhp = $result->jumlah_bmhp ?? 0;  // Menangani null jika tidak ada baris yang tersisa
                $total_biaya = $result->total_biaya ?? 0;

                // Memperbarui tabel bmhp
                $bmhpBuilder = $db->table('bmhp');
                $bmhpBuilder->where('id_bmhp', $id_bmhp);
                $bmhpBuilder->update([
                    'jumlah_bmhp' => $jumlah_bmhp,
                    'total_biaya' => $total_biaya,
                ]);

                // Menghapus catatan detail_transaksi yang terkait
                $builderTransaksiDetail = $db->table('detail_transaksi');
                $builderTransaksiDetail->where('id_bmhp', $id_bmhp)->delete();
                // Panggil WebSocket untuk update client
                $this->notify_clients('update_bmhp');
                return $this->response->setJSON(['message' => 'Item bmhp berhasil dihapus']);
            }

            return $this->response->setJSON(['message' => 'Detail bmhp tidak ditemukan'], 404);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function notify_clients($action)
    {
        if (!in_array($action, ['update', 'update_bmhp', 'delete'])) {
            return $this->response->setJSON([
                'status' => 'Invalid action',
                'error' => 'Action must be either "update", "update_bmhp", or "delete"'
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
