<?php

namespace App\Controllers;

use App\Models\PembelianObatModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianObatModel;
use App\Models\ObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\I18n\Time;

class PembelianObat extends BaseController
{
    protected $PembelianObatModel;
    protected $DetailPembelianObatModel;
    public function __construct()
    {
        $this->PembelianObatModel = new PembelianObatModel();
        $this->DetailPembelianObatModel = new DetailPembelianObatModel();
    }

    public function index()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = [
                'title' => 'Pembelian Obat - ' . $this->systemName,
                'headertitle' => 'Pembelian Obat',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pembelian_obat/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pembelianobatlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');
            $status = $this->request->getGet('status');

            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            $PembelianObatModel = $this->PembelianObatModel;

            // Join tables before applying search filter
            $PembelianObatModel
                ->select('pembelian_obat.*, 
            supplier.nama_supplier as supplier_nama_supplier, 
            user.fullname as user_fullname, 
            user.username as user_username')
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner');

            // Apply status filter if provided
            if ($status === '1') {
                $PembelianObatModel->where('diterima', 1);
            } elseif ($status === '0') {
                $PembelianObatModel->where('diterima', 0);
            }

            // Apply search filter on supplier name or purchase date
            if ($search) {
                $PembelianObatModel
                    ->groupStart()
                    ->like('supplier.nama_supplier', $search)
                    ->orLike('user.fullname', $search)
                    ->orLike('user.username', $search)
                    ->orLike('tgl_pembelian', $search)
                    ->groupEnd();
            }

            // Count total results
            $total = $PembelianObatModel->countAllResults(false);

            // Get paginated results
            $PembelianObat = $PembelianObatModel
                ->orderBy('tgl_pembelian', 'DESC')
                ->findAll($limit, $offset);

            // Calculate the starting number for the current page
            $startNumber = $offset + 1;

            $dataPembelianObat = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $PembelianObat, array_keys($PembelianObat));

            return $this->response->setJSON([
                'pembelian_obat' => $dataPembelianObat,
                'total' => $total
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function supplierlist()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $SupplierModel = new SupplierModel();

            $results = $SupplierModel->orderBy('nama_supplier', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $options[] = [
                    'value' => $row['id_supplier'],
                    'text' => $row['nama_supplier']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function pembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
                ->find($id);
            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_supplier' => 'required',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            // Save Data
            $data = [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_user' => session()->get('id_user'),
                'tgl_pembelian' => date('Y-m-d H:i:s'),
                'total_qty' => 0,
                'total_biaya' => 0,
                'diterima' => 0,
            ];
            $this->PembelianObatModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pembelian berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
            $this->PembelianObatModel->delete($id);
            $db->query('ALTER TABLE `pembelian_obat` auto_increment = 1');
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');
            return $this->response->setJSON(['message' => 'Obat berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function complete($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
            $db->table('pembelian_obat')
                ->set('diterima', 1)
                ->where('id_pembelian_obat', $id)
                ->update();

            $details = $db->table('detail_pembelian_obat')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getResultArray();

            // Update jumlah_masuk di tabel obat untuk setiap id_obat di detail_pembelian_obat
            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $jumlah_masuk = $detail['jumlah'];

                // Update jumlah_masuk di tabel obat
                $db->table('obat')
                    ->set('jumlah_masuk', "jumlah_masuk + $jumlah_masuk", false) // false untuk menghindari quoting otomatis
                    ->set('updated_at', date('Y-m-d H:i:s'))
                    ->where('id_obat', $id_obat)
                    ->update();
            }
            return $this->response->setJSON(['message' => 'Obat sudah diterima. Periksa jumlah masuk di menu obat.']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function cancel($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();
            $details = $db->table('detail_pembelian_obat')
                ->where('id_pembelian_obat', $id)
                ->get()
                ->getResultArray();

            foreach ($details as $detail) {
                $id_obat = $detail['id_obat'];
                $jumlah_masuk = $detail['jumlah'];
                $db->table('obat')
                    ->set('jumlah_masuk', "jumlah_masuk - $jumlah_masuk", false) // false untuk menghindari quoting otomatis
                    ->set('updated_at', date('Y-m-d H:i:s'))
                    ->where('id_obat', $id_obat)
                    ->update();
            }

            $db->table('pembelian_obat')
                ->set('diterima', 0)
                ->where('id_pembelian_obat', $id)
                ->update();

            return $this->response->setJSON(['message' => 'Pembelian obat telah dibatalkan. Jumlah masuk obat telah dikurangi.']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL PEMBELIAN OBAT
    public function detailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $pembelianobat = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
                ->find($id);
            $data = [
                'pembelianobat' => $pembelianobat,
                'title' => 'Detail Pembelian Obat dengan ID ' . $id . ' - ' . $this->systemName,
                'headertitle' => 'Detail Pembelian Obat',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pembelian_obat/details', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function detailpembelianobatlist($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->findAll();

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function detailpembelianobatitem($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $data = $this->DetailPembelianObatModel
                ->where('id_detail_pembelian_obat', $id)
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->find($id);

            return $this->response->setJSON($data);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function obatlist($id_supplier, $id_pembelian_obat)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $ObatModel = new ObatModel();
            $DetailPembelianObatModel = new DetailPembelianObatModel(); // Model untuk tabel detail_pembelian_obat

            // Ambil semua obat berdasarkan id_supplier
            $results = $ObatModel->where('id_supplier', $id_supplier)->orderBy('nama_obat', 'DESC')->findAll();

            $options = [];
            foreach ($results as $row) {
                $harga_obat = (int) $row['harga_obat'];
                $harga_obat_terformat = number_format($harga_obat, 0, ',', '.');
                // Cek apakah id_obat sudah ada di tabel detail_pembelian_obat dengan id_pembelian_obat yang sama
                $isUsed = $DetailPembelianObatModel->where('id_obat', $row['id_obat'])
                    ->where('id_pembelian_obat', $id_pembelian_obat) // Pastikan sesuai dengan id_pembelian_obat yang sedang digunakan
                    ->first();

                // Jika belum ada pada pembelian yang sama, tambahkan ke options
                if (!$isUsed) {
                    $options[] = [
                        'value' => $row['id_obat'],
                        'text' => $row['nama_obat'] . ' (' . $row['kategori_obat'] . ' • ' . $row['bentuk_obat'] . ' • Rp' . $harga_obat_terformat . ' • ' . $row['dosis_kali'] . ' × ' . $row['dosis_hari'] . ' hari • ' . $row['cara_pakai'] . ')'
                    ];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $options,
            ]);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function tambahdetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'id_obat' => 'required',
                'jumlah' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $ObatModel = new ObatModel();
            $obat = $ObatModel->find($this->request->getPost('id_obat'));

            // Save Data
            $data = [
                'id_pembelian_obat' => $id,
                'id_obat' => $this->request->getPost('id_obat'),
                'no_batch' => '',
                'expired' => NULL,
                'jumlah' => $this->request->getPost('jumlah'),
                'harga_satuan' => $obat['harga_obat'],
            ];
            $this->DetailPembelianObatModel->save($data);

            $db = db_connect();

            // Calculate total_qty and total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil ditambahkan']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function perbaruidetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Validate
            $validation = \Config\Services::validation();
            // Set base validation rules
            $validation->setRules([
                'jumlah_edit' => 'required|numeric|greater_than[0]',
            ]);

            if (!$this->validate($validation->getRules())) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $detail_pembelian_obat = $this->DetailPembelianObatModel->find($this->request->getPost('id_detail_pembelian_obat'));

            // Save Data
            $data = [
                'id_detail_pembelian_obat' => $this->request->getPost('id_detail_pembelian_obat'),
                'id_pembelian_obat' => $id,
                'id_obat' => $detail_pembelian_obat['id_obat'],
                'no_batch' => $detail_pembelian_obat['no_batch'],
                'expired' => $detail_pembelian_obat['expired'],
                'jumlah' => $this->request->getPost('jumlah_edit'),
                'harga_satuan' => $detail_pembelian_obat['harga_satuan'],
            ];
            $this->DetailPembelianObatModel->save($data);

            $db = db_connect();

            // Calculate total_qty and total_biaya
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id);
            $result = $builder->get()->getRow();

            $total_qty = $result->total_qty;
            $total_biaya = $result->total_biaya;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Item pembelian berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function hapusdetailpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Find the detail pembelian obat before deletion to get id_pembelian_obat
            $detail = $this->DetailPembelianObatModel->find($id);

            $id_pembelian_obat = $detail['id_pembelian_obat'];

            // Delete the detail pembelian obat
            $this->DetailPembelianObatModel->delete($id);

            // Reset auto_increment
            $db->query('ALTER TABLE `detail_pembelian_obat` auto_increment = 1');

            // Recalculate total_qty and total_biaya after deletion
            $builder = $db->table('detail_pembelian_obat');
            $builder->select('SUM(jumlah) as total_qty, SUM(jumlah * harga_satuan) as total_biaya');
            $builder->where('id_pembelian_obat', $id_pembelian_obat);
            $result = $builder->get()->getRow();

            $total_qty = $result->total_qty ?? 0; // Handle case when no rows are left
            $total_biaya = $result->total_biaya ?? 0;

            // Update pembelian_obat table
            $pembelianObatBuilder = $db->table('pembelian_obat');
            $pembelianObatBuilder->where('id_pembelian_obat', $id_pembelian_obat);
            $pembelianObatBuilder->update([
                'total_qty' => $total_qty,
                'total_biaya' => $total_biaya,
            ]);

            return $this->response->setJSON(['message' => 'Item pembelian berhasil dihapus']);
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function fakturpembelianobat($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $pembelianobat = $this->PembelianObatModel
                ->join('supplier', 'supplier.id_supplier = pembelian_obat.id_supplier', 'inner')
                ->join('user', 'user.id_user = pembelian_obat.id_user', 'inner')
                ->find($id);
            $detailpembelianobat = $this->DetailPembelianObatModel
                ->where('detail_pembelian_obat.id_pembelian_obat', $id)
                ->join('pembelian_obat', 'pembelian_obat.id_pembelian_obat = detail_pembelian_obat.id_pembelian_obat', 'inner')
                ->join('obat', 'obat.id_obat = detail_pembelian_obat.id_obat', 'inner')
                ->orderBy('id_detail_pembelian_obat', 'ASC')
                ->findAll();
            if (empty($detailpembelianobat)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                $filename = $pembelianobat['tgl_pembelian'] . '-pembelian-obat';
                $tanggal = Time::parse($pembelianobat['tgl_pembelian']);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'FAKTUR PEMBELIAN OBAT');

                $sheet->setCellValue('A4', 'Hari/Tanggal:');
                $sheet->setCellValue('C4', $tanggal->toLocalizedString('d MMMM yyyy HH.mm.ss'));
                $sheet->setCellValue('A5', 'Nama Supplier:');
                $sheet->setCellValue('C5', $pembelianobat['nama_supplier']);
                $sheet->setCellValue('A6', 'Alamat Supplier:');
                $sheet->setCellValue('C6', $pembelianobat['alamat_supplier']);
                $sheet->setCellValue('A7', 'Nomor Telepon Supplier:');
                $sheet->setCellValue('C7', $pembelianobat['kontak_supplier']);
                $sheet->setCellValue('A8', 'Apoteker:');
                $sheet->setCellValue('C8', $pembelianobat['fullname']);
                $sheet->setCellValue('A9', 'ID Pembelian:');
                $sheet->setCellValue('C9', $pembelianobat['id_pembelian_obat']);

                $sheet->setCellValue('A10', 'No.');
                $sheet->setCellValue('B10', 'Nama Obat');
                $sheet->setCellValue('C10', 'Kategori Obat');
                $sheet->setCellValue('D10', 'Bentuk Obat');
                $sheet->setCellValue('E10', 'Dosis');
                $sheet->setCellValue('F10', 'Cara Pakai');
                $sheet->setCellValue('G10', 'Harga Satuan');
                $sheet->setCellValue('H10', 'Qty');
                $sheet->setCellValue('I10', 'Total Harga');

                $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:I3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                $column = 11;
                foreach ($detailpembelianobat as $list) {
                    $sheet->setCellValue('A' . $column, ($column - 10));
                    $sheet->setCellValue('B' . $column, $list['nama_obat']);
                    $sheet->setCellValue('C' . $column, $list['kategori_obat']);
                    $sheet->setCellValue('D' . $column, $list['bentuk_obat']);
                    $sheet->setCellValue('E' . $column, $list['dosis_kali'] . '×' . $list['dosis_hari'] . ' hari');
                    $sheet->setCellValue('F' . $column, $list['cara_pakai']);
                    $sheet->setCellValue('G' . $column, $list['harga_satuan']);
                    $sheet->getStyle('G' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    $sheet->setCellValue('H' . $column, $list['jumlah']);
                    $total = $list['harga_satuan'] * $list['jumlah'];
                    $sheet->setCellValue('I' . $column, $total);
                    $sheet->getStyle('I' . $column)->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                    $sheet->getStyle('B' . $column . ':I' . $column)->getAlignment()->setWrapText(true);
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'Total Pembelian');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':G' . ($column));
                $sheet->setCellValue('H' . ($column), $pembelianobat['total_qty']);
                $sheet->setCellValue('I' . ($column), $pembelianobat['total_biaya']);
                $sheet->getStyle('I' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');

                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:C9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A10:I10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('A1:A9')->getFont()->setBold(TRUE);
                $sheet->getStyle('A10:I10')->getFont()->setBold(TRUE);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(TRUE);

                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:I2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A10:I' . ($column))->applyFromArray($tableBorder);

                $sheet->getColumnDimension('A')->setWidth(50, 'px');
                $sheet->getColumnDimension('B')->setWidth(210, 'px');
                $sheet->getColumnDimension('C')->setWidth(120, 'px');
                $sheet->getColumnDimension('D')->setWidth(120, 'px');
                $sheet->getColumnDimension('E')->setWidth(120, 'px');
                $sheet->getColumnDimension('F')->setWidth(120, 'px');
                $sheet->getColumnDimension('G')->setWidth(180, 'px');
                $sheet->getColumnDimension('H')->setWidth(50, 'px');
                $sheet->getColumnDimension('I')->setWidth(180, 'px');

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
