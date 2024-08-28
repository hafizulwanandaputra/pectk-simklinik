<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermintaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_menu' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
            ],
            'nama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'jenis_kelamin' => [
                'type' => 'VARCHAR',
                'constraint' => '28',
            ],
            'kamar' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'jenis_tindakan' => [
                'type' => 'TEXT',
            ],
            'diet' => [
                'type' => 'TEXT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_menu', 'menu', 'id_menu', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permintaan');
    }

    public function down()
    {
        $this->forge->dropTable('permintaan');
    }
}
