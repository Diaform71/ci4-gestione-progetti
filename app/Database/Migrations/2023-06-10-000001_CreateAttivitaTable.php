<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttivitaTable extends Migration
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
                'null'       => false,
            ],
            'id_utente_assegnato' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_utente_creatore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'titolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'priorita' => [
                'type'       => 'ENUM',
                'constraint' => ['bassa', 'media', 'alta', 'urgente'],
                'default'    => 'media',
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['da_iniziare', 'in_corso', 'in_pausa', 'completata', 'annullata'],
                'default'    => 'da_iniziare',
            ],
            'data_scadenza' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'data_creazione' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'data_aggiornamento' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'completata' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'completata_il' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        
        // Aggiungi le foreign key
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_utente_assegnato', 'utenti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_utente_creatore', 'utenti', 'id', 'CASCADE', 'SET NULL');
        
        $this->forge->createTable('attivita');
    }

    public function down()
    {
        $this->forge->dropTable('attivita');
    }
} 