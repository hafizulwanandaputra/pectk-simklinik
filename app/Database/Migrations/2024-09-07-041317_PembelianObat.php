<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PembelianObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pembelian_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_supplier' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'id_user' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'tgl_pembelian' => [
                'type' => 'DATETIME',
            ],
            'total_qty' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'total_biaya' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'diterima' => [
                'type' => 'BOOLEAN'
            ],
        ]);
        $this->forge->addKey('id_pembelian_obat', true);
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id_supplier', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('id_user', 'user', 'id_user', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('pembelian_obat');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian_obat');
    }
}
