<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImpostazioniTable extends Migration
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
            'chiave' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'valore' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'id_utente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'stringa',
            ],
            'descrizione' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'gruppo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'sistema',
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['chiave', 'id_utente']);
        $this->forge->addForeignKey('id_utente', 'utenti', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('impostazioni');
    }

    public function down()
    {
        $this->forge->dropTable('impostazioni');
    }
} 