<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ObatModel;
use App\Models\OpnameObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class OpnameObat extends BaseController
{
    protected $ObatModel;
    protected $OpnameObatModel;
    public function __construct()
    {
        $this->ObatModel = new ObatModel();
        $this->OpnameObatModel = new OpnameObatModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Opname Obat - ' . $this->systemName,
                'headertitle' => 'Opname Obat',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman opnameobat
            return view('dashboard/opnameobat/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function opnameobatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $OpnameObatModel = $this->OpnameObatModel;

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($search) {
                $OpnameObatModel
                    ->like('tanggal', $search)
                    ->orLike('apoteker', $search);
            }

            // Menghitung total hasil
            $total = $OpnameObatModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $OpnameObat = $OpnameObatModel
                ->orderBy('tanggal', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataOpnameObat = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $OpnameObat, array_keys($OpnameObat));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'opname_obat' => $dataOpnameObat,
                'total' => $total
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
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyimpan data pembelian obat
            $data = [
                'tanggal' => date('Y-m-d H:i:s'),
                'apoteker' => session()->get('fullname'),
            ];

            // Simpan data opname obat
            $opnameId = $this->OpnameObatModel->save($data); // Menyimpan data ke database dan mendapatkan ID yang baru ditambahkan

            // Jika penyimpanan opname obat berhasil, lanjutkan untuk menyimpan detail opname
            if ($opnameId) {
                // Ambil seluruh data dari tabel obat
                $obatData = $this->ObatModel->findAll();

                // Siapkan data untuk disimpan ke detail_opname_obat
                $detailData = [];
                foreach ($obatData as $obat) {
                    $sisa_stok = $obat['jumlah_masuk'] - $obat['jumlah_keluar'];
                    $detailData[] = [
                        'id_opname_obat' => $opnameId, // ID opname obat yang baru
                        'nama_obat' => $obat['nama_obat'],
                        'sisa_stok' => $sisa_stok,
                    ];
                }

                // Simpan data detail opname obat ke database
                if (!empty($detailData)) {
                    $db = db_connect(); // Menghubungkan ke database
                    $builder = $db->table('detail_opname_obat'); // Mengakses tabel detail_opname_obat
                    $builder->insertBatch($detailData); // Menggunakan insertBatch untuk menyimpan beberapa data sekaligus
                }

                return $this->response->setJSON(['success' => true, 'message' => 'Opname obat berhasil ditambahkan']);
            } else {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal menambahkan opname obat']);
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Menghapus detail opname obat
            $db->table('detail_opname_obat')->where('id_opname_obat', $id)->delete();
            // Menghapus data opname obat
            $this->OpnameObatModel->delete($id);

            // Reset auto increment untuk tabel opname_obat dan detail_opname_obat
            $db->query('ALTER TABLE `opname_obat` auto_increment = 1');
            $db->query('ALTER TABLE `detail_opname_obat` auto_increment = 1');

            return $this->response->setJSON(['success' => true, 'message' => 'Opname obat berhasil dihapus']);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL PEMBELIAN OBAT
    public function detailopnameobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail pembelian obat dengan bergabung dengan tabel supplier
            $opname_obat = $this->OpnameObatModel->find($id);

            // Menyiapkan data untuk ditampilkan
            $data = [
                'opname_obat' => $opname_obat,
                'title' => 'Detail Opname Obat ' . $opname_obat['tanggal'] . ' - ' . $this->systemName,
                'headertitle' => 'Detail Opname Obat',
                'agent' => $this->request->getUserAgent()
            ];

            // Mengembalikan view dengan data yang telah disiapkan
            return view('dashboard/opnameobat/details', $data);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function obatlist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail opname obat
            $db = db_connect();
            $opname_obat = $db->table('detail_opname_obat')->where('id_opname_obat', $id)->get()->getResultArray();

            // Mengembalikan menjadi JSON
            return $this->response->setJSON($opname_obat);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
