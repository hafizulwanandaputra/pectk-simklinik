<?php

namespace App\Controllers;

use App\Models\BatchObatModel;
use App\Models\ObatModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class BatchObat extends BaseController
{
    protected $BatchObatModel;
    public function __construct()
    {
        $this->BatchObatModel = new BatchObatModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Faktur Obat - ' . $this->systemName,
                'headertitle' => 'Faktur Obat',
                'agent' => $this->request->getUserAgent()
            ];
            // Mengembalikan tampilan daftar batch obat
            return view('dashboard/batchobat/index', $data);
        } else {
            // Jika peran tidak dikenali, lempar pengecualian untuk halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function batchobatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
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
                0 => 'id_batch_obat',
                1 => 'id_batch_obat',
                2 => 'obat',
                3 => 'no_faktur',
                4 => 'nama_batch',
                5 => 'tgl_kedaluwarsa',
                6 => 'jumlah_masuk',
                7 => 'jumlah_keluar',
                8 => 'sisa_stok',
                9 => 'diperbarui',
            ];

            // Mengambil kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_batch_obat';

            // Menghitung total record
            $totalRecords = $this->BatchObatModel->countAllResults(true);

            if ($sortColumn === 'obat') {
                // Mengurutkan berdasarkan nama_obat, kemudian no_faktur, lalu nama_batch
                $this->BatchObatModel
                    ->orderBy('nama_obat', $sortDirection)
                    ->orderBy('no_faktur', 'ASC')
                    ->orderBy('nama_batch', 'ASC');
            } elseif ($sortColumn === 'no_faktur') {
                // Mengurutkan berdasarkan no_faktur, lalu nama_batch
                $this->BatchObatModel
                    ->orderBy('no_faktur', $sortDirection)
                    ->orderBy('nama_batch', 'ASC');
            } elseif ($sortColumn === 'nama_batch') {
                // Hanya mengurutkan berdasarkan nama_batch
                $this->BatchObatModel->orderBy('nama_batch', $sortDirection);
            } else {
                // Perilaku pengurutan default
                $this->BatchObatModel->orderBy($sortColumn, $sortDirection);
            }

            // Menerapkan kueri pencarian
            if ($search) {
                $this->BatchObatModel
                    ->join('obat as s1', 's1.id_obat = batch_obat.id_obat', 'inner')
                    ->groupStart()
                    ->like('s1.nama_obat', $search)
                    ->orLike('batch_obat.no_faktur', $search)
                    ->orLike('batch_obat.nama_batch', $search)
                    ->orLike('batch_obat.tgl_kedaluwarsa', $search)
                    ->groupEnd();
            }

            // Menghitung jumlah record yang difilter
            $filteredRecords = $this->BatchObatModel
                ->join('obat AS s2', 's2.id_obat = batch_obat.id_obat', 'inner')->countAllResults(false);

            $batch_obat = $this->BatchObatModel
                ->select('
                batch_obat.*, 
                obat.*, 
                -- Hitung sisa stok
                (batch_obat.jumlah_masuk - batch_obat.jumlah_keluar) as sisa_stok
                ')
                ->join('obat', 'obat.id_obat = batch_obat.id_obat', 'inner')
                ->findAll($length, $start);

            // Menambahkan penomoran langsung ke $batch_obat
            foreach ($batch_obat as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $batch_obat
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $ObatModel = new ObatModel();

            // Mengambil daftar obat dan mengurutkannya
            $results = $ObatModel->orderBy('nama_obat', 'ASC')->findAll();

            $options = [];
            // Menyiapkan opsi untuk ditampilkan
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

                $options[] = [
                    'value' => $row['id_obat'], // Menyimpan id_obat
                    'text' => $row['nama_obat'] .
                        ' (' . $row['isi_obat'] .
                        ' • ' . $row['kategori_obat'] .
                        ' • ' . $row['bentuk_obat'] .
                        ' • Rp' . $harga_obat_terformat . ')' // Menyimpan informasi obat
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

    public function fakturlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Mengambil daftar obat dan mengurutkannya
            $results = $db->table('batch_obat')
                ->select('no_faktur')
                ->orderBy('no_faktur', 'ASC')
                ->groupBy('no_faktur')
                ->get()->getResultArray();

            $options = [];
            // Menyiapkan opsi untuk ditampilkan
            foreach ($results as $row) {
                $options[] = [
                    'value' => $row['no_faktur']
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

    public function batchobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil data obat berdasarkan ID
            $data = $this->BatchObatModel->find($id);
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
                'id_obat' => 'required',
                'tgl_kedaluwarsa' => 'required',
                'jumlah_masuk' => 'required|numeric|greater_than_equal_to[0]',
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan Data
            $data = [
                'id_obat' => $this->request->getPost('id_obat'),
                'no_faktur' => $this->request->getPost('no_faktur') ?: null,
                'nama_batch' => $this->request->getPost('nama_batch') ?: null,
                'tgl_kedaluwarsa' => $this->request->getPost('tgl_kedaluwarsa'),
                'jumlah_masuk' => $this->request->getPost('jumlah_masuk'),
                'diperbarui' => date('Y-m-d H:i:s'), // Waktu pembaruan
            ];
            // Menyimpan data obat ke dalam database
            $this->BatchObatModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Faktur obat berhasil ditambahkan']);
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
                'id_obat' => 'required',
                'tgl_kedaluwarsa' => 'required',
                'jumlah_masuk' => 'required|numeric',
            ]);

            // Memeriksa apakah validasi berhasil
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Mengambil data obat berdasarkan ID obat yang akan diupdate
            $batch_obat = $this->BatchObatModel->find($this->request->getPost('id_batch_obat'));

            $db = db_connect(); // Koneksi ke database
            $db->transBegin();

            // Menyimpan Data
            $data = [
                'id_batch_obat' => $batch_obat['id_batch_obat'],
                'id_obat' => $this->request->getPost('id_obat'),
                'no_faktur' => $this->request->getPost('no_faktur') ?: null,
                'nama_batch' => $this->request->getPost('nama_batch') ?: null,
                'tgl_kedaluwarsa' => $this->request->getPost('tgl_kedaluwarsa'),
                'jumlah_masuk' => $batch_obat['jumlah_masuk'] + $this->request->getPost('jumlah_masuk'),
                'diperbarui' => $batch_obat['diperbarui'], // Mengambil waktu pembaruan dari data sebelumnya
            ];

            // Memperbarui data obat
            $itemBuilder1 = $db->table('batch_obat');
            $itemBuilder1->where('id_batch_obat', $batch_obat['id_batch_obat']);
            $itemBuilder1->update($data);

            if ($data['jumlah_masuk'] < $batch_obat['jumlah_keluar']) {
                // Gagalkan jika pembelian obat sudah diterima
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Jumlah masuk tidak bisa kurang dari jumlah keluar.']);
            }

            // Memeriksa status stok obat
            if ($db->transStatus() === false) {
                $db->transRollback();  // Mengembalikan jika ada masalah
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal memproses faktur obat', 'errors' => NULL]);
            } else {
                $db->transCommit();
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons sukses
                return $this->response->setJSON(['success' => true, 'message' => 'Faktur obat berhasil diedit']);
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
                $this->BatchObatModel->delete($id);
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons sukses
                return $this->response->setJSON(['message' => 'Faktur obat berhasil dihapus']);
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
