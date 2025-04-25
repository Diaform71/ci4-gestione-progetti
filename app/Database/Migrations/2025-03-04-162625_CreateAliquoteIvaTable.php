<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAliquoteIvaTable extends Migration
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
            'codice' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'descrizione' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'percentuale' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
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
        $this->forge->addUniqueKey('codice');
        $this->forge->createTable('aliquote_iva');
        
        // Inserire le aliquote IVA più comuni
        $data = [
            [
                'codice'      => '22',
                'descrizione' => 'Aliquota ordinaria',
                'percentuale' => 22.00,
                'note'        => 'Aliquota ordinaria per la maggior parte dei beni e servizi',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => '10',
                'descrizione' => 'Aliquota ridotta',
                'percentuale' => 10.00,
                'note'        => 'Per prodotti alimentari, farmaci, servizi di trasporto, ecc.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => '5',
                'descrizione' => 'Aliquota super-ridotta',
                'percentuale' => 5.00,
                'note'        => 'Per alcuni prodotti alimentari, dispositivi medici, ecc.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => '4',
                'descrizione' => 'Aliquota minima',
                'percentuale' => 4.00,
                'note'        => 'Per beni di prima necessità, servizi sanitari, ecc.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => '0',
                'descrizione' => 'Esente IVA',
                'percentuale' => 0.00,
                'note'        => 'Operazioni esenti da IVA ai sensi dell\'art. 10 del DPR 633/72',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => 'FC',
                'descrizione' => 'Fuori campo IVA',
                'percentuale' => 0.00,
                'note'        => 'Operazioni fuori campo IVA',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'codice'      => 'NI',
                'descrizione' => 'Non imponibile',
                'percentuale' => 0.00,
                'note'        => 'Operazioni non imponibili IVA',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('aliquote_iva')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('aliquote_iva');
    }
}
