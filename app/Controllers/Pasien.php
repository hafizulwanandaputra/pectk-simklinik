<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Pasien extends BaseController
{
    public function index()
    {
        // Memeriksa peran pengguna, hanya 'Admin' yang diizinkan
        if (session()->get('role') == 'Admin') {
            // Menyiapkan data untuk tampilan
            $data = [
                'title' => 'Pasien Rawat Jalan - ' . $this->systemName,
                'headertitle' => 'Pasien Rawat Jalan',
                'agent' => $this->request->getUserAgent() // Mengambil informasi user agent
            ];
            // Menampilkan tampilan untuk halaman pasien
            return view('dashboard/pasien/index', $data);
        } else {
            // Jika peran tidak dikenali, lemparkan pengecualian 404
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienapi()
    {
        // Memeriksa peran pengguna, hanya 'Admin' yang diizinkan
        if (session()->get('role') == 'Admin') {
            // Mengambil tanggal dari query string
            $tanggal = $this->request->getGet('tanggal');

            // Memeriksa apakah tanggal diisi
            if (!$tanggal) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Tanggal harus diisi',
                ]);
            }

            // Membuat klien HTTP Guzzle baru
            $client = new Client();

            try {
                // Mengirim permintaan GET ke API
                $response = $client->request('GET', env('API-URL') . $tanggal, [
                    'headers' => [
                        'Accept' => 'application/json', // Menyatakan format data yang diinginkan
                        'x-key' => env('X-KEY') // Menyertakan kunci API untuk autentikasi
                    ],
                ]);

                // Mendekode JSON dan menangani potensi kesalahan
                $data = json_decode($response->getBody()->getContents(), true);

                // Mengembalikan respons JSON dengan data pasien
                return $this->response->setJSON([
                    'data' => $data,
                ]);
            } catch (RequestException $e) {
                // Periksa apakah ada respons dari API
                $response = $e->getResponse();
                $errorDetails = [];

                // Jika ada respons, coba parse JSON-nya
                if ($response) {
                    $body = $response->getBody()->getContents();
                    $errorDetails = json_decode($body, true);

                    // Jika parsing gagal, simpan teks asli
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $errorDetails = ['raw_error' => $body];
                    }
                } else {
                    // Jika tidak ada respons, gunakan pesan kesalahan default
                    $errorDetails = ['message' => $e->getMessage()];
                }

                // Mengembalikan respons dengan detail kesalahan
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Gagal mengambil data pasien',
                    'details' => $errorDetails,
                ]);
            }
        } else {
            // Jika peran tidak dikenali, kembalikan status 404
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
