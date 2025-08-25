<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// AUTH
$routes->get('/', 'Auth::index');
$routes->post('/(?i)check-login', 'Auth::check_login');
$routes->get('/(?i)logout', 'Auth::logout');

// HOME
$routes->get('/(?i)home', 'Home::index');
$routes->get('/(?i)home/(?i)icd_x', 'Home::icd_x');
$routes->get('/(?i)home/(?i)icd_9', 'Home::icd_9');
$routes->post('/(?i)home/(?i)list_loket', 'Home::list_loket');
$routes->post('/(?i)home/(?i)list_antrean', 'Home::list_antrean');
$routes->get('/(?i)home/(?i)list_antrean_monitor', 'Home::list_antrean_monitor');
$routes->post('/(?i)home/(?i)buat_antrean', 'Home::buat_antrean');
$routes->get('/(?i)home/(?i)cetak_antrean/(:num)', 'Home::cetak_antrean/$1');

// ANTREAN
$routes->get('/(?i)antrean', 'Antrean::index');
$routes->get('/(?i)antrean/(?i)list_antrean', 'Antrean::list_antrean');
$routes->post('/(?i)antrean/(?i)cek_antrean/(:num)', 'Antrean::cek_antrean/$1');
$routes->post('/(?i)antrean/(?i)panggil_antrean/(:num)', 'Antrean::panggil_antrean/$1');
$routes->post('/(?i)antrean/(?i)selesai_antrean/(:num)', 'Antrean::selesai_antrean/$1');
$routes->post('/(?i)antrean/(?i)batal_antrean/(:num)', 'Antrean::batal_antrean/$1');

// PASIEN
$routes->get('/(?i)pasien', 'Pasien::index');
$routes->get('/(?i)pasien/(?i)pasienlist', 'Pasien::pasienlist');
$routes->get('/(?i)pasien/(?i)detailpasien/(:any)', 'Pasien::detailpasien/$1');
$routes->get('/(?i)pasien/(?i)pasien/(:any)', 'Pasien::pasien/$1');
$routes->post('/(?i)pasien/(?i)cekkososng', 'Pasien::cekkososng');
$routes->post('/(?i)pasien/(?i)create', 'Pasien::create');
$routes->get('/(?i)pasien/(?i)provinsi', 'Pasien::provinsi');
$routes->get('/(?i)pasien/(?i)kabupaten/(:any)', 'Pasien::kabupaten/$1');
$routes->get('/(?i)pasien/(?i)kecamatan/(:any)', 'Pasien::kecamatan/$1');
$routes->get('/(?i)pasien/(?i)kelurahan/(:any)', 'Pasien::kelurahan/$1');
$routes->get('/(?i)pasien/(?i)kiup/(:any)', 'Pasien::kiup/$1');
$routes->get('/(?i)pasien/(?i)barcode/(:any)', 'Pasien::barcode/$1');
$routes->get('/(?i)pasien/(?i)rawatjalanlist/(:any)', 'Pasien::rawatjalanlist/$1');
$routes->get('/(?i)pasien/(?i)kunjunganoptions/(:any)', 'Pasien::kunjunganoptions/$1');
$routes->get('/(?i)pasien/(?i)jaminanoptions', 'Pasien::jaminanoptions');
$routes->get('/(?i)pasien/(?i)ruanganoptions', 'Pasien::ruanganoptions');
$routes->get('/(?i)pasien/(?i)dokteroptions', 'Pasien::dokteroptions');
$routes->get('/(?i)pasien/(?i)pendaftaroptions/(:any)', 'Pasien::pendaftaroptions/$1');
$routes->get('/(?i)pasien/(?i)statusoptions/(:any)', 'Pasien::statusoptions/$1');
$routes->get('/(?i)pasien/(?i)exportexcel', 'Pasien::exportexcel');
$routes->post('/(?i)pasien/(?i)update/(:any)', 'Pasien::update/$1');

// RAWAT JALAN
$routes->get('/(?i)rawatjalan', 'RawatJalan::index');
$routes->get('/(?i)rawatjalan/(?i)rawatjalanlisttanggal', 'RawatJalan::rawatjalanlisttanggal');
$routes->get('/(?i)rawatjalan/(?i)rawatjalanlistrm', 'RawatJalan::rawatjalanlistrm');
$routes->get('/(?i)rawatjalan/(?i)rawatjalanlistnama', 'RawatJalan::rawatjalanlistnama');
$routes->get('/(?i)rawatjalan/(?i)rawatjalan/(:any)', 'RawatJalan::rawatjalan/$1');
$routes->get('/(?i)rawatjalan/(?i)kunjunganoptions', 'Pasien::kunjunganoptions');
$routes->get('/(?i)rawatjalan/(?i)jaminanoptions', 'Pasien::jaminanoptions');
$routes->get('/(?i)rawatjalan/(?i)ruanganoptions', 'Pasien::ruanganoptions');
$routes->get('/(?i)rawatjalan/(?i)dokteroptions', 'Pasien::dokteroptions');
$routes->get('/(?i)rawatjalan/(?i)statusoptions', 'Pasien::statusoptions');
$routes->post('/(?i)rawatjalan/(?i)create/(:any)', 'RawatJalan::create/$1');
$routes->post('/(?i)rawatjalan/(?i)edit/(:any)', 'RawatJalan::edit/$1');
$routes->post('/(?i)rawatjalan/(?i)editlembarisianoperasi/(:any)', 'RawatJalan::editlembarisianoperasi/$1');
$routes->get('/(?i)rawatjalan/(?i)lembarisianoperasi/(:any)', 'RawatJalan::lembarisianoperasi/$1');
$routes->get('/(?i)rawatjalan/(?i)struk/(:any)', 'RawatJalan::struk/$1');
$routes->post('/(?i)rawatjalan/(?i)cancel/(:any)', 'RawatJalan::cancel/$1');

