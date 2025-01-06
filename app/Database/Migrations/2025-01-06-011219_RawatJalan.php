<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RawatJalan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rawat_jalan' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pasien' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
            ],
            'nomor_registrasi' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'tanggal_registrasi' => [
                'type' => 'DATETIME',
            ],
            'jenis_kunjungan' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'jaminan' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'ruangan' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'keluhan' => [
                'type' => 'TEXT',
            ],
            'no_antrian' => [
                'type' => 'VARCHAR',
                'constraint' => 6,
            ],
            'jenis_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->forge->addKey('id_rawat_jalan', true);
        $this->forge->addForeignKey('id_pasien', 'pasien', 'id_pasien', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('rawat_jalan');
    }

    public function down()
    {
        $this->forge->dropTable('rawat_jalan');
    }
}
