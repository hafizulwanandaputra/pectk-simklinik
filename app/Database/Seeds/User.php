<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class User extends Seeder
{
    public function run()
    {
        $data = [
            'fullname' => 'Administrator',
            'username' => 'admin',
            'password' => password_hash('12345', PASSWORD_DEFAULT),
            'profilephoto' => NULL,
            'role' => 'Master Admin'
        ];
        $this->db->table('user')->insert($data);
    }
}
