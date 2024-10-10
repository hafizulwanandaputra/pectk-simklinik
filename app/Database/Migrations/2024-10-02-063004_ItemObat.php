<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItemObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_item_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_detail_pembelian_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'no_batch' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'expired' => [
                'type' => 'DATE',
                'null' => TRUE,
            ],
            'jumlah_item' => [
                'type' => 'INT',
                'constraint' => 24,
            ]
        ]);
        $this->forge->addKey('id_item_obat', true);
        $this->forge->addForeignKey('id_detail_pembelian_obat', 'detail_pembelian_obat', 'id_detail_pembelian_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('item_obat');
    }

    public function down()
    {
        $this->forge->dropTable('item_obat');
    }
}
