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

        // Menghitung total data dari setiap tabel
        $total_supplier = $supplier->countAllResults(); // Total supplier
        $total_obat = $obat->countAllResults(); // Total obat
        $total_pembelian_obat_blm_diterima = $pembelian_obat->where('diterima', 0)->countAllResults(); // Total pembelian obat belum diterima
        $total_pembelian_obat_sdh_diterima = $pembelian_obat->where('diterima', 1)->countAllResults(); // Total pembelian obat sudah diterima

        // Memeriksa peran pengguna untuk menghitung resep
        if (session()->get('role') != 'Dokter') {
            $total_resep_blm_status = $resep->where('status', 0)->countAllResults(); // Total resep belum status
            $total_resep_sdh_status = $resep->where('status', 1)->countAllResults(); // Total resep sudah status
        } else {
            // Jika pengguna adalah dokter, hitung resep berdasarkan nama dokter
            $total_resep_blm_status = $resep->where('status', 0)->where('dokter', session()->get('fullname'))->countAllResults(); // Total resep belum status berdasarkan dokter
            $total_resep_sdh_status = $resep->where('status', 1)->where('dokter', session()->get('fullname'))->countAllResults(); // Total resep sudah status berdasarkan dokter
        }

        $resepbydoktergraph = $resep->select('dokter, COUNT(*) AS jumlah')->where('status', 1)->groupBy('dokter')->get(); // Resep yang Diberikan Menurut Dokter

        $resepgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, COUNT(*) AS total_resep')->where('resep.status', 1)->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m")')->get(); // Resep Per Bulan

        $total_transaksi_blm_lunas = $transaksi->where('lunas', 0)->countAllResults(); // Total transaksi belum lunas
        $total_transaksi_sdh_lunas = $transaksi->where('lunas', 1)->countAllResults(); // Total transaksi sudah lunas
        $transaksiperbulangraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, COUNT(*) AS total_transaksi')->where('transaksi.lunas', 1)->groupBy('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m")')->get(); // Transaksi yang Sudah Diproses Per Bulan
        $pemasukanperbulangraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, SUM(total_pembayaran) AS total_pemasukan')->where('transaksi.lunas', 1)->groupBy('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m")')->get(); // Pemasukan Per Bulan
        $total_user = $user->countAllResults(); // Total pengguna

        // Menyusun data untuk ditampilkan di view
        $data = [
            'total_supplier' => $total_supplier,
            'total_obat' => $total_obat,
            'total_pembelian_obat_blm_diterima' => $total_pembelian_obat_blm_diterima,
            'total_pembelian_obat_sdh_diterima' => $total_pembelian_obat_sdh_diterima,
            'total_resep_blm_status' => $total_resep_blm_status,
            'total_resep_sdh_status' => $total_resep_sdh_status,
            'resepbydoktergraph' => $resepbydoktergraph,
            'resepgraph' => $resepgraph,
            'total_transaksi_blm_lunas' => $total_transaksi_blm_lunas,
            'total_transaksi_sdh_lunas' => $total_transaksi_sdh_lunas,
            'transaksiperbulangraph' => $transaksiperbulangraph,
            'pemasukanperbulangraph' => $pemasukanperbulangraph,
            'total_user' => $total_user,
            'txtgreeting' => $txtGreeting, // Ucapan yang ditentukan sebelumnya
            'title' => 'Beranda - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Beranda', // Judul header
            'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
        ];

        // Mengembalikan tampilan beranda dengan data yang telah disiapkan
        return view('dashboard/home/index', $data);
    }
}
