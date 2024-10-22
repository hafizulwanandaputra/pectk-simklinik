<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PwdTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pwd_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pwd_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('pwd_transaksi');
    }
}
