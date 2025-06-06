<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePickupDeliveryTable extends Migration
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
                'null'           => false,
            ],
            'id_contatto' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'titolo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['ritiro', 'consegna'],
                'null'       => false,
            ],
            'data_programmata' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'data_completata' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'indirizzo' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'citta' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'cap' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'provincia' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'nazione' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'Italia',
            ],
            'nome_contatto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'telefono_contatto' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email_contatto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'id_attivita' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_utente_assegnato' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'priorita' => [
                'type'       => 'ENUM',
                'constraint' => ['bassa', 'normale', 'alta', 'urgente'],
                'default'    => 'normale',
            ],
            'stato' => [
                'type'       => 'ENUM',
                'constraint' => ['programmata', 'in_corso', 'completata', 'annullata'],
                'default'    => 'programmata',
            ],
            'id_utente_creatore' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'descrizione' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'richiesta_ddt' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'numero_ddt' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'data_ddt' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'orario_preferito' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'note_trasportatore' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'costo_stimato' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'costo_effettivo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        
        // Indici per migliorare le performance
        $this->forge->addKey('id_anagrafica');
        $this->forge->addKey('id_contatto');
        $this->forge->addKey('id_attivita');
        $this->forge->addKey('id_utente_assegnato');
        $this->forge->addKey('id_utente_creatore');
        $this->forge->addKey('data_programmata');
        $this->forge->addKey('stato');
        $this->forge->addKey('priorita');
        $this->forge->addKey('tipo');
        
        $this->forge->createTable('pickup_delivery');
        
        // Aggiungo le foreign key constraints
        $this->db->query('ALTER TABLE pickup_delivery ADD CONSTRAINT fk_pickup_delivery_anagrafica FOREIGN KEY (id_anagrafica) REFERENCES anagrafiche(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE pickup_delivery ADD CONSTRAINT fk_pickup_delivery_contatto FOREIGN KEY (id_contatto) REFERENCES contatti(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE pickup_delivery ADD CONSTRAINT fk_pickup_delivery_attivita FOREIGN KEY (id_attivita) REFERENCES attivita(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE pickup_delivery ADD CONSTRAINT fk_pickup_delivery_utente_assegnato FOREIGN KEY (id_utente_assegnato) REFERENCES utenti(id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE pickup_delivery ADD CONSTRAINT fk_pickup_delivery_utente_creatore FOREIGN KEY (id_utente_creatore) REFERENCES utenti(id) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('pickup_delivery');
    }
} 