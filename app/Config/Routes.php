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
$routes->get('/(?i)pasien/(?i)pasienapi', 'Pasien::pasienapi');

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
$routes->post('/(?i)resep/(?i)create', 'Resep::create');
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