// ASESMEN
$routes->get('/(?i)rawatjalan/(?i)asesmen/(:num)', 'Asesmen::index/$1');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)view/(:any)', 'Asesmen::view/$1');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)listvisus', 'Asesmen::listvisus');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)icdx', 'Asesmen::icdx');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)icd9', 'Asesmen::icd9');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)export/(:any)', 'Asesmen::export/$1');
$routes->post('/(?i)rawatjalan/(?i)asesmen/(?i)create/(:any)', 'Asesmen::create/$1');
$routes->post('/(?i)rawatjalan/(?i)asesmen/(?i)update/(:any)', 'Asesmen::update/$1');

// ASESMEN MATA
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)mata/(?i)list/(:any)', 'AsesmenMata::index/$1');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)mata/(?i)view/(:any)', 'AsesmenMata::view/$1');
$routes->post('/(?i)rawatjalan/(?i)asesmen/(?i)mata/(?i)create/(:any)', 'AsesmenMata::create/$1');
$routes->post('/(?i)rawatjalan/(?i)asesmen/(?i)mata/(?i)update', 'AsesmenMata::update');
$routes->delete('/(?i)rawatjalan/(?i)asesmen/(?i)mata/(?i)delete/(:any)', 'AsesmenMata::delete/$1');
$routes->get('/(?i)uploads/(?i)asesmen_mata/(:any)', 'AsesmenMata::gambar/$1');

// SKRINING
$routes->get('/(?i)rawatjalan/(?i)skrining/(:num)', 'Skrining::index/$1');
$routes->get('/(?i)rawatjalan/(?i)skrining/(?i)view/(:any)', 'Skrining::view/$1');
$routes->get('/(?i)rawatjalan/(?i)skrining/(?i)export/(:any)', 'Skrining::export/$1');
$routes->post('/(?i)rawatjalan/(?i)skrining/(?i)update/(:any)', 'Skrining::update/$1');

// EDUKASI
$routes->get('/(?i)rawatjalan/(?i)edukasi/(:num)', 'Edukasi::index/$1');
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)view/(:any)', 'Edukasi::view/$1');
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)export/(:any)', 'Edukasi::export/$1');
$routes->post('/(?i)rawatjalan/(?i)edukasi/(?i)update/(:any)', 'Edukasi::update/$1');

// EVALUASI EDUKASI
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)evaluasi/(?i)list/(:any)', 'EdukasiEvaluasi::index/$1');
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)evaluasi/(?i)view/(:any)', 'EdukasiEvaluasi::view/$1');
$routes->post('/(?i)rawatjalan/(?i)edukasi/(?i)evaluasi/(?i)create/(:any)', 'EdukasiEvaluasi::create/$1');
$routes->post('/(?i)rawatjalan/(?i)edukasi/(?i)evaluasi/(?i)update', 'EdukasiEvaluasi::update');
$routes->delete('/(?i)rawatjalan/(?i)edukasi/(?i)evaluasi/(?i)delete/(:any)', 'EdukasiEvaluasi::delete/$1');
$routes->get('/(?i)uploads/(?i)ttd_edukator_evaluasi/(:any)', 'EdukasiEvaluasi::tandatanganedukator/$1');
$routes->get('/(?i)uploads/(?i)ttd_pasien_evaluasi/(:any)', 'EdukasiEvaluasi::tandatanganpasien/$1');

// PEMERIKSAAN PENUNJANG
$routes->get('/(?i)rawatjalan/(?i)penunjang/(:num)', 'Penunjang::index/$1');
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)view/(:any)', 'Penunjang::view/$1');
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)export/(:any)', 'Penunjang::export/$1');
$routes->post('/(?i)rawatjalan/(?i)penunjang/(?i)update/(:any)', 'Penunjang::update/$1');
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)ruanganoptions', 'Penunjang::ruanganoptions');
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)dokteroptions', 'Penunjang::dokteroptions');

// PINDAI PEMERIKSAAN PENUNJANG
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)scan/(?i)list/(:any)', 'PenunjangScan::index/$1');
$routes->get('/(?i)rawatjalan/(?i)penunjang/(?i)scan/(?i)view/(:any)', 'PenunjangScan::view/$1');
$routes->post('/(?i)rawatjalan/(?i)penunjang/(?i)scan/(?i)create/(:any)', 'PenunjangScan::create/$1');
$routes->post('/(?i)rawatjalan/(?i)penunjang/(?i)scan/(?i)update', 'PenunjangScan::update');
$routes->delete('/(?i)rawatjalan/(?i)penunjang/(?i)scan/(?i)delete/(:any)', 'PenunjangScan::delete/$1');
$routes->get('/(?i)uploads/(?i)scan_penunjang/(:any)', 'PenunjangScan::gambar/$1');

// RESEP OBAT
$routes->get('/(?i)rawatjalan/(?i)resepobat/(:num)', 'ResepObat::index/$1');
$routes->post('/(?i)rawatjalan/(?i)resepobat/(?i)create/(:any)', 'ResepObat::create/$1');
$routes->post('/(?i)rawatjalan/(?i)resepobat/(?i)confirm/(:any)', 'ResepObat::confirm/$1');
$routes->post('/(?i)rawatjalan/(?i)resepobat/(?i)cancel/(:any)', 'ResepObat::cancel/$1');

