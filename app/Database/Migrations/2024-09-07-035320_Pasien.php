<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pasien extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pasien' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'no_mr' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'no_registrasi' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'jenis_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'alamat_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'tgl_pendaftaran' => [
                'type' => 'DATE'
            ],
        ]);
        $this->forge->addKey('id_pasien', true);
        $this->forge->createTable('pasien');
    }

    public function down()
    {
        $this->forge->dropTable('pasien');
    }
}
