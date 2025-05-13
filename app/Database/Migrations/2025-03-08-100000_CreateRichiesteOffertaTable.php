<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRichiesteOffertaTable extends Migration
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
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'data' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'oggetto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_anagrafica' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_referente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['bozza', 'inviata', 'accettata', 'rifiutata', 'annullata'],
                'default'    => 'bozza',
                'null'       => false,
            ],
            'id_utente_creatore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'data_invio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_accettazione' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addForeignKey('id_anagrafica', 'anagrafiche', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_utente_creatore', 'utenti', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('richieste_offerta');
    }

    public function down()
    {
        $this->forge->dropTable('richieste_offerta');
    }
} 