// RESEP KACAMATA
$routes->get('/(?i)rawatjalan/(?i)optik/(:num)', 'Optik::index/$1');
$routes->get('/(?i)rawatjalan/(?i)optik/(?i)view/(:any)', 'Optik::view/$1');
$routes->get('/(?i)rawatjalan/(?i)optik/(?i)export/(:any)', 'Optik::export/$1');
$routes->post('/(?i)rawatjalan/(?i)optik/(?i)create/(:any)', 'Optik::create/$1');
$routes->post('/(?i)rawatjalan/(?i)optik/(?i)update/(:any)', 'Optik::update/$1');

// LAPORAN TINDAKAN RAJAL
$routes->get('/(?i)rawatjalan/(?i)laporanrajal/(:num)', 'TindakanRajal::index/$1');
$routes->get('/(?i)rawatjalan/(?i)laporanrajal/(?i)view/(:any)', 'TindakanRajal::view/$1');
$routes->get('/(?i)rawatjalan/(?i)laporanrajal/(?i)icdx', 'TindakanRajal::icdx');
$routes->get('/(?i)rawatjalan/(?i)laporanrajal/(?i)export/(:any)', 'TindakanRajal::export/$1');
$routes->post('/(?i)rawatjalan/(?i)laporanrajal/(?i)update/(:any)', 'TindakanRajal::update/$1');

// LAYANAN (DOKTER)
$routes->get('/(?i)rawatjalan/(?i)layanan/(:num)', 'LayananDokter::index/$1');
$routes->post('/(?i)rawatjalan/(?i)layanan/(?i)create/(:any)', 'LayananDokter::create/$1');

// OPERASI
$routes->get('/(?i)operasi', 'Operasi::index');
$routes->get('/(?i)operasi/(?i)operasilist', 'Operasi::operasilist');
$routes->get('/(?i)operasi/(?i)rawatjalanlist', 'Operasi::rawatjalanlist');
$routes->get('/(?i)operasi/(?i)dokterlist', 'Operasi::dokterlist');
$routes->post('/(?i)operasi/(?i)create/', 'Operasi::create');
$routes->post('/(?i)operasi/(?i)setstatus', 'Operasi::setstatus');

// SPKO
$routes->get('/(?i)operasi/(?i)spko/(:num)', 'SPOperasi::index/$1');
$routes->get('/(?i)operasi/(?i)spko/(?i)view/(:any)', 'SPOperasi::view/$1');
$routes->get('/(?i)operasi/(?i)spko/(?i)export/(:any)', 'SPOperasi::export/$1');
$routes->post('/(?i)operasi/(?i)spko/(?i)update/(:any)', 'SPOperasi::update/$1');

// PRA OPERASI
$routes->get('/(?i)operasi/(?i)praoperasi/(:num)', 'PraOperasi::index/$1');
$routes->get('/(?i)operasi/(?i)praoperasi/(?i)view/(:any)', 'PraOperasi::view/$1');
$routes->get('/(?i)operasi/(?i)praoperasi/(?i)export/(:any)', 'PraOperasi::export/$1');
$routes->post('/(?i)operasi/(?i)praoperasi/(?i)update/(:any)', 'PraOperasi::update/$1');

// KESELAMATAN
$routes->get('/(?i)operasi/(?i)safety/(:num)', 'SafetyOperasi::index/$1');
$routes->get('/(?i)operasi/(?i)safety/(?i)export/(:any)', 'SafetyOperasi::export/$1');

// SIGN IN
$routes->get('/(?i)operasi/(?i)signin/(?i)view/(:any)', 'SafetyOperasi::view_signin/$1');
$routes->post('/(?i)operasi/(?i)signin/(?i)update/(:any)', 'SafetyOperasi::update_signin/$1');

// SIGN OUT
$routes->get('/(?i)operasi/(?i)signout/(?i)view/(:any)', 'SafetyOperasi::view_signout/$1');
$routes->post('/(?i)operasi/(?i)signout/(?i)update/(:any)', 'SafetyOperasi::update_signout/$1');

// TIME OUT
$routes->get('/(?i)operasi/(?i)timeout/(?i)view/(:any)', 'SafetyOperasi::view_timeout/$1');
$routes->post('/(?i)operasi/(?i)timeout/(?i)update/(:any)', 'SafetyOperasi::update_timeout/$1');

// LAPORAN OPERASI KATARAK
$routes->get('/(?i)lpoperasikatarak', 'LPOperasiKatarak::index');
$routes->get('/(?i)lpoperasikatarak/lpoperasikataraklist', 'LPOperasiKatarak::lpoperasikataraklist');
$routes->get('/(?i)lpoperasikatarak/pasienlist', 'LPOperasiKatarak::pasienlist');
$routes->get('/(?i)lpoperasikatarak/(:num)', 'LPOperasiKatarak::details/$1');
$routes->post('/(?i)lpoperasikatarak/(?i)create', 'LPOperasiKatarak::create');
$routes->get('/(?i)lpoperasikatarak/(?i)details/(:any)', 'LPOperasiKatarak::details/$1');
$routes->get('/(?i)lpoperasikatarak/(?i)view/(:any)', 'LPOperasiKatarak::view/$1');
$routes->get('/(?i)lpoperasikatarak/(?i)export/(:any)', 'LPOperasiKatarak::export/$1');
$routes->post('/(?i)lpoperasikatarak/(?i)update/(:any)', 'LPOperasiKatarak::update/$1');
$routes->delete('/(?i)lpoperasikatarak/(?i)delete/(:any)', 'LPOperasiKatarak::delete/$1');

