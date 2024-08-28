<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePetugasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_petugas' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_petugas' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
            ],
            'jumlah_menu' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_petugas', true);
        $this->forge->createTable('petugas');
    }

    public function down()
    {
        $this->forge->dropTable('petugas');
    }
}
