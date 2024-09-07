<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StokObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_stok_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'jumlah_masuk' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
            'jumlah_keluar' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
            'updated_at' => [
                'type' => 'DATE',
            ],
        ]);
        $this->forge->addKey('id_stok_obat', true);
        $this->forge->addForeignKey('id_obat', 'obat', 'id_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stok_obat');
    }

    public function down()
    {
        $this->forge->dropTable('stok_obat');
    }
}
