<?php

namespace App\Controllers;

use App\Models\ObatModel;
use App\Models\SupplierModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Obat extends BaseController
{
    protected $ObatModel;
    public function __construct()
    {
        $this->ObatModel = new ObatModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Obat - ' . $this->systemName,
                'headertitle' => 'Obat',
                'agent' => $this->request->getUserAgent()
            ];
            // Mengembalikan tampilan daftar obat
            return view('dashboard/obat/index', $data);
        } else {
            // Jika peran tidak dikenali, lempar pengecualian untuk halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function obatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks mulai untuk paginasi
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Hitungan draw untuk DataTables

            // Mengambil parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // arah asc atau desc

            // Memetakan indeks kolom ke nama kolom di database
            $columnMapping = [
                0 => 'id_obat',
                1 => 'id_obat',
                2 => 'merek',
                3 => 'nama_obat',
                4 => 'isi_obat',
                5 => 'kategori_obat',
                6 => 'bentuk_obat',
                7 => 'harga_obat',
                8 => 'ppn',
                9 => 'mark_up',
                12 => 'penyesuaian_harga',
                13 => 'diskon',
                14 => 'harga_jual',
                15 => 'total_stok'
            ];

            // Mengambil kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_obat';

            // Menghitung total record
            $totalRecords = $this->ObatModel->countAllResults(true);

            // Query utama
            $this->ObatModel
                ->select('
        obat.*, 
        supplier.*, 

        (SELECT SUM(jumlah_masuk - jumlah_keluar) 
        FROM batch_obat 
        WHERE batch_obat.id_obat = obat.id_obat 
        AND batch_obat.tgl_kedaluwarsa > "' . date('Y-m-d') . '") AS total_stok,

        (obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) AS harga_setelah_ppn,

        ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) 
        + ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) * obat.mark_up / 100)) AS harga_jual_sebelum_bulat,

        CEIL(
            ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) 
            + ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) * obat.mark_up / 100)) / 100
        ) * 100 AS harga_jual_bulat,

        obat.penyesuaian_harga AS penyesuaian_harga,
        obat.diskon AS diskon_persen,

        (CEIL(
            ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100))
            + ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) * obat.mark_up / 100)) / 100
        ) * 100 * (1 - (obat.diskon / 100))) AS harga_setelah_diskon,

        ((CEIL(
            ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100))
            + ((obat.harga_obat + (obat.harga_obat * obat.ppn / 100)) * obat.mark_up / 100)) / 100
        ) * 100 * (1 - (obat.diskon / 100))) + obat.penyesuaian_harga) AS harga_jual
    ')
                ->join('supplier', 'supplier.id_supplier = obat.id_supplier', 'inner');

            // Menerapkan kueri pencarian
            if ($search) {
                $this->ObatModel
                    ->groupStart()
                    ->like('supplier.merek', $search)
                    ->orLike('obat.nama_obat', $search)
                    ->orLike('obat.isi_obat', $search)
                    ->groupEnd();
            }

            // Menghitung jumlah record yang difilter
            $filteredRecords = $this->ObatModel->countAllResults(false);

            // Mengurutkan data
            if ($sortColumn === 'merek') {
                $this->ObatModel
                    ->orderBy('merek', $sortDirection)
                    ->orderBy('nama_supplier', 'ASC')
                    ->orderBy('nama_obat', 'ASC')
                    ->orderBy('isi_obat', 'ASC');
            } else if ($sortColumn === 'nama_obat') {
                $this->ObatModel
                    ->orderBy('nama_obat', $sortDirection)
                    ->orderBy('isi_obat', 'ASC');
            } else {
                $this->ObatModel->orderBy($sortColumn, $sortDirection);
            }

            // Mengambil data dengan paginasi
            $obat = $this->ObatModel->findAll($length, $start);

            // Menambahkan penomoran langsung ke $obat
            foreach ($obat as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
                $item['total_stok'] = (int) ($item['total_stok'] ?? 0); // Pastikan nilai integer
            }
            unset($item); // Menghindari efek referensi

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $obat
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
            $SupplierModel = new SupplierModel();

            // Mengambil daftar supplier dan mengurutkannya
            $results = $SupplierModel->orderBy('merek', 'DESC')->findAll();

            $options = [];
            // Menyiapkan opsi untuk ditampilkan
            foreach ($results as $row) {
                // Mengkondisikan Merek
                $merek = ($row['merek'] == '') ? 'Tanpa merek' : $row['merek'];
                $nama_supplier = ($row['nama_supplier'] == '') ? 'Tanpa nama pemasok' : $row['nama_supplier'];
                $alamat_supplier = ($row['alamat_supplier'] == '') ? 'Tanpa alamat' : $row['alamat_supplier'];
                $options[] = [
                    'value' => $row['id_supplier'],
                    'text' => $merek . ' • ' . $nama_supplier . ' • ' . $alamat_supplier
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

    public function obat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data obat berdasarkan ID
            $data = $this->ObatModel->find($id);
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
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'id_supplier' => 'required',
                'nama_obat' => 'required',
                'bentuk_obat' => 'required',
                'harga_obat' => 'required|numeric|greater_than_equal_to[0]',
                'ppn' => 'required|numeric|greater_than_equal_to[0]',
                'mark_up' => 'required|numeric|greater_than_equal_to[0]',
                'penyesuaian_harga' => 'required|numeric',
                'diskon' => 'required|numeric'
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'nama_obat' => $this->request->getPost('nama_obat'),
                'isi_obat' => $this->request->getPost('isi_obat'),
                'kategori_obat' => $this->request->getPost('kategori_obat'),
                'bentuk_obat' => $this->request->getPost('bentuk_obat'),
                'harga_obat' => $this->request->getPost('harga_obat'),
                'ppn' => $this->request->getPost('ppn'),
                'mark_up' => $this->request->getPost('mark_up'),
                'penyesuaian_harga' => $this->request->getPost('penyesuaian_harga'),
                'diskon' => $this->request->getPost('diskon')
            ];
            // Menyimpan data obat ke dalam database
            $this->ObatModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil ditambahkan']);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
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
                'id_supplier' => 'required',
                'nama_obat' => 'required',
                'bentuk_obat' => 'required',
                'harga_obat' => 'required|numeric|greater_than_equal_to[0]',
                'ppn' => 'required|numeric|greater_than_equal_to[0]',
                'mark_up' => 'required|numeric|greater_than_equal_to[0]',
                'penyesuaian_harga' => 'required|numeric',
                'diskon' => 'required|numeric'
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Mengambil data obat berdasarkan ID obat yang akan diupdate
            $obat = $this->ObatModel->find($this->request->getPost('id_obat'));

            $db = db_connect(); // Koneksi ke database
            $db->transBegin();

            // Menyimpan Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_obat' => $obat['id_obat'],
                'nama_obat' => $this->request->getPost('nama_obat'),
                'isi_obat' => $this->request->getPost('isi_obat'),
                'kategori_obat' => $this->request->getPost('kategori_obat'),
                'bentuk_obat' => $this->request->getPost('bentuk_obat'),
                'harga_obat' => $this->request->getPost('harga_obat'),
                'ppn' => $this->request->getPost('ppn'),
                'mark_up' => $this->request->getPost('mark_up'),
                'penyesuaian_harga' => $this->request->getPost('penyesuaian_harga'),
                'diskon' => $this->request->getPost('diskon')
            ];

            // Memperbarui data obat
            $itemBuilder1 = $db->table('obat');
            $itemBuilder1->where('id_obat', $obat['id_obat']);
            $itemBuilder1->update($data);

            // Memeriksa status stok obat
            if ($db->transStatus() === false) {
                $db->transRollback();  // Mengembalikan jika ada masalah
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal memproses obat', 'errors' => NULL]);
            } else {
                $db->transCommit();
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons sukses
                return $this->response->setJSON(['success' => true, 'message' => 'Obat berhasil diedit']);
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
            // Menghubungkan ke database
            $db = db_connect();

            try {
                // Menghapus data obat berdasarkan ID
                $this->ObatModel->delete($id);
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons sukses
                return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Mencatat pesan kesalahan
                log_message('error', $e->getMessage());

                // Mengembalikan pesan kesalahan yang umum
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function notify_clients()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post(env('WS-URL-PHP'), [
            'json' => []
        ]);

        return $this->response->setJSON([
            'status' => 'Notification sent',
            'response' => json_decode($response->getBody(), true)
        ]);
    }
}
