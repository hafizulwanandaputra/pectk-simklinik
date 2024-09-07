<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Supplier extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_supplier' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'alamat_supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'kontak_supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
        ]);
        $this->forge->addKey('id_supplier', true);
        $this->forge->createTable('supplier');
    }

    public function down()
    {
        $this->forge->dropTable('supplier');
    }
}
