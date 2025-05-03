<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfferteFornitoreAllegati extends Migration
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
            'nome_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'file_originale' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'dimensione' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tipo_mime' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'descrizione' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'data_caricamento' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => date('Y-m-d H:i:s'),
            ],
            'id_utente' => [
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
        $this->forge->addForeignKey('id_utente', 'utenti', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('offerte_fornitore_allegati');
    }

    public function down()
    {
        $this->forge->dropTable('offerte_fornitore_allegati');
    }
}
