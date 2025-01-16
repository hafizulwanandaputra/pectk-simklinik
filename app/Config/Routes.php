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

// PASIEN
$routes->get('/(?i)pasien', 'Pasien::index');
$routes->get('/(?i)pasien/(?i)pasienlist', 'Pasien::pasienlist');
$routes->get('/(?i)pasien/(?i)detailpasien/(:any)', 'Pasien::detailpasien/$1');
$routes->get('/(?i)pasien/(?i)pasien/(:any)', 'Pasien::pasien/$1');
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
$routes->get('/(?i)rawatjalan/(?i)rawatjalanlisttanggal/(:any)', 'RawatJalan::rawatjalanlisttanggal/$1');
$routes->get('/(?i)rawatjalan/(?i)rawatjalanlistrm/(:any)', 'RawatJalan::rawatjalanlistrm/$1');
$routes->get('/(?i)rawatjalan/(?i)kunjunganoptions', 'Pasien::kunjunganoptions');
$routes->get('/(?i)rawatjalan/(?i)jaminanoptions', 'Pasien::jaminanoptions');
$routes->get('/(?i)rawatjalan/(?i)ruanganoptions', 'Pasien::ruanganoptions');
$routes->get('/(?i)rawatjalan/(?i)dokteroptions', 'Pasien::dokteroptions');
$routes->get('/(?i)rawatjalan/(?i)statusoptions', 'Pasien::statusoptions');
$routes->post('/(?i)rawatjalan/(?i)create/(:any)', 'RawatJalan::create/$1');
$routes->get('/(?i)rawatjalan/(?i)struk/(:any)', 'RawatJalan::struk/$1');
$routes->post('/(?i)rawatjalan/(?i)cancel/(:any)', 'RawatJalan::cancel/$1');

// ASESMEN
$routes->get('/(?i)rawatjalan/(?i)asesmen/(:num)', 'Asesmen::index/$1');
$routes->get('/(?i)rawatjalan/(?i)asesmen/(?i)view/(:any)', 'Asesmen::view/$1');
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
$routes->post('/(?i)rawatjalan/(?i)skrining/(?i)create/(:any)', 'Skrining::create/$1');
$routes->post('/(?i)rawatjalan/(?i)skrining/(?i)update/(:any)', 'Skrining::update/$1');

// EDUKASI
$routes->get('/(?i)rawatjalan/(?i)edukasi/(:num)', 'Edukasi::index/$1');
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)view/(:any)', 'Edukasi::view/$1');
$routes->get('/(?i)rawatjalan/(?i)edukasi/(?i)export/(:any)', 'Edukasi::export/$1');
$routes->post('/(?i)rawatjalan/(?i)edukasi/(?i)create/(:any)', 'Edukasi::create/$1');
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
$routes->post('/(?i)rawatjalan/(?i)penunjang/(?i)create/(:any)', 'Penunjang::create/$1');
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
$routes->get('/(?i)obat/(?i)stokobat/(:any)', 'Obat::stokobat/$1');
$routes->post('/(?i)obat/(?i)create', 'Obat::create');
$routes->post('/(?i)obat/(?i)update', 'Obat::update');
$routes->post('/(?i)obat/(?i)updatestokobat', 'Obat::updatestokobat');
$routes->delete('/(?i)obat/(?i)delete/(:any)', 'Obat::delete/$1');

// PEMBELIAN OBAT
$routes->get('/(?i)pembelianobat', 'PembelianObat::index');
$routes->get('/(?i)pembelianobat/(?i)apotekerlist', 'PembelianObat::apotekerlist');
$routes->get('/(?i)pembelianobat/(?i)pembelianobat/(:any)', 'PembelianObat::pembelianobat/$1');
$routes->get('/(?i)pembelianobat/(?i)pembelianobatlist', 'PembelianObat::pembelianobatlist');
$routes->post('/(?i)pembelianobat/(?i)create', 'PembelianObat::create');
$routes->delete('/(?i)pembelianobat/(?i)delete/(:any)', 'PembelianObat::delete/$1');
$routes->post('/(?i)pembelianobat/(?i)complete/(:any)', 'PembelianObat::complete/$1');

