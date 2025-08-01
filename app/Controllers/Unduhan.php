<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Unduhan extends BaseController
{
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Unduh Dokumen - ' . $this->systemName,
                'headertitle' => 'Unduh Dokumen',
                'agent' => $this->request->getUserAgent()
            ];
            // Mengembalikan tampilan daftar obat
            return view('dashboard/unduhdokumen/index', $data);
        } else {
            // Jika peran tidak dikenali, lempar pengecualian untuk halaman tidak ditemukan
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function optik()
    {
        // Memeriksa peran pengguna, hanya 'Admin' atau 'Admisi' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Resep Kacamata - ' . $this->systemName,
                'agent' => $this->request->getUserAgent()
            ];
            // return view('dashboard/unduhdokumen/optik', $data);
            // die;
            $client = \Config\Services::curlrequest();
            $html = view('dashboard/unduhdokumen/optik', $data);
            $filename = 'Resep-Kacamata-Kosong.pdf';

            try {
                $response = $client->post(env('PDF-URL'), [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'html' => $html,
                        'filename' => $filename,
                        'paper' => [
                            'format' => 'A4',
                            'margin' => [
                                'top' => '0.25cm',
                                'right' => '0.25cm',
                                'bottom' => '0.25cm',
                                'left' => '0.25cm'
                            ]
                        ]
                    ]
                ]);

                $result = json_decode($response->getBody(), true);

                if (isset($result['success']) && $result['success']) {
                    $path = WRITEPATH . 'temp/' . $result['file'];

                    if (!is_file($path)) {
                        return $this->response
                            ->setStatusCode(500)
                            ->setBody("PDF berhasil dibuat tapi file tidak ditemukan: $path");
                    }

                    return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                        ->setBody(file_get_contents($path));
                } else {
                    $errorMessage = $result['error'] ?? 'Tidak diketahui';
                    return $this->response
                        ->setStatusCode(500)
                        ->setBody("Gagal membuat PDF: " . esc($errorMessage));
                }
            } catch (\Exception $e) {
                return $this->response
                    ->setStatusCode(500)
                    ->setBody("Kesalahan saat request ke PDF worker: " . esc($e->getMessage()));
            }
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
