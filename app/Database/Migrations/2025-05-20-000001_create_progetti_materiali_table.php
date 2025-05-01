<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgettiMaterialiTable extends Migration
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
            'id_progetto' => [
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
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_materiale', 'materiali', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['id_progetto', 'id_materiale']);
        
        $this->forge->createTable('progetti_materiali');
    }

    public function down()
    {
        $this->forge->dropTable('progetti_materiali');
    }
} 