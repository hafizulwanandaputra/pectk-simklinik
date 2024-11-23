<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // GREETINGS
        $seasonalGreetingA = array(); // Array untuk menyimpan ucapan musiman
        $seasonalGreetingA[] = array('dayBegin' => 30, 'monthBegin' => 12, 'dayEnd' => 31, 'monthEnd' => 12, 'text' => 'Selamat Tahun Baru'); // Ucapan untuk Tahun Baru
        $seasonalGreetingA[] = array('dayBegin' => 1, 'monthBegin' => 1, 'dayEnd' => 2, 'monthEnd' => 1, 'text' => 'Selamat Tahun Baru'); // Ucapan untuk hari pertama Tahun Baru

        $timeGreetingA = array(); // Array untuk menyimpan ucapan berdasarkan waktu
        $timeGreetingA[] = array('timeBegin' => 0, 'timeEnd' => 5, 'text' => 'Selamat Malam'); // Ucapan malam
        $timeGreetingA[] = array('timeBegin' => 5, 'timeEnd' => 11, 'text' => 'Selamat Pagi'); // Ucapan pagi
        $timeGreetingA[] = array('timeBegin' => 11, 'timeEnd' => 16, 'text' => 'Selamat Siang'); // Ucapan siang
        $timeGreetingA[] = array('timeBegin' => 16, 'timeEnd' => 19, 'text' => 'Selamat Sore'); // Ucapan sore
        $timeGreetingA[] = array('timeBegin' => 19, 'timeEnd' => 24, 'text' => 'Selamat Malam'); // Ucapan malam

        $standardGreetingA = array(); // Array untuk menyimpan ucapan standar
        $standardGreetingA[] = array('text' => 'Halo'); // Ucapan standar 1
        $standardGreetingA[] = array('text' => 'Hai'); // Ucapan standar 2

        $txtGreeting = ''; // Variabel untuk menyimpan ucapan yang akan ditampilkan

        // Mendapatkan tanggal dan bulan saat ini
        $d = (int)date('d');
        $m = (int)date('m');

        // Memeriksa apakah ada ucapan musiman yang cocok dengan tanggal saat ini
        if ($txtGreeting == '')
            if (count($seasonalGreetingA) > 0)
                foreach ($seasonalGreetingA as $sgA) {
                    $d1 = $sgA['dayBegin']; // Hari mulai ucapan
                    $m1 = $sgA['monthBegin']; // Bulan mulai ucapan

                    $d2 = $sgA['dayEnd']; // Hari akhir ucapan
                    $m2 = $sgA['monthEnd']; // Bulan akhir ucapan

                    // Memeriksa apakah tanggal saat ini berada dalam rentang ucapan musiman
                    if ($m >= $m1 and $m <= $m2)
                        if ($d >= $d1 and $d <= $d2)
                            $txtGreeting = $sgA['text']; // Menyimpan ucapan musiman yang cocok
                }

        // Mendapatkan waktu saat ini
        $time = (int)date('H');
        // Memeriksa apakah ada ucapan berdasarkan waktu yang cocok dengan waktu saat ini
        if ($txtGreeting == '')
            if (count($timeGreetingA) > 0)
                foreach ($timeGreetingA as $tgA) {
                    // Memeriksa apakah waktu saat ini berada dalam rentang ucapan berdasarkan waktu
                    if ($time >= $tgA['timeBegin'] and $time <= $tgA['timeEnd']) {
                        $txtGreeting = $tgA['text']; // Menyimpan ucapan berdasarkan waktu yang cocok
                        break; // Keluar dari loop setelah menemukan ucapan
                    }
                }

        // Jika tidak ada ucapan musiman atau waktu yang cocok, memilih ucapan standar secara acak
        if ($txtGreeting == '')
            if (count($standardGreetingA) > 0) {
                $ind = rand(0, count($standardGreetingA) - 1); // Memilih indeks acak dari ucapan standar
                if (isset($standardGreetingA[$ind])) $txtGreeting = $standardGreetingA[$ind]['text']; // Menyimpan ucapan standar yang dipilih
            }
        // END GREETINGS

        // Menghubungkan ke database
        $db = db_connect();
        // Mendapatkan tabel-tabel yang diperlukan
        $supplier = $db->table('supplier');
        $obat = $db->table('obat');
        $pembelian_obat = $db->table('pembelian_obat');
        $resep = $db->table('resep');
        $transaksi = $db->table('transaksi');
        $user = $db->table('user');
        $user_sessions = $db->table('user_sessions');

        $dokter = $resep->select('dokter')->where('status', 1)->groupBy('dokter')->get()->getResultArray(); // Dokter
        $kasir = $transaksi->select('kasir')->where('lunas', 1)->groupBy('kasir')->get()->getResultArray(); // Dokter

        // Menghitung total data dari setiap tabel
        $total_supplier = $supplier->countAllResults(); // Total supplier
        $total_obat = $obat->countAllResults(); // Total obat
        $total_pembelian_obat_blm_diterima = $pembelian_obat->where('diterima', 0)->countAllResults(); // Total pembelian obat belum diterima
        $total_pembelian_obat_sdh_diterima = $pembelian_obat->where('diterima', 1)->countAllResults(); // Total pembelian obat sudah diterima

        // Memeriksa peran pengguna untuk menghitung resep
        if (session()->get('role') == 'Dokter') {
            // Jika pengguna adalah dokter, hitung resep berdasarkan nama dokter
            $total_resep_blm_status = $resep->where('status', 0)->where('dokter !=', 'Resep Luar')->countAllResults(); // Total resep belum status berdasarkan dokter
            $total_resep_sdh_status = $resep->where('status', 1)->where('dokter !=', 'Resep Luar')->countAllResults(); // Total resep sudah status berdasarkan dokter
            $resepbydoktergraph = $resep->select('dokter, COUNT(*) AS jumlah')->where('dokter !=', 'Resep Luar')->where('status', 1)->groupBy('dokter')->get(); // Resep yang Diberikan Menurut Dokter
            $resepgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, dokter, COUNT(*) AS total_resep')
                ->where('dokter !=', 'Resep Luar')
                ->where('resep.status', 1)
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m"), dokter')
                ->get()
                ->getResultArray();
        } else {
            $total_resep_blm_status = $resep->where('status', 0)->countAllResults(); // Total resep belum status
            $total_resep_sdh_status = $resep->where('status', 1)->countAllResults(); // Total resep sudah status
            $resepbydoktergraph = $resep->select('dokter, COUNT(*) AS jumlah')->where('status', 1)->groupBy('dokter')->get(); // Resep yang Diberikan Menurut Dokter
            $resepgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, dokter, COUNT(*) AS total_resep')
                ->where('resep.status', 1)
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m"), dokter')
                ->get()
                ->getResultArray();
        }

        // Inisialisasi array untuk labels (bulan unik) dan datasets
        $labels_resep = [];
        $data_per_dokter = [];

        // Proses data hasil query
        foreach ($resepgraph as $row) {
            // Tambahkan bulan ke array labels jika belum ada
            if (!in_array($row['bulan'], $labels_resep)) {
                $labels_resep[] = $row['bulan'];
            }

            // Atur data resep per dokter
            $data_per_dokter[$row['dokter']][$row['bulan']] = $row['total_resep'];
        }

        // Urutkan labels secara kronologis
        sort($labels_resep);

        // Siapkan struktur data untuk Chart.js
        $datasets_resep = [];
        foreach ($data_per_dokter as $dokter => $data_bulan) {
            $dataset = [
                'label' => $dokter,
                'borderWidth' => 2,
                'pointStyle' => 'rectRot',
                'fill' => true,
                'data' => []
            ];

            // Isi data sesuai urutan bulan di labels
            foreach ($labels_resep as $bulan) {
                // Gunakan nilai resep jika ada, atau 0 jika tidak ada data untuk bulan tersebut
                $dataset['data'][] = $data_bulan[$bulan] ?? 0;
            }

            $datasets_resep[] = $dataset;
        }

        $total_transaksi_blm_lunas = $transaksi->where('lunas', 0)->countAllResults(); // Total transaksi belum lunas
        $total_transaksi_sdh_lunas = $transaksi->where('lunas', 1)->countAllResults(); // Total transaksi sudah lunas

        $transaksibykasirgraph = $transaksi->select('kasir, COUNT(*) AS jumlah')->where('lunas', 1)->groupBy('kasir')->get(); // Transaksi yang Diproses Menurut Petugas Kasir

        // Query untuk mendapatkan data transaksi per bulan dan per kasir
        $transaksiperbulangraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, kasir, COUNT(*) AS total_transaksi')
            ->where('transaksi.lunas', 1)
            ->groupBy('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m"), kasir')
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk labels (bulan unik) dan datasets
        $labels_transaksi = [];
        $data_per_kasir = [];

        // Memproses hasil query untuk mengumpulkan bulan dan data per kasir
        foreach ($transaksiperbulangraph as $row) {
            // Tambahkan bulan ke array labels jika belum ada
            if (!in_array($row['bulan'], $labels_transaksi)) {
                $labels_transaksi[] = $row['bulan'];
            }

            // Atur data transaksi per kasir
            $data_per_kasir[$row['kasir']][$row['bulan']] = $row['total_transaksi'];
        }

        // Urutkan labels secara kronologis
        sort($labels_transaksi);

        // Siapkan struktur data untuk Chart.js
        $datasets_transaksi = [];
        foreach ($data_per_kasir as $kasir => $data_bulan) {
            $dataset = [
                'label' => $kasir,
                'borderWidth' => 2,
                'pointStyle' => 'rectRot',
                'fill' => true,
                'data' => []
            ];

            // Isi data sesuai urutan bulan di labels
            foreach ($labels_transaksi as $bulan) {
                // Gunakan nilai transaksi jika ada, atau 0 jika tidak ada data untuk bulan tersebut
                $dataset['data'][] = $data_bulan[$bulan] ?? 0;
            }

            $datasets_transaksi[] = $dataset;
        }

        $total_pemasukan = $transaksi->where('lunas', 1)->selectSum('total_pembayaran')->get()->getRow()->total_pembayaran; // Total Pemasukan

        $pemasukanperbulangraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, SUM(total_pembayaran) AS total_pemasukan')->where('transaksi.lunas', 1)->groupBy('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m")')->get(); // Pemasukan Per Bulan

        $total_user = $user->countAllResults(); // Total pengguna
        $total_user_inactive = $user->where('active', 0)->countAllResults(); // Total pengguna nonaktif
        $total_user_active = $user->where('active', 1)->countAllResults(); // Total pengguna aktif

        $currentDateTime = date('Y-m-d H:i:s');
        $total_sessions = $user_sessions->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi
        $total_sessions_expired = $user_sessions->where('expires_at <', $currentDateTime)->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi kedaluwarsa
        $total_sessions_active = $user_sessions->where('expires_at >=', $currentDateTime)->where('session_token !=', session()->get('session_token'))->countAllResults(); // Total sesi aktif

        // Menyusun data untuk ditampilkan di view
        $data = [
            'dokter' => $dokter,
            'kasir' => $kasir,
            'total_supplier' => $total_supplier,
            'total_obat' => $total_obat,
            'total_pembelian_obat_blm_diterima' => $total_pembelian_obat_blm_diterima,
            'total_pembelian_obat_sdh_diterima' => $total_pembelian_obat_sdh_diterima,
            'total_resep_blm_status' => $total_resep_blm_status,
            'total_resep_sdh_status' => $total_resep_sdh_status,
            'resepbydoktergraph' => $resepbydoktergraph,
            'labels_resep' => json_encode($labels_resep),
            'datasets_resep' => json_encode($datasets_resep),
            'total_transaksi_blm_lunas' => $total_transaksi_blm_lunas,
            'total_transaksi_sdh_lunas' => $total_transaksi_sdh_lunas,
            'transaksibykasirgraph' => $transaksibykasirgraph,
            'labels_transaksi' => json_encode($labels_transaksi),
            'datasets_transaksi' => json_encode($datasets_transaksi),
            'total_pemasukan' => $total_pemasukan,
            'pemasukanperbulangraph' => $pemasukanperbulangraph,
            'total_user' => $total_user,
            'total_user_inactive' => $total_user_inactive,
            'total_user_active' => $total_user_active,
            'total_sessions' => $total_sessions,
            'total_sessions_expired' => $total_sessions_expired,
            'total_sessions_active' => $total_sessions_active,
            'txtgreeting' => $txtGreeting, // Ucapan yang ditentukan sebelumnya
            'title' => 'Beranda - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Beranda', // Judul header
            'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
        ];

        // Mengembalikan tampilan beranda dengan data yang telah disiapkan
        return view('dashboard/home/index', $data);
    }
}
