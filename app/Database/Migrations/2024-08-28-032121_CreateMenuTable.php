<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_menu' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_petugas' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'nama_menu' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
            ],
            'jadwal_makan' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'protein_hewani' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'protein_nabati' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'sayur' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'buah' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => true,
            ],
            'jumlah' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_menu', true);
        $this->forge->addForeignKey('id_petugas', 'petugas', 'id_petugas', 'CASCADE', 'CASCADE');
        $this->forge->createTable('menu');
    }

    public function down()
    {
        $this->forge->dropTable('menu');
    }
}
