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

// DOKTER
$routes->get('/(?i)dokter', 'Dokter::index');
$routes->post('/(?i)dokter/(?i)dokterlist', 'Dokter::dokterlist');
$routes->get('/(?i)dokter/(?i)dokter/(:any)', 'Dokter::dokter/$1');
$routes->post('/(?i)dokter/(?i)create', 'Dokter::create');
$routes->post('/(?i)dokter/(?i)update', 'Dokter::update');
$routes->delete('/(?i)dokter/(?i)delete/(:any)', 'Dokter::delete/$1');

// PASIEN
$routes->get('/(?i)pasien', 'Pasien::index');
$routes->post('/(?i)pasien/(?i)pasienlist', 'Pasien::pasienlist');
$routes->get('/(?i)pasien/(?i)dokterlist', 'Pasien::dokterlist');
$routes->get('/(?i)pasien/(?i)pasien/(:any)', 'Pasien::pasien/$1');
$routes->post('/(?i)pasien/(?i)create', 'Pasien::create');
$routes->post('/(?i)pasien/(?i)update', 'Pasien::update');
$routes->delete('/(?i)pasien/(?i)delete/(:any)', 'Pasien::delete/$1');

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
$routes->get('/(?i)pembelianobat/(?i)pembelianobatlist', 'PembelianObat::pembelianobatlist');
$routes->post('/(?i)pembelianobat/(?i)create', 'PembelianObat::create');
$routes->delete('/(?i)pembelianobat/(?i)delete/(:any)', 'PembelianObat::delete/$1');

// DETAIL PEMBELIAN OBAT
$routes->get('/(?i)pembelianobat/(?i)detailpembelianobat/(:any)', 'PembelianObat::detailpembelianobat/$1');
$routes->get('/(?i)pembelianobat/(?i)detailpembelianobatlist/(:any)', 'PembelianObat::detailpembelianobatlist/$1');
$routes->post('/(?i)pembelianobat/(?i)tambahdetailpembelianobat/(:any)', 'PembelianObat::tambahdetailpembelianobat/$1');
$routes->post('/(?i)pembelianobat/(?i)perbaruidetailpembelianobat/(:any)', 'PembelianObat::perbaruidetailpembelianobat/$1');
$routes->delete('/(?i)pembelianobat/(?i)hapusdetailpembelianobat/(:any)', 'PembelianObat::hapusdetailpembelianobat/$1');

// RESEP
$routes->get('/(?i)resep', 'Resep::index');
$routes->post('/(?i)resep/(?i)listresep', 'Resep::listresep');
$routes->get('/(?i)resep/(?i)resep/(:any)', 'Resep::resep/$1');
$routes->post('/(?i)resep/(?i)create', 'Resep::create');
$routes->post('/(?i)resep/(?i)update', 'Resep::update');
$routes->delete('/(?i)resep/(?i)delete/(:any)', 'Resep::delete/$1');

// DETAIL RESEP
$routes->get('/(?i)resep/(?i)detailreseplist/(:any)', 'Resep::detailreseplist/$1');
$routes->get('/(?i)resep/(?i)detailresep/(:any)', 'Resep::detailresep/$1');
$routes->post('/(?i)resep/(?i)tambahdetailresep/(:any)', 'Resep::tambahdetailresep/$1');
$routes->post('/(?i)resep/(?i)perbaruidetailresep/(:any)', 'Resep::perbaruidetailresep/$1');
$routes->delete('/(?i)resep/(?i)hapusdetailresep/(:any)', 'Resep::hapusdetailresep/$1');

// TRANSAKSI
$routes->get('/(?i)transaksi', 'Transaksi::index');
$routes->post('/(?i)transaksi/(?i)listtransaksi', 'Transaksi::listtransaksi');
$routes->get('/(?i)transaksi/(?i)transaksi/(:any)', 'Transaksi::transaksi/$1');
$routes->post('/(?i)transaksi/(?i)create', 'Transaksi::create');
$routes->post('/(?i)transaksi/(?i)update', 'Transaksi::update');
$routes->post('/(?i)transaksi/(?i)process', 'Transaksi::process');
$routes->delete('/(?i)transaksi/(?i)delete/(:any)', 'Transaksi::delete/$1');

// DETAIL TRANSAKSI
$routes->get('/(?i)transaksi/(?i)detailtransaksilist/(:any)', 'PembelianObat::detailtransaksilist/$1');
$routes->get('/(?i)transaksi/(?i)detailtransaksi/(:any)', 'PembelianObat::detailtransaksi/$1');
$routes->post('/(?i)transaksi/(?i)tambahdetailtransaksi/(:any)', 'PembelianObat::tambahdetailtransaksi/$1');
$routes->post('/(?i)transaksi/(?i)perbaruidetailtransaksi/(:any)', 'PembelianObat::perbaruidetailtransaksi/$1');
$routes->delete('/(?i)transaksi/(?i)hapusdetailtransaksi/(:any)', 'PembelianObat::hapusdetailtransaksi/$1');

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

// CHANGE USER INFORMATION
$routes->get('/(?i)settings/(?i)edit', 'Settings::edit');
$routes->post('/(?i)settings/(?i)update', 'Settings::update');

// CHANGE PASSWORD
$routes->get('/(?i)settings/(?i)changepassword', 'ChangePassword::index');
$routes->post('/(?i)settings/(?i)changepassword/(?i)update', 'ChangePassword::update');

// ABOUT SYSTEM
$routes->get('/(?i)settings/(?i)about', 'Settings::about');