// LAPORAN OPERASI PTERIGIUM
$routes->get('/(?i)lpoperasipterigium', 'LPOperasiPterigium::index');
$routes->get('/(?i)lpoperasipterigium/lpoperasipterigiumlist', 'LPOperasiPterigium::lpoperasipterigiumlist');
$routes->get('/(?i)lpoperasipterigium/pasienlist', 'LPOperasiPterigium::pasienlist');
$routes->get('/(?i)lpoperasipterigium/(:num)', 'LPOperasiPterigium::details/$1');
$routes->post('/(?i)lpoperasipterigium/(?i)create', 'LPOperasiPterigium::create');
$routes->get('/(?i)lpoperasipterigium/(?i)details/(:any)', 'LPOperasiPterigium::details/$1');
$routes->get('/(?i)lpoperasipterigium/(?i)view/(:any)', 'LPOperasiPterigium::view/$1');
$routes->get('/(?i)lpoperasipterigium/(?i)export/(:any)', 'LPOperasiPterigium::export/$1');
$routes->post('/(?i)lpoperasipterigium/(?i)update/(:any)', 'LPOperasiPterigium::update/$1');
$routes->delete('/(?i)lpoperasipterigium/(?i)delete/(:any)', 'LPOperasiPterigium::delete/$1');

// LAPORAN OPERASI LAINNYA
$routes->get('/(?i)lpoperasi', 'LPOperasi::index');
$routes->get('/(?i)lpoperasi/lpoperasilist', 'LPOperasi::lpoperasilist');
$routes->get('/(?i)lpoperasi/pasienlist', 'LPOperasi::pasienlist');
$routes->get('/(?i)lpoperasi/(:num)', 'LPOperasi::details/$1');
$routes->post('/(?i)lpoperasi/(?i)create', 'LPOperasi::create');
$routes->get('/(?i)lpoperasi/(?i)details/(:any)', 'LPOperasi::details/$1');
$routes->get('/(?i)lpoperasi/(?i)view/(:any)', 'LPOperasi::view/$1');
$routes->get('/(?i)lpoperasi/(?i)export/(:any)', 'LPOperasi::export/$1');
$routes->post('/(?i)lpoperasi/(?i)update/(:any)', 'LPOperasi::update/$1');
$routes->delete('/(?i)lpoperasi/(?i)delete/(:any)', 'LPOperasi::delete/$1');

// FORM PERSETUJUAN TINDAKAN KEDOKTERAN
$routes->get('/(?i)frmsetujukedokteran', 'FRMSetujuKedokteran::index');
$routes->get('/(?i)frmsetujukedokteran/frmsetujukedokteranlist', 'FRMSetujuKedokteran::frmsetujukedokteranlist');
$routes->get('/(?i)frmsetujukedokteran/pasienlist', 'FRMSetujuKedokteran::pasienlist');
$routes->get('/(?i)frmsetujukedokteran/(:num)', 'FRMSetujuKedokteran::details/$1');
$routes->post('/(?i)frmsetujukedokteran/(?i)create', 'FRMSetujuKedokteran::create');
$routes->get('/(?i)frmsetujukedokteran/(?i)details/(:any)', 'FRMSetujuKedokteran::details/$1');
$routes->get('/(?i)frmsetujukedokteran/(?i)view/(:any)', 'FRMSetujuKedokteran::view/$1');
$routes->get('/(?i)frmsetujukedokteran/(?i)export/(:any)', 'FRMSetujuKedokteran::export/$1');
$routes->post('/(?i)frmsetujukedokteran/(?i)update/(:any)', 'FRMSetujuKedokteran::update/$1');
$routes->delete('/(?i)frmsetujukedokteran/(?i)delete/(:any)', 'FRMSetujuKedokteran::delete/$1');

// FORM PERSETUJUAN TINDAKAN ANESTESI
$routes->get('/(?i)frmsetujuanestesi', 'FRMSetujuAnestesi::index');
$routes->get('/(?i)frmsetujuanestesi/frmsetujuanestesilist', 'FRMSetujuAnestesi::frmsetujuanestesilist');
$routes->get('/(?i)frmsetujuanestesi/pasienlist', 'FRMSetujuAnestesi::pasienlist');
$routes->get('/(?i)frmsetujuanestesi/(:num)', 'FRMSetujuAnestesi::details/$1');
$routes->post('/(?i)frmsetujuanestesi/(?i)create', 'FRMSetujuAnestesi::create');
$routes->get('/(?i)frmsetujuanestesi/(?i)details/(:any)', 'FRMSetujuAnestesi::details/$1');
$routes->get('/(?i)frmsetujuanestesi/(?i)view/(:any)', 'FRMSetujuAnestesi::view/$1');
$routes->get('/(?i)frmsetujuanestesi/(?i)export/(:any)', 'FRMSetujuAnestesi::export/$1');
$routes->post('/(?i)frmsetujuanestesi/(?i)update/(:any)', 'FRMSetujuAnestesi::update/$1');
$routes->delete('/(?i)frmsetujuanestesi/(?i)delete/(:any)', 'FRMSetujuAnestesi::delete/$1');

