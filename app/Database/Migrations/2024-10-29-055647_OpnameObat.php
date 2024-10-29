<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OpnameObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_opname_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATETIME',
            ],
            'apoteker' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->forge->addKey('id_opname_obat', true);
        $this->forge->createTable('opname_obat');
    }

    public function down()
    {
        $this->forge->dropTable('opname_obat');
    }
}
