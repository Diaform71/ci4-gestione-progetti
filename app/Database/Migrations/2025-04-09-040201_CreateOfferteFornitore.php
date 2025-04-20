<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfferteFornitore extends Migration
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
                'type'       => 'DATE',
                'null'       => false,
            ],
            'oggetto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => true,
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
            'id_richiesta_offerta' => [
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
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'ricevuta',
            ],
            'id_utente_creatore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'data_ricezione' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'data_approvazione' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'importo_totale' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'valuta' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'EUR',
            ],
            'note' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'sconto_totale' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'sconto_fisso' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'costo_trasporto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_anagrafica', 'anagrafiche', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_referente', 'contatti', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_richiesta_offerta', 'richieste_offerta', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_utente_creatore', 'utenti', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('offerte_fornitore');
    }

    public function down()
    {
        $this->forge->dropTable('offerte_fornitore');
    }
}
