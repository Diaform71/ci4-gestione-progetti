<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScadenzeTable extends Migration
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
            'titolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_scadenza' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'data_promemoria' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_attivita' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_utente_assegnato' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_utente_creatore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'completata' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'completata_il' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'priorita' => [
                'type'       => 'ENUM',
                'constraint' => ['bassa', 'media', 'alta', 'urgente'],
                'default'    => 'media',
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['da_iniziare', 'in_corso', 'completata', 'annullata'],
                'default'    => 'da_iniziare',
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
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_attivita', 'attivita', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_utente_assegnato', 'utenti', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_utente_creatore', 'utenti', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('scadenze');
    }

    public function down()
    {
        $this->forge->dropTable('scadenze');
    }
} 