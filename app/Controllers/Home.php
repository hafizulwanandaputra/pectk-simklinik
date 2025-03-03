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
        $pasien = $db->table('pasien');
        $rawatjalan = $db->table('rawat_jalan');
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
        $total_pasien = $pasien->countAllResults(); // Total pasien
        $total_rajal = $rawatjalan->countAllResults(); // Total pasien
        $total_rawatjalan = $rawatjalan->like('tanggal_registrasi', date('Y-m-d'))->where('status', 'DAFTAR')->countAllResults(); // Total rawat jalan hari ini
        $total_rawatjalan_batal = $rawatjalan->like('tanggal_registrasi', date('Y-m-d'))->where('status', 'BATAL')->countAllResults(); // Total rawat jalan yang batal hari ini
        $agamagraph = $pasien->select('agama, COUNT(*) AS total_agama')
            ->orderBy('agama', 'ASC')
            ->groupBy('agama')
            ->get();
        $jeniskelamingraph = $pasien->select('jenis_kelamin, COUNT(*) AS total_jeniskelamin')
            ->orderBy('jenis_kelamin', 'ASC')
            ->groupBy('jenis_kelamin')
            ->get();
        $persebaranprovinsigraph = $pasien->select('provinsi, COUNT(*) AS total_provinsi')
            ->orderBy('total_provinsi', 'DESC')
            ->orderBy('provinsi', 'ASC')
            ->groupBy('provinsi')
            ->get();
        $persebarankabupatengraph = $pasien->select('kabupaten, COUNT(*) AS total_kabupaten')
            ->orderBy('total_kabupaten', 'DESC')
            ->orderBy('kabupaten', 'ASC')
            ->groupBy('kabupaten')
            ->get();
        $persebarankecamatangraph = $pasien->select('kecamatan, COUNT(*) AS total_kecamatan')
            ->orderBy('total_kecamatan', 'DESC')
            ->orderBy('kecamatan', 'ASC')
            ->groupBy('kecamatan')
            ->get();
        $persebarankelurahangraph = $pasien->select('kelurahan, COUNT(*) AS total_kelurahan')
            ->orderBy('total_kelurahan', 'DESC')
            ->orderBy('kelurahan', 'ASC')
            ->groupBy('kelurahan')
            ->get();
        $rawatjalanpiegraph = $rawatjalan->select('dokter, COUNT(*) AS total_rajal')
            ->where('status', 'DAFTAR')
            ->orderBy('dokter', 'ASC')
            ->groupBy('dokter')
            ->get();
        $rawatjalangraph = $rawatjalan->select('DATE_FORMAT(tanggal_registrasi, "%Y-%m") AS bulan, COUNT(*) AS total_rajal')
            ->where('status', 'DAFTAR')
            ->groupBy('DATE_FORMAT(tanggal_registrasi, "%Y-%m")')
            ->get();
        $rawatjalandoktergraph = $rawatjalan->select('DATE_FORMAT(tanggal_registrasi, "%Y-%m") AS bulan, dokter, COUNT(*) AS total_rajal')
            ->where('status', 'DAFTAR')
            ->orderBy('dokter', 'ASC')
            ->groupBy('DATE_FORMAT(tanggal_registrasi, "%Y-%m"), dokter')
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk labels (bulan unik) dan datasets
        $labels_rawatjalandokter = [];
        $data_per_dokter_rajal = [];

        // Proses data hasil query
        foreach ($rawatjalandoktergraph as $row) {
            // Tambahkan bulan ke array labels jika belum ada
            if (!in_array($row['bulan'], $labels_rawatjalandokter)) {
                $labels_rawatjalandokter[] = $row['bulan'];
            }

            // Atur data rawat jalan per dokter
            $data_per_dokter_rajal[$row['dokter']][$row['bulan']] = $row['total_rajal'];
        }

        // Urutkan labels secara kronologis
        sort($labels_rawatjalandokter);

        // Siapkan struktur data untuk Chart.js
        $datasets_rawatjalandokter = [];
        foreach ($data_per_dokter_rajal as $dokter => $data_bulan) {
            $dataset = [
                'label' => $dokter,
                'pointStyle' => 'circle',
                'pointRadius' => 6,
                'pointHoverRadius' => 12,
                'fill' => false,
                'data' => []
            ];

            // Isi data sesuai urutan bulan di labels
            foreach ($labels_rawatjalandokter as $bulan) {
                // Gunakan nilai rawat jalan jika ada, atau 0 jika tidak ada data untuk bulan tersebut
                $dataset['data'][] = $data_bulan[$bulan] ?? 0;
            }

            $datasets_rawatjalandokter[] = $dataset;
        }

        $total_supplier = $supplier->countAllResults(); // Total supplier
        $total_obat = $obat->countAllResults(); // Total obat
        $total_pembelian_obat_blm_diterima = $pembelian_obat->where('diterima', 0)->countAllResults(); // Total pembelian obat belum diterima
        $total_pembelian_obat_sdh_diterima = $pembelian_obat->where('diterima', 1)->countAllResults(); // Total pembelian obat sudah diterima

        // Memeriksa peran pengguna untuk menghitung resep
        if (session()->get('role') == 'Dokter') {
            // Jika pengguna adalah dokter, hitung resep berdasarkan nama dokter
            $total_resep_blm_status = $resep->where('status', 0)->where('dokter !=', 'Resep Luar')->countAllResults(); // Total resep belum status berdasarkan dokter
            $total_resep_sdh_status = $resep->where('status', 1)->where('dokter !=', 'Resep Luar')->countAllResults(); // Total resep sudah status berdasarkan dokter
            $resepbydoktergraph = $resep->select('dokter, COUNT(*) AS jumlah')
                ->where('dokter !=', 'Resep Luar')
                ->where('status', 1)
                ->orderBy('dokter', 'ASC')
                ->groupBy('dokter')
                ->get(); // Resep yang Diberikan Menurut Dokter
            $resepgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, dokter, COUNT(*) AS total_resep')
                ->where('dokter !=', 'Resep Luar')
                ->where('resep.status', 1)
                ->orderBy('dokter', 'ASC')
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m"), dokter')
                ->get()
                ->getResultArray();
            $resepallgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, COUNT(*) AS total_resep')
                ->where('dokter !=', 'Resep Luar')
                ->where('resep.status', 1)
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m")')
                ->get(); // Resep Per Bulan
        } else {
            $total_resep_blm_status = $resep->where('status', 0)->countAllResults(); // Total resep belum status
            $total_resep_sdh_status = $resep->where('status', 1)->countAllResults(); // Total resep sudah status
            $resepbydoktergraph = $resep->select('dokter, COUNT(*) AS jumlah')
                ->where('status', 1)
                ->orderBy('dokter', 'ASC')
                ->groupBy('dokter')
                ->get(); // Resep yang Diberikan Menurut Dokter
            $resepgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, dokter, COUNT(*) AS total_resep')
                ->where('resep.status', 1)
                ->orderBy('dokter', 'ASC')
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m"), dokter')
                ->get()
                ->getResultArray();
            $resepallgraph = $resep->select('DATE_FORMAT(resep.tanggal_resep, "%Y-%m") AS bulan, COUNT(*) AS total_resep')
                ->where('resep.status', 1)
                ->groupBy('DATE_FORMAT(resep.tanggal_resep, "%Y-%m")')
                ->get(); // Resep Per Bulan
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
                'pointStyle' => 'circle',
                'pointRadius' => 6,
                'pointHoverRadius' => 12,
                'fill' => false,
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

        $transaksibykasirgraph = $transaksi->select('kasir, COUNT(*) AS jumlah')
            ->where('lunas', 1)
            ->orderBy('kasir', 'ASC')
            ->groupBy('kasir')
            ->get(); // Transaksi yang Diproses Menurut Petugas Kasir

        // Query untuk mendapatkan data transaksi per bulan dan per kasir
        $transaksiperbulangraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, kasir, COUNT(*) AS total_transaksi')
            ->where('transaksi.lunas', 1)
            ->orderBy('kasir', 'ASC')
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
                'pointRadius' => 6,
                'pointHoverRadius' => 12,
                'fill' => false,
                'data' => []
            ];

            // Isi data sesuai urutan bulan di labels
            foreach ($labels_transaksi as $bulan) {
                // Gunakan nilai transaksi jika ada, atau 0 jika tidak ada data untuk bulan tersebut
                $dataset['data'][] = $data_bulan[$bulan] ?? 0;
            }

            $datasets_transaksi[] = $dataset;
        }

        $transaksiperbulanallgraph = $transaksi->select('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m") AS bulan, COUNT(*) AS total_transaksi')->where('transaksi.lunas', 1)->groupBy('DATE_FORMAT(transaksi.tgl_transaksi, "%Y-%m")')->get(); // Transaksi Per Bulan

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
            'total_pasien' => $total_pasien,
            'total_rajal' => $total_rajal,
            'total_rawatjalan' => $total_rawatjalan,
            'total_rawatjalan_batal' => $total_rawatjalan_batal,
            'agamagraph' => $agamagraph,
            'jeniskelamingraph' => $jeniskelamingraph,
            'persebaranprovinsigraph' => $persebaranprovinsigraph,
            'persebarankabupatengraph' => $persebarankabupatengraph,
            'persebarankecamatangraph' => $persebarankecamatangraph,
            'persebarankelurahangraph' => $persebarankelurahangraph,
            'rawatjalanpiegraph' => $rawatjalanpiegraph,
            'labels_rawatjalandokter' => json_encode($labels_rawatjalandokter),
            'datasets_rawatjalandokter' => json_encode($datasets_rawatjalandokter),
            'rawatjalangraph' => $rawatjalangraph,
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
            'resepallgraph' => $resepallgraph,
            'total_transaksi_blm_lunas' => $total_transaksi_blm_lunas,
            'total_transaksi_sdh_lunas' => $total_transaksi_sdh_lunas,
            'transaksibykasirgraph' => $transaksibykasirgraph,
            'labels_transaksi' => json_encode($labels_transaksi),
            'datasets_transaksi' => json_encode($datasets_transaksi),
            'transaksiperbulanallgraph' => $transaksiperbulanallgraph,
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

    public function icd_x()
    {
        if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") {
            $db = db_connect();
            $bulan = $this->request->getGet('bulan');

            if (!$bulan) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Silakan masukkan bulan.'
                ]);
            }

            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $startNumber = $offset + 1;

            $icdx_bulanan_subquery = "
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_1 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_1 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_2 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_2 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_3 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_3 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_4 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_4 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icdx_kode_5 AS icdx_kode FROM medrec_assesment WHERE icdx_kode_5 IS NOT NULL
    ";

            $query = "
        SELECT 
            icdx_data.bulan, 
            icdx_data.icdx_kode, 
            icd_x.icdNamaInggris AS icdx_nama,
            COUNT(*) AS total_icdx 
        FROM ({$icdx_bulanan_subquery}) AS icdx_data 
        JOIN icd_x ON icdx_data.icdx_kode = icd_x.icdKode
        WHERE icdx_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";

            $query .= " GROUP BY icdx_data.bulan, icdx_data.icdx_kode, icd_x.icdNamaInggris ";
            $query .= " ORDER BY icdx_data.bulan DESC, total_icdx DESC ";
            $query .= " LIMIT {$limit} OFFSET {$offset} ";

            $icdx_bulanan = $db->query($query)->getResultArray();

            foreach ($icdx_bulanan as $index => &$data) {
                $data['number'] = $startNumber + $index;
            }

            $totalQuery = "
        SELECT COUNT(*) as total FROM (
            SELECT icdx_data.bulan FROM ({$icdx_bulanan_subquery}) AS icdx_data 
            JOIN icd_x ON icdx_data.icdx_kode = icd_x.icdKode
            WHERE icdx_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";
            $totalQuery .= " GROUP BY icdx_data.bulan, icdx_data.icdx_kode, icd_x.icdNamaInggris 
        ) AS count_table ";

            $total = $db->query($totalQuery)->getRowArray()['total'] ?? 0;

            return $this->response->setJSON([
                'data' => $icdx_bulanan,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function icd_9()
    {
        if (session()->get('role') == "Admin" || session()->get('role') == "Dokter" || session()->get('role') == "Perawat" || session()->get('role') == "Admisi") {
            $db = db_connect();
            $bulan = $this->request->getGet('bulan');

            if (!$bulan) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Silakan masukkan bulan.'
                ]);
            }

            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $startNumber = $offset + 1;

            $icd9_bulanan_subquery = "
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_1 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_1 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_2 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_2 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_3 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_3 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_4 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_4 IS NOT NULL
        UNION ALL
        SELECT DATE_FORMAT(waktu_dibuat, '%Y-%m') AS bulan, icd9_kode_5 AS icd9_kode FROM medrec_assesment WHERE icd9_kode_5 IS NOT NULL
    ";

            $query = "
        SELECT 
            icd9_data.bulan, 
            icd9_data.icd9_kode, 
            icd_9.icdNamaInggris AS icd9_nama,
            COUNT(*) AS total_icd9 
        FROM ({$icd9_bulanan_subquery}) AS icd9_data 
        JOIN icd_9 ON icd9_data.icd9_kode = icd_9.icdKode
        WHERE icd9_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";

            $query .= " GROUP BY icd9_data.bulan, icd9_data.icd9_kode, icd_9.icdNamaInggris ";
            $query .= " ORDER BY icd9_data.bulan DESC, total_icd9 DESC ";
            $query .= " LIMIT {$limit} OFFSET {$offset} ";

            $icd9_bulanan = $db->query($query)->getResultArray();

            foreach ($icd9_bulanan as $index => &$data) {
                $data['number'] = $startNumber + $index;
            }

            $totalQuery = "
        SELECT COUNT(*) as total FROM (
            SELECT icd9_data.bulan FROM ({$icd9_bulanan_subquery}) AS icd9_data 
            JOIN icd_9 ON icd9_data.icd9_kode = icd_9.icdKode
            WHERE icd9_data.bulan LIKE '" . $db->escapeLikeString($bulan) . "%' 
    ";
            $totalQuery .= " GROUP BY icd9_data.bulan, icd9_data.icd9_kode, icd_9.icdNamaInggris 
        ) AS count_table ";

            $total = $db->query($totalQuery)->getRowArray()['total'] ?? 0;

            return $this->response->setJSON([
                'data' => $icd9_bulanan,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
