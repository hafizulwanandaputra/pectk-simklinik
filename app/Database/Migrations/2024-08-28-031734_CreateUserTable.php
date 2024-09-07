<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'fullname' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
            ],
            'profilephoto' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => true,
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'active' => [
                'type' => 'BOOLEAN',
            ],
            'registered' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->createTable('user');
    }

    public function down()
    {
        $this->forge->dropTable('user');
    }
}
