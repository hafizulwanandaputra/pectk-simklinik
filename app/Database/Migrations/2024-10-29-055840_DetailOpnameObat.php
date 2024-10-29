<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailOpnameObat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_opname_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_opname_obat' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
            ],
            'nama_obat' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'sisa_stok' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
        ]);
        $this->forge->addKey('id_detail_opname_obat', true);
        $this->forge->addForeignKey('id_opname_obat', 'opname_obat', 'id_opname_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_opname_obat');
    }

    public function down()
    {
        $this->forge->dropTable('detail_opname_obat');
    }
}
