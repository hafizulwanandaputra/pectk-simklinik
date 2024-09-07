<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Dokter extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_dokter' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'alamat_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'kontak_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
        ]);
        $this->forge->addKey('id_dokter', true);
        $this->forge->createTable('dokter');
    }

    public function down()
    {
        $this->forge->dropTable('dokter');
    }
}
