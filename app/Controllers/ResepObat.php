<?php

namespace App\Controllers;

use App\Models\ResepModel;
use App\Models\DetailResepModel;
use App\Models\RawatJalanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ResepObat extends BaseController
{
    protected $ResepModel;
    protected $DetailResepModel;
    protected $RawatJalanModel;
    public function __construct()
    {
        $this->ResepModel = new ResepModel();
        $this->DetailResepModel = new DetailResepModel();
        $this->RawatJalanModel = new RawatJalanModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if (!$rawatjalan) {
                throw PageNotFoundException::forPageNotFound();
            }

            $title = 'Resep Obat ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName;
            $headertitle = 'Resep Obat';

            // Memeriksa apakah resep sudah ada
            $resep = $db->table('resep')
                ->where('nomor_registrasi', $rawatjalan['nomor_registrasi'])
                ->get()
                ->getRowArray();

            // Query untuk item sebelumnya
            $previous = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan <', $id) // Kondisi untuk id sebelumnya
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.id_rawat_jalan >', $id) // Kondisi untuk id berikutnya
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.no_rm', $rawatjalan['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            if (!$resep) {
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'title' => $title,
                    'headertitle' => $headertitle, // Judul header
                    'agent' => $this->request->getUserAgent(), // Mengambil user agent
                    'previous' => $previous,
                    'next' => $next,
                    'listRawatJalan' => $listRawatJalan
                ];
                return view('dashboard/rawatjalan/resepobat/empty', $data);
            }

            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'resep' => $resep,
                'title' => $title,
                'headertitle' => $headertitle, // Judul header
                'agent' => $this->request->getUserAgent(), // Mengambil user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            return view('dashboard/rawatjalan/resepobat/index', $data); // Mengembalikan tampilan resep
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function create($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Inisialisasi rawat jalan
            $rawatjalan = $this->RawatJalanModel
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->where('rawat_jalan.status', 'DAFTAR')
                ->find($id);

            if ($rawatjalan['transaksi'] == 1) {
                session()->setFlashdata('error', 'Resep obat tidak dapat ditambahkan pada rawat jalan yang transaksisnya sudah diproses');
                return redirect()->back();
            }

            if (session()->get('role') == 'Dokter') {
                if ($rawatjalan['dokter'] != session()->get('fullname')) {
                    session()->setFlashdata('error', 'Resep obat ini hanya bisa ditambahkan oleh ' . $rawatjalan['dokter']);
                    return redirect()->back();
                }
            }

            // Menyiapkan data untuk disimpan
            $data = [
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'no_rm' => $rawatjalan['no_rm'],
                'nama_pasien' => $rawatjalan['nama_pasien'],
                'alamat' => $rawatjalan['alamat'],
                'telpon' => $rawatjalan['telpon'],
                'jenis_kelamin' => $rawatjalan['jenis_kelamin'],
                'tempat_lahir' => $rawatjalan['tempat_lahir'],
                'tanggal_lahir' => $rawatjalan['tanggal_lahir'],
                'dokter' => $rawatjalan['dokter'], // Menyimpan nama dokter yang sedang login
                'apoteker' => NULL,
                'tanggal_resep' => date('Y-m-d H:i:s'), // Menyimpan tanggal resep saat ini
                'jumlah_resep' => 0,
                'total_biaya' => 0,
                'confirmed' => 0,
                'status' => 0,
            ];

            // Menyimpan data resep ke dalam model
            $this->ResepModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return redirect()->back();
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function listresepold($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            // Ambil parameter no_rm
            $id_resep = $this->request->getGet('id_resep');
            $no_rm = $this->request->getGet('no_rm');

            // Jika no_rm tidak ada / kosong → kirim data kosong
            if (empty($id_resep) && empty($no_rm)) {
                return $this->response->setJSON([]);
            }

            // ambil resep berdasarkan ID
            $results = $this->ResepModel
                ->where('id_resep !=', $id_resep)
                ->where('no_rm', $no_rm)
                ->findAll();

            $options = [];
            // Memetakan hasil resep ke dalam format yang diinginkan
            foreach ($results as $row) {
                $total_biaya = (int) $row['total_biaya']; // Mengonversi total biaya ke integer
                $total_biaya_terformat = number_format($total_biaya, 0, ',', '.'); // Memformat total biaya

                $options[] = [
                    'value' => $row['id_resep'], // ID resep
                    'text' =>  $row['nomor_registrasi'] . ' (' . $row['tanggal_resep'] . ' • Rp' . $total_biaya_terformat . ')' // Tanggal resep dengan total biaya terformat
                ];
            }

            // Mengembalikan opsi resep dalam bentuk JSON
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

    public function listdetailresepold($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $id_resep = $this->request->getGet('id_resep');
            // ambil resep berdasarkan ID
            $resep = $this->ResepModel
                ->where('id_resep', $id)
                ->first();
            // Mengambil detail resep berdasarkan id_resep yang diberikan
            $data = $this->DetailResepModel
                ->join('resep', 'resep.id_resep = detail_resep.id_resep', 'inner') // Bergabung dengan tabel resep
                ->where('detail_resep.id_resep', $id_resep)
                ->orderBy('id_detail_resep', 'ASC') // Mengurutkan berdasarkan id_detail_resep
                ->findAll();

            // Mengembalikan data dalam format JSON
            return $this->response->setJSON([
                'resep' => $resep,
                'data' => $data,
            ]);
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function confirm($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Memperbarui resep
            $resep = $db->table('resep');
            $prescription = $resep->where('id_resep', $id)->get()->getRow();

            if ($prescription->status == 1) {
                // Jika resep ini sudah ditransaksikan
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengonfirmasi resep, resep ini sudah ditransaksikan.'
                ]);
            }

            $detailCount = $db->table('detail_resep')
                ->where('id_resep', $id)
                ->countAllResults();

            if ($detailCount < 1) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengonfirmasi resep, detail resep masih kosong.'
                ]);
            }

            if (session()->get('role') == 'Dokter') {
                if (!$prescription) {
                    // Jika dokter tidak sesuai atau resep tidak ditemukan
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengonfirmasi resep, resep tidak ditemukan.'
                    ]);
                } else if ($prescription->dokter !== session()->get('fullname')) {
                    // Jika dokter tidak sesuai atau resep tidak ditemukan
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengonfirmasi resep, dokter tidak sesuai (DPJP: ' . $prescription->dokter . ').'
                    ]);
                }
            }

            $resep->where('id_resep', $id);
            $resep->update([
                'confirmed' => 1, // Atur sebagai dikonfirmasi
            ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil dikonfirmasi dan sudah dapat diproses oleh apoteker']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function cancel($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Dokter' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            // Memperbarui resep
            $resep = $db->table('resep');
            $prescription = $resep->where('id_resep', $id)->get()->getRow();

            if ($prescription->status == 1) {
                // Jika resep ini sudah ditransaksikan
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan konfirmasi resep, resep ini sudah ditransaksikan.'
                ]);
            }

            if (session()->get('role') == 'Dokter') {
                if (!$prescription) {
                    // Jika dokter tidak sesuai atau resep tidak ditemukan
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Gagal membatalkan konfirmasi resep, resep tidak ditemukan.'
                    ]);
                } else if ($prescription->dokter !== session()->get('fullname')) {
                    // Jika dokter tidak sesuai atau resep tidak ditemukan
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Gagal membatalkan konfirmasi resep, dokter tidak sesuai (DPJP: ' . $prescription->dokter . ').'
                    ]);
                }
            }

            $resep->where('id_resep', $id);
            $resep->update([
                'confirmed' => 0, // Atur sebagai tidak dikonfirmasi
            ]);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return $this->response->setJSON(['success' => true, 'message' => 'Resep berhasil dibatalkan konfirmasinya']);
        } else {
            // Jika peran tidak valid, kembalikan status 404
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
