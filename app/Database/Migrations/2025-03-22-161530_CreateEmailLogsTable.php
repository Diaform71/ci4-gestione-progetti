<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailLogsTable extends Migration
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
            'destinatario' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'cc' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'ccn' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'oggetto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'corpo' => [
                'type'       => 'TEXT',
            ],
            'id_riferimento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tipo_riferimento' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'RDO',
                'comment'    => 'RDO, ORDINE, ecc.',
            ],
            'data_invio' => [
                'type'       => 'DATETIME',
            ],
            'stato' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'inviato',
                'comment'    => 'inviato, errore',
            ],
            'error_message' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'allegati' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'JSON array con informazioni sugli allegati',
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
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey(['id_riferimento', 'tipo_riferimento']);
        $this->forge->addKey('id_utente');
        $this->forge->addKey('data_invio');
        $this->forge->addKey('stato');
        
        $this->forge->createTable('email_logs');
    }

    public function down()
    {
        $this->forge->dropTable('email_logs');
    }
}
