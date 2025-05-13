<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfferteFornitoreVoci extends Migration
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
            'id_offerta_fornitore' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_materiale' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'codice' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'quantita' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 1,
            ],
            'prezzo_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'importo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'unita_misura' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'pz',
            ],
            'sconto' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => 0,
            ],
            'note' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'id_progetto' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_richiesta_materiale' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addForeignKey('id_offerta_fornitore', 'offerte_fornitore', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_materiale', 'materiali', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_progetto', 'progetti', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_richiesta_materiale', 'richieste_materiali', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('offerte_fornitore_voci');
    }

    public function down()
    {
        $this->forge->dropTable('offerte_fornitore_voci');
    }
}
