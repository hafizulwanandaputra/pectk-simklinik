<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ObatModel;
use App\Models\OpnameObatModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OpnameObat extends BaseController
{
    protected $ObatModel;
    protected $OpnameObatModel;
    public function __construct()
    {
        $this->ObatModel = new ObatModel();
        $this->OpnameObatModel = new OpnameObatModel();
    }
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' dan 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Laporan Stok Obat - ' . $this->systemName,
                'headertitle' => 'Laporan Stok Obat',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman opnameobat
            return view('dashboard/opnameobat/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function opnameobatlist()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil parameter pencarian, limit, offset, dan status dari query string
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit');
            $offset = $this->request->getGet('offset');

            // Menentukan limit dan offset
            $limit = $limit ? intval($limit) : 0;
            $offset = $offset ? intval($offset) : 0;

            // Memuat model PembelianObat
            $OpnameObatModel = $this->OpnameObatModel;

            // Menerapkan filter pencarian pada nama supplier atau tanggal pembelian
            if ($search) {
                $OpnameObatModel
                    ->like('tanggal', $search)
                    ->orLike('apoteker', $search);
            }

            // Menghitung total hasil
            $total = $OpnameObatModel->countAllResults(false);

            // Mendapatkan hasil yang dipaginasikan
            $OpnameObat = $OpnameObatModel
                ->orderBy('tanggal', 'DESC')
                ->findAll($limit, $offset);

            // Menghitung nomor awal untuk halaman saat ini
            $startNumber = $offset + 1;

            // Menambahkan nomor urut ke data pembelian obat
            $dataOpnameObat = array_map(function ($data, $index) use ($startNumber) {
                $data['number'] = $startNumber + $index;
                return $data;
            }, $OpnameObat, array_keys($OpnameObat));

            // Mengembalikan respons JSON dengan data pembelian obat dan total
            return $this->response->setJSON([
                'opname_obat' => $dataOpnameObat,
                'total' => $total
            ]);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function create()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Menyimpan data pembelian obat
            $data = [
                'tanggal' => date('Y-m-d H:i:s'),
                'apoteker' => session()->get('fullname'),
            ];

            // Simpan data opname obat
            $opnameId = $this->OpnameObatModel->save($data); // Menyimpan data ke database dan mendapatkan ID yang baru ditambahkan

            // Jika penyimpanan opname obat berhasil, lanjutkan untuk menyimpan detail opname
            if ($opnameId) {
                // Ambil seluruh data dari tabel obat
                $obatData = $this->ObatModel->findAll();

                // Siapkan data untuk disimpan ke detail_opname_obat
                $detailData = [];
                foreach ($obatData as $obat) {
                    $sisa_stok = $obat['jumlah_masuk'] - $obat['jumlah_keluar'];
                    $detailData[] = [
                        'id_opname_obat' => $opnameId, // ID opname obat yang baru
                        'nama_obat' => $obat['nama_obat'],
                        'sisa_stok' => $sisa_stok,
                    ];
                }

                // Simpan data detail opname obat ke database
                if (!empty($detailData)) {
                    $db = db_connect(); // Menghubungkan ke database
                    $builder = $db->table('detail_opname_obat'); // Mengakses tabel detail_opname_obat
                    $builder->insertBatch($detailData); // Menggunakan insertBatch untuk menyimpan beberapa data sekaligus
                }

                return $this->response->setJSON(['success' => true, 'message' => 'Opname obat berhasil ditambahkan']);
            } else {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Gagal menambahkan opname obat']);
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function delete($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            $db = db_connect();

            // Menghapus detail opname obat
            $db->table('detail_opname_obat')->where('id_opname_obat', $id)->delete();
            // Menghapus data opname obat
            $this->OpnameObatModel->delete($id);

            // Reset auto increment untuk tabel opname_obat dan detail_opname_obat
            $db->query('ALTER TABLE `opname_obat` auto_increment = 1');
            $db->query('ALTER TABLE `detail_opname_obat` auto_increment = 1');

            return $this->response->setJSON(['success' => true, 'message' => 'Opname obat berhasil dihapus']);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    // DETAIL PEMBELIAN OBAT
    public function detailopnameobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil opname obat
            $opname_obat = $this->OpnameObatModel->find($id);

            // Menyiapkan data untuk ditampilkan
            $data = [
                'opname_obat' => $opname_obat,
                'title' => 'Detail Laporan Stok Obat ' . $opname_obat['tanggal'] . ' - ' . $this->systemName,
                'headertitle' => 'Detail Laporan Stok Obat',
                'agent' => $this->request->getUserAgent()
            ];

            // Mengembalikan view dengan data yang telah disiapkan
            return view('dashboard/opnameobat/details', $data);
        } else {
            // Jika peran tidak dikenali, kembalikan kesalahan 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function obatlist($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil detail opname obat
            $db = db_connect();
            $detail_opname_obat = $db->table('detail_opname_obat')->where('id_opname_obat', $id)->orderBy('nama_obat')->get()->getResultArray();

            // Mengembalikan menjadi JSON
            return $this->response->setJSON($detail_opname_obat);
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function exportopnameobat($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Apoteker') {
            // Mengambil opname obat
            $opname_obat = $this->OpnameObatModel->find($id);
            // Mengambil detail opname obat
            $db = db_connect();
            $detail_opname_obat = $db->table('detail_opname_obat')->where('id_opname_obat', $id)->orderBy('nama_obat')->get()->getResultArray();
            // Mengambil jumlah total sisa_stok dari detail_opname_obat
            $total_stok = $db->table('detail_opname_obat')
                ->selectSum('sisa_stok')  // Fungsi SUM untuk menjumlahkan sisa_stok
                ->where('id_opname_obat', $id)
                ->get()
                ->getRowArray();

            // Ambil nilai jumlah sisa_stok dari array
            $jumlah_sisa_stok = $total_stok['sisa_stok'] ?? 0;

            // Memeriksa apakah detail pembelian obat kosong
            if (empty($opname_obat)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                // Membuat nama file berdasarkan tanggal pembelian
                $filename = $opname_obat['tanggal'] . '-laporan-stok-obat';
                $tanggal = new DateTime($opname_obat['tanggal']);
                // Buat formatter untuk tanggal dan waktu
                $formatter = new IntlDateFormatter(
                    'id_ID', // Locale untuk bahasa Indonesia
                    IntlDateFormatter::LONG, // Format untuk tanggal
                    IntlDateFormatter::NONE, // Tidak ada waktu
                    'Asia/Jakarta', // Timezone
                    IntlDateFormatter::GREGORIAN, // Calendar
                    'EEEE, d MMMM yyyy HH:mm:ss' // Format tanggal lengkap dengan nama hari
                );

                // Format tanggal
                $tanggalFormat = $formatter->format($tanggal);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Menambahkan informasi header di spreadsheet
                $sheet->setCellValue('A1', 'KLINIK UTAMA MATA PADANG EYE CENTER TELUK KUANTAN');
                $sheet->setCellValue('A2', 'Jl. Rusdi S. Abrus No. 35 LK III Sinambek, Kelurahan Sungai Jering, Kecamatan Kuantan Tengah, Kabupaten Kuantan Singingi, Riau.');
                $sheet->setCellValue('A3', 'LAPORAN STOK OBAT');

                // Path gambar yang ingin ditambahkan
                $gambarPath = FCPATH . 'assets/images/logo_pec.png'; // Ganti dengan path gambar Anda

                // Membuat objek Drawing
                $drawing = new Drawing();
                $drawing->setName('Logo PEC-TK'); // Nama gambar
                $drawing->setDescription('Logo PEC-TK'); // Deskripsi gambar
                $drawing->setPath($gambarPath); // Path ke gambar
                $drawing->setCoordinates('A1'); // Koordinat sel tempat gambar akan ditambahkan
                $drawing->setHeight(36); // Tinggi gambar dalam piksel (opsional)
                $drawing->setWorksheet($sheet); // Menambahkan gambar ke worksheet

                // Menambahkan informasi tanggal dan supplier
                $sheet->setCellValue('A4', 'Tanggal dan Waktu:');
                $sheet->setCellValue('C4', $tanggalFormat);
                $sheet->setCellValue('A5', 'Apoteker:');
                $sheet->setCellValue('C5', $opname_obat['apoteker']);

                // Menambahkan header tabel detail pembelian
                $sheet->setCellValue('A6', 'No.');
                $sheet->setCellValue('B6', 'Nama Obat');
                $sheet->setCellValue('D6', 'Sisa Stok');

                // Mengatur tata letak dan gaya untuk header
                $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
                $spreadsheet->getActiveSheet()->mergeCells('A2:D2');
                $spreadsheet->getActiveSheet()->mergeCells('A3:D3');
                $spreadsheet->getActiveSheet()->mergeCells('B6:C6');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Helvetica');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                // Mengisi data detail pembelian oasien Rawat Jalanbat ke dalam spreadsheet
                $column = 7;
                $nomor = 1;

                foreach ($detail_opname_obat as $list) {
                    $sheet->setCellValue('A' . $column, $nomor++);
                    $sheet->setCellValue('B' . $column, $list['nama_obat']);
                    $sheet->setCellValue('D' . $column, $list['sisa_stok']);
                    // Menggabungkan sel dari B hingga C
                    $sheet->mergeCells('B' . $column . ':C' . $column);
                    // Mengatur gaya teks
                    $sheet->getStyle('A' . $column . ':D' . $column)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('A' . $column)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $column)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A' . $column . ':D' . $column)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $column++;
                }

                // Menambahkan total pembelian di bawah tabel
                $sheet->setCellValue('A' . ($column), 'Total');
                $spreadsheet->getActiveSheet()->mergeCells('A' . ($column) . ':C' . ($column));
                $sheet->setCellValue('D' . ($column), $jumlah_sisa_stok);
                $sheet->getStyle('D' . $column)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Menambahkan bagian tanda tangan penerima
                $sheet->setCellValue('C' . ($column + 2), 'Apoteker');
                $sheet->setCellValue('C' . ($column + 7), $opname_obat['apoteker']);

                // Mengatur gaya teks untuk header dan total
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setSize(6);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setSize(10);
                $sheet->getStyle('C4:C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A6:D6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('C' . ($column + 2) . ':C' . ($column + 7))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Mengatur gaya font untuk header dan total
                $sheet->getStyle('A1:A5')->getFont()->setBold(TRUE);
                $sheet->getStyle('A6:D6')->getFont()->setBold(TRUE);
                $sheet->getStyle('A6:D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':D' . ($column))->getFont()->setBold(TRUE);
                $sheet->getStyle('A' . ($column) . ':D' . ($column))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Menambahkan border untuk header dan tabel
                $headerBorder1 = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A2:D2')->applyFromArray($headerBorder1);
                $tableBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A6:D' . ($column))->applyFromArray($tableBorder);

                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(50, 'px');
                $sheet->getColumnDimension('B')->setWidth(300, 'px');
                $sheet->getColumnDimension('C')->setWidth(300, 'px');
                $sheet->getColumnDimension('D')->setWidth(120, 'px');

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
