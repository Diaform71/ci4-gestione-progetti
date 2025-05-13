<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRichiesteMaterialiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_richiesta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_materiale' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantita' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'unita_misura' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'pz',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_richiesta', 'richieste_offerta', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_materiale', 'materiali', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'SET NULL', 'SET NULL');
        
        $this->forge->createTable('richieste_materiali');
    }

    public function down()
    {
        $this->forge->dropTable('richieste_materiali');
    }
} 