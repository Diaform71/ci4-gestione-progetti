<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdiniAllegatiTable extends Migration
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
            'id_ordine_materiale' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'nome_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_originale' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
                'null'       => true,
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
        $this->forge->addForeignKey('id_ordine_materiale', 'ordini_materiale', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_utente', 'utenti', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('ordini_materiale_allegati');
    }

    public function down()
    {
        $this->forge->dropTable('ordini_materiale_allegati');
    }
} 