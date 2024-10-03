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
$routes->post('/(?i)pembelianobat/(?i)tambahitemobat/(:any)', 'PembelianObat::tambahitemobat/$1');
$routes->post('/(?i)pembelianobat/(?i)perbaruiitemobat/(:any)/(:any)', 'PembelianObat::perbaruiitemobat/$1/$2');
$routes->delete('/(?i)pembelianobat/(?i)hapusitemobat/(:any)', 'PembelianObat::hapusitemobat/$1');
$routes->get('/(?i)pembelianobat/(?i)fakturpembelianobat/(:any)', 'PembelianObat::fakturpembelianobat/$1');

// RESEP
$routes->get('/(?i)resep', 'Resep::index');
$routes->get('/(?i)resep/(?i)listresep', 'Resep::listresep');
$routes->get('/(?i)resep/(?i)pasienlist', 'Resep::pasienlist');
$routes->get('/(?i)resep/(?i)resep/(:any)', 'Resep::resep/$1');
$routes->post('/(?i)resep/(?i)create', 'Resep::create');
$routes->delete('/(?i)resep/(?i)delete/(:any)', 'Resep::delete/$1');

// DETAIL RESEP
$routes->get('/(?i)resep/(?i)detailresep/(:any)', 'Resep::detailresep/$1');
$routes->get('/(?i)resep/(?i)detailreseplist/(:any)', 'Resep::detailreseplist/$1');
$routes->get('/(?i)resep/(?i)detailresepitem/(:any)', 'Resep::detailresepitem/$1');
$routes->get('/(?i)resep/(?i)obatlist/(:any)', 'Resep::obatlist/$1');
$routes->post('/(?i)resep/(?i)tambahdetailresep/(:any)', 'Resep::tambahdetailresep/$1');
$routes->post('/(?i)resep/(?i)perbaruidetailresep/(:any)', 'Resep::perbaruidetailresep/$1');
$routes->delete('/(?i)resep/(?i)hapusdetailresep/(:any)', 'Resep::hapusdetailresep/$1');
$routes->get('/(?i)resep/(?i)keterangan/(:any)', 'Resep::keterangan/$1');
$routes->post('/(?i)resep/(?i)editketerangan/(:any)', 'Resep::editketerangan/$1');
$routes->get('/(?i)transaksi/(?i)etiket/(:any)', 'Transaksi::etiket/$1');

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
$routes->get('/(?i)transaksi/(?i)transaksi/(:any)', 'Transaksi::transaksi/$1');
$routes->get('/(?i)transaksi/(?i)struk/(:any)', 'Transaksi::struk/$1');
$routes->post('/(?i)transaksi/(?i)create', 'Transaksi::create');
$routes->post('/(?i)transaksi/(?i)update', 'Transaksi::update');
$routes->post('/(?i)transaksi/(?i)process/(:any)', 'Transaksi::process/$1');
$routes->delete('/(?i)transaksi/(?i)delete/(:any)', 'Transaksi::delete/$1');

// DETAIL TRANSAKSI
$routes->get('/(?i)transaksi/(?i)detailtransaksi/(:any)', 'Transaksi::detailtransaksi/$1');
$routes->get('/(?i)transaksi/(?i)detaillayananlist/(:any)', 'Transaksi::detaillayananlist/$1');
$routes->get('/(?i)transaksi/(?i)detailobatalkeslist/(:any)', 'Transaksi::detailobatalkeslist/$1');
$routes->get('/(?i)transaksi/(?i)detailtransaksiitem/(:any)', 'Transaksi::detailtransaksiitem/$1');
$routes->get('/(?i)transaksi/(?i)layananlist/(:any)/(:any)', 'Transaksi::layananlist/$1/$2');
$routes->get('/(?i)transaksi/(?i)reseplist/(:any)/(:any)', 'Transaksi::reseplist/$1/$2');
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

// CHANGE USER INFORMATION
$routes->get('/(?i)settings/(?i)edit', 'Settings::edit');
$routes->post('/(?i)settings/(?i)update', 'Settings::update');

// CHANGE PASSWORD
$routes->get('/(?i)settings/(?i)changepassword', 'ChangePassword::index');
$routes->post('/(?i)settings/(?i)changepassword/(?i)update', 'ChangePassword::update');

// ABOUT SYSTEM
$routes->get('/(?i)settings/(?i)about', 'Settings::about');
