<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRuoloToUtenti extends Migration
{
    public function up()
    {
        $this->forge->addColumn('utenti', [
            'ruolo' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('utenti', 'ruolo');
    }
} 