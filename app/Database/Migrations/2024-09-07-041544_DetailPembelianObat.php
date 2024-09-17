<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailPembelianObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_pembelian_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pembelian_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'id_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'jumlah' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'harga_satuan' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_detail_pembelian_obat', true);
        $this->forge->addForeignKey('id_pembelian_obat', 'pembelian_obat', 'id_pembelian_obat', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_obat', 'obat', 'id_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pembelian_obat');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pembelian_obat');
    }
}