// FORM PERSETUJUAN TINDAKAN PHACOEMULSIFIKASI
$routes->get('/(?i)frmsetujuphaco', 'FRMSetujuPhaco::index');
$routes->get('/(?i)frmsetujuphaco/frmsetujuphacolist', 'FRMSetujuPhaco::frmsetujuphacolist');
$routes->get('/(?i)frmsetujuphaco/pasienlist', 'FRMSetujuPhaco::pasienlist');
$routes->get('/(?i)frmsetujuphaco/(:num)', 'FRMSetujuPhaco::details/$1');
$routes->post('/(?i)frmsetujuphaco/(?i)create', 'FRMSetujuPhaco::create');
$routes->get('/(?i)frmsetujuphaco/(?i)details/(:any)', 'FRMSetujuPhaco::details/$1');
$routes->get('/(?i)frmsetujuphaco/(?i)view/(:any)', 'FRMSetujuPhaco::view/$1');
$routes->get('/(?i)frmsetujuphaco/(?i)export/(:any)', 'FRMSetujuPhaco::export/$1');
$routes->post('/(?i)frmsetujuphaco/(?i)update/(:any)', 'FRMSetujuPhaco::update/$1');
$routes->delete('/(?i)frmsetujuphaco/(?i)delete/(:any)', 'FRMSetujuPhaco::delete/$1');

// SURAT RUJUKAN
$routes->get('/(?i)rujukan', 'Rujukan::index');
$routes->get('/(?i)rujukan/rujukanlist', 'Rujukan::rujukanlist');
$routes->get('/(?i)rujukan/pasienlist', 'Rujukan::pasienlist');
$routes->get('/(?i)rujukan/(:num)', 'Rujukan::details/$1');
$routes->post('/(?i)rujukan/(?i)create', 'Rujukan::create');
$routes->get('/(?i)rujukan/(?i)details/(:any)', 'Rujukan::details/$1');
$routes->get('/(?i)rujukan/(?i)view/(:any)', 'Rujukan::view/$1');
$routes->get('/(?i)rujukan/(?i)export/(:any)', 'Rujukan::export/$1');
$routes->post('/(?i)rujukan/(?i)update/(:any)', 'Rujukan::update/$1');
$routes->delete('/(?i)rujukan/(?i)delete/(:any)', 'Rujukan::delete/$1');

// SURAT KETERANGAN SAKIT MATA
$routes->get('/(?i)sakitmata', 'SakitMata::index');
$routes->get('/(?i)sakitmata/sakitmatalist', 'SakitMata::sakitmatalist');
$routes->get('/(?i)sakitmata/pasienlist', 'SakitMata::pasienlist');
$routes->get('/(?i)sakitmata/(:num)', 'SakitMata::details/$1');
$routes->post('/(?i)sakitmata/(?i)create', 'SakitMata::create');
$routes->get('/(?i)sakitmata/(?i)details/(:any)', 'SakitMata::details/$1');
$routes->get('/(?i)sakitmata/(?i)view/(:any)', 'SakitMata::view/$1');
$routes->get('/(?i)sakitmata/(?i)export/(:any)', 'SakitMata::export/$1');
$routes->post('/(?i)sakitmata/(?i)update/(:any)', 'SakitMata::update/$1');
$routes->delete('/(?i)sakitmata/(?i)delete/(:any)', 'SakitMata::delete/$1');

// SURAT KETERANGAN ISTIRAHAT
$routes->get('/(?i)istirahat', 'Istirahat::index');
$routes->get('/(?i)istirahat/istirahatlist', 'Istirahat::istirahatlist');
$routes->get('/(?i)istirahat/pasienlist', 'Istirahat::pasienlist');
$routes->get('/(?i)istirahat/(:num)', 'Istirahat::details/$1');
$routes->post('/(?i)istirahat/(?i)create', 'Istirahat::create');
$routes->get('/(?i)istirahat/(?i)details/(:any)', 'Istirahat::details/$1');
$routes->get('/(?i)istirahat/(?i)view/(:any)', 'Istirahat::view/$1');
$routes->get('/(?i)istirahat/(?i)export/(:any)', 'Istirahat::export/$1');
$routes->post('/(?i)istirahat/(?i)update/(:any)', 'Istirahat::update/$1');
$routes->delete('/(?i)istirahat/(?i)delete/(:any)', 'Istirahat::delete/$1');

// SURAT KETERANGAN BUTA WARNA
$routes->get('/(?i)butawarna', 'ButaWarna::index');
$routes->get('/(?i)butawarna/butawarnalist', 'ButaWarna::butawarnalist');
$routes->get('/(?i)butawarna/pasienlist', 'ButaWarna::pasienlist');
$routes->get('/(?i)butawarna/(:num)', 'ButaWarna::details/$1');
$routes->post('/(?i)butawarna/(?i)create', 'ButaWarna::create');
$routes->get('/(?i)butawarna/(?i)details/(:any)', 'ButaWarna::details/$1');
$routes->get('/(?i)butawarna/(?i)view/(:any)', 'ButaWarna::view/$1');
$routes->get('/(?i)butawarna/(?i)listvisus', 'ButaWarna::listvisus');
$routes->get('/(?i)butawarna/(?i)export/(:any)', 'ButaWarna::export/$1');
$routes->post('/(?i)butawarna/(?i)update/(:any)', 'ButaWarna::update/$1');
$routes->delete('/(?i)butawarna/(?i)delete/(:any)', 'ButaWarna::delete/$1');

// SUPPLIER
$routes->get('/(?i)supplier', 'Supplier::index');
$routes->post('/(?i)supplier/(?i)supplierlist', 'Supplier::supplierlist');
$routes->get('/(?i)supplier/(?i)supplier/(:any)', 'Supplier::supplier/$1');
$routes->post('/(?i)supplier/(?i)create', 'Supplier::create');
$routes->post('/(?i)supplier/(?i)update', 'Supplier::update');
$routes->delete('/(?i)supplier/(?i)delete/(:any)', 'Supplier::delete/$1');

