<?php

namespace App\Controllers;

use App\Models\SettingsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Settings extends BaseController
{
    protected $SettingsModel;
    public function __construct()
    {
        $this->SettingsModel = new SettingsModel();
    }

    public function index()
    {
        // Menyiapkan data untuk tampilan halaman pengaturan
        $data = [
            'title' => 'Pengaturan - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Pengaturan', // Judul header
            'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
        ];
        return view('dashboard/settings/index', $data); // Mengembalikan tampilan halaman pengaturan
    }

    public function edit()
    {
        // Menyiapkan data untuk tampilan halaman edit informasi pengguna
        $data = [
            'title' => 'Ubah Informasi Pengguna - ' . $this->systemName, // Judul halaman
            'headertitle' => 'Ubah Informasi Pengguna', // Judul header
            'agent' => $this->request->getUserAgent(), // Mengambil informasi user agent
            'validation' => \Config\Services::validation() // Mengambil layanan validasi
        ];
        echo view('dashboard/settings/edit', $data); // Mengembalikan tampilan halaman edit
    }

    public function update()
    {
        // Memeriksa apakah username yang diinput sama dengan username yang ada di session
        if (session()->get('username') == $this->request->getVar('username')) {
            $username = 'required|alpha_numeric_punct'; // Aturan validasi jika username tidak berubah
        } else {
            $username = 'required|is_unique[user.username]|alpha_numeric_punct'; // Aturan validasi jika username berubah
        }

        // Memvalidasi input
        if (!$this->validate([
            'username' => [
                'label' => 'Nama Pengguna', // Label untuk kesalahan validasi
                'rules' => $username, // Aturan yang diterapkan
                'errors' => [
                    'required' => '{field} wajib diisi!', // Pesan kesalahan jika tidak diisi
                    'is_unique' => '{field} harus berbeda dari pengguna lainnya!' // Pesan kesalahan jika tidak unik
                ]
            ]
        ])) {
            return redirect()->back()->withInput(); // Kembali jika validasi gagal
        }

        // Menyimpan perubahan username ke dalam model SettingsModel
        $this->SettingsModel->save([
            'id_user' => session()->get('id_user'), // Mengambil id_user dari session
            'username' => $this->request->getVar('username'), // Mengambil username baru dari input
        ]);

        // Memeriksa apakah username yang diinput sama dengan username yang ada di session
        if ($this->request->getVar('username') == session()->get('username')) {
            session()->setFlashdata('info', 'Tidak ada perubahan apa-apa dalam formulir ini!'); // Pesan jika tidak ada perubahan
        } else {
            session()->remove('username'); // Menghapus username lama dari session
            session()->set('username', $this->request->getVar('username')); // Memperbarui username di session
            session()->setFlashdata('msg', 'Informasi Pengguna berhasil diubah!'); // Pesan sukses
        }

        return redirect()->back(); // Kembali ke halaman sebelumnya
    }

    public function pwdTransaksi()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Menyiapkan data untuk tampilan halaman ubah kata sandi
            $data = [
                'title' => 'Ubah Kata Sandi Transaksi - ' . $this->systemName, // Judul halaman
                'headertitle' => 'Ubah Kata Sandi Transaksi', // Judul header
                'agent' => $this->request->getUserAgent() // Mendapatkan user agent dari request
            ];
            // Mengembalikan tampilan halaman ubah kata sandi dengan data yang telah disiapkan
            return view('dashboard/settings/pwdtransaksi', $data);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function updatePwdTransaksi()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            // Memvalidasi input dari form ubah kata sandi
            if (!$this->validate([
                'new_password1' => [
                    'label' => 'Kata Sandi Baru', // Label untuk kata sandi baru
                    'rules' => 'required|min_length[5]|matches[new_password2]', // Validasi untuk kata sandi baru
                    'errors' => [
                        'required' => '{field} wajib diisi!', // Pesan error jika kata sandi baru tidak diisi
                        'min_length' => '{field} harus sekurang-kurangnya lima karakter', // Pesan error jika kata sandi baru kurang dari 5 karakter
                        'matches' => '{field} tidak cocok dengan Konfirmasi Kata Sandi!' // Pesan error jika kata sandi baru tidak cocok dengan konfirmasi
                    ]
                ],
                'new_password2' => [
                    'label' => 'Konfirmasi Kata Sandi', // Label untuk konfirmasi kata sandi baru
                    'rules' => 'required|min_length[5]|matches[new_password1]', // Validasi untuk konfirmasi kata sandi baru
                    'errors' => [
                        'required' => '{field} wajib diisi!', // Pesan error jika konfirmasi tidak diisi
                        'min_length' => '{field} harus sekurang-kurangnya lima karakter', // Pesan error jika konfirmasi kurang dari 5 karakter
                        'matches' => '{field} tidak cocok dengan Kata Sandi Baru!' // Pesan error jika konfirmasi tidak cocok dengan kata sandi baru
                    ]
                ]
            ])) {
                // Jika validasi gagal, mengalihkan kembali ke halaman ubah kata sandi dengan input yang ada
                return redirect()->back()->withInput();
            }

            $db = db_connect();
            $new_password = $this->request->getVar('new_password1');

            // Menghash kata sandi baru
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            // Menyimpan kata sandi baru ke database pwd_transaksi
            $data = [
                'id' => 1,
                'pwd_transaksi' => $password_hash
            ];
            $db->table('pwd_transaksi')->update($data, ['id' => 1]); // Update data berdasarkan id
            // Mengatur flashdata untuk menampilkan pesan sukses
            session()->setFlashdata('msg', 'Anda berhasil mengubah kata sandi transaksi!');
            return redirect()->back(); // Kembali ke halaman sebelumnya
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function about()
    {
        // Menghubungkan ke database
        $db = db_connect();
        $php_extensions = get_loaded_extensions(); // Mengambil ekstensi PHP yang terpasang
        $query_version = $db->query('SELECT VERSION() as version'); // Mengambil versi database
        $query_comment = $db->query('SHOW VARIABLES LIKE "version_comment"'); // Mengambil komentar versi database
        $query_compile_os = $db->query('SHOW VARIABLES LIKE "version_compile_os"'); // Mengambil sistem operasi
        $query_compile_machine = $db->query('SHOW VARIABLES LIKE "version_compile_machine"'); // Mengambil jenis mesin
        $row_version = $query_version->getRow(); // Mendapatkan hasil query versi
        $row_comment = $query_comment->getRow(); // Mendapatkan hasil query komentar versi
        $row_compile_os = $query_compile_os->getRow(); // Mendapatkan hasil sistem operasi
        $row_compile_machine = $query_compile_machine->getRow(); // Mendapatkan hasil jenis mesin
        $agent = $this->request->getUserAgent(); // Mengambil informasi user agent

        // Menyiapkan data untuk tampilan halaman tentang
        $data = [
            'php_extensions' => implode(', ', $php_extensions), // Menggabungkan ekstensi PHP menjadi string
            'version' => $row_version->version, // Versi database
            'version_comment' => $row_comment->Value, // Komentar versi database
            'version_compile_os' => $row_compile_os->Value, // Sistem operasi database
            'version_compile_machine' => $row_compile_machine->Value, // Jenis mesin database
            'systemName' => $this->systemName,
            'systemSubtitleName' => $this->systemSubtitleName,
            'companyName' => $this->companyName,
            'agent' => $agent, // Informasi user agent
            'title' => 'Tentang ' . $this->systemName, // Judul halaman
            'headertitle' => 'Tentang Sistem' // Judul header
        ];

        return view('dashboard/settings/about', $data); // Mengembalikan tampilan halaman tentang
    }

    public function autodate_on($id)
    {
        $db = db_connect(); // Menghubungkan ke database
        $builder = $db->table('user');

        // Update nilai auto_date menjadi 1
        $builder->set('auto_date', '1')->where('id_user', $id)->update();

        // Pastikan perubahan berhasil
        if ($db->affectedRows() > 0) {
            session()->set('auto_date', '1'); // Set session jika berhasil
            return $this->response->setJSON(['success' => true, 'message' => 'Tanggal otomatis berhasil diaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengaktifkan tanggal otomatis']);
    }

    public function autodate_off($id)
    {
        $db = db_connect(); // Menghubungkan ke database
        $builder = $db->table('user');

        // Update nilai auto_date menjadi 0
        $builder->set('auto_date', '0')->where('id_user', $id)->update();

        // Pastikan perubahan berhasil
        if ($db->affectedRows() > 0) {
            session()->set('auto_date', '0'); // Set session jika berhasil
            return $this->response->setJSON(['success' => true, 'message' => 'Tanggal otomatis berhasil dinonaktifkan']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menonaktifkan tanggal otomatis']);
    }

    public function deleteempty()
    {
        // Memeriksa apakah peran pengguna dalam sesi adalah "Admin"
        if (session()->get('role') == 'Admin') {
            $db = db_connect();
            $tables = [
                'medrec_assesment' => [
                    'keluhan_utama',
                    'riwayat_penyakit_sekarang',
                    'riwayat_penyakit_dahulu',
                    'riwayat_penyakit_keluarga',
                    'riwayat_pengobatan',
                    'riwayat_sosial_pekerjaan',
                    'kesadaran',
                    'tekanan_darah',
                    'nadi',
                    'suhu',
                    'pernapasan',
                    'keadaan_umum',
                    'alergi',
                    'alergi_keterangan',
                    'sakit_lainnya',
                    'tono_od',
                    'tono_os',
                    'visus_od1',
                    'visus_od2',
                    'visus_os1',
                    'visus_os2',
                    'od_ucva',
                    'od_bcva',
                    'os_ucva',
                    'os_bcva',
                    'indirect_fundus_img',
                    'fundus_vitreus_od',
                    'fundus_koroid_od',
                    'fundus_retina_od',
                    'fundus_vitreus_os',
                    'fundus_koroid_os',
                    'fundus_retina_os',
                    'diagnosa_medis_1',
                    'icdx_kode_1',
                    'icdx_nama_1',
                    'diagnosa_medis_2',
                    'icdx_kode_2',
                    'icdx_nama_2',
                    'diagnosa_medis_3',
                    'icdx_kode_3',
                    'icdx_nama_3',
                    'diagnosa_medis_4',
                    'icdx_kode_4',
                    'icdx_nama_4',
                    'diagnosa_medis_5',
                    'icdx_kode_5',
                    'icdx_nama_5',
                    'terapi_1',
                    'icd9_kode_1',
                    'icd9_nama_1',
                    'terapi_2',
                    'icd9_kode_2',
                    'icd9_nama_2',
                    'terapi_3',
                    'icd9_kode_3',
                    'icd9_nama_3',
                    'terapi_4',
                    'icd9_kode_4',
                    'icd9_nama_4',
                    'terapi_5',
                    'anatomi_img',
                    'anatomi_palpebra_od',
                    'anatomi_skelera_od',
                    'anatomi_konjungstiva_od',
                    'anatomi_kornea_od',
                    'anatomi_coa_od',
                    'anatomi_iris_od',
                    'anatomi_pupil_od',
                    'anatomi_lensa_od',
                    'anatomi_vitreus_od',
                    'anatomi_retina_od',
                    'anatomi_nervus_optikus_od',
                    'anatomi_posisi_od',
                    'anatomi_gerak_bola_mata_od',
                    'anatomi_palpebra_os',
                    'anatomi_skelera_os',
                    'anatomi_konjungstiva_os',
                    'anatomi_kornea_os',
                    'anatomi_coa_os',
                    'anatomi_iris_os',
                    'anatomi_pupil_os',
                    'anatomi_lensa_os',
                    'anatomi_vitreus_os',
                    'anatomi_retina_os',
                    'anatomi_nervus_optikus_os',
                    'anatomi_posisi_os',
                    'anatomi_gerak_bola_mata_os',
                ],
                'medrec_edukasi' => [
                    'bahasa',
                    'bahasa_lainnya',
                    'penterjemah',
                    'pendidikan',
                    'baca_tulis',
                    'cara_belajar',
                    'budaya',
                    'hambatan',
                    'keyakinan',
                    'keyakinan_khusus',
                    'topik_pembelajaran',
                    'topik_lainnya',
                    'kesediaan_pasien',
                    'nama_pasien_keluarga',
                    'ttd_pasien_keluarga'
                ],
                'medrec_skrining' => [
                    'jatuh_sempoyongan',
                    'jatuh_penopang',
                    'jatuh_info_dokter',
                    'jatuh_info_pukul',
                    'status_fungsional',
                    'status_info_pukul',
                    'nyeri_kategori',
                    'nyeri_lokasi',
                    'nyeri_karakteristik',
                    'nyeri_durasi',
                    'nyeri_frekuensi',
                    'nyeri_hilang_bila',
                    'nyeri_hilang_lainnya',
                    'nyeri_info_dokter',
                    'nyeri_info_pukul'
                ],
                'medrec_permintaan_penunjang' => [
                    'dokter_pengirim',
                    'rujukan_dari',
                    'pemeriksaan',
                    'pemeriksaan_lainnya',
                    'lokasi_pemeriksaan'
                ],
                'medrec_lp_tindakan_rajal' => [
                    'nama_dokter_dpjp',
                    'nama_perawat',
                    'diagnosa',
                    'kode_icd_x',
                    'lokasi_mata',
                    'isi_laporan'
                ],
                'medrec_operasi_pra' => [
                    'perawat_praoperasi',
                    'jenis_operasi',
                    'ctt_vital_suhu',
                    'ctt_vital_nadi',
                    'ctt_vital_rr',
                    'ctt_vital_td',
                    'ctt_vital_nyeri',
                    'ctt_vital_tb',
                    'ctt_vital_bb',
                    'ctt_mental',
                    'ctt_riwayat_sakit',
                    'ctt_riwayat_sakit_lain',
                    'ctt_pengobatan_sekarang',
                    'ctt_alat_bantu',
                    'ctt_operasi_jenis',
                    'ctt_operasi_tanggal',
                    'ctt_operasi_lokasi',
                    'ctt_alergi',
                    'ctt_alergi_jelaskan',
                    'ctt_lab_hb',
                    'ctt_lab_bt',
                    'ctt_lab_ctaptt',
                    'ctt_lab_goldarah',
                    'ctt_lab_urin',
                    'ctt_lab_lainnya',
                    'ctt_haid',
                    'ctt_kepercayaan',
                    'cek_biometri',
                    'cek_retinometri',
                    'cek_labor',
                    'cek_radiologi',
                    'cek_puasa',
                    'cek_instruksi',
                    'cek_lensa',
                    'cek_rotgen',
                    'cek_rotgen_usia',
                    'cek_rotgen_konsul',
                    'cek_penyakit',
                    'cek_hepatitis_akhir',
                    'cek_penyakit_lainnya',
                    'cek_berat_badan',
                    'cek_foto_fundus',
                    'cek_usg',
                    'cek_perhiasan',
                    'cek_ttd',
                    'cek_cuci',
                    'cek_mark',
                    'cek_tetes_pantocain',
                    'cek_tetes_efrisel1',
                    'cek_tetes_efrisel2',
                    'cek_tetes_midriatil1',
                    'cek_tetes_midriatil2',
                    'cek_tetes_midriatil3',
                    'cek_makan',
                    'cek_obat',
                    'cek_jenis_obat'
                ],
                'medrec_operasi_safety_signin' => [
                    'ns_konfirmasi_identitas',
                    'dr_konfirmasi_identitas',
                    'ns_marker_operasi',
                    'dr_marker_operasi',
                    'ns_inform_consent_sesuai',
                    'dr_inform_consent_sesuai',
                    'ns_identifikasi_alergi',
                    'dr_identifikasi_alergi',
                    'ns_puasa',
                    'dr_puasa',
                    'ns_cek_lensa_intrakuler',
                    'ns_konfirmasi_lensa',
                    'dr_cek_anestesi_khusus',
                    'dr_konfirmasi_anastersi',
                    'nama_dokter_anastesi',
                    'tanda_dokter_anastesi'
                ],
                'medrec_operasi_safety_signout' => [
                    'kelengkapan_instrumen',
                    'spesimen_kultur',
                    'label_pasien',
                    'masalah_instrumen',
                    'keterangan_masalah',
                    'instruksi_khusus',
                    'keterangan_instruksi',
                    'nama_dokter_operator',
                    'tanda_tangan_dokter_operator',
                    'jam'
                ],
                'medrec_operasi_safety_timeout' => [
                    'perkenalan_diri',
                    'cek_nama_mr',
                    'cek_rencana_tindakan',
                    'cek_marker',
                    'alergi',
                    'lateks',
                    'proteksi',
                    'proteksi_kasa',
                    'proteksi_shield',
                    'info_instrumen_ok',
                    'info_teknik_ok',
                    'info_steril_instrumen',
                    'info_kelengkapan_instrumen',
                    'perlu_antibiotik_dan_guladarah',
                    'nama_perawat',
                    'tanda_tangan_perawat',
                    'jam'
                ],
                'medrec_optik' => [
                    'od_login_spher',
                    'od_login_cyldr',
                    'od_login_axis',
                    'od_login_prisma',
                    'od_login_basis',
                    'od_domo_spher',
                    'od_domo_cyldr',
                    'od_domo_axis',
                    'od_domo_prisma',
                    'od_domo_basis',
                    'od_quitat_spher',
                    'od_quitat_cyldr',
                    'od_quitat_axis',
                    'od_quitat_prisma',
                    'od_quitat_basis',
                    'os_login_spher',
                    'os_login_cyldr',
                    'os_login_axis',
                    'os_login_prisma',
                    'os_login_basis',
                    'os_login_vitror',
                    'os_login_pupil',
                    'os_domo_spher',
                    'os_domo_cyldr',
                    'os_domo_axis',
                    'os_domo_prisma',
                    'os_domo_basis',
                    'os_domo_vitror',
                    'os_domo_pupil',
                    'os_quitat_spher',
                    'os_quitat_cyldr',
                    'os_quitat_axis',
                    'os_quitat_prisma',
                    'os_quitat_basis',
                    'os_quitat_vitror',
                    'os_quitat_pupil'
                ]
            ];

            foreach ($tables as $table => $columns) {
                $conditions = [];
                foreach ($columns as $column) {
                    $conditions[] = "$column IS NULL";
                }

                $query = "DELETE FROM $table WHERE " . implode(' AND ', $conditions);
                $db->query($query);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Data rekam medis yang kosong berhasil dihapus']);
        } else {
            // Jika bukan admin, mengembalikan status 404 dengan pesan error
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
