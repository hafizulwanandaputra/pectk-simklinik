<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\PermintaanModel;
use App\Models\MenuModel;
use App\Models\PetugasModel;
use App\Models\DataTablesPermintaan;
use CodeIgniter\Exceptions\PageNotFoundException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class Permintaan extends BaseController
{
    protected $PermintaanModel;
    protected $MenuModel;
    protected $PetugasModel;
    protected $DataTablesPermintaan;
    public function __construct()
    {
        $this->PermintaanModel = new PermintaanModel();
        $this->MenuModel = new MenuModel();
        $this->PetugasModel = new PetugasModel();
        $this->DataTablesPermintaan = new DataTablesPermintaan();
    }

    public function index()
    {
        $data = [
            'title' => 'Permintaan - ' . $this->systemName,
            'headertitle' => 'Permintaan',
            'agent' => $this->request->getUserAgent()
        ];
        return view('dashboard/permintaan/index', $data);
    }

    public function permintaanlist()
    {
        $request = $this->request->getPost();
        $search = $request['search']['value']; // Search value
        $start = $request['start']; // Start index for pagination
        $length = $request['length']; // Length of the page
        $draw = $request['draw']; // Draw counter for DataTables

        // Get sorting parameters
        $order = $request['order'];
        $sortColumnIndex = $order[0]['column']; // Column index
        $sortDirection = $order[0]['dir']; // asc or desc

        // Map column index to the database column name
        $columnMapping = [
            0 => 'id',
            1 => 'id',
            2 => 'tanggal',
            3 => 'nama_menu',
            4 => 'nama_petugas',
            5 => 'nama_pasien',
            6 => 'tanggal_lahir',
            7 => 'jenis_kelamin',
            8 => 'kamar',
            9 => 'jenis_tindakan',
            10 => 'diet',
            11 => 'keterangan',
        ];

        // Get the column to sort by
        $sortColumn = $columnMapping[$sortColumnIndex] ?? 'id';

        // Get total records count
        $totalRecords = $this->PermintaanModel->countAllResults(true);

        // Apply search query
        if ($search) {
            $this->PermintaanModel
                ->like('nama_pasien', $search)
                ->orderBy($sortColumn, $sortDirection);
        }

        // Get filtered records count
        $filteredRecords = $this->PermintaanModel->countAllResults(false);

        // Fetch the data
        $demands = $this->PermintaanModel
            ->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')
            ->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')
            ->orderBy($sortColumn, $sortDirection)
            ->findAll($length, $start);

        // Format the data
        $data = [];
        foreach ($demands as $demand) {
            $data[] = $demand;
        }

        // Return the JSON response
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function menuoptions()
    {
        $results = $this->MenuModel->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->orderBy('nama_menu', 'ASC')->findAll();

        $options = [];
        foreach ($results as $row) {
            $options[] = [
                'value' => $row['id_menu'],
                'text' => $row['nama_menu'] . ' - ' . $row['nama_petugas']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function eticketprint($id)
    {
        $permintaan = $this->PermintaanModel->join('menu', 'permintaan.id_menu = menu.id_menu', 'inner')->join('petugas', 'menu.id_petugas = petugas.id_petugas', 'inner')->find($id);
        if (empty($permintaan)) {
            throw PageNotFoundException::forPageNotFound();
        } else {
            $data = [
                'permintaan' => $permintaan,
                'title' => 'E-tiket untuk Permintaan "' . $permintaan['nama_pasien'] . '" - ' . $this->systemName,
                'agent' => $this->request->getUserAgent()
            ];
            $dompdf = new Dompdf();
            $html = view('dashboard/permintaan/eticketview', $data);
            $dompdf->loadHtml($html);
            $dompdf->render();
            $dompdf->stream('eticket-id-' . $permintaan['id'] . '-' . $permintaan['tanggal'] . '-' . urlencode($permintaan['nama_pasien']) . '.pdf', array(
                'Attachment' => FALSE
            ));
        }
    }

    public function exportexcel()
    {
        $keyword = $this->request->getGet('keyword');
        if ($keyword != '') {
            $db = \Config\Database::connect();
            $permintaanq = $db->table('permintaan');
            $permintaanq->select('*');
            $permintaanq->join('menu', 'menu.id_menu = permintaan.id_menu')->join('petugas', 'petugas.id_petugas = menu.id_petugas');
            $permintaanq->where('permintaan.id_menu', $keyword);
            $countpermintaan = $permintaanq->countAllResults(false);
            $permintaan = $permintaanq->orderBy('permintaan.nama_pasien', 'ASC')->get()->getResult('array');
            $permintaaninitq = $db->table('permintaan');
            $permintaaninitq->select('*');
            $permintaaninitq->join('menu', 'menu.id_menu = permintaan.id_menu')->join('petugas', 'petugas.id_petugas = menu.id_petugas');
            $permintaaninitq->where('permintaan.id_menu', $keyword);
            $permintaaninit = $permintaaninitq->get()->getRowArray();

            if (empty($permintaaninit)) {
                throw PageNotFoundException::forPageNotFound();
            } else {
                $filename = $permintaaninit['tanggal'] . '-permintaan-makanan-pasien-rawat-inap';
                $tanggal = Time::parse($permintaaninit['tanggal']);
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'DAFTAR PERMINTAAN MAKANAN PASIEN RAWAT INAP');
                $sheet->setCellValue('A2', 'Hari/Tanggal:');
                $sheet->setCellValue('C2', $tanggal->toLocalizedString('d MMMM yyyy'));
                $sheet->setCellValue('A3', 'Jadwal Makan:');
                $sheet->setCellValue('C3', $permintaaninit['jadwal_makan']);
                $sheet->setCellValue('A4', 'Nama Menu:');
                $sheet->setCellValue('C4', $permintaaninit['nama_menu']);
                $sheet->setCellValue('C5', 'Protein Hewani:');
                $sheet->setCellValue('D5', $permintaaninit['protein_hewani']);
                $sheet->setCellValue('C6', 'Protein Nabati:');
                $sheet->setCellValue('D6', $permintaaninit['protein_nabati']);
                $sheet->setCellValue('C7', 'Protein Sayur:');
                $sheet->setCellValue('D7', $permintaaninit['sayur']);
                $sheet->setCellValue('C8', 'Protein Buah:');
                $sheet->setCellValue('D8', $permintaaninit['buah']);
                $sheet->setCellValue('A9', 'Petugas Gizi:');
                $sheet->setCellValue('C9', $permintaaninit['nama_petugas']);

                $sheet->setCellValue('A10', 'No');
                $sheet->setCellValue('B10', 'Nama Pasien');
                $sheet->setCellValue('C10', 'Tanggal Lahir');
                $sheet->setCellValue('D10', 'Jenis Kelamin');
                $sheet->setCellValue('E10', 'Kamar');
                $sheet->setCellValue('F10', 'Jenis Tindakan');
                $sheet->setCellValue('G10', 'Diet');
                $sheet->setCellValue('H10', 'Keterangan');

                $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $spreadsheet->getActiveSheet()->getPageSetup()
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(8);

                $column = 11;
                foreach ($permintaan as $listpermintaan) {
                    $tanggal_lahir = Time::parse($listpermintaan['tanggal_lahir']);
                    $sheet->setCellValue('A' . $column, ($column - 10));
                    $sheet->setCellValue('B' . $column, $listpermintaan['nama_pasien']);
                    $sheet->setCellValue('C' . $column, $tanggal_lahir->toLocalizedString('d MMMM yyyy'));
                    $sheet->setCellValue('D' . $column, $listpermintaan['jenis_kelamin']);
                    $sheet->setCellValue('E' . $column, $listpermintaan['kamar']);
                    $sheet->setCellValue('F' . $column, $listpermintaan['jenis_tindakan']);
                    $sheet->setCellValue('G' . $column, $listpermintaan['diet']);
                    $sheet->setCellValue('H' . $column, $listpermintaan['keterangan']);
                    $sheet->getStyle('B' . $column . ':H' . $column)->getAlignment()->setWrapText(true);
                    $column++;
                }
                $sheet->setCellValue('B' . $column, 'Jumlah Pasien: ' . $countpermintaan . ' orang');
                $sheet->setCellValue('H' . ($column + 1), 'Padang,');
                $sheet->setCellValue('F' . ($column + 2), 'Petugas Gizi');
                $sheet->setCellValue('G' . ($column + 2), 'Ahli Gizi');
                $sheet->setCellValue('H' . ($column + 2), 'Perawat Rawat Inap');
                $sheet->setCellValue('F' . ($column + 6), '(                                    )');
                $sheet->setCellValue('G' . ($column + 6), '(                                    )');
                $sheet->setCellValue('H' . ($column + 6), '(                                    )');

                $sheet->getStyle('A1')->getFont()->setBold(TRUE);
                $sheet->getStyle('A2:A9')->getFont()->setBold(TRUE);
                $sheet->getStyle('C5:C8')->getFont()->setBold(TRUE);
                $sheet->getStyle('A10:H10')->getFont()->setBold(TRUE);

                $sheet->getStyle('B' . $column)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A10:H10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A11:A' . ($column + 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . ($column + 2) . ':H' . ($column + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . ($column + 6) . ':H' . ($column + 6))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000']
                        ]
                    ]
                ];
                $sheet->getStyle('A10:H' . ($column - 1))->applyFromArray($styleArray);

                $sheet->getColumnDimension('A')->setWidth(35, 'px');
                $sheet->getColumnDimension('B')->setWidth(210, 'px');
                $sheet->getColumnDimension('C')->setWidth(120, 'px');
                $sheet->getColumnDimension('D')->setWidth(120, 'px');
                $sheet->getColumnDimension('E')->setWidth(120, 'px');
                $sheet->getColumnDimension('F')->setWidth(180, 'px');
                $sheet->getColumnDimension('G')->setWidth(180, 'px');
                $sheet->getColumnDimension('H')->setWidth(180, 'px');

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

    public function permintaan($id)
    {
        $data = $this->PermintaanModel
            ->join('menu', 'menu.id_menu = permintaan.id_menu', 'inner')
            ->join('petugas', 'petugas.id_petugas = menu.id_petugas', 'inner')
            ->find($id);
        return $this->response->setJSON($data);
    }

    public function create()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_menu' => [
                'label' => 'Menu',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'nama_pasien' => [
                'label' => 'Nama Pasien',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'tanggal_lahir' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'kamar' => [
                'label' => 'Kamar',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_tindakan' => [
                'label' => 'Jenis Tindakan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'diet' => [
                'label' => 'Diet',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id_menu' => $this->request->getPost('id_menu'),
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kamar' => $this->request->getPost('kamar'),
            'jenis_tindakan' => $this->request->getPost('jenis_tindakan'),
            'diet' => $this->request->getPost('diet'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $this->PermintaanModel->save($data);
        $db = db_connect();
        $permintaan = $db->table('permintaan')->where('id_menu', $this->request->getPost('id_menu'));
        $totalpermintaan = $permintaan->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $this->request->getPost('id_menu'));
        return $this->response->setJSON(['success' => true, 'message' => 'Permintaan berhasil ditambahkan']);
    }

    public function update()
    {
        // Validate
        $validation = \Config\Services::validation();
        // Set base validation rules
        $validation->setRules([
            'id_menu' => [
                'label' => 'Menu',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'nama_pasien' => [
                'label' => 'Nama Pasien',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'tanggal_lahir' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib dipilih!'
                ]
            ],
            'kamar' => [
                'label' => 'Kamar',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'jenis_tindakan' => [
                'label' => 'Jenis Tindakan',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
            'diet' => [
                'label' => 'Diet',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} wajib diisi!'
                ]
            ],
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
        }

        // Save Data
        $data = [
            'id' => $this->request->getPost('id_permintaan'),
            'id_menu' => $this->request->getPost('id_menu'),
            'nama_pasien' => $this->request->getPost('nama_pasien'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kamar' => $this->request->getPost('kamar'),
            'jenis_tindakan' => $this->request->getPost('jenis_tindakan'),
            'diet' => $this->request->getPost('diet'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $this->PermintaanModel->save($data);
        $db = db_connect();
        $permintaanlama = $db->table('permintaan')->where('id_menu', $this->request->getPost('id_menu_lama'));
        $totalpermintaanlama = $permintaanlama->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaanlama . ' WHERE id_menu = ' . $this->request->getPost('id_menu_lama'));
        $permintaan = $db->table('permintaan')->where('id_menu', $this->request->getPost('id_menu'));
        $totalpermintaan = $permintaan->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $this->request->getPost('id_menu'));
        return $this->response->setJSON(['success' => true, 'message' => 'Permintaan berhasil diedit']);
    }

    public function delete($id)
    {
        $permintaan = $this->PermintaanModel->getPermintaan($id);
        $this->PermintaanModel->delete($id);
        $db = db_connect();
        $permintaan1 = $db->table('permintaan')->where('id_menu', $permintaan['id_menu']);
        $totalpermintaan = $permintaan1->countAllResults();
        $db->query('UPDATE menu SET jumlah = ' . $totalpermintaan . ' WHERE id_menu = ' . $permintaan['id_menu']);
        $db->query('ALTER TABLE `permintaan` auto_increment = 1');
        return $this->response->setJSON(['message' => 'Permintaan berhasil dihapus']);
    }
}
