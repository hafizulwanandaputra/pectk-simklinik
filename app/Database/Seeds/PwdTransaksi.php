<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PwdTransaksi extends Seeder
{
    public function run()
    {
        $data = [
            'pwd_transaksi' => password_hash('12345', PASSWORD_DEFAULT),
        ];
        $this->db->table('pwd_transaksi')->insert($data);
    }
}
