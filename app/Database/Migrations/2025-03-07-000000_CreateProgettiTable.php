<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgettiTable extends Migration
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
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fase_kanban' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'backlog',
                'null'       => false,
            ],
            'id_anagrafica' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'data_inizio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_scadenza' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_fine' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'id_creato_da' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_responsabile' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'priorita' => [
                'type'       => 'ENUM',
                'constraint' => ['bassa', 'media', 'alta', 'critica'],
                'default'    => 'media',
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['in_corso', 'completato', 'sospeso', 'annullato'],
                'default'    => 'in_corso',
            ],
            'budget' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'attivo' => [
                'type'       => 'BOOLEAN',
                'default'    => 1,
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
        $this->forge->addForeignKey('id_anagrafica', 'anagrafiche', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_creato_da', 'utenti', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_responsabile', 'utenti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('progetti');
    }

    public function down()
    {
        $this->forge->dropTable('progetti');
    }
} 