<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Resep extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_resep' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pasien' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'id_user' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'tanggal_resep' => [
                'type' => 'DATETIME'
            ],
            'jumlah_resep' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'total_biaya' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'keterangan' => [
                'type' => 'TEXT'
            ],
            'status' => [
                'type' => 'BOOLEAN'
            ],
        ]);
        $this->forge->addKey('id_resep', true);
        $this->forge->addForeignKey('id_pasien', 'pasien', 'id_pasien', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resep');
    }

    public function down()
    {
        $this->forge->dropTable('resep');
    }
}
