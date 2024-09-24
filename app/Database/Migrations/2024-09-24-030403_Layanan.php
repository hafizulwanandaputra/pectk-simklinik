<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Layanan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_layanan' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_layanan' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'jenis_layanan' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tarif' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ]
        ]);
        $this->forge->addKey('id_layanan', true);
        $this->forge->createTable('layanan');
    }

    public function down()
    {
        $this->forge->dropTable('layanan');
    }
}