// OBAT
$routes->get('/(?i)obat', 'Obat::index');
$routes->post('/(?i)obat/(?i)obatlist', 'Obat::obatlist');
$routes->get('/(?i)obat/(?i)supplierlist', 'Obat::supplierlist');
$routes->get('/(?i)obat/(?i)obat/(:any)', 'Obat::obat/$1');
$routes->post('/(?i)obat/(?i)create', 'Obat::create');
$routes->post('/(?i)obat/(?i)update', 'Obat::update');
$routes->delete('/(?i)obat/(?i)delete/(:any)', 'Obat::delete/$1');

// BATCH OBAT
$routes->get('/(?i)batchobat', 'BatchObat::index');
$routes->post('/(?i)batchobat/(?i)batchobatlist', 'BatchObat::batchobatlist');
$routes->get('/(?i)batchobat/(?i)obatlist', 'BatchObat::obatlist');
$routes->get('/(?i)batchobat/(?i)fakturlist', 'BatchObat::fakturlist');
$routes->get('/(?i)batchobat/(?i)batchobat/(:any)', 'BatchObat::batchobat/$1');
$routes->post('/(?i)batchobat/(?i)create', 'BatchObat::create');
$routes->post('/(?i)batchobat/(?i)update', 'BatchObat::update');
$routes->delete('/(?i)batchobat/(?i)delete/(:any)', 'BatchObat::delete/$1');

// OPNAME OBAT
$routes->get('/(?i)opnameobat', 'OpnameObat::index');
$routes->get('/(?i)opnameobat/(?i)opnameobatlist', 'OpnameObat::opnameobatlist');
$routes->get('/(?i)opnameobat/(?i)apotekerlist', 'OpnameObat::apotekerlist');
$routes->post('/(?i)opnameobat/(?i)create', 'OpnameObat::create');
$routes->delete('/(?i)opnameobat/(?i)delete/(:any)', 'OpnameObat::delete/$1');

// DETAIL OPNAME OBAT
$routes->get('/(?i)opnameobat/(?i)detailopnameobat/(:any)', 'OpnameObat::detailopnameobat/$1');
$routes->get('/(?i)opnameobat/(?i)obatlist/(:any)', 'OpnameObat::obatlist/$1');
$routes->get('/(?i)opnameobat/(?i)exportopnameobat/(:any)', 'OpnameObat::exportopnameobat/$1');

// RESEP DOKTER (DARI RESEP OBAT RAWAT JALAN)
$routes->get('/(?i)resep', 'ResepDokter::index');
$routes->get('/(?i)resep/(?i)dokterlist', 'ResepDokter::dokterlist');
$routes->get('/(?i)resep/(?i)listresep', 'ResepDokter::listresep');
$routes->get('/(?i)resep/(?i)pasienlist', 'ResepDokter::pasienlist');
$routes->get('/(?i)resep/(?i)resep/(:any)', 'ResepDokter::resep/$1');
$routes->get('/(?i)resep/(?i)print/(:any)', 'ResepDokter::print/$1');
$routes->post('/(?i)resep/(?i)create', 'ResepDokter::create');
$routes->post('/(?i)resep/(?i)confirm/(:any)', 'ResepDokter::confirm/$1');
$routes->post('/(?i)resep/(?i)cancel/(:any)', 'ResepDokter::cancel/$1');
$routes->delete('/(?i)resep/(?i)delete/(:any)', 'ResepDokter::delete/$1');

// DETAIL RESEP DOKTER
$routes->get('/(?i)resep/(?i)detailresep/(:any)', 'ResepDokter::detailresep/$1');
$routes->get('/(?i)resep/(?i)detailreseplist/(:any)', 'ResepDokter::detailreseplist/$1');
$routes->get('/(?i)resep/(?i)detailresepitem/(:any)', 'ResepDokter::detailresepitem/$1');
$routes->get('/(?i)resep/(?i)obatlist/(:any)', 'ResepDokter::obatlist/$1');
$routes->get('/(?i)resep/(?i)obatkedaluwarsa', 'ResepDokter::obatkedaluwarsa');
$routes->post('/(?i)resep/(?i)tambahdetailresep/(:any)', 'ResepDokter::tambahdetailresep/$1');
$routes->post('/(?i)resep/(?i)perbaruidetailresep/(:any)', 'ResepDokter::perbaruidetailresep/$1');
$routes->delete('/(?i)resep/(?i)hapusdetailresep/(:any)', 'ResepDokter::hapusdetailresep/$1');
$routes->get('/(?i)resep/(?i)etiket-dalam/(:any)', 'ResepDokter::etiketdalam/$1');
$routes->get('/(?i)resep/(?i)etiket-luar/(:any)', 'ResepDokter::etiketluar/$1');

// RESEP LUAR
$routes->get('/(?i)resepluar', 'ResepLuar::index');
$routes->get('/(?i)resepluar/(?i)apotekerlist', 'ResepLuar::apotekerlist');
$routes->get('/(?i)resepluar/(?i)listresep', 'ResepLuar::listresep');
$routes->get('/(?i)resepluar/(?i)pasienlist', 'ResepLuar::pasienlist');
$routes->get('/(?i)resepluar/(?i)resep/(:any)', 'ResepLuar::resep/$1');
$routes->post('/(?i)resepluar/(?i)create', 'ResepLuar::create');
$routes->post('/(?i)resepluar/(?i)update', 'ResepLuar::update');
$routes->delete('/(?i)resepluar/(?i)delete/(:any)', 'ResepLuar::delete/$1');

