<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdCondizionePagamento extends Migration
{
    public function up()
    {
        // Aggiungi campo id_condizione_pagamento
        $this->forge->addColumn('ordini_materiale', [
            'id_condizione_pagamento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'condizioni_pagamento'
            ]
        ]);
        
        // Aggiungi un indice per migliorare le prestazioni
        $this->db->query('ALTER TABLE ordini_materiale ADD INDEX idx_id_condizione_pagamento (id_condizione_pagamento)');
    }

    public function down()
    {
        // Rimuovi l'indice
        $this->db->query('ALTER TABLE ordini_materiale DROP INDEX idx_id_condizione_pagamento');
        
        // Rimuovi la colonna
        $this->forge->dropColumn('ordini_materiale', 'id_condizione_pagamento');
    }
} 