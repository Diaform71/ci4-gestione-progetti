<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScontoFissoToOfferteFornitore extends Migration
{
    public function up()
    {
        $this->forge->addColumn('offerte_fornitore', [
            'sconto_fisso' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'sconto_totale'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('offerte_fornitore', 'sconto_fisso');
    }
} 