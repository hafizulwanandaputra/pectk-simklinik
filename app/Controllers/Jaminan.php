<?php

namespace App\Controllers;

use App\Models\JaminanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;

class Jaminan extends BaseController
{
    protected $JaminanModel;
    public function __construct()
    {
        $this->JaminanModel = new JaminanModel();
    }

    public function index()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == "Admin") {
            // Jika ya, siapkan data untuk ditampilkan di tampilan
            $data = [
                'title' => 'Jaminan - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Jaminan', // Judul header
                'agent' => $this->request->getUserAgent() // Mengambil user agent dari permintaan
            ];
            // Mengembalikan tampilan dengan data yang telah disiapkan
            return view('dashboard/jaminan/index', $data);
        } else {
            // Jika bukan admin, lemparkan pengecualian halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function jaminanlist()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Mengambil data dari permintaan POST
            $request = $this->request->getPost();
            $search = $request['search']['value']; // Nilai pencarian
            $start = $request['start']; // Indeks awal untuk paginasi
            $length = $request['length']; // Panjang halaman
            $draw = $request['draw']; // Hitungan gambar untuk DataTables

            // Mendapatkan parameter pengurutan
            $order = $request['order'];
            $sortColumnIndex = $order[0]['column']; // Indeks kolom
            $sortDirection = $order[0]['dir']; // Arah pengurutan (asc atau desc)

            // Pemetaan indeks kolom ke nama kolom di database
            $columnMapping = [
                0 => 'jaminanId',
                1 => 'jaminanId',
                2 => 'jaminanKode',
                3 => 'jaminanNama',
                4 => 'jaminanAntrian',
                5 => 'jaminanStatus',
            ];

            // Mendapatkan kolom untuk diurutkan
            $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id_user';

            // Mendapatkan jumlah total catatan
            $totalRecords = $this->JaminanModel->where('jaminanKode !=', 'UMUM')->countAllResults(true);

            // Menerapkan kueri pencarian
            if ($search) {
                $this->JaminanModel
                    ->groupStart()
                    ->like('jaminanKode', $search) // Mencari berdasarkan jaminanKode
                    ->orLike('jaminanNama', $search) // Mencari berdasarkan jaminanNama
                    ->groupEnd()
                    ->where('jaminanKode !=', 'UMUM') // Mengabaikan jaminan umum
                    ->orderBy($sortColumn, $sortDirection); // Mengurutkan hasil
            }

            // Mendapatkan jumlah catatan yang terfilter
            $filteredRecords = $this->JaminanModel->where('jaminanKode !=', 'UMUM')->countAllResults(false);

            // Mengambil data pengguna
            $users = $this->JaminanModel->where('jaminanKode !=', 'UMUM')
                ->orderBy($sortColumn, $sortDirection) // Mengurutkan hasil
                ->findAll($length, $start); // Mengambil hasil dengan batasan panjang dan awal

            // Menambahkan kolom 'no' untuk menandai urutan
            foreach ($users as $index => &$item) {
                $item['no'] = $start + $index + 1; // Menambahkan kolom 'no'
            }

            // Mengembalikan respons JSON
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords, // Total catatan
                'recordsFiltered' => $filteredRecords, // Catatan terfilter
                'data' => $users // Data pengguna
            ]);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan', // Pesan error
            ]);
        }
    }

    public function jaminan($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Mengambil data pengguna berdasarkan ID, kecuali pengguna yang sedang login
            $data = $this->JaminanModel->where('jaminanKode !=', 'UMUM')->find($id);
            // Mengembalikan respons JSON dengan data pengguna
            return $this->response->setJSON($data);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menginisialisasi layanan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'jaminanKode' => 'required|is_unique[master_jaminan.jaminanKode]|max_length[4]|alpha',
                'jaminanNama' => 'required',
                'jaminanStatus' => 'required'
            ]);

            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Menyimpan data pengguna baru
            $data = [
                'jaminanKode' => $this->request->getPost('jaminanKode'),
                'jaminanAntrian' => $this->generateKodeAntrian(),
                'jaminanNama' => $this->request->getPost('jaminanNama'),
                'jaminanStatus' => $this->request->getPost('jaminanStatus'),
            ];
            // Menyimpan data ke dalam model
            $this->JaminanModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Jaminan berhasil ditambahkan']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    /**
     * Fungsi untuk menghasilkan kode antrian yang terdiri dari huruf (A-Z, AA-ZZ, dst.).
     *
     * @return string Kode antrian berikutnya.
     */
    private function generateKodeAntrian()
    {
        // Ambil kode antrian terakhir dari database
        $lastKode = $this->JaminanModel->select('jaminanAntrian')
            ->orderBy('jaminanId', 'DESC') // Mengurutkan berdasarkan ID terbaru
            ->first();

        // Jika belum ada kode antrian, mulai dari 'A'
        if (!$lastKode || empty($lastKode['jaminanAntrian'])) {
            return 'A';
        }

        // Ambil kode terakhir
        $lastChar = $lastKode['jaminanAntrian'];

        // Hitung kode berikutnya
        $nextKode = $this->incrementKodeAntrian($lastChar);

        return $nextKode;
    }

    /**
     * Fungsi untuk menghitung kode antrian berikutnya.
     *
     * @param string $current Kode antrian saat ini.
     * @return string Kode antrian berikutnya.
     */
    private function incrementKodeAntrian(string $current): string
    {
        $length = strlen($current);
        $index = $length - 1;

        while ($index >= 0) {
            if ($current[$index] === 'Z') {
                $current[$index] = 'A'; // Reset ke 'A' jika mencapai 'Z'
                $index--; // Lanjutkan ke huruf sebelumnya
            } else {
                $current[$index] = chr(ord($current[$index]) + 1); // Increment huruf
                return $current;
            }
        }

        // Jika semua huruf adalah 'Z', tambahkan huruf baru di depan
        return 'A' . $current;
    }


    public function update()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menginisialisasi layanan validasi
            $validation = \Config\Services::validation();
            // Menetapkan aturan validasi dasar
            $validation->setRules([
                'jaminanKode' => 'required|max_length[4]|alpha',
                'jaminanNama' => 'required',
                'jaminanStatus' => 'required'
            ]);
            // Validasi hanya jika kode jaminan telah diubah
            if ($this->request->getPost('jaminanKode') != $this->request->getPost('originalJaminanKode')) {
                $validation->setRule('jaminanKode', 'jaminanKode', 'required|is_unique[master_jaminan.jaminanKode]|max_length[4]|alpha'); // Pastikan kode jaminan unik
            }
            // Memeriksa apakah validasi gagal
            if (!$this->validate($validation->getRules())) {
                // Mengembalikan respons JSON dengan kesalahan validasi
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $db = db_connect();

            $data = [
                'jaminanKode' => $this->request->getPost('jaminanKode'),
                'jaminanNama' => $this->request->getPost('jaminanNama'),
                'jaminanStatus' => $this->request->getPost('jaminanStatus'),
            ];

            $jaminanId = $this->request->getPost('jaminanId');

            $db->table('master_jaminan')->where('jaminanId', $jaminanId)->update($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            // Mengembalikan respons JSON sukses
            return $this->response->setJSON(['success' => true, 'message' => 'Jaminan berhasil diedit']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            try {
                // Menghapus pengguna berdasarkan ID
                $this->JaminanModel->delete($id);
                $db = db_connect(); // Menghubungkan ke database
                // Mengatur ulang nilai Auto Increment
                $db->query('ALTER TABLE `master_jaminan` auto_increment = 1');
                // Panggil WebSocket untuk update client
                $this->notify_clients();
                // Mengembalikan respons JSON sukses
                return $this->response->setJSON(['message' => 'Jaminan berhasil dihapus']);
            } catch (DatabaseException $e) {
                // Mencatat pesan kesalahan
                log_message('error', $e->getMessage());

                // Mengembalikan pesan kesalahan generik
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
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
