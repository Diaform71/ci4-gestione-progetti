<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgettoParentField extends Migration
{
    public function up()
    {
        // Aggiungi la colonna id_progetto_padre
        $fields = [
            'id_progetto_padre' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
                'after' => 'attivo'
            ]
        ];
        
        // Aggiungi la colonna in modo sicuro
        try {
            $this->forge->addColumn('progetti', $fields);
        } catch (\Exception $e) {
            // La colonna potrebbe già esistere, ignoriamo l'errore
        }
    }

    public function down()
    {
        // Rimuovi la colonna in modo sicuro
        try {
            $this->forge->dropColumn('progetti', 'id_progetto_padre');
        } catch (\Exception $e) {
            // La colonna potrebbe non esistere, ignoriamo l'errore
        }
    }
} 