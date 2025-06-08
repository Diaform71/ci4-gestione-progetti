<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPickupDeliveryNullableCosts extends Migration
{
    public function up()
    {
        // Modifica i campi per renderli nullable
        $this->forge->modifyColumn('pickup_delivery', [
            'costo_stimato' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => null,
            ],
            'costo_effettivo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        // Ripristina i campi come NOT NULL
        $this->forge->modifyColumn('pickup_delivery', [
            'costo_stimato' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
            ],
            'costo_effettivo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0.00,
            ],
        ]);
    }
}
