<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSottoAttivitaTable extends Migration
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
            'id_attivita' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_utente_assegnato' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'titolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addForeignKey('id_attivita', 'attivita', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_utente_assegnato', 'utenti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('sotto_attivita');
    }

    public function down()
    {
        $this->forge->dropTable('sotto_attivita');
    }
} 