<?php

namespace App\Controllers;

use App\Models\PembelianObatModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianObatModel;
use App\Models\ItemObatModel;
use App\Models\ObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DateTime;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PembelianObat extends BaseController
{
    protected $PembelianObatModel;
    protected $DetailPembelianObatModel;
    protected $ItemObatModel;
    public function __construct()
    {
        $this->PembelianObatModel = new PembelianObatModel();
        $this->DetailPembelianObatModel = new DetailPembelianObatModel();
        $this->ItemObatModel = new ItemObatModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Obat Masuk - ' . $this->systemName,
                'headertitle' => 'Obat Masuk',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pembelian obat
            return view('dashboard/pembelian_obat/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pembelianobatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $status = $this->request->getGet('status');
            $tanggal = $this->request->getGet('tanggal');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $PembelianObatModel = $this->PembelianObatModel;

            // Mengambil data dari tabel dengan join ke tabel supplier
            $PembelianObatModel
                ->select('pembelian_obat.*, supplier.merek as supplier_merek, supplier.nama_supplier as supplier_nama_supplier')
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner');

            // Mengaplikasikan filter status jika diberikan
            if ($status === '1') {
                $PembelianObatModel->where('diterima', 1);
            } elseif ($status === '0') {
                $PembelianObatModel->where('diterima', 0);
            }

            // Mengaplikasikan filter tanggal jika diberikan
            if ($tanggal) {
                $PembelianObatModel->like('tgl_pembelian', $tanggal);
            }

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($search) {
                $PembelianObatModel
                    ->groupStart()
                    ->like('supplier.merek', $search)
                    ->orLike('supplier.nama_supplier', $search)
                    ->orLike('apoteker', $search)
                    ->groupEnd();
            }

            // Menghitung total hasil
            $total = $PembelianObatModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $PembelianObat = $PembelianObatModel
                ->orderBy('tgl_pembelian', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataPembelianObat = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $PembelianObat, array_keys($PembelianObat));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'pembelian_obat' => $dataPembelianObat,
                'total' => $total
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function supplierlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Memuat model Supplier
            $SupplierModel = new SupplierModel();

            // Mengambil semua supplier yang diurutkan berdasarkan nama
            $results = $SupplierModel->orderBy('merek', 'DESC')->findAll();

            // Menyiapkan opsi untuk ditampilkan di dropdown
            $options = [];
            foreach ($results as $row) {
                $options[] = [
                    'value' => $row['id_supplier'],
                    'text' => $row['merek'] . ' • ' . $row['nama_supplier']
                ];
            }

            // Mengembalikan respons JSON dengan data supplier
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function pembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data pembelian obat berdasarkan ID
            $data = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);
            // Mengembalikan respons JSON dengan data pembelian obat
            return $this->response->setJSON($data);
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
            // Validasi input
            $validation = \Config\Services::validation();
            // Mengatur aturan validasi dasar
            $validation->setRules([
                'id_supplier' => 'required', // Memastikan id_supplier harus diisi
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pembelian obat
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'apoteker' => session()->get('fullname'),
                'tgl_pembelian' => date('Y-m-d H:i:s'), // Menyimpan tanggal pembelian
                'total_qty' => 0,
                'total_biaya' => 0,
                'diterima' => 0, // Menandai bahwa obat belum diterima
            ];
            $this->PembelianObatModel->save($data); // Menyimpan data ke database
            return $this->response->setJSON(['success' => true, 'message' => 'Pembelian berhasil ditambahkan']);
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
            // $db->transBegin(); // Memulai transaksi

            // // Mengambil semua detail pembelian obat sebelum penghapusan
            // $details = $db->table('detail_pembelian_obat')
            //     ->where('id_pembelian_obat', $id)
            //     ->get()
            //     ->getResultArray();

            // // Memeriksa dan mengurangi jumlah_masuk di tabel obat untuk setiap detail
            // foreach ($details as $detail) {
            //     $id_obat = $detail['id_obat'];
            //     $obat_masuk = $detail['obat_masuk'];

            //     // Mengambil jumlah_masuk dan jumlah_keluar saat ini dari tabel obat
            //     $obat = $db->table('obat')
            //         ->select('jumlah_masuk, jumlah_keluar')
            //         ->where('id_obat', $id_obat)
            //         ->get()
            //         ->getRow();

            //     // Memeriksa apakah jumlah_masuk setelah penghapusan akan kurang dari jumlah_keluar
            //     if (($obat->jumlah_masuk - $obat_masuk) < $obat->jumlah_keluar) {
            //         $db->transRollback(); // Rollback transaksi
            //         return $this->response->setStatusCode(422)->setJSON([
            //             'success' => false,
            //             'message' => 'Gagal menghapus pembelian obat: stok masuk kurang dari jumlah keluar'
            //         ]);
            //     }

            //     // Mengurangi jumlah_masuk jika kondisi terpenuhi
            //     $db->table('obat')
            //         ->set('jumlah_masuk', "jumlah_masuk - $obat_masuk", false)
            //         ->where('id_obat', $id_obat)
            //         ->update();
            // }

            // Menghapus detail pembelian obat
            $db->table('detail_pembelian_obat')->where('id_pembelian_obat', $id)->delete();
            // Menghapus data pembelian obat
            $this->PembelianObatModel->delete($id);

            // Reset auto increment untuk tabel pembelian_obat dan detail_pembelian_obat
            $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
            $db->query('ALTER TABLE `item_obat` auto_increment = 1');

            // // Menyelesaikan transaksi
            // if ($db->transCommit()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Pembelian obat berhasil dihapus']);
            // } else {
            //     return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus pembelian obat']);
            // }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function complete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect(); // Menghubungkan ke database
            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Pembelian obat sudah diterima sebelumnya']);
            }
            $db->transStart(); // Memulai transaksi

            // Menghitung total jumlah dan total obat_masuk_baru dari detail pembelian obat
            $totals = $db->table('detail_pembelian_obat')
                ->select('SUM(jumlah) as total_jumlah, SUM(obat_masuk_baru) as total_obat_masuk_baru')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getRow();

            // Mendapatkan nilai totalJumlah dan totalObatMasuk
            $totalJumlah = $totals->total_jumlah;
            $totalObatMasuk = $totals->total_obat_masuk_baru;

            // Jika total jumlah sama dengan total obat masuk baru, tandai obat sebagai diterima
            if ($totalJumlah == $totalObatMasuk) {
                $db->table('pembelian_obat')
                    ->set('diterima', 1)
                    ->where('id_pembelian_obat', $id)
                    ->update();
            }

            // Mengambil detail pembelian obat untuk pemrosesan lebih lanjut
            $details = $db->table('detail_pembelian_obat')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getResultArray();

            // Mengelompokkan detail berdasarkan id_obat dan menjumlahkan obat_masuk_baru
            $groupedDetails = [];
            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $new_jumlah_masuk = $detail['obat_masuk_baru'];

                // Menjumlahkan obat_masuk_baru untuk id_obat yang sama
                if (isset($groupedDetails[$id_obat])) {
                    $groupedDetails[$id_obat] += $new_jumlah_masuk;
                } else {
                    $groupedDetails[$id_obat] = $new_jumlah_masuk;
                }
            }

            // Memperbarui jumlah_masuk dalam tabel obat untuk setiap id_obat
            foreach ($groupedDetails as $id_obat => $total_jumlah_masuk_baru) {
                // Mengambil nilai jumlah_masuk dan jumlah_keluar saat ini untuk obat ini
                $obat = $db->table('obat')
                    ->select('jumlah_masuk, jumlah_keluar')
                    ->where('id_obat', $id_obat)
                    ->get()
                    ->getRow();

                if ($obat) {
                    // Mengambil total obat_masuk dari detail_pembelian_obat untuk id_obat ini
                    $total_obat_masuk = $db->table('detail_pembelian_obat')
                        ->where('id_obat', $id_obat)
                        ->where('id_pembelian_obat', $id)
                        ->selectSum('obat_masuk')
                        ->get()
                        ->getRow()
                        ->obat_masuk;

                    // Mendapatkan jumlah saat ini
                    $current_jumlah_masuk = $obat->jumlah_masuk;
                    $jumlah_keluar = $obat->jumlah_keluar;

                    // Menghitung jumlah_masuk baru dengan mempertimbangkan obat_masuk
                    $new_jumlah_masuk = ($current_jumlah_masuk - $total_obat_masuk + $total_jumlah_masuk_baru);

                    // Pastikan jumlah masuk baru tidak kurang dari jumlah keluar
                    if ($new_jumlah_masuk < $jumlah_keluar) {
                        // Rollback transaksi jika jumlah masuk baru tidak valid
                        $db->transRollback();
                        return $this->response->setStatusCode(422)->setJSON([
                            'success' => false,
                            'message' => "Gagal memproses pembelian obat: stok masuk kurang dari jumlah keluar."
                        ]);
                    }

                    // Memperbarui jumlah_masuk untuk obat
                    $db->table('obat')
                        ->set('jumlah_masuk', $new_jumlah_masuk, false)
                        ->set('updated_at', date('Y-m-d H:i:s')) // Memperbarui timestamp
                        ->where('id_obat', $id_obat)
                        ->update();

                    // Memperbarui detail_pembelian_obat dengan nilai obat_masuk baru
                    $db->table('detail_pembelian_obat')
                        ->set('obat_masuk', $total_jumlah_masuk_baru)
                        ->where('id_pembelian_obat', $id)
                        ->where('id_obat', $id_obat)
                        ->update();
                }
            }

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();  // Rollback jika ada masalah
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pembelian', 'errors' => NULL]);
            } else {
                $db->transCommit(); // Menyelesaikan transaksi
                if ($totalJumlah == $totalObatMasuk) {
                    // Semua obat diterima
                    return $this->response->setJSON(['success' => true, 'message' => 'Obat sudah diterima. Periksa jumlah masuk di menu obat.']);
                } else {
                    // Sebagian obat sudah diterima
                    return $this->response->setJSON(['success' => true, 'message' => 'Sebagian obat sudah diterima. Jika ada obat yang baru saja diterima, silakan perbarui item masing-masing dan klik "Terima Obat" lagi.']);
                }
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail pembelian obat dengan bergabung dengan tabel supplier
            $pembelianobat = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);

            // Menyiapkan data untuk ditampilkan
            $data = [
                'pembelianobat' => $pembelianobat,
                'title' => 'Detail Obat Masuk dengan ID ' . $id . ' - ' . $this->systemName,
                'headertitle' => 'Detail Obat Masuk',
                'agent' => $this->request->getUserAgent()
            ];

            // Mengembalikan view dengan data yang telah disiapkan
            return view('dashboard/pembelian_obat/details', $data);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpembelianobatlist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil semua detail pembelian obat berdasarkan id_pembelian_obat
            $detail_pembelian_obat = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('detail_pembelian_obat.id_detail_pembelian_obat', 'ASC')
                ->findAll();

            $result = [];

            // Melakukan loop pada setiap baris detail_pembelian_obat
            foreach ($detail_pembelian_obat as $row) {
                // Mengambil item obat terkait dengan id_detail_pembelian_obat saat ini
                $item_obat = $this->ItemObatModel
                    ->where('id_detail_pembelian_obat', $row['id_detail_pembelian_obat'])
                    ->orderBy('id_detail_pembelian_obat', 'ASC')
                    ->findAll();

                // Menambahkan 'item' ke baris saat ini
                $row['item'] = $item_obat;

                // Menambahkan baris yang telah dimodifikasi ke dalam hasil
                $result[] = $row;
            }

            // Mengembalikan hasil sebagai JSON
            return $this->response->setJSON(array_values($result));
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailpembelianobatitem($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail pembelian obat berdasarkan id_detail_pembelian_obat
            $data = $this->DetailPembelianObatModel
                ->where('id_detail_pembelian_obat', $id)
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->find($id);

            // Mengembalikan data sebagai JSON
            return $this->response->setJSON($data);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist($id_supplier, $id_pembelian_obat)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $ObatModel = new ObatModel();
            $DetailPembelianObatModel = new DetailPembelianObatModel(); // Model untuk tabel detail_pembelian_obat

            // Mengambil semua obat berdasarkan id_supplier
            $results = $ObatModel->where('id_supplier', $id_supplier)->orderBy('nama_obat', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $harga_obat = (int) $row['harga_obat'];
                $harga_obat_terformat = number_format($harga_obat, 0, ',', '.');

                // Memeriksa apakah id_obat sudah ada di tabel detail_pembelian_obat dengan id_pembelian_obat yang sama
                $isUsed = $DetailPembelianObatModel->where('id_obat', $row['id_obat'])
                    ->where('id_pembelian_obat', $id_pembelian_obat) // Pastikan sesuai dengan id_pembelian_obat yang sedang digunakan
                    ->first();

                // Jika belum ada pada pembelian yang sama, tambahkan ke options
                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_obat'],
                        'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ')'
                    ];
                }
            }

            // Mengembalikan data sebagai JSON
            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahdetailpembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_obat' => 'required', // id_obat wajib diisi
                'jumlah' => 'required|numeric|greater_than[0]', // jumlah harus angka dan lebih dari 0
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menghubungkan ke database
            $db = db_connect();

            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Mengambil model obat dan informasi obat yang dipilih
            $ObatModel = new ObatModel();
            $obat = $ObatModel->find($this->request->getPost('id_obat'));

            // Menyimpan data detail pembelian obat
            $data = [
                'id_pembelian_obat' => $id,
                'id_obat' => $this->request->getPost('id_obat'),
                'jumlah' => $this->request->getPost('jumlah'),
                'obat_masuk' => 0,
                'obat_masuk_baru' => 0,
                'harga_satuan' => $obat['harga_obat'],
            ];
            $this->DetailPembelianObatModel->save($data);

            // Menghitung total_qty dan total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk_baru) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_masuk = $result->total_masuk;
            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Memperbarui tabel pembelian_obat dengan total yang baru
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil ditambahkan']);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailpembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi untuk jumlah_edit
            $validation->setRules([
                'jumlah_edit' => 'required|numeric|greater_than[0]', // jumlah_edit harus angka dan lebih dari 0
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            // Menghubungkan ke database
            $db = db_connect();

            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Mengambil detail pembelian obat yang ingin diperbarui
            $detail_pembelian_obat = $this->DetailPembelianObatModel->find($this->request->getPost('id_detail_pembelian_obat'));

            // Memeriksa apakah jumlah_edit lebih kecil dari obat yang masuk
            if ($this->request->getPost('jumlah_edit') < $detail_pembelian_obat['obat_masuk_baru']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah obat yang diminta kurang dari jumlah obat yang masuk', 'errors' => NULL]);
            }

            // Menyimpan data yang telah diperbarui
            $data = [
                'id_detail_pembelian_obat' => $this->request->getPost('id_detail_pembelian_obat'),
                'id_pembelian_obat' => $id,
                'id_obat' => $detail_pembelian_obat['id_obat'],
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'obat_masuk' => $detail_pembelian_obat['obat_masuk'],
                'obat_masuk_baru' => $detail_pembelian_obat['obat_masuk_baru'],
                'harga_satuan' => $detail_pembelian_obat['harga_satuan'],
            ];
            $this->DetailPembelianObatModel->save($data);


            // Menghitung total_qty dan total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_masuk = $result->total_masuk;
            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Memperbarui tabel pembelian_obat dengan total yang baru
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil diperbarui']);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function itemobat($id)
    {
        // Memeriksa apakah pengguna adalah Admin atau Apoteker
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data item obat berdasarkan ID
            $data = $this->ItemObatModel
                ->where('id_item_obat', $id)
                ->find($id);

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON($data);
        } else {
            // Mengembalikan status 404 jika halaman tidak ditemukan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahitemobat($id, $id_pembelian_obat)
    {
        // Memeriksa apakah pengguna adalah Admin atau Apoteker
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi
            $validation->setRules([
                'no_batch' => 'required',
                'expired' => 'required',
                'jumlah_item' => 'required|numeric|greater_than[0]',
            ]);

            // Jika validasi gagal, mengembalikan pesan error
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect(); // Koneksi ke database
            $db->transBegin(); // Memulai transaksi

            // Menyimpan item obat baru
            $itemBuilder1 = $db->table('item_obat');
            $itemBuilder1->insert([
                'id_detail_pembelian_obat' => $id,
                'no_batch' => $this->request->getPost('no_batch'),
                'expired' => $this->request->getPost('expired'),
                'jumlah_item' => $this->request->getPost('jumlah_item'),
            ]);

            // Mengambil jumlah total 'jumlah_item' dari 'item_obat'
            $itemBuilder2 = $db->table('item_obat');
            $itemBuilder2->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder2->where('id_detail_pembelian_obat', $id);
            $itemSum = $itemBuilder2->get()->getRowArray();

            // Mengambil 'jumlah_pembelian' saat ini dari 'detail_pembelian_obat'
            $detailBuilder1 = $db->table('detail_pembelian_obat');
            $detailBuilder1->select('jumlah');
            $detailBuilder1->where('id_detail_pembelian_obat', $id);
            $detail = $detailBuilder1->get()->getRowArray();

            // Mengambil 'pembelian_obat'
            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Memeriksa apakah 'total_jumlah_item' melebihi 'jumlah_pembelian'
            if ($itemSum['total_jumlah_item'] > $detail['jumlah']) {
                $db->transRollback();  // Mengembalikan transaksi jika jumlah melebihi
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Jumlah obat yang diterima sementara melebihi jumlah yang diminta',
                    'errors' => NULL
                ]);
            }

            // Memperbarui 'obat_masuk_baru' di 'detail_pembelian_obat' dengan jumlah total
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $id);
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();  // Mengembalikan jika ada masalah
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pembelian', 'errors' => NULL]);
            } else {
                $db->transCommit();  // Mengonfirmasi transaksi jika semua baik-baik saja
                return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil ditambahkan']);
            }
        } else {
            // Mengembalikan status 404 jika halaman tidak ditemukan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruiitemobat($id, $id_pembelian_obat)
    {
        // Memeriksa apakah pengguna adalah Admin atau Apoteker
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validasi input
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi
            $validation->setRules([
                'no_batch_edit' => 'required',
                'expired_edit' => 'required',
                'jumlah_item_edit' => 'required|numeric|greater_than[0]',
            ]);

            // Jika validasi gagal, mengembalikan pesan error
            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'message' => NULL, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect(); // Koneksi ke database
            $db->transBegin(); // Memulai transaksi

            // Memperbarui data item obat berdasarkan ID
            $itemBuilder1 = $db->table('item_obat');
            $itemBuilder1->where('id_item_obat', $id);
            $itemBuilder1->update([
                'no_batch' => $this->request->getPost('no_batch_edit'),
                'expired' => $this->request->getPost('expired_edit'),
                'jumlah_item' => $this->request->getPost('jumlah_item_edit'),
            ]);

            // Mengambil jumlah total 'jumlah_item' dari 'item_obat'
            $itemBuilder2 = $db->table('item_obat');
            $itemBuilder2->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder2->where('id_detail_pembelian_obat', $this->request->getPost('id_detail_pembelian_obat'));
            $itemSum = $itemBuilder2->get()->getRowArray();

            // Mengambil 'jumlah_pembelian' saat ini dari 'detail_pembelian_obat'
            $detailBuilder1 = $db->table('detail_pembelian_obat');
            $detailBuilder1->select('jumlah');
            $detailBuilder1->where('id_detail_pembelian_obat', $this->request->getPost('id_detail_pembelian_obat'));
            $detail = $detailBuilder1->get()->getRowArray();

            // Mengambil 'pembelian_obat'
            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Memeriksa apakah 'total_jumlah_item' melebihi 'jumlah_pembelian'
            if ($itemSum['total_jumlah_item'] > $detail['jumlah']) {
                $db->transRollback();  // Mengembalikan transaksi jika jumlah melebihi
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Jumlah obat yang diterima sementara melebihi jumlah yang diminta',
                    'errors' => NULL
                ]);
            }

            // Memperbarui 'obat_masuk_baru' di 'detail_pembelian_obat' dengan jumlah total
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $this->request->getPost('id_detail_pembelian_obat'));
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            // Memeriksa status transaksi
            if ($db->transStatus() === false) {
                $db->transRollback();  // Mengembalikan jika ada masalah
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses pembelian', 'errors' => NULL]);
            } else {
                $db->transCommit();  // Mengonfirmasi transaksi jika semua baik-baik saja
                return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil diedit']);
            }
        } else {
            // Mengembalikan status 404 jika halaman tidak ditemukan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusitemobat($id, $id_pembelian_obat)
    {
        // Memeriksa apakah pengguna adalah Admin atau Apoteker
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect(); // Koneksi ke database

            // Mengambil ID detail pembelian obat
            $itemBuilder = $db->table('item_obat');
            $itemBuilder->select('id_detail_pembelian_obat');
            $itemBuilder->where('id_item_obat', $id); // Menggunakan 'id_item_obat' sebagai nama kolom
            $itemObat = $itemBuilder->get()->getRowArray();

            $id_detail_pembelian_obat = $itemObat['id_detail_pembelian_obat'];

            // Mengambil 'pembelian_obat'
            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                $db->transRollback();
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Menghapus item dari 'item_obat'
            $this->ItemObatModel->delete($id);

            // Mengatur auto increment pada tabel 'item_obat'
            $db->query('ALTER TABLE `item_obat` auto_increment = 1');

            // Mengambil jumlah total 'jumlah_item' dari 'item_obat' untuk 'id_detail_pembelian_obat' yang sama
            $itemBuilder->selectSum('jumlah_item', 'total_jumlah_item');
            $itemBuilder->where('id_detail_pembelian_obat', $id_detail_pembelian_obat);
            $itemSum = $itemBuilder->get()->getRowArray();

            // Memperbarui 'obat_masuk_baru' di 'detail_pembelian_obat' dengan jumlah total baru
            $detailBuilder2 = $db->table('detail_pembelian_obat');
            $detailBuilder2->where('id_detail_pembelian_obat', $id_detail_pembelian_obat);
            $detailBuilder2->update(['obat_masuk_baru' => $itemSum['total_jumlah_item']]);

            // Mengembalikan respon sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Item obat berhasil dihapus']);
        } else {
            // Mengembalikan status 404 jika halaman tidak ditemukan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailpembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Mengambil detail pembelian obat sebelum penghapusan untuk mendapatkan id_pembelian_obat dan detail lainnya
            $detail = $this->DetailPembelianObatModel->find($id);
            $id_pembelian_obat = $detail['id_pembelian_obat'];
            $id_obat = $detail['id_obat'];
            $obat_masuk = $detail['obat_masuk'];

            $pembelian_obatb = $db->table('pembelian_obat');
            $pembelian_obatb->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelian_obat = $pembelian_obatb->get()->getRowArray();

            if ($pembelian_obat['diterima'] == 1) {
                // Gagalkan jika pembelian obat sudah diterima
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Tidak bisa dilakukan. Pembelian obat sudah diterima.']);
            }

            // Mengurangi jumlah_masuk di tabel obat untuk id_obat yang sesuai
            $db->table('obat')
                ->set('jumlah_masuk', "jumlah_masuk - $obat_masuk", false)
                ->where('id_obat', $id_obat)
                ->update();

            // Menghapus detail pembelian obat
            $this->DetailPembelianObatModel->delete($id);

            // Mengatur ulang auto_increment
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
            $db->query('ALTER TABLE `item_obat` auto_increment = 1');

            // Menghitung kembali total_qty, total_masuk, dan total_biaya untuk pembelian_obat
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(obat_masuk) as total_masuk, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id_pembelian_obat);
            $result = $builder->get()->getRow();

            // Menyimpan hasil perhitungan total
            $total_qty = $result->total_qty;
            $total_masuk = $result->total_masuk;
            $total_biaya = $result->total_biaya;

            // Memperbarui tabel pembelian_obat dengan total yang baru
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_masuk' => $total_masuk,
                'total_biaya' => $total_biaya,
            ]);

            // Mengembalikan respon sukses
            return $this->response->setJSON(['message' => 'Item pembelian berhasil dihapus']);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function fakturpembelianobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data pembelian obat yang belum diterima dan informasi supplier
            $pembelianobat = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->find($id);

            // Mengambil detail pembelian obat dan menggabungkannya dengan informasi lainnya
            $detailpembelianobat = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->findAll();

            // Memeriksa apakah detail pembelian obat kosong
            if ($pembelianobat['diterima'] == 1) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Membuat nama file berdasarkan tanggal pembelian
                $filename = $pembelianobat['tgl_pembelian'] . '-pembelian-obat';
                $tanggal = new DateTime($pembelianobat['tgl_pembelian']);
                // Buat formatter untuk tanggal dan waktu
                $formatter = new IntlDateFormatter(
                    'id_ID', // Locale untuk bahasa Indonesia
                    IntlDateFormatter::LONG, // Format untuk tanggal
                    IntlDateFormatter::NONE, // Tidak ada waktu
                    'Asia/Jakarta', // Timezone
                    IntlDateFormatter::GREGORIAN, // Calendar
                    'EEEE, d MMMM yyyy HH:mm:ss' // Format tanggal lengkap dengan nama hari
                );

                // Format tanggal
                $tanggalFormat = $formatter->format($tanggal);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Menambahkan informasi header di spreadsheet
                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'FAKTUR PEMBELIAN OBAT');

                // Path gambar yang ingin ditambahkan
                $gambarPath = FCPATH . 'assets/images/logo_pec.png'; // Ganti dengan path gambar Anda

                // Membuat objek Drawing
                $drawing = new Drawing();
                $drawing->setName('Logo PEC-TK'); // Nama gambar
                $drawing->setDescription('Logo PEC-TK'); // Deskripsi gambar
                $drawing->setPath($gambarPath); // Path ke gambar
                $drawing->setCoordinates('A1'); // Koordinat sel tempat gambar akan ditambahkan
                $drawing->setHeight(36); // Tinggi gambar dalam piksel (opsional)
                $drawing->setWorksheet($sheet); // Menambahkan gambar ke worksheet

                // Menambahkan informasi tanggal dan supplier
                $sheet->setCellValue('A4', 'Hari/Tanggal:');
                $sheet->setCellValue('C4', $tanggalFormat);
                $sheet->setCellValue('A5', 'Nama Supplier:');
                $sheet->setCellValue('C5', $pembelianobat['nama_supplier']);
                $sheet->setCellValue('A6', 'Alamat Supplier:');
                $sheet->setCellValue('C6', $pembelianobat['alamat_supplier']);
                $sheet->setCellValue('A7', 'Nomor Telepon Supplier:');
                $sheet->setCellValueExplicit('C7', $pembelianobat['kontak_supplier'], DataType::TYPE_STRING);
                $sheet->setCellValue('A8', 'Merek:');
                $sheet->setCellValue('C8', $pembelianobat['merek']);
                $sheet->setCellValue('A9', 'Apoteker:');
                $sheet->setCellValue('C9', $pembelianobat['apoteker']);
                $sheet->setCellValue('A10', 'ID Pembelian:');
                $sheet->setCellValue('C10', $pembelianobat['id_pembelian_obat']);

                // Menambahkan header tabel detail pembelian
                $sheet->setCellValue('A11', 'No.');
                $sheet->setCellValue('B11', 'Nama Obat');
                $sheet->setCellValue('C11', 'Kategori Obat');
                $sheet->setCellValue('D11', 'Bentuk Obat');
                $sheet->setCellValue('E11', 'Harga Satuan');
                $sheet->setCellValue('F11', 'Qty');
                $sheet->setCellValue('G11', 'Total Harga');

                // Mengatur tata letak dan gaya untuk header
                $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:G2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:G3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                // Mengisi data detail pembelian obat ke dalam spreadsheet
                $column = 12;
                foreach ($detailpembelianobat as $list) {
                    $sheet->setCellValue('A' . $column, ($column - 10));
                    $sheet->setCellValue('B' . $column, $list['nama_obat']);
                    $sheet->setCellValue('C' . $column, $list['kategori_obat']);
                    $sheet->setCellValue('D' . $column, $list['bentuk_obat']);
                    $sheet->setCellValue('E' . $column, $list['harga_satuan']);
                    // Mengatur format harga satuan
                    $sheet->getStyle('E' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    $sheet->setCellValue('F' . $column, $list['jumlah']);
                    // Menghitung total harga
                    $total = $list['harga_satuan'] * $list['jumlah'];
                    $sheet->setCellValue('G' . $column, $total);
                    // Mengatur format total harga
                    $sheet->getStyle('G' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    // Mengatur gaya teks
                    $sheet->getStyle('A' . $column . ':G' . $column)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('A' . $column)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A' . $column . ':G' . $column)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $column++;
                }

                // Menambahkan total pembelian di bawah tabel
                $sheet->setCellValue('A' . ($column), 'Total Pembelian');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':E' . ($column));
                $sheet->setCellValue('F' . ($column), $pembelianobat['total_qty']);
                $sheet->setCellValue('G' . ($column), $pembelianobat['total_biaya']);
                // Mengatur format untuk total biaya
                $sheet->getStyle('G' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');

                // Menambahkan bagian tanda tangan penerima
                $sheet->setCellValue('G' . ($column + 2), 'Penerima');
                $sheet->setCellValue('G' . ($column + 7), '(_________________________)');

                // Mengatur gaya teks untuk header dan total
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setSize(10);
                $sheet->getStyle('C4:C10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A11:G11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G' . ($column + 2) . ':G' . ($column + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Mengatur gaya font untuk header dan total
                $sheet->getStyle('A1:A10')->getFont()->setBold(TRUE);
                $sheet->getStyle('A11:G11')->getFont()->setBold(TRUE);
                $sheet->getStyle('A11:G11')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':G' . ($column))->getFont()->setBold(TRUE);
                $sheet->getStyle('A' . ($column) . ':G' . ($column))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Menambahkan border untuk header dan tabel
                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:G2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A11:G' . ($column))->applyFromArray($tableBorder);

                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(50, 'px');
                $sheet->getColumnDimension('B')->setWidth(320, 'px');
                $sheet->getColumnDimension('C')->setWidth(120, 'px');
                $sheet->getColumnDimension('D')->setWidth(120, 'px');
                $sheet->getColumnDimension('E')->setWidth(240, 'px');
                $sheet->getColumnDimension('F')->setWidth(50, 'px');
                $sheet->getColumnDimension('G')->setWidth(240, 'px');

                // Menyimpan file spreadsheet dan mengirimkan ke browser
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
