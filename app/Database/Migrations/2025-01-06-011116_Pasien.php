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
            'no_rm' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
            ],
            'nama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'null' => true,
            ],
            'no_bpjs' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'null' => true,
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jenis_kelamin' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'null' => true,
            ],
            'alamat' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true,
            ],
            'provinsi' => [
                'type' => 'CHAR',
                'constraint' => 2,
                'null' => true,
            ],
            'kabupaten' => [
                'type' => 'CHAR',
                'constraint' => 4,
                'null' => true,
            ],
            'kecamatan' => [
                'type' => 'CHAR',
                'constraint' => 6,
                'null' => true,
            ],
            'kelurahan' => [
                'type' => 'CHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'rt' => [
                'type' => 'VARCHAR',
                'constraint' => 6,
                'null' => true,
            ],
            'rw' => [
                'type' => 'VARCHAR',
                'constraint' => 6,
                'null' => true,
            ],
            'telpon' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'kewarganegaraan' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'agama' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'status_nikah' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'pekerjaan' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
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
