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
            'id_dokter' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
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
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'jenis_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'agama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
            ],
            'no_hp_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
            ],
            'alamat_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'provinsi' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'kota' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'desa' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'status_kawin' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tgl_pendaftaran' => [
                'type' => 'DATE'
            ],
        ]);
        $this->forge->addKey('id_pasien', true);
        $this->forge->addForeignKey('id_dokter', 'dokter', 'id_dokter', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pasien');
    }

    public function down()
    {
        $this->forge->dropTable('pasien');
    }
}
