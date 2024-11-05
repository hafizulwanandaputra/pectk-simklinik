<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_transaksi' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_resep' => [
                'type' => 'BIGINT',
                'constraint' => 24,
                'unsigned' => true,
                'null' => true,
            ],
            'nomor_registrasi' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'no_rm' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'nama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'alamat' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
            ],
            'telpon' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null' => true,
            ],
            'jenis_kelamin' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'kasir' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
            'no_kwitansi' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'tgl_transaksi' => [
                'type' => 'DATETIME',
            ],
            'total_pembayaran' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'terima_uang' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'uang_kembali' => [
                'type' => 'INT',
                'constraint' => 24,
            ],
            'metode_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
            ],
            'bank' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => true,
            ],
            'lunas' => [
                'type' => 'BOOLEAN'
            ],
        ]);
        $this->forge->addKey('id_transaksi', true);
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}
