<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SPOperasiModel;
use App\Models\SafetySignInModel;
use App\Models\SafetySignOutModel;
use App\Models\SafetyTimeOutModel;
use CodeIgniter\Exceptions\PageNotFoundException;

use Picqer\Barcode\BarcodeGeneratorPNG;

class SafetyOperasi extends BaseController
{
    protected $SPOperasiModel;
    protected $SafetySignInModel;
    protected $SafetySignOutModel;
    protected $SafetyTimeOutModel;
    public function __construct()
    {
        $this->SPOperasiModel = new SPOperasiModel();
        $this->SafetySignInModel = new SafetySignInModel();
        $this->SafetySignOutModel = new SafetySignOutModel();
        $this->SafetyTimeOutModel = new SafetyTimeOutModel();
    }

    public function index($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_signin) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_signin')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_timeout) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_timeout')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            if (!$operasi_safety_signout) {
                // Jika asesmen tidak ditemukan, buat asesmen baru dengan query builder
                $db->table('medrec_operasi_safety_signout')->insert([
                    'nomor_booking' => $sp_operasi['nomor_booking'],
                    'nomor_registrasi' => $sp_operasi['nomor_registrasi'],
                    'no_rm' => $sp_operasi['no_rm'],
                    'waktu_dibuat' => date('Y-m-d H:i:s')
                ]);

                // Setelah asesmen dibuat, ambil kembali data asesmen menggunakan query builder
                $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                    ->where('nomor_booking', $sp_operasi['nomor_booking'])
                    ->get()
                    ->getRowArray();
            }

            $perawat = $db->table('user')
                ->where('role', 'Perawat')
                ->where('active', 1)
                ->get()->getResultArray();

            $dokter = $db->table('user')
                ->where('role', 'Dokter')
                ->where('active', 1)
                ->get()->getResultArray();

            // Query untuk item sebelumnya
            $previous = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi <', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'DESC') // Urutan descending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk item berikutnya
            $next = $db->table('medrec_sp_operasi')
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->where('medrec_sp_operasi.id_sp_operasi >', $id)
                ->orderBy('medrec_sp_operasi.id_sp_operasi', 'ASC') // Urutan ascending
                ->limit(1) // Batas 1 hasil
                ->get()
                ->getRowArray();

            // Query untuk daftar rawat jalan berdasarkan no_rm
            $listRawatJalan = $db->table('rawat_jalan')
                ->join('pasien', 'rawat_jalan.no_rm = pasien.no_rm', 'inner')
                ->join('medrec_sp_operasi', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->where('rawat_jalan.no_rm', $sp_operasi['no_rm'])
                ->where('rawat_jalan.status', 'DAFTAR')
                ->where('rawat_jalan.ruangan', 'Kamar Operasi')
                ->orderBy('rawat_jalan.id_rawat_jalan', 'DESC')
                ->get()
                ->getResultArray();

            // Menyiapkan data untuk tampilan
            $data = [
                'operasi' => $sp_operasi,
                'operasi_safety_signin' => $operasi_safety_signin,
                'operasi_safety_timeout' => $operasi_safety_timeout,
                'operasi_safety_signout' => $operasi_safety_signout,
                'perawat' => $perawat,
                'dokter' => $dokter,
                'title' => 'Pemeriksaan Keselamatan ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                'headertitle' => 'Pemeriksaan Keselamatan',
                'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                'previous' => $previous,
                'next' => $next,
                'listRawatJalan' => $listRawatJalan
            ];
            // Menampilkan tampilan untuk halaman pra operasi
            return view('dashboard/operasi/safety/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function view_signin($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data safety sign in berdasarkan ID
            $data = $this->SafetySignInModel->find($id); // Mengambil safety sign in
            return $this->response->setJSON($data); // Mengembalikan data safety sign in dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view_signout($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data safety sign out berdasarkan ID
            $data = $this->SafetySignOutModel->find($id); // Mengambil safety sign out
            return $this->response->setJSON($data); // Mengembalikan data safety sign out dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function view_timeout($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Perawat' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            // Mengambil data safety time out berdasarkan ID
            $data = $this->SafetyTimeOutModel->find($id); // Mengambil safety time out
            return $this->response->setJSON($data); // Mengembalikan data safety time out dalam format JSON
        } else {
            // Mengembalikan status 404 jika peran tidak diizinkan
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }

    public function export($id)
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Dokter', atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat' || session()->get('role') == 'Admisi') {
            $db = db_connect();

            // Inisialisasi rawat jalan
            $sp_operasi = $this->SPOperasiModel
                ->join('rawat_jalan', 'rawat_jalan.nomor_registrasi = medrec_sp_operasi.nomor_registrasi', 'inner')
                ->join('pasien', 'pasien.no_rm = rawat_jalan.no_rm', 'inner')
                ->find($id);

            if (!$sp_operasi) {
                throw PageNotFoundException::forPageNotFound();
            }

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                ->where('nomor_booking', $sp_operasi['nomor_booking'])
                ->get()
                ->getRowArray();

            // === Generate Barcode ===
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $bcNoReg = base64_encode($barcodeGenerator->getBarcode($sp_operasi['nomor_registrasi'], $barcodeGenerator::TYPE_CODE_128));

            // Memeriksa apakah pasien tidak kosong
            if ($operasi_safety_signin && $operasi_safety_signout && $operasi_safety_timeout) {
                // Menyiapkan data untuk tampilan
                $data = [
                    'operasi' => $sp_operasi,
                    'operasi_safety_signin' => $operasi_safety_signin,
                    'operasi_safety_signout' => $operasi_safety_signout,
                    'operasi_safety_timeout' => $operasi_safety_timeout,
                    'bcNoReg' => $bcNoReg,
                    'title' => 'Pemeriksaan Keselamatan ' . $sp_operasi['nama_pasien'] . ' (' . $sp_operasi['no_rm'] . ') - ' . $sp_operasi['nomor_registrasi'] . ' - ' . $sp_operasi['nomor_booking'] . ' - ' . $this->systemName,
                    'headertitle' => 'Pemeriksaan Keselamatan',
                    'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
                ];
                // return view('dashboard/operasi/safety/form', $data);
                // die;
                // Simpan HTML ke file sementara
                $htmlFile = WRITEPATH . 'temp/output-safety.html';
                file_put_contents($htmlFile, view('dashboard/operasi/safety/form', $data));

                // Tentukan path output PDF
                $pdfFile = WRITEPATH . 'temp/output-safety.pdf';

                // Jalankan Puppeteer untuk konversi HTML ke PDF
                // Keterangan: "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile panjang lebar marginAtas margin Kanan marginBawah marginKiri"
                // Silakan lihat puppeteer-pdf.js di folder public untuk keterangan lebih lanjut.
                $command = env('CMD-ENV') . "node " . FCPATH . "puppeteer-pdf.js $htmlFile $pdfFile 297mm 210mm 1cm 1cm 1cm 1cm";
                shell_exec($command);

                // Hapus file HTML
                @unlink($htmlFile);

                // Kirim PDF ke browser
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="Safety_' . $sp_operasi['nomor_booking'] . '_' . str_replace('-', '', $sp_operasi['no_rm']) . '.pdf')
                    ->setBody(file_get_contents($pdfFile));
            } else {
                // Menampilkan halaman tidak ditemukan jika pasien tidak ditemukan
                throw PageNotFoundException::forPageNotFound();
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function update_signin($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter' || session()->get('role') == 'Perawat') {
            $db = db_connect();
            $validation = \Config\Services::validation();

            $operasi_safety_signin = $db->table('medrec_operasi_safety_signin')
                ->where('id_signin', $id)
                ->get()
                ->getRowArray();

            if (session()->get('role') == 'Perawat') {
                $rules = [
                    'ns_marker_operasi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_identifikasi_alergi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_puasa' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_cek_lensa_intrakuler' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'nama_dokter_anastesi' => [
                        'rules' => 'required'
                    ]
                ];
            } else if (session()->get('role') == 'Dokter') {
                $rules = [
                    'dr_marker_operasi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_identifikasi_alergi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_puasa' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_cek_anestesi_khusus' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'nama_dokter_anastesi' => [
                        'rules' => 'required'
                    ]
                ];
            } else if (session()->get('role') == 'Admin') {
                $rules = [
                    'ns_marker_operasi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_marker_operasi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_identifikasi_alergi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_identifikasi_alergi' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_puasa' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_puasa' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'ns_cek_lensa_intrakuler' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'dr_cek_anestesi_khusus' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Wajib dipilih'
                        ]
                    ],
                    'nama_dokter_anastesi' => [
                        'rules' => 'required'
                    ]
                ];
            }

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            if (session()->get('role') == 'Perawat') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'ns_konfirmasi_identitas' => $this->request->getPost('ns_konfirmasi_identitas') ?: NULL,
                    'ns_marker_operasi' => $this->request->getPost('ns_marker_operasi') ?: NULL,
                    'ns_inform_consent_sesuai' => $this->request->getPost('ns_inform_consent_sesuai') ?: NULL,
                    'ns_identifikasi_alergi' => $this->request->getPost('ns_identifikasi_alergi') ?: NULL,
                    'ns_puasa' => $this->request->getPost('ns_puasa') ?: NULL,
                    'ns_cek_lensa_intrakuler' => $this->request->getPost('ns_cek_lensa_intrakuler') ?: NULL,
                    'ns_konfirmasi_lensa' => $this->request->getPost('ns_konfirmasi_lensa') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            } else if (session()->get('role') == 'Dokter') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'dr_konfirmasi_identitas' => $this->request->getPost('dr_konfirmasi_identitas') ?: NULL,
                    'dr_marker_operasi' => $this->request->getPost('dr_marker_operasi') ?: NULL,
                    'dr_inform_consent_sesuai' => $this->request->getPost('dr_inform_consent_sesuai') ?: NULL,
                    'dr_identifikasi_alergi' => $this->request->getPost('dr_identifikasi_alergi') ?: NULL,
                    'dr_puasa' => $this->request->getPost('dr_puasa') ?: NULL,
                    'dr_cek_anestesi_khusus' => $this->request->getPost('dr_cek_anestesi_khusus') ?: NULL,
                    'dr_konfirmasi_anastersi' => $this->request->getPost('dr_konfirmasi_anastersi') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            } else if (session()->get('role') == 'Admin') {
                $data = [
                    'id_signin' => $id,
                    'nomor_booking' => $operasi_safety_signin['nomor_booking'],
                    'nomor_registrasi' => $operasi_safety_signin['nomor_registrasi'],
                    'no_rm' => $operasi_safety_signin['no_rm'],
                    'ns_konfirmasi_identitas' => $this->request->getPost('ns_konfirmasi_identitas') ?: NULL,
                    'ns_marker_operasi' => $this->request->getPost('ns_marker_operasi') ?: NULL,
                    'ns_inform_consent_sesuai' => $this->request->getPost('ns_inform_consent_sesuai') ?: NULL,
                    'ns_identifikasi_alergi' => $this->request->getPost('ns_identifikasi_alergi') ?: NULL,
                    'ns_puasa' => $this->request->getPost('ns_puasa') ?: NULL,
                    'ns_cek_lensa_intrakuler' => $this->request->getPost('ns_cek_lensa_intrakuler') ?: NULL,
                    'ns_konfirmasi_lensa' => $this->request->getPost('ns_konfirmasi_lensa') ?: NULL,
                    'dr_konfirmasi_identitas' => $this->request->getPost('dr_konfirmasi_identitas') ?: NULL,
                    'dr_marker_operasi' => $this->request->getPost('dr_marker_operasi') ?: NULL,
                    'dr_inform_consent_sesuai' => $this->request->getPost('dr_inform_consent_sesuai') ?: NULL,
                    'dr_identifikasi_alergi' => $this->request->getPost('dr_identifikasi_alergi') ?: NULL,
                    'dr_puasa' => $this->request->getPost('dr_puasa') ?: NULL,
                    'dr_cek_anestesi_khusus' => $this->request->getPost('dr_cek_anestesi_khusus') ?: NULL,
                    'dr_konfirmasi_anastersi' => $this->request->getPost('dr_konfirmasi_anastersi') ?: NULL,
                    'nama_dokter_anastesi' => $this->request->getPost('nama_dokter_anastesi') ?: NULL,
                    'waktu_dibuat' => $operasi_safety_signin['waktu_dibuat'],
                ];
            }

            $db->table('medrec_operasi_safety_signin')->where('id_signin', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan keselamatan <em>sign in</em> berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
        }
    }

    public function update_signout($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Dokter') {
            $db = db_connect();
            $validation = \Config\Services::validation();

            $operasi_safety_signout = $db->table('medrec_operasi_safety_signout')
                ->where('id_signout', $id)
                ->get()
                ->getRowArray();

            $masalah_instrumen = $this->request->getPost('masalah_instrumen');
            $instruksi_khusus = $this->request->getPost('instruksi_khusus');

            $rules = [
                'spesimen_kultur' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Wajib dipilih'
                    ]
                ],
                'masalah_instrumen' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Wajib dipilih'
                    ]
                ],
                'keterangan_masalah' => [
                    'rules' => $masalah_instrumen === 'YA' ? 'required' : 'permit_empty',
                    'errors' => [
                        'required' => 'Wajib diisi'
                    ]
                ],
                'keterangan_instruksi' => [
                    'rules' => $instruksi_khusus === '1' ? 'required' : 'permit_empty',
                    'errors' => [
                        'required' => 'Wajib diisi'
                    ]
                ],
                'nama_dokter_operator' => [
                    'rules' => 'required'
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $data = [
                'id_signout' => $id,
                'nomor_booking' => $operasi_safety_signout['nomor_booking'],
                'nomor_registrasi' => $operasi_safety_signout['nomor_registrasi'],
                'no_rm' => $operasi_safety_signout['no_rm'],
                'kelengkapan_instrumen' => $this->request->getPost('kelengkapan_instrumen') ?: NULL,
                'spesimen_kultur' => $this->request->getPost('spesimen_kultur') ?: NULL,
                'label_pasien' => $this->request->getPost('label_pasien') ?: NULL,
                'masalah_instrumen' => $this->request->getPost('masalah_instrumen') ?: NULL,
                'keterangan_masalah' => $this->request->getPost('keterangan_masalah') ?: NULL,
                'instruksi_khusus' => $this->request->getPost('instruksi_khusus') ?: NULL,
                'keterangan_instruksi' => $this->request->getPost('keterangan_instruksi') ?: NULL,
                'nama_dokter_operator' => $this->request->getPost('nama_dokter_operator') ?: NULL,
                'waktu_dibuat' => $operasi_safety_signout['waktu_dibuat'],
            ];

            // Periksa apakah jam sudah ada, jika belum, isi dengan waktu sekarang
            if (empty($operasi_safety_signout['jam'])) {
                $data['jam'] = date('H:i:s');
            }

            $db->table('medrec_operasi_safety_signout')->where('id_signout', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan keselamatan <em>sign out</em> berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
        }
    }

    public function update_timeout($id)
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Perawat') {
            $db = db_connect();
            $validation = \Config\Services::validation();

            $operasi_safety_timeout = $db->table('medrec_operasi_safety_timeout')
                ->where('id_timeout', $id)
                ->get()
                ->getRowArray();

            $rules = [
                'alergi' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Wajib dipilih'
                    ]
                ],
                'proteksi' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Wajib dipilih'
                    ]
                ],
                'perlu_antibiotik_dan_guladarah' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Wajib dipilih'
                    ]
                ],
                'nama_perawat' => [
                    'rules' => 'required'
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $validation->getErrors()]);
            }

            $data = [
                'id_timeout' => $id,
                'nomor_booking' => $operasi_safety_timeout['nomor_booking'],
                'nomor_registrasi' => $operasi_safety_timeout['nomor_registrasi'],
                'no_rm' => $operasi_safety_timeout['no_rm'],
                'perkenalan_diri' => $this->request->getPost('perkenalan_diri') ?: NULL,
                'cek_nama_mr' => $this->request->getPost('cek_nama_mr') ?: NULL,
                'cek_rencana_tindakan' => $this->request->getPost('cek_rencana_tindakan') ?: NULL,
                'cek_marker' => $this->request->getPost('cek_marker') ?: NULL,
                'alergi' => $this->request->getPost('alergi') ?: NULL,
                'lateks' => $this->request->getPost('lateks') ?: NULL,
                'proteksi' => $this->request->getPost('proteksi') ?: NULL,
                'proteksi_kasa' => $this->request->getPost('proteksi_kasa') ?: NULL,
                'proteksi_shield' => $this->request->getPost('proteksi_shield') ?: NULL,
                'info_instrumen_ok' => $this->request->getPost('info_instrumen_ok') ?: NULL,
                'info_teknik_ok' => $this->request->getPost('info_teknik_ok') ?: NULL,
                'info_steril_instrumen' => $this->request->getPost('info_steril_instrumen') ?: NULL,
                'info_kelengkapan_instrumen' => $this->request->getPost('info_kelengkapan_instrumen') ?: NULL,
                'perlu_antibiotik_dan_guladarah' => $this->request->getPost('perlu_antibiotik_dan_guladarah') ?: NULL,
                'nama_perawat' => $this->request->getPost('nama_perawat') ?: NULL,
                'waktu_dibuat' => $operasi_safety_timeout['waktu_dibuat'],
            ];

            // Periksa apakah jam sudah ada, jika belum, isi dengan waktu sekarang
            if (empty($operasi_safety_timeout['jam'])) {
                $data['jam'] = date('H:i:s');
            }

            $db->table('medrec_operasi_safety_timeout')->where('id_timeout', $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Pemeriksaan keselamatan <em>time out</em> berhasil diperbarui']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Halaman tidak ditemukan']);
        }
    }
}
