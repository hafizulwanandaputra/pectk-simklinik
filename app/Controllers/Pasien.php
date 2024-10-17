<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Pasien extends BaseController
{
    public function index()
    {
        if (session()->get('role') == 'Admin') {
            $data = [
                'title' => 'Pasien - ' . $this->systemName,
                'headertitle' => 'Pasien (Rawat Jalan)',
                'agent' => $this->request->getUserAgent()
            ];
            return view('dashboard/pasien/index', $data);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    public function pasienapi()
    {
        if (session()->get('role') == 'Admin') {
            $tanggal = $this->request->getGet('tanggal'); // Ambil tanggal dari query string

            if (!$tanggal) {
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Tanggal harus diisi',
                ]);
            }
            $client = new Client(); // Create a new Guzzle HTTP client

            try {
                // Send a GET request to the API
                $response = $client->request('GET', env('API-URL') . $tanggal, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'x-key' => env('X-KEY')
                    ],
                ]);

                // Decode JSON and handle potential errors
                $data = json_decode($response->getBody()->getContents(), true);

                return $this->response->setJSON([
                    'data' => $data,
                ]);
            } catch (RequestException $e) {
                // Handle API request errors
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Gagal mengambil data pasien<br>' . $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Halaman tidak ditemukan',
            ]);
        }
    }
}
