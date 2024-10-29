<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Obat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_obat' => [
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
            'nama_obat' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'isi_obat' => [
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
            'harga_obat' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'ppn' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'mark_up' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'penyesuaian_harga' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'jumlah_masuk' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'jumlah_keluar' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id_obat', true);
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id_supplier', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('obat');
    }

    public function down()
    {
        $this->forge->dropTable('obat');
    }
}
