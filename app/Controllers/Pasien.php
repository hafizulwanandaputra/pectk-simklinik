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
            $client = new Client(); // Create a new Guzzle HTTP client
            //  . date('Y-m-d')

            try {
                // Send a GET request to the API
                $response = $client->request('GET', env('API-URL') . date('Y-m-d'), [
                    'headers' => [
                        'Accept' => 'application/json',
                        'x-key' => env('X-KEY')
                    ],
                ]);

                // Decode JSON and handle potential errors
                $data = json_decode($response->getBody()->getContents(), true);

                $counter = 1; // Initialize a counter variable
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as &$item) {
                        $item['nomor'] = $counter++; // Assign and increment the counter
                    }
                }

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