// DETAIL PEMBELIAN OBAT
$routes->get('/(?i)pembelianobat/(?i)detailpembelianobat/(:any)', 'PembelianObat::detailpembelianobat/$1');
$routes->get('/(?i)pembelianobat/(?i)detailpembelianobatlist/(:any)', 'PembelianObat::detailpembelianobatlist/$1');
$routes->get('/(?i)pembelianobat/(?i)detailpembelianobatitem/(:any)', 'PembelianObat::detailpembelianobatitem/$1');
$routes->get('/(?i)pembelianobat/(?i)obatlist/(:any)/(:any)', 'PembelianObat::obatlist/$1/$2');
$routes->post('/(?i)pembelianobat/(?i)tambahdetailpembelianobat/(:any)', 'PembelianObat::tambahdetailpembelianobat/$1');
$routes->post('/(?i)pembelianobat/(?i)perbaruidetailpembelianobat/(:any)', 'PembelianObat::perbaruidetailpembelianobat/$1');
$routes->delete('/(?i)pembelianobat/(?i)hapusdetailpembelianobat/(:any)', 'PembelianObat::hapusdetailpembelianobat/$1');
$routes->get('/(?i)pembelianobat/(?i)itemobat/(:any)', 'PembelianObat::itemobat/$1');
$routes->post('/(?i)pembelianobat/(?i)tambahitemobat/(:any)/(:any)', 'PembelianObat::tambahitemobat/$1/$2');
$routes->post('/(?i)pembelianobat/(?i)perbaruiitemobat/(:any)/(:any)', 'PembelianObat::perbaruiitemobat/$1/$2');
$routes->delete('/(?i)pembelianobat/(?i)hapusitemobat/(:any)/(:any)', 'PembelianObat::hapusitemobat/$1/$2');
$routes->get('/(?i)pembelianobat/(?i)fakturpembelianobat/(:any)', 'PembelianObat::fakturpembelianobat/$1');

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

// RESEP DOKTER
$routes->get('/(?i)resep', 'Resep::index');
$routes->get('/(?i)resep/(?i)dokterlist', 'Resep::dokterlist');
$routes->get('/(?i)resep/(?i)listresep', 'Resep::listresep');
$routes->get('/(?i)resep/(?i)pasienlist', 'Resep::pasienlist');
$routes->get('/(?i)resep/(?i)resep/(:any)', 'Resep::resep/$1');
$routes->get('/(?i)resep/(?i)print/(:any)', 'Resep::print/$1');
$routes->post('/(?i)resep/(?i)create', 'Resep::create');
$routes->post('/(?i)resep/(?i)confirm/(:any)', 'Resep::confirm/$1');
$routes->post('/(?i)resep/(?i)cancel/(:any)', 'Resep::cancel/$1');
$routes->delete('/(?i)resep/(?i)delete/(:any)', 'Resep::delete/$1');

// DETAIL RESEP DOKTER
$routes->get('/(?i)resep/(?i)detailresep/(:any)', 'Resep::detailresep/$1');
$routes->get('/(?i)resep/(?i)detailreseplist/(:any)', 'Resep::detailreseplist/$1');
$routes->get('/(?i)resep/(?i)detailresepitem/(:any)', 'Resep::detailresepitem/$1');
$routes->get('/(?i)resep/(?i)obatlist/(:any)', 'Resep::obatlist/$1');
$routes->post('/(?i)resep/(?i)tambahdetailresep/(:any)', 'Resep::tambahdetailresep/$1');
$routes->post('/(?i)resep/(?i)perbaruidetailresep/(:any)', 'Resep::perbaruidetailresep/$1');
$routes->delete('/(?i)resep/(?i)hapusdetailresep/(:any)', 'Resep::hapusdetailresep/$1');
$routes->get('/(?i)resep/(?i)keterangan/(:any)', 'Resep::keterangan/$1');
$routes->post('/(?i)resep/(?i)editketerangan/(:any)', 'Resep::editketerangan/$1');
$routes->get('/(?i)resep/(?i)etiket-dalam/(:any)', 'Resep::etiketdalam/$1');
$routes->get('/(?i)resep/(?i)etiket-luar/(:any)', 'Resep::etiketluar/$1');

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
$routes->post('/(?i)resepluar/(?i)tambahdetailresep/(:any)', 'ResepLuar::tambahdetailresep/$1');
$routes->post('/(?i)resepluar/(?i)perbaruidetailresep/(:any)', 'ResepLuar::perbaruidetailresep/$1');
$routes->delete('/(?i)resepluar/(?i)hapusdetailresep/(:any)', 'ResepLuar::hapusdetailresep/$1');
$routes->get('/(?i)resepluar/(?i)keterangan/(:any)', 'ResepLuar::keterangan/$1');
$routes->post('/(?i)resepluar/(?i)editketerangan/(:any)', 'ResepLuar::editketerangan/$1');
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

// SETTINGS
$routes->get('/(?i)settings', 'Settings::index');

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
