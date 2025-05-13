<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnagraficheContattiTable extends Migration
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
            'id_anagrafica' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'id_contatto' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'ruolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'principale' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'note' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_anagrafica', 'anagrafiche', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_contatto', 'contatti', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['id_anagrafica', 'id_contatto']); // Ogni contatto può essere associato una sola volta ad una anagrafica
        $this->forge->createTable('anagrafiche_contatti');
    }

    public function down()
    {
        $this->forge->dropTable('anagrafiche_contatti');
    }
}