// DETAIL RESEP LUAR
$routes->get('/(?i)resepluar/(?i)detailresep/(:any)', 'ResepLuar::detailresep/$1');
$routes->get('/(?i)resepluar/(?i)detailreseplist/(:any)', 'ResepLuar::detailreseplist/$1');
$routes->get('/(?i)resepluar/(?i)detailresepitem/(:any)', 'ResepLuar::detailresepitem/$1');
$routes->get('/(?i)resepluar/(?i)obatlist/(:any)', 'ResepLuar::obatlist/$1');
$routes->get('/(?i)resepluar/(?i)obatkedaluwarsa', 'ResepLuar::obatkedaluwarsa');
$routes->post('/(?i)resepluar/(?i)tambahdetailresep/(:any)', 'ResepLuar::tambahdetailresep/$1');
$routes->post('/(?i)resepluar/(?i)perbaruidetailresep/(:any)', 'ResepLuar::perbaruidetailresep/$1');
$routes->delete('/(?i)resepluar/(?i)hapusdetailresep/(:any)', 'ResepLuar::hapusdetailresep/$1');
$routes->get('/(?i)resepluar/(?i)etiket-dalam/(:any)', 'ResepLuar::etiketdalam/$1');
$routes->get('/(?i)resepluar/(?i)etiket-luar/(:any)', 'ResepLuar::etiketluar/$1');

// LAPORAN RESEP
$routes->get('/(?i)laporanresep/', 'LaporanResep::index');
$routes->get('/(?i)laporanresep/(?i)exportdaily/(:any)', 'LaporanResep::exportdaily/$1');
$routes->get('/(?i)laporanresep/(?i)exportdailyexcel/(:any)', 'LaporanResep::exportdailyexcel/$1');
$routes->get('/(?i)laporanresep/(?i)exportmonthly/(:any)', 'LaporanResep::exportmonthly/$1');
$routes->get('/(?i)laporanresep/(?i)exportmonthlyexcel/(:any)', 'LaporanResep::exportmonthlyexcel/$1');

// LAYANAN
$routes->get('/(?i)layanan', 'Layanan::index');
$routes->post('/(?i)layanan/(?i)layananlist', 'Layanan::layananlist');
$routes->get('/(?i)layanan/(?i)layanan/(:any)', 'Layanan::layanan/$1');
$routes->post('/(?i)layanan/(?i)create', 'Layanan::create');
$routes->post('/(?i)layanan/(?i)update', 'Layanan::update');
$routes->delete('/(?i)layanan/(?i)delete/(:any)', 'Layanan::delete/$1');

// TRANSAKSI
$routes->get('/(?i)transaksi', 'Transaksi::index');
$routes->get('/(?i)transaksi/(?i)kasirlist', 'Transaksi::kasirlist');
$routes->get('/(?i)transaksi/(?i)listtransaksi', 'Transaksi::listtransaksi');
$routes->get('/(?i)transaksi/(?i)pasienlist', 'Transaksi::pasienlist');
$routes->get('/(?i)transaksi/(?i)pasienlistexternal', 'Transaksi::pasienlistexternal');
$routes->get('/(?i)transaksi/(?i)transaksi/(:any)', 'Transaksi::transaksi/$1');
$routes->get('/(?i)transaksi/(?i)struk/(:any)', 'Transaksi::struk/$1');
$routes->post('/(?i)transaksi/(?i)create', 'Transaksi::create');
$routes->post('/(?i)transaksi/(?i)createexternal', 'Transaksi::createexternal');
$routes->post('/(?i)transaksi/(?i)process/(:any)', 'Transaksi::process/$1');
$routes->post('/(?i)transaksi/(?i)cancel/(:any)', 'Transaksi::cancel/$1');
$routes->get('/(?i)transaksi/(?i)report', 'Transaksi::reportinit');
$routes->get('/(?i)transaksi/(?i)report/(:any)', 'Transaksi::report/$1');
$routes->get('/(?i)transaksi/(?i)reportexcel/(:any)', 'Transaksi::reportexcel/$1');
$routes->delete('/(?i)transaksi/(?i)delete/(:any)', 'Transaksi::delete/$1');

// DETAIL TRANSAKSI
$routes->get('/(?i)transaksi/(?i)detailtransaksi/(:any)', 'Transaksi::detailtransaksi/$1');
$routes->get('/(?i)transaksi/(?i)detaillayananlist/(:any)', 'Transaksi::detaillayananlist/$1');
$routes->get('/(?i)transaksi/(?i)detailobatalkeslist/(:any)', 'Transaksi::detailobatalkeslist/$1');
$routes->get('/(?i)transaksi/(?i)detailtransaksiitem/(:any)', 'Transaksi::detailtransaksiitem/$1');
$routes->get('/(?i)transaksi/(?i)layananlist/(:any)/(:any)', 'Transaksi::layananlist/$1/$2');
$routes->get('/(?i)transaksi/(?i)reseplist/(:any)/(:any)', 'Transaksi::reseplist/$1/$2');
$routes->get('/(?i)transaksi/(?i)reseplistexternal/(:any)/(:any)', 'Transaksi::reseplistexternal/$1/$2');
$routes->post('/(?i)transaksi/(?i)tambahlayanan/(:any)', 'Transaksi::tambahlayanan/$1');
$routes->post('/(?i)transaksi/(?i)tambahobatalkes/(:any)', 'Transaksi::tambahobatalkes/$1');
$routes->post('/(?i)transaksi/(?i)perbaruilayanan/(:any)', 'Transaksi::perbaruilayanan/$1');
$routes->post('/(?i)transaksi/(?i)perbaruiobatalkes/(:any)', 'Transaksi::perbaruiobatalkes/$1');
$routes->delete('/(?i)transaksi/(?i)hapusdetailtransaksi/(:any)', 'Transaksi::hapusdetailtransaksi/$1');
$routes->get('/(?i)transaksi/(?i)struk/(:any)', 'Transaksi::struk/$1');

