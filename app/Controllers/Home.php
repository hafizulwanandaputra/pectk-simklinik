<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function __construct() {}

    public function index()
    {
        // GREETINGS
        $seasonalGreetingA = array();
        $seasonalGreetingA[] = array('dayBegin' => 30, 'monthBegin' => 12, 'dayEnd' => 31, 'monthEnd' => 12, 'text' => 'Selamat Tahun Baru');
        $seasonalGreetingA[] = array('dayBegin' => 1, 'monthBegin' => 1, 'dayEnd' => 2, 'monthEnd' => 1, 'text' => 'Selamat Tahun Baru');

        $timeGreetingA = array();
        $timeGreetingA[] = array('timeBegin' => 0, 'timeEnd' => 5, 'text' => 'Selamat Malam');
        $timeGreetingA[] = array('timeBegin' => 5, 'timeEnd' => 11, 'text' => 'Selamat Pagi');
        $timeGreetingA[] = array('timeBegin' => 11, 'timeEnd' => 16, 'text' => 'Selamat Siang');
        $timeGreetingA[] = array('timeBegin' => 16, 'timeEnd' => 19, 'text' => 'Selamat Sore');
        $timeGreetingA[] = array('timeBegin' => 19, 'timeEnd' => 24, 'text' => 'Selamat Malam');

        $standardGreetingA = array();
        $standardGreetingA[] = array('text' => 'Halo');
        $standardGreetingA[] = array('text' => 'Hai');

        $txtGreeting = '';

        $d = (int)date('d');
        $m = (int)date('m');
        if ($txtGreeting == '')
            if (count($seasonalGreetingA) > 0)
                foreach ($seasonalGreetingA as $sgA) {
                    $d1 = $sgA['dayBegin'];
                    $m1 = $sgA['monthBegin'];

                    $d2 = $sgA['dayEnd'];
                    $m2 = $sgA['monthEnd'];

                    //echo $m1.' >= '.$m.' <= '.$m2.'<br />';
                    if ($m >= $m1 and $m <= $m2)
                        if ($d >= $d1 and $d <= $d2)
                            $txtGreeting = $sgA['text'];
                }

        $time = (int)date('H');
        if ($txtGreeting == '')
            if (count($timeGreetingA) > 0)
                foreach ($timeGreetingA as $tgA) {
                    if ($time >= $tgA['timeBegin'] and $time <= $tgA['timeEnd']) {
                        $txtGreeting = $tgA['text'];
                        break;
                    }
                }

        if ($txtGreeting == '')
            if (count($standardGreetingA) > 0) {
                $ind = rand(0, count($standardGreetingA) - 1);
                if (isset($standardGreetingA[$ind])) $txtGreeting = $standardGreetingA[$ind]['text'];
            }
        // END GREETINGS

        $db = db_connect();
        $menu = $db->table('menu');
        $permintaan = $db->table('permintaan');
        $petugas = $db->table('petugas');
        $admin = $db->table('user');
        $totalmenu = $menu->countAllResults();
        $totalpermintaan = $permintaan->countAllResults();
        $totalpetugas = $petugas->countAllResults();
        $totaladmin = $admin->countAllResults();
        $permintaangraph = $db->query('SELECT `nama_menu`, `jumlah` FROM `menu`;');
        $petugasgraph = $db->query('SELECT `nama_petugas`, `jumlah_menu` FROM `petugas`;');
        $permintaanperbulangraph = $permintaan->select('DATE_FORMAT(menu.tanggal, "%Y-%m") AS bulan, COUNT(*) AS jumlah_permintaan')->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')->groupBy('DATE_FORMAT(menu.tanggal, "%Y-%m")')->get();
        $data = [
            'totalmenu' => $totalmenu,
            'totalpermintaan' => $totalpermintaan,
            'totalpetugas' => $totalpetugas,
            'totaladmin' => $totaladmin,
            'permintaanperbulangraph' => $permintaanperbulangraph,
            'permintaangraph' => $permintaangraph,
            'petugasgraph' => $petugasgraph,
            'txtgreeting' => $txtGreeting,
            'title' => 'Beranda - ' . $this->systemName,
            'headertitle' => 'Beranda',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/home/index', $data);
    }
}
