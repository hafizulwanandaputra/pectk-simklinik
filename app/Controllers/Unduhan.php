<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Unduhan extends BaseController
{
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin', 'Admisi', atau 'Apoteker' yang diizinkan
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Apoteker') {
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

    public function kartuberobat()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $path = WRITEPATH . 'privatefiles/kartuberobat.pdf';

            // Cek apakah file ada
            if (!file_exists($path)) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Tampilkan PDF di browser
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="Kartu Berobat PECTK.pdf"')
                ->setBody(file_get_contents($path));
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function resepobatluar()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi' || session()->get('role') == 'Apoteker') {
            $path = WRITEPATH . 'privatefiles/resepobat.pdf';

            // Cek apakah file ada
            if (!file_exists($path)) {
                throw PageNotFoundException::forPageNotFound();
            }

            // Tampilkan PDF di browser
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="Resep Obat PECTK.pdf"')
                ->setBody(file_get_contents($path));
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function optik()
    {
        if (session()->get('role') == 'Admin' || session()->get('role') == 'Admisi') {
            $data = [
                'title' => 'Resep Kacamata - ' . $this->systemName,
                'agent' => $this->request->getUserAgent()
            ];

            $html = view('dashboard/unduhdokumen/optik', $data);
            $filename = 'Resep-Kacamata-Kosong.pdf';

            $client = new Client(); // pakai Guzzle langsung

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

                $rawBody = $response->getBody()->getContents();
                $result = json_decode($rawBody, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return $this->response
                        ->setStatusCode($response->getStatusCode())
                        ->setBody("Gagal membuat PDF. Respons worker:\n\n" . esc($rawBody));
                }

                if (!empty($result['success']) && $result['success']) {
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
                    $errorDetails = $result['details'] ?? '';

                    return $this->response
                        ->setStatusCode(500)
                        ->setBody(
                            "Gagal membuat PDF: " . esc($errorMessage) .
                                (!empty($errorDetails) ? "\n\nDetail:\n" . esc($errorDetails) : '')
                        );
                }
            } catch (RequestException $e) {
                // Ambil pesan default
                $errorMessage = "Kesalahan saat request ke PDF worker: " . $e->getMessage();

                // Kalau ada response dari worker
                if ($e->hasResponse()) {
                    $errorBody = (string) $e->getResponse()->getBody();

                    $json = json_decode($errorBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($json['error'])) {
                        $errorMessage .= "\n\nPesan dari worker: " . esc($json['error']);
                        if (!empty($json['details'])) {
                            $errorMessage .= "\n\nDetail:\n" . esc($json['details']);
                        }
                    } else {
                        $errorMessage .= "\n\nRespons worker:\n" . esc($errorBody);
                    }
                }

                return $this->response
                    ->setStatusCode(500)
                    ->setBody($errorMessage);
            }
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
