<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDateFieldsToOrdini extends Migration
{
    public function up()
    {
        // Aggiungiamo i campi mancanti per le date degli stati
        $fields = [
            'data_completamento' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'data_consegna_effettiva'
            ],
            'data_annullamento' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'data_completamento'
            ]
        ];

        $this->forge->addColumn('ordini_materiale', $fields);
    }

    public function down()
    {
        // Rimuoviamo i campi aggiunti in caso di rollback
        $this->forge->dropColumn('ordini_materiale', ['data_completamento', 'data_annullamento']);
    }
} 