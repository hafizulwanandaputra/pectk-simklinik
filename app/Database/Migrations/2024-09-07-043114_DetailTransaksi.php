<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_transaksi' => [
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
            'id_transaksi' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'jumlah' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
            'harga_satuan' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
            'diskon' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
            'total_harga' => [
                'type' => 'BIGINT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_detail_transaksi', true);
        $this->forge->addForeignKey('id_obat', 'obat', 'id_obat', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_transaksi', 'transaksi', 'id_transaksi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_transaksi');
    }
}
