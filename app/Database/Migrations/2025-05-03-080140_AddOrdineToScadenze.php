<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrdineToScadenze extends Migration
{
    public function up()
    {
        $this->forge->addColumn('scadenze', [
            'id_ordine_materiale' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'stato'
            ]
        ]);
        
        // Aggiungi la foreign key per collegare la scadenza all'ordine
        $this->db->query('ALTER TABLE scadenze ADD CONSTRAINT fk_scadenze_ordini_materiale 
                          FOREIGN KEY (id_ordine_materiale) REFERENCES ordini_materiale(id) 
                          ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Rimuovi prima la foreign key
        $this->db->query('ALTER TABLE scadenze DROP FOREIGN KEY fk_scadenze_ordini_materiale');
        
        // Poi rimuovi la colonna
        $this->forge->dropColumn('scadenze', 'id_ordine_materiale');
    }
}
