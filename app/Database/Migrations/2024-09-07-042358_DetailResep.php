<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailResep extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_resep' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_resep' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'id_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true
            ],
            'nama_obat' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'kategori_obat' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'bentuk_obat' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'signa' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
            ],
            'catatan' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'cara_pakai' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
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
        $this->forge->addKey('id_detail_resep', true);
        $this->forge->addForeignKey('id_resep', 'resep', 'id_resep', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_resep');
    }

    public function down()
    {
        $this->forge->dropTable('detail_resep');
    }
}
