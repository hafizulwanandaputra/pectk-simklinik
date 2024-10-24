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
            'id_resep' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'null' => true
            ],
            'id_layanan' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'null' => true
            ],
            'id_transaksi' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'nama_layanan' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => true
            ],
            'jenis_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'qty_transaksi' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'harga_transaksi' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'diskon' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_detail_transaksi', true);
        $this->forge->addForeignKey('id_resep', 'resep', 'id_resep', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_transaksi', 'transaksi', 'id_transaksi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_transaksi');
    }
}
