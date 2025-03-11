<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\RawatJalanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class LayananDokter extends BaseController
{
    protected $TransaksiModel;
    protected $DetailTransaksiModel;
    protected $RawatJalanModel;
    public function __construct()
    {
        $this->TransaksiModel = new TransaksiModel();
        $this->DetailTransaksiModel = new DetailTransaksiModel();
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

            $title = 'Transaksi ' . $rawatjalan['nama_pasien'] . ' (' . $rawatjalan['no_rm'] . ') - ' . $rawatjalan['nomor_registrasi'] . ' - ' . $this->systemName;
            $headertitle = 'Transaksi';

            // Memeriksa apakah transaksi sudah ada
            $transaksi = $db->table('transaksi')
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

            if (!$transaksi) {
                $data = [
                    'rawatjalan' => $rawatjalan,
                    'title' => $title,
                    'headertitle' => $headertitle, // Judul header
                    'agent' => $this->request->getUserAgent(), // Mengambil user agent
                    'previous' => $previous,
                    'next' => $next,
                    'listRawatJalan' => $listRawatJalan
                ];
                return view('dashboard/rawatjalan/layanan/empty', $data);
            }

            // Menyusun data yang akan dikirim ke tampilan
            $data = [
                'rawatjalan' => $rawatjalan,
                'transaksi' => $transaksi,
                'title' => $title,
                'headertitle' => $headertitle, // Judul header
                'agent' => $this->request->getUserAgent(), // Mengambil user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            return view('dashboard/rawatjalan/layanan/index', $data); // Mengembalikan tampilan resep
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
                session()->setFlashdata('error', 'Layanan tidak dapat ditambahkan pada rawat jalan yang transaksisnya sudah diproses');
                return redirect()->back();
            }

            if (session()->get('role') == 'Dokter') {
                if ($rawatjalan['dokter'] != session()->get('fullname')) {
                    session()->setFlashdata('error', 'Layanan ini hanya bisa ditambahkan oleh ' . $rawatjalan['dokter']);
                    return redirect()->back();
                }
            }

            // Mendapatkan tanggal saat ini
            $date = new \DateTime();
            $tanggal = $date->format('d'); // Hari (2 digit)
            $bulan = $date->format('m'); // Bulan (2 digit)
            $tahun = $date->format('y'); // Tahun (2 digit)

            // Mengambil nomor registrasi terakhir untuk di-increment
            $lastNoReg = $this->TransaksiModel->getLastNoReg1($tahun, $bulan, $tanggal);
            $lastNumber = $lastNoReg ? intval(substr($lastNoReg, -4)) : 0; // Mendapatkan nomor terakhir
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Menyiapkan nomor berikutnya

            // Memformat nomor kwitansi
            $no_kwitansi = sprintf('TRJ%s%s%s-%s', $tanggal, $bulan, $tahun, $nextNumber);

            // Menyiapkan data untuk disimpan
            $data = [
                'id_resep' => NULL, // ID resep diatur ke NULL
                'nomor_registrasi' => $rawatjalan['nomor_registrasi'],
                'no_rm' => $rawatjalan['no_rm'],
                'nama_pasien' => $rawatjalan['nama_pasien'],
                'alamat' => $rawatjalan['alamat'],
                'telpon' => $rawatjalan['telpon'],
                'jenis_kelamin' => $rawatjalan['jenis_kelamin'],
                'tempat_lahir' => $rawatjalan['tempat_lahir'],
                'tanggal_lahir' => $rawatjalan['tanggal_lahir'],
                'dokter' => $rawatjalan['dokter'], // Menyimpan nama dokter yang sedang login
                'kasir' => 'Ditambahkan Dokter',
                'no_kwitansi' => $no_kwitansi, // Nomor kwitansi
                'tgl_transaksi' => date('Y-m-d H:i:s'), // Tanggal dan waktu transaksi
                'total_pembayaran' => 0, // Total pembayaran awal
                'metode_pembayaran' => '', // Metode pembayaran (kosong pada awalnya)
                'lunas' => 0, // Status lunas (0 berarti belum lunas)
            ];

            // Menyimpan data resep ke dalam model
            $this->TransaksiModel->save($data);
            // Panggil WebSocket untuk update client
            $this->notify_clients();
            return redirect()->back();
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
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