// LOKET
$routes->get('/(?i)loket', 'Loket::index');
$routes->post('/(?i)loket/(?i)loketlist', 'Loket::loketlist');
$routes->get('/(?i)loket/(?i)loket/(:any)', 'Loket::loket/$1');
$routes->post('/(?i)loket/(?i)create', 'Loket::create');
$routes->post('/(?i)loket/(?i)update', 'Loket::update');
$routes->delete('/(?i)loket/(?i)delete/(:any)', 'Loket::delete/$1');

// RUANGAN POLIKLINIK
$routes->get('/(?i)poliklinik', 'Poliklinik::index');
$routes->post('/(?i)poliklinik/(?i)polikliniklist', 'Poliklinik::polikliniklist');
$routes->get('/(?i)poliklinik/(?i)poliklinik/(:any)', 'Poliklinik::poliklinik/$1');
$routes->post('/(?i)poliklinik/(?i)create', 'Poliklinik::create');
$routes->post('/(?i)poliklinik/(?i)update', 'Poliklinik::update');
$routes->delete('/(?i)poliklinik/(?i)delete/(:any)', 'Poliklinik::delete/$1');

// JAMINAN
$routes->get('/(?i)jaminan', 'Jaminan::index');
$routes->post('/(?i)jaminan/(?i)jaminanlist', 'Jaminan::jaminanlist');
$routes->get('/(?i)jaminan/(?i)jaminan/(:any)', 'Jaminan::jaminan/$1');
$routes->post('/(?i)jaminan/(?i)create', 'Jaminan::create');
$routes->post('/(?i)jaminan/(?i)update', 'Jaminan::update');
$routes->delete('/(?i)jaminan/(?i)delete/(:any)', 'Jaminan::delete/$1');

// TINDAKAN OPERASI
$routes->get('/(?i)tindakanoperasi', 'TindakanOperasi::index');
$routes->post('/(?i)tindakanoperasi/(?i)tindakanoperasilist', 'TindakanOperasi::tindakanoperasilist');
$routes->get('/(?i)tindakanoperasi/(?i)tindakanoperasi/(:any)', 'TindakanOperasi::tindakanoperasi/$1');
$routes->post('/(?i)tindakanoperasi/(?i)create', 'TindakanOperasi::create');
$routes->post('/(?i)tindakanoperasi/(?i)update', 'TindakanOperasi::update');
$routes->delete('/(?i)tindakanoperasi/(?i)delete/(:any)', 'TindakanOperasi::delete/$1');

// UNDUH DOKUMEN
$routes->get('/(?i)unduhdokumen', 'Unduhan::index');
$routes->get('/(?i)unduhdokumen/optik', 'Unduhan::optik');

// PENGGUNA
$routes->get('/(?i)admin', 'Admin::index');
$routes->post('/(?i)admin/(?i)adminlist', 'Admin::adminlist');
$routes->get('/(?i)admin/(?i)admin/(:any)', 'Admin::admin/$1');
$routes->post('/(?i)admin/(?i)create', 'Admin::create');
$routes->post('/(?i)admin/(?i)update', 'Admin::update');
$routes->post('/(?i)admin/(?i)resetpassword/(:any)', 'Admin::resetpassword/$1');
$routes->post('/(?i)admin/(?i)activate/(:any)', 'Admin::activate/$1');
$routes->post('/(?i)admin/(?i)deactivate/(:any)', 'Admin::deactivate/$1');
$routes->delete('/(?i)admin/(?i)delete/(:any)', 'Admin::delete/$1');

// ATUR TANGGAL OTOMATIS
$routes->post('/(?i)settings/(?i)autodate-on/(:any)', 'Settings::autodate_on/$1');
$routes->post('/(?i)settings/(?i)autodate-off/(:any)', 'Settings::autodate_off/$1');

// SETTINGS
$routes->get('/(?i)settings', 'Settings::index');

// HAPUS DATA REKAM MEDIS YANG KOSONG
$routes->get('/(?i)settings/(?i)emptyrecords', 'Settings::emptyrecords');
$routes->delete('/(?i)settings/(?i)deleteempty', 'Settings::deleteempty');

// CHANGE CASHIER PASSWORD
$routes->get('/(?i)settings/(?i)pwdtransaksi', 'Settings::pwdTransaksi');
$routes->post('/(?i)settings/(?i)updatepwdtransaksi', 'Settings::updatePwdTransaksi');

// SESSION MANAGER
$routes->get('/(?i)settings/(?i)sessions', 'Sessions::index');
$routes->post('/(?i)settings/(?i)sessionslist', 'Sessions::sessionslist');
$routes->delete('/(?i)settings/(?i)flush', 'Sessions::flush');
$routes->delete('/(?i)settings/(?i)deleteexpired', 'Sessions::deleteexpired');
$routes->delete('/(?i)settings/(?i)deletesession/(:any)', 'Sessions::deletesession/$1');

// CHANGE USER INFORMATION
$routes->get('/(?i)settings/(?i)edit', 'Settings::edit');
$routes->post('/(?i)settings/(?i)update', 'Settings::update');

// CHANGE PASSWORD
$routes->get('/(?i)settings/(?i)changepassword', 'ChangePassword::index');
$routes->post('/(?i)settings/(?i)changepassword/(?i)update', 'ChangePassword::update');

// ABOUT SYSTEM
$routes->get('/(?i)settings/(?i)about', 'Settings::about');
