<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResepModel;
use App\Models\DetailResepModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanResep extends BaseController
{
    protected $DetailResepModel;
    protected $ResepModel;
    public function __construct()
    {
        $this->ResepModel = new ResepModel();
        $this->DetailResepModel = new DetailResepModel();
    }

    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $daftarDokter = $this->ResepModel->select('dokter')->where('status', 1)->groupBy('dokter')->orderBy('dokter', 'ASC')->findAll();
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Laporan Resep - ' . $this->systemName,
                'headertitle' => 'Laporan Resep',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'daftarDokter' => $daftarDokter,
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/laporanresep/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function exportdaily($tanggal)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Ambil daftar dokter dari query string
            $dokterFilter = $this->request->getGet('dokter'); // Array nama dokter

            // Jika tidak ada dokter yang dipilih, kembalikan data kosong
            if (empty($dokterFilter)) {
                return $this->response->setJSON([
                    'laporanresep' => [],
                    'tanggal' => $tanggal,
                    'total_keluar_keseluruhan' => 0,
                    'total_harga_keseluruhan' => 0,
                    'message' => 'Silakan beri kotak centang pada daftar dokter'
                ]);
            }

            // Ambil laporan resep
            $query = $this->DetailResepModel
                ->select('resep.dokter AS dokter, nama_obat, 
                SUM(detail_resep.jumlah) AS total_keluar, 
                detail_resep.harga_satuan AS harga_satuan, 
                (SUM(detail_resep.jumlah) * harga_satuan) AS total_harga')
                ->join('resep', 'resep.id_resep = detail_resep.id_resep')
                ->where('DATE(resep.tanggal_resep)', $tanggal) // Kondisi berdasarkan tanggal
                ->where('resep.status', 1); // Tambahkan kondisi status

            // Tambahkan filter dokter jika ada
            if (!empty($dokterFilter)) {
                $query->whereIn('resep.dokter', $dokterFilter);
            }

            $laporanresep = $query
                ->groupBy('resep.dokter, nama_obat, DATE(resep.tanggal_resep)')
                ->orderBy('resep.dokter', 'ASC')
                ->orderBy('nama_obat', 'ASC')
                ->findAll();

            // Hitung total keseluruhan obat keluar dan harga
            $totalKeluarKeseluruhan = array_sum(array_column($laporanresep, 'total_keluar'));
            $totalHargaKeseluruhan = array_sum(array_column($laporanresep, 'total_harga'));

            // Kirim dalam bentuk JSON
            return $this->response->setJSON([
                'laporanresep' => $laporanresep,
                'tanggal' => $tanggal,
                'total_keluar_keseluruhan' => $totalKeluarKeseluruhan,
                'total_harga_keseluruhan' => $totalHargaKeseluruhan,
                'message' => null
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function exportdailyexcel($tanggal)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Ambil daftar dokter dari query string
            $dokterFilter = $this->request->getGet('dokter'); // Array nama dokter

            // Jika tidak ada dokter yang dipilih, kirim HTTP 404
            if (empty($dokterFilter)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Ambil laporan resep
                $query = $this->DetailResepModel
                    ->select('resep.dokter AS dokter, nama_obat, 
                        SUM(detail_resep.jumlah) AS total_keluar, 
                        detail_resep.harga_satuan AS harga_satuan, 
                        (SUM(detail_resep.jumlah) * harga_satuan) AS total_harga')
                    ->join('resep', 'resep.id_resep = detail_resep.id_resep')
                    ->where('DATE(resep.tanggal_resep)', $tanggal) // Kondisi berdasarkan tanggal
                    ->where('resep.status', 1); // Tambahkan kondisi status

                // Tambahkan filter dokter jika ada
                if (!empty($dokterFilter)) {
                    $query->whereIn('resep.dokter', $dokterFilter);
                }

                $result = $query
                    ->groupBy('resep.dokter, nama_obat, DATE(resep.tanggal_resep)')
                    ->orderBy('resep.dokter', 'ASC')
                    ->orderBy('nama_obat', 'ASC')
                    ->findAll();

                // Hitung total keseluruhan obat keluar dan harga
                $totalKeluarKeseluruhan = array_sum(array_column($result, 'total_keluar'));
                $totalHargaKeseluruhan = array_sum(array_column($result, 'total_harga'));
            }

            // Memeriksa apakah detail pembelian obat kosong
            if (empty($result)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Membuat nama file berdasarkan tanggal pembelian
                $filename = $tanggal . '-resep';
                $tanggalinit = new DateTime($tanggal);
                // Buat formatter untuk tanggal dan waktu
                $formatter = new IntlDateFormatter(
                    'id_ID', // Locale untuk bahasa Indonesia
                    IntlDateFormatter::LONG, // Format untuk tanggal
                    IntlDateFormatter::NONE, // Tidak ada waktu
                    'Asia/Jakarta', // Timezone
                    IntlDateFormatter::GREGORIAN, // Calendar
                    'EEEE, d MMMM yyyy' // Format tanggal lengkap dengan nama hari
                );

                // Format tanggal
                $tanggalFormat = $formatter->format($tanggalinit);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Menambahkan informasi header di spreadsheet
                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'LAPORAN RESEP HARIAN');

                // Path gambar yang ingin ditambahkan
                $gambarPath = FCPATH . 'assets/images/logo_pec.png'; // Ganti dengan path gambar Anda

                // Membuat objek Drawing
                $drawing = new Drawing();
                $drawing->setName('Logo PEC-TK'); // Nama gambar
                $drawing->setDescription('Logo PEC-TK'); // Deskripsi gambar
                $drawing->setPath($gambarPath); // Path ke gambar
                $drawing->setCoordinates('A1'); // Koordinat sel tempat gambar akan ditambahkan
                $drawing->setHeight(38); // Tinggi gambar dalam piksel (opsional)
                $drawing->setWorksheet($sheet); // Menambahkan gambar ke worksheet

                // Menambahkan informasi tanggal dan supplier
                $sheet->setCellValue('A4', 'Hari dan Tanggal:');
                $sheet->setCellValue('C4', $tanggalFormat);

                // Menambahkan header tabel detail laporan resep
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Dokter');
                $sheet->setCellValue('C5', 'Nama Obat');
                $sheet->setCellValue('D5', 'Harga Satuan');
                $sheet->setCellValue('E5', 'Obat Keluar');
                $sheet->setCellValue('F5', 'Total Harga');

                // Mengatur tata letak dan gaya untuk header
                $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:F2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

                $column = 6; // Baris awal data
                $nomor = 1;  // Nomor urut resep

                foreach ($result as $list) {
                    // Isi data resep
                    $sheet->setCellValue('A' . $column, $nomor++);
                    $sheet->setCellValue('B' . $column, $list['dokter']);
                    $sheet->setCellValue('C' . $column, $list['nama_obat']);
                    $sheet->getStyle('D' . $column)->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('D' . $column, $list['harga_satuan']);
                    $sheet->setCellValue('E' . $column, $list['total_keluar']);
                    $sheet->getStyle('F' . $column)->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('F' . $column, $list['total_harga']);

                    // Atur nomor ke rata tengah
                    $sheet->getStyle("A{$column}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Tambahkan baris pemisah antar transaksi
                    $column++;
                }

                // Menambahkan total keseluruhan di bawah tabel
                $sheet->setCellValue('A' . ($column), 'Total Keseluruhan');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->setCellValue('E' . ($column), $totalKeluarKeseluruhan);
                $sheet->getStyle('F' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                $sheet->setCellValue('F' . ($column), $totalHargaKeseluruhan);

                // Mengatur gaya teks untuk header dan total
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setSize(8);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setSize(12);
                $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // Mengatur gaya font untuk header dan total
                $sheet->getStyle('A1:A4')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:F5')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:F5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':F' . ($column))->getFont()->setBold(TRUE);

                // Menambahkan border untuk header dan tabel
                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:F2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A5:F' . ($column))->applyFromArray($tableBorder);
                $sheet->getStyle('A5:F' . ($column))->getAlignment()->setWrapText(true);
                $sheet->getStyle('A6:F' . ($column + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(30, 'px');
                $sheet->getColumnDimension('B')->setWidth(275, 'px');
                $sheet->getColumnDimension('C')->setWidth(275, 'px');
                $sheet->getColumnDimension('D')->setWidth(125, 'px');
                $sheet->getColumnDimension('E')->setWidth(75, 'px');
                $sheet->getColumnDimension('F')->setWidth(125, 'px');

                // Menyimpan file spreadsheet dan mengirimkan ke browser
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function exportmonthly($bulan)
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Ambil daftar dokter dari query string
            $dokterFilter = $this->request->getGet('dokter'); // Array nama dokter

            // Jika tidak ada dokter yang dipilih, kembalikan data kosong
            if (empty($dokterFilter)) {
                return $this->response->setJSON([
                    'laporanresep' => [],
                    'bulan' => $bulan,
                    'total_keluar_keseluruhan' => 0,
                    'total_harga_keseluruhan' => 0,
                    'message' => 'Silakan beri kotak centang pada daftar dokter'
                ]);
            }

            // Ambil laporan resep
            $query = $this->DetailResepModel
                ->select('MONTH(resep.tanggal_resep) AS bulan,
                resep.dokter AS dokter, 
                nama_obat, 
                SUM(detail_resep.jumlah) AS total_keluar, 
                detail_resep.harga_satuan AS harga_satuan, 
                (SUM(detail_resep.jumlah) * harga_satuan) AS total_harga')
                ->join('resep', 'resep.id_resep = detail_resep.id_resep')
                ->where('YEAR(resep.tanggal_resep)', date('Y', strtotime($bulan)))
                ->where('MONTH(resep.tanggal_resep)', date('m', strtotime($bulan)))
                ->where('resep.status', 1); // Tambahkan kondisi status

            // Tambahkan filter dokter jika ada
            if (!empty($dokterFilter)) {
                $query->whereIn('resep.dokter', $dokterFilter);
            }

            $laporanresep = $query
                ->groupBy('MONTH(resep.tanggal_resep), resep.dokter, nama_obat')
                ->orderBy('bulan', 'ASC') // Urutkan berdasarkan bulan ASC
                ->orderBy('resep.dokter', 'ASC') // Urutkan berdasarkan dokter ASC
                ->orderBy('nama_obat', 'ASC') // Urutkan berdasarkan nama_obat ASC
                ->findAll();

            // Hitung total keseluruhan obat keluar dan harga
            $totalKeluarKeseluruhan = array_sum(array_column($laporanresep, 'total_keluar'));
            $totalHargaKeseluruhan = array_sum(array_column($laporanresep, 'total_harga'));

            // Kirim dalam bentuk JSON
            return $this->response->setJSON([
                'laporanresep' => $laporanresep,
                'bulan' => $bulan,
                'total_keluar_keseluruhan' => $totalKeluarKeseluruhan,
                'total_harga_keseluruhan' => $totalHargaKeseluruhan,
                'message' => null
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function exportmonthlyexcel($bulan)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Ambil daftar dokter dari query string
            $dokterFilter = $this->request->getGet('dokter'); // Array nama dokter

            // Jika tidak ada dokter yang dipilih, kirim HTTP 404
            if (empty($dokterFilter)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Ambil laporan resep
                $query = $this->DetailResepModel
                    ->select('MONTH(resep.tanggal_resep) AS bulan,
                        resep.dokter AS dokter, 
                        nama_obat, 
                        SUM(detail_resep.jumlah) AS total_keluar, 
                        detail_resep.harga_satuan AS harga_satuan, 
                        (SUM(detail_resep.jumlah) * harga_satuan) AS total_harga')
                    ->join('resep', 'resep.id_resep = detail_resep.id_resep')
                    ->where('YEAR(resep.tanggal_resep)', date('Y', strtotime($bulan)))
                    ->where('MONTH(resep.tanggal_resep)', date('m', strtotime($bulan)))
                    ->where('resep.status', 1); // Tambahkan kondisi status

                // Tambahkan filter dokter jika ada
                if (!empty($dokterFilter)) {
                    $query->whereIn('resep.dokter', $dokterFilter);
                }

                $result = $query
                    ->groupBy('MONTH(resep.tanggal_resep), resep.dokter, nama_obat')
                    ->orderBy('bulan', 'ASC') // Urutkan berdasarkan bulan ASC
                    ->orderBy('resep.dokter', 'ASC') // Urutkan berdasarkan dokter ASC
                    ->orderBy('nama_obat', 'ASC') // Urutkan berdasarkan nama_obat ASC
                    ->findAll();

                // Hitung total keseluruhan obat keluar dan harga
                $totalKeluarKeseluruhan = array_sum(array_column($result, 'total_keluar'));
                $totalHargaKeseluruhan = array_sum(array_column($result, 'total_harga'));
            }

            // Memeriksa apakah detail pembelian obat kosong
            if (empty($result)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Membuat nama file berdasarkan tanggal pembelian
                $filename = $bulan . '-resep';
                $bulaninit = new DateTime($bulan);
                // Buat formatter untuk tanggal dan waktu
                $formatter = new IntlDateFormatter(
                    'id_ID', // Locale untuk bahasa Indonesia
                    IntlDateFormatter::LONG, // Format untuk tanggal
                    IntlDateFormatter::NONE, // Tidak ada waktu
                    'Asia/Jakarta', // Timezone
                    IntlDateFormatter::GREGORIAN, // Calendar
                    'MMMM yyyy' // Format tanggal lengkap dengan nama hari
                );

                // Format tanggal
                $bulanFormat = $formatter->format($bulaninit);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Menambahkan informasi header di spreadsheet
                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'LAPORAN RESEP BULANAN');

                // Path gambar yang ingin ditambahkan
                $gambarPath = FCPATH . 'assets/images/logo_pec.png'; // Ganti dengan path gambar Anda

                // Membuat objek Drawing
                $drawing = new Drawing();
                $drawing->setName('Logo PEC-TK'); // Nama gambar
                $drawing->setDescription('Logo PEC-TK'); // Deskripsi gambar
                $drawing->setPath($gambarPath); // Path ke gambar
                $drawing->setCoordinates('A1'); // Koordinat sel tempat gambar akan ditambahkan
                $drawing->setHeight(38); // Tinggi gambar dalam piksel (opsional)
                $drawing->setWorksheet($sheet); // Menambahkan gambar ke worksheet

                // Menambahkan informasi tanggal dan supplier
                $sheet->setCellValue('A4', 'Bulan:');
                $sheet->setCellValue('C4', $bulanFormat);

                // Menambahkan header tabel detail laporan resep
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Dokter');
                $sheet->setCellValue('C5', 'Nama Obat');
                $sheet->setCellValue('D5', 'Harga Satuan');
                $sheet->setCellValue('E5', 'Obat Keluar');
                $sheet->setCellValue('F5', 'Total Harga');

                // Mengatur tata letak dan gaya untuk header
                $spreadsheet->getActiveSheet()->mergeCells('A1:F1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:F2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

                $column = 6; // Baris awal data
                $nomor = 1;  // Nomor urut resep

                foreach ($result as $list) {
                    // Isi data resep
                    $sheet->setCellValue('A' . $column, $nomor++);
                    $sheet->setCellValue('B' . $column, $list['dokter']);
                    $sheet->setCellValue('C' . $column, $list['nama_obat']);
                    $sheet->getStyle('D' . $column)->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('D' . $column, $list['harga_satuan']);
                    $sheet->setCellValue('E' . $column, $list['total_keluar']);
                    $sheet->getStyle('F' . $column)->getNumberFormat()->setFormatCode(
                        '_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * "-"_-;_-@_-'
                    );
                    $sheet->setCellValue('F' . $column, $list['total_harga']);

                    // Atur nomor ke rata tengah
                    $sheet->getStyle("A{$column}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Tambahkan baris pemisah antar transaksi
                    $column++;
                }

                // Menambahkan total keseluruhan di bawah tabel
                $sheet->setCellValue('A' . ($column), 'Total Keseluruhan');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->setCellValue('E' . ($column), $totalKeluarKeseluruhan);
                $sheet->getStyle('F' . ($column))->getNumberFormat()->setFormatCode('_\Rp * #,##0_-;[Red]_\Rp * -#,##0_-;_-_\Rp * \"-\"_-;_-@_-');
                $sheet->setCellValue('F' . ($column), $totalHargaKeseluruhan);

                // Mengatur gaya teks untuk header dan total
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setSize(8);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setSize(12);
                $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // Mengatur gaya font untuk header dan total
                $sheet->getStyle('A1:A4')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:F5')->getFont()->setBold(TRUE);
                $sheet->getStyle('A5:F5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':F' . ($column))->getFont()->setBold(TRUE);

                // Menambahkan border untuk header dan tabel
                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:F2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A5:F' . ($column))->applyFromArray($tableBorder);
                $sheet->getStyle('A5:F' . ($column))->getAlignment()->setWrapText(true);
                $sheet->getStyle('A6:F' . ($column + 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(30, 'px');
                $sheet->getColumnDimension('B')->setWidth(275, 'px');
                $sheet->getColumnDimension('C')->setWidth(275, 'px');
                $sheet->getColumnDimension('D')->setWidth(125, 'px');
                $sheet->getColumnDimension('E')->setWidth(75, 'px');
                $sheet->getColumnDimension('F')->setWidth(125, 'px');

                // Menyimpan file spreadsheet dan mengirimkan ke browser
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                exit();
            }
        } else {
            // Menghasilkan exception jika peran tidak diizinkan
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
